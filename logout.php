<?php
session_start();
session_destroy();
header("Location: login.php?msg=Anda telah logout.");
exit;
?>
