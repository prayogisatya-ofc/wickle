<?php

include "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data terbaru
$latestQuery = "SELECT * FROM sensor ORDER BY created_at DESC LIMIT 1";
$latestResult = mysqli_query($conn, $latestQuery);
$latest = mysqli_fetch_assoc($latestResult);

// Ambil data 5 jam terakhir
$chartQuery = "SELECT * FROM sensor WHERE created_at >= NOW() - INTERVAL 5 HOUR ORDER BY created_at ASC";
$chartResult = mysqli_query($conn, $chartQuery);

$labels = [];
$tdsData = [];
$tempData = [];
while ($row = mysqli_fetch_assoc($chartResult)) {
    $labels[] = date('H:i', strtotime($row['created_at']));
    $tdsData[] = $row['tds_value'];
    $tempData[] = $row['temperature_value'];
}

// Ambil data profil
$user_id = $_SESSION['user_id'];
$profilQuery = "SELECT name, username from users WHERE id = $user_id";
$profilResult = mysqli_query($conn, $profilQuery);
$myProfil = mysqli_fetch_assoc($profilResult);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wickle</title>
    <link rel="shortcut icon" type="image/png" href="../public/assets/images/logos/favicon.ico" />
    <link rel="stylesheet" href="../public/assets/css/styles.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        .text-hijau {
            color: #579665 !important;
        }
        .bg-hijau {
            background-color: #579665 !important;
        }
    </style>
</head>

<body class="bg-light" style="font-family: 'Poppins', sans-serif;">
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!--  Main wrapper -->
        <div class="body-wrapper m-0">
            <!--  Header Start -->
            <header class="app-header w-100 bg-transparent pt-4 top-0 position-sticky" style="padding-left: 20px; padding-right: 20px">
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <img src="../public/assets/images/logos/logo_wickle.png" width="180" alt="" class="nav-link" />
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['user_name'] ?>&background=579665&color=fff" alt="" width="35" height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#profileModal" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="../process/logout.php" role="button" onclick="return confirm('Apakah anda yakin ingin logout?')" class="btn btn-outline-success mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="container-fluid pt-4" style="max-width: none;">
                <!--  Row 1 -->
                <div class="row">
                    <div class="col-lg-8 d-flex align-items-strech">
                        <div class="card w-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                                    <div class="mb-3 mb-sm-0">
                                        <h5 class="card-title fw-semibold">Grafik TDS (5 Jam Terakhir)</h5>
                                    </div>
                                </div>
                                <div id="chartTds"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- Yearly Breakup -->
                                <div class="card overflow-hidden shadow-sm bg-hijau">
                                    <div class="card-body">
                                        <h5 class="card-title mb-9 fw-semibold text-white">Kepekaan Nutrisi (TDS)</h5>
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h1 class="fw-semibold mb-3 text-white"><?= $latest['tds_value'] ?? 'N/A' ?> ppm</h1>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex justify-content-end">
                                                    <div
                                                        class="text-hijau rounded-circle p-6 d-flex align-items-center justify-content-center bg-white">
                                                        <i class="ti ti-plant" style="font-size: 2.25rem !important;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tds"></div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <!-- Monthly Earnings -->
                                <div class="card shadow-sm bg-secondary">
                                    <div class="card-body">
                                        <div class="row alig n-items-start">
                                            <div class="col-8">
                                                <h5 class="card-title mb-9 fw-semibold text-white">Suhu Air</h5>
                                                <h1 class="fw-semibold mb-3 text-white"><?= $latest['temperature_value'] ?? 'N/A' ?>°C</h1>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex justify-content-end">
                                                    <div
                                                        class="text-secondary bg-white rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-droplet" style="font-size: 2.25rem !important;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="suhu"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-9 d-flex align-items-strech">
                        <div class="card w-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                                    <div class="mb-3 mb-sm-0">
                                        <h5 class="card-title fw-semibold">Grafik Suhu (5 Jam Terakhir)</h5>
                                    </div>
                                </div>
                                <div id="chartSuhu"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 d-flex align-items-stretch">
                        <div class="card w-100">
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <h5 class="card-title fw-semibold">Riwayat Sensor (5 Jam Terakhir)</h5>
                                </div>
                                <ul class="timeline-widget mb-0 position-relative">
                                    <?php 
                                        $riwayatResult = mysqli_query($conn, "SELECT * FROM sensor WHERE created_at >= NOW() - INTERVAL 5 HOUR ORDER BY created_at DESC");
                                        while ($riwayat = mysqli_fetch_assoc($riwayatResult)) { 
                                    ?>
                                        <li class="timeline-item d-flex position-relative overflow-hidden">
                                            <div class="timeline-time text-dark flex-shrink-0 text-end"><?= date('H:i', strtotime($riwayat['created_at'])) ?></div>
                                            <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                                <span class="timeline-badge border-2 border flex-shrink-0 my-8" style="border-color: #579665 !important"></span>
                                                <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                            </div>
                                            <div class="timeline-desc fs-3 text-dark mt-n1">Sensor update data <span class="text-hijau fw-semibold">TDS : <?= $riwayat['tds_value'] ?> ppm</span> dan <span class="text-secondary fw-semibold">Suhu Air : <?= $riwayat['temperature_value'] ?>°C</span></div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #579665; border-top-left-radius: 15px; border-top-right-radius: 15px">
                    <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Profil Saya</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <?php if (isset($_SESSION['flash_error'])) { ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            <?= $_SESSION['flash_error'] ?>
                        </div>
                    <?php unset($_SESSION['flash_error']); } ?>

                    <form method="post" id="profileForm" action="../process/update_profil.php">
                        <div class="form-group mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" value="<?= $myProfil['name'] ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" value="<?= $myProfil['username'] ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control" name="password">
                            <small class="form-helper">Kosongkan jika tidak ingin diganti.</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-muted" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" form="profileForm" class="btn btn-success text-white">Update</button>
                </div>
            </div>
        </div>
    </div>  
    <script src="../public/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../public/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../public/assets/js/app.min.js"></script>
    <script src="../public/assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../public/assets/libs/simplebar/dist/simplebar.js"></script>
    <script>
        const labels = <?= json_encode($labels) ?>;
        const tdsData = <?= json_encode($tdsData) ?>;
        const tempData = <?= json_encode($tempData) ?>;
    </script>
    <script src="../public/assets/js/dashboard.js"></script>
</body>

</html>