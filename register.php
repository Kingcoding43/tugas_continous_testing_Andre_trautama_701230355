<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama  = trim($_POST['nama']);
    $email = strtolower(trim($_POST['email']));
    $pass  = $_POST['password'];

    if (empty($nama) || empty($email) || empty($pass)) {
        $error = "Semua kolom wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($pass) < 8) {
        $error = "Password minimal 8 karakter!";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $conn->query("INSERT INTO users (nama, email, password) VALUES ('$nama','$email','$hash')");
            header("Location: login.php?msg=Registrasi berhasil, silakan login");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Registrasi Akun</h2>
    <form method="POST">
        <input type="text" name="nama" placeholder="Nama Lengkap" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Sign Up</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login</a></p>
    <p class="error"><?= $error ?? '' ?></p>
</div>
</body>
</html>
