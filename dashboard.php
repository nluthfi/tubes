<?php 
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login/login.php");
    exit;
}

include 'layout/header.php';
include 'layout/sidebar.php';
include 'koneksi.php';

// Total Mitra
$total_mitra = mysqli_fetch_assoc(
    mysqli_query($koneksi, "
        SELECT COUNT(*) AS total_mitra 
        FROM mitra
    ")
);

// Total Toko
$total_toko = mysqli_fetch_assoc(
    mysqli_query($koneksi, "
        SELECT COUNT(*) AS total_toko 
        FROM toko
    ")
);

// Total Menu
$total_menu = mysqli_fetch_assoc(
    mysqli_query($koneksi, "
        SELECT COUNT(*) AS total_menu 
        FROM menu
    ")
);

// Total Pembayaran
$total_pembayaran = mysqli_fetch_assoc(
    mysqli_query($koneksi, "
        SELECT COUNT(*) AS total_pembayaran 
        FROM bayar
    ")
);
?>

<!-- Main Content -->
<main class="main-content">

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Dashboard
        </h2>

        <p class="text-muted">
            Selamat datang di dashboard admin Street Food.
        </p>

    </div>

    <!-- Statistik -->
    <div class="row g-4">

        <!-- TOTAL MITRA -->
        <div class="col-md-3 col-sm-6">

            <div class="dashboard-card">

                <div>
                    <h6>Total Mitra</h6>

                    <h3>
                        <?= $total_mitra['total_mitra']; ?>
                    </h3>
                </div>

                <div class="card-icon bg-primary">
                    <i class="fa-solid fa-building-columns"></i>
                </div>

            </div>

        </div>

        <!-- TOTAL TOKO -->
        <div class="col-md-3 col-sm-6">

            <div class="dashboard-card">

                <div>
                    <h6>Total Toko</h6>

                    <h3>
                        <?= $total_toko['total_toko']; ?>
                    </h3>
                </div>

                <div class="card-icon bg-success">
                    <i class="fa-solid fa-store"></i>
                </div>

            </div>

        </div>

        <!-- TOTAL MENU -->
        <div class="col-md-3 col-sm-6">

            <div class="dashboard-card">

                <div>
                    <h6>Total Menu</h6>

                    <h3>
                        <?= $total_menu['total_menu']; ?>
                    </h3>
                </div>

                <div class="card-icon bg-warning">
                    <i class="fa-solid fa-utensils"></i>
                </div>

            </div>

        </div>

        <!-- TOTAL PEMBAYARAN -->
        <div class="col-md-3 col-sm-6">

            <div class="dashboard-card">

                <div>
                    <h6>Pembayaran</h6>

                    <h3>
                        <?= $total_pembayaran['total_pembayaran']; ?>
                    </h3>
                </div>

                <div class="card-icon bg-danger">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>

            </div>

        </div>

    </div>

</main>

<?php include 'layout/footer.php'; ?>
