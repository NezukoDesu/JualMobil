<?php 
session_start();
include 'DB.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_username = mysqli_real_escape_string($conn, $_POST['username']);
    $input_password = mysqli_real_escape_string($conn, $_POST['password']);

    // Ambil data berdasarkan username PERSIS (case-sensitive)
    $query = "SELECT * FROM users WHERE BINARY username = '$input_username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($input_password, $user['password'])) {
            $_SESSION['id'] = $user['id']; 
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['foto'] = $user['foto']; // Add this line to store photo in session
            header("Location: Index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - JualMobil</title>
    <link rel="stylesheet" href="./Style/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h2>Login Page</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>

            <div class="auth-links">
                <p>Belum punya akun? <a href="Register.php">Daftar Sekarang</a></p>
            </div>
        </form>
    </div>
</body>
</html>

