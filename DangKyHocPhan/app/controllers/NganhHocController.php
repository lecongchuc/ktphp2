<?php
// File: app/controllers/NganhHocController.php

// Require các file cần thiết
require_once('app/config/database.php');
require_once('app/models/NganhHocModel.php');
// Có thể cần thêm UserModel nếu bạn muốn tích hợp kiểm tra quyền sau này
// require_once('app/models/UserModel.php');

class NganhHocController
{
    private $nganhHocModel;
    private $db;

    /**
     * Constructor: Khởi tạo kết nối DB và NganhHocModel
     */
    public function __construct()
    {
        // Tạo kết nối cơ sở dữ liệu
        $this->db = (new Database())->getConnection();
        // Khởi tạo NganhHocModel
        $this->nganhHocModel = new NganhHocModel($this->db);
    }

    /**
     * Hiển thị danh sách các ngành học.
     * Tương tự phương thức list() của PhongBanController.
     * Đổi tên thành index() cho nhất quán với các controller CRUD khác.
     */
    public function index()
    {
        // // Ví dụ kiểm tra quyền (nếu cần)
        // session_start(); // Hoặc cơ chế quản lý session khác
        // if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
        //     // Nếu không có quyền, chuyển hướng hoặc báo lỗi
        //     echo "Bạn không có quyền truy cập trang này.";
        //     // header('Location: /path/to/login');
        //     exit;
        // }

        // Gọi phương thức từ model để lấy danh sách ngành học
        $nganhHocs = $this->nganhHocModel->getNganhHocs();

        // Include view để hiển thị danh sách
        // Giả sử view nằm tại: app/views/nganhHoc/list.php
        // Lưu ý: Đường dẫn view cần chính xác với cấu trúc thư mục của bạn
        include 'app/views/nganhHoc/list.php';
    }

    /*
    // --- Các phương thức CRUD khác (Tùy chọn, nếu cần mở rộng) ---

    // Hiển thị form thêm ngành học mới (Tương tự NhanVienController::add)
    public function add() {
        // Kiểm tra quyền
        // ...
        include 'app/views/nganhHoc/add.php';
    }

    // Lưu ngành học mới (Tương tự NhanVienController::save)
    public function save() {
        // Kiểm tra quyền
        // ...
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maNganh = $_POST['MaNganh'] ?? '';
            $tenNganh = $_POST['TenNganh'] ?? '';

            // Thêm validation ở đây...
            $errors = [];
            if(empty($maNganh)) $errors['MaNganh'] = "Mã ngành không được trống";
            // Kiểm tra trùng mã, độ dài,...

            if (empty($errors)) {
                // Gọi model để thêm
                // $result = $this->nganhHocModel->addNganhHoc($maNganh, $tenNganh);
                // if ($result) {
                //     header('Location: /path/to/nganhhoc/index?success=add');
                //     exit;
                // } else {
                //     $errors[] = "Lỗi khi thêm vào CSDL";
                //     include 'app/views/nganhHoc/add.php';
                // }
            } else {
                 include 'app/views/nganhHoc/add.php';
            }
        }
    }

    // Hiển thị form sửa ngành học (Tương tự NhanVienController::edit)
    public function edit($id) {
         // Kiểm tra quyền
        // ...
        $nganhHoc = $this->nganhHocModel->getNganhHocById($id);
        if ($nganhHoc) {
             include 'app/views/nganhHoc/edit.php';
        } else {
            echo "Không tìm thấy ngành học";
            // header('Location: /path/to/nganhhoc/index?error=notfound');
        }
    }

    // Cập nhật ngành học (Tương tự NhanVienController::update)
     public function update() {
         // Kiểm tra quyền
        // ...
         if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $maNganh = $_POST['MaNganh'];
             $tenNganh = $_POST['TenNganh'] ?? '';
             // Validation...
             // Gọi model update...
             // Redirect hoặc hiển thị lỗi...
         }
    }

    // Xóa ngành học (Tương tự NhanVienController::delete)
     public function delete($id) {
         // Kiểm tra quyền
        // ...
        // Lưu ý kiểm tra ràng buộc khóa ngoại trước khi xóa (SinhVien đang tham chiếu)
        // Ví dụ: kiểm tra xem có sinh viên nào thuộc ngành này không
        // if (/* có sinh viên thuộc ngành ) {
        //      header('Location: /path/to/nganhhoc/index?error=deleterestricted');
        //      exit;
        // }

        // if ($this->nganhHocModel->deleteNganhHoc($id)) {
        //     header('Location: /path/to/nganhhoc/index?success=delete');
        //     exit;
        // } else {
        //     header('Location: /path/to/nganhhoc/index?error=deletefailed');
        //     exit;
        // }
     }
    */
} // Kết thúc class NganhHocController
