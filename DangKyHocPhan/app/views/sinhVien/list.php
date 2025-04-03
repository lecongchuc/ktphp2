<?php
// File: app/views/sinhVien/list.php

// Bao gồm header chung
include 'app/views/shares/header.php';
?>

<h1>Danh sách sinh viên</h1>

<?php
// --- Phần Logic Phân Trang ---
$itemsPerPage = 4;
$totalItems = count($sinhViens);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) {
    $currentPage = 1;
} elseif ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
}
$startIndex = ($currentPage - 1) * $itemsPerPage;
$sinhViensPage = array_slice($sinhViens, $startIndex, $itemsPerPage);
// --- Kết thúc Logic Phân Trang ---

// --- Kiểm tra quyền Admin ---
$isAdmin = true;
// if (isset($_COOKIE['username'])) {
//     try {
//         // --- TẠM THỜI MÔ PHỎNG ---
//         if ($_COOKIE['username'] === 'admin_user_demo') {
//             $isAdmin = true;
//         }
//         // --- KẾT THÚC MÔ PHỎNG ---
//     } catch (Exception $e) {
//         error_log("Lỗi kiểm tra quyền trong sinhVienListView: " . $e->getMessage());
//         $isAdmin = false;
//     }
// }
// --- Kết thúc kiểm tra quyền Admin ---

?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Thao tác thành công!</div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger">Có lỗi xảy ra: <?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if ($isAdmin): ?>
    <p><a href="/manguonmo/DangKyHocPhan/SinhVien/add" class="btn btn-primary mb-3">Thêm sinh viên mới</a></p>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead class="table-primary">
        <tr>
            <th>Mã SV</th>
            <th>Họ Tên</th>
            <th>Giới tính</th>
            <th>Ngày sinh</th>
            <th>Hình ảnh</th>
            <th>Tên Ngành</th>
            <?php if ($isAdmin) : // Thêm cột Hành động nếu là admin 
            ?>
                <th style="width: 180px;">Hành động</th> <?php // Tăng độ rộng cột hành động 
                                                            ?>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($sinhViensPage)) :
            foreach ($sinhViensPage as $sinhVien) :
        ?>
                <tr>
                    <td><?php echo htmlspecialchars($sinhVien->MaSV, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($sinhVien->HoTen, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php
                        $gioiTinh = strtoupper(htmlspecialchars($sinhVien->GioiTinh ?? '', ENT_QUOTES, 'UTF-8'));
                        echo $gioiTinh;
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($sinhVien->NgaySinh ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php
                        if (!empty($sinhVien->Hinh)) :
                            // Giả định $sinhVien->Hinh lưu dạng "/Content/images/tenfile.jpg"
                            $imagePath = "/manguonmo/DangKyHocPhan" . htmlspecialchars($sinhVien->Hinh, ENT_QUOTES, 'UTF-8');
                        ?>
                            <img src="<?php echo $imagePath; ?>?t=<?php echo time(); // Cache busting 
                                                                    ?>" alt="Ảnh <?php echo htmlspecialchars($sinhVien->HoTen, ENT_QUOTES, 'UTF-8'); ?>" width="60" height="60" style="object-fit: cover; border-radius: 50%;">
                        <?php else : ?>
                            <span class="text-muted">(Chưa có ảnh)</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($sinhVien->TenNganh ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>

                    <?php if ($isAdmin) : // Chỉ hiển thị các nút hành động cho admin 
                    ?>
                        <td>
                            <a href="/manguonmo/DangKyHocPhan/SinhVien/show/<?php echo $sinhVien->MaSV; ?>" class="btn btn-sm btn-info" title="Xem chi tiết">Xem</a>
                            &nbsp; <a href="/manguonmo/DangKyHocPhan/SinhVien/edit/<?php echo $sinhVien->MaSV; ?>" class="btn btn-sm btn-warning" title="Chỉnh sửa">Sửa</a>
                            &nbsp; <a href="/manguonmo/DangKyHocPhan/SinhVien/delete/<?php echo $sinhVien->MaSV; ?>" class="btn btn-sm btn-danger" title="Xóa sinh viên" onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên [<?php echo htmlspecialchars($sinhVien->HoTen, ENT_QUOTES, 'UTF-8'); ?>]? Thao tác này không thể hoàn tác.');">Xóa</a>
                            <!-- <a href="/manguonmo/DangKyHocPhan/HocPhan/showRegistrationForm/<?php echo $sinhVien->MaSV; ?>" class="btn btn-sm btn-success" title="Đăng ký học phần cho sinh viên này">ĐK HP</a> -->
                        </td>
                    <?php endif; ?>
                </tr>
            <?php
            endforeach;
        else : // Trường hợp không có sinh viên
            ?>
            <tr>
                <td colspan="<?php echo $isAdmin ? 7 : 6; ?>" class="text-center">Không có dữ liệu sinh viên nào.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if ($totalPages > 1) : ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<?php
// Bao gồm footer chung
include 'app/views/shares/footer.php';
?>