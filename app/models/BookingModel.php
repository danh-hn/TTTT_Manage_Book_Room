<?php
class BookingModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function countPendingRequests() {
        $sql = "SELECT COUNT(id) as total FROM bookings WHERE status = 'pending'";
        return $this->conn->query($sql)->fetch_assoc()['total'];
    }

    public function countMonthlyBookings($month, $year) {
        $stmt = $this->conn->prepare("SELECT COUNT(id) as total FROM bookings WHERE MONTH(start_time) = ? AND YEAR(start_time) = ?");
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        return $total;
    }

    public function getRecentActivities($limit = 5) {
        $stmt = $this->conn->prepare("
            SELECT b.*, u.full_name, r.room_name 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            ORDER BY b.id DESC LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function getPendingBookings() {
        $result = $this->conn->query("
            SELECT b.*, u.full_name, u.email, r.room_name, r.room_number, r.capacity 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            WHERE b.status = 'pending'
            ORDER BY b.start_time ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateBookingStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Lấy danh sách lịch đặt phòng đã được duyệt theo ngày cụ thể
    public function getApprovedBookingsByDate($date) {
        $stmt = $this->conn->prepare("
            SELECT b.*, u.full_name 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            WHERE DATE(b.start_time) = ? AND b.status = 'approved'
        ");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // Admin tạo lịch đặt phòng trực tiếp (trạng thái mặc định là approved)
    public function adminCreateBooking($data) {
        $stmt = $this->conn->prepare("INSERT INTO bookings (room_id, user_id, title, start_time, end_time, participant_count, status) VALUES (?, ?, ?, ?, ?, ?, 'approved')");
        $stmt->bind_param("iisssi", $data['room_id'], $data['user_id'], $data['title'], $data['start_time'], $data['end_time'], $data['participant_count']);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    public function getHistoryByUserId($user_id) {
        $stmt = $this->conn->prepare("
            SELECT b.*, r.room_name, r.room_number, r.image_url 
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            WHERE b.user_id = ?
            ORDER BY b.start_time DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // Lưu yêu cầu đặt phòng mới của User (Bắt buộc phải ở trạng thái 'pending' để chờ Admin duyệt)
    public function insertBooking($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO bookings (room_id, user_id, title, participant_count, extra_services, start_time, end_time, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("iisisss", 
            $data['room_id'], 
            $data['user_id'], 
            $data['title'], 
            $data['participant_count'], 
            $data['extra_services'], 
            $data['start_time'], 
            $data['end_time']
        );
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Lấy danh sách lịch đã đặt của một phòng trong ngày cụ thể để lọc giờ trống
    public function getBookingsByRoomAndDate($room_id, $date) {
        $stmt = $this->conn->prepare("
            SELECT start_time, end_time, title 
            FROM bookings 
            WHERE room_id = ? 
            AND status IN ('approved', 'pending') 
            AND DATE(start_time) = ?
            ORDER BY start_time ASC
        ");
        $stmt->bind_param("is", $room_id, $date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }
    
    // Kiểm tra xung đột bao gồm cả trạng thái approved, pending và holding còn hạn
    public function checkRoomConflict($room_id, $start_time, $end_time) {
        // Dọn dẹp các đơn holding đã quá hạn 5 phút trước khi kiểm tra
        $this->conn->query("DELETE FROM bookings WHERE status = 'holding' AND expires_at < NOW()");

        $stmt = $this->conn->prepare("
            SELECT id FROM bookings 
            WHERE room_id = ? 
            AND (
                status = 'approved' 
                OR status = 'pending' 
                OR (status = 'holding' AND expires_at > NOW())
            ) 
            AND (start_time < ? AND end_time > ?)
        ");
        $stmt->bind_param("iss", $room_id, $end_time, $start_time);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;
        $stmt->close();
        return $count > 0;
    }

    // Tạo bản ghi giữ chỗ tạm thời trong 5 phút
    public function createHoldingBooking($data) {
        // Dọn dẹp các đơn holding cũ của user này nếu có
        $stmt_del = $this->conn->prepare("DELETE FROM bookings WHERE user_id = ? AND status = 'holding'");
        $stmt_del->bind_param("i", $data['user_id']);
        $stmt_del->execute();
        $stmt_del->close();

        // Tạo holding mới hết hạn sau 5 phút
        $stmt = $this->conn->prepare("
            INSERT INTO bookings (room_id, user_id, title, start_time, end_time, status, expires_at) 
            VALUES (?, ?, 'Đang giữ chỗ tạm thời', ?, ?, 'holding', DATE_ADD(NOW(), INTERVAL 5 MINUTE))
        ");
        $stmt->bind_param("iiss", $data['room_id'], $data['user_id'], $data['start_time'], $data['end_time']);
        $success = $stmt->execute();
        $booking_id = $stmt->insert_id;
        $stmt->close();
        return $success ? $booking_id : false;
    }

    // Lấy toàn bộ lịch bận (approved, pending, holding hợp lệ) của phòng theo ngày để vẽ lưới
    public function getRoomScheduleByDate($room_id, $date) {
        $this->conn->query("DELETE FROM bookings WHERE status = 'holding' AND expires_at < NOW()");

        $stmt = $this->conn->prepare("
            SELECT start_time, end_time, status, user_id 
            FROM bookings 
            WHERE room_id = ? 
            AND (
                status = 'approved' 
                OR status = 'pending' 
                OR (status = 'holding' AND expires_at > NOW())
            )
            AND DATE(start_time) = ?
        ");
        $stmt->bind_param("is", $room_id, $date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // Chuyển đơn giữ chỗ thành pending an toàn dựa vào expires_at
    public function updateHoldingToPending($holding_id, $data) {
        $sql = "UPDATE bookings 
                SET title = ?, participant_count = ?, extra_services = ?, status = 'pending', expires_at = NULL 
                WHERE id = ? AND user_id = ? AND expires_at IS NOT NULL";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return "Lỗi Prepare SQL: " . $this->conn->error;
        }

        // s: title, i: participant_count, s: extra_services, i: holding_id, i: user_id
        $stmt->bind_param("sisii", 
            $data['title'], 
            $data['participant_count'], 
            $data['extra_services'], 
            $holding_id, 
            $data['user_id']
        );
        
        if (!$stmt->execute()) {
            return "Lỗi Execute SQL: " . $stmt->error;
        }
        
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected > 0;
    }

    // Hàm xóa đơn giữ chỗ khi hết hạn
    public function deleteBooking($holding_id, $user_id) {
        // Chỉ xóa khi đơn đó đang có thời gian hết hạn (tránh xóa nhầm đơn chính thức)
        $sql = "DELETE FROM bookings WHERE id = ? AND user_id = ? AND expires_at IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $holding_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>