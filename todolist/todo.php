<?php
session_start();
require "../database/conf.php";
require_once __DIR__ . '/../vendor/autoload.php'; // Ubah path ini sesuai dengan struktur direktori Anda
use Dotenv\Dotenv;

$loggedInUserId = $_SESSION["id"];



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

// buat session untuk menyimpan data inputan user
if (!isset($_SESSION['input-data'])) {
    $_SESSION['input-data'] = array();
}

// tambahkan data inputan user ke dalam session dan database
if (isset($_POST['submit-btn'])) {
    $input_text = $_POST['input-text'];
    $status = 'aktif';



    // simpan ke database
    $sql = "INSERT INTO todolist (user_id, kegiatan, status) VALUES ('$loggedInUserId', '$input_text', '$status')";
    if (mysqli_query($mysql, $sql)) {
        array_push($_SESSION['input-data'], $input_text);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($mysql);
    }
}

// hapus data inputan user dari session dan database
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    $id = $index + 1;

    // hapus dari database
    $sql = "DELETE FROM todolist WHERE id = $id AND user_id = $loggedInUserId";
    if (mysqli_query($mysql, $sql)) {
        unset($_SESSION['input-data'][$index]);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($mysql);
    }
}
// selesaikan session dan redirect ke halaman login
if (isset($_POST['selesai-btn'])) {
    session_destroy();
    header("Location: login/login.php");
    exit();
}

// tandai kegiatan sebagai selesai dan update database
if (isset($_GET['complete'])) {
    $index = $_GET['complete'];
    $id = $index + 1;

    // update database
    $sql = "UPDATE todolist SET status = 'selesai' WHERE id = $id AND user_id = $loggedInUserId";
    if (mysqli_query($mysql, $sql)) {
        // tidak perlu mengubah nilai $_SESSION['input-data'] karena halaman akan direfresh dan data akan didapatkan dari database lagi
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($mysql);
    }
}
// // tampilkan semua data inputan user dari database
// $sql = "SELECT * FROM todolist WHERE user_id = $loggedInUserId";
// $result = mysqli_query($mysql, $sql);

// if ($result) {
//     if (mysqli_num_rows($result) > 0) {
//         echo "<div style='text-align: center'>
//     <table class='table table-striped table-container' style='width: 58%; margin: 0 auto;'>
//       <thead>
//         <tr>
//           <th>No</th>
//           <th>Kegiatan</th>
//           <th>Status</th>
//           <th>Aksi</th>
//         </tr>
//       </thead>
//       <tbody>";

//         $no = 1;
//         while ($row = mysqli_fetch_assoc($result)) {
//             $status = $row['status'] == 'selesai' ? 'Selesai' : 'Aktif';
//             $kegiatanClass = $row['status'] == 'selesai' ? 'completed' : '';
//             echo "<tr>
//                 <td>$no</td>
//                 <td class='$kegiatanClass'>" . $row['kegiatan'] . "</td>
//                 <td>$status</td>
//                 <td>
//                   <a href='?delete=" . ($row['id'] - 1) . "' class='btn btn-outline-danger mr-2'>Delete</a>
//                   <a href='?complete=" . ($row['id'] - 1) . "' class='btn btn-outline-success mr-2'>$status</a>
//                 </td>
//               </tr>";
//             $no++;
//         }

//         echo " </tbody>
//     </table>
//   </div>";
//     }
// } else {
//     echo "Error: " . $sql . "<br>" . mysqli_error($mysql);
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <title>Todo List</title>
    <style>

        .form-container {
            background-color: #8294C4;
            padding: 20px;
            border-radius: 5px;
        }

        .table-container {
            background-color: #8294C4;
            padding: 20px;
            border-radius: 5px;
        }

        .completed {
            text-decoration: line-through;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Todo List</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../Auth/Logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <form method="POST" action="">
            <div class="container form-container">
                <h1 class="text-center mt-5">Todo List</h1>
                <div class="my-5">
                    <div class="form-group">
                        <label for="input-text">Nama Kegiatan</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="input-text" id="input-text">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-success" name="submit-btn">Tambah</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger" name="selesai-btn">Selesai</button>
                </div>
            </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>
