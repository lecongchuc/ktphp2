<?php
// File: app/controllers/HocPhanController.php

// Require các file cần thiết
require_once('app/config/database.php');
require_once('app/models/HocPhanModel.php');
require_once('app/models/SinhVienModel.php'); // Cần để lấy thông tin SV

class HocPhanController
{
    private $hocPhanModel;
    private $sinhVienModel; // Thêm để lấy thông tin sinh viên
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->hocPhanModel = new HocPhanModel($this->db);
        $this->sinhVienModel = new SinhVienModel($this->db); // Khởi tạo SinhVienModel
    }

    /**
     * (Optional) Hiển thị danh sách tất cả học phần (trang catalogue)
     */



    /**
     * Hiển thị trang cho phép sinh viên đăng ký học phần
     */
    public function showRegistrationForm()
    {
        // --- Xác định sinh viên đang đăng nhập ---
        // Giả sử MaSV được lưu trong Cookie hoặc Session sau khi đăng nhập
        // **QUAN TRỌNG:** Cần có cơ chế xác thực người dùng an toàn.
        $maSinhVienLoggedIn = null;
        if (isset($_COOKIE['maSV'])) { // Ví dụ dùng cookie 'maSV'
            $maSinhVienLoggedIn = $_COOKIE['maSV'];
        } else {
            // Nếu không xác định được sinh viên, chuyển hướng về trang đăng nhập
            // Hoặc hiển thị lỗi
            echo "Vui lòng đăng nhập với tài khoản sinh viên để đăng ký học phần.";
            // header('Location: /path/to/login');
            exit;
        }

        // Lấy thông tin chi tiết của sinh viên đang đăng nhập
        $sinhVien = $this->sinhVienModel->getSinhVienById($maSinhVienLoggedIn);

        if (!$sinhVien) {
            echo "Lỗi: Không tìm thấy thông tin sinh viên ứng với mã " . htmlspecialchars($maSinhVienLoggedIn);
            exit;
        }

        // --- Lấy danh sách học phần ---
        // Có thể lọc bỏ những học phần SV đã đăng ký hoặc đã qua môn ở đây nếu cần
        $hocPhans = $this->hocPhanModel->getHocPhans();

        // --- Hiển thị View Đăng ký ---
        // Truyền thông tin sinh viên và danh sách học phần vào view
        // Giả sử view nằm trong app/views/dangKy/form.php
        include 'app/views/hocPhan/form.php';
    }
    public function index()
    {
        // --- Xác định sinh viên đang đăng nhập ---
        $loggedInMaSV = null;
        // Ưu tiên Session nếu dùng
        // if (session_status() == PHP_SESSION_NONE) { session_start(); }
        // if (isset($_SESSION['maSV'])) {
        //     $loggedInMaSV = $_SESSION['maSV'];
        // } else
        if (isset($_COOKIE['maSV'])) { // Hoặc dùng Cookie
            $loggedInMaSV = $_COOKIE['maSV'];
            // Nên kiểm tra lại xem MaSV trong cookie có hợp lệ không bằng cách query DB nhẹ
        }

        // Lấy danh sách học phần
        $hocPhans = $this->hocPhanModel->getHocPhans();

        // Truyền $hocPhans và $loggedInMaSV vào view
        include 'app/views/hocPhan/list.php';
    }

    // ... (showRegistrationForm và các phương thức khác) ...

    // --- PHƯƠNG THỨC MỚI ĐỂ XỬ LÝ ĐĂNG KÝ TỪNG HỌC PHẦN ---
    /**
     * Xử lý yêu cầu đăng ký một học phần cụ thể.
     * @param string $maHP Mã học phần cần đăng ký (từ URL).
     */
    public function registerSingleCourse($maHP)
    {
        // --- Xác thực người dùng và lấy MaSV ---
        $maSV = null;
        // Ưu tiên Session
        // if (session_status() == PHP_SESSION_NONE) { session_start(); }
        // if (isset($_SESSION['maSV'])) {
        //     $maSV = $_SESSION['maSV'];
        // } else
        if (isset($_COOKIE['maSV'])) { // Hoặc dùng Cookie
            $maSV = $_COOKIE['maSV'];
        }

        if ($maSV === null) {
            // Chưa đăng nhập, chuyển hướng về trang login hoặc báo lỗi
            header('Location: /manguonmo/DangKyHocPhan/Auth/showLoginForm?error=notloggedin');
            exit;
        }

        // --- Validate $maHP (có tồn tại không?) ---
        $hocPhan = $this->hocPhanModel->getHocPhanById($maHP);
        if (!$hocPhan) {
            header('Location: /manguonmo/DangKyHocPhan/HocPhan/index?error=invalidhp');
            exit;
        }

        // --- Logic Đăng ký ---
        // **QUAN TRỌNG:** Phần này cần các Model DangKyModel và ChiTietDangKyModel
        // và nên được đặt trong một TRANSACTION để đảm bảo toàn vẹn dữ liệu.

        // Bỏ comment và triển khai khi có Model tương ứng
        /*
         require_once('app/models/DangKyModel.php');
         require_once('app/models/ChiTietDangKyModel.php');

         $dangKyModel = new DangKyModel($this->db);
         $chiTietModel = new ChiTietDangKyModel($this->db);

         $this->db->beginTransaction();
         try {
             // 1. Tìm hoặc Tạo bản ghi DangKy cho SV và ngày hôm nay
             // (Cần logic phức tạp hơn nếu đăng ký theo kỳ/phiên thay vì ngày)
             $ngayDK = date('Y-m-d');
             $maDK = $dangKyModel->findOrCreateDangKy($maSV, $ngayDK); // Cần tạo phương thức này

             if (!$maDK) {
                 throw new Exception("LoiTaoLuotDangKy");
             }

             // 2. Kiểm tra xem HP này đã được đăng ký trong lượt này chưa
             // if ($chiTietModel->isAlreadyRegistered($maDK, $maHP)) {
             //      throw new Exception("HocPhanDaDangKy");
             // }

             // 3. Thêm vào ChiTietDangKy
             if (!$chiTietModel->addChiTiet($maDK, $maHP)) {
                 throw new Exception("LoiDangKyChiTiet");
             }

             // 4. Commit transaction
             $this->db->commit();
             header('Location: /manguonmo/DangKyHocPhan/HocPhan/index?success=single_registered&hp=' . urlencode($maHP));
             exit;

         } catch (Exception $e) {
             // 5. Rollback transaction
             $this->db->rollBack();
             error_log("Lỗi đăng ký học phần đơn lẻ: " . $e->getMessage());
             // Chuyển hướng với mã lỗi cụ thể
             header('Location: /manguonmo/DangKyHocPhan/HocPhan/index?error=' . $e->getMessage() . '&hp=' . urlencode($maHP));
             exit;
         }
         */

        // --- Code tạm thời (XÓA SAU KHI CÓ MODEL) ---
        echo "Chức năng đăng ký học phần '{$maHP}' cho sinh viên '{$maSV}' đang được xây dựng.";
        echo '<br><a href="/manguonmo/DangKyHocPhan/HocPhan/index">Quay lại danh sách</a>';
        // --- Hết code tạm thời ---
    }

    // PHƯƠNG THỨC XỬ LÝ SUBMIT ĐĂNG KÝ SẼ NẰM Ở ĐÂY HOẶC TRONG DangKyController
    /*
    public function processRegistration() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maSV = $_POST['maSV'] ?? null;
            $selectedMaHPs = $_POST['maHPs'] ?? []; // Mảng các mã HP được chọn

            if (empty($maSV) || empty($selectedMaHPs)) {
                 // Báo lỗi thiếu thông tin
                 header('Location: /manguonmo/DangKyHocPhan/HocPhan/showRegistrationForm?error=missingdata');
                 exit;
            }

            // --- BẮT ĐẦU TRANSACTION ---
            $this->db->beginTransaction();
            try {
                 // 1. Tạo record trong bảng DangKy
                 // Cần có DangKyModel với phương thức addDangKy($maSV, $ngayDK) trả về MaDK mới
                 // $dangKyModel = new DangKyModel($this->db);
                 // $ngayDK = date('Y-m-d'); // Lấy ngày hiện tại
                 // $maDK = $dangKyModel->addDangKy($maSV, $ngayDK);

                 // if (!$maDK) throw new Exception("Không thể tạo lượt đăng ký.");

                 // 2. Tạo các record trong bảng ChiTietDangKy
                 // Cần có ChiTietDangKyModel với phương thức addChiTiet($maDK, $maHP)
                 // $chiTietModel = new ChiTietDangKyModel($this->db);
                 // foreach ($selectedMaHPs as $maHP) {
                 //    if (!$chiTietModel->addChiTiet($maDK, $maHP)) {
                 //        throw new Exception("Không thể đăng ký học phần " . $maHP);
                 //    }
                 // }

                 // --- COMMIT TRANSACTION ---
                 $this->db->commit();
                 header('Location: /manguonmo/DangKyHocPhan/HocPhan/showRegistrationForm?success=registered');
                 exit;

            } catch (Exception $e) {
                 // --- ROLLBACK TRANSACTION ---
                 $this->db->rollBack();
                 error_log("Lỗi đăng ký học phần: " . $e->getMessage());
                 header('Location: /manguonmo/DangKyHocPhan/HocPhan/showRegistrationForm?error=' . urlencode($e->getMessage()));
                 exit;
            }
        } else {
             // Chuyển hướng nếu không phải POST
             header('Location: /manguonmo/DangKyHocPhan/HocPhan/showRegistrationForm');
             exit;
        }
    }
    */
} // Kết thúc class HocPhanController
