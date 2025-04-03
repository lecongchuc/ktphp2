<?php include 'app/views/shares/header.php'; ?>

<h1>Thêm nhân viên mới</h1>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="/manguonmo/QL_NhanSu/NhanVien/save" onsubmit="return validateForm();">
    <div class="form-group">
        <label for="name">Mã Nhân viên:</label>
        <input type="text" id="idNV" name="idNV" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="name">Tên Nhân viên:</label>
        <input type="text" id="ten" name="ten" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Phái:</label>
        <input type="text" id="phai" name="phai" class="form-control">
    </div>
    <div class="form-group">
        <label for="description">Nơi sinh:</label>
        <input type="text" id="noiSinh" name="noiSinh" class="form-control">
    </div>
    <div class="form-group">
        <label for="price">Lương:</label>
        <input type="number" id="luong" name="luong" class="form-control" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="maPhongBan">Phòng ban:</label>
        <select id="maPhongBan" name="maPhongBan" class="form-control" required>
            <?php foreach ($phongBans as $phongBan): ?>
                <option value="<?php echo $phongBan->Ma_Phong; ?>"><?php echo htmlspecialchars($phongBan->Ten_Phong, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Thêm nhân viên</button>
</form>

<a href="/manguonmo/QL_NhanSu/NhanVien/index" class="btn btn-secondary mt-2">Quay lại danh sách nhân viên</a>

<?php include 'app/views/shares/footer.php'; ?>