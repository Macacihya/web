<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

// Retrieve form fields
$judul = trim($_POST['judul'] ?? '');
$tanggal = $_POST['tanggal'] ?? '';
$isi = $_POST['isi'] ?? '';
// peserta may come as array (peserta[]) or single value
$peserta_ids = $_POST['peserta'] ?? [];

if ($judul === '' || $tanggal === '' || $isi === '') {
    echo json_encode(['success' => false, 'message' => 'Judul, tanggal, isi wajib diisi']);
    exit;
}

// ---------- Handle file upload (optional) ----------
$uploadedFileName = null;
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // Basic validation – you can extend this as needed
    $tmp = $_FILES['file']['tmp_name'];
    $originalName = basename($_FILES['file']['name']);
    // Sanitize filename
    $safeName = time() . '_' . preg_replace('/[^a-z0-9\-_.]/i', '_', $originalName);
    $dest = __DIR__ . '/../file/' . $safeName;
    if (move_uploaded_file($tmp, $dest)) {
        $uploadedFileName = $safeName;
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengunggah file']);
        exit;
    }
}

// ---------- Prepare peserta CSV ----------
$peserta_csv = '';
if (is_array($peserta_ids) && count($peserta_ids) > 0) {
    // Sanitize IDs to integers
    $clean = array_map('intval', $peserta_ids);
    $clean = array_filter($clean, fn($v) => $v > 0);
    $peserta_csv = implode(',', $clean);
} else {
    // No participants selected – fallback to ALL participants
    $stmtAll = $conn->prepare("SELECT id FROM users WHERE role = 'peserta'");
    $stmtAll->execute();
    $resAll = $stmtAll->get_result();
    $allIds = [];
    while ($row = $resAll->fetch_assoc()) {
        $allIds[] = $row['id'];
    }
    $peserta_csv = implode(',', $allIds);
}

// ---------- Insert notulen ----------
$created_by = $_SESSION['user_name'] ?? 'Admin';
$stmt = $conn->prepare("INSERT INTO tambah_notulen (judul_rapat, tanggal_rapat, isi_rapat, Lampiran, peserta, created_by) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssssss', $judul, $tanggal, $isi, $uploadedFileName, $peserta_csv, $created_by);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Notulen berhasil disimpan']);
} else {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $stmt->error]);
}
exit;
?>
