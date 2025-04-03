<?php
// File: app/models/NganhHocModel.php

class NganhHocModel
{
    private $conn;
    private $table_name = "nganhhoc"; // Tên bảng trong csdlMoi

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Lấy tất cả các ngành học
     */
    public function getNganhHocs()
    {
        $query = "SELECT MaNganh, TenNganh FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ); // Lấy về dạng object

        return $result;
    }

    // Có thể thêm các phương thức khác nếu cần (getById, add, update, delete)
    // Ví dụ:
    public function getNganhHocById($maNganh)
    {
        $query = "SELECT MaNganh, TenNganh FROM " . $this->table_name . " WHERE MaNganh = :maNganh";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maNganh', $maNganh);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
