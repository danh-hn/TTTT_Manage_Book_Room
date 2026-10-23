<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');
class UserController extends Controller
{
    // 1. Hiển thị trang chủ danh mục phòng & modal đặt phòng
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $roomModel = $this->model('RoomModel');
        $active_rooms = $roomModel->getActiveRooms();

        $this->view('user/index', ['active_rooms' => $active_rooms]);
    }

    // 2. Hiển thị lịch sử đặt phòng của tôi
    public function myBookings()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $bookingModel = $this->model('BookingModel');
        $history = $bookingModel->getHistoryByUserId($_SESSION['user_id']);

        $this->view('user/my_bookings', ['history' => $history]);
    }

    // 3. Xử lý logic đặt phòng nhanh
    public function bookRoom()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $room_id = (int) ($_POST['room_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $participant_count = (int) ($_POST['participant_count'] ?? 1);
            $extra_services = $_POST['extra_services'] ?? 'Không';
            $start_time = $_POST['start_time'] ?? '';
            $end_time = $_POST['end_time'] ?? '';

            // Kiểm tra thời gian cơ bản
            if (strtotime($start_time) >= strtotime($end_time)) {
                echo "<script>alert('Lỗi: Thời gian kết thúc phải sau thời gian bắt đầu.'); window.history.back();</script>";
                exit();
            }

            $bookingModel = $this->model('BookingModel');
            $roomModel = $this->model('RoomModel');

            // Kiểm tra sức chứa phòng có hợp lệ không
            $room = $roomModel->getRoomById($room_id);
            if (!$room || $participant_count > $room['capacity']) {
                echo "<script>alert('Lỗi: Số lượng người tham gia vượt quá sức chứa của phòng.'); window.history.back();</script>";
                exit();
            }

            // Kiểm tra giao thoa thời gian (trùng lịch với đơn đã duyệt khác)
            if ($bookingModel->checkRoomConflict($room_id, $start_time, $end_time)) {
                echo "<script>alert('Lỗi: Phòng đã có lịch đặt được duyệt trong khoảng thời gian này.'); window.history.back();</script>";
                exit();
            }

            // Tiến hành lưu vào CSDL
            $data = [
                'room_id' => $room_id,
                'user_id' => $user_id,
                'title' => $title,
                'participant_count' => $participant_count,
                'extra_services' => $extra_services,
                'start_time' => $start_time,
                'end_time' => $end_time
            ];

            if ($bookingModel->insertBooking($data)) {
                header("Location: index.php?controller=user&action=myBookings&msg=success");
                exit();
            } else {
                echo "<script>alert('Lỗi hệ thống: Không thể gửi yêu cầu đặt phòng.'); window.history.back();</script>";
                exit();
            }
        }
    }

    // Hiển thị form đặt phòng chi tiết cho một phòng cụ thể
    public function bookingForm()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $room_id = (int) ($_GET['room_id'] ?? 0);
        $roomModel = $this->model('RoomModel');
        $room = $roomModel->getRoomById($room_id);

        if (!$room) {
            header("Location: index.php?controller=user&action=index");
            exit();
        }

        $this->view('user/booking_detail', [
            'room' => $room
        ]);
    }

    // Bước 1: Hiển thị lưới chọn khung giờ trống của phòng
    public function selectTimeSlot()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $room_id = (int) ($_GET['room_id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');

        $roomModel = $this->model('RoomModel');
        $bookingModel = $this->model('BookingModel');

        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            header("Location: index.php?controller=user&action=index");
            exit();
        }

        $booked_slots = $bookingModel->getRoomScheduleByDate($room_id, $date);

        $this->view('user/select_timeslot', [
            'room' => $room,
            'date' => $date,
            'booked_slots' => $booked_slots
        ]);
    }

    // Xử lý lưu đơn đặt phòng chi tiết
    public function bookRoomWithDetails()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=user&action=index");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $holding_id = (int) ($_POST['holding_id'] ?? 0);

        if ($holding_id === 0) {
            echo "<script>alert('Lỗi: Bị mất phiên giữ chỗ. Vui lòng quay lại chọn giờ!'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }

        $title = trim($_POST['title'] ?? '');
        $participant_count = max(1, (int) ($_POST['participant_count'] ?? 1));
        $services = array_map('trim', (array) ($_POST['services'] ?? []));
        $extra_services = implode(', ', $services);

        $bookingModel = $this->model('BookingModel');
        $data = [
            'user_id' => $user_id,
            'title' => $title,
            'participant_count' => $participant_count,
            'extra_services' => $extra_services
        ];

        $result = $bookingModel->updateHoldingToPending($holding_id, $data);

        if ($result === true) {
            header("Location: index.php?controller=user&action=myBookings&msg=success");
            exit();
        } elseif (is_string($result)) {
            echo "<script>alert('Lỗi Database: " . addslashes($result) . "'); window.history.back();</script>";
            exit();
        } else {
            echo "<script>alert('Lỗi: ID giữ chỗ không khớp hoặc trạng thái không hợp lệ. Vui lòng đặt lại!'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }
    }

    // Xử lý tự động hủy đơn khi hết 5 phút giữ chỗ
    public function cancelHolding()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }
        $holding_id = (int) ($_GET['holding_id'] ?? 0);
        if ($holding_id > 0) {
            $this->model('BookingModel')->deleteBooking($holding_id, $_SESSION['user_id']);
        }
        header("Location: index.php?controller=user&action=index");
        exit();
    }

    // Bước 2: Người dùng bấm "Tiếp tục đặt phòng" -> khóa tạm khung giờ 5 phút
    public function lockTimeSlot()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=user&action=index");
            exit();
        }

        $user_id = (int) $_SESSION['user_id'];
        $room_id = (int) ($_POST['room_id'] ?? 0);
        $date = $_POST['date'] ?? date('Y-m-d');
        $backUrl = "index.php?controller=user&action=selectTimeSlot&room_id=$room_id&date=" . urlencode($date);

        $startTs = strtotime($_POST['start_time'] ?? '');
        $endTs = strtotime($_POST['end_time'] ?? '');
        if (!$room_id || !$startTs || !$endTs || $endTs <= $startTs || $startTs < time() - 60) {
            echo "<script>alert('Thời gian không hợp lệ. Vui lòng chọn lại!'); window.location.href='$backUrl';</script>";
            exit();
        }
        
        $start_time = date('Y-m-d H:i:s', $startTs);
        $end_time = date('Y-m-d H:i:s', $endTs);

        $bookingModel = $this->model('BookingModel');
        $result = $bookingModel->createHoldingSlot($room_id, $user_id, $start_time, $end_time);

        // THÀNH CÔNG -> chuyển (redirect) sang form chi tiết
        if (is_int($result) && $result > 0) {
            header("Location: index.php?controller=user&action=bookingDetail&holding_id=$result");
            exit();
        }

        if ($result === false) {
            echo "<script>alert('Lỗi hệ thống, vui lòng thử lại!'); window.location.href='$backUrl';</script>";
            exit();
        }

        // XUNG ĐỘT -> lấy thời gian chờ
        $state = $bookingModel->getSlotState($room_id, $start_time, $end_time);
        $room = $this->model('RoomModel')->getRoomById($room_id);

        $this->view('user/select_timeslot', [
            'room' => $room,
            'date' => $date,
            'booked_slots' => $bookingModel->getRoomScheduleByDate($room_id, $date),
            'posted_start' => $start_time,
            'posted_end' => $end_time,
            'conflict_type' => $result === 'conflict_holding' ? 'holding' : 'booked',
            // Đã Bọc An Toàn Bằng Null Coalescing (??)
            'conflict_seconds' => $state['seconds_left'] ?? 0 
        ]);
    }

    // Bước 3: Form chi tiết - dựa hoàn toàn vào bản ghi holding trong DB
    public function bookingDetail()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $holding_id = (int) ($_GET['holding_id'] ?? 0);
        $bookingModel = $this->model('BookingModel');
        $holding = $bookingModel->getHoldingById($holding_id, (int) $_SESSION['user_id']);

        if (!$holding) {
            echo "<script>alert('Phiên giữ chỗ đã hết hạn hoặc không tồn tại. Vui lòng chọn lại giờ!'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }

        $room = $this->model('RoomModel')->getRoomById((int) $holding['room_id']);

        $this->view('user/booking_detail', [
            'room' => $room,
            'start_time' => str_replace(' ', 'T', substr($holding['start_time'] ?? '', 0, 16)),
            'end_time' => str_replace(' ', 'T', substr($holding['end_time'] ?? '', 0, 16)),
            'holding_id' => $holding_id,
            // Đã Bọc An Toàn Bằng Null Coalescing (??)
            'seconds_left' => $holding['seconds_left'] ?? 0 
        ]);
    }

    // Endpoint cho người đang chờ (JS gọi định kỳ): free | holding | booked
    public function checkSlot()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['state' => 'unauthorized']);
            exit();
        }

        $room_id = (int) ($_GET['room_id'] ?? 0);
        $startTs = strtotime($_GET['start_time'] ?? '');
        $endTs = strtotime($_GET['end_time'] ?? '');
        
        if (!$room_id || !$startTs || !$endTs || $endTs <= $startTs) {
            echo json_encode(['state' => 'invalid']);
            exit();
        }

        $bookingModel = $this->model('BookingModel');
        echo json_encode($bookingModel->getSlotState($room_id, date('Y-m-d H:i:s', $startTs), date('Y-m-d H:i:s', $endTs)));
        exit();
    }
}
?>