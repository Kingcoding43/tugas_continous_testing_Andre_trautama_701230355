<?php
session_start();
$timeout = 300; // 5 menit
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_destroy();
        header("Location: login.php?msg=Session timeout, silakan login kembali.");
        exit;
    }
}
$_SESSION['last_activity'] = time();
?>
