<?php
session_start();
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'Super Admin' && $_SESSION['role'] !== 'Manager')) {
    exit('Unauthorized access');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Filter Laporan Pemesanan</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
            text-align: center;
            color: #2c3e50;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #34495e;
        }

        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2c3e50;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #1a252f;
        }
        .back-btn {
    display: block;
    margin-top: 15px;
    text-align: center;
    font-size: 14px;
    color: #2c3e50;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.2s ease;
}

.back-btn:hover {
    color: #1a252f;
}
    </style>
</head>
<body>
    <div class="card">
        <h2>Filter Laporan Pemesanan</h2>
        <form method="GET" action="ExportPesanan.php">
            <label for="bulan">Pilih Bulan:</label>
            <select name="bulan" id="bulan" required>
                <?php
                for ($i = 1; $i <= 12; $i++) {
                    $namaBulan = date('F', mktime(0, 0, 0, $i, 1));
                    echo "<option value='$i'>$namaBulan</option>";
                }
                ?>
            </select>

            <label for="tahun">Pilih Tahun:</label>
            <select name="tahun" id="tahun" required>
                <?php
                $tahun = date('Y');
                for ($i = $tahun; $i >= $tahun - 5; $i--) {
                    echo "<option value='$i'>$i</option>";
                }
                ?>
            </select>

            <button type="submit">Export as PDF</button>
        </form>
            <a href="Laporan.php" class="back-btn">← Kembali ke Laporan</a>
    </div>
</body>
</html>
