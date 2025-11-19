<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Atur header JSON
header('Content-Type: application/json');

// 1. CEK LOGIN & ROLE
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit;
}

// 2. AMBIL DATA JSON
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int) $input['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

try {
    // 3. HAPUS FILE LAMPIRAN DULU
    $stmt = $conn->prepare("SELECT Lampiran FROM tambah_notulen WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if ($row && !empty($row['Lampiran'])) {
        $file = __DIR__ . '/../file/' . $row['Lampiran'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // 4. HAPUS DATA DARI DATABASE
    $stmt = $conn->prepare("DELETE FROM tambah_notulen WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Notulen berhasil dihapus.']);
    } else {
        throw new Exception("Gagal hapus DB: " . $stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan server.']);
}

$conn->close();
?>