<?php
include "session_check.php";
if (!isset($_SESSION['nama'])) {
    header("Location: login.php");
    exit;
}
include "db.php";

// Ambil skor terakhir & high score user
$email = $_SESSION['email'] ?? null;
$user_high_score = 0;
$user_current_score = 0;

if ($email) {
    $result = $conn->query("SELECT high_score, current_score FROM users WHERE email='$email' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_high_score = (int) ($row['high_score'] ?? 0);
        $user_current_score = (int) ($row['current_score'] ?? 0);
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuis Matematika</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .game-box {
            margin-top: 25px;
            padding: 20px;
            border-radius: 15px;
            background: linear-gradient(135deg, #fbc2eb, #a6c1ee);
            color: #333;
        }
        .game-box h3 { margin-bottom: 15px; color: #4b0082; }
        .game-box input {
            width: 50%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            text-align: center;
        }
        .game-box button {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #9d50bb, #6e48aa);
            color: white;
            cursor: pointer;
        }
        .game-box button:hover {
            background: linear-gradient(135deg, #8e2de2, #4a00e0);
        }
        #timer {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #6e48aa;
        }
    </style>
</head>
<body>
<div class="container">
    <p class="welcome">Selamat datang, <?= $_SESSION['nama'] ?>!</p>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<script>
    let score = 0;
    let correctAnswer = 0;
    let timeLeft = 60;
    let timerId;

    function newQuestion() {
        let num1 = Math.floor(Math.random() * 10) + 1;
        let num2 = Math.floor(Math.random() * 10) + 1;
        let ops = ["+", "-", "*"];
        let op = ops[Math.floor(Math.random() * ops.length)];

        let qText = `${num1} ${op} ${num2}`;
        correctAnswer = eval(qText);

        document.getElementById("question").innerText = qText + " = ?";
        document.getElementById("answer").value = "";
    }

    function checkAnswer() {
        let ans = parseInt(document.getElementById("answer").value);
        let result = document.getElementById("result");

        if (isNaN(ans)) {
            result.innerHTML = "⚠️ Masukkan angka!";
            result.style.color = "red";
            return;
        }

        if (ans === correctAnswer) {
            score += 10;
            result.innerHTML = "✅ Benar!";
            result.style.color = "green";
            updateHighScore(score);
        } else {
            result.innerHTML = "❌ Salah! Jawaban: " + correctAnswer;
            result.style.color = "red";
        }
        document.getElementById("score").innerText = score;
        newQuestion();
    }

    function updateHighScore(newScore) {
        let highScore = parseInt(document.getElementById("highScore").innerText);
        if (newScore > highScore) {
            document.getElementById("highScore").innerText = newScore;

            // Simpan high score ke database
            fetch("update_score.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "score=" + newScore
            });
        }
    }

    function startTimer() {
        timerId = setInterval(() => {
            timeLeft--;
            document.getElementById("timer").innerText = "⏳ Waktu: " + timeLeft + " detik";

            if (timeLeft <= 0) {
                clearInterval(timerId);
                endGame();
            }
        }, 1000);
    }

    function endGame() {
        document.getElementById("question").innerText = "⏰ Waktu habis!";
        document.getElementById("submitBtn").disabled = true;
        document.getElementById("answer").disabled = true;
        document.getElementById("result").innerHTML = "Skor akhir kamu: " + score;
        document.getElementById("result").style.color = "blue";
    }

    // Mulai game
    newQuestion();
    startTimer();
</script>
</body>
</html>
