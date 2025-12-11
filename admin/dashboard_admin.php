<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Pastikan login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data user login
$userId = (int) $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nama, foto FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userRes = $stmt->get_result();
$userData = $userRes->fetch_assoc();
$stmt->close();
$userName = $userData['nama'] ?? 'Admin';
$userPhoto = $userData['foto'] ?? null;

// Initialize viewed notulen session if not exists
if (!isset($_SESSION['viewed_notulen'])) {
    $_SESSION['viewed_notulen'] = [];
}

// Ambil data untuk highlight cards
// 1. Total Peserta
$sqlPeserta = "SELECT COUNT(*) as total FROM users WHERE role = 'peserta'";
$resPeserta = $conn->query($sqlPeserta);
$totalPeserta = $resPeserta ? $resPeserta->fetch_assoc()['total'] : 0;
                        showToast(json.message || 'Gagal menghapus notulen.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    showToast('Terjadi kesalahan saat menghapus.', 'error');
                }
            });

            function setupLogoutButtons() {
                if (logoutBtn) {
                    logoutBtn.addEventListener("click", async function (e) {
                        e.preventDefault();
                        const confirmed = await showConfirm("Apakah kamu yakin ingin logout?");
                        if (confirmed) {
                            localStorage.removeItem("adminData");
                            window.location.href = "../proses/proses_logout.php";
                        }
                    });
                }
                if (logoutBtnMobile) {
                    logoutBtnMobile.addEventListener("click", async function (e) {
                        e.preventDefault();
                        const confirmed = await showConfirm("Apakah kamu yakin ingin logout?");
                        if (confirmed) {
                            localStorage.removeItem("adminData");
                            window.location.href = "../proses/proses_logout.php";
                        }
                    });
                }
            }

            populateFilterPembuat();
            updateTable();

            // Re-render table when viewport changes (debounced)
            window.addEventListener('resize', function () {
                if (window._dashResizeTimer) clearTimeout(window._dashResizeTimer);
                window._dashResizeTimer = setTimeout(() => {
                    updateTable();
                }, 120);
            });

            setupLogoutButtons();

            // Handle highlight card clicks to mark as viewed
            document.querySelectorAll('.highlight-card').forEach(card => {
                const link = card.closest('a');
                if (link) {
                    link.addEventListener('click', function(e) {
                        const href = this.getAttribute('href');
                        const urlParams = new URLSearchParams(new URL(href, window.location.origin).search);
                        const id = urlParams.get('id');
                        
                        if (id) {
                            fetch('../proses/proses_mark_viewed.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id: id })
                            }).catch(err => console.error('Error marking as viewed:', err));
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>