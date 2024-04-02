<?php
session_start();

// Cek apakah sesi pengguna sudah ada
if (isset($_SESSION['id'])) {
    // Jika sesi sudah ada, arahkan ke halaman todo list
    header("Location: ./todolist/todo.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/style.css">
    <title>Home</title>
</head>
<body>
<button class='glowing-btn'>
    <a href='./Auth/Login.php'>
        <span class='glowing-txt'>C<span class='faulty-letter'>L</span>ICK</span>
    </a>
</button>

</body>
</html>