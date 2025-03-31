<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SensorData";

// Kết nối MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý yêu cầu POST để lưu trạng thái LED
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['led_status'])) {
    $ledStatus = $_POST['led_status']; // "ON" hoặc "OFF"

    // Lưu trạng thái LED vào bảng led_status
    $sql = "INSERT INTO led_status (status, timestamp) VALUES ('$ledStatus', NOW())";
    if ($conn->query($sql) === TRUE) {
        echo "LED status updated to: $ledStatus";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Xử lý yêu cầu GET để trả về trạng thái LED mới nhất
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT status FROM led_status ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['status']; // Trả về trạng thái "ON" hoặc "OFF"
    } else {
        echo "OFF"; // Mặc định trả về "OFF" nếu không có dữ liệu
    }
}

$conn->close();
?>
