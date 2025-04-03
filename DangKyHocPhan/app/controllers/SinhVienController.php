<?php
// File: app/controllers/SinhVienController.php

// Require các file cần thiết
require_once('app/config/database.php');
require_once('app/models/SinhVienModel.php');
require_once('app/models/NganhHocModel.php');
// require_once('app/models/UserModel.php'); // Bỏ comment nếu cần kiểm tra quyền

class SinhVienController
{
    private $sinhVienModel;
    private $nganhHocModel; // Thêm để lấy danh sách ngành học
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->sinhVienModel = new SinhVienModel($this->db);
        $this->nganhHocModel = new NganhHocModel($this->db); // Khởi tạo NganhHocModel
    }

    /**
     * Hiển thị danh sách sinh viên
     */
    public function index()
    {
        $sinhViens = $this->sinhVienModel->getSinhViens();
        // Giả sử view nằm trong app/views/sinhVien/list.php
        include 'app/views/sinhVien/list.php';
    }

    /**
     * Hiển thị chi tiết một sinh viên
     */
    public function show($id)
    {
        $sinhVien = $this->sinhVienModel->getSinhVienById($id);
        if ($sinhVien) {
            // Giả sử view nằm trong app/views/sinhVien/show.php
            include 'app/views/sinhVien/show.php';
        } else {
            echo "Không tìm thấy sinh viên.";
            // Hoặc chuyển hướng về trang danh sách với thông báo lỗi
            // header('Location: /path/to/sinhvien?error=notfound');
        }
    }

    /**
     * Hiển thị form thêm sinh viên mới
     */
    public function add()
    {
        // Lấy danh sách ngành học để hiển thị trong dropdown
        $nganhHocs = $this->nganhHocModel->getNganhHocs();
        // Giả sử view nằm trong app/views/sinhVien/add.php
        include 'app/views/sinhVien/add.php';
    }

    /**
     * Lưu sinh viên mới vào CSDL
     */
    public function save()
    {
        // Chỉ xử lý nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = []; // Mảng chứa lỗi validation và upload
            $hinh_filename_to_save = null; // Tên file hình ảnh sẽ lưu vào DB

            // Lấy dữ liệu từ form POST
            $maSV = $_POST['maSV'] ?? '';
            $hoTen = $_POST['hoTen'] ?? '';
            $gioiTinh = $_POST['gioiTinh'] ?? null;
            $ngaySinh = $_POST['ngaySinh'] ?? null;
            $maNganh = $_POST['maNganh'] ?? null;
            $password = $maSV; // Mặc định là mã sinh viên, có thể thay đổi nếu cần

            // --- Xử lý Upload Hình Ảnh ---
            // Kiểm tra xem có file được tải lên không và có lỗi không
            if (isset($_FILES['hinhFile']) && $_FILES['hinhFile']['error'] === UPLOAD_ERR_OK) {

                $fileTmpPath = $_FILES['hinhFile']['tmp_name'];
                $fileName = $_FILES['hinhFile']['name'];
                $fileSize = $_FILES['hinhFile']['size'];
                $fileType = $_FILES['hinhFile']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // 1. Xác định thư mục lưu trữ trên hệ thống file
                // Sử dụng DOCUMENT_ROOT để có đường dẫn tuyệt đối an toàn hơn
                $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/manguonmo/DangKyHocPhan/Content/images/';

                // 2. Tạo thư mục nếu chưa tồn tại
                if (!is_dir($uploadFileDir)) {
                    // Cần quyền để tạo thư mục
                    if (!mkdir($uploadFileDir, 0775, true)) { // 0775 là quyền thông thường, true cho phép tạo đệ quy
                        $errors['hinhFile'] = "Không thể tạo thư mục lưu trữ hình ảnh.";
                        exit; // Dừng lại nếu không thể tạo thư mục
                    }
                }

                // 3. Validation file (có thể thêm nhiều kiểm tra hơn)
                $allowedfileExtensions = ['jpg', 'jpeg', 'gif', 'png'];
                $maxFileSize = 10 * 1024 * 1024; // Ví dụ: 5 MB

                if (!in_array($fileExtension, $allowedfileExtensions)) {
                    $errors['hinhFile'] = 'Loại file không hợp lệ. Chỉ chấp nhận: ' . implode(',', $allowedfileExtensions);
                }

                if ($fileSize > $maxFileSize) {
                    $errors['hinhFile'] = 'Kích thước file quá lớn. Tối đa là ' . ($maxFileSize / 1024 / 1024) . ' MB.';
                }

                // 4. Tạo tên file mới, duy nhất để tránh ghi đè và vấn đề bảo mật
                // Lấy tên gốc không bao gồm đuôi file
                $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                // Làm sạch tên file (loại bỏ ký tự không mong muốn)
                $safeBaseName = preg_replace("/[^a-zA-Z0-9_-]/", "_", $baseName);
                // Tạo tên file duy nhất
                $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;

                // 5. Đường dẫn đầy đủ đến file đích
                $dest_path = $uploadFileDir . $newFileName;

                // 6. Di chuyển file từ thư mục tạm vào thư mục đích
                // Chỉ di chuyển nếu không có lỗi validation file trước đó
                if (empty($errors['hinhFile'])) {
                    if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                        $errors['hinhFile'] = 'Có lỗi xảy ra khi di chuyển file tải lên.';
                    } else {
                        // Nếu upload và di chuyển thành công, gán tên file mới để lưu vào DB
                        $hinh_filename_to_save = $newFileName;
                    }
                }
            } elseif (isset($_FILES['hinhFile']) && $_FILES['hinhFile']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Có lỗi khác xảy ra trong quá trình upload (ngoại trừ việc không chọn file)
                $errors['hinhFile'] = 'Có lỗi khi tải file lên. Mã lỗi: ' . $_FILES['hinhFile']['error'];
            }
            // Nếu không có file tải lên (UPLOAD_ERR_NO_FILE), $hinh_filename_to_save sẽ là null (chấp nhận được)

            // --- Kết thúc Xử lý Upload Hình Ảnh ---


            // --- Kiểm tra quyền (nếu cần) ---
            // ... (logic kiểm tra quyền như trước) ...


            // --- Gọi Model để thêm sinh viên (chỉ khi không có lỗi upload nghiêm trọng) ---
            // Kiểm tra lại $errors trước khi gọi model
            if (empty($errors)) { // Chỉ thêm vào DB nếu không có lỗi gì

                // Gọi model để thêm sinh viên, truyền tên file hình ảnh đã xử lý
                $result = $this->sinhVienModel->addSinhVien($maSV, $hoTen, $gioiTinh, $ngaySinh, "/Content/images/" . $hinh_filename_to_save, $maNganh, $password);

                // Kiểm tra kết quả trả về từ model
                if (is_array($result)) {
                    // Nếu là mảng -> có lỗi validation từ Model
                    $errors = array_merge($errors, $result); // Gộp lỗi upload (nếu có) và lỗi validation
                    // Load lại view add với lỗi
                    $nganhHocs = $this->nganhHocModel->getNganhHocs();
                    include 'app/views/sinhVien/add.php';
                } elseif ($result === true) {
                    // Nếu là true -> thành công, chuyển hướng về trang danh sách
                    header('Location: /manguonmo/DangKyHocPhan/SinhVien/index?success=add');
                    exit; // Kết thúc script sau khi chuyển hướng
                } else {
                    // Nếu là false -> có lỗi CSDL từ Model
                    $errors[] = "Đã xảy ra lỗi khi thêm sinh viên vào cơ sở dữ liệu.";
                    // Load lại view add với lỗi
                    $nganhHocs = $this->nganhHocModel->getNganhHocs();
                    include 'app/views/sinhVien/add.php';
                }
            } else {
                // Nếu có lỗi (từ upload file hoặc validation khác), load lại view add
                $nganhHocs = $this->nganhHocModel->getNganhHocs();
                include 'app/views/sinhVien/add.php';
            }
        } else {
            // Nếu không phải POST, chuyển hướng về form add
            header('Location: /manguonmo/DangKyHocPhan/SinhVien/add');
            exit;
        }
    }

    /**
     * Hiển thị form chỉnh sửa thông tin sinh viên
     */
    public function edit($id)
    {
        // Lấy thông tin sinh viên cần sửa
        $sinhVien = $this->sinhVienModel->getSinhVienById($id);

        if ($sinhVien) {
            // Lấy danh sách ngành học cho dropdown
            $nganhHocs = $this->nganhHocModel->getNganhHocs();
            // Giả sử view nằm trong app/views/sinhVien/edit.php
            include 'app/views/sinhVien/edit.php';
        } else {
            echo "Không tìm thấy sinh viên để sửa.";
            // Hoặc chuyển hướng
            // header('Location: /manguonmo/DangKyHocPhan/SinhVien/index?error=notfound');
        }
    }

    /**
     * Cập nhật thông tin sinh viên vào CSDL
     */
    public function update()
    {
        // Chỉ xử lý nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form (bao gồm cả MaSV đang ẩn hoặc trong URL)
            $maSV = $_POST['maSV']; // Cần đảm bảo MaSV được gửi lên
            $hoTen = $_POST['hoTen'] ?? '';
            $gioiTinh = $_POST['gioiTinh'] ?? null;
            $ngaySinh = $_POST['ngaySinh'] ?? null;
            $hinh = $_POST['hinh'] ?? null; // Xử lý upload file nếu cho phép thay đổi hình
            $maNganh = $_POST['maNganh'] ?? null;

            // Xử lý upload hình ảnh mới nếu có
            // Tương tự như phần add()

            // Gọi model để cập nhật
            $result = $this->sinhVienModel->updateSinhVien($maSV, $hoTen, $gioiTinh, $ngaySinh, $hinh, $maNganh);

            if (is_array($result)) {
                // Nếu là mảng -> có lỗi validation
                $errors = $result;
                // Lấy lại thông tin sv và ds ngành học để hiển thị lại form edit
                $sinhVien = $this->sinhVienModel->getSinhVienById($maSV); // Lấy lại sv bằng $maSV từ POST
                $nganhHocs = $this->nganhHocModel->getNganhHocs();
                include 'app/views/sinhVien/edit.php'; // Hiển thị lại form với lỗi
            } elseif ($result === true) {
                // Nếu true -> thành công, chuyển hướng
                header('Location: /manguonmo/DangKyHocPhan/SinhVien/index?success=edit');
                exit;
            } else {
                // Nếu false -> lỗi CSDL
                $errors[] = "Đã xảy ra lỗi khi cập nhật sinh viên.";
                $sinhVien = (object)$_POST; // Giữ lại dữ liệu đã nhập để hiển thị lại form
                $nganhHocs = $this->nganhHocModel->getNganhHocs();
                include 'app/views/sinhVien/edit.php';
            }
        } else {
            // Chuyển hướng nếu không phải POST
            header('Location: /manguonmo/DangKyHocPhan/SinhVien/index');
            exit;
        }
    }

    /**
     * Xóa sinh viên
     */
    public function delete($id)
    {
        // Có thể thêm bước kiểm tra quyền xóa ở đây

        if ($this->sinhVienModel->deleteSinhVien($id)) {
            // Xóa thành công, chuyển hướng
            header('Location: /manguonmo/DangKyHocPhan/SinhVien/index?success=delete');
            exit;
        } else {
            // Có lỗi khi xóa
            // Có thể chuyển hướng về trang danh sách với thông báo lỗi
            header('Location: /manguonmo/DangKyHocPhan/SinhVien/index?error=deletefailed');
            exit;
            // Hoặc echo "Đã xảy ra lỗi khi xóa sinh viên.";
        }
    }
}
