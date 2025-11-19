<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// hanya POST JSON
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int) $input['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

// (optional) cek hak akses: hanya admin boleh hapus
// if ($_SESSION['user_role'] !== 'admin') { ... }

$stmt = $conn->prepare("SELECT Lampiran FROM tambah_notulen WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if ($row && !empty($row['Lampiran'])) {
    $file = __DIR__ . '/../file/' . $row['Lampiran'];
    if (is_file($file))
        @unlink($file);
}

$stmt = $conn->prepare("DELETE FROM tambah_notulen WHERE id = ?");
$stmt->bind_param("i", $id);
$ok = $stmt->execute();
$stmt->close();

echo json_encode(['success' => (bool) $ok]);
exit;
