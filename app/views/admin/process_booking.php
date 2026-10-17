<?php
session_start();

// Bảo vệ luồng
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/db_connect.php';

// Kiểm tra xem URL có truyền đủ tham số action và id không
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $booking_id = (int)$_GET['id'];

    // Xác định trạng thái mới dựa trên hành động của Admin
    $new_status = '';
    if ($action === 'approve') {
        $new_status = 'approved';
    } elseif ($action === 'reject') {
        $new_status = 'cancelled';
    }

    if ($new_status !== '') {
        // Thực thi cập nhật trạng thái yêu cầu
        $sql_update = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $new_status, $booking_id);
        
        if ($stmt->execute()) {
            // Quay lại trang duyệt kèm thông báo thành công
            header("Location: approve_bookings.php?msg=" . $new_status);
            exit();
        } else {
            // Quay lại trang duyệt kèm báo lỗi
            header("Location: approve_bookings.php?msg=error");
            exit();
        }
        $stmt->close();
    }
}

// Bắt lỗi fallback nếu ai đó truy cập file mà không có tham số
header("Location: approve_bookings.php");
exit();
?>