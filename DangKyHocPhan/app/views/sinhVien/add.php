<?php
// File: app/views/sinhVien/add.php

// Bao gồm header chung
include 'app/views/shares/header.php';
?>

<h1>Thêm sinh viên mới</h1>

<?php
// Hiển thị lỗi nếu có (biến $errors được truyền từ SinhVienController::save)
if (!empty($errors)) :
?>
    <div class="alert alert-danger">
        <strong>Có lỗi xảy ra!</strong>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
// Lấy lại dữ liệu đã nhập nếu có lỗi để điền lại form (ví dụ)
$old_data = $_POST ?? []; // Lấy dữ liệu từ POST nếu có lỗi trước đó
?>

<form method="POST" action="/manguonmo/DangKyHocPhan/SinhVien/save" enctype="multipart/form-data" onsubmit="return validateSinhVienForm();">

    <div class="form-group mb-3">
        <label for="maSV">Mã Sinh viên:</label>
        <input type="text" id="maSV" name="maSV" class="form-control" value="<?php echo htmlspecialchars($old_data['maSV'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required maxlength="10">
        <?php if (isset($errors['maSV'])): ?><div class="text-danger"><?php echo $errors['maSV']; ?></div><?php endif; ?>
    </div>

    <div class="form-group mb-3">
        <label for="hoTen">Họ và Tên:</label>
        <input type="text" id="hoTen" name="hoTen" class="form-control" value="<?php echo htmlspecialchars($old_data['hoTen'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        <?php if (isset($errors['hoTen'])): ?><div class="text-danger"><?php echo $errors['hoTen']; ?></div><?php endif; ?>
    </div>

    <div class="form-group mb-3">
        <label for="gioiTinh">Giới tính:</label>
        <select id="gioiTinh" name="gioiTinh" class="form-control">
            <option value="Nam" <?php echo (($old_data['gioiTinh'] ?? '') === 'Nam') ? 'selected' : ''; ?>>Nam</option>
            <option value="Nữ" <?php echo (($old_data['gioiTinh'] ?? '') === 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
            <option value="Khác" <?php echo (($old_data['gioiTinh'] ?? '') === 'Khác') ? 'selected' : ''; ?>>Khác</option>
        </select>
        <?php if (isset($errors['gioiTinh'])): ?><div class="text-danger"><?php echo $errors['gioiTinh']; ?></div><?php endif; ?>
    </div>

    <div class="form-group mb-3">
        <label for="ngaySinh">Ngày sinh:</label>
        <input type="date" id="ngaySinh" name="ngaySinh" class="form-control" value="<?php echo htmlspecialchars($old_data['ngaySinh'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <?php if (isset($errors['ngaySinh'])): ?><div class="text-danger"><?php echo $errors['ngaySinh']; ?></div><?php endif; ?>
    </div>

    <div class="form-group mb-3">
        <label for="hinhFile">Hình ảnh:</label>
        <input type="file" id="hinhFile" name="hinhFile" class="form-control">
        <small class="form-text text-muted">Chọn file ảnh để tải lên (nếu có).</small>
        <?php if (isset($errors['hinhFile'])): ?><div class="text-danger"><?php echo $errors['hinhFile']; ?></div><?php endif; ?>
    </div>

    <div class="form-group mb-3">
        <label for="maNganh">Ngành học:</label>
        <select id="maNganh" name="maNganh" class="form-control" required>
            <option value="">-- Chọn Ngành học --</option>
            <?php
            // Lặp qua danh sách ngành học (biến $nganhHocs được truyền từ SinhVienController::add)
            if (!empty($nganhHocs)) {
                foreach ($nganhHocs as $nganhHoc) :
                    $selected = (($old_data['maNganh'] ?? '') === $nganhHoc->MaNganh) ? 'selected' : '';
            ?>
                    <option value="<?php echo $nganhHoc->MaNganh; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($nganhHoc->TenNganh, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
            <?php
                endforeach;
            } else {
                echo '<option value="" disabled>Không có ngành học nào</option>';
            }
            ?>
        </select>
        <?php if (isset($errors['maNganh'])): ?><div class="text-danger"><?php echo $errors['maNganh']; ?></div><?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Thêm sinh viên</button>
</form>

<a href="/manguonmo/DangKyHocPhan/SinhVien/index" class="btn btn-secondary mt-3">Quay lại danh sách sinh viên</a>

<script>
    // Đặt hàm validateForm() của bạn ở đây hoặc trong file JS chung
    function validateSinhVienForm() {
        // Ví dụ validation cơ bản phía client
        let maSV = document.getElementById('maSV').value.trim();
        let hoTen = document.getElementById('hoTen').value.trim();
        let maNganh = document.getElementById('maNganh').value;

        if (maSV === '') {
            alert('Mã sinh viên không được để trống!');
            return false; // Ngăn form submit
        }
        if (maSV.length > 10) {
            alert('Mã sinh viên không được vượt quá 10 ký tự!');
            return false;
        }
        if (hoTen === '') {
            alert('Họ tên không được để trống!');
            return false;
        }
        if (maNganh === '') {
            alert('Vui lòng chọn ngành học!');
            return false;
        }
        // Thêm các kiểm tra khác nếu cần (ngày sinh, định dạng file ảnh,...)

        return true; // Cho phép form submit
    }
</script>

<?php
// Bao gồm footer chung
include 'app/views/shares/footer.php';
?>