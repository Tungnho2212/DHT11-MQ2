<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SensorData";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý yêu cầu POST để thay đổi trạng thái cửa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['door_action'])) {
    $doorAction = $_POST['door_action']; // "OPEN" hoặc "CLOSE"

    $sql = "INSERT INTO door_status (action, timestamp) VALUES ('$doorAction', NOW())";
    if ($conn->query($sql) === TRUE) {
        echo "Door action updated to: $doorAction";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Xử lý yêu cầu GET để trả về trạng thái cửa mới nhất
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT action FROM door_status ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['action']; // Trả về "OPEN" hoặc "CLOSE"
    } else {
        echo "CLOSE"; // Mặc định là đóng
    }
}

$conn->close();
?>
