<?php
$servername = "localhost";
$username = "root";
$password = ""; // Thay đổi nếu bạn có mật khẩu cho MySQL
$dbname = "SensorData"; // Tên cơ sở dữ liệu của bạn

// Kết nối MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xóa toàn bộ dữ liệu và đặt lại ID
$sql = "TRUNCATE TABLE data_log";

if ($conn->query($sql) === TRUE) {
    echo "Dữ liệu đã được xóa và ID đã được đặt lại!";
} else {
    echo "Lỗi khi xóa dữ liệu: " . $conn->error;
}

$conn->close();

// Chuyển hướng trở lại trang index.php
header("Location: index.php");
exit();
?>
