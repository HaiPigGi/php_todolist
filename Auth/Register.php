<?php

require "../database/conf.php";
require_once __DIR__ . '/../vendor/autoload.php'; 
use Dotenv\Dotenv;

// inisiasi variable error dan pesan 
$error = "";
$message = "";
// Load konfigurasi dari file .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..'); 
$dotenv->load();

// Gunakan konfigurasi
$db_host = $_ENV['DB_HOST'];
$db_username = $_ENV['DB_USERNAME'];
$db_password = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];

// Buat koneksi ke database
$database = new Database($db_host, $db_username, $db_password, $db_name);

$mysql = $database->getConnection();
// function untuk register
function Register($data, $mysql)
{
    $username = strtolower(stripslashes($data['userName']));
    $password = mysqli_real_escape_string($mysql, $data['password']);

    //check input tidak boleh kosong atau tidak
    if (empty($username) || empty($password)) {
        $error = "Username Dan Password Tidak Boleh Kosong";
        echo "<script>
    alert ('$error')
    </script>";
        // Menghentikan eksekusi fungsi jika terjadi kesalahan
        return false;
    }

    //check username sudah ada atau tidak 
    $query = mysqli_query($mysql, "SELECT name FROM users WHERE name = '$username'");
    if (mysqli_fetch_assoc($query)) {
        $error = "Username sudah digunakan ";
        echo "<script>alert ('$error');</script>";
        return false;
    }


    //check panjang password adalah 8
    if (strlen($password) < 8) {
        $error = "Password harus memiliki minimal 8 karakter";
        echo "<script>alert ('$error');</script>";
        // Menghentikan eksekusi fungsi jika terjadi kesalahan
        return false;
    }


    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    //membuat id dengan uuid
    $uuid = uniqid();

    // Insert user kedalam database
    mysqli_query($mysql, "INSERT INTO users VALUES ('$uuid', '$username', '$password')");

    // check validasi
    if (mysqli_affected_rows($mysql) > 0) {
        $message = "Registrasi berhasil";
        echo "<script> 
            alert ('$message');
            window.location.href = './Login.php'; 
          </script>";
        return true;
    } else {
        $message = "Registrasi gagal";
        echo "<script> alert ('$message');</script>";
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/auth.css">
    <title>Registrasi</title>
</head>

<body>
    <div class="wrapper">
        <div class="logo">
            <img src="https://www.freepnglogos.com/uploads/stitch-png/dark-lilo-stitch-character-png-transparent-0.png" alt="">
        </div>

        <!-- Pesan Kesalahan atau Pesan Berhasil -->
        <?php if ($error != "") : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($message != "") : ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="text-center mt-4 name">
            Register
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
            <div class="form-field d-flex align-items-center">
                <span class="fas fa-key"></span>
                <input type="password" name="confirmPassword" id="confirmPwd" placeholder="Confirm Password">
            </div>
            <button type="submit" class="btn mt-3" name="register">Register</button>
        </form>
    </div>

    <?php
    if (isset($_POST['register'])) {
        $username = $_POST['userName'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        // Periksa apakah password dan konfirmasi password cocok
        if ($password != $confirmPassword) {
            $error = "Password dan konfirmasi password tidak cocok";
            echo "<script>alert ('$error');</script>";
        } else {
            // Panggil fungsi Register atau lakukan operasi penyimpanan data lainnya
            Register($_POST, $mysql);
        }
    }
    ?>

</body>

</html>