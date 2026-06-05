<?php
require_once '../config/database.php';

// Ambil semua kafe yang aktif
$kafe = query("SELECT * FROM kafe WHERE status = 'aktif' ORDER BY nama_kafe ASC");
?>

<!-- [Head] start -->
<?php require_once 'includes/header.php'; ?>
<!-- [Head] end -->

<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ Sidebar Menu ] start -->
    <?php require_once 'includes/sidebar.php'; ?>
    <!-- [ Sidebar Menu ] end -->

    <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper">
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </header>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">

            <!-- Header & Search -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h4><i class="ti ti-building-store"></i> Daftar Kafe</h4>

                <!-- Search Box -->
                <div class="search-box" style="min-width: 250px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text"
                            id="searchInput"
                            class="form-control"
                            placeholder="Cari kafe... (nama, kecamatan, alamat)"
                            autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <small class="text-muted" id="searchResultCount"></small>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6 col-md-3">
                    <div class="card bg-light text-dark">
                        <div class="card-body text-center">
                            <h5 class="mb-0" id="totalKafeCount">
                                <?= mysqli_num_rows($kafe) ?>
                            </h5>
                            <small>Total Kafe</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card bg-light text-dark">
                        <div class="card-body text-center">
                            <h5 class="mb-0" id="displayCount">
                                <?= mysqli_num_rows($kafe) ?>
                            </h5>
                            <small>Kafe Ditampilkan</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Card Kafe -->
            <div class="row" id="kafeContainer">
                <?php
                if (mysqli_num_rows($kafe) > 0):
                    while ($row = fetch_one($kafe)):
                ?>
                        <div class="col-md-4 col-lg-3 mb-4 kafe-card"
                            data-nama="<?= strtolower(htmlspecialchars($row['nama_kafe'])) ?>"
                            data-kecamatan="<?= strtolower(htmlspecialchars($row['kecamatan'] ?? '')) ?>"
                            data-alamat="<?= strtolower(htmlspecialchars($row['alamat'])) ?>">
                            <div class="card h-100 shadow-sm hover-card">
                                <!-- Gambar Kafe -->
                                <div class="card-img-top-wrapper" style="height: 200px; overflow: hidden;">
                                    <?php if ($row['foto'] && file_exists("../uploads/" . $row['foto'])): ?>
                                        <img src="../uploads/<?= $row['foto'] ?>"
                                            class="card-img-top"
                                            alt="<?= $row['nama_kafe'] ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                            <i class="ti ti-camera-off" style="font-size: 48px; color: #ccc;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($row['nama_kafe']) ?></h5>
                                    <p class="text-muted small mb-2">
                                        <i class="ti ti-map-pin"></i> <?= htmlspecialchars($row['kecamatan'] ?? 'Kecamatan tidak tersedia') ?>
                                    </p>
                                    <p class="small text-secondary mb-2">
                                        <i class="ti ti-clock"></i> <?= date('H:i', strtotime($row['jam_buka'])) ?> - <?= date('H:i', strtotime($row['jam_tutup'])) ?>
                                    </p>
                                    <p class="small text-muted mb-0">
                                        <?= strlen($row['alamat']) > 60 ? substr($row['alamat'], 0, 60) . '...' : $row['alamat'] ?>
                                    </p>
                                </div>

                                <div class="card-footer bg-transparent border-top-0">
                                    <div class="d-flex justify-content-between gap-2">
                                        <a href="kafe_detail.php?id=<?= $row['id_kafe'] ?>" class="btn btn-primary btn-sm flex-fill">
                                            <i class="ti ti-eye"></i> Detail
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    endwhile;
                else:
                    ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="ti ti-alert-triangle"></i> Belum ada data kafe.
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pesan ketika tidak ada hasil -->
            <div id="noResult" class="alert alert-info text-center" style="display: none;">
                <i class="ti ti-search"></i> Tidak ada kafe yang sesuai dengan pencarian.
            </div>

        </div>
    </div>

    <style>
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .card-img-top-wrapper {
            background-color: #f8f9fa;
        }

        .btn-sm {
            font-size: 12px;
            padding: 5px 8px;
        }

        .card-footer {
            padding: 12px 16px 16px 16px;
        }

        .search-box .input-group-text {
            border-right: none;
        }

        .search-box input {
            border-left: none;
        }

        .search-box input:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .search-box input:focus+.btn-outline-secondary {
            border-color: #86b7fe;
        }

        /* Highlight search keyword */
        .highlight {
            background-color: #fff3cd;
            padding: 0 2px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>

    <!-- JavaScript Search -->
    <script>
        // Ambil semua elemen card kafe
        const kafeCards = document.querySelectorAll('.kafe-card');
        const searchInput = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearSearch');
        const noResultDiv = document.getElementById('noResult');
        const kafeContainer = document.getElementById('kafeContainer');
        const displayCountSpan = document.getElementById('displayCount');
        const totalKafeCount = parseInt(document.getElementById('totalKafeCount').innerText);

        // Fungsi untuk melakukan pencarian
        function searchKafe() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            // Hilangkan highlight sebelumnya
            removeHighlights();

            if (searchTerm === '') {
                // Tampilkan semua card
                kafeCards.forEach(card => {
                    card.style.display = '';
                    visibleCount++;
                });
                noResultDiv.style.display = 'none';
                updateResultCount(visibleCount);
                return;
            }

            // Filter card berdasarkan search term
            kafeCards.forEach(card => {
                const nama = card.getAttribute('data-nama');
                const kecamatan = card.getAttribute('data-kecamatan');
                const alamat = card.getAttribute('data-alamat');

                // Cek apakah search term cocok dengan nama, kecamatan, atau alamat
                const matchesNama = nama.includes(searchTerm);
                const matchesKecamatan = kecamatan.includes(searchTerm);
                const matchesAlamat = alamat.includes(searchTerm);

                if (matchesNama || matchesKecamatan || matchesAlamat) {
                    card.style.display = '';
                    visibleCount++;

                    // Highlight teks yang cocok di card
                    if (matchesNama) {
                        highlightText(card, 'card-title', searchTerm);
                    }
                    if (matchesKecamatan) {
                        highlightText(card, '.text-muted.small.mb-2', searchTerm);
                    }
                    if (matchesAlamat) {
                        highlightText(card, '.small.text-muted.mb-0', searchTerm);
                    }
                } else {
                    card.style.display = 'none';
                }
            });

            // Tampilkan pesan jika tidak ada hasil
            if (visibleCount === 0) {
                noResultDiv.style.display = 'block';
            } else {
                noResultDiv.style.display = 'none';
            }

            updateResultCount(visibleCount);
        }

        // Fungsi untuk menghilangkan highlight
        function removeHighlights() {
            const highlightedElements = document.querySelectorAll('.highlight');
            highlightedElements.forEach(el => {
                const parent = el.parentNode;
                parent.replaceChild(document.createTextNode(el.textContent), el);
                parent.normalize();
            });
        }

        // Fungsi untuk highlight teks
        function highlightText(card, selector, searchTerm) {
            const element = card.querySelector(selector);
            if (!element) return;

            const text = element.textContent;
            const lowerText = text.toLowerCase();
            const searchLower = searchTerm;

            if (lowerText.includes(searchLower)) {
                const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                const newHtml = text.replace(regex, '<span class="highlight">$1</span>');
                element.innerHTML = newHtml;
            }
        }

        // Update jumlah hasil yang ditampilkan
        function updateResultCount(count) {
            displayCountSpan.innerText = count;

            // Tampilkan informasi jumlah hasil
            const resultInfo = document.getElementById('searchResultCount');
            if (searchInput.value.trim() !== '') {
                resultInfo.innerHTML = `Menampilkan ${count} dari ${totalKafeCount} kafe`;
            } else {
                resultInfo.innerHTML = '';
            }
        }

        // Event listener untuk input search
        searchInput.addEventListener('input', searchKafe);

        // Event listener untuk tombol clear
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchKafe();
            searchInput.focus();
        });

        // Event listener untuk tombol Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchKafe();
            }
        });

        // Inisialisasi
        updateResultCount(kafeCards.length);
    </script>

    <!-- Required Js -->
    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/fonts/custom-font.js"></script>
    <script src="../assets/js/pcoded.js"></script>
    <script src="../assets/js/plugins/feather.min.js"></script>

    <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-1");
        font_change("Public-Sans");
    </script>

</body>

</html>