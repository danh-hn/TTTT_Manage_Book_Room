<?php
class UserController extends Controller {

    // 1. Hiển thị trang chủ danh mục phòng & modal đặt phòng
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $roomModel = $this->model('RoomModel');
        $active_rooms = $roomModel->getActiveRooms();

        $this->view('user/index', ['active_rooms' => $active_rooms]);
    }

    // 2. Hiển thị lịch sử đặt phòng của tôi
    public function myBookings() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $bookingModel = $this->model('BookingModel');
        $history = $bookingModel->getHistoryByUserId($_SESSION['user_id']);

        $this->view('user/my_bookings', ['history' => $history]);
    }

    // 3. Xử lý logic đặt phòng nhanh (thay thế file book_room.php cũ)
    public function storeBooking() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            $user_id = $_SESSION['user_id'];
            $room_id = (int)$_POST['room_id'];
            $title = trim($_POST['title']);
            $participant_count = (int)$_POST['participant_count'];
            $extra_services = $_POST['extra_services'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];

            if (strtotime($start_time) >= strtotime($end_time)) {
                echo "<script>alert('Lỗi: Thời gian kết thúc phải sau thời gian bắt đầu.'); window.history.back();</script>";
                exit();
            }

            // Thực hiện các tầng bảo vệ kiểm tra sức chứa & giao thoa thời gian (như trong book_room.php cũ)
            // Sau đó chèn vào bảng bookings với status = 'pending'
            // Code transaction giữ nguyên tương tự file book_room.php của bạn.
            
            header("Location: index.php?controller=user&action=index&msg=success");
            exit();
        }
    }
    // 3. Xử lý logic đặt phòng nhanh (Đổi tên từ storeBooking thành bookRoom)
    // 3. Xử lý logic đặt phòng nhanh
    public function bookRoom() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $room_id = (int)($_POST['room_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $participant_count = (int)($_POST['participant_count'] ?? 1);
            $extra_services = $_POST['extra_services'] ?? 'Không';
            $start_time = $_POST['start_time'] ?? '';
            $end_time = $_POST['end_time'] ?? '';

            // 1. Kiểm tra thời gian cơ bản
            if (strtotime($start_time) >= strtotime($end_time)) {
                echo "<script>alert('Lỗi: Thời gian kết thúc phải sau thời gian bắt đầu.'); window.history.back();</script>";
                exit();
            }

            $bookingModel = $this->model('BookingModel');
            $roomModel = $this->model('RoomModel');

            // 2. Kiểm tra sức chứa phòng có hợp lệ không
            $room = $roomModel->getRoomById($room_id);
            if (!$room || $participant_count > $room['capacity']) {
                echo "<script>alert('Lỗi: Số lượng người tham gia vượt quá sức chứa của phòng.'); window.history.back();</script>";
                exit();
            }

            // 3. Kiểm tra giao thoa thời gian (trùng lịch với đơn đã duyệt khác)
            if ($bookingModel->checkRoomConflict($room_id, $start_time, $end_time)) {
                echo "<script>alert('Lỗi: Phòng đã có lịch đặt được duyệt trong khoảng thời gian này.'); window.history.back();</script>";
                exit();
            }

            // 4. Tiến hành lưu vào CSDL thông qua Model
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
    public function bookingForm() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $room_id = (int)($_GET['room_id'] ?? 0);
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
    public function selectTimeSlot() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $room_id = (int)($_GET['room_id'] ?? 0);
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

    // Xử lý khi người dùng chọn khung giờ -> Tạo trạng thái holding rồi chuyển sang trang điền form
    public function lockTimeSlot() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=user&action=index");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $room_id = (int)$_POST['room_id'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        $bookingModel = $this->model('BookingModel');

        // Kiểm tra xem giờ đó có bị tranh chấp không
        if ($bookingModel->checkRoomConflict($room_id, $start_time, $end_time)) {
            echo "<script>alert('Khung giờ này vừa có người chọn hoặc đã có lịch đặt. Vui lòng chọn khung giờ khác!'); window.history.back();</script>";
            exit();
        }

        // Tiến hành giữ chỗ trong 5 phút
        $holding_id = $bookingModel->createHoldingBooking([
            'room_id' => $room_id,
            'user_id' => $user_id,
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);

        if ($holding_id) {
            // Chuyển sang trang điền thông tin chi tiết kèm theo booking_id giữ chỗ
            header("Location: index.php?controller=user&action=bookingForm&room_id=$room_id&holding_id=$holding_id&start=$start_time&end=$end_time");
            exit();
        } else {
            echo "<script>alert('Lỗi hệ thống: Không thể giữ chỗ tạm thời.'); window.history.back();</script>";
            exit();
        }
    }
    
    // Xử lý lưu đơn đặt phòng chi tiết
    public function bookRoomWithDetails() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=user&action=index");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $holding_id = (int)($_POST['holding_id'] ?? 0);
        
        if ($holding_id === 0) {
            echo "<script>alert('Lỗi: Bị mất phiên giữ chỗ. Vui lòng quay lại chọn giờ!'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }
        
        $title = trim($_POST['title']);
        $participant_count = (int)$_POST['participant_count'];
        
        $bookingModel = $this->model('BookingModel');
        $data = [
            'user_id' => $user_id,
            'title' => $title,
            'participant_count' => $participant_count
        ];

        $result = $bookingModel->updateHoldingToPending($holding_id, $data);
        
        if ($result === true) {
            header("Location: index.php?controller=user&action=myBookings&msg=success");
            exit();
        } elseif (is_string($result)) {
            // NẾU LỖI CSDL, NÓ SẼ HIỆN CHÍNH XÁC LỖI Ở ĐÂY
            echo "<script>alert('Lỗi Database: " . addslashes($result) . "'); window.history.back();</script>";
            exit();
        } else {
            echo "<script>alert('Lỗi: ID giữ chỗ không khớp hoặc trạng thái không hợp lệ. Vui lòng đặt lại!'); window.location.href='index.php?controller=user&action=index';</script>";
            exit();
        }
    }

    // Xử lý tự động hủy đơn khi hết 5 phút giữ chỗ
    public function cancelHolding() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }
        $holding_id = (int)($_GET['holding_id'] ?? 0);
        if ($holding_id > 0) {
            $this->model('BookingModel')->deleteBooking($holding_id, $_SESSION['user_id']);
        }
        header("Location: index.php?controller=user&action=index");
        exit();
    }
}
?>