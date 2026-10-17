<?php
class AuthController extends Controller {

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu đã đăng nhập thì chuyển hướng theo quyền
        if (isset($_SESSION['user_id'])) {
            $redirect = ($_SESSION['role'] === 'admin') 
                ? 'index.php?controller=admin&action=manageRooms' 
                : 'index.php?controller=user&action=index';
            header("Location: " . $redirect);
            exit();
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Vui lòng nhập email và mật khẩu.";
            } else {
                $userModel = $this->model('UserModel');
                $user = $userModel->getUserByEmail($email);

                if ($user) {
                    $is_password_correct = false;
                    if (password_verify($password, $user['password']) || $password === $user['password']) {
                        $is_password_correct = true;
                    }

                    if ($is_password_correct) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['role'] = $user['role'];

                        if ($user['role'] === 'admin') {
                            header("Location: index.php?controller=admin&action=manageRooms");
                        } else {
                            header("Location: index.php?controller=user&action=index");
                        }
                        exit();
                    } else {
                        $error = "Mật khẩu không chính xác.";
                    }
                } else {
                    $error = "Tài khoản không tồn tại.";
                }
            }
        }

        // Nạp View đăng nhập
        $this->view('auth/login', ['error' => $error]);
    }

    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
                $error = "Vui lòng nhập đầy đủ tất cả thông tin.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Định dạng email không hợp lệ (Ví dụ đúng: ten@congty.com).";
            } elseif ($password !== $confirm_password) {
                $error = "Mật khẩu nhập lại không khớp. Vui lòng kiểm tra lại.";
            } elseif (strlen($password) < 6) {
                $error = "Mật khẩu quá ngắn. Vui lòng nhập ít nhất 6 ký tự.";
            } else {
                $userModel = $this->model('UserModel');
                if ($userModel->checkEmailExists($email)) {
                    $error = "Email này đã được sử dụng trong hệ thống.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    if ($userModel->createUser($full_name, $email, $hashed_password, 'user')) {
                        $success = "Tạo tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.";
                    } else {
                        $error = "Lỗi hệ thống: Không thể tạo tài khoản.";
                    }
                }
            }
        }

        // Nạp View đăng ký
        $this->view('auth/register', ['error' => $error, 'success' => $success]);
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: index.php?controller=auth&action=login&msg=logged_out");
        exit();
    }
}
?>