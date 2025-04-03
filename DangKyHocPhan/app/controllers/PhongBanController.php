<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/PhongBanModel.php');

class PhongBanController
{
    private $phongBanModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->phongBanModel = new PhongBanModel($this->db);
    }

    public function list()
    {
        $categories = $this->phongBanModel->getPhongBans();
        include '../app/views/phongBan/list.php';
    }
}
