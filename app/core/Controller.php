<?php
class Controller {
    // Hàm gọi Model
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    // Hàm gọi View và truyền dữ liệu
    public function view($view, $data = []) {
        // Hàm extract giúp biến mảng ['error' => 'Lỗi'] thành biến $error
        extract($data);
        
        $viewFile = '../app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Lỗi: Không tìm thấy View " . $viewFile);
        }
    }
}
?>