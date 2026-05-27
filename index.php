<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Street Food Bandung</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap"
        rel="stylesheet">
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">🍜</div>
            <div>
                <div class="logo-text">STREET FOOD</div>
                <div class="logo-sub">Bandung</div>
            </div>
        </div>
        <nav class="nav">
            <div class="nav-section">Menu Utama</div>
            <div class="nav-item" onclick="showPage('dashboard')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                Dashboard
            </div>
            <div class="nav-item active" id="nav-toko" onclick="showPage('toko')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    <polyline points="9,22 9,12 15,12 15,22" />
                </svg>
                Toko
            </div>
            <div class="nav-item" onclick="showPage('menu-page-static')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                    <rect x="9" y="3" width="6" height="4" rx="1" />
                </svg>
                Menu
            </div>
            <div class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="5" />
                    <path d="M3 21v-1a7 7 0 0114 0v1" />
                </svg>
                Mitra
            </div>
            <div class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2" />
                    <line x1="2" y1="10" x2="22" y2="10" />
                </svg>
                Metode Pembayaran
            </div>
            <div class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" />
                </svg>
                Kategori Rasa
            </div>
            <div class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path
                        d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                </svg>
                Bahan Baku
            </div>
            <div class="nav-section">Laporan</div>
            <div class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10" />
                    <line x1="12" y1="20" x2="12" y2="4" />
                    <line x1="6" y1="20" x2="6" y2="14" />
                </svg>
                Laporan & Statistik
            </div>
            <div class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                Info & Analisis
            </div>
            <div class="nav-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>
                Pengaturan
            </div>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-card">
                <div class="user-ava">A</div>
                <div>
                    <div class="user-name">Admin</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="search-wrap">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" id="searchInput" placeholder="Cari nama menu atau nama toko..."
                    oninput="handleSearch()">
            </div>
            <button class="filter-btn" id="filterToggle" onclick="toggleFilter()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46" />
                </svg>
                Filter
            </button>
            <span class="realtime-clock" id="clock"></span>
        </div>

        <!-- TOKO PAGE -->
        <div class="content">
            <div class="page active" id="page-toko">
                <div class="page-header">
                    <div class="page-title">Daftar Toko Street Food</div>
                    <div class="page-sub">Temukan berbagai pilihan street food favoritmu di Bandung</div>
                </div>

                <div class="quick-filters">
                    <button class="qf-btn active" data-qf="semua" onclick="setQuickFilter('semua',this)">Semua</button>
                    <button class="qf-btn" data-qf="buka" onclick="setQuickFilter('buka',this)">
                        <span class="qf-dot" style="color:#4ade80"></span>Terbuka Sekarang
                    </button>
                    <button class="qf-btn" data-qf="halal" onclick="setQuickFilter('halal',this)">
                        <span class="qf-dot qf-halal"></span>Halal
                    </button>
                    <div class="sort-wrap">
                        <select class="sort-sel" id="sortSel" onchange="applyFilters()">
                            <option value="">Urutkan</option>
                            <option value="name_az">Nama A-Z</option>
                            <option value="name_za">Nama Z-A</option>
                            <option value="jam_buka">Jam Buka Paling Awal</option>
                        </select>
                    </div>
                </div>

                <div class="toko-grid" id="tokoGrid"></div>
                <div class="pagination" id="pagination"></div>
            </div>

            <!-- MENU DETAIL PAGE -->
            <div class="page" id="page-menu">
                <button class="back-btn" onclick="backToToko()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="15,18 9,12 15,6" />
                    </svg>
                    Kembali ke Daftar Toko
                </button>
                <div id="menuContent"></div>
            </div>

            <!-- DASHBOARD PAGE -->
            <div class="page" id="page-dashboard">
                <div class="page-header">
                    <div class="page-title">Dashboard</div>
                    <div class="page-sub">Selamat datang di panel admin Street Food Bandung</div>
                </div>
                <div
                    style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-top:8px;">
                    <div
                        style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px;">
                        <div style="font-size:28px;font-weight:800;font-family:'Syne',sans-serif;color:var(--accent)"
                            id="dash-total">31</div>
                        <div style="font-size:13px;color:var(--text3);margin-top:4px">Total Toko</div>
                    </div>
                    <div
                        style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px;">
                        <div style="font-size:28px;font-weight:800;font-family:'Syne',sans-serif;color:#4ade80"
                            id="dash-buka">-</div>
                        <div style="font-size:13px;color:var(--text3);margin-top:4px">Toko Buka Sekarang</div>
                    </div>
                    <div
                        style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px;">
                        <div style="font-size:28px;font-weight:800;font-family:'Syne',sans-serif;color:#60a5fa">17</div>
                        <div style="font-size:13px;color:var(--text3);margin-top:4px">Item Menu</div>
                    </div>
                    <div
                        style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px;">
                        <div style="font-size:28px;font-weight:800;font-family:'Syne',sans-serif;color:#c084fc">3</div>
                        <div style="font-size:13px;color:var(--text3);margin-top:4px">Mitra Platform</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FILTER PANEL -->
    <div class="filter-overlay" id="filterOverlay" onclick="closeFilter(event)">
        <div class="filter-panel" onclick="e=>e.stopPropagation()">
            <div class="filter-head">
                <div class="filter-title">Filter Pencarian</div>
                <div style="display:flex;align-items:center;gap:12px">
                    <span class="filter-reset" onclick="resetFilters()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="23,4 23,10 17,10" />
                            <path d="M20.49 15a9 9 0 11-2.12-9.36L23 10" />
                        </svg>
                        Reset Semua
                    </span>
                    <button class="close-btn" onclick="toggleFilter()">✕</button>
                </div>
            </div>
            <div class="filter-body">
                <!-- Jam Operasional -->
                <div class="filter-section">
                    <div class="filter-label">Jam Operasional</div>
                    <div class="chip-grid">
                        <div class="chip active" data-jam="semua" onclick="setJamChip(this)">Semua</div>
                        <div class="chip" data-jam="buka" onclick="setJamChip(this)">Buka Sekarang</div>
                        <div class="chip" data-jam="07-10" onclick="setJamChip(this)">07:00 - 10:00</div>
                        <div class="chip" data-jam="10-14" onclick="setJamChip(this)">10:00 - 14:00</div>
                        <div class="chip" data-jam="14-17" onclick="setJamChip(this)">14:00 - 17:00</div>
                        <div class="chip" data-jam="17-21" onclick="setJamChip(this)">17:00 - 21:00</div>
                        <div class="chip" data-jam="21-24" onclick="setJamChip(this)">21:00 - 24:00</div>
                    </div>
                </div>
                <!-- Status Halal -->
                <div class="filter-section">
                    <div class="filter-label">Status Halal</div>
                    <div class="chip-grid">
                        <div class="chip active" data-halal="semua" onclick="setHalalChip(this)">Semua</div>
                        <div class="chip" data-halal="tersertifikasi" onclick="setHalalChip(this)">Tersertifikasi</div>
                        <div class="chip" data-halal="belum tersertifikasi" onclick="setHalalChip(this)">Belum
                            Tersertifikasi</div>
                        <div class="chip" data-halal="non halal" onclick="setHalalChip(this)">Non Halal</div>
                    </div>
                </div>
                <!-- Mitra -->
                <div class="filter-section">
                    <div class="filter-label">Mitra</div>
                    <div class="check-grid">
                        <label class="check-item"><input type="checkbox" class="mitra-check" value="GoFood"><span
                                class="check-box"></span><span class="check-label">GoFood</span></label>
                        <label class="check-item"><input type="checkbox" class="mitra-check" value="GrabFood"><span
                                class="check-box"></span><span class="check-label">GrabFood</span></label>
                        <label class="check-item"><input type="checkbox" class="mitra-check" value="ShopeeFood"><span
                                class="check-box"></span><span class="check-label">ShopeeFood</span></label>
                    </div>
                </div>
            </div>
            <div class="filter-footer">
                <button class="btn-cancel" onclick="toggleFilter()">Batal</button>
                <button class="btn-apply" onclick="applyFiltersFromPanel()">Terapkan Filter</button>
            </div>
        </div>
    </div>

    <script>
    // ========== DATA ==========
    const tokoData = [{
            id: 1,
            nama: 'Toko Contoh',
            foto: 'img/pict/1.jpg',
            lokasi: 'Jl. Contoh No.1, Bandung',
            jam_buka: '07:00',
            jam_tutup: '22:00',
            halal: 'belum tersertifikasi',
            rating: null,
            mitra: ['GoFood', 'GrabFood', 'ShopeeFood']
        },
        {
            id: 2,
            nama: 'MIE BASO RESTORJA DO\'EL',
            foto: 'img/pict/2.jpg',
            lokasi: 'Jl. Geger Kalong Girang No.65, Kota Bandung',
            jam_buka: '08:30',
            jam_tutup: '21:30',
            halal: 'tersertifikasi',
            rating: 4.5,
            mitra: ['GoFood']
        },
        {
            id: 3,
            nama: 'Mie Ayam Pedas Sugih',
            foto: 'img/pict/3.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.31, Kota Bandung',
            jam_buka: '10:30',
            jam_tutup: '20:30',
            halal: 'tersertifikasi',
            rating: 4.3,
            mitra: ['GrabFood']
        },
        {
            id: 4,
            nama: 'AYAM JUBER - GERLONG',
            foto: 'img/pict/4.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.17, Kota Bandung',
            jam_buka: '07:30',
            jam_tutup: '22:00',
            halal: 'tersertifikasi',
            rating: 4.6,
            mitra: ['GoFood', 'GrabFood']
        },
        {
            id: 5,
            nama: 'Warkop Sukarasa Gerlong',
            foto: 'img/pict/5.jpg',
            lokasi: 'Jl. Cibeunying Kolot No.87, Kota Bandung',
            jam_buka: null,
            jam_tutup: null,
            halal: 'belum tersertifikasi',
            rating: 4.1,
            mitra: []
        },
        {
            id: 6,
            nama: 'Pawon Teteh Gegerkalong',
            foto: 'img/pict/6.jpg',
            lokasi: 'Jl. Gegerkalong Tengah, Kota Bandung',
            jam_buka: '09:00',
            jam_tutup: '21:30',
            halal: 'belum tersertifikasi',
            rating: 4.2,
            mitra: ['ShopeeFood']
        },
        {
            id: 7,
            nama: 'Hayang Thai Tea Koramil',
            foto: 'img/pict/7.jpg',
            lokasi: 'Jl. Guru Gantangan, Isola, Kota Bandung',
            jam_buka: '11:00',
            jam_tutup: '21:00',
            halal: 'tersertifikasi',
            rating: 4.4,
            mitra: ['GoFood']
        },
        {
            id: 8,
            nama: 'AndisPIZZA',
            foto: 'img/pict/8.jpg',
            lokasi: 'Gegerkalong, Kota Bandung',
            jam_buka: null,
            jam_tutup: null,
            halal: 'tersertifikasi',
            rating: 4.0,
            mitra: ['GrabFood']
        },
        {
            id: 9,
            nama: 'Kababoss Geger Kalong',
            foto: 'img/pict/9.jpg',
            lokasi: 'Jl. Gegerkalong Girang, Kota Bandung',
            jam_buka: '09:00',
            jam_tutup: '22:00',
            halal: 'tersertifikasi',
            rating: 4.7,
            mitra: ['GoFood', 'ShopeeFood']
        },
        {
            id: 10,
            nama: 'AYAMIN',
            foto: 'img/pict/10.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.20C, Kota Bandung',
            jam_buka: '10:00',
            jam_tutup: '23:59',
            halal: 'tersertifikasi',
            rating: 4.5,
            mitra: ['GrabFood']
        },
        {
            id: 11,
            nama: 'HAPPY Nasi Telor',
            foto: 'img/pict/11.jpg',
            lokasi: 'Jl. Gegerkalong Girang, Kota Bandung',
            jam_buka: '05:25',
            jam_tutup: '13:00',
            halal: 'tersertifikasi',
            rating: 4.8,
            mitra: ['GoFood']
        },
        {
            id: 12,
            nama: 'Waroeng BANG BOIM',
            foto: 'img/pict/12.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.36, Kota Bandung',
            jam_buka: '17:00',
            jam_tutup: '22:00',
            halal: 'tersertifikasi',
            rating: 4.3,
            mitra: ['ShopeeFood']
        },
        {
            id: 13,
            nama: 'Ayam Tulang Lunak Pawon Sesambelan',
            foto: 'img/pict/13.jpg',
            lokasi: 'Jl. Gegerkalong Girang, Kota Bandung',
            jam_buka: '08:00',
            jam_tutup: '15:00',
            halal: 'tersertifikasi',
            rating: 4.6,
            mitra: ['GoFood', 'GrabFood']
        },
        {
            id: 14,
            nama: 'Pisang Keju & Goreng Tanduk Isola',
            foto: 'img/pict/14.jpg',
            lokasi: 'Jl. Gegerkalong Girang, Isola, Kota Bandung',
            jam_buka: '07:00',
            jam_tutup: '21:30',
            halal: 'tersertifikasi',
            rating: 4.2,
            mitra: ['GoFood']
        },
        {
            id: 15,
            nama: "GG Juicy 'n Fruity",
            foto: 'img/pict/15.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.23, Kota Bandung',
            jam_buka: '10:00',
            jam_tutup: '19:00',
            halal: 'tersertifikasi',
            rating: 4.1,
            mitra: ['GrabFood']
        },
        {
            id: 16,
            nama: 'Molen Aneka Rasa',
            foto: 'img/pict/16.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.101a, Kota Bandung',
            jam_buka: '07:00',
            jam_tutup: '17:00',
            halal: 'tersertifikasi',
            rating: 4.0,
            mitra: ['ShopeeFood']
        },
        {
            id: 17,
            nama: 'Megumi Daifuku Mochi',
            foto: 'img/pict/17.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.99, Kota Bandung',
            jam_buka: '11:15',
            jam_tutup: '22:00',
            halal: 'tersertifikasi',
            rating: 4.4,
            mitra: ['GoFood']
        },
        {
            id: 18,
            nama: 'Alfathir Frozen Food',
            foto: 'img/pict/18.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.119, Kota Bandung',
            jam_buka: '14:00',
            jam_tutup: '23:00',
            halal: 'tersertifikasi',
            rating: 4.2,
            mitra: ['GrabFood']
        },
        {
            id: 19,
            nama: 'Ayam Geprek Bejeuk Gerlong',
            foto: 'img/pict/19.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.123, Kota Bandung',
            jam_buka: '11:00',
            jam_tutup: '22:00',
            halal: 'tersertifikasi',
            rating: 4.5,
            mitra: ['GoFood', 'ShopeeFood']
        },
        {
            id: 20,
            nama: 'Warung Nasi Padang 88 Uni Angel',
            foto: 'img/pict/20.jpg',
            lokasi: 'Jl. Gegerkalong Girang, Kota Bandung',
            jam_buka: '00:00',
            jam_tutup: '23:59',
            halal: 'tersertifikasi',
            rating: 4.3,
            mitra: ['GoFood']
        },
        {
            id: 21,
            nama: 'Republic Kebab Premium',
            foto: 'img/pict/21.jpg',
            lokasi: 'Jl. Gegerkalong Girang, Kota Bandung',
            jam_buka: '00:00',
            jam_tutup: '23:59',
            halal: 'tersertifikasi',
            rating: 4.6,
            mitra: ['GrabFood']
        },
        {
            id: 22,
            nama: 'Chocolate Changer Gegerkalong',
            foto: 'img/pict/22.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.53A, Kota Bandung',
            jam_buka: '09:30',
            jam_tutup: '20:45',
            halal: 'tersertifikasi',
            rating: 4.4,
            mitra: ['ShopeeFood']
        },
        {
            id: 23,
            nama: 'Ayam Geprek Bebas, Gegerkalong',
            foto: 'img/pict/23.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.33, Kota Bandung',
            jam_buka: '08:45',
            jam_tutup: '21:45',
            halal: 'tersertifikasi',
            rating: 4.5,
            mitra: ['GoFood']
        },
        {
            id: 24,
            nama: 'Jus Egan 71',
            foto: 'img/pict/24.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.71, Kota Bandung',
            jam_buka: '07:00',
            jam_tutup: '17:30',
            halal: 'tersertifikasi',
            rating: 4.0,
            mitra: ['GrabFood']
        },
        {
            id: 25,
            nama: 'Rumah Makan Padang Rajo Bungsu',
            foto: 'img/pict/25.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.75, Kota Bandung',
            jam_buka: '10:00',
            jam_tutup: '22:00',
            halal: 'tersertifikasi',
            rating: 4.3,
            mitra: ['GoFood', 'GrabFood']
        },
        {
            id: 26,
            nama: 'Kantin 77',
            foto: 'img/pict/26.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.77, Kota Bandung',
            jam_buka: '07:00',
            jam_tutup: '21:00',
            halal: 'tersertifikasi',
            rating: 4.2,
            mitra: ['ShopeeFood']
        },
        {
            id: 27,
            nama: 'GEBROS Gegerkalong',
            foto: 'img/pict/27.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.44, Kota Bandung',
            jam_buka: '10:00',
            jam_tutup: '20:00',
            halal: 'tersertifikasi',
            rating: 4.1,
            mitra: ['GoFood']
        },
        {
            id: 28,
            nama: 'ARA FRIED CHICKEN PLACE',
            foto: 'img/pict/28.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.44, Kota Bandung',
            jam_buka: '09:30',
            jam_tutup: '21:00',
            halal: 'tersertifikasi',
            rating: 4.3,
            mitra: ['GrabFood']
        },
        {
            id: 29,
            nama: 'Gerlong Dimsum',
            foto: 'img/pict/29.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.48, Kota Bandung',
            jam_buka: '11:00',
            jam_tutup: '20:30',
            halal: 'tersertifikasi',
            rating: 4.4,
            mitra: ['GoFood']
        },
        {
            id: 30,
            nama: 'RM Padang Maju Jaya',
            foto: 'img/pict/30.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.72, Kota Bandung',
            jam_buka: '09:00',
            jam_tutup: '21:45',
            halal: 'tersertifikasi',
            rating: 4.2,
            mitra: ['ShopeeFood', 'GrabFood']
        },
        {
            id: 31,
            nama: 'A.I. Drinks',
            foto: 'img/pict/31.jpg',
            lokasi: 'Jl. Gegerkalong Girang No.95, Kota Bandung',
            jam_buka: '13:00',
            jam_tutup: '21:00',
            halal: 'tersertifikasi',
            rating: 4.1,
            mitra: ['GoFood']
        }
    ];

    // Menu mapped by toko id (from SQL - using toko id 1 as sample with real data)
    const menuData = {
        1: [{
            id: 1,
            nama: 'Contoh Menu',
            foto: 'img/menu/1.jpg',
            deskripsi: 'Menu contoh pertama',
            harga: 15000,
            rasa: 'pedas',
            kategori: 'Makanan berat'
        }, ],
        // Mie Baso (toko 2) - sample menu
        2: [{
                id: 2,
                nama: 'Mie Yamin Manis Komplit',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/b12016f4-f22e-47f1-81cf-28f1aec94c21_3dfc5855-c25a-4c7d-bf6c-1442337590d5_Go-Biz_20200411_191453.jpeg?auto=format',
                deskripsi: 'Yamin Manis + BASO + PANGSIT KUAH + CEKER',
                harga: 25000,
                rasa: null,
                kategori: 'Makanan berat'
            },
            {
                id: 3,
                nama: 'Mie Yamin Asin Komplit',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/8df8ba2a-2606-4115-9588-9c9f43a731ce_0f9af109-cef9-426c-ba52-a6a638f04179_Go-Biz_20200411_191433.jpeg?auto=format',
                deskripsi: 'Mie Yamin + BASO + PANGSIT KUAH + CEKER',
                harga: 25000,
                rasa: null,
                kategori: 'Makanan berat'
            },
            {
                id: 4,
                nama: 'Yahun Manis Komplit',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/058dadc5-7eaa-4526-a5ea-cc675027301a_e109e954-6650-4e11-99de-f0ef9d5d4f30_Go-Biz_20200411_191346.jpeg?auto=format',
                deskripsi: 'Yahun (Yamin Bihun) Manis + BASO + PANGSIT KUAH + CEKER',
                harga: 25000,
                rasa: null,
                kategori: 'Makanan berat'
            },
            {
                id: 5,
                nama: 'Yahun Asin Komplit',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/4fc05983-9ceb-478c-8ce8-678273cfb952_2ff66768-3a93-46b2-b47a-b238727321d9_Go-Biz_20200411_191409.jpeg?auto=format',
                deskripsi: 'Yahun (Yamin Bihun) Asin + BASO + PANGSIT KUAH + CEKER',
                harga: 25000,
                rasa: null,
                kategori: 'Makanan berat'
            },
            {
                id: 6,
                nama: 'Mie Yamin Manis Polos',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/b7b6bbce-1d00-4175-8bfd-e42718bcba32_1a10f09e-e8fc-4cab-ace5-277b94b839e0_Go-Biz_20200411_190957.jpeg?auto=format',
                deskripsi: 'Mie Yamin manis polos + Ditaburi Ayam Cingcang',
                harga: 15000,
                rasa: null,
                kategori: 'Makanan berat'
            },
            {
                id: 7,
                nama: 'Mie Yamin Asin Polos',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/13b5e046-f080-43f9-a5f8-977da1eef41b_51d92be2-56bc-4a63-8823-dbf7fbc0fe66_Go-Biz_20200411_191027.jpeg?auto=format',
                deskripsi: 'Mie Yamin asin polos + Ditaburi Ayam Cingcang',
                harga: 15000,
                rasa: null,
                kategori: 'Makanan berat'
            },
            {
                id: 8,
                nama: 'Yamin Bihun Manis Polos',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/e35ab707-403e-4f2b-b3b2-13c457282ebb_17567604-1aee-434e-be82-44976e399571_Go-Biz_20200411_191050.jpeg?auto=format',
                deskripsi: 'Yamin BIHUN + Ditaburi Ayam Cingcang',
                harga: 15000,
                rasa: null,
                kategori: 'Makanan berat'
            },
            {
                id: 9,
                nama: 'Yamin Bihun Asin Polos',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/812262ac-2534-403e-b2c1-5210db9e47b7_6072eead-3f41-4a0a-a910-bdbe21df8ff4_Go-Biz_20200411_191116.jpeg?auto=format',
                deskripsi: 'Yamin BIHUN + Ditaburi Ayam Cingcang',
                harga: 15000,
                rasa: null,
                kategori: 'Makanan berat'
            },
            {
                id: 10,
                nama: 'Baso + Pangsit Kuah',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/c9ea8a7d-06ac-45ab-aec5-c460b936957f_Go-Biz_20221218_105247.jpeg?auto=format',
                deskripsi: '6 baso sapi asli + 4 pangsit kuah isi ayam',
                harga: 22000,
                rasa: 'berkuah',
                kategori: 'Makanan berat'
            },
            {
                id: 11,
                nama: 'Baso Polos',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/13c7667d-f798-4fa7-8f4d-0fc121ea4a46_Go-Biz_20221218_105200.jpeg?auto=format',
                deskripsi: '10pcs baso sapi asli + kuah dan sayur',
                harga: 20000,
                rasa: 'berkuah',
                kategori: 'Makanan berat'
            },
            {
                id: 12,
                nama: 'Pangsit Kuah Isi Ayam',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/327765fa-6332-47f1-9ccb-cef75bdfbf3e_7b0a516a-0b49-4bad-a279-6fb7356d1622_Go-Biz_20200411_191231.jpeg?auto=format',
                deskripsi: '10pcs pangsit kuah isi ayam',
                harga: 20000,
                rasa: 'berkuah',
                kategori: 'Makanan berat'
            },
            {
                id: 13,
                nama: 'Baso Kecil',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/667c63f1-a592-42e1-9948-c8a9800839c4_dbfd1b45-c75c-477d-8959-917f281171c6_Go-Biz_20200411_190757.jpeg?auto=format',
                deskripsi: '1 buah baso sapi murni',
                harga: 2000,
                rasa: null,
                kategori: 'Cemilan'
            },
            {
                id: 14,
                nama: 'Pangsit Basah',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/3605f23d-8ce1-4450-a539-f5967de9885b_c1030f59-246b-4586-b186-9fced2d831e5_Go-Biz_20200411_190742.jpeg?auto=format',
                deskripsi: '1 buah pangsit kuah isi ayam special',
                harga: 2000,
                rasa: null,
                kategori: 'Cemilan'
            },
            {
                id: 15,
                nama: 'Ceker Ayam Lunak',
                foto: 'https://i.gojekapi.com/darkroom/gofood-indonesia/v2/images/uploads/664e0f70-e77d-4d86-9e0a-17d09121816c_b9ff1c91-5255-4beb-b4b5-dbdb039d52f3_Go-Biz_20200411_190705.jpeg?auto=format',
                deskripsi: '1-2 buah ceker ayam lunak',
                harga: 2000,
                rasa: null,
                kategori: 'Cemilan'
            },
            {
                id: 16,
                nama: 'Mie Jebew Komplit',
                foto: '',
                deskripsi: 'Mie dengan sambal chili oil, isi pangsit 1 baso 2',
                harga: 25000,
                rasa: 'pedas',
                kategori: 'Makanan berat'
            },
            {
                id: 17,
                nama: 'Mie Jebew Polos',
                foto: '',
                deskripsi: 'Mie dengan sambal chili oil',
                harga: 18000,
                rasa: 'pedas',
                kategori: 'Makanan berat'
            },
        ]
    };

    // Food emojis for placeholder
    const foodEmojis = ['🍜', '🍲', '🥘', '🍛', '🥙', '🌮', '🍱', '🥗', '🍝', '🥞', '🫕', '🥓', '🍗', '🍖', '🥚', '🧆',
        '🥜', '🫙'
    ];
    const storeEmojis = ['🏪', '🏬', '🛖', '⛺', '🏠', '🚐', '🎪', '🏚'];

    // ========== STATE ==========
    let currentPage = 1;
    const PER_PAGE = 9;
    let activeQuickFilter = 'semua';
    let filterState = {
        jam: 'semua',
        halal: 'semua',
        mitra: []
    };
    let searchQuery = '';

    // ========== TIME ==========
    function getNow() {
        const now = new Date();
        return {
            h: now.getHours(),
            m: now.getMinutes(),
            totalMin: now.getHours() * 60 + now.getMinutes()
        };
    }

    function isOpen(toko) {
        if (!toko.jam_buka || !toko.jam_tutup) return false;
        const {
            totalMin
        } = getNow();
        const [bh, bm] = toko.jam_buka.split(':').map(Number);
        const [th, tm] = toko.jam_tutup.split(':').map(Number);
        const bukaMin = bh * 60 + bm,
            tutupMin = th * 60 + tm;
        if (tutupMin < bukaMin) return totalMin >= bukaMin || totalMin < tutupMin;
        return totalMin >= bukaMin && totalMin < tutupMin;
    }

    function updateClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('clock').textContent =
            `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())} WIB`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ========== FILTERS ==========
    function handleSearch() {
        searchQuery = document.getElementById('searchInput').value.toLowerCase();
        currentPage = 1;
        renderToko();
    }

    function setQuickFilter(val, btn) {
        activeQuickFilter = val;
        document.querySelectorAll('.qf-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentPage = 1;
        renderToko();
    }

    function setJamChip(el) {
        document.querySelectorAll('[data-jam]').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        filterState.jam = el.dataset.jam;
    }

    function setHalalChip(el) {
        document.querySelectorAll('[data-halal]').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        filterState.halal = el.dataset.halal;
    }

    function applyFiltersFromPanel() {
        filterState.mitra = [...document.querySelectorAll('.mitra-check:checked')].map(c => c.value);
        toggleFilter();
        currentPage = 1;
        renderToko();
        // update filter button style
        const hasFilter = filterState.jam !== 'semua' || filterState.halal !== 'semua' || filterState.mitra.length > 0;
        document.getElementById('filterToggle').classList.toggle('active-filter', hasFilter);
    }

    function applyFilters() {
        currentPage = 1;
        renderToko();
    }

    function resetFilters() {
        filterState = {
            jam: 'semua',
            halal: 'semua',
            mitra: []
        };
        document.querySelectorAll('[data-jam]').forEach(c => c.classList.remove('active'));
        document.querySelector('[data-jam="semua"]').classList.add('active');
        document.querySelectorAll('[data-halal]').forEach(c => c.classList.remove('active'));
        document.querySelector('[data-halal="semua"]').classList.add('active');
        document.querySelectorAll('.mitra-check').forEach(c => c.checked = false);
    }

    function getFilteredToko() {
        let data = [...tokoData];

        // Search
        if (searchQuery) {
            data = data.filter(t => t.nama.toLowerCase().includes(searchQuery));
        }

        // Quick filter
        if (activeQuickFilter === 'buka') data = data.filter(t => isOpen(t));
        if (activeQuickFilter === 'halal') data = data.filter(t => t.halal === 'tersertifikasi');

        // Jam filter
        if (filterState.jam === 'buka') data = data.filter(t => isOpen(t));
        else if (filterState.jam === '07-10') data = data.filter(t => t.jam_buka && timeInRange(t.jam_buka, '07:00',
            '10:00'));
        else if (filterState.jam === '10-14') data = data.filter(t => t.jam_buka && timeInRange(t.jam_buka, '10:00',
            '14:00'));
        else if (filterState.jam === '14-17') data = data.filter(t => t.jam_buka && timeInRange(t.jam_buka, '14:00',
            '17:00'));
        else if (filterState.jam === '17-21') data = data.filter(t => t.jam_buka && timeInRange(t.jam_buka, '17:00',
            '21:00'));
        else if (filterState.jam === '21-24') data = data.filter(t => t.jam_buka && timeInRange(t.jam_buka, '21:00',
            '24:00'));

        // Halal filter
        if (filterState.halal !== 'semua') data = data.filter(t => t.halal === filterState.halal);

        // Mitra filter
        if (filterState.mitra.length > 0) data = data.filter(t => filterState.mitra.some(m => t.mitra.includes(m)));

        // Sort
        const sort = document.getElementById('sortSel').value;
        if (sort === 'name_az') data.sort((a, b) => a.nama.localeCompare(b.nama));
        if (sort === 'name_za') data.sort((a, b) => b.nama.localeCompare(a.nama));
        if (sort === 'jam_buka') data.sort((a, b) => {
            if (!a.jam_buka) return 1;
            if (!b.jam_buka) return -1;
            return a.jam_buka.localeCompare(b.jam_buka);
        });

        return data;
    }

    function timeInRange(jam, from, to) {
        const toMin = t => {
            const [h, m] = t.split(':').map(Number);
            return h * 60 + m;
        };
        const jamMin = toMin(jam),
            fromMin = toMin(from),
            toMin2 = toMin(to);
        return jamMin >= fromMin && jamMin < toMin2;
    }

    // ========== RENDER TOKO ==========
    function renderToko() {
        const filtered = getFilteredToko();
        const total = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if (currentPage > totalPages) currentPage = 1;
        const slice = filtered.slice((currentPage - 1) * PER_PAGE, currentPage * PER_PAGE);

        const grid = document.getElementById('tokoGrid');
        if (slice.length === 0) {
            grid.innerHTML =
                `<div class="empty-state" style="grid-column:1/-1"><span class="emoji">🔍</span><h3>Toko Tidak Ditemukan</h3><p>Coba ubah kata kunci atau filter pencarian</p></div>`;
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        grid.innerHTML = slice.map((toko, i) => renderTokoCard(toko, i)).join('');
        renderPagination(totalPages);

        // update dashboard
        const bukaCount = tokoData.filter(t => isOpen(t)).length;
        const dashBuka = document.getElementById('dash-buka');
        if (dashBuka) dashBuka.textContent = bukaCount;
    }

    function renderTokoCard(toko, idx) {
        const buka = isOpen(toko);
        const emoji = storeEmojis[toko.id % storeEmojis.length];
        const imgSrc = toko.foto;
        const imgHtml = `
    <div class="toko-img-wrap">
      <img class="toko-img" src="${imgSrc}" alt="${toko.nama}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="toko-img-placeholder" style="display:none">${emoji}</div>
      <div class="status-badge ${buka?'status-buka':'status-tutup'}">${buka?'Buka':'Tutup'}</div>
      ${toko.halal==='tersertifikasi'?`<div class="halal-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Halal</div>`:''}
    </div>
  `;

        const jam = toko.jam_buka && toko.jam_tutup ?
            `${toko.jam_buka} - ${toko.jam_tutup}` :
            'Jam buka tidak tersedia';

        const mitraHtml = toko.mitra.map(m => {
            const cls = m === 'GoFood' ? 'mitra-gofood' : m === 'GrabFood' ? 'mitra-grabfood' :
                'mitra-shopeefood';
            return `<span class="mitra-badge ${cls}">${m}</span>`;
        }).join('');

        const ratingHtml = toko.rating ?
            `<div class="toko-rating"><svg viewBox="0 0 24 24"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>${toko.rating.toFixed(1)}</div>` :
            `<div class="toko-rating" style="color:var(--text3)">—</div>`;

        return `
    <div class="toko-card" onclick="openToko(${toko.id})" style="animation-delay:${idx*0.04}s">
      ${imgHtml}
      <div class="toko-info">
        <div class="toko-name-row">
          <div class="toko-name">${toko.nama}</div>
          ${ratingHtml}
        </div>
        <div class="toko-loc">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;flex-shrink:0;margin-top:2px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span>${toko.lokasi}</span>
        </div>
        <div class="toko-jam">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          ${jam}
        </div>
        <div class="mitra-row">${mitraHtml||'<span style="font-size:11px;color:var(--text3)">Tidak ada mitra</span>'}</div>
      </div>
    </div>
  `;
    }

    function renderPagination(totalPages) {
        const pg = document.getElementById('pagination');
        if (totalPages <= 1) {
            pg.innerHTML = '';
            return;
        }
        let html =
            `<button class="page-btn nav-pg" onclick="gotoPage(${currentPage-1})" ${currentPage===1?'disabled style="opacity:.4"':''}>‹</button>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 1) {
                html +=
                    `<button class="page-btn ${i===currentPage?'active':''}" onclick="gotoPage(${i})">${i}</button>`;
            } else if (Math.abs(i - currentPage) === 2) {
                html += `<span style="color:var(--text3);padding:0 4px">…</span>`;
            }
        }
        html +=
            `<button class="page-btn nav-pg" onclick="gotoPage(${currentPage+1})" ${currentPage===totalPages?'disabled style="opacity:.4"':''}>›</button>`;
        pg.innerHTML = html;
    }

    function gotoPage(p) {
        const filtered = getFilteredToko();
        const total = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
        currentPage = Math.max(1, Math.min(p, total));
        renderToko();
        window.scrollTo(0, 0);
    }

    // ========== MENU DETAIL ==========
    function openToko(id) {
        const toko = tokoData.find(t => t.id === id);
        if (!toko) return;
        showPage('menu');

        const buka = isOpen(toko);
        const menus = menuData[id] || [];
        const emoji = storeEmojis[id % storeEmojis.length];

        // Hero
        const halalPill = toko.halal === 'tersertifikasi' ?
            `<span class="halal-pill halal-yes">✓ Halal Tersertifikasi</span>` :
            toko.halal === 'non halal' ?
            `<span class="halal-pill halal-no">Non Halal</span>` :
            `<span class="halal-pill halal-unset">Belum Tersertifikasi</span>`;

        const mitraHtml = toko.mitra.map(m => {
            const cls = m === 'GoFood' ? 'mitra-gofood' : m === 'GrabFood' ? 'mitra-grabfood' :
                'mitra-shopeefood';
            return `<span class="mitra-badge ${cls}">${m}</span>`;
        }).join('');

        const jam = toko.jam_buka && toko.jam_tutup ? `${toko.jam_buka} – ${toko.jam_tutup}` : 'Jam tidak tersedia';

        // Group menu by kategori
        const kategoriMap = {};
        if (menus.length > 0) {
            menus.forEach(m => {
                const k = m.kategori || 'Lainnya';
                if (!kategoriMap[k]) kategoriMap[k] = [];
                kategoriMap[k].push(m);
            });
        }
        const kategoriList = Object.keys(kategoriMap);

        let menuTabsHtml = '',
            menuSectionsHtml = '';
        if (menus.length === 0) {
            menuSectionsHtml =
                `<div class="no-menu">🍽️<br>Menu untuk toko ini belum tersedia.<br>Silakan hubungi pengelola toko.</div>`;
        } else {
            menuTabsHtml = `<div class="menu-tabs">
      <div class="menu-tab active" onclick="switchTab('semua',this)">Semua</div>
      ${kategoriList.map(k=>`<div class="menu-tab" onclick="switchTab('${k}',this)">${k}</div>`).join('')}
    </div>`;

            // Render all menus
            menuSectionsHtml = `<div id="menuGrid">
      ${menus.map(m=>renderMenuItem(m)).join('')}
    </div>`;
        }

        document.getElementById('menuContent').innerHTML = `
    <div class="shop-hero">
      <img class="shop-hero-img" src="${toko.foto}" alt="${toko.nama}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="shop-hero-img-placeholder" style="display:none">${emoji}</div>
      <div class="shop-hero-info">
        <div class="shop-hero-top">
          <div class="shop-hero-name">${toko.nama}</div>
          <div class="shop-status-badge ${buka?'status-buka':'status-tutup'}">${buka?'🟢 Buka':'🔴 Tutup'}</div>
        </div>
        <div class="shop-meta">
          <div class="shop-meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            ${jam}
          </div>
          <div class="shop-meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            ${toko.lokasi}
          </div>
          ${toko.rating?`<div class="shop-meta-item" style="color:var(--accent)"><svg viewBox="0 0 24 24" fill="var(--accent)" width="14" height="14"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>${toko.rating.toFixed(1)}</div>`:''}
        </div>
        <div class="shop-halal-row">${halalPill}</div>
        ${mitraHtml?`<div class="mitra-list" style="margin-top:10px">${mitraHtml}</div>`:''}
      </div>
    </div>
    <div class="page-header"><div class="page-title">Menu</div></div>
    ${menuTabsHtml}
    <div class="menu-grid">${menuSectionsHtml}</div>
  `;

        // Store menus for tab switching
        window._currentMenus = menus;
        window._currentKategoriMap = kategoriMap;
    }

    function renderMenuItem(m) {
        const rasaClass = m.rasa ? `rasa-${m.rasa}` : '';
        const rasaHtml = m.rasa ? `<span class="menu-rasa ${rasaClass}">${m.rasa}</span>` : '';
        const hasImg = m.foto && m.foto.trim() !== '';
        const imgEl = hasImg ?
            `<img class="menu-img" src="${m.foto}" alt="${m.nama}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">` :
            '';
        const placeholder =
            `<div class="menu-img-placeholder" ${hasImg?'style="display:none"':''}>${foodEmojis[m.id%foodEmojis.length]}</div>`;
        return `
    <div class="menu-item">
      ${imgEl}${placeholder}
      <div class="menu-info">
        <div class="menu-name">${m.nama}</div>
        ${m.deskripsi?`<div class="menu-desc">${m.deskripsi.trim()}</div>`:''}
        <div class="menu-bottom">
          <div style="display:flex;align-items:center;gap:8px">
            <span class="menu-price">Rp ${m.harga.toLocaleString('id-ID')}</span>
            ${rasaHtml}
          </div>
          <button class="add-btn" onclick="event.stopPropagation();addToCart(${m.id})">+</button>
        </div>
      </div>
    </div>
  `;
    }

    function switchTab(kategori, el) {
        document.querySelectorAll('.menu-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        const menus = window._currentMenus || [];
        const filtered = kategori === 'semua' ? menus : menus.filter(m => (m.kategori || 'Lainnya') === kategori);
        const grid = document.querySelector('.menu-grid');
        if (filtered.length === 0) {
            grid.innerHTML = `<div class="no-menu">Tidak ada menu dalam kategori ini.</div>`;
        } else {
            grid.innerHTML = filtered.map(m => renderMenuItem(m)).join('');
        }
    }

    function addToCart(id) {
        // Simple toast feedback
        const toast = document.createElement('div');
        toast.textContent = '✓ Ditambahkan ke keranjang';
        toast.style.cssText =
            'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#4ade80;color:#052e16;padding:10px 20px;border-radius:20px;font-size:13px;font-weight:700;z-index:999;animation:fadeUp .3s ease';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }

    // ========== PAGE NAV ==========
    function showPage(name) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.getElementById('page-' + name).classList.add('active');
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        if (name === 'toko') document.getElementById('nav-toko').classList.add('active');
    }

    function backToToko() {
        showPage('toko');
    }

    // ========== FILTER TOGGLE ==========
    function toggleFilter() {
        document.getElementById('filterOverlay').classList.toggle('open');
    }

    function closeFilter(e) {
        if (e.target === document.getElementById('filterOverlay')) toggleFilter();
    }

    // ========== INIT ==========
    renderToko();
    // update open count every minute
    setInterval(() => {
        renderToko();
    }, 60000);
    </script>
</body>

</html>