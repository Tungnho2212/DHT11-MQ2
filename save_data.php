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

if (isset($_GET['temperature']) && isset($_GET['humidity']) && isset($_GET['mq2'])) {
    $temperature = $_GET['temperature'];
    $humidity = $_GET['humidity'];
    $mq2 = $_GET['mq2'];

    $sql = "INSERT INTO data_log (temperature, humidity, mq2) VALUES ('$temperature', '$humidity', '$mq2')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Invalid data";
}

$conn->close();
?>
