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

$sql = "SELECT * FROM data_log ORDER BY timestamp DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['temperature']}</td>
            <td>{$row['humidity']}</td>
            <td>{$row['mq2']}</td>
            <td>{$row['timestamp']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No data available</td></tr>";
}
$conn->close();
?>
