<?php
// File: app/views/hocPhan/list.php

include 'app/views/shares/header.php';

// Biến $hocPhans và $loggedInMaSV được truyền từ HocPhanController::index()
// Biến $isAdmin được kiểm tra (logic giả định)
?>

<h1>Danh sách học phần</h1>

<?php
// --- Phân Trang (Giữ nguyên) ---
$itemsPerPage = 10;
$totalItems = count($hocPhans);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// ... (logic tính $startIndex, $hocPhansPage như trước) ...
$startIndex = ($currentPage - 1) * $itemsPerPage;
$hocPhansPage = array_slice($hocPhans, $startIndex, $itemsPerPage);

// --- Kiểm tra quyền Admin (Giữ nguyên logic giả định) ---
$isAdmin = false;
if (isset($_COOKIE['username'])) {
    if ($_COOKIE['username'] === 'admin_user_demo') {
        $isAdmin = true;
    }
}

// --- Xác định có sinh viên đăng nhập không (từ controller) ---
$isStudentLoggedIn = isset($loggedInMaSV) && !empty($loggedInMaSV);

?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php
        if ($_GET['success'] == 'single_registered' && isset($_GET['hp'])) {
            echo "Đăng ký học phần " . htmlspecialchars(urldecode($_GET['hp'])) . " thành công!";
        } else {
            echo "Thao tác thành công!";
        }
        ?>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo "Có lỗi xảy ra: " . htmlspecialchars(urldecode($_GET['error'])); ?>
        <?php if (isset($_GET['hp'])) echo " (HP: " . htmlspecialchars(urldecode($_GET['hp'])) . ")"; ?>
    </div>
<?php endif; ?>

<?php if ($isAdmin): ?>
    <p><a href="/manguonmo/DangKyHocPhan/HocPhan/add" class="btn btn-primary mb-3">Thêm học phần mới</a></p>
<?php endif; ?>

<table class="table table-bordered table-striped table-hover">
    <thead class="table-primary">
        <tr>
            <th style="width: 15%;">Mã HP</th>
            <th>Tên Học Phần</th>
            <th style="width: 15%;" class="text-center">Số Tín Chỉ</th>
            <?php if ($isStudentLoggedIn && !$isAdmin) : // Thêm cột Đăng ký nếu là SV 
            ?>
                <th style="width: 15%;">Đăng ký</th>
            <?php elseif ($isAdmin) : // Cột Hành động cho admin 
            ?>
                <th style="width: 15%;">Hành động</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($hocPhansPage)) :
            foreach ($hocPhansPage as $hocPhan) :
        ?>
                <tr>
                    <td><?php echo htmlspecialchars($hocPhan->MaHP, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($hocPhan->TenHP, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($hocPhan->SoTinChi, ENT_QUOTES, 'UTF-8'); ?></td>

                    <?php if ($isStudentLoggedIn && !$isAdmin) : // Hiển thị nút Đăng ký cho SV 
                    ?>
                        <td class="text-center">
                            <a href="/manguonmo/DangKyHocPhan/HocPhan/registerSingleCourse/<?php echo $hocPhan->MaHP; ?>"
                                class="btn btn-sm btn-success"
                                onclick="return confirm('Bạn muốn đăng ký học phần [<?php echo htmlspecialchars($hocPhan->TenHP, ENT_QUOTES, 'UTF-8'); ?>]?');"
                                title="Đăng ký học phần này">
                                Đăng ký
                            </a>
                            <?php // Có thể thêm logic kiểm tra HP đã đăng ký để disable nút này 
                            ?>
                        </td>
                    <?php elseif ($isAdmin) : // Hiển thị nút Sửa/Xóa cho admin 
                    ?>
                        <td class="text-nowrap">
                            <a href="/manguonmo/DangKyHocPhan/HocPhan/edit/<?php echo $hocPhan->MaHP; ?>" class="btn btn-sm btn-warning" title="Chỉnh sửa học phần">Sửa</a>
                            &nbsp;
                            <a href="/manguonmo/DangKyHocPhan/HocPhan/delete/<?php echo $hocPhan->MaHP; ?>" class="btn btn-sm btn-danger" title="Xóa học phần" onclick="return confirm('Bạn có chắc chắn muốn xóa học phần [<?php echo htmlspecialchars($hocPhan->TenHP, ENT_QUOTES, 'UTF-8'); ?>]? Lưu ý: Xóa học phần có thể ảnh hưởng đến dữ liệu đăng ký của sinh viên.');">Xóa</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php
            endforeach;
        else : // Trường hợp không có học phần
            $colspan = 3 + ($isAdmin || ($isStudentLoggedIn && !$isAdmin) ? 1 : 0); // Tính colspan động
            ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" class="text-center">Không có dữ liệu học phần nào.</td>
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
include 'app/views/shares/footer.php';
?>