<?php

class NhanVienModel
{
    private $conn;
    private $table_name = "nhanvien";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getNhanViens()
    {
        $query = "SELECT p.Ma_NV, p.Ten_NV, p.Phai, p.Noi_Sinh, p.Luong, c.Ten_Phong as Ten_Phong
                  FROM " . $this->table_name . " p
                  LEFT JOIN phongban c ON p.Ma_Phong = c.Ma_Phong";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    public function getNhanVienById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE Ma_NV = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result;
    }

    public function addNhanVien($id, $ten, $phai, $noiSinh, $luong, $maPhongBan)
    {
        $errors = [];

        if (empty($id)) {
            $errors['id'] = 'Mã nhân viên không được để trống';
        }
        if (empty($ten)) {
            $errors['ten'] = 'Tên nhân viên không được để trống';
        }

        // if (empty($phai)) {
        //     $errors['phai'] = 'Phái không được để trống';
        // }
        // if (empty($noiSinh)) {
        //     $errors['noiSinh'] = 'Nơi sinh không được để trống';
        // }
        // if (empty($maPhongBan)) {
        //     $errors['maPhongBan'] = 'Mã phòng ban không được để trống';
        // }
        // if (!is_numeric($luong) || $luong < 0) {
        //     $errors['luong'] = 'Lương không hợp lệ';
        // }

        if (count($errors) > 0) {
            return $errors;
        }

        $query = "INSERT INTO " . $this->table_name . " (Ma_NV, Ten_NV, Phai, Noi_Sinh, Luong, Ma_Phong) VALUES (:id,:ten, :phai, :noiSinh, :luong, :maPhongBan)";
        $stmt = $this->conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));
        $ten = htmlspecialchars(strip_tags($ten));
        $phai = htmlspecialchars(strip_tags($phai));
        $noiSinh = htmlspecialchars(strip_tags($noiSinh));
        $luong = htmlspecialchars(strip_tags($luong));
        $maPhongBan = htmlspecialchars(strip_tags($maPhongBan));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ten', $ten);
        $stmt->bindParam(':phai', $phai);
        $stmt->bindParam(':noiSinh', $noiSinh);
        $stmt->bindParam(':luong', $luong);
        $stmt->bindParam(':maPhongBan', $maPhongBan);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function updateNhanVien($id, $ten, $phai, $noiSinh, $luong, $maPhongBan)
    {
        $query = "UPDATE " . $this->table_name . " SET Ten_NV=:ten, Phai=:phai, Noi_Sinh=:noiSinh, Luong=:luong, Ma_Phong=:maPhongBan WHERE Ma_NV=:id";
        $stmt = $this->conn->prepare($query);

        $ten = htmlspecialchars(strip_tags($ten));
        $phai = htmlspecialchars(strip_tags($phai));
        $noiSinh = htmlspecialchars(strip_tags($noiSinh));
        $luong = htmlspecialchars(strip_tags($luong));
        $maPhongBan = htmlspecialchars(strip_tags($maPhongBan));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ten', $ten);
        $stmt->bindParam(':phai', $phai);
        $stmt->bindParam(':noiSinh', $noiSinh);
        $stmt->bindParam(':luong', $luong);
        $stmt->bindParam(':maPhongBan', $maPhongBan);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteNhanVien($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE MaNV=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
