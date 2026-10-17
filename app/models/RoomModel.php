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
}
?>