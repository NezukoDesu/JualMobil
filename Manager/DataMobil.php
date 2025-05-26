<?php
session_start();
include('../DB.php');

// Akses hanya untuk Super Admin dan Manager
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    header("Location: ../Login.php");
    exit;
}

// Pagination setup
$limit = 10; // maksimal 10 mobil per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Total data
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM mobil");
$totalRow = mysqli_fetch_assoc($totalQuery);
$totalMobil = $totalRow['total'];
$totalPages = ceil($totalMobil / $limit);

// Ambil data mobil untuk halaman sekarang
$query = "SELECT * FROM mobil ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Data Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Daftar Mobil</h2>

    <a href="../SuperAdmin/TambahMobil.php" class="btn btn-primary mb-3">+ Tambah Mobil</a>

    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th>No</th>
                <th>Nama Mobil</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="mobilTableBody">
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            $no = $offset + 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $shortDesc = strlen($row['keterangan']) > 20 ? substr($row['keterangan'], 0, 20) . '...' : $row['keterangan'];
                echo "<tr id='row-{$row['id']}'>";
                echo "<td>{$no}</td>";
                echo "<td>{$row['nama']}</td>";
                echo "<td>{$row['stok']}</td>";
                echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                echo "<td>{$shortDesc}</td>";
                echo "<td>
                        <a href='../SuperAdmin/EditMobil.php?id={$row['id']}' class='btn btn-warning btn-sm me-2'>Edit</a>
                        <button onclick='hapusMobil({$row['id']})' class='btn btn-danger btn-sm'>Hapus</button>
                      </td>";
                echo "</tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>Tidak ada data mobil.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
      <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1 ?>">Prev</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
        <?php endif; ?>
      </ul>
    </nav>

    <a href="../Index.php" class="btn btn-secondary" style="margin-bottom:20px;">Kembali</a>
</div>

<script>
function hapusMobil(id) {
    if (confirm('Yakin ingin menghapus mobil ini?')) {
        fetch('../SuperAdmin/HapusMobil.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id)
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === 'success') {
                alert('Data mobil berhasil dihapus.');
                const row = document.getElementById('row-' + id);
                if (row) row.remove();
            } else {
                alert('Gagal menghapus data: ' + data);
            }
        })
        .catch(err => alert('Error: ' + err));
    }
}
</script>

</body>
</html>
