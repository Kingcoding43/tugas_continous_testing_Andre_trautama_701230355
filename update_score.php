<?php
session_start();
include "db.php";

if (isset($_POST['score']) && isset($_SESSION['email'])) {
    $score = intval($_POST['score']);
    $email = $_SESSION['email'];

    // update skor saat ini
    $conn->query("UPDATE users SET current_score=$score WHERE email='$email'");

    // update high score jika lebih tinggi
    $result = $conn->query("SELECT high_score FROM users WHERE email='$email'");
    $row = $result->fetch_assoc();
    if ($score > $row['high_score']) {
        $conn->query("UPDATE users SET high_score=$score WHERE email='$email'");
    }
}
