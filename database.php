
<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "sk youth db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>
