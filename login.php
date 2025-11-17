<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));
    $pass  = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    // cek jika akun terkunci
    if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
        $error = "Akun terkunci. Coba lagi setelah 5 menit.";
    } else {
        if ($user && password_verify($pass, $user['password'])) {
            // reset gagal login
            $conn->query("UPDATE users SET failed_login=0, locked_until=NULL WHERE email='$email'");
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['last_activity'] = time(); // untuk session timeout
            header("Location: home.php");
            exit;
        } else {
            if ($user) {
                $failed = $user['failed_login'] + 1;
                if ($failed >= 3) {
                    $lock_time = date("Y-m-d H:i:s", strtotime("+5 minutes"));
                    $conn->query("UPDATE users SET failed_login=0, locked_until='$lock_time' WHERE email='$email'");
                    $error = "Akun terkunci 5 menit karena 3x gagal login.";
                } else {
                    $conn->query("UPDATE users SET failed_login=$failed WHERE email='$email'");
                    $error = "Email atau password salah.";
                }
            } else {
                $error = "Email atau password salah.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Registrasi</a></p>
    <p class="error"><?= $error ?? ($_GET['msg'] ?? '') ?></p>
</div>
</body>
</html>
