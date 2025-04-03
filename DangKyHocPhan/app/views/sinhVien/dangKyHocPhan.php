<?php
// File: app/views/dangKy/form.php

// Bao gồm header chung
include 'app/views/shares/header.php';

// Biến $sinhVien và $hocPhans được truyền từ HocPhanController::showRegistrationForm()
?>

<div class="container mt-4">
    <?php if ($sinhVien) : ?>
        <h1 class="mb-3">Đăng ký học phần</h1>
        <div class="card mb-4">
            <div class="card-header">
                Thông tin sinh viên
            </div>
            <div class="card-body">
                <p class="card-text"><strong>Mã Sinh viên:</strong> <?php echo htmlspecialchars($sinhVien->MaSV, ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="card-text"><strong>Họ Tên:</strong> <?php echo htmlspecialchars($sinhVien->HoTen, ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="card-text"><strong>Ngành học:</strong> <?php echo htmlspecialchars($sinhVien->TenNganh ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Đăng ký học phần thành công!</div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Có lỗi xảy ra: <?php echo htmlspecialchars(urldecode($_GET['error']), ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>


        <?php if (!empty($hocPhans)) : ?>
            <h3 class="mb-3">Danh sách học phần có thể đăng ký</h3>

            <form method="POST" action="/manguonmo/HocPhanHocPhan/HocPhan/processRegistration" id="registrationForm">

                <input type="hidden" name="maSV" value="<?php echo htmlspecialchars($sinhVien->MaSV, ENT_QUOTES, 'UTF-8'); ?>">

                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">Chọn</th>
                            <th>Mã HP</th>
                            <th>Tên Học Phần</th>
                            <th class="text-center">Số Tín Chỉ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hocPhans as $hocPhan) : ?>
                            <tr>
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox"
                                        name="maHPs[]"
                                        value="<?php echo htmlspecialchars($hocPhan->MaHP, ENT_QUOTES, 'UTF-8'); ?>"
                                        id="hp_<?php echo htmlspecialchars($hocPhan->MaHP, ENT_QUOTES, 'UTF-8'); ?>">
                                </td>
                                <td>
                                    <label for="hp_<?php echo htmlspecialchars($hocPhan->MaHP, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($hocPhan->MaHP, ENT_QUOTES, 'UTF-8'); ?>
                                    </label>
                                </td>
                                <td>
                                    <label for="hp_<?php echo htmlspecialchars($hocPhan->MaHP, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($hocPhan->TenHP, ENT_QUOTES, 'UTF-8'); ?>
                                    </label>
                                </td>
                                <td class="text-center"><?php echo htmlspecialchars($hocPhan->SoTinChi, ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="mt-3 text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Đăng ký các học phần đã chọn</button>
                </div>

            </form>

        <?php else : ?>
            <div class="alert alert-info">Hiện tại không có học phần nào để đăng ký.</div>
        <?php endif; ?>

    <?php else : ?>
        <div class="alert alert-danger">Không thể tải thông tin sinh viên. Vui lòng thử lại.</div>
    <?php endif; ?>
</div>

<?php
// Bao gồm footer chung
include 'app/views/shares/footer.php';
?>