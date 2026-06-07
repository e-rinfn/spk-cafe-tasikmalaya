<?php
require_once '../config/database.php';

$sesi_list = query("SELECT * FROM bobot_sesi ORDER BY id_sesi DESC");
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

            <!-- Header -->
            <div class="mb-4">
                <h4><i class="ti ti-history"></i> Riwayat Sesi Perhitungan OCRA</h4>
                <p class="text-muted">Daftar semua sesi perhitungan yang telah dilakukan</p>
            </div>

            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-6 col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="mb-0"><?= mysqli_num_rows($sesi_list) ?></h5>
                            <small>Total Sesi</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="mb-0" id="completedCount">0</h5>
                            <small>Selesai</small>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (mysqli_num_rows($sesi_list) == 0): ?>
                <div class="alert alert-info text-center">
                    <i class="ti ti-info-circle"></i> Belum ada sesi perhitungan.
                    <a href="sesi_baru.php" class="alert-link">Buat sesi baru sekarang</a>
                </div>
            <?php else: ?>
                <!-- Grid Card untuk Sesi -->
                <div class="row g-4" id="sesiContainer">
                    <?php
                    $no = 1;
                    $completed = 0;
                    while ($sesi = fetch_one($sesi_list)):
                        // Hitung jumlah kafe yang dinilai dalam sesi ini
                        $count_result = query("SELECT COUNT(*) as total FROM hasil_ocra WHERE id_sesi = {$sesi['id_sesi']}");
                        $count_data = fetch_one($count_result);
                        $count = $count_data ? $count_data['total'] : 0;

                        // Cek apakah sesi sudah selesai (memiliki hasil)
                        $is_completed = $count > 0;
                        if ($is_completed) $completed++;

                        // Tentukan warna card berdasarkan status
                        $card_class = $is_completed ? 'border-success' : 'border-warning';
                        $status_badge = $is_completed ?
                            '<span class="badge bg-success"><i class="ti ti-check-circle"></i> Selesai</span>' :
                            '<span class="badge bg-warning text-dark"><i class="ti ti-clock"></i> Belum Diproses</span>';
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm hover-card <?= $card_class ?>">
                                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-truncate" style="max-width: 200px;">
                                        <i class="ti ti-file"></i> <?= htmlspecialchars($sesi['nama_sesi']) ?>
                                    </h6>
                                    <?= $status_badge ?>
                                </div>

                                <div class="card-body">
                                    <!-- Tanggal -->
                                    <div class="mb-2">
                                        <i class="ti ti-calendar text-primary"></i>
                                        <span class="small">
                                            <?= date('d-m-Y', strtotime($sesi['tanggal_perhitungan'])) ?>
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            <i class="ti ti-clock"></i> <?= date('H:i:s', strtotime($sesi['tanggal_perhitungan'])) ?>
                                        </small>
                                    </div>

                                    <!-- Pembuat -->
                                    <div class="mb-2">
                                        <i class="ti ti-user text-info"></i>
                                        <span class="small">Dibuat oleh: <strong><?= htmlspecialchars($sesi['created_by']) ?></strong></span>
                                    </div>

                                    <!-- Keterangan -->
                                    <?php if ($sesi['keterangan']): ?>
                                        <div class="mb-2">
                                            <i class="ti ti-notes text-secondary"></i>
                                            <span class="small"><?= htmlspecialchars($sesi['keterangan']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Jumlah Kafe -->
                                    <div class="mt-3 pt-2 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small text-muted">
                                                <i class="ti ti-building-store"></i> Kafe dinilai:
                                            </span>
                                            <span class="badge bg-secondary"><?= $count ?> kafe</span>
                                        </div>

                                        <!-- Progress Bar jika ada hasil -->
                                        <?php if ($is_completed): ?>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-success" style="width: 100%"></div>
                                            </div>
                                        <?php else: ?>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-warning" style="width: 0%"></div>
                                            </div>
                                            <small class="text-muted">Belum ada hasil perhitungan</small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-footer bg-transparent">
                                    <div class="d-flex gap-2">
                                        <a href="hasil_detail.php?id=<?= $sesi['id_sesi'] ?>"
                                            class="btn btn-info btn-sm flex-fill">
                                            <i class="ti ti-eye"></i> Lihat Detail
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    endwhile;
                    ?>
                </div>

                <!-- Tombol Aksi -->
                <div class="mb-4 text-center">
                    <a href="sesi_baru.php" class="btn btn-primary">
                        <i class="ti ti-plus"></i> Buat Sesi Baru
                    </a>
                </div>
                <hr>
                <script>
                    // Update statistik completed
                    document.getElementById('completedCount').innerText = '<?= $completed ?>';
                </script>
            <?php endif; ?>

            <!-- Fitur Search (Opsional) -->
            <?php if (mysqli_num_rows($sesi_list) > 6): ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                            <input type="text" id="searchSesi" class="form-control" placeholder="Cari sesi...">
                            <button class="btn btn-outline-secondary" id="clearSearch"><i class="ti ti-x"></i></button>
                        </div>
                    </div>
                </div>

                <script>
                    // Fitur search untuk sesi
                    const searchInput = document.getElementById('searchSesi');
                    const sesiCards = document.querySelectorAll('#sesiContainer .col-md-6');
                    const clearBtn = document.getElementById('clearSearch');

                    function searchSesi() {
                        const searchTerm = searchInput.value.toLowerCase().trim();
                        let visibleCount = 0;

                        sesiCards.forEach(card => {
                            const title = card.querySelector('.card-header h6')?.innerText.toLowerCase() || '';
                            const creator = card.querySelector('.ti-user + span')?.innerText.toLowerCase() || '';

                            if (searchTerm === '' || title.includes(searchTerm) || creator.includes(searchTerm)) {
                                card.style.display = '';
                                visibleCount++;
                            } else {
                                card.style.display = 'none';
                            }
                        });

                        // Tampilkan pesan jika tidak ada hasil
                        let noResultMsg = document.getElementById('noResultMsg');
                        if (!noResultMsg && visibleCount === 0) {
                            const container = document.getElementById('sesiContainer');
                            const msg = document.createElement('div');
                            msg.id = 'noResultMsg';
                            msg.className = 'col-12 text-center text-muted py-5';
                            msg.innerHTML = '<i class="ti ti-search" style="font-size: 48px;"></i><br>Tidak ada sesi yang ditemukan';
                            container.appendChild(msg);
                        } else if (visibleCount > 0 && noResultMsg) {
                            noResultMsg.remove();
                        }
                    }

                    if (searchInput) {
                        searchInput.addEventListener('input', searchSesi);
                    }
                    if (clearBtn) {
                        clearBtn.addEventListener('click', function() {
                            searchInput.value = '';
                            searchSesi();
                            searchInput.focus();
                        });
                    }
                </script>
            <?php endif; ?>

        </div>
    </div>

    <style>
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .card-footer {
            padding: 0.75rem 1rem;
            background-color: rgba(0, 0, 0, 0.02);
        }

        .border-success {
            border: 1px solid #28a745;
        }

        .border-warning {
            border: 1px solid #ffc107;
        }

        @media (max-width: 768px) {
            .card-header h6 {
                font-size: 0.9rem;
            }

            .badge {
                font-size: 0.7rem;
            }

            .btn-sm {
                padding: 0.4rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        /* Animasi loading untuk card */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #sesiContainer .col-md-6 {
            animation: fadeInUp 0.3s ease forwards;
        }
    </style>

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