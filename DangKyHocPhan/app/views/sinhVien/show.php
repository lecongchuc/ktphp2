<?php
// File: app/views/sinhVien/show.php

// Bao gồm header chung
include 'app/views/shares/header.php';

// Biến $sinhVien được truyền từ SinhVienController::show($id)
// Giả định $sinhVien là một object chứa đầy đủ thông tin SV, bao gồm cả TenNganh
?>

<?php if ($sinhVien) : // Chỉ hiển thị nếu có thông tin sinh viên 
?>

    <h1 class="mb-4">Chi tiết sinh viên: <?php echo htmlspecialchars($sinhVien->HoTen, ENT_QUOTES, 'UTF-8'); ?></h1>

    <div class="row">
        <div class="col-md-3 text-center mb-3">
            <?php
            // Hiển thị hình ảnh sinh viên
            $imagePathDisplay = '/manguonmo/Content/images/default.jpg'; // Ảnh mặc định
            if (!empty($sinhVien->Hinh)) {
                // Giả định $sinhVien->Hinh lưu dạng "/Content/images/tenfile.jpg"
                $potentialImagePath = "/manguonmo/DangKyHocPhan" . htmlspecialchars($sinhVien->Hinh, ENT_QUOTES, 'UTF-8');
                // Kiểm tra file tồn tại trên server trước khi hiển thị (Tùy chọn nhưng nên có)
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $potentialImagePath)) {
                    $imagePathDisplay = $potentialImagePath;
                }
            }
            ?>
            <img src="<?php echo $imagePathDisplay; ?>?t=<?php echo time(); // Cache busting 
                                                            ?>"
                alt="Ảnh <?php echo htmlspecialchars($sinhVien->HoTen, ENT_QUOTES, 'UTF-8'); ?>"
                class="img-thumbnail rounded-circle"
                style="width: 150px; height: 150px; object-fit: cover;">
        </div>

        <div class="col-md-9">
            <dl class="row">
                <dt class="col-sm-3">Mã Sinh viên:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($sinhVien->MaSV, ENT_QUOTES, 'UTF-8'); ?></dd>

                <dt class="col-sm-3">Họ và Tên:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($sinhVien->HoTen, ENT_QUOTES, 'UTF-8'); ?></dd>

                <dt class="col-sm-3">Giới tính:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($sinhVien->GioiTinh ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></dd>

                <dt class="col-sm-3">Ngày sinh:</dt>
                <dd class="col-sm-9">
                    <?php
                    // Định dạng lại ngày sinh nếu có
                    if (!empty($sinhVien->NgaySinh)) {
                        try {
                            $date = date_create($sinhVien->NgaySinh);
                            echo date_format($date, 'd/m/Y'); // Định dạng ngày/tháng/năm
                        } catch (Exception $e) {
                            echo htmlspecialchars($sinhVien->NgaySinh, ENT_QUOTES, 'UTF-8'); // Hiển thị gốc nếu lỗi định dạng
                        }
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </dd>

                <dt class="col-sm-3">Ngành học:</dt>
                <dd class="col-sm-9"><?php echo htmlspecialchars($sinhVien->TenNganh ?? 'N/A', ENT_QUOTES, 'UTF-8'); // TenNganh lấy từ JOIN trong Model 
                                        ?></dd>

            </dl>

            <div class="mt-4">
                <a href="/manguonmo/DangKyHocPhan/SinhVien/index" class="btn btn-secondary">Quay lại danh sách</a>

                <?php
                // --- Kiểm tra quyền Admin để hiển thị nút Sửa/Xóa ---
                // Tương tự như trong list view, logic này nên ở Controller
                $isAdmin = false;
                if (isset($_COOKIE['username'])) {
                    // --- TẠM THỜI MÔ PHỎNG ---
                    if ($_COOKIE['username'] === 'admin_user_demo') {
                        $isAdmin = true;
                    }
                    // --- KẾT THÚC MÔ PHỎNG ---
                }

                if ($isAdmin) :
                ?>
                    <a href="/manguonmo/DangKyHocPhan/SinhVien/edit/<?php echo $sinhVien->MaSV; ?>" class="btn btn-warning">Chỉnh sửa</a>
                    <a href="/manguonmo/DangKyHocPhan/SinhVien/delete/<?php echo $sinhVien->MaSV; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên [<?php echo htmlspecialchars($sinhVien->HoTen, ENT_QUOTES, 'UTF-8'); ?>]?');">Xóa</a>
                <?php endif; ?>
            </div>

        </div>
    </div> <?php else : // Trường hợp $sinhVien không tồn tại 
            ?>
    <div class="alert alert-warning" role="alert">
        Không tìm thấy thông tin sinh viên.
    </div>
    <a href="/manguonmo/DangKyHocPhan/SinhVien/index" class="btn btn-secondary">Quay lại danh sách</a>
<?php endif; ?>


<?php
// Bao gồm footer chung
include 'app/views/shares/footer.php';
?>