<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

echo "<h2>Debug Dashboard Peserta</h2>";

// Check session
echo "<h3>1. Session Info:</h3>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
echo "User Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "<br>";
echo "User Role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "<br>";

// Check all notulens in database
echo "<h3>2. All Notulens in Database:</h3>";
$sql_all = "SELECT id, judul_rapat, tanggal_rapat, created_by, peserta FROM tambah_notulen ORDER BY id DESC LIMIT 5";
$result_all = $conn->query($sql_all);
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Judul</th><th>Tanggal</th><th>Created By</th><th>Peserta (CSV)</th></tr>";
while ($row = $result_all->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['judul_rapat']) . "</td>";
    echo "<td>" . $row['tanggal_rapat'] . "</td>";
    echo "<td>" . htmlspecialchars($row['created_by'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($row['peserta']) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check filtered notulens for current user
if (isset($_SESSION['user_id'])) {
    echo "<h3>3. Notulens for User ID " . $_SESSION['user_id'] . ":</h3>";
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT id, judul_rapat, tanggal_rapat, created_by, peserta 
            FROM tambah_notulen 
            WHERE FIND_IN_SET(?, peserta) > 0 
            ORDER BY tanggal_rapat DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Query: " . $sql . "<br>";
    echo "User ID parameter: " . $user_id . "<br>";
    echo "Number of results: " . $result->num_rows . "<br><br>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Judul</th><th>Tanggal</th><th>Created By</th><th>Peserta</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['judul_rapat']) . "</td>";
            echo "<td>" . $row['tanggal_rapat'] . "</td>";
            echo "<td>" . htmlspecialchars($row['created_by'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['peserta']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red;'>NO RESULTS FOUND!</p>";
    }
}

// Test FIND_IN_SET manually
echo "<h3>4. Manual FIND_IN_SET Test:</h3>";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $test_sql = "SELECT id, judul_rapat, peserta, FIND_IN_SET($user_id, peserta) as find_result FROM tambah_notulen";
    $test_result = $conn->query($test_sql);
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Judul</th><th>Peserta CSV</th><th>FIND_IN_SET Result</th></tr>";
    while ($row = $test_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['judul_rapat']) . "</td>";
        echo "<td>" . htmlspecialchars($row['peserta']) . "</td>";
        echo "<td>" . $row['find_result'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check all users
echo "<h3>5. All Users (Peserta):</h3>";
$users_sql = "SELECT id, nama, email, role FROM users WHERE role = 'peserta'";
$users_result = $conn->query($users_sql);
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th></tr>";
while ($row = $users_result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
