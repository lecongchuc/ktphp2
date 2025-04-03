<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/NhanVienModel.php');
require_once('app/models/PhongBanModel.php');
require_once('app/models/UserModel.php');

class NhanVienController
{
    private $nhanVienModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->nhanVienModel = new NhanVienModel($this->db);
    }

    public function index()
    {
        $nhanViens = $this->nhanVienModel->getNhanViens();
        include 'app/views/nhanVien/list.php';
    }

    public function show($id)
    {
        $nhanVien = $this->nhanVienModel->getNhanVienById($id);
        if ($nhanVien) {
            include 'app/views/nhanVien/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function add()
    {
        $phongBans = (new PhongBanModel($this->db))->getPhongBans();
        include_once 'app/views/nhanVien/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['idNV'] ?? '';
            $ten = $_POST['ten'] ?? '';
            $phai = $_POST['phai'] ?? null;
            $noiSinh = $_POST['noiSinh'] ?? null;
            $luong = $_POST['luong'] ?? null;
            $maPhongBan = $_POST['maPhongBan'] ?? null;
            if ($_COOKIE['username'] != null) {
                $roleUser = (new UserModel($this->db))->getUserRole($_COOKIE['username']);
                if ($roleUser != 'admin') {
                    $errors = "Bạn không có quyền thêm nhân viên!";
                    include 'app/views/nhanVien/add.php';
                }
            } else {
                header('Location: /manguonmo/QL_NhanSu/NhanVien');
            }
            $result = $this->nhanVienModel->addNhanVien($id, $ten, $phai, $noiSinh, $luong, $maPhongBan);
            if (is_array($result)) {
                $errors = $result;
                $phongBans = (new PhongBanModel($this->db))->getPhongBans();
                include 'app/views/nhanVien/add.php';
            } else {
                header('Location: /manguonmo/QL_NhanSu/NhanVien');
            }
        }
    }

    public function edit($id)
    {
        $nhanVien = $this->nhanVienModel->getNhanVienById($id);
        $phongBans = (new PhongBanModel($this->db))->getPhongBans();
        if ($nhanVien) {
            include 'app/views/nhanVien/edit.php';
        } else {
            echo "Không thấy nhân viên.";
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['idNV'];
            $ten = $_POST['ten'];
            $phai = $_POST['phai'];
            $noiSinh = $_POST['noiSinh'];
            $luong = $_POST['luong'];
            $maPhongBan = $_POST['maPhongBan'];
            $edit = $this->nhanVienModel->updateNhanVien($id, $ten, $phai, $noiSinh, $luong, $maPhongBan);
            if ($edit) {
                header('Location: /manguonmo/QL_NhanSu/NhanVien');
            } else {
                echo "Đã xảy ra lỗi khi lưu nhân viên.";
            }
        }
    }

    public function delete($id)
    {
        if ($this->nhanVienModel->deleteNhanVien($id)) {
            header('Location: /manguonmo/QL_NhanSu/NhanVien');
        } else {
            echo "Đã xảy ra lỗi khi xóa nhân viên.";
        }
    }
}
