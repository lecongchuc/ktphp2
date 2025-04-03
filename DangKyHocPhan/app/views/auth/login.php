<?php
// File: app/views/auth/login.php

// Bao gồm header (có thể tạo header/footer riêng cho trang login nếu cần)
// include 'app/views/shares/header_login.php'; // Giả sử có header riêng

// Kiểm tra xem có thông báo lỗi từ Controller không (qua session flash hoặc biến $error)
// session_start(); // Cần session để lấy lỗi nếu dùng flash message
$error_message = null;
if (isset($_SESSION['login_error'])) {
    $error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Xóa lỗi khỏi session sau khi hiển thị
} elseif (isset($error)) { // Nếu controller truyền trực tiếp biến $error
    $error_message = $error;
}

$loggedout_message = null;
if (isset($_GET['loggedout']) && $_GET['loggedout'] == '1') {
    $loggedout_message = "Bạn đã đăng xuất thành công.";
}

// Favicon và tiêu đề trang
echo '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | Hệ thống Đăng ký Học phần</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 0 15px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            padding: 1.5rem;
            border-bottom: none;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-primary {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            border: none;
            padding: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 84, 200, 0.4);
        }
        .form-control {
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #e1e5ea;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #8f94fb;
            box-shadow: 0 0 0 0.25rem rgba(78, 84, 200, 0.25);
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        .input-group-text {
            background-color: transparent;
            border-right: none;
        }
        .password-toggle {
            cursor: pointer;
            border-left: none;
        }
        .input-group .form-control {
            border-right: none;
        }
        .school-logo {
            height: 70px;
            margin-bottom: 10px;
        }
        .login-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>';
?>

<div class="login-container">
    <div class="text-center mb-4">
        <!-- Tùy chỉnh link logo trường -->
        <img src="https://via.placeholder.com/150x70" alt="School Logo" class="school-logo">
    </div>

    <div class="card">
        <div class="card-header text-center text-white">
            <h3 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Đăng nhập Hệ thống</h3>
            <p class="mb-0 mt-2">Hệ thống Đăng ký Học phần</p>
        </div>
        <div class="card-body">
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($loggedout_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($loggedout_message, ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="/manguonmo/DangKyHocPhan/Auth/processLogin">
                <div class="mb-4">
                    <label for="maSV" class="form-label">Mã Sinh viên</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="maSV" name="maSV" placeholder="Nhập mã sinh viên" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        <span class="input-group-text password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="#" class="text-decoration-none">Quên mật khẩu?</a>
            </div>
        </div>
    </div>

    <div class="login-footer">
        <p>&copy; <?php echo date('Y'); ?> Hệ thống Đăng ký Học phần. Bản quyền thuộc về Nhà trường.</p>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>

<?php
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
?>