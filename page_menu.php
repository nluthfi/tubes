<?php
session_start();
include 'koneksi.php';

$id_toko = isset($_GET['id_toko']) ? (int)$_GET['id_toko'] : 0;
if (!$id_toko) { header('Location: index.php'); exit; }

// Fetch toko
$stmt = $koneksi->prepare("SELECT * FROM toko WHERE id_toko = ?");
$stmt->bind_param('i', $id_toko);
$stmt->execute();
$toko = $stmt->get_result()->fetch_assoc();
if (!$toko) { header('Location: index.php'); exit; }

$now_time = date('H:i:s');
$jam_buka  = $toko['jam_buka']  ? substr($toko['jam_buka'], 0, 5)  : null;
$jam_tutup = $toko['jam_tutup'] ? substr($toko['jam_tutup'], 0, 5) : null;
$is_buka   = ($jam_buka && $jam_tutup && $now_time >= $toko['jam_buka'] && $now_time <= $toko['jam_tutup']);

// Fetch mitra
$mitras = [];
$r = $koneksi->query("SELECT m.* FROM toko_mitra tm JOIN mitra m ON m.id_mitra = tm.id_mitra WHERE tm.id_toko = $id_toko");
while ($row = $r->fetch_assoc()) $mitras[] = $row;

// Fetch metode bayar
$bayars = [];
$r = $koneksi->query("SELECT b.* FROM metode_toko mt JOIN bayar b ON b.id_metode = mt.id_metode WHERE mt.id_toko = $id_toko");
while ($row = $r->fetch_assoc()) $bayars[] = $row;

// Fetch menu with filter
$search_menu   = isset($_GET['search_menu']) ? trim($_GET['search_menu']) : '';
$filter_rasa   = isset($_GET['rasa'])        ? $_GET['rasa']             : '';
$filter_kat    = isset($_GET['kategori'])    ? (int)$_GET['kategori']    : 0;
$sort_menu     = isset($_GET['sort'])        ? $_GET['sort']             : '';

$where_m = ["m.id_toko = $id_toko"];
$params_m = []; $types_m = '';

if ($search_menu !== '') {
    $where_m[] = '(m.nama_menu LIKE ? OR m.deskripsi LIKE ?)';
    $params_m[] = "%$search_menu%"; $params_m[] = "%$search_menu%";
    $types_m .= 'ss';
}
if ($filter_rasa !== '') {
    $where_m[] = 'm.rasa = ?';
    $params_m[] = $filter_rasa; $types_m .= 's';
}
if ($filter_kat > 0) {
    $where_m[] = 'm.id_kategori = ?';
    $params_m[] = $filter_kat; $types_m .= 'i';
}

$order_m = match($sort_menu) {
    'harga_asc'  => 'ORDER BY m.harga ASC',
    'harga_desc' => 'ORDER BY m.harga DESC',
    'nama_az'    => 'ORDER BY m.nama_menu ASC',
    default      => 'ORDER BY m.id_menu ASC',
};

$where_sql_m = 'WHERE ' . implode(' AND ', $where_m);
$menu_sql = "SELECT m.*, k.kategori_makanan FROM menu m LEFT JOIN kategori k ON k.id_kategori = m.id_kategori $where_sql_m $order_m";
$stmt_m = $koneksi->prepare($menu_sql);
if ($params_m) $stmt_m->bind_param($types_m, ...$params_m);
$stmt_m->execute();
$menus = $stmt_m->get_result()->fetch_all(MYSQLI_ASSOC);

// Group by kategori
$grouped = [];
foreach ($menus as $menu) {
    $kat = $menu['kategori_makanan'] ?: 'Lainnya';
    $grouped[$kat][] = $menu;
}

// Fetch all kategori for filter
$all_kat = $koneksi->query("SELECT DISTINCT k.id_kategori, k.kategori_makanan FROM menu m JOIN kategori k ON k.id_kategori = m.id_kategori WHERE m.id_toko = $id_toko ORDER BY k.id_kategori");

// Reviews
$reviews = [];
$r = $koneksi->query("SELECT * FROM review WHERE id_toko = $id_toko ORDER BY tanggal_review DESC LIMIT 5");
if ($r) while ($row = $r->fetch_assoc()) $reviews[] = $row;
$rating_text = $toko['rating'] ? number_format($toko['rating'], 1) : '–';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($toko['nama_toko']) ?> – Menu</title>
    <link rel="prekoneksiect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0
    }

    :root {
        --bg: #0f1623;
        --sidebar: #162035;
        --card: #1a2540;
        --card-hover: #1f2d4e;
        --border: #263354;
        --accent: #f0a500;
        --accent2: #e05c2a;
        --green: #22c55e;
        --red: #ef4444;
        --text: #e8edf8;
        --text-muted: #7a8aaa;
        --blue: #3b82f6;
        --radius: 16px;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--bg);
        color: var(--text);
        display: flex;
        min-height: 100vh
    }

    /* SIDEBAR */
    .sidebar {
        width: 240px;
        min-height: 100vh;
        background: var(--sidebar);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 100
    }

    .sidebar-logo {
        padding: 22px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid var(--border)
    }

    .sidebar-logo .icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px
    }

    .sidebar-logo span {
        font-weight: 800;
        font-size: 16px;
        letter-spacing: .5px;
        color: #fff
    }

    .sidebar-logo span small {
        display: block;
        font-size: 10px;
        font-weight: 500;
        color: var(--text-muted);
        letter-spacing: 1px;
        text-transform: uppercase
    }

    .sidebar-nav {
        padding: 16px 12px;
        flex: 1
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 10px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: .18s;
        margin-bottom: 2px
    }

    .sidebar-nav a:hover {
        background: rgba(255, 255, 255, .05);
        color: var(--text)
    }

    .sidebar-nav a.active {
        background: linear-gradient(90deg, rgba(240, 165, 0, .18), rgba(240, 165, 0, .06));
        color: var(--accent);
        font-weight: 700
    }

    .sidebar-nav a i {
        width: 18px;
        text-align: center;
        font-size: 15px
    }

    .sidebar-nav a.active i {
        color: var(--accent)
    }

    .sidebar-bottom {
        padding: 16px;
        border-top: 1px solid var(--border)
    }

    .user-card {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b5bdb, #7048e8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        color: #fff
    }

    .user-info .name {
        font-size: 13px;
        font-weight: 700
    }

    .user-info .role {
        font-size: 11px;
        color: var(--text-muted)
    }

    /* MAIN */
    .main {
        margin-left: 240px;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh
    }

    /* TOPBAR */
    .topbar {
        background: var(--sidebar);
        border-bottom: 1px solid var(--border);
        padding: 0 28px;
        height: 64px;
        display: flex;
        align-items: center;
        gap: 16px;
        position: sticky;
        top: 0;
        z-index: 90
    }

    .topbar .back-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid var(--border);
        transition: .15s;
        background: var(--bg)
    }

    .topbar .back-btn:hover {
        color: var(--text);
        border-color: var(--text-muted)
    }

    .search-box {
        flex: 1;
        max-width: 420px;
        position: relative
    }

    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 14px
    }

    .search-box input {
        width: 100%;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 9px 14px 9px 38px;
        color: var(--text);
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: .18s
    }

    .search-box input:focus {
        border-color: var(--accent)
    }

    .search-box input::placeholder {
        color: var(--text-muted)
    }

    .topbar-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 14px
    }

    .notif-btn {
        width: 38px;
        height: 38px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        color: var(--text-muted);
        font-size: 15px
    }

    .notif-btn .badge {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 8px;
        height: 8px;
        background: var(--accent2);
        border-radius: 50%;
        border: 2px solid var(--sidebar)
    }

    .topbar-user {
        display: flex;
        align-items: center;
        gap: 9px;
        cursor: pointer
    }

    .topbar-user .avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b5bdb, #7048e8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: #fff
    }

    .topbar-user .info .name {
        font-size: 13px;
        font-weight: 700;
        line-height: 1
    }

    .topbar-user .info .role {
        font-size: 11px;
        color: var(--text-muted)
    }

    /* CONTENT */
    .content {
        flex: 1;
        padding: 0
    }

    /* STORE HERO */
    .store-hero {
        background: var(--card);
        border-bottom: 1px solid var(--border);
        padding: 28px 32px;
        display: flex;
        gap: 28px;
        align-items: flex-start
    }

    .store-hero-img {
        width: 200px;
        height: 150px;
        border-radius: var(--radius);
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, #1f2d4e, #263354);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 40px
    }

    .store-hero-img img {
        width: 100%;
        height: 100%;
        object-fit: cover
    }

    .store-hero-info {
        flex: 1
    }

    .store-hero-info h1 {
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap
    }

    .status-pill {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700
    }

    .status-buka {
        background: rgba(34, 197, 94, .15);
        color: var(--green);
        border: 1px solid rgba(34, 197, 94, .35)
    }

    .status-tutup {
        background: rgba(239, 68, 68, .15);
        color: var(--red);
        border: 1px solid rgba(239, 68, 68, .35)
    }

    .halal-tag {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        background: rgba(34, 197, 94, .1);
        color: var(--green);
        border: 1px solid rgba(34, 197, 94, .3)
    }

    .store-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 14px
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--text-muted)
    }

    .meta-item i {
        width: 14px;
        text-align: center
    }

    .meta-item.rating-item {
        color: var(--accent);
        font-weight: 700
    }

    .hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px
    }

    .mitra-badge,
    .bayar-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600
    }

    .mitra-badge {
        background: rgba(59, 130, 246, .1);
        border: 1px solid rgba(59, 130, 246, .25);
        color: var(--blue)
    }

    .mitra-badge img {
        width: 14px;
        height: 14px;
        object-fit: contain;
        border-radius: 2px
    }

    .bayar-badge {
        background: rgba(240, 165, 0, .08);
        border: 1px solid rgba(240, 165, 0, .2);
        color: var(--accent)
    }

    /* MENU SECTION */
    .menu-section {
        padding: 24px 32px
    }

    .menu-filter-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 24px
    }

    .chip {
        padding: 7px 16px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        background: transparent;
        color: var(--text-muted);
        text-decoration: none;
        transition: .15s;
        display: inline-block
    }

    .chip:hover {
        border-color: var(--text-muted);
        color: var(--text)
    }

    .chip.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #0f1623
    }

    .rasa-chip {
        padding: 7px 14px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        background: transparent;
        color: var(--text-muted);
        text-decoration: none;
        transition: .15s
    }

    .rasa-chip:hover,
    .rasa-chip.active {
        background: rgba(240, 165, 0, .12);
        border-color: var(--accent);
        color: var(--accent)
    }

    .sort-wrap select {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 8px 14px;
        color: var(--text);
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        outline: none;
        cursor: pointer;
        margin-left: auto
    }

    /* KATEGORI + MENU */
    .kategori-section {
        margin-bottom: 32px
    }

    .kategori-title {
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px
    }

    .kategori-title::before {
        content: '';
        width: 4px;
        height: 18px;
        background: var(--accent);
        border-radius: 2px;
        display: inline-block
    }

    .menu-list {
        display: flex;
        flex-direction: column;
        gap: 12px
    }

    .menu-item {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px;
        display: flex;
        gap: 16px;
        align-items: flex-start;
        transition: .18s
    }

    .menu-item:hover {
        border-color: rgba(240, 165, 0, .3);
        background: var(--card-hover)
    }

    .menu-item-img {
        width: 120px;
        min-width: 120px;
        height: 90px;
        border-radius: 10px;
        overflow: hidden;
        background: linear-gradient(135deg, #1f2d4e, #263354);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 24px
    }

    .menu-item-img img {
        width: 120px;
        height: 90px;
        object-fit: cover;
        border-radius: 10px
    }

    .menu-item-body {
        flex: 1;
        min-width: 0
    }

    .menu-item-body h4 {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 4px
    }

    .menu-item-body .desc {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden
    }

    .menu-item-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px
    }

    .menu-price {
        font-size: 16px;
        font-weight: 800;
        color: var(--accent)
    }

    .rasa-tag {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(240, 165, 0, .1);
        border: 1px solid rgba(240, 165, 0, .2);
        color: var(--accent)
    }

    .rasa-pedas {
        background: rgba(239, 68, 68, .1);
        border-color: rgba(239, 68, 68, .3);
        color: var(--red)
    }

    .rasa-asam {
        background: rgba(16, 185, 129, .1);
        border-color: rgba(16, 185, 129, .3);
        color: #10b981
    }

    .rasa-manis {
        background: rgba(168, 85, 247, .1);
        border-color: rgba(168, 85, 247, .3);
        color: #a855f7
    }

    .rasa-asin {
        background: rgba(59, 130, 246, .1);
        border-color: rgba(59, 130, 246, .3);
        color: var(--blue)
    }

    .rasa-berkuah {
        background: rgba(6, 182, 212, .1);
        border-color: rgba(6, 182, 212, .3);
        color: #06b6d4
    }

    /* REVIEWS */
    .reviews-section {
        padding: 0 32px 32px
    }

    .reviews-header {
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px
    }

    .reviews-header::before {
        content: '';
        width: 4px;
        height: 18px;
        background: var(--blue);
        border-radius: 2px;
        display: inline-block
    }

    .review-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 10px
    }

    .review-top {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px
    }

    .review-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b5bdb, #7048e8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: #fff
    }

    .review-name {
        font-size: 14px;
        font-weight: 700
    }

    .review-date {
        font-size: 11px;
        color: var(--text-muted);
        margin-left: auto
    }

    .review-stars {
        color: var(--accent);
        font-size: 12px;
        margin-bottom: 4px
    }

    .review-text {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5
    }

    .no-review {
        text-align: center;
        padding: 32px;
        color: var(--text-muted);
        font-size: 14px
    }

    .empty-menu {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted)
    }

    .empty-menu i {
        font-size: 40px;
        margin-bottom: 12px;
        opacity: .4
    }

    .empty-menu h3 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
        color: var(--text)
    }

    @media(max-width:768px) {
        .sidebar {
            transform: translateX(-100%)
        }

        .main {
            margin-left: 0
        }

        .store-hero {
            flex-direction: column
        }

        .store-hero-img {
            width: 100%;
            height: 180px
        }

        .menu-item {
            flex-direction: column
        }

        .menu-item-img {
            width: 100%;
            min-width: 100%;
            height: 160px
        }

        .menu-item-img img {
            width: 100%;
            height: 160px
        }
    }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="icon">🍜</div>
            <span>STREET FOOD<small>Management</small></span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="index.php" class="active"><i class="fas fa-store"></i> Toko</a>
            <a href="menu.php"><i class="fas fa-utensils"></i> Menu</a>
            <a href="mitra.php"><i class="fas fa-handshake"></i> Mitra</a>
            <a href="metode_pembayaran.php"><i class="fas fa-credit-card"></i> Metode Pembayaran</a>
            <a href="kategori_rasa.php"><i class="fas fa-tags"></i> Kategori Rasa</a>
            <a href="bahan_baku.php"><i class="fas fa-box"></i> Bahan Baku</a>
            <a href="laporan.php"><i class="fas fa-chart-bar"></i> Laporan &amp; Statistik</a>
            <a href="info_analisis.php"><i class="fas fa-info-circle"></i> Info &amp; Analisis</a>
            <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-card">
                <div class="user-avatar">A</div>
                <div class="user-info">
                    <div class="name">Admin</div>
                    <div class="role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="main">
        <!-- TOPBAR -->
        <header class="topbar">
            <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchMenuInput" placeholder="Cari menu..."
                    value="<?= htmlspecialchars($search_menu) ?>">
            </div>
            <div class="topbar-right">
                <div class="notif-btn"><i class="fas fa-bell"></i><span class="badge"></span></div>
                <div class="topbar-user">
                    <div class="avatar">A</div>
                    <div class="info">
                        <div class="name">Admin</div>
                        <div class="role">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </header>

        <div class="content">
            <!-- STORE HERO -->
            <div class="store-hero">
                <div class="store-hero-img">
                    <?php if ($toko['foto_outlet']): ?>
                    <img src="../img/pict/<?= $toko['foto_outlet']; ?>"
                        alt="<?= htmlspecialchars($toko['nama_toko']); ?>"
                        style="width:100%;max-height:220px;object-fit:cover;border-radius:<?= var_export(16, true) ?>px">
                    <?php else: ?>
                    <i class="fas fa-store"></i>
                    <?php endif; ?>
                </div>
                <div class="store-hero-info">
                    <h1>
                        <?= htmlspecialchars($toko['nama_toko']) ?>
                        <span class="status-pill <?= $is_buka ? 'status-buka' : 'status-tutup' ?>">
                            <?= $is_buka ? '● Buka' : '● Tutup' ?>
                        </span>
                        <?php if ($toko['status_halal'] === 'tersertifikasi'): ?>
                        <span class="halal-tag"><i class="fas fa-leaf"></i> Halal</span>
                        <?php endif; ?>
                    </h1>
                    <div class="store-meta">
                        <?php if ($toko['rating']): ?>
                        <div class="meta-item rating-item"><i class="fas fa-star"></i> <?= $rating_text ?></div>
                        <?php endif; ?>
                        <?php if ($toko['lokasi']): ?>
                        <div class="meta-item"><i class="fas fa-map-marker-alt" style="color:var(--accent2)"></i>
                            <?= htmlspecialchars($toko['lokasi']) ?></div>
                        <?php endif; ?>
                        <?php if ($jam_buka): ?>
                        <div class="meta-item"><i class="fas fa-clock" style="color:var(--blue)"></i> <?= $jam_buka ?> –
                            <?= $jam_tutup ?></div>
                        <?php endif; ?>
                        <?php if ($toko['no_telepon']): ?>
                        <div class="meta-item"><i class="fas fa-phone" style="color:var(--green)"></i>
                            <?= htmlspecialchars($toko['no_telepon']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="hero-badges">
                        <?php foreach ($mitras as $m): ?>
                        <span class="mitra-badge">
                            <?php if ($m['logo']): ?><img src="../img/mitra/<?= $m['logo'] ?>" alt=""><?php endif; ?>
                            <?= htmlspecialchars($m['nama_mitra']) ?>
                        </span>
                        <?php endforeach; ?>
                        <?php foreach ($bayars as $b): ?>
                        <span class="bayar-badge"><i class="fas fa-wallet" style="font-size:11px"></i>
                            <?= htmlspecialchars($b['metode_pembayaran']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- MENU FILTER BAR -->
            <div class="menu-section">
                <form id="menuForm" method="GET" action="">
                    <input type="hidden" name="id_toko" value="<?= $id_toko ?>">
                    <input type="hidden" name="search_menu" id="searchMenuHidden"
                        value="<?= htmlspecialchars($search_menu) ?>">
                    <input type="hidden" name="rasa" id="rasa_hidden" value="<?= htmlspecialchars($filter_rasa) ?>">
                    <input type="hidden" name="kategori" id="kat_hidden" value="<?= $filter_kat ?>">
                    <input type="hidden" name="sort" id="sort_hidden" value="<?= htmlspecialchars($sort_menu) ?>">

                    <div class="menu-filter-bar">
                        <!-- Kategori chips -->
                        <button type="button" class="chip <?= !$filter_kat?'active':'' ?>"
                            onclick="setKat(0)">Semua</button>
                        <?php $all_kat->data_seek(0); while ($k = $all_kat->fetch_assoc()): ?>
                        <button type="button" class="chip <?= $filter_kat==$k['id_kategori']?'active':'' ?>"
                            onclick="setKat(<?= $k['id_kategori'] ?>)"><?= htmlspecialchars($k['kategori_makanan']) ?></button>
                        <?php endwhile; ?>

                        <!-- Rasa chips -->
                        <?php foreach (['pedas','manis','asin','berkuah','asam'] as $r): ?>
                        <a href="?id_toko=<?= $id_toko ?>&rasa=<?= $filter_rasa===$r?'':$r ?>&kategori=<?= $filter_kat ?>&sort=<?= $sort_menu ?>&search_menu=<?= urlencode($search_menu) ?>"
                            class="rasa-chip <?= $filter_rasa===$r?'active':'' ?>"><?= ucfirst($r) ?></a>
                        <?php endforeach; ?>

                        <!-- Sort -->
                        <select class="sort-wrap"
                            onchange="document.getElementById('sort_hidden').value=this.value;document.getElementById('menuForm').submit()">
                            <option value="" <?= !$sort_menu?'selected':'' ?>>Urutkan</option>
                            <option value="harga_asc" <?= $sort_menu==='harga_asc'?'selected':'' ?>>Harga Terendah
                            </option>
                            <option value="harga_desc" <?= $sort_menu==='harga_desc'?'selected':'' ?>>Harga Tertinggi
                            </option>
                            <option value="nama_az" <?= $sort_menu==='nama_az'?'selected':'' ?>>Nama A-Z</option>
                        </select>
                    </div>
                </form>


                <!-- MENU LIST -->
                <?php if (empty($menus)): ?>
                <div class="empty-menu">
                    <i class="fas fa-utensils"></i>
                    <h3>Menu tidak ditemukan</h3>
                    <p>Coba ubah filter atau kata kunci</p>
                </div>
                <?php elseif ($filter_kat || $filter_rasa || $search_menu || $sort_menu): ?>
                <!-- Flat list when filtered -->
                <div class="menu-list">
                    <?php foreach ($menus as $menu): ?>
                    <?php include_once 'koneksi.php';?>
                    <div class="menu-item">
                        <div class="menu-item-img">
                            <?php if ($menu['foto_menu']): ?>
                            <img src="../img/makanan/<?= $menu['foto_menu']; ?>" width="120"
                                style="border-radius:10px;">
                            <?php else: ?>
                            <i class="fas fa-utensils"></i>
                            <?php endif; ?>
                        </div>
                        <div class="menu-item-body">
                            <h4><?= htmlspecialchars($menu['nama_menu']) ?></h4>
                            <?php if ($menu['deskripsi']): ?>
                            <p class="desc"><?= htmlspecialchars(trim($menu['deskripsi'])) ?></p>
                            <?php endif; ?>
                            <div class="menu-item-footer">
                                <span class="menu-price">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></span>
                                <?php if ($menu['rasa']): ?>
                                <?php $rasa_class = 'rasa-' . $menu['rasa']; ?>
                                <span class="rasa-tag <?= $rasa_class ?>"><?= ucfirst($menu['rasa']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <!-- Grouped by kategori -->
                <?php foreach ($grouped as $kat_name => $kat_menus): ?>
                <div class="kategori-section">
                    <div class="kategori-title"><?= htmlspecialchars($kat_name) ?> <span
                            style="font-size:12px;font-weight:500;color:var(--text-muted)">(<?= count($kat_menus) ?>
                            item)</span></div>
                    <div class="menu-list">
                        <?php foreach ($kat_menus as $menu): ?>
                        <div class="menu-item">
                            <div class="menu-item-img">
                                <?php if ($menu['foto_menu']): ?>
                                <img src="../img/makanan/<?= $menu['foto_menu']; ?>" width="120"
                                    style="border-radius:10px;">
                                <?php else: ?>
                                <i class="fas fa-utensils"></i>
                                <?php endif; ?>
                            </div>
                            <div class="menu-item-body">
                                <h4><?= htmlspecialchars($menu['nama_menu']) ?></h4>
                                <?php if ($menu['deskripsi']): ?>
                                <p class="desc"><?= htmlspecialchars(trim($menu['deskripsi'])) ?></p>
                                <?php endif; ?>
                                <div class="menu-item-footer">
                                    <span class="menu-price">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></span>
                                    <?php if ($menu['rasa']): ?>
                                    <?php $rasa_class = 'rasa-' . $menu['rasa']; ?>
                                    <span class="rasa-tag <?= $rasa_class ?>"><?= ucfirst($menu['rasa']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- REVIEWS -->
            <div class="reviews-section">
                <div class="reviews-header">Ulasan Pelanggan</div>
                <?php if (empty($reviews)): ?>
                <div class="no-review"><i class="far fa-comment-dots"
                        style="font-size:32px;opacity:.4;display:block;margin-bottom:8px"></i> Belum ada ulasan</div>
                <?php else: ?>
                <?php foreach ($reviews as $rv): ?>
                <div class="review-card">
                    <div class="review-top">
                        <div class="review-avatar"><?= strtoupper(substr($rv['nama_pengulas'],0,1)) ?></div>
                        <div>
                            <div class="review-name"><?= htmlspecialchars($rv['nama_pengulas']) ?></div>
                            <div class="review-stars">
                                <?= str_repeat('★', $rv['rating']) ?><?= str_repeat('☆', 5 - $rv['rating']) ?></div>
                        </div>
                        <div class="review-date"><?= date('d M Y', strtotime($rv['tanggal_review'])) ?></div>
                    </div>
                    <div class="review-text"><?= htmlspecialchars($rv['komentar']) ?></div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function setKat(v) {
        document.getElementById('kat_hidden').value = v;
        document.getElementById('menuForm').submit();
    }

    let menuTimer;
    document.getElementById('searchMenuInput').addEventListener('input', function() {
        clearTimeout(menuTimer);
        menuTimer = setTimeout(() => {
            document.getElementById('searchMenuHidden').value = this.value;
            document.getElementById('menuForm').submit();
        }, 500);
    });
    </script>
</body>

</html>