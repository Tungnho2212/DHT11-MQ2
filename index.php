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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giám sát nhiệt độ, độ ẩm, khí gas</title>
    <style>
      body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('https://variety.com/wp-content/uploads/2019/11/weathering-with-you.jpg?w=1000&h=563&crop=1');
    background-size: cover; /* Làm cho ảnh phủ đầy trang mà không bị bóp méo */
    background-position: center; /* Căn giữa ảnh */
    background-repeat: no-repeat; /* Ngăn ngừa ảnh lặp lại */
    background-attachment: fixed; /* Giữ ảnh nền cố định khi cuộn trang */
}

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #2ecc71; /* Màu xanh lá cây */
            font-size: 36px;
            font-weight: bold;
        }
       table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: rgba(255, 255, 255, 0.7); /* Nền màu trắng nhạt với độ trong suốt */
    color: #fff;
}

th, td {
    text-align: center;
    padding: 12px 15px;
    border: 1px solid #444;
}

th {
    background: #444;
    color: #ffd700;
}

tr:nth-child(even) {
    background: #555;
}

tr:hover {
    background: #444;
}

.scrollable-table {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #555;
}

        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background: #ffd700;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ffa500;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px; /* Cách đều các nút */
            margin: 20px;
        }
        .delete-button, .fan-button, .led-button, .door-button {
            padding: 10px 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 10px; /* Bo góc */
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .fan-button {
            background-color: #3498db;
        }
		.led-button {
            background-color: #3498db;
        }
		.door-button {
            background-color: #3498db;
        }
        .delete-button:hover {
            background-color: #c0392b;
        }
        .fan-button:hover {
            background-color: #2980b9;
        }
		
        #clock {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 18px;
            color: #ffd700;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 5px 10px;
            border-radius: 5px;
        }
		#weather {
    position: absolute;
    top: 10px; /* Cách mép trên 10px */
    right: 10px; /* Cách mép phải 10px */
    background-color: rgba(0, 0, 0, 0.6); /* Nền trong suốt */
    color: #00CED1; /* Màu chữ vàng */
    font-size: 18px;
    padding: 10px;
    border: 2px solid #2ecc71; /* Border xanh lá cây */
    border-radius: 8px; /* Bo góc cho border */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); /* Thêm bóng đổ */
}

    </style>
</head>
<body>
    <!-- Đồng hồ thời gian thực -->
    <div id="clock"></div>
     <div id="weather"></div>
    <div class="container">
        <h1>Giám sát nhiệt độ, độ ẩm, khí gas</h1>

        <!-- Button để xóa dữ liệu và bật/tắt quạt -->
        <div class="button-container">
            <form action="delete_data.php" method="post">
                <button type="submit" class="delete-button">Xóa dữ liệu</button>
            </form>
            <button id="fanButton" class="fan-button">Bật quạt</button>
			<button id="ledButton" class="led-button">Bật đèn LED</button>
			<button id="toggleDoor" class="door-button">Mở cửa</button>
        </div>

        <div class="scrollable-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Temperature (°C)</th>
                        <th>Humidity (%)</th>
                        <th>MQ2 Value</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
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
                    ?>
                </tbody>
            </table>
        </div>
    </div>
<audio id="backgroundMusic" src="thienlyoi.mp3"></audio>
<script>
    // Hiển thị thời gian thực
    function updateClock() {
    const now = new Date();

    // Lấy giờ, phút, giây
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');

    // Lấy ngày, tháng, năm
    const day = now.getDate().toString().padStart(2, '0');
    const month = (now.getMonth() + 1).toString().padStart(2, '0'); // Tháng bắt đầu từ 0
    const year = now.getFullYear();

    // Cập nhật nội dung đồng hồ
    document.getElementById('clock').textContent = 
        `Date: ${day}/${month}/${year} - Time: ${hours}:${minutes}:${seconds}`;
}

// Gọi hàm mỗi giây
setInterval(updateClock, 1000);
updateClock();


    // Logic bật/tắt quạt
    const fanButton = document.getElementById('fanButton');
    let fanStatus = false; // false: OFF, true: ON

    fanButton.addEventListener('click', () => {
        fanStatus = !fanStatus;
        fanButton.textContent = fanStatus ? 'Tắt quạt' : 'Bật quạt';
        fanButton.style.backgroundColor = fanStatus ? '#27ae60' : '#3498db';

        // Gửi trạng thái quạt đến server
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "fan_control.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(`fan_status=${fanStatus ? 'ON' : 'OFF'}`);
        xhr.onload = () => {
            if (xhr.status === 200) {
                console.log(`Server response: ${xhr.responseText}`);
            } else {
                console.error("Error updating fan status on the server.");
            }
        };
    });
	const ledButton = document.getElementById('ledButton');
let ledStatus = false;

ledButton.addEventListener('click', () => {
    ledStatus = !ledStatus;
    ledButton.textContent = ledStatus ? 'Tắt đèn LED' : 'Bật đèn LED';
    ledButton.style.backgroundColor = ledStatus ? '#27ae60' : '#3498db';
    // Gửi trạng thái LED đến server
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "led_control.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(`led_status=${ledStatus ? 'ON' : 'OFF'}`);
});
 const toggleDoorButton = document.getElementById('toggleDoor');
    let doorState = "CLOSE"; // Trạng thái ban đầu là đóng cửa

toggleDoorButton.addEventListener('click', () => {
    // Đổi trạng thái cửa
    const action = doorState === "CLOSE" ? "OPEN" : "CLOSE";

    // Gửi trạng thái mới lên server
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "door_control.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("door_action=" + action);

    xhr.onload = () => {
        if (xhr.status === 200) {
            console.log("Server response:", xhr.responseText);

            // Cập nhật trạng thái cửa
            doorState = action;

            // Cập nhật trạng thái nút bấm và màu nền
            toggleDoorButton.textContent = doorState === "CLOSE" ? "Mở cửa" : "Đóng cửa";
            toggleDoorButton.style.backgroundColor = doorState === "CLOSE" ? '#3498db' : '#27ae60';
        } else {
            console.error("Error controlling door.");
        }
    };
});

function updateTable() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "update_table.php", true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Thay đổi nội dung bảng
                document.querySelector('tbody').innerHTML = xhr.responseText;
            } else {
                console.error('Error updating table:', xhr.status);
            }
        };
        xhr.send();
    }
    setInterval(updateTable, 1000); // Gọi hàm updateTable mỗi 3 giây
	    function getWeather() {
        const apiKey = '7ef34e9970e0f6b127cc45c61f9221b8'; // Thay 'YOUR_API_KEY' bằng API key của bạn
        const city = 'Jakarta'; // Tên thành phố bạn muốn lấy thông tin thời tiết
        const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric&lang=vi`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const weatherDescription = data.weather[0].description;
                const temperature = data.main.temp;
                const humidity = data.main.humidity;

                const weatherInfo = `
                    <p>Thành phố: ${city}</p>
                    <p>Thời tiết: ${weatherDescription}</p>
                    <p>Nhiệt độ: ${temperature}°C</p>
                    <p>Độ ẩm: ${humidity}%</p>
                `;
                document.getElementById('weather').innerHTML = weatherInfo;
            })
            .catch(error => {
                console.error('Error fetching weather data:', error);
                document.getElementById('weather').innerHTML = 'Không thể lấy thông tin thời tiết.';
            });
    }

    // Gọi hàm getWeather khi trang tải
    window.onload = function() {
        getWeather();
    };


</script>

</body>
</html>
