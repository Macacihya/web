<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid method']);
    exit;
}

$judul = trim($_POST['judul'] ?? '');
$tanggal = $_POST['tanggal'] ?? '';
$isi = $_POST['isi'] ?? '';
$peserta_ids = $_POST['peserta'] ?? []; // array of ids (strings)

if ($judul==='' || $tanggal==='' || $isi==='') {
    echo json_encode(['success'=>false,'message'=>'Judul, tanggal, isi wajib diisi']);
    exit;
}

// tangani file (sama seperti contoh sebelumnya)
$uploadedFileName = null;
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // ... sama validasi ekstensi & ukuran ...
    $tmp = $_FILES['file']['tmp_name'];
    $name = time().'_'.preg_replace('/[^a-z0-9\-_\.]/i','_',basename($_FILES['file']['name']));
    move_uploaded_file($tmp, __DIR__ . '/../file/' . $name);
    $uploadedFileName = $name;
}

// Opsi A: simpan peserta sebagai CSV id di kolom peserta
$peserta_csv = '';
if (is_array($peserta_ids) && count($peserta_ids) > 0) {
    // sanitize to int
    $clean = array_map('intval', $peserta_ids);
    $clean = array_filter($clean, fn($v) => $v>0);
    $peserta_csv = implode(',', $clean);
}

// Insert notulen
$stmt = $conn->prepare("INSERT INTO tambah_notulen (judul_rapat, tanggal_rapat, isi_rapat, Lampiran, peserta) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $judul, $tanggal, $isi, $uploadedFileName, $peserta_csv);
$ok = $stmt->execute();
if (!$ok) {
    echo json_encode(['success'=>false,'message'=>'DB error: '.$stmt->error]);
    exit;
}

// Opsi B (lebih bagus): simpan relasi notulen_peserta(notulen_id, user_id)
// Jika kamu mau, aku bisa kasih contoh membuat tabel notulen_peserta dan insert per id.

echo json_encode(['success'=>true,'message'=>'Notulen berhasil disimpan']);
exit;
