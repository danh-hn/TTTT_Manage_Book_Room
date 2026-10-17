<?php
// Bật session cho toàn bộ hệ thống
session_start();

// Nạp file kết nối CSDL và Base Controller
require_once '../config/db_connect.php';
require_once '../app/core/Controller.php';

// 1. Phân tích URL: Lấy controller và action (Mặc định là user/index)
$controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'UserController';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

// 2. Tìm kiếm file Controller
$controllerFile = '../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Khởi tạo đối tượng Controller (Ví dụ: new AuthController())
    $controller = new $controllerName();
    
    // 3. Kiểm tra và chạy hàm (Action) tương ứng
    if (method_exists($controller, $actionName)) {
        $controller->$actionName();
    } else {
        echo "Lỗi 404: Không tìm thấy phương thức '$actionName' trong '$controllerName'.";
    }
} else {
    echo "Lỗi 404: Không tìm thấy file Controller '$controllerFile'.";
}
?>