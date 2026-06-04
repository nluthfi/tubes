<?php
session_start();
include 'koneksi.php';

// Fetch ALL toko (no pagination)
$all_toko_sql = "SELECT DISTINCT t.* FROM toko t ORDER BY t.id_toko ASC";
$result = $koneksi->query($all_toko_sql);
$rows = [];
$all_toko_ids = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $all_toko_ids[] = $row['id_toko'];
}

// Batch fetch mitra & bayar per toko
$mitra_map = [];
$bayar_map = [];
if ($all_toko_ids) {
    $ids_str = implode(',', $all_toko_ids);
    $r_mitra = $koneksi->query("SELECT tm.id_toko, mi.id_mitra, mi.nama_mitra, mi.logo FROM toko_mitra tm JOIN mitra mi ON mi.id_mitra = tm.id_mitra WHERE tm.id_toko IN ($ids_str)");
    while ($rm = $r_mitra->fetch_assoc()) $mitra_map[$rm['id_toko']][] = $rm;
    $r_bayar = $koneksi->query("SELECT mt.id_toko, b.id_metode, b.metode_pembayaran, b.logo FROM metode_toko mt JOIN bayar b ON b.id_metode = mt.id_metode WHERE mt.id_toko IN ($ids_str)");
    while ($rb = $r_bayar->fetch_assoc()) $bayar_map[$rb['id_toko']][] = $rb;
    // Fetch min harga per toko
    $r_harga = $koneksi->query("SELECT id_toko, MIN(harga) as min_harga FROM menu WHERE id_toko IN ($ids_str) GROUP BY id_toko");
    $harga_map = [];
    while ($rh = $r_harga->fetch_assoc()) $harga_map[$rh['id_toko']] = $rh['min_harga'];
    // Fetch distinct rasa per toko
    $r_rasa = $koneksi->query("SELECT DISTINCT id_toko, rasa FROM menu WHERE id_toko IN ($ids_str) AND rasa IS NOT NULL AND rasa != ''");
    $rasa_map = [];
    while ($rr = $r_rasa->fetch_assoc()) $rasa_map[$rr['id_toko']][] = $rr['rasa'];
}

// Dropdown data for filter
$all_mitra = $koneksi->query("SELECT * FROM mitra ORDER BY nama_mitra");
$all_bayar  = $koneksi->query("SELECT * FROM bayar ORDER BY metode_pembayaran");

// Build JSON data for JS filtering
$toko_json = [];
foreach ($rows as $row) {
    $mitras = $mitra_map[$row['id_toko']] ?? [];
    $bayars = $bayar_map[$row['id_toko']] ?? [];
    $foto = $row['foto_outlet'] ? (strpos($row['foto_outlet'],'http')===0 ? $row['foto_outlet'] : 'img/pict/'.$row['foto_outlet']) : '';
    $toko_json[] = [
        'id'         => $row['id_toko'],
        'nama'       => $row['nama_toko'],
        'lokasi'     => $row['lokasi'] ?? '',
        'jam_buka'   => $row['jam_buka']  ? substr($row['jam_buka'], 0, 5)  : null,
        'jam_tutup'  => $row['jam_tutup'] ? substr($row['jam_tutup'], 0, 5) : null,
        'halal'      => $row['status_halal'] ?? '',
        'rating'     => $row['rating'] ? (float)$row['rating'] : null,
        'foto'       => $foto,
        'min_harga'  => isset($harga_map[$row['id_toko']]) ? (int)$harga_map[$row['id_toko']] : 0,
        'mitra_ids'  => array_column($mitras, 'id_mitra'),
        'mitra_names'=> array_column($mitras, 'nama_mitra'),
        'bayar_ids'  => array_column($bayars, 'id_metode'),
        'bayar_names'=> array_column($bayars, 'metode_pembayaran'),
    ];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Street Food – Daftar Toko</title>
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
        --topbar: #111d30;
    }

    html {
        scroll-behavior: smooth
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--bg);
        color: var(--text);
        min-height: 100vh
    }

    /* ── TOPBAR ── */
    .topbar {
        background: var(--topbar);
        border-bottom: 1px solid var(--border);
        padding: 0 24px;
        height: 64px;
        display: flex;
        align-items: center;
        gap: 14px;
        position: sticky;
        top: 0;
        z-index: 100;
        backdrop-filter: blur(8px)
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        flex-shrink: 0
    }

    .logo-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px
    }

    .logo-text {
        font-weight: 800;
        font-size: 15px;
        color: #fff;
        white-space: nowrap
    }

    .logo-text small {
        display: block;
        font-size: 9px;
        font-weight: 500;
        color: var(--text-muted);
        letter-spacing: 1px;
        text-transform: uppercase
    }

    .search-box {
        flex: 1;
        max-width: 480px;
        position: relative;
        margin: 0 8px
    }

    .search-box i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 13px
    }

    .search-box input {
        width: 100%;
        background: rgba(255, 255, 255, .06);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 9px 14px 9px 36px;
        color: var(--text);
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: .18s
    }

    .search-box input:focus {
        border-color: var(--accent);
        background: rgba(240, 165, 0, .04)
    }

    .search-box input::placeholder {
        color: var(--text-muted)
    }

    .topbar-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 10px
    }

    .btn-filter {
        display: flex;
        align-items: center;
        gap: 7px;
        background: rgba(255, 255, 255, .06);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 9px 14px;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: .18s;
        font-family: inherit;
        white-space: nowrap
    }

    .btn-filter:hover,
    .btn-filter.active {
        border-color: var(--accent);
        color: var(--accent)
    }

    .filter-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent);
        display: none
    }

    .filter-dot.show {
        display: block
    }

    /* ── HERO ── */
    .hero {
        background: linear-gradient(135deg, #111d30 0%, #162035 60%, #1a2540 100%);
        padding: 40px 24px 32px;
        text-align: center;
        border-bottom: 1px solid var(--border)
    }

    .hero h1 {
        font-size: clamp(22px, 4vw, 36px);
        font-weight: 800;
        margin-bottom: 8px;
        background: linear-gradient(135deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text
    }

    .hero p {
        color: var(--text-muted);
        font-size: 15px
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 32px;
        margin-top: 24px;
        flex-wrap: wrap
    }

    .hero-stat {
        text-align: center
    }

    .hero-stat .num {
        font-size: 22px;
        font-weight: 800;
        color: var(--accent)
    }

    .hero-stat .lbl {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px
    }

    /* ── FILTER CHIPS ── */
    .filter-chips-bar {
        padding: 16px 24px 0;
        display: flex;
        gap: 8px;
        overflow-x: auto;
        scrollbar-width: none;
        border-bottom: 1px solid var(--border)
    }

    .filter-chips-bar::-webkit-scrollbar {
        display: none
    }

    .fchip {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        background: transparent;
        color: var(--text-muted);
        white-space: nowrap;
        transition: .15s;
        font-family: inherit;
        margin-bottom: 16px
    }

    .fchip:hover {
        border-color: var(--text-muted);
        color: var(--text)
    }

    .fchip.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #0f1623
    }

    .fchip i {
        margin-right: 5px;
        font-size: 11px
    }

    /* ── CONTENT WRAPPER ── */
    .content-wrap {
        max-width: 1400px;
        margin: 0 auto;
        padding: 28px 24px 60px
    }

    /* RESULTS BAR */
    .results-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px
    }

    .results-count {
        font-size: 14px;
        color: var(--text-muted)
    }

    .results-count span {
        color: var(--text);
        font-weight: 700
    }

    .sort-select {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 8px 14px;
        color: var(--text);
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        outline: none;
        cursor: pointer
    }

    .sort-select:focus {
        border-color: var(--accent)
    }

    /* ── STORES GRID ── */
    .stores-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px
    }

    /* STORE CARD */
    .store-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        cursor: pointer;
        transition: .22s;
        text-decoration: none;
        display: block;
        color: var(--text)
    }

    .store-card:hover {
        transform: translateY(-4px);
        border-color: rgba(240, 165, 0, .35);
        box-shadow: 0 12px 40px rgba(0, 0, 0, .4)
    }

    .card-img {
        position: relative;
        overflow: hidden;
        height: 180px;
        background: linear-gradient(135deg, #1f2d4e, #263354);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 40px
    }

    .card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: .3s;
        display: block
    }

    .store-card:hover .card-img img {
        transform: scale(1.04)
    }

    .card-status {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700
    }

    .status-buka {
        background: rgba(34, 197, 94, .2);
        color: var(--green);
        border: 1px solid rgba(34, 197, 94, .4)
    }

    .status-tutup {
        background: rgba(239, 68, 68, .2);
        color: var(--red);
        border: 1px solid rgba(239, 68, 68, .4)
    }

    .halal-badge {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(34, 197, 94, .15);
        border: 1px solid rgba(34, 197, 94, .35);
        color: var(--green);
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 4px
    }

    .card-body {
        padding: 14px
    }

    .store-name {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px
    }

    .store-name-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }

    .rating {
        display: flex;
        align-items: center;
        gap: 3px;
        font-size: 12px;
        font-weight: 700;
        color: var(--accent);
        flex-shrink: 0
    }

    .rating i {
        font-size: 10px
    }

    .card-address {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        gap: 5px;
        align-items: flex-start;
        margin-bottom: 4px
    }

    .card-address i {
        margin-top: 2px;
        flex-shrink: 0;
        color: var(--accent2);
        font-size: 11px
    }

    .card-address span {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden
    }

    .card-hours {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        gap: 5px;
        align-items: center;
        margin-bottom: 10px
    }

    .card-hours i {
        color: var(--blue);
        font-size: 11px
    }

    .mitra-row {
        display: flex;
        gap: 5px;
        flex-wrap: wrap
    }

    .mitra-badge {
        background: rgba(59, 130, 246, .1);
        border: 1px solid rgba(59, 130, 246, .22);
        color: var(--blue);
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 4px
    }

    .mitra-badge img {
        width: 12px;
        height: 12px;
        object-fit: contain
    }

    /* EMPTY */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--text-muted);
        grid-column: 1/-1
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: .3;
        display: block
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--text)
    }

    /* ── FILTER PANEL ── */
    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .65);
        z-index: 200;
        opacity: 0;
        pointer-events: none;
        transition: .2s
    }

    .overlay.show {
        opacity: 1;
        pointer-events: all
    }

    .filter-panel {
        position: fixed;
        top: 0;
        right: -440px;
        width: 420px;
        max-width: 100vw;
        height: 100vh;
        background: #111d30;
        border-left: 1px solid var(--border);
        z-index: 201;
        overflow-y: auto;
        transition: .28s cubic-bezier(.4, 0, .2, 1);
        display: flex;
        flex-direction: column
    }

    .filter-panel.show {
        right: 0
    }

    .filter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        background: #111d30;
        z-index: 1
    }

    .filter-header h3 {
        font-size: 16px;
        font-weight: 800
    }

    .filter-header-right {
        display: flex;
        align-items: center;
        gap: 12px
    }

    .filter-reset {
        color: var(--accent);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        background: none;
        border: none;
        font-family: inherit;
        display: flex;
        align-items: center;
        gap: 5px
    }

    .close-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--card);
        border: 1px solid var(--border);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 15px
    }

    .close-btn:hover {
        color: var(--text)
    }

    .filter-body {
        padding: 18px 22px;
        flex: 1
    }

    .filter-section {
        margin-bottom: 22px
    }

    .filter-section h4 {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .8px;
        text-transform: uppercase;
        margin-bottom: 12px;
        color: var(--text-muted)
    }

    .chip-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px
    }

    .chip-opt {
        padding: 7px 14px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        background: transparent;
        color: var(--text-muted);
        transition: .15s;
        font-family: inherit
    }

    .chip-opt:hover {
        border-color: var(--text-muted);
        color: var(--text)
    }

    .chip-opt.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #0f1623
    }

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 10px
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-muted)
    }

    .checkbox-item input {
        accent-color: var(--accent);
        width: 16px;
        height: 16px;
        cursor: pointer;
        flex-shrink: 0
    }

    .checkbox-item:hover {
        color: var(--text)
    }

    .filter-footer {
        padding: 16px 22px;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 12px;
        position: sticky;
        bottom: 0;
        background: #111d30
    }

    .btn-apply {
        flex: 1;
        background: var(--accent);
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-size: 14px;
        font-weight: 700;
        color: #0f1623;
        cursor: pointer;
        font-family: inherit;
        transition: .15s
    }

    .btn-apply:hover {
        background: #e09800
    }

    .btn-cancel {
        flex: 1;
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 12px;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        font-family: inherit;
        transition: .15s
    }

    .btn-cancel:hover {
        color: var(--text)
    }

    /* hidden card */
    .store-card.hidden {
        display: none
    }

    @media(max-width:640px) {
        .topbar {
            padding: 0 14px;
            gap: 8px
        }

        .logo-text small,
        .topbar-right .btn-filter span {
            display: none
        }

        .hero {
            padding: 28px 16px 24px
        }

        .content-wrap {
            padding: 20px 14px 60px
        }

        .filter-chips-bar {
            padding: 12px 14px 0
        }

        .filter-panel {
            width: 100vw
        }
    }
    </style>
</head>

<body>

    <!-- TOPBAR -->
    <header class="topbar">
        <a href="index.php" class="logo">
            <div class="logo-icon">🍜</div>
            <div class="logo-text">STREET FOOD<small>Gegerkalong</small></div>
        </a>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari toko atau menu...">
        </div>
        <div class="topbar-right">
            <button class="btn-filter" id="btnFilter" onclick="openFilter()">
                <i class="fas fa-sliders-h"></i>
                <span>Filter</span>
                <div class="filter-dot" id="filterDot"></div>
            </button>
        </div>
    </header>

    <!-- FILTER CHIPS BAR -->
    <div class="filter-chips-bar" id="filterChipsBar">
        <button class="fchip active" id="chip-all" onclick="toggleChip('all')">Semua</button>
        <button class="fchip" id="chip-buka" onclick="toggleChip('buka')"><i class="fas fa-clock"></i>Buka
            Sekarang</button>
        <button class="fchip" id="chip-halal" onclick="toggleChip('halal')"><i class="fas fa-leaf"></i>Halal</button>
        <button class="fchip" id="chip-rating" onclick="toggleChip('rating')"><i class="fas fa-star"></i>Rating
            Tinggi</button>
        <div style="width:1px;background:var(--border);margin:6px 4px 16px;flex-shrink:0"></div>
        <button class="fchip" id="chip-rasa-pedas" onclick="toggleRasaChip('pedas')">🌶️ Pedas</button>
        <button class="fchip" id="chip-rasa-manis" onclick="toggleRasaChip('manis')">🍬 Manis</button>
        <button class="fchip" id="chip-rasa-asin" onclick="toggleRasaChip('asin')">🧂 Asin</button>
        <button class="fchip" id="chip-rasa-berkuah" onclick="toggleRasaChip('berkuah')">🍲 Berkuah</button>
        <button class="fchip" id="chip-rasa-asam" onclick="toggleRasaChip('asam')">🍋 Asam</button>
    </div>

    <!-- HERO -->
    <div class="hero">
        <h1>🍜 Street Food Gegerkalong</h1>
        <p>Temukan berbagai pilihan kuliner street food favoritmu</p>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num" id="statTotal"><?= count($rows) ?></div>
                <div class="lbl">Total Toko</div>
            </div>
            <div class="hero-stat">
                <div class="num" id="statBuka">–</div>
                <div class="lbl">Toko Buka</div>
            </div>
            <div class="hero-stat">
                <div class="num" id="statHalal">
                    <?= count(array_filter($rows, fn($r) => $r['status_halal'] === 'tersertifikasi')) ?></div>
                <div class="lbl">Halal Tersertifikasi</div>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content-wrap">
        <div class="results-bar">
            <div class="results-count">Menampilkan <span id="countVisible"><?= count($rows) ?></span> toko</div>
            <select class="sort-select" id="sortSelect" onchange="applyFilters()">
                <option value="">Urutkan</option>
                <option value="rating_desc">Rating Tertinggi</option>
                <option value="rating_asc">Rating Terendah</option>
                <option value="nama_az">Nama A–Z</option>
                <option value="nama_za">Nama Z–A</option>
                <option value="harga_asc">Harga Terendah</option>
            </select>
        </div>

        <div class="stores-grid" id="storesGrid">
            <?php foreach ($rows as $row):
      $foto = $row['foto_outlet'] ? (strpos($row['foto_outlet'],'http')===0 ? $row['foto_outlet'] : 'img/pict/'.$row['foto_outlet']) : '';
      $mitras = $mitra_map[$row['id_toko']] ?? [];
      $jam_buka_str  = $row['jam_buka']  ? substr($row['jam_buka'], 0, 5)  : null;
      $jam_tutup_str = $row['jam_tutup'] ? substr($row['jam_tutup'], 0, 5) : null;
      $rating = $row['rating'] ? number_format($row['rating'], 1) : null;
      $min_harga = isset($harga_map[$row['id_toko']]) ? $harga_map[$row['id_toko']] : 0;
    ?>
            <a class="store-card" href="page_menu.php?id_toko=<?= $row['id_toko'] ?>" data-id="<?= $row['id_toko'] ?>"
                data-nama="<?= htmlspecialchars(strtolower($row['nama_toko'].($row['lokasi']??'')),ENT_QUOTES) ?>"
                data-halal="<?= htmlspecialchars($row['status_halal']??'') ?>"
                data-jam-buka="<?= htmlspecialchars($row['jam_buka']??'') ?>"
                data-jam-tutup="<?= htmlspecialchars($row['jam_tutup']??'') ?>"
                data-rating="<?= (float)$row['rating'] ?>" data-min-harga="<?= $min_harga ?>"
                data-mitra-ids="<?= htmlspecialchars(implode(',', array_column($mitras,'id_mitra'))) ?>"
                data-bayar-ids="<?= htmlspecialchars(implode(',', $bayar_map[$row['id_toko']] ? array_column($bayar_map[$row['id_toko']],'id_metode') : [])) ?>"
                data-rasa="<?= htmlspecialchars(implode(',', $rasa_map[$row['id_toko']] ?? [])) ?>">
                <div class="card-img">
                    <?php if ($foto): ?>
                    <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($row['nama_toko']) ?>"
                        loading="lazy" onerror="this.style.display='none'">
                    <?php else: ?>
                    <i class="fas fa-store"></i>
                    <?php endif; ?>
                    <!-- Status jam diisi JS secara realtime -->
                    <span class="card-status" data-jam-buka="<?= htmlspecialchars($row['jam_buka']??'') ?>"
                        data-jam-tutup="<?= htmlspecialchars($row['jam_tutup']??'') ?>"></span>
                    <?php if ($row['status_halal'] === 'tersertifikasi'): ?>
                    <span class="halal-badge"><i class="fas fa-leaf"></i> Halal</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="store-name">
                        <span class="store-name-text"><?= htmlspecialchars($row['nama_toko']) ?></span>
                        <?php if ($rating): ?>
                        <span class="rating"><i class="fas fa-star"></i> <?= $rating ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($row['lokasi']): ?>
                    <div class="card-address"><i
                            class="fas fa-map-marker-alt"></i><span><?= htmlspecialchars($row['lokasi']) ?></span></div>
                    <?php endif; ?>
                    <?php if ($jam_buka_str): ?>
                    <div class="card-hours"><i class="fas fa-clock"></i><span><?= $jam_buka_str ?> –
                            <?= $jam_tutup_str ?></span></div>
                    <?php endif; ?>
                    <?php if ($mitras): ?>
                    <div class="mitra-row">
                        <?php foreach ($mitras as $m):
            $mlogo = $m['logo'] ? (strpos($m['logo'],'http')===0 ? $m['logo'] : 'img/logo/'.$m['logo']) : '';
          ?>
                        <span class="mitra-badge">
                            <?php if ($mlogo): ?><img src="<?= htmlspecialchars($mlogo) ?>" alt=""><?php endif; ?>
                            <?= htmlspecialchars($m['nama_mitra']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="empty-state hidden" id="emptyState">
            <i class="fas fa-store-slash"></i>
            <h3>Tidak ada toko ditemukan</h3>
            <p>Coba ubah filter atau kata kunci pencarian</p>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="overlay" id="overlay" onclick="closeFilter()"></div>
    <div class="filter-panel" id="filterPanel">
        <div class="filter-header">
            <h3><i class="fas fa-sliders-h" style="margin-right:8px;color:var(--accent)"></i>Filter</h3>
            <div class="filter-header-right">
                <button class="filter-reset" onclick="resetFilter()"><i class="fas fa-rotate-left"></i> Reset</button>
                <button class="close-btn" onclick="closeFilter()"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="filter-body">

            <!-- HARGA -->
            <div class="filter-section">
                <h4>Harga Menu (mulai dari)</h4>
                <div class="chip-group" id="hargaChips">
                    <button class="chip-opt active" data-harga="0" onclick="setHarga(this,0)">Semua</button>
                    <button class="chip-opt" data-harga="1" onclick="setHarga(this,1)">&lt; Rp 5.000</button>
                    <button class="chip-opt" data-harga="5000" onclick="setHarga(this,5000)">Rp 5.000–10.000</button>
                    <button class="chip-opt" data-harga="10000" onclick="setHarga(this,10000)">Rp 10.000–15.000</button>
                    <button class="chip-opt" data-harga="15000" onclick="setHarga(this,15000)">Rp 15.000–20.000</button>
                    <button class="chip-opt" data-harga="20000" onclick="setHarga(this,20000)">&gt; Rp 20.000</button>
                </div>
            </div>

            <!-- JAM OPERASIONAL -->
            <div class="filter-section">
                <h4>Jam Operasional</h4>
                <div class="chip-group" id="jamChips">
                    <button class="chip-opt active" data-jam="" onclick="setJam(this,'')">Semua</button>
                    <button class="chip-opt" data-jam="now" onclick="setJam(this,'now')">Buka Sekarang</button>
                    <button class="chip-opt" data-jam="07-10" onclick="setJam(this,'07-10')">07:00–10:00</button>
                    <button class="chip-opt" data-jam="10-14" onclick="setJam(this,'10-14')">10:00–14:00</button>
                    <button class="chip-opt" data-jam="14-17" onclick="setJam(this,'14-17')">14:00–17:00</button>
                    <button class="chip-opt" data-jam="17-21" onclick="setJam(this,'17-21')">17:00–21:00</button>
                    <button class="chip-opt" data-jam="21-24" onclick="setJam(this,'21-24')">21:00–24:00</button>
                </div>
            </div>

            <!-- STATUS HALAL -->
            <div class="filter-section">
                <h4>Status Halal</h4>
                <div class="chip-group" id="halalChips">
                    <button class="chip-opt active" data-halal="" onclick="setHalal(this,'')">Semua</button>
                    <button class="chip-opt" data-halal="tersertifikasi"
                        onclick="setHalal(this,'tersertifikasi')">Tersertifikasi</button>
                    <button class="chip-opt" data-halal="belum tersertifikasi"
                        onclick="setHalal(this,'belum tersertifikasi')">Belum Sertifikasi</button>
                    <button class="chip-opt" data-halal="non halal" onclick="setHalal(this,'non halal')">Non
                        Halal</button>
                </div>
            </div>

            <!-- KATEGORI RASA -->
            <div class="filter-section">
                <h4>Kategori Rasa</h4>
                <div class="chip-group" id="rasaChips">
                    <button class="chip-opt cb-rasa" data-val="pedas" onclick="toggleRasaPanel(this)">🌶️ Pedas</button>
                    <button class="chip-opt cb-rasa" data-val="manis" onclick="toggleRasaPanel(this)">🍬 Manis</button>
                    <button class="chip-opt cb-rasa" data-val="asin" onclick="toggleRasaPanel(this)">🧂 Asin</button>
                    <button class="chip-opt cb-rasa" data-val="berkuah" onclick="toggleRasaPanel(this)">🍲
                        Berkuah</button>
                    <button class="chip-opt cb-rasa" data-val="asam" onclick="toggleRasaPanel(this)">🍋 Asam</button>
                </div>
            </div>

            <!-- MITRA -->
            <div class="filter-section">
                <h4>Platform Mitra</h4>
                <div class="checkbox-group">
                    <?php $all_mitra->data_seek(0); while ($m = $all_mitra->fetch_assoc()): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" class="cb-mitra" value="<?= $m['id_mitra'] ?>">
                        <?= htmlspecialchars($m['nama_mitra']) ?>
                    </label>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- METODE BAYAR -->
            <div class="filter-section">
                <h4>Metode Pembayaran</h4>
                <div class="chip-group" id="bayarChips">
                    <?php $all_bayar->data_seek(0); while ($b = $all_bayar->fetch_assoc()): ?>
                    <button class="chip-opt cb-bayar" data-val="<?= $b['id_metode'] ?>"
                        onclick="toggleBayar(this)"><?= htmlspecialchars($b['metode_pembayaran']) ?></button>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>
        <div class="filter-footer">
            <button class="btn-cancel" onclick="closeFilter()">Batal</button>
            <button class="btn-apply" onclick="applyFilters();closeFilter()">Terapkan</button>
        </div>
    </div>

    <script>
    // ── FILTER STATE ──────────────────────────────────────────────
    const state = {
        search: '',
        harga: 0,
        jam: '',
        halal: '',
        mitra: [],
        bayar: [],
        rasa: [],
        quickBuka: false,
        quickHalal: false,
        quickRating: false,
        sort: '',
    };

    // All cards NodeList
    const allCards = () => document.querySelectorAll('.store-card');

    // ── REALTIME JAM BUKA/TUTUP ───────────────────────────────────
    function timeToMin(str) {
        if (!str) return null;
        const [h, m] = str.split(':').map(Number);
        return h * 60 + m;
    }

    function nowMin() {
        const now = new Date();
        return now.getHours() * 60 + now.getMinutes();
    }

    function isTokoOpen(bukaSec, tutupSec) {
        if (!bukaSec || !tutupSec) return null; // unknown
        const bMins = timeToMin(bukaSec.substring(0, 5));
        const tMins = timeToMin(tutupSec.substring(0, 5));
        const now = nowMin();
        if (tMins <= bMins) {
            // overnight: e.g. 22:00-02:00 or 00:00-23:59 (treat 00:00-23:59 as always open)
            return now >= bMins || now <= tMins;
        }
        return now >= bMins && now <= tMins;
    }

    function updateAllStatus() {
        let bukaCount = 0;
        document.querySelectorAll('.card-status').forEach(el => {
            const buka = el.dataset.jamBuka;
            const tutup = el.dataset.jamTutup;
            const open = isTokoOpen(buka, tutup);
            if (open === null) {
                el.textContent = '';
                el.className = 'card-status';
            } else if (open) {
                el.textContent = '● Buka';
                el.className = 'card-status status-buka';
                bukaCount++;
            } else {
                el.textContent = '● Tutup';
                el.className = 'card-status status-tutup';
            }
        });
        const el = document.getElementById('statBuka');
        if (el) el.textContent = bukaCount;
    }

    // Update every 30s
    updateAllStatus();
    setInterval(updateAllStatus, 30000);

    // ── FILTER HELPERS ────────────────────────────────────────────
    function setHarga(btn, val) {
        document.querySelectorAll('#hargaChips .chip-opt').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        state.harga = val;
    }

    function setJam(btn, val) {
        document.querySelectorAll('#jamChips .chip-opt').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        state.jam = val;
    }

    function setHalal(btn, val) {
        document.querySelectorAll('#halalChips .chip-opt').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        state.halal = val;
    }

    function toggleBayar(btn) {
        btn.classList.toggle('active');
    }

    function toggleRasaPanel(btn) {
        btn.classList.toggle('active');
        syncRasaChipsBar();
    }

    function toggleRasaChip(val) {
        // toggle quick chip
        const chipEl = document.getElementById('chip-rasa-' + val);
        if (chipEl) chipEl.classList.toggle('active');
        // sync panel
        const panelBtn = document.querySelector('.cb-rasa[data-val="' + val + '"]');
        if (panelBtn) panelBtn.classList.toggle('active', chipEl ? chipEl.classList.contains('active') : false);
        updateAllChip();
        applyFilters();
    }

    function syncRasaChipsBar() {
        ['pedas', 'manis', 'asin', 'berkuah', 'asam'].forEach(r => {
            const panelActive = document.querySelector('.cb-rasa[data-val="' + r + '"]')?.classList.contains(
                'active');
            const chipEl = document.getElementById('chip-rasa-' + r);
            if (chipEl) chipEl.classList.toggle('active', !!panelActive);
        });
        updateAllChip();
    }

    function updateAllChip() {
        const anyRasa = [...document.querySelectorAll('.cb-rasa.active')].length > 0;
        const anyQuick = state.quickBuka || state.quickHalal || state.quickRating;
        document.getElementById('chip-all').classList.toggle('active', !anyQuick && !anyRasa);
    }

    // ── QUICK CHIPS BAR ───────────────────────────────────────────
    function toggleChip(id) {
        if (id === 'all') {
            state.quickBuka = false;
            state.quickHalal = false;
            state.quickRating = false;
            document.getElementById('chip-all').classList.add('active');
            document.getElementById('chip-buka').classList.remove('active');
            document.getElementById('chip-halal').classList.remove('active');
            document.getElementById('chip-rating').classList.remove('active');
            // clear rasa
            document.querySelectorAll('.cb-rasa').forEach(b => b.classList.remove('active'));
            ['pedas', 'manis', 'asin', 'berkuah', 'asam'].forEach(r => {
                const el = document.getElementById('chip-rasa-' + r);
                if (el) el.classList.remove('active');
            });
        } else if (id === 'buka') {
            state.quickBuka = !state.quickBuka;
            document.getElementById('chip-buka').classList.toggle('active', state.quickBuka);
            updateAllChip();
        } else if (id === 'halal') {
            state.quickHalal = !state.quickHalal;
            document.getElementById('chip-halal').classList.toggle('active', state.quickHalal);
            updateAllChip();
        } else if (id === 'rating') {
            state.quickRating = !state.quickRating;
            document.getElementById('chip-rating').classList.toggle('active', state.quickRating);
            updateAllChip();
        }
        applyFilters();
    }

    // ── MAIN FILTER + SORT ────────────────────────────────────────
    function applyFilters() {
        state.search = document.getElementById('searchInput').value.toLowerCase();
        state.sort = document.getElementById('sortSelect').value;

        // collect bayar
        state.bayar = [...document.querySelectorAll('.cb-bayar.active')].map(b => b.dataset.val);
        // collect mitra
        state.mitra = [...document.querySelectorAll('.cb-mitra:checked')].map(b => b.value);
        // collect rasa
        state.rasa = [...document.querySelectorAll('.cb-rasa.active')].map(b => b.dataset.val);

        const cards = [...allCards()];
        let visible = [];

        cards.forEach(card => {
            let show = true;
            const nama = card.dataset.nama || '';
            const halal = card.dataset.halal || '';
            const jamBuka = card.dataset.jamBuka || '';
            const jamTutup = card.dataset.jamTutup || '';
            const rating = parseFloat(card.dataset.rating) || 0;
            const minHarga = parseInt(card.dataset.minHarga) || 0;
            const mitraIds = card.dataset.mitraIds ? card.dataset.mitraIds.split(',') : [];
            const bayarIds = card.dataset.bayarIds ? card.dataset.bayarIds.split(',') : [];
            const rasaList = card.dataset.rasa ? card.dataset.rasa.split(',') : [];

            // Search
            if (state.search && !nama.includes(state.search)) show = false;

            // Quick: buka sekarang
            if (show && state.quickBuka) {
                if (!isTokoOpen(jamBuka, jamTutup)) show = false;
            }
            // Quick: halal
            if (show && state.quickHalal && halal !== 'tersertifikasi') show = false;
            // Quick: rating
            if (show && state.quickRating && rating < 4) show = false;

            // Panel jam filter
            if (show && state.jam) {
                if (state.jam === 'now') {
                    if (!isTokoOpen(jamBuka, jamTutup)) show = false;
                } else {
                    const ranges = {
                        '07-10': [7 * 60, 10 * 60],
                        '10-14': [10 * 60, 14 * 60],
                        '14-17': [14 * 60, 17 * 60],
                        '17-21': [17 * 60, 21 * 60],
                        '21-24': [21 * 60, 24 * 60]
                    };
                    const r = ranges[state.jam];
                    if (r) {
                        const bMin = timeToMin(jamBuka.substring(0, 5));
                        const tMin = timeToMin(jamTutup.substring(0, 5));
                        // toko must overlap with the range
                        if (bMin === null || tMin === null || tMin < r[0] || bMin > r[1]) show = false;
                    }
                }
            }

            // Panel halal
            if (show && state.halal && halal !== state.halal) show = false;

            // Panel harga
            if (show && state.harga > 0) {
                if (state.harga === 1) {
                    if (minHarga >= 5000) show = false;
                } else if (state.harga === 5000) {
                    if (minHarga < 5000 || minHarga >= 10000) show = false;
                } else if (state.harga === 10000) {
                    if (minHarga < 10000 || minHarga >= 15000) show = false;
                } else if (state.harga === 15000) {
                    if (minHarga < 15000 || minHarga >= 20000) show = false;
                } else if (state.harga === 20000) {
                    if (minHarga < 20000) show = false;
                }
            }

            // Mitra
            if (show && state.mitra.length > 0) {
                if (!state.mitra.some(id => mitraIds.includes(id))) show = false;
            }

            // Bayar
            if (show && state.bayar.length > 0) {
                if (!state.bayar.some(id => bayarIds.includes(id))) show = false;
            }

            // Rasa (toko harus punya minimal satu menu dengan rasa yg dipilih)
            if (show && state.rasa.length > 0) {
                if (!state.rasa.some(r => rasaList.includes(r))) show = false;
            }

            if (show) visible.push(card);
            else card.classList.add('hidden');
        });

        // Sort visible
        if (state.sort) {
            const grid = document.getElementById('storesGrid');
            visible.sort((a, b) => {
                if (state.sort === 'rating_desc') return (parseFloat(b.dataset.rating) || 0) - (parseFloat(a
                    .dataset.rating) || 0);
                if (state.sort === 'rating_asc') return (parseFloat(a.dataset.rating) || 0) - (parseFloat(b
                    .dataset.rating) || 0);
                if (state.sort === 'nama_az') return a.dataset.nama.localeCompare(b.dataset.nama);
                if (state.sort === 'nama_za') return b.dataset.nama.localeCompare(a.dataset.nama);
                if (state.sort === 'harga_asc') return (parseInt(a.dataset.minHarga) || 0) - (parseInt(b.dataset
                    .minHarga) || 0);
                return 0;
            });
            visible.forEach(c => {
                c.classList.remove('hidden');
                grid.appendChild(c);
            });
        } else {
            visible.forEach(c => c.classList.remove('hidden'));
        }

        // Update count
        document.getElementById('countVisible').textContent = visible.length;
        const empty = document.getElementById('emptyState');
        empty.classList.toggle('hidden', visible.length > 0);

        // Filter dot
        const hasFilter = state.harga || state.jam || state.halal || state.mitra.length || state.bayar.length || state
            .rasa.length;
        document.getElementById('filterDot').classList.toggle('show', !!hasFilter);
        document.getElementById('btnFilter').classList.toggle('active', !!hasFilter);
    }

    // ── FILTER OPEN/CLOSE ─────────────────────────────────────────
    function openFilter() {
        document.getElementById('overlay').classList.add('show');
        document.getElementById('filterPanel').classList.add('show');
    }

    function closeFilter() {
        document.getElementById('overlay').classList.remove('show');
        document.getElementById('filterPanel').classList.remove('show');
    }

    function resetFilter() {
        state.harga = 0;
        state.jam = '';
        state.halal = '';
        state.mitra = [];
        state.bayar = [];
        document.querySelectorAll('#hargaChips .chip-opt').forEach((b, i) => b.classList.toggle('active', i === 0));
        document.querySelectorAll('#jamChips .chip-opt').forEach((b, i) => b.classList.toggle('active', i === 0));
        document.querySelectorAll('#halalChips .chip-opt').forEach((b, i) => b.classList.toggle('active', i === 0));
        document.querySelectorAll('.cb-mitra').forEach(b => b.checked = false);
        document.querySelectorAll('.cb-bayar').forEach(b => b.classList.remove('active'));
        applyFilters();
    }

    // ── SEARCH DEBOUNCE ───────────────────────────────────────────
    let searchTimer;
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(applyFilters, 300);
    });

    // Init
    applyFilters();
    </script>
</body>

</html>