<?php include 'app/views/shares/header.php'; ?>

<h1>Danh sách nhân viên</h1>

<?php
$itemsPerPage = 5;
$totalItems = count($nhanViens);
$totalPages = ceil($totalItems / $itemsPerPage);

$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$startIndex = ($currentPage - 1) * $itemsPerPage;

$nhanViensPage = array_slice($nhanViens, $startIndex, $itemsPerPage);
?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Mã Nhân Viên</th>
            <th>Tên Nhân Viên</th>
            <th>Giới tính</th>
            <th>Nơi Sinh</th>
            <th>Tên Phòng</th>
            <th>Lương</th>
            <?php if (isset($_COOKIE['username'])): ?>
                <?php $roleUser = (new UserModel($this->db))->getUserRole($_COOKIE['username']); ?>
                <?php if ($roleUser == 'admin'): ?>
                    <th>Hành động</th>
                <?php endif; ?>
            <?php endif; ?>


        </tr>
    </thead>
    <tbody>
        <?php foreach ($nhanViensPage as $nhanVien): ?>
            <tr>
                <td><?php echo htmlspecialchars($nhanVien->Ma_NV, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($nhanVien->Ten_NV, ENT_QUOTES, 'UTF-8'); ?></td>
                <?php if (htmlspecialchars($nhanVien->Phai, ENT_QUOTES, 'UTF-8') === 'NU'): ?>
                    <td><img src="/manguonmo/QL_NhanSu/public/woman.png" alt="Nữ" width="30" height="30"></td>
                <?php elseif (htmlspecialchars($nhanVien->Phai, ENT_QUOTES, 'UTF-8') === 'NAM'): ?>
                    <td><img src="/manguonmo/QL_NhanSu/public/man.png" alt="Nam" width="30" height="30"></td>
                <?php endif; ?>
                <td><?php echo htmlspecialchars($nhanVien->Noi_Sinh, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($nhanVien->Ten_Phong, ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($nhanVien->Luong, ENT_QUOTES, 'UTF-8'); ?></td>
                <?php if (isset($_COOKIE['username'])): ?>
                    <?php $roleUser = (new UserModel($this->db))->getUserRole($_COOKIE['username']); ?>
                    <?php if ($roleUser == 'admin'): ?>
                        <td><a href="/manguonmo/QL_NhanSu/NhanSu/edit/<?php echo $nhanVien->Ma_NV; ?>" class="btn btn-warning">Sửa</a>
                            <a href="/manguonmo/QL_NhanSu/NhanSu/delete/<?php echo $nhanVien->Ma_NV; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                        </td>
                    <?php endif; ?>
                <?php endif; ?>



            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<nav aria-label="Page navigation example">
    <ul class="pagination">
        <?php if ($currentPage > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">Previous</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">Next</a></li>
        <?php endif; ?>
    </ul>
</nav>

<?php include 'app/views/shares/footer.php'; ?>