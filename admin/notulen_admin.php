<?php
require_once __DIR__ . '/../koneksi.php';

// Ambil peserta
$users = [];
$q = $conn->prepare("SELECT id, nama, email FROM users WHERE role = 'peserta' ORDER BY nama ASC");
if ($q) {
    $q->execute();
    $res = $q->get_result();
    while ($r = $res->fetch_assoc()) {
        $users[] = $r;
    }
    $q->close();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Notulen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.min.css">

    <script src="https://cdn.tiny.cloud/1/cl3yw8j9ej8nes9mctfudi2r0jysibdrbn3y932667p04jg5/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
    <nav class="navbar navbar-light bg-white sticky-top px-3">
        <button class="btn btn-outline-success d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
            <i class="bi bi-list"></i>
        </button>
    </nav>

    <!-- Sidebar Mobile -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-body p-0">
            <h5 class="fw-bold mb-3 ms-3">Menu</h5>
            <ul class="nav flex-column">
                <li><a class="nav-link" href="dashboard_admin.php"><i class="bi bi-grid me-2"></i>Dashboard</a></li>
                <li><a class="nav-link" href="kelola_rapat_admin.php"><i class="bi bi-people me-2"></i>Kelola Pengguna</a></li>
                <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
            </ul>
            <div class="text-center mt-4">
                <button id="logoutBtnMobile" class="btn logout-btn px-4 py-2">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </div>
        </div>
    </div>

    <!-- Sidebar Desktop -->
    <div class="sidebar-content d-none d-lg-flex flex-column justify-content-between position-fixed">
        <div>
            <h5 class="fw-bold mb-4 ms-3">Menu</h5>
            <ul class="nav flex-column">
                <li><a class="nav-link" href="dashboard_admin.php"><i class="bi bi-grid me-2"></i>Dashboard</a></li>
                <li><a class="nav-link" href="kelola_rapat_admin.php"><i class="bi bi-people me-2"></i>Kelola Pengguna</a></li>
                <li><a class="nav-link" href="profile.php"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
            </ul>
        </div>

        <div class="text-center">
            <button id="logoutBtn" class="btn logout-btn px-4 py-2">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </button>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div></div>
            <div class="profile"><span>Halo, Admin 👋</span></div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard_admin.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Notulen</li>
            </ol>
        </nav>

        <div class="form-section">
            <h5 class="fw-semibold mb-4">Tambah Notulen</h5>

            <div id="alertBox" class="alert alert-success" style="display:none;">Notulen berhasil disimpan!</div>

            <form id="notulenForm" method="POST" enctype="multipart/form-data">
                
                <!-- Judul -->
                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" class="form-control" name="judul" id="judul" placeholder="Masukkan judul rapat" required>
                </div>

                <!-- Tanggal -->
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" id="tanggal" required>
                </div>

                <!-- Isi/TinyMCE -->
                <div class="mb-3">
                    <label class="form-label">Isi Notulen</label>
                    <textarea name="isi" id="isi" rows="10"></textarea>
                </div>

                <!-- Upload file -->
                <div class="mb-3">
                    <label class="form-label">Upload File (Opsional)</label>
                    <input type="file" class="form-control" name="file" id="fileInput">
                </div>

                <!-- PESERTA -->
                <div class="mb-3">
                    <label class="form-label">Peserta Notulen</label>
                    <div class="dropdown w-50">
                        <button class="btn btn-save w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Pilih Peserta
                        </button>

<<<<<<< HEAD
    <!-- Trigger button -->
    <div class="dropdown w-50" data-bs-auto-close="false">
        <button id="dropdownToggle" class="btn btn-save w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown"
            aria-expanded="false">Pilih Peserta</button>
=======
                        <div class="dropdown-menu w-100 p-3" style="max-height:360px; overflow:auto;">
                            <input id="searchInput" type="search" class="form-control mb-2" placeholder="Cari peserta...">
>>>>>>> 58511082e59edfd8e666deedb9cc6095e8ab71f6

                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                                <label class="form-check-label" for="selectAll">Pilih Semua</label>
                            </div>

                            <hr>

                            <div id="notulenList">
                                <?php foreach ($users as $u): ?>
                                <div class="form-check notulen-item py-1">
                                    <input class="form-check-input notulen-checkbox"
                                        type="checkbox"
                                        value="<?= $u['id'] ?>"
                                        data-name="<?= htmlspecialchars($u['nama']) ?>"
                                        id="u<?= $u['id'] ?>">
                                    <label class="form-check-label" for="u<?= $u['id'] ?>">
                                        <?= htmlspecialchars($u['nama']) ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <button type="button" id="clearSearchBtn" class="btn btn-light btn-sm">Reset</button>
                                <button type="button" id="addButton" class="btn btn-success btn-sm">Tambah</button>
                            </div>

                        </div>
                    </div>

<<<<<<< HEAD
            <hr />

            <!-- Actions -->
            <div class="d-flex justify-content-between">
                <button id="clearSearchBtn" type="button" class="btn btn-sm btn-light">Reset</button>
                <button id="addButton" type="button" class="btn btn-sm btn-success">Tambah</button>
            </div>
        </div>
    </div>

                <!-- List peserta (target) -->
                <div id="addedList" class="added-list mt-3">
                    <h6 class="fw-bold mb-2">Peserta yang Telah Ditambahkan:</h6>
                    <div id="addedContainer">
                        <p class="text-muted">Belum ada peserta yang ditambahkan</p>
                    </div>
                </div>
            </div>
=======
                    <!-- LIST PESERTA YANG DIPILIH -->
                    <div id="addedList" class="added-list mt-3">
                        <h6 class="fw-bold mb-2">Peserta yang Ditambahkan:</h6>
                        <div id="addedContainer">
                            <p class="text-muted">Belum ada peserta yang ditambahkan</p>
                        </div>
                    </div>
                </div>
>>>>>>> 58511082e59edfd8e666deedb9cc6095e8ab71f6

                <!-- Submit -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-save px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* =======================
   TINYMCE
======================= */
tinymce.init({
    selector: '#isi',
    height: 350,
    menubar: false,
    plugins: "lists link table code",
    toolbar: "undo redo | bold italic underline | bullist numlist | link",
});

/* =======================
   FORM SUBMIT AJAX
======================= */
document.getElementById("notulenForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const fd = new FormData(this);

    // Ambil peserta
    document.querySelectorAll('.added-item').forEach(item => {
        fd.append("peserta[]", item.dataset.id);
    });

    const res = await fetch("../proses/proses_simpan_notulen.php", {
        method: "POST",
        body: fd
    });

    const json = await res.json();

    if (json.success) {
        const alertBox = document.getElementById("alertBox");
        alertBox.style.display = "block";
        alertBox.textContent = json.message;

        setTimeout(() => {
            alertBox.style.display = "none";
            location.reload();
        }, 1500);
    } else {
        alert(json.message);
    }
});

/* =======================
   PESERTA HANDLING
======================= */
const searchInput = document.getElementById("searchInput");
const selectAll = document.getElementById("selectAll");
const addButton = document.getElementById("addButton");
const clearSearchBtn = document.getElementById("clearSearchBtn");
const addedContainer = document.getElementById("addedContainer");

// Search
searchInput.addEventListener("keyup", () => {
    const val = searchInput.value.toLowerCase();

    document.querySelectorAll(".notulen-item").forEach(item => {
        const name = item.innerText.toLowerCase();
        item.style.display = name.includes(val) ? "" : "none";
    });
});

// Reset search
clearSearchBtn.addEventListener("click", () => {
    searchInput.value = "";
    searchInput.dispatchEvent(new Event("keyup"));
});

<<<<<<< HEAD
        // Logout Desktop
        document.getElementById("logoutBtn").addEventListener("click", function () {
            if (confirm("Apakah kamu yakin ingin logout?")) {
                localStorage.removeItem("userData");
                window.location.href = "../proses/proses_logout.php";
            }
        });

        // Logout Mobile
        const logoutBtnMobile = document.getElementById("logoutBtnMobile");
        if (logoutBtnMobile) {
            logoutBtnMobile.addEventListener("click", function () {
                if (confirm("Apakah kamu yakin ingin logout?")) {
                    localStorage.removeItem("adminData");
                    window.location.href = "../proses/proses_logout.php";
                }
            });
        }
        // ===================
        // Fungsi Dropdown Peserta
        // ===================
        
        // Helper function untuk escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
        
        const searchInput = document.getElementById('searchInput');
        const notulenItems = document.querySelectorAll('#notulenList .form-check');
        const selectAll = document.getElementById('selectAll');
        const addButton = document.getElementById('addButton');
        const addedContainer = document.getElementById('addedContainer');
=======
// Select all
selectAll.addEventListener("change", function () {
    document.querySelectorAll(".notulen-checkbox").forEach(cb => cb.checked = selectAll.checked);
});

// Tambah peserta
addButton.addEventListener("click", () => {
    const selected = document.querySelectorAll(".notulen-checkbox:checked");
>>>>>>> 58511082e59edfd8e666deedb9cc6095e8ab71f6

    if (selected.length === 0) {
        addedContainer.innerHTML = '<p class="text-muted">Belum ada peserta yang ditambahkan</p>';
        return;
    }

    if (addedContainer.querySelector("p")) addedContainer.innerHTML = "";

<<<<<<< HEAD
        // Tambah peserta
        addButton.addEventListener('click', function () {
            const selected = document.querySelectorAll('.notulen-checkbox:checked');
            
            if (selected.length === 0) {
                addedContainer.innerHTML = '<p class="text-muted">Belum ada peserta yang ditambahkan</p>';
                return;
            }

            // Ambil peserta yang sudah ada untuk mencegah duplikat
            const existingIds = new Set();
            addedContainer.querySelectorAll('.added-item').forEach(item => {
                existingIds.add(item.dataset.id);
            });
            
            // Hapus pesan "Belum ada peserta" jika ada
            const emptyMsg = addedContainer.querySelector('.text-muted');
            if (emptyMsg) {
                emptyMsg.remove();
            }

            selected.forEach(cb => {
                const id = cb.value;
                const name = cb.dataset.name || cb.nextElementSibling?.textContent?.trim() || 'Unknown';
                
                // Cek jika id sudah ada untuk mencegah duplikat
                if (existingIds.has(id)) return;

                const div = document.createElement('div');
                div.className = 'added-item d-flex align-items-center gap-2 mb-2';
                div.dataset.id = id;
                div.innerHTML = `
                    <span class="flex-grow-1">${escapeHtml(name)}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-btn">Hapus</button>
                `;
                addedContainer.appendChild(div);
                
                // Attach event listener ke tombol hapus
                const removeBtn = div.querySelector('.remove-btn');
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.parentElement.dataset.id;
                    const cb = document.querySelector(`.notulen-checkbox[value="${id}"]`);
                    if (cb) cb.checked = false;
                    this.parentElement.remove();
                    if (addedContainer.children.length === 0) {
                        addedContainer.innerHTML = '<p class="text-muted">Belum ada peserta yang ditambahkan</p>';
                    }
                });
            });
        });

    </script>
=======
    selected.forEach(cb => {
        const id = cb.value;
        const name = cb.dataset.name;

        if (addedContainer.querySelector(`[data-id="${id}"]`)) return;

        const row = document.createElement("div");
        row.className = "added-item d-flex align-items-center gap-2 mb-2";
        row.dataset.id = id;

        row.innerHTML = `
            <span class="flex-grow-1">${name}</span>
            <button class="btn btn-sm btn-outline-danger remove-btn">Hapus</button>
        `;

        addedContainer.appendChild(row);
    });
});

// Hapus peserta
addedContainer.addEventListener("click", (e) => {
    if (!e.target.classList.contains("remove-btn")) return;

    const row = e.target.closest(".added-item");

    const id = row.dataset.id;
    const cb = document.querySelector(`.notulen-checkbox[value="${id}"]`);
    if (cb) cb.checked = false;

    row.remove();

    if (addedContainer.children.length === 0) {
        addedContainer.innerHTML = '<p class="text-muted">Belum ada peserta yang ditambahkan</p>';
    }
});
</script>

>>>>>>> 58511082e59edfd8e666deedb9cc6095e8ab71f6
</body>
</html>
