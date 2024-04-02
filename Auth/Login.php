<?php
session_start();
require "../database/conf.php";
require_once __DIR__ . '/../vendor/autoload.php'; // Ubah path ini sesuai dengan struktur direktori Anda
use Dotenv\Dotenv;


// inisiasi variable error dan pesan 
$error = "";
$message = "";

// Load konfigurasi dari file .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); // Ubah path ini sesuai dengan struktur direktori Anda
$dotenv->load();

// Gunakan konfigurasi
$db_host = $_ENV['DB_HOST'];
$db_username = $_ENV['DB_USERNAME'];
$db_password = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];

// Buat koneksi ke database
$database = new Database($db_host, $db_username, $db_password, $db_name);

$mysql = $database->getConnection();


function login($data, $mysql)
{
    //get Data
    $username = $data["userName"];
    $password = $data["password"];

    //check is not empty
    if (empty($username) || empty($password)) {
        $error = "Username dan Password tidak boleh kosong";
        echo "<script> alert('$error'); </script>";
        return false;
    }

    //check apakah username ada
    $query = mysqli_query($mysql, "SELECT name, password FROM users WHERE name = '$username'");

    if (mysqli_num_rows($query)) {
        $checkQuery = mysqli_fetch_assoc($query);

        //check password is valid
        if (password_verify($password, $checkQuery['password'])) {
            //jika sukses redirect ke halaman todo
            //membuat session untuk user
            $_SESSION['login'] = true;
            $_SESSION['id'] = $checkQuery['id'];
            // Set waktu kedaluwarsa session (contoh: 1 jam)
            $_SESSION['timeout'] = time() + 3600; // 3600 detik = 1 jam
            // Atur cookie untuk menyimpan username
            setcookie("username", $username, time() + (86400 * 30), "/"); // 86400 detik = 1 hari
            header("Location: ../todolist/todo.php");
            exit();
        } else {
            $error = "Password salah";
            echo "<script> alert ('$error'); </script>";
        }
    } else {
        $error = "User tidak ditemukan. Silahkan Lakukan Register Terlebih Dahulu.";
        echo "<script> alert ('$error'); </script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/auth.css">
    <title>Login</title>
</head>

<body>
    <div class="wrapper">
        <!-- Pesan Kesalahan atau Pesan Berhasil -->
        <?php if ($error != "") : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($message != "") : ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="logo">
            <img src="https://www.freepnglogos.com/uploads/stitch-png/dark-lilo-stitch-character-png-transparent-0.png" alt="">
        </div>
        <div class="text-center mt-4 name">
            Login
        </div>
        <form class="p-3 mt-3" action="" method="POST">
            <div class="form-field d-flex align-items-center">
                <span class="far fa-user"></span>
                <input type="text" name="userName" id="userName" placeholder="Username">
            </div>
            <div class="form-field d-flex align-items-center">
                <span class="fas fa-key"></span>
                <input type="password" name="password" id="pwd" placeholder="Password">
            </div>
            <button type="submit" class="btn mt-3" name="login">Login</button>
        </form>
        <div class="text-center fs-6">
            <a href="#">Forget password?</a> or <a href="./Register.php">Register</a>
        </div>
    </div>

    <?php
    if (isset($_POST['login'])) {
        $username = $_POST['userName'];
        $password = $_POST['password'];
        login($_POST, $mysql);
    }
    ?>

</body>

</html>