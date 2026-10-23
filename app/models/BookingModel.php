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

    // Cập nhật trạng thái đơn (Có kiểm tra chống Admin duyệt đè lịch)
    public function updateBookingStatus($id, $status) {
        // Nếu Admin bấm duyệt đơn ('approved')
        if ($status === 'approved') {
            // 1. Lấy thông tin phòng và thời gian của đơn chuẩn bị duyệt
            $stmtInfo = $this->conn->prepare("SELECT room_id, start_time, end_time FROM bookings WHERE id = ?");
            $stmtInfo->bind_param("i", $id);
            $stmtInfo->execute();
            $resInfo = $stmtInfo->get_result();
            if ($resInfo->num_rows === 0) {
                $stmtInfo->close();
                return false;
            }
            $booking = $resInfo->fetch_assoc();
            $stmtInfo->close();

            // 2. Kiểm tra xem phòng này đã có đơn nào KHÁC được 'approved' trùng giờ chưa
            $stmtCheck = $this->conn->prepare("
                SELECT id FROM bookings 
                WHERE room_id = ? 
                AND id != ? 
                AND status = 'approved' 
                AND start_time < ? 
                AND end_time > ?
            ");
            $stmtCheck->bind_param("iiss", $booking['room_id'], $id, $booking['end_time'], $booking['start_time']);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                // Báo lỗi ngầm, từ chối duyệt vì trùng lịch đã duyệt trước đó
                return false; 
            }
            $stmtCheck->close();
        }

        // Tiến hành cập nhật trạng thái bình thường
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
        // Tự động xóa các đơn giữ chỗ tạm thời đã quá hạn để chúng biến mất khỏi lịch sử
        $this->conn->query("DELETE FROM bookings WHERE status = 'holding' AND expires_at < NOW()");

        $stmt = $this->conn->prepare("
            SELECT b.*, r.room_name, r.room_number, r.image_url, r.location 
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

    // Chuyển đơn holding thành pending (Có kiểm tra xung đột chặng cuối)
    public function updateHoldingToPending($holding_id, $data) {
        $holding_id = (int)$holding_id;
        // 1. Lấy thông tin room_id, start_time, end_time của đơn holding này
        $stmtGet = $this->conn->prepare("SELECT room_id, start_time, end_time FROM bookings WHERE id = ? AND user_id = ? AND status = 'holding' AND expires_at > NOW()");
        $stmtGet->bind_param("ii", $holding_id, $data['user_id']);
        $stmtGet->execute();
        $res = $stmtGet->get_result();
        if ($res->num_rows === 0) {
            $stmtGet->close();
            return "Phiên giữ chỗ của bạn đã hết hạn hoặc không tồn tại.";
        }
        $holding = $res->fetch_assoc();
        $stmtGet->close();

        $room_id = $holding['room_id'];
        $start_time = $holding['start_time'];
        $end_time = $holding['end_time'];

        // 2. KIỂM TRA CHẶNG CUỐI: Xem có đơn nào KHÁC đã ở trạng thái 'approved' hoặc 'pending' trùng giờ này chưa
        $sqlCheck = "SELECT id FROM bookings 
                     WHERE room_id = ? 
                     AND id != ? 
                     AND status IN ('approved', 'pending') 
                     AND start_time < ? 
                     AND end_time > ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param("iiss", $room_id, $holding_id, $end_time, $start_time);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            // Xóa luôn đơn holding rác này vì giờ đã bị người khác chốt trước
            $this->conn->query("DELETE FROM bookings WHERE id = $holding_id");
            return "Rất tiếc! Khung giờ này vừa có người khác hoàn tất thủ tục đặt phòng trước bạn.";
        }
        $stmtCheck->close();

        // 3. Nếu an toàn, tiến hành update lên pending
        $sql = "UPDATE bookings 
                SET title = ?, participant_count = ?, extra_services = ?, status = 'pending', expires_at = NULL 
                WHERE id = ? AND user_id = ? AND status = 'holding' AND expires_at > NOW()";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return "Lỗi Prepare SQL: " . $this->conn->error;
        }

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
    /**
     * Tạo đơn giữ chỗ 5 phút.
     * Trả về: holding_id (int) | 'conflict_holding' | 'conflict_booked' | false
     * - Khóa dòng của PHÒNG (rooms FOR UPDATE): 2 người bấm cùng lúc được xếp hàng,
     *   người đến trước thắng, người đến sau chắc chắn thấy bản ghi của người trước.
     * - Dùng NOW() của MySQL cho expires_at (tránh lệch múi giờ PHP/MySQL).
     */
    public function createHoldingSlot($room_id, $user_id, $start_time, $end_time) {
        $this->conn->begin_transaction();

        try {
            // 0. Khóa phòng -> serialize mọi yêu cầu đặt cùng phòng
            $stmtRoom = $this->conn->prepare("SELECT id FROM rooms WHERE id = ? FOR UPDATE");
            $stmtRoom->bind_param("i", $room_id);
            $stmtRoom->execute();
            $roomExists = $stmtRoom->get_result()->num_rows > 0;
            $stmtRoom->close();
            if (!$roomExists) {
                $this->conn->rollback();
                return false;
            }

            // 1. Dọn holding đã hết hạn
            $this->conn->query("DELETE FROM bookings WHERE status = 'holding' AND expires_at < NOW()");

            // 2. Xóa holding cũ của chính user này
            $stmt_del = $this->conn->prepare("DELETE FROM bookings WHERE user_id = ? AND status = 'holding'");
            $stmt_del->bind_param("i", $user_id);
            $stmt_del->execute();
            $stmt_del->close();

            // 3. Kiểm tra xung đột (ưu tiên báo "đã đặt" nếu có cả đơn chính thức lẫn holding)
            $stmtCheck = $this->conn->prepare("
                SELECT status FROM bookings
                WHERE room_id = ?
                AND status IN ('approved', 'pending', 'holding')
                AND start_time < ?
                AND end_time > ?
                ORDER BY (status = 'holding') ASC
                LIMIT 1
            ");
            $stmtCheck->bind_param("iss", $room_id, $end_time, $start_time);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();

            if ($result->num_rows > 0) {
                $conflict = $result->fetch_assoc();
                $stmtCheck->close();
                $this->conn->rollback();
                return $conflict['status'] === 'holding' ? 'conflict_holding' : 'conflict_booked';
            }
            $stmtCheck->close();

            // 4. Phòng trống -> tạo holding 5 phút
            $stmtInsert = $this->conn->prepare("
                INSERT INTO bookings (room_id, user_id, start_time, end_time, status, expires_at, title, participant_count, extra_services)
                VALUES (?, ?, ?, ?, 'holding', DATE_ADD(NOW(), INTERVAL 5 MINUTE), 'Đang giữ chỗ tạm thời', 1, '')
            ");
            $stmtInsert->bind_param("iiss", $room_id, $user_id, $start_time, $end_time);
            $stmtInsert->execute();
            $holding_id = $stmtInsert->insert_id;
            $stmtInsert->close();

            $this->conn->commit();
            return $holding_id;

        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    /**
     * Trạng thái hiện tại của một khung giờ (dùng cho màn hình "chờ" + polling).
     * Trả về ['state' => 'free'|'holding'|'booked', 'seconds_left' => int]
     */
    public function getSlotState($room_id, $start_time, $end_time) {
        $this->conn->query("DELETE FROM bookings WHERE status = 'holding' AND expires_at < NOW()");

        $stmt = $this->conn->prepare("
            SELECT status, GREATEST(TIMESTAMPDIFF(SECOND, NOW(), expires_at), 0) AS seconds_left
            FROM bookings
            WHERE room_id = ?
            AND status IN ('approved', 'pending', 'holding')
            AND start_time < ?
            AND end_time > ?
            ORDER BY (status = 'holding') ASC, expires_at DESC
            LIMIT 1
        ");
        $stmt->bind_param("iss", $room_id, $end_time, $start_time);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) return ['state' => 'free', 'seconds_left' => 0];
        if ($row['status'] === 'holding') return ['state' => 'holding', 'seconds_left' => (int)$row['seconds_left']];
        return ['state' => 'booked', 'seconds_left' => 0];
    }

    // Lấy holding CÒN HẠN của đúng user (dùng cho trang chi tiết)
    public function getHoldingById($holding_id, $user_id) {
        $stmt = $this->conn->prepare("
            SELECT room_id, start_time, end_time,
                   GREATEST(TIMESTAMPDIFF(SECOND, NOW(), expires_at), 0) AS seconds_left
            FROM bookings
            WHERE id = ? AND user_id = ? AND status = 'holding' AND expires_at > NOW()
        ");
        $stmt->bind_param("ii", $holding_id, $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    // Số giây còn lại của một holding (tính theo giờ DB, không phụ thuộc session/trình duyệt)
    public function getHoldingSecondsLeft($holding_id, $user_id) {
        $stmt = $this->conn->prepare("
            SELECT GREATEST(TIMESTAMPDIFF(SECOND, NOW(), expires_at), 0) AS s
            FROM bookings WHERE id = ? AND user_id = ? AND status = 'holding'
        ");
        $stmt->bind_param("ii", $holding_id, $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? (int)$row['s'] : 0;
    }

    // Lấy danh sách các đơn đặt phòng đã được duyệt (giới hạn 20 đơn gần nhất)
    public function getRecentApprovedBookings($limit = 20) {
        $sql = "SELECT b.*, u.full_name, u.email, r.room_name, r.room_number, r.capacity 
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN rooms r ON b.room_id = r.id
                WHERE b.status = 'approved'
                ORDER BY b.start_time DESC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $data;
        }
        return [];
    }
}
?>