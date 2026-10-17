<?php
class AdminController extends Controller {

    // Khởi tạo chung: Bắt buộc kiểm tra bảo mật quyền Admin
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    // 1. Trang tổng quan Admin (Dashboard)
    public function dashboard() {
        $roomModel = $this->model('RoomModel');
        $bookingModel = $this->model('BookingModel');

        $total_rooms = count($roomModel->getActiveRooms());
        $pending_requests = $bookingModel->countPendingRequests();
        $current_month = date('m');
        $current_year = date('Y');
        $monthly_bookings = $bookingModel->countMonthlyBookings($current_month, $current_year);
        $recent_activities = $bookingModel->getRecentActivities(5);

        $this->view('admin/dashboard', [
            'total_rooms' => $total_rooms,
            'pending_requests' => $pending_requests,
            'current_month' => $current_month,
            'current_year' => $current_year,
            'monthly_bookings' => $monthly_bookings,
            'recent_activities' => $recent_activities
        ]);
    }

    // 2. Quản lý danh sách phòng
    public function manageRooms() {
        $roomModel = $this->model('RoomModel');
        $rooms = $roomModel->getAllRooms();

        $this->view('admin/manage_rooms', ['rooms' => $rooms]);
    }

    // 3. Thêm phòng mới
    public function createRoom() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_name = trim($_POST['room_name'] ?? '');
            $room_number = trim($_POST['room_number'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $area = (float)($_POST['area'] ?? 0); 
            $location = trim($_POST['location'] ?? '');
            $equipment_details = trim($_POST['equipment_details'] ?? '');
            
            $image_url = 'https://via.placeholder.com/400x200?text=No+Image';

            // Xử lý upload file ảnh từ thiết bị
            if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['room_image']['tmp_name'];
                $file_ext = strtolower(pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($file_ext, $allowed)) {
                    $new_name = 'room_' . uniqid() . '.' . $file_ext;
                    $upload_dir = '../public/uploads/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                        $image_url = 'uploads/' . $new_name;
                    }
                }
            }

            // Ràng buộc dữ liệu
            if (empty($room_name) || empty($room_number) || empty($location)) {
                $error = "Vui lòng nhập đầy đủ Tên phòng, Mã phòng và Vị trí.";
            } elseif (!is_numeric($room_number)) {
                $error = "Mã phòng bắt buộc phải là dạng số.";
            } elseif ($capacity <= 0 || $area <= 0) {
                $error = "Sức chứa và Diện tích phải lớn hơn 0.";
            } else {
                $roomModel = $this->model('RoomModel');
                $data = [
                    'room_name' => $room_name,
                    'room_number' => $room_number,
                    'capacity' => $capacity,
                    'area' => $area,
                    'location' => $location,
                    'equipment_details' => $equipment_details,
                    'image_url' => $image_url
                ];

                if ($roomModel->insertRoom($data)) {
                    header("Location: index.php?controller=admin&action=manageRooms&msg=success");
                    exit();
                } else {
                    $error = "Lỗi hệ thống: Không thể lưu dữ liệu.";
                }
            }
        }

        $this->view('admin/create_room', ['error' => $error]);
    }

    // 4. Chỉnh sửa thông tin phòng
    public function editRoom() {
        $roomModel = $this->model('RoomModel');
        $id = $_GET['id'] ?? 0;
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_name = trim($_POST['room_name'] ?? '');
            $room_number = trim($_POST['room_number'] ?? ''); 
            $capacity = (int)($_POST['capacity'] ?? 0);
            $area = (float)($_POST['area'] ?? 0);
            $location = trim($_POST['location'] ?? '');
            
            $selected_equipments = $_POST['equipments'] ?? [];
            $custom_equipment = trim($_POST['custom_equipment'] ?? '');
            if (!empty($custom_equipment)) {
                $selected_equipments[] = $custom_equipment;
            }
            $equipment_details = implode(', ', $selected_equipments);

            $old_room = $roomModel->getRoomById($id);
            $image_url = $old_room['image_url'] ?? '';

            if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['room_image']['tmp_name'];
                $file_ext = strtolower(pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($file_ext, $allowed)) {
                    $new_name = 'room_' . uniqid() . '.' . $file_ext;
                    $upload_dir = '../public/uploads/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                        $image_url = 'uploads/' . $new_name;
                    }
                }
            }

            if (empty($room_name) || empty($room_number) || empty($location)) {
                $error = "Vui lòng nhập đầy đủ Tên phòng, Mã phòng và Vị trí.";
            } elseif (!is_numeric($room_number)) {
                $error = "Mã phòng bắt buộc phải là dạng số.";
            } elseif ($capacity <= 0 || $area <= 0) {
                $error = "Sức chứa và Diện tích phải lớn hơn 0.";
            } else {
                $data = [
                    'room_name' => $room_name,
                    'room_number' => $room_number,
                    'capacity' => $capacity,
                    'area' => $area,
                    'location' => $location,
                    'equipment_details' => $equipment_details,
                    'image_url' => $image_url
                ];
                if ($roomModel->updateRoom($id, $data)) {
                    header("Location: index.php?controller=admin&action=manageRooms&msg=updated");
                    exit();
                } else {
                    $error = "Lỗi hệ thống: Không thể cập nhật dữ liệu.";
                }
            }
        }

        $room = $roomModel->getRoomById($id);
        $this->view('admin/edit_room', ['room' => $room, 'error' => $error]);
    }

    // 5. Đổi trạng thái active/maintenance
    public function toggleStatus() {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id > 0) {
            $roomModel = $this->model('RoomModel');
            $new_status = $roomModel->toggleStatus($id);
            
            if ($new_status) {
                $msg = ($new_status === 'active') ? 'activated' : 'suspended';
                header("Location: index.php?controller=admin&action=manageRooms&status=" . $msg);
                exit();
            }
        }
        header("Location: index.php?controller=admin&action=manageRooms");
        exit();
    }

    // 6. Lịch Timeline phòng họp
    public function schedule() {
        $current_date = $_GET['date'] ?? date('Y-m-d');
        $display_date_str = date('d/m/Y', strtotime($current_date));
        
        $roomModel = $this->model('RoomModel');
        $bookingModel = $this->model('BookingModel');

        $rooms = $roomModel->getActiveRooms();
        $approved_bookings = $bookingModel->getApprovedBookingsByDate($current_date);
        
        $bookings_by_room = [];
        foreach ($approved_bookings as $b) {
            $bookings_by_room[$b['room_id']][] = $b;
        }

        $this->view('admin/schedule', [
            'rooms' => $rooms,
            'bookings_by_room' => $bookings_by_room,
            'current_date' => $current_date,
            'display_date_str' => $display_date_str
        ]);
    }

    // 7. Trang phê duyệt yêu cầu đặt phòng
    public function approveBookings() {
        $bookingModel = $this->model('BookingModel');
        $pending_bookings = $bookingModel->getPendingBookings();

        $this->view('admin/approve_bookings', ['pending_bookings' => $pending_bookings]);
    }

    // 8. Xử lý duyệt hoặc từ chối đơn
    public function processBooking() {
        $action_type = $_GET['action_type'] ?? '';
        $booking_id = (int)($_GET['id'] ?? 0);

        $new_status = '';
        if ($action_type === 'approve') {
            $new_status = 'approved';
        } elseif ($action_type === 'reject') {
            $new_status = 'cancelled';
        }

        if ($new_status !== '' && $booking_id > 0) {
            $bookingModel = $this->model('BookingModel');
            
            if ($bookingModel->updateBookingStatus($booking_id, $new_status)) {
                header("Location: index.php?controller=admin&action=approveBookings&msg=" . $new_status);
                exit();
            } else {
                header("Location: index.php?controller=admin&action=approveBookings&msg=error");
                exit();
            }
        }

        header("Location: index.php?controller=admin&action=approveBookings");
        exit();
    }

    // 9. Giao diện và xử lý Đặt phòng nhanh dành cho Admin
    public function createBooking() {
        $roomModel = $this->model('RoomModel');
        $bookingModel = $this->model('BookingModel');
        
        $rooms = $roomModel->getActiveRooms();
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_id = (int)($_POST['room_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $start_time = trim($_POST['start_time'] ?? '');
            $end_time = trim($_POST['end_time'] ?? '');
            $participant_count = (int)($_POST['participant_count'] ?? 0);
            $user_id = $_SESSION['user_id']; // Lấy ID của Admin đang thao tác

            if ($room_id <= 0 || empty($title) || empty($start_time) || empty($end_time)) {
                $error = "Vui lòng điền đầy đủ thông tin bắt buộc.";
            } else {
                $data = [
                    'room_id' => $room_id,
                    'user_id' => $user_id,
                    'title' => $title,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'participant_count' => $participant_count
                ];

                // Gọi Model thực thi, Controller không chạm vào SQL
                if ($bookingModel->adminCreateBooking($data)) {
                    header("Location: index.php?controller=admin&action=schedule&msg=booked_success");
                    exit();
                } else {
                    $error = "Lỗi hệ thống: Không thể tạo lịch đặt phòng.";
                }
            }
        }

        $this->view('user/create_booking', [
            'rooms' => $rooms,
            'error' => $error,
            'success' => $success
        ]);
    }
}
?>