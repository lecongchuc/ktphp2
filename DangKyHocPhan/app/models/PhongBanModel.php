<?php
class PhongBanModel
{
    private $conn;
    private $table_name = "phongban";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getPhongBans()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
}
