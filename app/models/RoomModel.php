<?php
class RoomModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllRooms() {
        $result = $this->conn->query("SELECT * FROM rooms ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getActiveRooms() {
        $result = $this->conn->query("SELECT * FROM rooms WHERE status = 'active' ORDER BY room_name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRoomById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $room = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $room;
    }

    // Hàm thêm phòng mới
    public function insertRoom($data) {
        $stmt = $this->conn->prepare("INSERT INTO rooms (room_name, room_number, capacity, area, location, equipment_details, image_url, status, version) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', 1)");
        $stmt->bind_param("ssiisss", $data['room_name'], $data['room_number'], $data['capacity'], $data['area'], $data['location'], $data['equipment_details'], $data['image_url']);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Hàm cập nhật phòng hiện tại
    public function updateRoom($id, $data) {
        $stmt = $this->conn->prepare("UPDATE rooms SET room_name=?, room_number=?, capacity=?, area=?, location=?, equipment_details=?, image_url=? WHERE id=?");
        $stmt->bind_param("ssiisssi", $data['room_name'], $data['room_number'], $data['capacity'], $data['area'], $data['location'], $data['equipment_details'], $data['image_url'], $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Hàm chuyển đổi trạng thái (active <-> maintenance)
    public function toggleStatus($id) {
        $stmt = $this->conn->prepare("SELECT status FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $current_status = $result->fetch_assoc()['status'];
            $new_status = ($current_status === 'active') ? 'maintenance' : 'active';
            
            $stmt_up = $this->conn->prepare("UPDATE rooms SET status = ? WHERE id = ?");
            $stmt_up->bind_param("si", $new_status, $id);
            $stmt_up->execute();
            $stmt_up->close();
            
            $stmt->close();
            return $new_status;
        }
        
        $stmt->close();
        return false;
    }

    // Lọc danh sách phòng theo tầng và trạng thái
    public function getFilteredRooms($floor = '', $status = '') {
        $sql = "SELECT * FROM rooms WHERE 1=1";
        $params = [];
        $types = "";

        // Lọc chính xác theo cột location
        if ($floor !== '') {
            $sql .= " AND location = ?";
            $params[] = $floor;
            $types .= "s";
        }

        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $sql .= " ORDER BY room_number ASC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $rooms = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
        }
        $stmt->close();
        
        return $rooms;
    }

    // Kiểm tra số phòng đã tồn tại chưa (Tuyệt đối không được trùng)
    public function isRoomNumberExists($room_number, $exclude_id = null) {
        $sql = "SELECT id FROM rooms WHERE room_number = ?";
        $params = [$room_number];
        $types = "s";

        if ($exclude_id !== null) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->store_result();
        
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }

    /**
     * Kiểm tra Mã phòng hoặc Tên phòng đã tồn tại chưa
     */
    public function isRoomNumberOrNameExists($room_number, $room_name, $exclude_id = null) {
        // Tìm xem có phòng nào trùng Tên HOẶC trùng Mã
        $sql = "SELECT id FROM rooms WHERE (room_number = ? OR room_name = ?)";
        $params = [$room_number, $room_name];
        $types = "ss";

        // Nếu đang Sửa phòng, bỏ qua ID của chính phòng đó
        if ($exclude_id !== null) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->store_result();
        
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }

    /**
     * Xóa phòng họp và các lịch đặt liên quan
     */
    public function deleteRoomById($id) {
        // Bước 1: Xóa các lịch đặt phòng liên quan trước để tránh lỗi khóa ngoại
        $sqlBookings = "DELETE FROM bookings WHERE room_id = ?";
        $stmtBookings = $this->conn->prepare($sqlBookings);
        if ($stmtBookings) {
            $stmtBookings->bind_param("i", $id);
            $stmtBookings->execute();
            $stmtBookings->close();
        }

        // Bước 2: Xóa phòng
        $sql = "DELETE FROM rooms WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Cập nhật trạng thái bảo trì/hoạt động của phòng
     */
    public function updateRoomStatus($id, $status) {
        $sql = "UPDATE rooms SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        
        $stmt->bind_param("si", $status, $id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

}
?>