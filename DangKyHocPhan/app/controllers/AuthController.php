<?php
// File: app/controllers/AuthController.php

require_once('app/config/database.php');
require_once('app/models/SinhVienModel.php'); // Cần SinhVienModel

class AuthController
{
    private $db;
    private $sinhVienModel;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->sinhVienModel = new SinhVienModel($this->db);
    }

    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        // Đảm bảo session được khởi động để xử lý thông báo lỗi (nếu dùng session flash)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Chỉ cần include view
        include 'app/views/auth/login.php';
    }

    /**
     * Xử lý dữ liệu đăng nhập gửi từ form
     */
    public function processLogin()
    {
        // Bắt đầu session nếu chưa có
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Chỉ xử lý nếu là POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Nếu không phải POST, chuyển hướng về form login
            header('Location: /manguonmo/DangKyHocPhan/Auth/showLoginForm');
            exit;
        }

        $maSV = $_POST['maSV'] ?? null;
        $password = $_POST['password'] ?? null;
        $error = null; // Biến lưu lỗi

        // 1. Validation cơ bản
        if (empty($maSV) || empty($password)) {
            $error = "Vui lòng nhập Mã sinh viên và Mật khẩu.";
        } else {
            // 2. Lấy thông tin user từ Model
            $sinhVien = $this->sinhVienModel->getSinhVienForLogin($maSV);

            // 3. Kiểm tra user tồn tại và xác thực mật khẩu
            if ($sinhVien && isset($sinhVien->Password) && $password === $sinhVien->Password) {
                // Mật khẩu chính xác! Đăng nhập thành công.

                // 4. Lưu thông tin vào Session
                $_SESSION['user_logged_in'] = true; // Cờ đánh dấu đã đăng nhập
                $_SESSION['maSV'] = $sinhVien->MaSV;
                $_SESSION['hoTen'] = $sinhVien->HoTen;
                // Có thể lưu thêm vai trò nếu cần phân quyền (ví dụ: $_SESSION['role'] = 'student';)

                // 5. (Tùy chọn) Lưu cookie nếu cần (ví dụ: để view check nhanh)
                setcookie('maSV', $sinhVien->MaSV, time() + (86400 * 30), "/"); // Ví dụ: lưu 30 ngày

                // 6. Chuyển hướng đến trang chính sau khi đăng nhập thành công
                // Thay đổi '/path/to/dashboard' thành URL mong muốn
                header('Location: /manguonmo/DangKyHocPhan/SinhVien'); // Ví dụ: về trang chủ của ứng dụng DKHP
                exit;
            } else {
                // User không tồn tại hoặc sai mật khẩu
                $error = "Mã sinh viên hoặc Mật khẩu không chính xác.";
            }
        }

        // Nếu có lỗi, lưu lỗi vào session và chuyển hướng lại form login
        if ($error) {
            $_SESSION['login_error'] = $error;
            header('Location: /manguonmo/DangKyHocPhan/Auth/showLoginForm');
            exit;
            // Hoặc truyền $error vào view nếu không dùng session flash
            // include 'app/views/auth/login.php';
        }
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Xóa tất cả biến session
        $_SESSION = array();

        // Hủy session
        session_destroy();

        // Xóa cookie liên quan (ví dụ: cookie maSV)
        if (isset($_COOKIE['maSV'])) {
            setcookie('maSV', '', time() - 3600, "/"); // Đặt thời gian hết hạn trong quá khứ
        }

        // Chuyển hướng về trang đăng nhập với thông báo đã đăng xuất
        header('Location: /manguonmo/DangKyHocPhan/Auth/showLoginForm?loggedout=1');
        exit;
    }
}
