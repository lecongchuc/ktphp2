<?php
// File: app/models/SinhVienModel.php

class SinhVienModel
{
    private $conn;
    private $table_name = "sinhvien"; // Tên bảng trong csdlMoi

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getSinhVienForLogin($maSV)
    {
        $query = "SELECT MaSV, HoTen, Password -- Lấy cả cột Password đã hash
                  FROM " . $this->table_name . "
                  WHERE MaSV = :maSV";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maSV', $maSV);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ); // Lấy một bản ghi

        return $result; // Trả về object hoặc false
    }
    /**
     * Lấy danh sách sinh viên kèm tên ngành học
     */
    public function getSinhViens()
    {
        // Sử dụng JOIN để lấy TenNganh từ bảng NganhHoc
        $query = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, nh.TenNganh
                  FROM " . $this->table_name . " sv
                  LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ); // Lấy về dạng object

        return $result;
    }

    /**
     * Lấy thông tin chi tiết một sinh viên theo MaSV
     */
    public function getSinhVienById($id)
    {
        // Có thể JOIN với NganhHoc nếu cần hiển thị cả tên ngành ở trang chi tiết
        $query = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, sv.MaNganh, nh.TenNganh
                  FROM " . $this->table_name . " sv
                  LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
                  WHERE sv.MaSV = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ); // Lấy một bản ghi

        return $result;
    }

    /**
     * Thêm sinh viên mới
     */
    public function addSinhVien($maSV, $hoTen, $gioiTinh, $ngaySinh, $hinh, $maNganh, $hashedPassword) // Thêm $hashedPassword
    {
        $errors = [];

        // --- Validation cơ bản (giữ nguyên hoặc bổ sung) ---
        if (empty($maSV)) {
            $errors['maSV'] = 'Mã sinh viên không được để trống';
        } else if (strlen($maSV) > 10) {
            $errors['maSV'] = 'Mã sinh viên không được vượt quá 10 ký tự';
        } else if ($this->getSinhVienById($maSV)) { // Kiểm tra trùng mã SV
            $errors['maSV'] = 'Mã sinh viên đã tồn tại';
        }
        if (empty($hoTen)) {
            $errors['hoTen'] = 'Họ tên sinh viên không được để trống';
        }
        if (empty($maNganh)) {
            $errors['maNganh'] = 'Vui lòng chọn ngành học';
        }
        if (empty($hashedPassword)) { // Mật khẩu đã hash không được rỗng
            $errors['password'] = 'Lỗi tạo mật khẩu.'; // Lỗi nội bộ nếu hash bị rỗng
        }
        // ... thêm validation khác nếu cần ...

        if (count($errors) > 0) {
            return $errors; // Trả về mảng lỗi nếu có
        }

        // --- Câu lệnh SQL Insert (Thêm cột Password) ---
        $query = "INSERT INTO " . $this->table_name . " (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh, Password)
                  VALUES (:maSV, :hoTen, :gioiTinh, :ngaySinh, :hinh, :maNganh, :password)"; // Thêm :password

        $stmt = $this->conn->prepare($query);

        // --- Làm sạch dữ liệu (giữ nguyên) ---
        $maSV_clean = htmlspecialchars(strip_tags($maSV));
        $hoTen_clean = htmlspecialchars(strip_tags($hoTen));
        $gioiTinh_clean = htmlspecialchars(strip_tags($gioiTinh));
        $ngaySinh_clean = htmlspecialchars(strip_tags($ngaySinh)); // Cần xử lý định dạng date
        $hinh_clean = htmlspecialchars(strip_tags($hinh)); // Đường dẫn file hoặc null
        $maNganh_clean = htmlspecialchars(strip_tags($maNganh));
        // Không cần làm sạch $hashedPassword vì nó là kết quả của password_hash()

        // --- Bind các tham số (Thêm bind cho password) ---
        $stmt->bindParam(':maSV', $maSV_clean);
        $stmt->bindParam(':hoTen', $hoTen_clean);
        $stmt->bindParam(':gioiTinh', $gioiTinh_clean);
        $stmt->bindParam(':ngaySinh', $ngaySinh_clean);
        $stmt->bindParam(':hinh', $hinh_clean);
        $stmt->bindParam(':maNganh', $maNganh_clean);
        $stmt->bindParam(':password', $hashedPassword); // Bind mật khẩu đã hash

        // --- Thực thi ---
        if ($stmt->execute()) {
            return true; // Thành công
        }

        // In lỗi nếu cần debug
        error_log("SQL Error in addSinhVien: " . implode(":", $stmt->errorInfo()));

        return false; // Thất bại
    }

    /**
     * Cập nhật thông tin sinh viên
     */
    public function updateSinhVien($maSV, $hoTen, $gioiTinh, $ngaySinh, $hinh, $maNganh)
    {
        $errors = [];
        // --- Validation cơ bản ---
        if (empty($maSV)) {
            $errors['maSV'] = 'Mã sinh viên không được để trống';
        }
        if (empty($hoTen)) {
            $errors['hoTen'] = 'Họ tên sinh viên không được để trống';
        }
        if (empty($maNganh)) {
            $errors['maNganh'] = 'Vui lòng chọn ngành học';
        }
        // Thêm các validation khác nếu cần

        if (count($errors) > 0) {
            return $errors; // Trả về mảng lỗi nếu có
        }

        // --- Câu lệnh SQL Update ---
        $query = "UPDATE " . $this->table_name . "
                  SET HoTen = :hoTen,
                      GioiTinh = :gioiTinh,
                      NgaySinh = :ngaySinh,
                      Hinh = :hinh,
                      MaNganh = :maNganh
                  WHERE MaSV = :maSV";

        $stmt = $this->conn->prepare($query);

        // --- Làm sạch dữ liệu ---
        $maSV = htmlspecialchars(strip_tags($maSV));
        $hoTen = htmlspecialchars(strip_tags($hoTen));
        $gioiTinh = htmlspecialchars(strip_tags($gioiTinh));
        $ngaySinh = htmlspecialchars(strip_tags($ngaySinh));
        $hinh = htmlspecialchars(strip_tags($hinh));
        $maNganh = htmlspecialchars(strip_tags($maNganh));

        // --- Bind tham số ---
        $stmt->bindParam(':maSV', $maSV);
        $stmt->bindParam(':hoTen', $hoTen);
        $stmt->bindParam(':gioiTinh', $gioiTinh);
        $stmt->bindParam(':ngaySinh', $ngaySinh);
        $stmt->bindParam(':hinh', $hinh);
        $stmt->bindParam(':maNganh', $maNganh);

        // --- Thực thi ---
        if ($stmt->execute()) {
            return true; // Thành công
        }
        return false; // Thất bại
    }

    /**
     * Xóa sinh viên
     */
    public function deleteSinhVien($id)
    {
        // Lưu ý: Cần xem xét việc xóa các bản ghi liên quan trong DangKy/ChiTietDangKy
        // hoặc thiết lập ON DELETE CASCADE / SET NULL trong CSDL.
        // Ví dụ đơn giản chỉ xóa sinh viên:
        $query = "DELETE FROM " . $this->table_name . " WHERE MaSV = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true; // Thành công
        }
        return false; // Thất bại
    }
}
