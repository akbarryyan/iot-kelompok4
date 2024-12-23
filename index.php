<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Lampu Otomatis Berdasarkan Gerak</title> 
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');
        * {
            font-family: 'Poppins';
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "lampu_otomatis";

    // Membuat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Memeriksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    ?>

    <div class="w-4/5 mx-auto mt-8">
        <p class="text-xl font-bold">Dashboard</p>

        <div class="w-full h-20 px-8 py-4 mt-6 bg-white border rounded-lg shadow">
            <p class="text-lg font-bold">Status Lampu</p>
            <?php
            $sqlStatus = "SELECT motion_detected, timestamp FROM motion_data ORDER BY timestamp DESC LIMIT 1";
            $resultStatus = $conn->query($sqlStatus);

            if ($resultStatus->num_rows > 0) {
                $status = $resultStatus->fetch_assoc();
                echo "<p class='text-sm'>Terakhir diperbarui: " . $status['timestamp'] . "</p>";
            } else {
                echo "<p class='text-lg'>Status tidak tersedia.</p>";
            }
            ?>
        </div>
        <div class="w-full px-6 py-4 mt-6 bg-white border rounded-lg shadow">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sqlLog = "SELECT id, motion_detected, timestamp FROM motion_data ORDER BY timestamp DESC";
                    $resultLog = $conn->query($sqlLog);
                    $i = 1;

                    if ($resultLog->num_rows > 0) {
                        while ($row = $resultLog->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $i++ . "</td>
                                    <td>" . $row['motion_detected'] . "</td>
                                    <td>" . $row['timestamp'] . "</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Data tidak ditemukan.</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
