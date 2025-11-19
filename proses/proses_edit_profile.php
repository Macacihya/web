<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['user_id'];
$role = $_SESSION['user_role'] ?? 'peserta'; // Default ke peserta jika tidak ada

// Tentukan redirect path berdasarkan role
$redirect_profile = ($role === 'admin') ? '../admin/profile.php' : '../peserta/profile_peserta.php';
$redirect_edit = ($role === 'admin') ? '../admin/edit_profile_admin.php' : '../peserta/edit_profile_peserta.php';

$nama = trim($_POST['nama']);
$password_baru = $_POST['password_baru'] ?? '';
$konfirmasi = $_POST['password_konfirmasi'] ?? '';
$foto_baru = null;

// Validasi password baru
if (!empty($password_baru)) {
    if ($password_baru !== $konfirmasi) {
        $_SESSION['error_message'] = "Konfirmasi password tidak cocok.";
        header("Location: $redirect_edit");
        exit;
    }
}

// Upload Foto
if (!empty($_FILES['foto']['name'])) {
    $file = $_FILES['foto'];

    // Validasi tipe file (opsional tapi disarankan)
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error_message'] = "Format file tidak didukung (hanya JPG, PNG, GIF).";
        header("Location: $redirect_edit");
        exit;
    }

    // Validasi ukuran (misal max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        $_SESSION['error_message'] = "Ukuran file terlalu besar (maks 2MB).";
        header("Location: $redirect_edit");
        exit;
    }

    $namaFile = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", basename($file['name']));
    $target_dir = __DIR__ . '/../file/';

    if (move_uploaded_file($file['tmp_name'], $target_dir . $namaFile)) {
        $foto_baru = $namaFile;

        // Ambil foto lama untuk dihapus (opsional, biar bersih)
        $stmt_old = $conn->prepare("SELECT foto FROM users WHERE id=?");
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $res_old = $stmt_old->get_result();
        if ($row_old = $res_old->fetch_assoc()) {
            $old_photo = $row_old['foto'];
            if ($old_photo && $old_photo !== 'user.jpg' && file_exists($target_dir . $old_photo)) {
                unlink($target_dir . $old_photo);
            }
        }
        $stmt_old->close();

        // update foto di database
        $stmt = $conn->prepare("UPDATE users SET foto=? WHERE id=?");
        $stmt->bind_param("si", $foto_baru, $id);
        $stmt->execute();

        $_SESSION['user_foto'] = $foto_baru; // sinkron session
    } else {
        $_SESSION['error_message'] = "Gagal mengupload foto.";
        header("Location: $redirect_edit");
        exit;
    }
}

// Update nama
if (!empty($nama)) {
    $stmt = $conn->prepare("UPDATE users SET nama=? WHERE id=?");
    $stmt->bind_param("si", $nama, $id);
    $stmt->execute();
    $_SESSION['user_name'] = $nama;
}

// Update password baru
if (!empty($password_baru)) {
    $hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $hash, $id);
    $stmt->execute();
}

$_SESSION['success_message'] = "Profil berhasil diperbarui!";
header("Location: $redirect_profile");
exit;
?>