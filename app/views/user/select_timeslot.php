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

        // Chỉ hiển thị lịch thuộc ngày đang xem; ẩn lịch đã kết thúc.
        if ($slotStart->format('Y-m-d') !== $selectedDate || $slotEnd <= $now) {
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
                        Phòng: <strong class="text-ink"><?= h($roomName) ?></strong>
                        <span aria-hidden="true"> · </span><?= h($roomLocation) ?>
                    </p>
                </div>
            </div>

            <form method="GET" action="index.php" class="flex flex-col gap-2 sm:flex-row sm:items-center" aria-label="Chọn ngày xem lịch">
                <input type="hidden" name="controller" value="user">
                <input type="hidden" name="action" value="selectTimeSlot">
                <input type="hidden" name="room_id" value="<?= (int)$roomId ?>">
                <label for="view_date" class="text-sm font-semibold">Ngày xem lịch</label>
                <input
                    id="view_date"
                    type="date"
                    name="date"
                    value="<?= h($selectedDate) ?>"
                    min="<?= h($todayString) ?>"
                    required
                    class="min-w-0 rounded-xl border border-line bg-white px-3 py-2.5 text-sm focus:border-primary focus:outline-none">
                <button type="submit" class="rounded-xl border border-line px-4 py-2.5 text-sm font-semibold transition hover:bg-slate-50">
                    Xem lịch
                </button>
            </form>
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

        <section aria-label="Chú thích trạng thái" class="flex flex-wrap gap-x-5 gap-y-3 rounded-xl border border-line bg-white px-4 py-4 text-sm shadow-sm sm:px-6">
            <div class="flex items-center gap-2">
                <span class="h-3.5 w-3.5 rounded border border-green-300 bg-green-100"></span>
                <span>Khung giờ có thể đặt</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-3.5 w-3.5 rounded border border-blue-300 bg-blue-100"></span>
                <span>Đã xác nhận</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-3.5 w-3.5 rounded border border-amber-300 bg-amber-100"></span>
                <span>Đang giữ chỗ hoặc chờ duyệt</span>
            </div>
        </section>

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

        const selectedDate = <?= json_encode($selectedDate, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');
        const form = document.getElementById('booking-form');
        const errorBox = document.getElementById('time-error');
        const submitButton = document.getElementById('submit-button');

        if (!startInput || !endInput || !form) return;

        // Tạo chuỗi ngày giờ theo giờ địa phương, không dùng toISOString() vì hàm đó đổi sang UTC.
        const localDateTimeString = (date) => {
            const pad = (number) => String(number).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
        };

        const localDateString = (date) => {
            const pad = (number) => String(number).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
        };

        const showError = (message) => {
            errorBox.textContent = message;
            errorBox.classList.remove('hidden');
        };

        const clearError = () => {
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
        };

        const updateConstraints = () => {
            const now = new Date();
            const minimumStart = selectedDate === localDateString(now)
                ? localDateTimeString(now)
                : `${selectedDate}T00:00`;

            startInput.min = minimumStart;
            endInput.min = startInput.value && startInput.value > minimumStart
                ? startInput.value
                : minimumStart;

            // Không tự sửa giá trị người dùng đang nhập; chỉ báo lỗi để họ chủ động điều chỉnh.
            if (startInput.value && endInput.value && endInput.value <= startInput.value) {
                showError('Thời gian kết thúc phải sau thời gian bắt đầu.');
            } else {
                clearError();
            }
        };

        startInput.addEventListener('change', () => {
            endInput.min = startInput.value || startInput.min;
            if (endInput.value && startInput.value && endInput.value <= startInput.value) {
                showError('Thời gian kết thúc phải sau thời gian bắt đầu. Vui lòng chọn lại.');
            } else {
                clearError();
            }
        });

        endInput.addEventListener('change', updateConstraints);
        startInput.addEventListener('input', updateConstraints);

        form.addEventListener('submit', (event) => {
            clearError();

            if (!startInput.value || !endInput.value) {
                event.preventDefault();
                showError('Vui lòng nhập đầy đủ thời gian bắt đầu và kết thúc.');
                return;
            }

            // if (startInput.value.slice(0, 10) !== selectedDate ||
            //     endInput.value.slice(0, 10) !== selectedDate) {
            //     event.preventDefault();
            //     showError('Thời gian bắt đầu và kết thúc phải thuộc ngày đang xem. Vui lòng chọn lại ngày nếu cần.');
            //     return;
            // }

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

            submitButton.disabled = true;
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.firstChild.textContent = 'Đang gửi yêu cầu... ';
        });

        updateConstraints();
    })();
    </script>
</body>
</html>
