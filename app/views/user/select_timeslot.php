<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * View: Chọn khung giờ phòng họp.
 * Lưu ý: Controller vẫn phải xác thực ngày/giờ, quyền đặt phòng,
 * trạng thái giữ chỗ và xung đột lịch trước khi ghi dữ liệu.
 */
function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$today = new DateTimeImmutable('today');
$todayString = $today->format('Y-m-d');

$requestedDate = isset($date) && is_string($date) ? $date : $todayString;
$parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $requestedDate);
$dateErrors = DateTimeImmutable::getLastErrors();
$dateIsValid = $parsedDate !== false
    && ($dateErrors === false || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0))
    && $parsedDate->format('Y-m-d') === $requestedDate
    && $requestedDate >= $todayString;

$selectedDate = $dateIsValid ? $requestedDate : $todayString;
$roomId = filter_var($room['id'] ?? null, FILTER_VALIDATE_INT);
$roomId = ($roomId !== false && $roomId !== null && $roomId > 0) ? $roomId : 0;

$roomName = $room['room_name'] ?? 'Phòng họp';
$roomLocation = $room['location'] ?? 'Chưa cập nhật vị trí';

/*
 * Giá trị mặc định:
 * - Ngày tương lai: bắt đầu 08:00, kết thúc 09:00.
 * - Hôm nay: chọn giờ bắt đầu ở giờ kế tiếp, nếu vẫn còn trong ngày.
 * Đây chỉ là giá trị mặc định trên giao diện, không thay thế kiểm tra backend.
 */
$now = new DateTimeImmutable('now');
$defaultStart = new DateTimeImmutable($selectedDate . ' 08:00:00');
if ($selectedDate === $todayString) {
    $defaultStart = $now->setTime((int)$now->format('H'), 0)->modify('+1 hour');
}
$defaultEnd = $defaultStart->modify('+1 hour');
$defaultStartValue = $defaultStart->format('Y-m-d\TH:i');
$defaultEndValue = $defaultEnd->format('Y-m-d\TH:i');

// Khi Controller trả về trang này do xung đột, giữ nguyên khung giờ người dùng vừa chọn
if (!empty($posted_start) && !empty($posted_end)) {
    $defaultStartValue = str_replace(' ', 'T', substr((string)$posted_start, 0, 16));
    $defaultEndValue   = str_replace(' ', 'T', substr((string)$posted_end, 0, 16));
}
$conflictType    = $conflict_type ?? null;            // 'holding' | 'booked' | null
$conflictSeconds = (int)($conflict_seconds ?? 0);

$filteredSlots = [];
if (!empty($booked_slots) && is_iterable($booked_slots)) {
    foreach ($booked_slots as $slot) {
        if (!is_array($slot) || empty($slot['start_time']) || empty($slot['end_time'])) {
            continue;
        }

        try {
            $slotStart = new DateTimeImmutable((string)$slot['start_time']);
            $slotEnd = new DateTimeImmutable((string)$slot['end_time']);
        } catch (Throwable $e) {
            continue;
        }

        // Hiển thị mọi booking giao với ngày đang xem, kể cả booking qua nửa đêm.
        $dayStart = new DateTimeImmutable($selectedDate . ' 00:00:00');
        $dayEnd = $dayStart->modify('+1 day');
        if ($slotStart >= $dayEnd || $slotEnd <= $dayStart || $slotEnd <= $now) {
            continue;
        }

        $status = (string)($slot['status'] ?? 'pending');
        // Các trạng thái đã hủy/từ chối không được tính là lịch bận.
        if (in_array($status, ['cancelled', 'rejected', 'canceled'], true)) {
            continue;
        }

        $slot['_start'] = $slotStart;
        $slot['_end'] = $slotEnd;
        $slot['_status'] = $status;
        $filteredSlots[] = $slot;
    }
}

usort($filteredSlots, static function (array $a, array $b): int {
    return $a['_start'] <=> $b['_start'];
});

$statusLabels = [
    'approved' => 'Đã xác nhận',
    'booked' => 'Đã xác nhận',
    'holding' => 'Đang giữ chỗ',
    'pending' => 'Chờ duyệt',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Chọn khung giờ trống | MeetingHub</title>

    <!-- Phù hợp cho bản mẫu. Khi triển khai thật, nên biên dịch Tailwind CSS riêng. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1746A2',
                        page: '#F5F7FB',
                        line: '#DCE3EF',
                        ink: '#172033',
                        muted: '#5B6578'
                    }
                }
            }
        };
    </script>
    <style>
        body { background: #F5F7FB; font-family: Inter, sans-serif; }
        input:focus-visible, button:focus-visible, a:focus-visible {
            outline: 3px solid #93C5FD; outline-offset: 2px;
        }
    </style>
</head>
<body class="min-h-screen text-ink">
    <main class="mx-auto flex w-full max-w-5xl flex-col gap-5 px-4 py-5 sm:px-6 sm:py-8">
        <header class="flex flex-col gap-4 rounded-2xl border border-line bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div class="flex min-w-0 items-start gap-3">
                <a href="index.php?controller=user&amp;action=index"
                   aria-label="Quay lại trang tổng quan"
                   class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 transition hover:bg-slate-200">
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                </a>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-primary sm:text-2xl">Chọn khung giờ phòng họp</h1>
                    <p class="mt-1 break-words text-sm text-muted">
                        Tên: <strong class="text-ink"><?= h($roomName) ?></strong><span aria-hidden="true"> ·
                        Số: <strong class="text-ink"><?= h($room['room_number'] ?? 'Chưa cập nhật') ?></strong>
                        <span aria-hidden="true"> · </span><?= h($roomLocation) ?>
                    </p>
                </div>
            </div>
        </header>

        <?php if (!$dateIsValid): ?>
            <div role="alert" class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Ngày đã chọn không hợp lệ hoặc đã qua. Hệ thống đang hiển thị lịch từ hôm nay.
            </div>
        <?php endif; ?>

        <?php if ($roomId === 0): ?>
            <div role="alert" class="rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                Không xác định được phòng họp. Vui lòng quay lại danh sách phòng và chọn lại.
            </div>
        <?php endif; ?>
        <section class="flex flex-col gap-6 rounded-2xl border border-line bg-white p-4 shadow-sm sm:p-7">
            <div>
                <h2 class="text-lg font-bold sm:text-xl">Đăng ký thời gian họp</h2>
                <p class="mt-1 text-sm text-muted">
                    Ngày <?= h($parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $selectedDate)->format('d/m/Y')) ?>.
                    Vui lòng kiểm tra lịch bận trước khi tiếp tục.
                </p>
            </div>

            <form id="booking-form" action="index.php?controller=user&amp;action=lockTimeSlot" method="POST" class="flex flex-col gap-5">
                <input type="hidden" name="room_id" value="<?= (int)$roomId ?>">
                <input type="hidden" name="date" value="<?= h($selectedDate) ?>">

                <?php if (!empty($csrf_token) && is_string($csrf_token)): ?>
                    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="start_time" class="text-sm font-semibold">Thời gian bắt đầu <span class="text-red-600">*</span></label>
                        <input
                            type="datetime-local"
                            name="start_time"
                            id="start_time"
                            required
                            step="60"
                            value="<?= h($defaultStartValue) ?>"
                            class="w-full rounded-xl border border-line bg-white px-3 py-3 focus:border-primary focus:outline-none">
                        <p class="text-xs text-muted">Chọn thời điểm bắt đầu theo giờ Việt Nam.</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="end_time" class="text-sm font-semibold">Thời gian kết thúc <span class="text-red-600">*</span></label>
                        <input
                            type="datetime-local"
                            name="end_time"
                            id="end_time"
                            required
                            step="60"
                            value="<?= h($defaultEndValue) ?>"
                            class="w-full rounded-xl border border-line bg-white px-3 py-3 focus:border-primary focus:outline-none">
                        <p class="text-xs text-muted">Thời gian kết thúc phải sau thời gian bắt đầu.</p>
                    </div>
                </div>

                <div id="time-error" class="hidden rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert" aria-live="polite"></div>

                <!-- HỘP CHỜ: khung giờ đang được người khác giữ chỗ -->
                <div id="wait-box" class="hidden flex-col gap-3 rounded-xl border border-amber-300 bg-amber-50 px-4 py-4 text-sm text-amber-900 sm:flex-row sm:items-center sm:justify-between" role="status" aria-live="polite">
                    <p id="wait-text" class="font-medium"></p>
                    <button type="button" id="cancel-wait" class="w-fit shrink-0 rounded-lg border border-amber-400 bg-white px-3 py-1.5 text-xs font-semibold hover:bg-amber-100">
                        Ngừng chờ / chọn giờ khác
                    </button>
                </div>

                <section class="flex flex-col gap-3" aria-labelledby="busy-slots-heading">
                    <div>
                        <h3 id="busy-slots-heading" class="font-semibold">Lịch bận trong ngày</h3>
                        <p class="mt-1 text-sm text-muted">Các khung giờ dưới đây không nên được chọn. Trạng thái có thể thay đổi nếu người khác đặt phòng đồng thời.</p>
                    </div>

                    <?php if ($filteredSlots): ?>
                        <ul class="flex max-h-64 flex-col gap-2 overflow-y-auto pr-1">
                            <?php foreach ($filteredSlots as $slot): ?>
                                <?php
                                    $status = $slot['_status'];
                                    $isConfirmed = in_array($status, ['approved', 'booked'], true);
                                    $slotClass = $isConfirmed
                                        ? 'border-blue-200 bg-blue-50 text-blue-900'
                                        : 'border-amber-200 bg-amber-50 text-amber-900';
                                    $statusText = $statusLabels[$status] ?? 'Không khả dụng';
                                ?>
                                <li class="flex flex-col gap-2 rounded-xl border px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between <?= $slotClass ?>">
                                    <span class="font-semibold">
                                        <?= h($slot['_start']->format('H:i')) ?> – <?= h($slot['_end']->format('H:i')) ?>
                                    </span>
                                    <span class="w-fit rounded-full bg-white px-3 py-1 text-xs font-semibold">
                                        <?= h($statusText) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            Chưa có lịch bận được ghi nhận trong ngày này. Vui lòng tiếp tục kiểm tra; tình trạng phòng sẽ được xác minh lại khi gửi yêu cầu.
                        </p>
                    <?php endif; ?>
                </section>

                <div class="flex flex-col-reverse gap-3 border-t border-line pt-5 sm:flex-row sm:justify-end">
                    <a href="index.php?controller=user&amp;action=index"
                       class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                        Hủy
                    </a>
                    <button
                        id="submit-button"
                        type="submit"
                        <?= $roomId === 0 ? 'disabled' : '' ?>
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50">
                        Tiếp tục đặt phòng
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_forward</span>
                    </button>
                </div>
                <p class="text-xs leading-5 text-muted">
                    Việc chọn giờ ở màn hình này chưa bảo đảm phòng đã được giữ. Hệ thống cần xác nhận lại lịch ở máy chủ trước khi tạo lượt giữ chỗ.
                </p>
            </form>
        </section>
    </main>

    <script>
    (() => {
        'use strict';

        const roomId = <?= (int)$roomId ?>;
        const initialConflict = <?= json_encode(
            ['type' => $conflictType, 'seconds' => $conflictSeconds],
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_THROW_ON_ERROR
        ) ?>;

        // Dữ liệu lịch chỉ dùng để hỗ trợ giao diện; backend vẫn phải kiểm tra lại.
        const bookedSlots = <?= json_encode(array_map(static function ($s) {
            return [
                'start' => $s['_start']->format('Y-m-d\TH:i:s'),
                'end' => $s['_end']->format('Y-m-d\TH:i:s'),
                'status' => $s['_status'],
            ];
        }, $filteredSlots), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_THROW_ON_ERROR) ?>;

        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');
        const form = document.getElementById('booking-form');
        const errorBox = document.getElementById('time-error');
        const submitButton = document.getElementById('submit-button');

        if (!startInput || !endInput || !form) return;

        const localDateTimeString = (date) => {
            const pad = (number) => String(number).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
        };

        const showError = (message) => {
            if (!errorBox) return;
            errorBox.textContent = message;
            errorBox.classList.remove('hidden');
        };

        const clearError = () => {
            if (!errorBox) return;
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
        };

        // ====== CHẾ ĐỘ CHỜ ======
        const waitBox = document.getElementById('wait-box');
        const waitText = document.getElementById('wait-text');
        const cancelWait = document.getElementById('cancel-wait');
        let pollTimer = null;
        let tickTimer = null;
        let waitLeft = 0;
        let isWaiting = false;
        let isSubmitting = false;
        let pollInFlight = false;
        let pollAbortController = null;

        const fmt = (sec) => {
            const safe = Math.max(0, Number(sec) || 0);
            return `${String(Math.floor(safe / 60)).padStart(2, '0')}:${String(safe % 60).padStart(2, '0')}`;
        };

        const renderWait = () => {
            waitText.textContent = `Khung giờ đang được giữ chỗ (thời gian ước tính còn ${fmt(waitLeft)}). ` +
                'Hệ thống sẽ kiểm tra lại tự động. Nếu người khác hoàn tất đặt phòng, bạn cần chọn giờ khác.';
        };

        const stopWaiting = () => {
            isWaiting = false;
            clearInterval(pollTimer);
            clearInterval(tickTimer);
            pollTimer = null;
            tickTimer = null;
            if (pollAbortController) {
                pollAbortController.abort();
                pollAbortController = null;
            }
            pollInFlight = false;
            startInput.readOnly = false;
            endInput.readOnly = false;
            waitBox.classList.add('hidden');
            waitBox.classList.remove('flex');
        };

        const poll = async () => {
            if (!isWaiting || isSubmitting || pollInFlight) return;
            pollInFlight = true;
            const requestedStart = startInput.value;
            const requestedEnd = endInput.value;
            pollAbortController = new AbortController();

            try {
                const qs = new URLSearchParams({
                    controller: 'user',
                    action: 'checkSlot',
                    room_id: String(roomId),
                    start_time: requestedStart,
                    end_time: requestedEnd
                });
                const res = await fetch('index.php?' + qs.toString(), {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' },
                    signal: pollAbortController.signal
                });

                if (!res.ok) {
                    if (res.status === 401 || res.status === 403) {
                        stopWaiting();
                        showError('Phiên đăng nhập không còn hợp lệ. Vui lòng đăng nhập lại.');
                    }
                    return;
                }

                const data = await res.json();
                // Bỏ qua response cũ hoặc response đến sau khi người dùng đã hủy chờ.
                if (!isWaiting || isSubmitting || requestedStart !== startInput.value || requestedEnd !== endInput.value) return;

                if (data.state === 'free') {
                    isSubmitting = true;
                    stopWaiting();
                    waitText.textContent = 'Phòng vừa trống. Đang gửi yêu cầu giữ chỗ...';
                    submitButton.disabled = true;
                    submitButton.setAttribute('aria-busy', 'true');
                    form.submit(); // Backend phải kiểm tra và giữ chỗ nguyên tử lần cuối.
                } else if (data.state === 'booked' || data.state === 'invalid' || data.state === 'unauthorized') {
                    stopWaiting();
                    showError(data.state === 'booked'
                        ? 'Khung giờ này đã được đặt. Vui lòng chọn khung giờ khác.'
                        : 'Không thể tiếp tục kiểm tra khung giờ. Vui lòng tải lại trang và thử lại.');
                } else if (data.state === 'holding') {
                    waitLeft = Math.max(0, Number(data.seconds_left) || 0);
                    renderWait();
                } else {
                    // Trạng thái không xác định: không tự gửi form.
                    showError('Máy chủ trả về trạng thái không xác định. Vui lòng thử lại.');
                }
            } catch (error) {
                if (error.name !== 'AbortError' && isWaiting) {
                    waitText.textContent = 'Đang gặp sự cố kết nối. Hệ thống sẽ thử kiểm tra lại.';
                }
            } finally {
                pollInFlight = false;
                pollAbortController = null;
            }
        };

        function startWaiting(seconds) {
            if (isSubmitting) return;
            stopWaiting();
            clearError();
            isWaiting = true;
            waitLeft = Math.max(0, Number(seconds) || 0);
            startInput.readOnly = true;
            endInput.readOnly = true;
            waitBox.classList.remove('hidden');
            waitBox.classList.add('flex');
            renderWait();
            tickTimer = setInterval(() => {
                if (waitLeft > 0) waitLeft--;
                renderWait();
            }, 1000);
            pollTimer = setInterval(poll, 4000);
            poll();
        }

        if (cancelWait) cancelWait.addEventListener('click', stopWaiting);

        // Server vừa báo xung đột khi bấm "Tiếp tục"
        if (initialConflict.type === 'holding') startWaiting(initialConflict.seconds || 300);
        else if (initialConflict.type === 'booked') showError('Phòng ĐÃ CÓ NGƯỜI ĐẶT trong khung giờ này. Vui lòng chọn khung giờ khác.');

        form.addEventListener('submit', (event) => {
            if (isSubmitting) {
                event.preventDefault();
                return;
            }
            clearError();

            if (!startInput.value || !endInput.value) {
                event.preventDefault();
                showError('Vui lòng nhập đầy đủ thời gian bắt đầu và kết thúc.');
                return;
            }

            if (endInput.value <= startInput.value) {
                event.preventDefault();
                showError('Thời gian kết thúc phải sau thời gian bắt đầu.');
                return;
            }

            const now = new Date();
            if (startInput.value < localDateTimeString(now)) {
                event.preventDefault();
                showError('Không thể đặt phòng vào thời điểm đã qua. Vui lòng chọn giờ trong tương lai.');
                return;
            }

            // KIỂM TRA TRÙNG LẶP TRỰC TIẾP TRÊN TRÌNH DUYỆT (CHẶN GIAO DIỆN)
            const userStart = new Date(startInput.value);
            const userEnd = new Date(endInput.value);
            
            for (let slot of bookedSlots) {
                let slotStart = new Date(slot.start);
                let slotEnd = new Date(slot.end);
                
                // Nếu giờ người dùng nhập giao thoa với giờ đã bận (start < end_bận và end > start_bận)
                if (userStart < slotEnd && userEnd > slotStart) {
                    event.preventDefault();
                    if (slot.status === 'holding') {
                        // Người khác đang giữ chỗ -> vào chế độ chờ, tự kiểm tra định kỳ
                        startWaiting(300);
                    } else {
                        showError('Phòng ĐÃ CÓ NGƯỜI ĐẶT trong khung giờ này (chờ duyệt / đã duyệt). Vui lòng chọn khung giờ khác.');
                    }
                    return;
                }
            }

            isSubmitting = true;
            submitButton.disabled = true;
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.firstChild.textContent = 'Đang gửi yêu cầu... ';
        });
    })();
    </script>
</body>
</html>