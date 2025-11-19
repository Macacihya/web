<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/dashboard_admin.php");
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$judul = trim($_POST['judul']);
$tanggal = $_POST['tanggal']; // Format YYYY-MM-DD dari input date
$isi = $_POST['isi'];
$peserta_arr = isset($_POST['peserta']) ? $_POST['peserta'] : [];
$peserta_str = implode(', ', $peserta_arr);

if ($id <= 0 || empty($judul) || empty($tanggal) || empty($isi)) {
    echo "<script>alert('Data tidak lengkap!'); window.history.back();</script>";
    exit;
}

// Cek apakah ada upload lampiran baru
$lampiran_baru = null;
if (!empty($_FILES['lampiran']['name'])) {
    $file = $_FILES['lampiran'];
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];

    if (!in_array($file['type'], $allowed_types)) {
        echo "<script>alert('Format file tidak didukung!'); window.history.back();</script>";
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        echo "<script>alert('Ukuran file terlalu besar (Maks 5MB)!'); window.history.back();</script>";
        exit;
    }

    $namaFile = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", basename($file['name']));
    $target_dir = __DIR__ . '/../file/';

    if (move_uploaded_file($file['tmp_name'], $target_dir . $namaFile)) {
        $lampiran_baru = $namaFile;

        // Hapus lampiran lama
        $stmt_old = $conn->prepare("SELECT Lampiran FROM tambah_notulen WHERE id = ?");
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $res_old = $stmt_old->get_result();
        if ($row = $res_old->fetch_assoc()) {
            $old_file = $row['Lampiran'];
            if ($old_file && file_exists($target_dir . $old_file)) {
                unlink($target_dir . $old_file);
            }
        }
        $stmt_old->close();
    } else {
        echo "<script>alert('Gagal upload file!'); window.history.back();</script>";
        exit;
    }
}

// Update Database
if ($lampiran_baru) {
    $sql = "UPDATE tambah_notulen SET judul_rapat=?, tanggal_rapat=?, isi_rapat=?, peserta=?, Lampiran=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $judul, $tanggal, $isi, $peserta_str, $lampiran_baru, $id);
} else {
    $sql = "UPDATE tambah_notulen SET judul_rapat=?, tanggal_rapat=?, isi_rapat=?, peserta=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $judul, $tanggal, $isi, $peserta_str, $id);
}

if ($stmt->execute()) {
    echo "<script>alert('Notulen berhasil diperbarui!'); window.location.href='../admin/dashboard_admin.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui notulen: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>