<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Atur header sebagai JSON
header('Content-Type: application/json');

// 1. PERIKSA APAKAH ADMIN SUDAH LOGIN (PENTING!)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak. Anda harus login sebagai admin.'
    ]);
    exit;
}

// 2. PERIKSA METODE REQUEST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metode tidak diizinkan.'
    ]);
    exit;
}

// 3. AMBIL DATA JSON DARI BODY REQUEST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Cek jika JSON invalid atau ID tidak ada
if (json_last_error() !== JSON_ERROR_NONE || empty($data['id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID pengguna tidak valid.'
    ]);
    exit;
}

$id_to_delete = $data['id'];

// 4. PENCEGAHAN KRUSIAL: JANGAN BIARKAN ADMIN MENGHAPUS DIRINYA SENDIRI
$current_admin_id = $_SESSION['user_id'];
if ($id_to_delete == $current_admin_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'
    ]);
    exit;
}

// 5. EKSEKUSI PENGHAPUSAN
try {
    // A. AMBIL INFO FOTO DULU (Sebelum delete)
    // Kita perlu tahu nama filenya untuk dihapus dari folder
    $sql_info = "SELECT foto FROM users WHERE id = ?";
    $stmt_info = $conn->prepare($sql_info);
    $stmt_info->bind_param("i", $id_to_delete);
    $stmt_info->execute();
    $result_info = $stmt_info->get_result();

    if ($result_info->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Pengguna tidak ditemukan.'
        ]);
        exit;
    }

    $user_data = $result_info->fetch_assoc();
    $foto_file = $user_data['foto'];
    $stmt_info->close();

    // B. DELETE DARI DATABASE
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Gagal mempersiapkan statement: " . $conn->error);
    }

    $stmt->bind_param("i", $id_to_delete);

    if ($stmt->execute()) {
        // C. HAPUS FILE FOTO (Jika bukan default dan file ada)
        // Path relatif ke folder file dari folder proses
        $path_foto = __DIR__ . '/../file/' . $foto_file;

        // Cek apakah file ada, bukan default, dan bisa dihapus
        if ($foto_file && $foto_file !== 'user.jpg' && file_exists($path_foto)) {
            unlink($path_foto); // Hapus file
        }

        echo json_encode([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus.'
        ]);
    } else {
        throw new Exception("Gagal mengeksekusi penghapusan: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log($e->getMessage()); // Catat error log server
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan pada server. Gagal menghapus pengguna.'
    ]);
}
?>