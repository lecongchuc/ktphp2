<?php
// File: app/models/HocPhanModel.php

class HocPhanModel
{
    private $conn;
    private $table_name = "HocPhan"; // Tên bảng trong csdlMoi

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Lấy tất cả các học phần (có thể thêm điều kiện lọc sau này)
     * Sắp xếp theo Tên HP để dễ nhìn
     */
    public function getHocPhans()
    {
        $query = "SELECT MaHP, TenHP, SoTinChi
                  FROM " . $this->table_name . "
                  ORDER BY TenHP ASC"; // Sắp xếp theo tên

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    /**
     * Lấy thông tin chi tiết một học phần theo MaHP (Nếu cần)
     */
    public function getHocPhanById($maHP)
    {
        $query = "SELECT MaHP, TenHP, SoTinChi
                  FROM " . $this->table_name . "
                  WHERE MaHP = :maHP";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maHP', $maHP);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result;
    }

    // Có thể thêm các phương thức add, update, delete nếu cần quản lý học phần
}
