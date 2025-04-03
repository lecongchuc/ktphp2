<?php
// File: app/views/sinhVien/edit.php

// Bao gồm header chung
include 'app/views/shares/header.php';

// Biến $sinhVien và $nganhHocs được truyền từ SinhVienController::edit()
// Biến $errors có thể được truyền từ SinhVienController::update() nếu có lỗi
?>

<h1>Chỉnh sửa thông tin sinh viên</h1>

<?php
// Hiển thị lỗi validation hoặc lỗi CSDL nếu có
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
// Lấy lại dữ liệu đã nhập nếu có lỗi từ lần submit trước (ưu tiên hơn dữ liệu gốc)
$old_data = $_POST ?? [];
// Helper function to get value, prioritizing old POST data then original $sinhVien data
function getValue($field_name, $original_object, $post_data)
{
    return htmlspecialchars($post_data[$field_name] ?? $original_object->$field_name ?? '', ENT_QUOTES, 'UTF-8');
}
// Helper function to check selected state for dropdowns/radios
function isSelected($field_name, $value_to_check, $original_object, $post_data)
{
    $current_value = $post_data[$field_name] ?? $original_object->$field_name ?? '';
    return ($current_value == $value_to_check) ? 'selected' : '';
}

// Lấy thông tin ảnh hiện tại từ đối tượng $sinhVien
// Đường dẫn này có thể cần điều chỉnh dựa vào cách bạn lưu trong DB và cấu trúc thư mục
// Giả định $sinhVien->Hinh lưu dạng "/Content/images/tenfile.jpg"
$currentImagePath = $sinhVien->Hinh ?? '';
$fullImagePathOnServer = $_SERVER['DOCUMENT_ROOT'] . '/manguonmo/DangKyHocPhan' . $currentImagePath;
$displayImagePath = '/manguonmo/DangKyHocPhan' . $currentImagePath;

?>

<form method="POST" action="/manguonmo/DangKyHocPhan/SinhVien/update" enctype="multipart/form-data" onsubmit="return validateSinhVienEditForm();">

    <div class="form-group mb-3">
        <label>Mã Sinh viên:</label>
        <input type="text" class="form-control" value="<?php echo getValue('MaSV', $sinhVien, []); // MaSV không có trong POST, lấy từ $sinhVien 
                                                        ?>" readonly>
        <input type="hidden" name="maSV" value="<?php echo htmlspecialchars($sinhVien->MaSV ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div class="form-group mb-3">
        <label for="hoTen">Họ và Tên:</label>
        <input type="text" id="hoTen" name="hoTen" class="form-control" value="<?php echo getValue('hoTen', $sinhVien, $old_data); ?>" required>
        <?php if (isset($errors['hoTen'])): ?><div class="text-danger"><?php echo $errors['hoTen']; ?></div><?php endif; ?>
    </div>

    <div class="form-group mb-3">
        <label for="gioiTinh">Giới tính:</label>
        <select id="gioiTinh" name="gioiTinh" class="form-control">
            <option value="Nam" <?php echo isSelected('gioiTinh', 'Nam', $sinhVien, $old_data); ?>>Nam</option>
            <option value="Nữ" <?php echo isSelected('gioiTinh', 'Nữ', $sinhVien, $old_data); ?>>Nữ</option>
            <option value="Khác" <?php echo isSelected('gioiTinh', 'Khác', $sinhVien, $old_data); ?>>Khác</option>
        </select>
        <?php if (isset($errors['gioiTinh'])): ?><div class="text-danger"><?php echo $errors['gioiTinh']; ?></div><?php endif; ?>
    </div>

    <div class="form-group mb-3">
        <label for="ngaySinh">Ngày sinh:</label>
        <?php
        // Lấy ngày sinh, ưu tiên dữ liệu POST cũ, nếu không thì lấy từ $sinhVien
        $ngaySinhValue = $old_data['ngaySinh'] ?? $sinhVien->NgaySinh ?? '';
        // Chuyển đổi định dạng nếu cần (ví dụ: từ DB là Y-m-d H:i:s sang Y-m-d)
        if ($ngaySinhValue) {
            try {
                $date = new DateTime($ngaySinhValue);
                $ngaySinhValue = $date->format('Y-m-d');
            } catch (Exception $e) {
                $ngaySinhValue = ''; // Reset nếu định dạng không hợp lệ
            }
        }
        ?>
        <input type="date" id="ngaySinh" name="ngaySinh" class="form-control" value="<?php echo htmlspecialchars($ngaySinhValue, ENT_QUOTES, 'UTF-8'); ?>">
        <?php if (isset($errors['ngaySinh'])): ?><div class="text-danger"><?php echo $errors['ngaySinh']; ?></div><?php endif; ?>
    </div>

    <div class="form-group mb-3">
        <label>Ảnh hiện tại:</label>
        <div>
            <?php if (!empty($currentImagePath) && file_exists($fullImagePathOnServer)): ?>
                <img src="<?php echo $displayImagePath; ?>?t=<?php echo time(); // Cache busting 
                                                                ?>" alt="Ảnh hiện tại" width="100" height="100" style="object-fit: cover; margin-bottom: 10px; border-radius: 50%;">
            <?php else: ?>
                <span class="text-muted">(Chưa có ảnh)</span>
            <?php endif; ?>
            <input type="hidden" name="hinh_current" value="<?php echo htmlspecialchars($currentImagePath, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <label for="hinhFile">Chọn ảnh mới (để thay thế):</label>
        <input type="file" id="hinhFile" name="hinhFile" class="form-control">
        <small class="form-text text-muted">Để trống nếu không muốn thay đổi ảnh hiện tại.</small>
        <?php if (isset($errors['hinhFile'])): ?><div class="text-danger"><?php echo $errors['hinhFile']; ?></div><?php endif; ?>
    </div>


    <div class="form-group mb-3">
        <label for="maNganh">Ngành học:</label>
        <select id="maNganh" name="maNganh" class="form-control" required>
            <option value="">-- Chọn Ngành học --</option>
            <?php
            // Lặp qua danh sách ngành học ($nganhHocs từ Controller)
            if (!empty($nganhHocs)) {
                foreach ($nganhHocs as $nganhHoc) :
                    // Kiểm tra xem ngành này có phải là ngành hiện tại của SV không (ưu tiên POST data)
                    $selected = isSelected('maNganh', $nganhHoc->MaNganh, $sinhVien, $old_data);
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

    <button type="submit" class="btn btn-primary">Cập nhật sinh viên</button>
    <a href="/manguonmo/DangKyHocPhan/SinhVien/index" class="btn btn-secondary">Hủy</a>
</form>


<script>
    // Đặt hàm validateForm() của bạn ở đây hoặc trong file JS chung
    function validateSinhVienEditForm() {
        // Ví dụ validation cơ bản phía client cho form edit
        let hoTen = document.getElementById('hoTen').value.trim();
        let maNganh = document.getElementById('maNganh').value;
        let hinhFile = document.getElementById('hinhFile').files.length > 0 ? document.getElementById('hinhFile').files[0] : null;


        if (hoTen === '') {
            alert('Họ tên không được để trống!');
            return false; // Ngăn form submit
        }
        if (maNganh === '') {
            alert('Vui lòng chọn ngành học!');
            return false;
        }

        // Kiểm tra file ảnh nếu có chọn file mới
        if (hinhFile) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (!allowedTypes.includes(hinhFile.type)) {
                alert('Loại file ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF.');
                return false;
            }
            if (hinhFile.size > maxSize) {
                alert('Kích thước file ảnh quá lớn (tối đa 10MB).');
                return false;
            }
        }

        return true; // Cho phép form submit
    }
</script>

<?php
// Bao gồm footer chung
include 'app/views/shares/footer.php';
?>