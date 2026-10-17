<?php
class UserModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function checkEmailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $count = $stmt->get_result()->num_rows;
        $stmt->close();
        return $count > 0;
    }

    public function createUser($full_name, $email, $hashed_password, $role = 'user') {
        $stmt = $this->conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Trang chủ đặt phòng của User
    public function index() {
        $roomModel = $this->model('RoomModel');
        $active_rooms = $roomModel->getActiveRooms();

        $this->view('user/index', [
            'active_rooms' => $active_rooms
        ]);
    }

    // Trang lịch sử đặt phòng của tôi
    public function myBookings() {
        $bookingModel = $this->model('BookingModel');
        $user_id = $_SESSION['user_id'];
        
        $history = $bookingModel->getBookingHistoryByUser($user_id);

        $this->view('user/my_bookings', [
            'history' => $history
        ]);
    }
    
}
?>