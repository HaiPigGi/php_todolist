<?php
session_start();
$_SESSION = [];

session_unset();
session_destroy();

// Hapus cookie
setcookie("username", "", time() - 3600, "/"); // Waktu kedaluwarsa diatur ke masa lalu

$message = "Berhasil Logout";

echo "<script> 
alert ('$message');
window.location.href = './Login.php'; 
</script>";
exit();

?>
