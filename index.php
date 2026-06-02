<?php
session_start();
include 'koneksi.php';

// ── FILTER PARAMETERS ──────────────────────────────────────────────────────
$search        = isset($_GET['search'])         ? trim($_GET['search'])            : '';
$harga_min     = isset($_GET['harga_min'])       ? (int)$_GET['harga_min']          : 0;
$harga_max     = isset($_GET['harga_max'])       ? (int)$_GET['harga_max']          : 0;
$jam_filter    = isset($_GET['jam'])             ? $_GET['jam']                     : '';
$halal_filter  = isset($_GET['halal'])           ? $_GET['halal']                   : '';
$mitra_filter  = isset($_GET['mitra'])           ? (array)$_GET['mitra']            : [];
$bayar_filter  = isset($_GET['bayar'])           ? (array)$_GET['bayar']            : [];
$rasa_filter   = isset($_GET['rasa'])            ? (array)$_GET['rasa']             : [];
$terbuka       = isset($_GET['terbuka'])         ? $_GET['terbuka']                 : '';
$sort          = isset($_GET['sort'])            ? $_GET['sort']                    : '';

// ── PAGINATION ──────────────────────────────────────────────────────────────
$per_page    = 6;
$page        = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset      = ($page - 1) * $per_page;

// ── BUILD QUERY ─────────────────────────────────────────────────────────────
$where   = [];
$params  = [];
$types   = '';

if ($search !== '') {
    $where[]  = '(t.nama_toko LIKE ? OR m.nama_menu LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types   .= 'ss';
}

if ($halal_filter !== '') {
    $where[]  = 't.status_halal = ?';
    $params[] = $halal_filter;
    $types   .= 's';
}

if ($terbuka === '1') {
    $now      = date('H:i:s');
    $where[]  = '(t.jam_buka <= ? AND t.jam_tutup >= ?)';
    $params[] = $now;
    $params[] = $now;
    $types   .= 'ss';
}

if ($jam_filter !== '') {
    switch ($jam_filter) {
        case '07-10': $w = "(t.jam_buka <= '10:00:00' AND t.jam_tutup >= '07:00:00')"; break;
        case '10-14': $w = "(t.jam_buka <= '14:00:00' AND t.jam_tutup >= '10:00:00')"; break;
        case '14-17': $w = "(t.jam_buka <= '17:00:00' AND t.jam_tutup >= '14:00:00')"; break;
        case '17-21': $w = "(t.jam_buka <= '21:00:00' AND t.jam_tutup >= '17:00:00')"; break;
        case '21-24': $w = "(t.jam_tutup >= '21:00:00' OR t.jam_tutup = '23:59:00')"; break;
        default: $w = '';
    }
    if ($w) $where[] = $w;
}

if (!empty($mitra_filter)) {
    $placeholders = implode(',', array_fill(0, count($mitra_filter), '?'));
    $where[]  = "t.id_toko IN (SELECT id_toko FROM toko_mitra WHERE id_mitra IN ($placeholders))";
    foreach ($mitra_filter as $v) { $params[] = $v; $types .= 'i'; }
}

if (!empty($bayar_filter)) {
    $placeholders = implode(',', array_fill(0, count($bayar_filter), '?'));
    $where[]  = "t.id_toko IN (SELECT id_toko FROM metode_toko WHERE id_metode IN ($placeholders))";
    foreach ($bayar_filter as $v) { $params[] = $v; $types .= 'i'; }
}

if (!empty($rasa_filter)) {
    $placeholders = implode(',', array_fill(0, count($rasa_filter), '?'));
    $where[]  = "t.id_toko IN (SELECT id_toko FROM menu WHERE rasa IN ($placeholders))";
    foreach ($rasa_filter as $v) { $params[] = $v; $types .= 's'; }
}

if ($harga_min > 0 || $harga_max > 0) {
    if ($harga_min > 0 && $harga_max > 0) {
        $where[]  = "t.id_toko IN (SELECT id_toko FROM menu WHERE harga >= ? AND harga <= ?)";
        $params[] = $harga_min * 1000; $params[] = $harga_max * 1000;
        $types   .= 'ii';
    } elseif ($harga_min > 0) {
        $where[]  = "t.id_toko IN (SELECT id_toko FROM menu WHERE harga >= ?)";
        $params[] = $harga_min * 1000;
        $types   .= 'i';
    }
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count query (with DISTINCT for search join)
$count_join = ($search !== '') ? 'LEFT JOIN menu m ON m.id_toko = t.id_toko' : '';
$count_sql  = "SELECT COUNT(DISTINCT t.id_toko) AS total FROM toko t $count_join $where_sql";
$stmt_count = $koneksi->prepare($count_sql);
if ($params) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total_rows = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_rows / $per_page));
$page = min($page, $total_pages);

// Order
$order_sql = match($sort) {
    'rating_desc' => 'ORDER BY t.rating DESC',
    'rating_asc'  => 'ORDER BY t.rating ASC',
    'nama_az'     => 'ORDER BY t.nama_toko ASC',
    'nama_za'     => 'ORDER BY t.nama_toko DESC',
    default       => 'ORDER BY t.id_toko ASC',
};

$main_join  = ($search !== '') ? 'LEFT JOIN menu m ON m.id_toko = t.id_toko' : '';
$main_sql   = "SELECT DISTINCT t.* FROM toko t $main_join $where_sql $order_sql LIMIT ? OFFSET ?";
$params_main   = $params;
$params_main[] = $per_page;
$params_main[] = $offset;
$types_main    = $types . 'ii';

$stmt = $koneksi->prepare($main_sql);
$stmt->bind_param($types_main, ...$params_main);
$stmt->execute();
$result = $stmt->get_result();

// Fetch mitra for each toko (batch)
$all_toko_ids = [];
$rows = [];
while ($row = $result->fetch_assoc()) { $rows[] = $row; $all_toko_ids[] = $row['id_toko']; }

$mitra_map = [];
$bayar_map = [];
if ($all_toko_ids) {
    $ids_str = implode(',', $all_toko_ids);
    $r_mitra = $koneksi->query("SELECT tm.id_toko, mi.nama_mitra, mi.logo FROM toko_mitra tm JOIN mitra mi ON mi.id_mitra = tm.id_mitra WHERE tm.id_toko IN ($ids_str)");
    while ($rm = $r_mitra->fetch_assoc()) $mitra_map[$rm['id_toko']][] = $rm;
    $r_bayar = $koneksi->query("SELECT mt.id_toko, b.metode_pembayaran, b.logo FROM metode_toko mt JOIN bayar b ON b.id_metode = mt.id_metode WHERE mt.id_toko IN ($ids_str)");
    while ($rb = $r_bayar->fetch_assoc()) $bayar_map[$rb['id_toko']][] = $rb;
}

// Dropdown data
$all_mitra = $koneksi->query("SELECT * FROM mitra ORDER BY nama_mitra");
$all_bayar = $koneksi->query("SELECT * FROM bayar ORDER BY metode_pembayaran");

// Build current query string (without page)
$current_query = $_GET;
unset($current_query['page']);
function build_url($extra = []) {
    global $current_query;
    $q = array_merge($current_query, $extra);
    $q = array_filter($q, fn($v) => $v !== '' && $v !== [] && $v !== null);
    return '?' . http_build_query($q);
}

$now_time = date('H:i:s');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Street Food – Daftar Toko</title>
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
        min-height: 100vh;
        overflow-x: hidden
    }

    /* ── SIDEBAR ── */
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

    .sidebar-nav .nav-section {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-muted);
        padding: 8px 12px 4px
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
        color: #fff;
        flex-shrink: 0
    }

    .user-info {
        flex: 1;
        min-width: 0
    }

    .user-info .name {
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }

    .user-info .role {
        font-size: 11px;
        color: var(--text-muted)
    }

    /* ── MAIN ── */
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

    .search-box {
        flex: 1;
        max-width: 480px;
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
        gap: 14px
    }

    .btn-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 9px 16px;
        color: var(--text-muted);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: .18s;
        font-family: inherit
    }

    .btn-filter:hover,
    .btn-filter.active {
        border-color: var(--accent);
        color: var(--accent)
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

    .topbar-user i {
        font-size: 12px;
        color: var(--text-muted)
    }

    /* CONTENT */
    .content {
        padding: 28px 28px 40px;
        flex: 1
    }

    .page-header {
        margin-bottom: 24px
    }

    .page-header h1 {
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 4px
    }

    .page-header p {
        color: var(--text-muted);
        font-size: 14px
    }

    /* FILTER BAR */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 24px
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: 1.5px solid var(--border);
        background: transparent;
        color: var(--text-muted);
        transition: .18s;
        font-family: inherit;
        text-decoration: none
    }

    .filter-chip:hover {
        border-color: var(--text-muted);
        color: var(--text)
    }

    .filter-chip.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #0f1623
    }

    .filter-chip i {
        font-size: 12px
    }

    .sort-wrap {
        margin-left: auto
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
        cursor: pointer
    }

    .sort-wrap select:focus {
        border-color: var(--accent)
    }

    /* GRID */
    .stores-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 22px;
        margin-bottom: 32px
    }

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
        box-shadow: 0 12px 40px rgba(0, 0, 0, .35)
    }

    .card-img {
        position: relative;
        overflow: hidden
    }

    .card-img img {
        width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 0;
        transition: .3s
    }

    .store-card:hover .card-img img {
        transform: scale(1.04)
    }

    .card-status {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px
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
        bottom: 12px;
        left: 12px;
        background: rgba(34, 197, 94, .15);
        border: 1px solid rgba(34, 197, 94, .35);
        color: var(--green);
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 5px
    }

    .card-body {
        padding: 16px
    }

    .card-body .store-name {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between
    }

    .rating {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 13px;
        font-weight: 700;
        color: var(--accent)
    }

    .rating i {
        font-size: 11px
    }

    .review-count {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 8px
    }

    .review-count i {
        margin-right: 3px
    }

    .card-address {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        gap: 6px;
        align-items: flex-start;
        margin-bottom: 4px
    }

    .card-address i {
        margin-top: 2px;
        flex-shrink: 0;
        color: var(--accent)
    }

    .card-hours {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        gap: 6px;
        align-items: center;
        margin-bottom: 12px
    }

    .card-hours i {
        color: var(--blue)
    }

    .mitra-list {
        display: flex;
        gap: 6px;
        flex-wrap: wrap
    }

    .mitra-badge {
        background: rgba(59, 130, 246, .12);
        border: 1px solid rgba(59, 130, 246, .25);
        color: var(--blue);
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 5px
    }

    .mitra-badge img {
        width: 14px;
        height: 14px;
        object-fit: contain;
        border-radius: 2px
    }

    .no-img {
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, #1f2d4e, #263354);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 36px
    }

    /* PAGINATION */
    .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px
    }

    .pagination a,
    .pagination span {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        border: 1.5px solid var(--border);
        color: var(--text-muted);
        transition: .15s
    }

    .pagination a:hover {
        border-color: var(--accent);
        color: var(--accent)
    }

    .pagination span.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #0f1623
    }

    .pagination .disabled {
        opacity: .3;
        pointer-events: none
    }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--text-muted)
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: .4
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--text)
    }

    .empty-state p {
        font-size: 14px
    }

    /* ── FILTER MODAL ── */
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
        background: var(--sidebar);
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
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        background: var(--sidebar);
        z-index: 1
    }

    .filter-header h3 {
        font-size: 17px;
        font-weight: 800
    }

    .filter-header .close-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: var(--bg);
        border: 1px solid var(--border);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 16px;
        transition: .15s
    }

    .filter-header .close-btn:hover {
        color: var(--text);
        border-color: var(--text-muted)
    }

    .filter-reset {
        color: var(--accent);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px
    }

    .filter-body {
        padding: 20px 24px;
        flex: 1
    }

    .filter-section {
        margin-bottom: 24px
    }

    .filter-section h4 {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .5px;
        margin-bottom: 12px;
        color: var(--text)
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

    .checkbox-item input[type=checkbox] {
        accent-color: var(--accent);
        width: 16px;
        height: 16px;
        cursor: pointer
    }

    .checkbox-item:hover {
        color: var(--text)
    }

    .checkbox-item label {
        cursor: pointer
    }

    .filter-footer {
        padding: 20px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 12px;
        position: sticky;
        bottom: 0;
        background: var(--sidebar)
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
        color: var(--text);
        border-color: var(--text-muted)
    }

    @media(max-width:768px) {
        .sidebar {
            transform: translateX(-100%)
        }

        .main {
            margin-left: 0
        }

        .stores-grid {
            grid-template-columns: 1fr
        }

        .filter-panel {
            width: 100vw
        }
    }
    </style>
</head>

<body>

    <!-- ── SIDEBAR ── -->
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

    <!-- ── MAIN ── -->
    <div class="main">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama menu atau nama toko..."
                    value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="topbar-right">
                <button class="btn-filter" id="btnOpenFilter" onclick="openFilter()">
                    <i class="fas fa-sliders-h"></i> Filter
                    <?php if ($halal_filter || $jam_filter || !empty($mitra_filter) || !empty($bayar_filter) || !empty($rasa_filter) || $harga_min || $harga_max): ?>
                    <span
                        style="background:var(--accent);color:#0f1623;border-radius:50%;width:18px;height:18px;font-size:10px;display:inline-flex;align-items:center;justify-content:center;font-weight:800">✓</span>
                    <?php endif; ?>
                </button>
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

        <!-- CONTENT -->
        <main class="content">
            <div class="page-header">
                <h1>Daftar Toko Street Food</h1>
                <p>Temukan berbagai pilihan street food favoritmu</p>
            </div>

            <!-- FILTER BAR -->
            <form id="mainForm" method="GET" action="">
                <input type="hidden" name="search" id="searchHidden" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="harga_min" id="harga_min_hidden" value="<?= $harga_min ?>">
                <input type="hidden" name="harga_max" id="harga_max_hidden" value="<?= $harga_max ?>">
                <input type="hidden" name="jam" id="jam_hidden" value="<?= htmlspecialchars($jam_filter) ?>">
                <input type="hidden" name="halal" id="halal_hidden" value="<?= htmlspecialchars($halal_filter) ?>">
                <?php foreach ($mitra_filter as $m): ?><input type="hidden" name="mitra[]" class="mitra_hidden"
                    value="<?= (int)$m ?>"><?php endforeach; ?>
                <?php foreach ($bayar_filter as $b): ?><input type="hidden" name="bayar[]" class="bayar_hidden"
                    value="<?= (int)$b ?>"><?php endforeach; ?>
                <?php foreach ($rasa_filter as $r): ?><input type="hidden" name="rasa[]" class="rasa_hidden"
                    value="<?= htmlspecialchars($r) ?>"><?php endforeach; ?>
                <input type="hidden" name="sort" id="sort_hidden" value="<?= htmlspecialchars($sort) ?>">

                <div class="filter-bar">
                    <a href="?"
                        class="filter-chip <?= (!$terbuka && !$halal_filter && empty($mitra_filter) && empty($bayar_filter) && empty($rasa_filter) && !$harga_min && !$harga_max && !$jam_filter) ? 'active' : '' ?>">Semua</a>
                    <a href="<?= build_url(['terbuka' => $terbuka ? '' : '1', 'page' => 1]) ?>"
                        class="filter-chip <?= $terbuka ? 'active' : '' ?>"><i class="fas fa-clock"></i> Terbuka
                        Sekarang</a>
                    <a href="<?= build_url(['halal' => $halal_filter === 'tersertifikasi' ? '' : 'tersertifikasi', 'page' => 1]) ?>"
                        class="filter-chip <?= $halal_filter === 'tersertifikasi' ? 'active' : '' ?>"><i
                            class="fas fa-leaf"></i> Halal</a>
                    <div class="sort-wrap">
                        <select
                            onchange="document.getElementById('sort_hidden').value=this.value;document.getElementById('mainForm').submit()">
                            <option value="" <?= !$sort?'selected':'' ?>>Urutkan</option>
                            <option value="rating_desc" <?= $sort==='rating_desc'?'selected':'' ?>>Rating Tertinggi
                            </option>
                            <option value="rating_asc" <?= $sort==='rating_asc'?'selected':'' ?>>Rating Terendah
                            </option>
                            <option value="nama_az" <?= $sort==='nama_az'?'selected':'' ?>>Nama A-Z</option>
                            <option value="nama_za" <?= $sort==='nama_za'?'selected':'' ?>>Nama Z-A</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- GRID -->
            <?php if (empty($rows)): ?>
            <div class="empty-state">
                <i class="fas fa-store-slash"></i>
                <h3>Tidak ada toko ditemukan</h3>
                <p>Coba ubah filter atau kata kunci pencarian kamu</p>
            </div>
            <?php else: ?>
            <div class="stores-grid">
                <?php foreach ($rows as $row):
            $jam_buka  = $row['jam_buka']  ? substr($row['jam_buka'], 0, 5)  : null;
            $jam_tutup = $row['jam_tutup'] ? substr($row['jam_tutup'], 0, 5) : null;
            $is_buka   = ($jam_buka && $jam_tutup && $now_time >= $row['jam_buka'] && $now_time <= $row['jam_tutup']);
            $mitras    = $mitra_map[$row['id_toko']] ?? [];
            $rating    = $row['rating'] ? number_format($row['rating'], 1) : '–';
        ?>
                <a class="store-card" href="page_menu.php?id_toko=<?= $row['id_toko'] ?>">
                    <div class="card-img">
                        <?php if ($row['foto_outlet']): ?>
                        <img src="../img/pict/<?= $row['foto_outlet']; ?>"
                            alt="<?= htmlspecialchars($row['nama_toko']); ?>"
                            style="width:100%;max-height:220px;object-fit:cover;border-radius:0;">
                        <?php else: ?>
                        <div class="no-img"><i class="fas fa-store"></i></div>
                        <?php endif; ?>
                        <span class="card-status <?= $is_buka ? 'status-buka' : 'status-tutup' ?>">
                            <?= $is_buka ? '● Buka' : '● Tutup' ?>
                        </span>
                        <?php if ($row['status_halal'] === 'tersertifikasi'): ?>
                        <span class="halal-badge"><i class="fas fa-leaf"></i> Halal</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="store-name">
                            <span><?= htmlspecialchars($row['nama_toko']) ?></span>
                            <?php if ($row['rating']): ?>
                            <span class="rating"><i class="fas fa-star"></i> <?= $rating ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($row['lokasi']): ?>
                        <div class="card-address">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars(substr($row['lokasi'], 0, 70)) ?><?= strlen($row['lokasi']) > 70 ? '...' : '' ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($jam_buka): ?>
                        <div class="card-hours">
                            <i class="fas fa-clock"></i>
                            <span><?= $jam_buka ?> – <?= $jam_tutup ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($mitras): ?>
                        <div class="mitra-list">
                            <?php foreach ($mitras as $m): ?>
                            <span class="mitra-badge">
                                <?php if ($m['logo']): ?>
                                <img src="../img/mitra/<?= $m['logo'] ?>"
                                    alt="<?= htmlspecialchars($m['nama_mitra']) ?>">
                                <?php endif; ?>
                                <?= htmlspecialchars($m['nama_mitra']) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="<?= build_url(['page' => $page - 1]) ?>"><i class="fas fa-chevron-left"></i></a>
                <?php else: ?>
                <span class="disabled"><i class="fas fa-chevron-left"></i></span>
                <?php endif; ?>

                <?php
            $range = 2;
            $start = max(1, $page - $range);
            $end   = min($total_pages, $page + $range);
            if ($start > 1) { echo '<a href="' . build_url(['page' => 1]) . '">1</a>'; if ($start > 2) echo '<span style="border:none;width:auto;padding:0 4px">…</span>'; }
            for ($p = $start; $p <= $end; $p++):
                if ($p == $page): echo "<span class='active'>$p</span>";
                else: echo "<a href='" . build_url(['page' => $p]) . "'>$p</a>";
                endif;
            endfor;
            if ($end < $total_pages) { if ($end < $total_pages - 1) echo '<span style="border:none;width:auto;padding:0 4px">…</span>'; echo '<a href="' . build_url(['page' => $total_pages]) . '">' . $total_pages . '</a>'; }
            ?>

                <?php if ($page < $total_pages): ?>
                <a href="<?= build_url(['page' => $page + 1]) ?>"><i class="fas fa-chevron-right"></i></a>
                <?php else: ?>
                <span class="disabled"><i class="fas fa-chevron-right"></i></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <!-- ── FILTER PANEL ── -->
    <div class="overlay" id="overlay" onclick="closeFilter()"></div>
    <div class="filter-panel" id="filterPanel">
        <div class="filter-header">
            <h3>Filter Pencarian</h3>
            <div style="display:flex;align-items:center;gap:12px">
                <a href="?" class="filter-reset"><i class="fas fa-rotate-left"></i> Reset Semua</a>
                <button class="close-btn" onclick="closeFilter()"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="filter-body">

            <!-- HARGA -->
            <div class="filter-section">
                <h4>Harga per Porsi</h4>
                <div class="chip-group">
                    <button type="button" class="chip-opt <?= (!$harga_min && !$harga_max)?'active':'' ?>"
                        onclick="setHarga(0,0)">Semua</button>
                    <button type="button" class="chip-opt <?= ($harga_min==0&&$harga_max==5)?'active':'' ?>"
                        onclick="setHarga(0,5)">&lt; Rp 5.000</button>
                    <button type="button" class="chip-opt <?= ($harga_min==5&&$harga_max==10)?'active':'' ?>"
                        onclick="setHarga(5,10)">Rp 5.000 – Rp 10.000</button>
                    <button type="button" class="chip-opt <?= ($harga_min==10&&$harga_max==15)?'active':'' ?>"
                        onclick="setHarga(10,15)">Rp 10.000 – Rp 15.000</button>
                    <button type="button" class="chip-opt <?= ($harga_min==15&&$harga_max==20)?'active':'' ?>"
                        onclick="setHarga(15,20)">Rp 15.000 – Rp 20.000</button>
                    <button type="button" class="chip-opt <?= ($harga_min==20&&$harga_max==0)?'active':'' ?>"
                        onclick="setHarga(20,0)">&gt; Rp 20.000</button>
                </div>
            </div>

            <!-- JAM OPERASIONAL -->
            <div class="filter-section">
                <h4>Jam Operasional</h4>
                <div class="chip-group">
                    <button type="button" class="chip-opt <?= !$jam_filter?'active':'' ?>"
                        onclick="setJam('')">Semua</button>
                    <button type="button" class="chip-opt <?= $terbuka?'active':'' ?>" onclick="setTerbuka()">Buka
                        Sekarang</button>
                    <button type="button" class="chip-opt <?= $jam_filter==='07-10'?'active':'' ?>"
                        onclick="setJam('07-10')">07:00 – 10:00</button>
                    <button type="button" class="chip-opt <?= $jam_filter==='10-14'?'active':'' ?>"
                        onclick="setJam('10-14')">10:00 – 14:00</button>
                    <button type="button" class="chip-opt <?= $jam_filter==='14-17'?'active':'' ?>"
                        onclick="setJam('14-17')">14:00 – 17:00</button>
                    <button type="button" class="chip-opt <?= $jam_filter==='17-21'?'active':'' ?>"
                        onclick="setJam('17-21')">17:00 – 21:00</button>
                    <button type="button" class="chip-opt <?= $jam_filter==='21-24'?'active':'' ?>"
                        onclick="setJam('21-24')">21:00 – 24:00</button>
                </div>
            </div>

            <!-- STATUS HALAL -->
            <div class="filter-section">
                <h4>Status Halal</h4>
                <div class="chip-group">
                    <button type="button" class="chip-opt <?= !$halal_filter?'active':'' ?>"
                        onclick="setHalal('')">Semua</button>
                    <button type="button" class="chip-opt <?= $halal_filter==='tersertifikasi'?'active':'' ?>"
                        onclick="setHalal('tersertifikasi')">Tersertifikasi</button>
                    <button type="button" class="chip-opt <?= $halal_filter==='belum tersertifikasi'?'active':'' ?>"
                        onclick="setHalal('belum tersertifikasi')">Belum Tersertifikasi</button>
                    <button type="button" class="chip-opt <?= $halal_filter==='non halal'?'active':'' ?>"
                        onclick="setHalal('non halal')">Non Halal</button>
                </div>
            </div>

            <!-- MITRA -->
            <div class="filter-section">
                <h4>Mitra</h4>
                <div class="checkbox-group">
                    <?php $all_mitra->data_seek(0); while ($m = $all_mitra->fetch_assoc()): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" class="cb-mitra" value="<?= $m['id_mitra'] ?>"
                            <?= in_array($m['id_mitra'], $mitra_filter) ? 'checked' : '' ?>>
                        <label><?= htmlspecialchars($m['nama_mitra']) ?></label>
                    </label>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- METODE PEMBAYARAN -->
            <div class="filter-section">
                <h4>Metode Pembayaran</h4>
                <div class="chip-group">
                    <?php $all_bayar->data_seek(0); while ($b = $all_bayar->fetch_assoc()): ?>
                    <button type="button"
                        class="chip-opt cb-bayar <?= in_array($b['id_metode'], $bayar_filter)?'active':'' ?>"
                        data-val="<?= $b['id_metode'] ?>"><?= htmlspecialchars($b['metode_pembayaran']) ?></button>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- KATEGORI RASA -->
            <div class="filter-section">
                <h4>Kategori Rasa</h4>
                <div class="chip-group">
                    <?php foreach (['pedas','asin','manis','berkuah','asam'] as $r): ?>
                    <button type="button" class="chip-opt cb-rasa <?= in_array($r,$rasa_filter)?'active':'' ?>"
                        data-val="<?= $r ?>"><?= ucfirst($r) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
        <div class="filter-footer">
            <button class="btn-cancel" onclick="closeFilter()">Batal</button>
            <button class="btn-apply" onclick="applyFilter()">Terapkan Filter</button>
        </div>
    </div>

    <script>
    // State
    let filterState = {
        harga_min: <?= $harga_min ?>,
        harga_max: <?= $harga_max ?>,
        jam: '<?= addslashes($jam_filter) ?>',
        terbuka: '<?= $terbuka ?>',
        halal: '<?= addslashes($halal_filter) ?>',
        mitra: <?= json_encode(array_map('intval', $mitra_filter)) ?>,
        bayar: <?= json_encode(array_map('intval', $bayar_filter)) ?>,
        rasa: <?= json_encode($rasa_filter) ?>,
    };

    function openFilter() {
        document.getElementById('overlay').classList.add('show');
        document.getElementById('filterPanel').classList.add('show');
    }

    function closeFilter() {
        document.getElementById('overlay').classList.remove('show');
        document.getElementById('filterPanel').classList.remove('show');
    }

    function setHarga(min, max) {
        filterState.harga_min = min;
        filterState.harga_max = max;
        refreshChips();
    }

    function setJam(v) {
        filterState.jam = v;
        filterState.terbuka = '';
        refreshChips();
    }

    function setTerbuka() {
        filterState.terbuka = filterState.terbuka ? '' : '1';
        filterState.jam = '';
        refreshChips();
    }

    function setHalal(v) {
        filterState.halal = v;
        refreshChips();
    }

    function refreshChips() {
        // harga
        document.querySelectorAll('.filter-section:nth-child(1) .chip-opt').forEach(btn => btn.classList.remove(
            'active'));
        // jam
        document.querySelectorAll('.filter-section:nth-child(2) .chip-opt').forEach(btn => btn.classList.remove(
            'active'));
        // halal
        document.querySelectorAll('.filter-section:nth-child(3) .chip-opt').forEach(btn => btn.classList.remove(
            'active'));
    }

    // Toggle bayar chips
    document.querySelectorAll('.cb-bayar').forEach(btn => {
        btn.addEventListener('click', function() {
            const v = parseInt(this.dataset.val);
            const idx = filterState.bayar.indexOf(v);
            if (idx > -1) filterState.bayar.splice(idx, 1);
            else filterState.bayar.push(v);
            this.classList.toggle('active');
        });
    });
    // Toggle rasa chips
    document.querySelectorAll('.cb-rasa').forEach(btn => {
        btn.addEventListener('click', function() {
            const v = this.dataset.val;
            const idx = filterState.rasa.indexOf(v);
            if (idx > -1) filterState.rasa.splice(idx, 1);
            else filterState.rasa.push(v);
            this.classList.toggle('active');
        });
    });

    function applyFilter() {
        const form = document.getElementById('mainForm');
        document.getElementById('harga_min_hidden').value = filterState.harga_min;
        document.getElementById('harga_max_hidden').value = filterState.harga_max;
        document.getElementById('jam_hidden').value = filterState.jam;
        document.getElementById('halal_hidden').value = filterState.halal;
        document.getElementById('searchHidden').value = document.getElementById('searchInput').value;

        // Remove old mitra/bayar/rasa inputs
        document.querySelectorAll('.mitra_hidden,.bayar_hidden,.rasa_hidden').forEach(e => e.remove());

        // Mitra from checkboxes
        document.querySelectorAll('.cb-mitra:checked').forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'mitra[]';
            inp.className = 'mitra_hidden';
            inp.value = cb.value;
            form.appendChild(inp);
        });
        // Bayar
        filterState.bayar.forEach(v => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'bayar[]';
            inp.className = 'bayar_hidden';
            inp.value = v;
            form.appendChild(inp);
        });
        // Rasa
        filterState.rasa.forEach(v => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'rasa[]';
            inp.className = 'rasa_hidden';
            inp.value = v;
            form.appendChild(inp);
        });
        // Terbuka
        let terbuka_inp = form.querySelector('[name="terbuka"]');
        if (filterState.terbuka) {
            if (!terbuka_inp) {
                terbuka_inp = document.createElement('input');
                terbuka_inp.type = 'hidden';
                terbuka_inp.name = 'terbuka';
                form.appendChild(terbuka_inp);
            }
            terbuka_inp.value = '1';
        } else if (terbuka_inp) terbuka_inp.remove();

        form.submit();
    }

    // Search with debounce
    let searchTimer;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            document.getElementById('searchHidden').value = this.value;
            document.getElementById('mainForm').submit();
        }, 500);
    });
    </script>
</body>

</html>