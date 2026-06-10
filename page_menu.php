<?php
session_start();
include 'koneksi.php';

$id_toko = isset($_GET['id_toko']) ? (int)$_GET['id_toko'] : 0;
if (!$id_toko) { 
    header('Location: index.php'); 
    exit; 
}

// ambil data toko
$stmt = $koneksi->prepare("SELECT * FROM toko WHERE id_toko = ?");
$stmt->bind_param('i', $id_toko);
$stmt->execute();

$toko = $stmt->get_result()->fetch_assoc();
if (!$toko) { 
    header('Location: index.php'); 
    exit; 
}


$now_time = date('H:i:s'); // ambil waktu real-time
$jam_buka  = $toko['jam_buka']  ? substr($toko['jam_buka'], 0, 5)  : null;
$jam_tutup = $toko['jam_tutup'] ? substr($toko['jam_tutup'], 0, 5) : null;
$is_buka   = (
    $jam_buka && 
    $jam_tutup && 
    $now_time >= $toko['jam_buka'] && 
    $now_time <= $toko['jam_tutup']
);

// ambil data mitra
$mitras = [];
$r = $koneksi->query(
    "SELECT m.* FROM toko_mitra tm 
     JOIN mitra m ON m.id_mitra = tm.id_mitra 
     WHERE tm.id_toko = $id_toko");
while ($row = $r->fetch_assoc()) $mitras[] = $row;

// ambil data metode bayar
$bayars = [];
$r = $koneksi->query(
    "SELECT b.* FROM metode_toko mt 
     JOIN bayar b ON b.id_metode = mt.id_metode 
     WHERE mt.id_toko = $id_toko");
while ($row = $r->fetch_assoc()) $bayars[] = $row;

// ambil data menu dengan filter
$search_menu = isset($_GET['search_menu']) ? trim($_GET['search_menu']) : '';
$filter_rasa = isset($_GET['rasa'])        ? $_GET['rasa']             : '';
$filter_kat  = isset($_GET['kategori'])    ? (int)$_GET['kategori']    : 0;
$sort_menu   = isset($_GET['sort'])        ? $_GET['sort']             : '';

$where_m = ["m.id_toko = $id_toko"];
$params_m = []; $types_m = '';

// Filter menu berdasarkan search, rasa, dan kategori
if ($search_menu !== '') {
    $where_m[] = '(m.nama_menu LIKE ? OR m.deskripsi LIKE ?)';
    $params_m[] = "%$search_menu%";
    $params_m[] = "%$search_menu%";
    $types_m .= 'ss';
}

if ($filter_rasa !== '') {
    $where_m[] = "
        EXISTS (
            SELECT 1
            FROM menu_rasa mr2
            JOIN rasa r2 ON r2.id_rasa = mr2.id_rasa
            WHERE mr2.id_menu = m.id_menu
            AND r2.nama_rasa = ?
        )
    ";

    $params_m[] = $filter_rasa;
    $types_m .= 's';
}

if ($filter_kat > 0) {
    $where_m[] = 'm.id_kategori = ?';
    $params_m[] = $filter_kat;
    $types_m .= 'i';
}

$order_m = match($sort_menu) {
    'harga_asc'  => 'ORDER BY m.harga ASC',
    'harga_desc' => 'ORDER BY m.harga DESC',
    'nama_az'    => 'ORDER BY m.nama_menu ASC',
    default      => 'ORDER BY m.id_kategori ASC, m.id_menu ASC',
};

$where_sql_m = 'WHERE ' . implode(' AND ', $where_m);

// ambil data menu dengan join kategori
$menu_sql = " SELECT m.*, k.kategori_makanan,
    GROUP_CONCAT(r.nama_rasa SEPARATOR ', ') AS rasa
    FROM menu m
    LEFT JOIN kategori k ON k.id_kategori = m.id_kategori
    LEFT JOIN menu_rasa mr ON mr.id_menu = m.id_menu
    LEFT JOIN rasa r ON r.id_rasa = mr.id_rasa $where_sql_m
    GROUP BY m.id_menu $order_m";

$stmt_m = $koneksi->prepare($menu_sql);
if ($params_m) $stmt_m->bind_param($types_m, ...$params_m);
$stmt_m->execute();
$menus = $stmt_m->get_result()->fetch_all(MYSQLI_ASSOC);

// kelompokin menu berdasarkan kategori
$grouped = [];
foreach ($menus as $menu) {
    $kat = $menu['kategori_makanan'] ?: 'Lainnya';
    $grouped[$kat][] = $menu;
}

// ambil semua data kategori
$all_kat = $koneksi->query(
    "SELECT DISTINCT k.id_kategori, k.kategori_makanan FROM menu m 
     JOIN kategori k ON k.id_kategori = m.id_kategori 
     WHERE m.id_toko = $id_toko 
     ORDER BY k.id_kategori");
$kat_list = [];
while ($k = $all_kat->fetch_assoc()) $kat_list[] = $k;

// Reviews
$reviews = [];
$r = $koneksi->query(
    "SELECT * FROM review 
     WHERE id_toko = $id_toko 
     ORDER BY tanggal_review DESC 
     LIMIT 5");
if ($r) while ($row = $r->fetch_assoc()) $reviews[] = $row;
$rating_text = $toko['rating'] ? number_format($toko['rating'], 1) : '–';

function fotoUrl($val, $folder) {
    if (!$val) return '';
    if (strpos($val, 'http') === 0) return $val;
    return $folder . $val;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($toko['nama_toko']) ?> – Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- TOPBAR -->
    <header class="topbar">
        <a href="index.php" class="logo">
            <div class="logo-icon">🍜</div>
            <div class="logo-text">STREET FOOD<small>Gegerkalong</small></div>
        </a>
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchMenuInput" placeholder="Cari menu..."
                value="<?= htmlspecialchars($search_menu) ?>">
        </div>
    </header>

    <div class="content">
        <!-- HERO BANNER (GoFood style) -->
        <div class="store-hero">
            <?php $heroFoto = fotoUrl($toko['foto_outlet'], 'img/pict/'); ?>
            <?php if ($heroFoto): ?>
            <img src="<?= htmlspecialchars($heroFoto) ?>" class="hero-banner"
                alt="<?= htmlspecialchars($toko['nama_toko']) ?>">
            <?php else: ?>
            <div class="hero-banner-placeholder">🍜</div>
            <?php endif; ?>
            <div class="hero-overlay"></div>
            <div class="hero-info">
                <div class="hero-name">
                    <?= htmlspecialchars($toko['nama_toko']) ?>
                    <span class="status-pill <?= $is_buka ? 'status-buka' : 'status-tutup' ?>">
                        <?= $is_buka ? '● Buka' : '● Tutup' ?>
                    </span>
                    <?php if ($toko['status_halal'] === 'tersertifikasi'): ?>
                    <span class="halal-tag"><i class="fas fa-leaf"></i> Halal</span>
                    <?php endif; ?>
                </div>
                <div class="hero-meta">
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
                        <i class="fas fa-motorcycle" style="font-size:11px"></i>
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

        <!-- STICKY KATEGORI NAV (GoFood style) -->
        <?php if (!$filter_rasa && !$search_menu): ?>
        <nav class="kat-nav" id="katNav">
            <a class="kat-nav-item <?= !$filter_kat ? 'active' : '' ?>" href="?id_toko=<?= $id_toko ?>">Semua</a>
            <?php foreach ($kat_list as $k): ?>
            <a class="kat-nav-item <?= $filter_kat == $k['id_kategori'] ? 'active' : '' ?>"
                href="#kat-<?= $k['id_kategori'] ?>" data-kat="<?= $k['id_kategori'] ?>"
                onclick="scrollToKat(<?= $k['id_kategori'] ?>); return false;"><?= htmlspecialchars($k['kategori_makanan']) ?></a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

        <!-- MENU SECTION -->
        <div class="menu-section">
            <form id="menuForm" method="GET" action="">
                <input type="hidden" name="id_toko" value="<?= $id_toko ?>">
                <input type="hidden" name="search_menu" id="searchMenuHidden"
                    value="<?= htmlspecialchars($search_menu) ?>">
                <input type="hidden" name="rasa" id="rasa_hidden" value="<?= htmlspecialchars($filter_rasa) ?>">
                <input type="hidden" name="kategori" id="kat_hidden" value="<?= $filter_kat ?>">
                <input type="hidden" name="sort" id="sort_hidden" value="<?= htmlspecialchars($sort_menu) ?>">

                <div class="menu-filter-bar">
                    <!-- Rasa chips -->
                    <a href="?id_toko=<?= $id_toko ?>" class="rasa-chip <?= !$filter_rasa ? 'active' : '' ?>">Semua
                        Rasa</a>
                    <?php foreach (['pedas','manis','asin','berkuah','asam'] as $r): ?>
                    <a href="?id_toko=<?= $id_toko ?>&rasa=<?= $filter_rasa===$r?'':$r ?>&sort=<?= $sort_menu ?>&search_menu=<?= urlencode($search_menu) ?>"
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
            <?php elseif ($filter_rasa || $search_menu || $sort_menu): ?>
            <!-- Flat list when filtered -->
            <div class="menu-list">
                <?php foreach ($menus as $menu):
          $fotoMenu = fotoUrl($menu['foto_menu'], 'img/makanan/'); ?>
                <div class="menu-item" data-id="<?= $menu['id_menu'] ?>"
                    data-nama="<?= htmlspecialchars($menu['nama_menu'],ENT_QUOTES) ?>"
                    data-harga="<?= $menu['harga'] ?>" data-foto="<?= htmlspecialchars($fotoMenu,ENT_QUOTES) ?>">
                    <div class="menu-item-img">
                        <?php if ($fotoMenu): ?>
                        <img src="<?= htmlspecialchars($fotoMenu) ?>" alt="<?= htmlspecialchars($menu['nama_menu']) ?>"
                            loading="lazy" onerror="this.parentElement.innerHTML='<i class=\'fas fa-utensils\'></i>'">
                        <?php else: ?><i class="fas fa-utensils"></i><?php endif; ?>
                    </div>
                    <div class="menu-item-body">
                        <h4><?= htmlspecialchars($menu['nama_menu']) ?></h4>
                        <?php if ($menu['deskripsi']): ?>
                        <p class="desc"><?= htmlspecialchars(trim($menu['deskripsi'])) ?></p>
                        <?php endif; ?>
                        <div class="menu-item-footer">
                            <span class="menu-price">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></span>
                            <div style="display:flex;align-items:center;gap:10px">
                                <?php if (!empty($menu['rasa'])): ?>
                                    <span class="rasa-tag">
                                        <?= htmlspecialchars($menu['rasa']) ?>
                                    </span>
                                <?php endif; ?>
                                <div class="qty-control" data-id="<?= $menu['id_menu'] ?>">
                                    <button class="btn-add" onclick="addToCart(<?= $menu['id_menu'] ?>)"><i
                                            class="fas fa-plus"></i> Tambah</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Grouped by kategori -->
            <?php foreach ($grouped as $kat_name => $kat_menus):
        // find kat id
        $kat_id = 0;
        foreach ($kat_list as $kl) { if ($kl['kategori_makanan'] === $kat_name) { $kat_id = $kl['id_kategori']; break; } }
      ?>
            <div class="kategori-section" id="kat-<?= $kat_id ?>" data-kat-id="<?= $kat_id ?>">
                <div class="kategori-title">
                    <?= htmlspecialchars($kat_name) ?>
                    <span style="font-size:12px;font-weight:500;color:var(--text-muted)">(<?= count($kat_menus) ?>
                        item)</span>
                </div>
                <div class="menu-list">
                    <?php foreach ($kat_menus as $menu):
            $fotoMenu = fotoUrl($menu['foto_menu'], 'img/makanan/'); ?>
                    <div class="menu-item" data-id="<?= $menu['id_menu'] ?>"
                        data-nama="<?= htmlspecialchars($menu['nama_menu'],ENT_QUOTES) ?>"
                        data-harga="<?= $menu['harga'] ?>" data-foto="<?= htmlspecialchars($fotoMenu,ENT_QUOTES) ?>">
                        <div class="menu-item-img">
                            <?php if ($fotoMenu): ?>
                            <img src="<?= htmlspecialchars($fotoMenu) ?>"
                                alt="<?= htmlspecialchars($menu['nama_menu']) ?>" loading="lazy"
                                onerror="this.parentElement.innerHTML='<i class=\'fas fa-utensils\'></i>'">
                            <?php else: ?><i class="fas fa-utensils"></i><?php endif; ?>
                        </div>
                        <div class="menu-item-body">
                            <h4><?= htmlspecialchars($menu['nama_menu']) ?></h4>
                            <?php if ($menu['deskripsi']): ?>
                            <p class="desc"><?= htmlspecialchars(trim($menu['deskripsi'])) ?></p>
                            <?php endif; ?>
                            <div class="menu-item-footer">
                                <span class="menu-price">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></span>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <?php if (!empty($menu['rasa'])): ?>
                                        <span class="rasa-tag">
                                            <?= htmlspecialchars($menu['rasa']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="qty-control" id="qty-<?= $menu['id_menu'] ?>">
                                        <button class="btn-add" onclick="addToCart(<?= $menu['id_menu'] ?>)"><i
                                                class="fas fa-plus"></i> Tambah</button>
                                    </div>
                                </div>
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
                            <?= str_repeat('★',$rv['rating']) ?><?= str_repeat('☆',5-$rv['rating']) ?></div>
                    </div>
                    <div class="review-date"><?= date('d M Y', strtotime($rv['tanggal_review'])) ?></div>
                </div>
                <div class="review-text"><?= htmlspecialchars($rv['komentar']) ?></div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- FLOATING CART -->
    <div class="cart-float" id="cartFloat" onclick="openCart()">
        <div class="cart-float-left">
            <div class="cart-count-badge" id="cartCount">0</div>
            <div class="cart-float-label">
                Lihat Keranjang
                <small id="cartItems">0 item</small>
            </div>
        </div>
        <div class="cart-float-total" id="cartTotal">Rp 0</div>
    </div>

    <!-- CART DRAWER -->
    <div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer-handle"></div>
        <div class="cart-drawer-header">
            <h3>🛒 Keranjang</h3>
            <button class="cart-close" onclick="closeCart()"><i class="fas fa-times"></i></button>
        </div>
        <div class="cart-items" id="cartItemsList"></div>
        <div class="cart-footer">
            <div class="cart-summary">
                <span>Total Pesanan</span>
                <span class="total" id="cartTotalDrawer">Rp 0</span>
            </div>
            <button class="btn-checkout" onclick="checkout()">
                <i class="fas fa-shopping-bag"></i> Pesan Sekarang
            </button>
        </div>
    </div>

    <script>
    // simpan data menu yang dipilih
    let cart = {}; // { id: { nama, harga, foto, qty } }

    // Format rupiah
    function fmt(n) {
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    // hitung total harga pesanan
    function cartTotal() {
        return Object.values(cart).reduce((s, i) => s + i.harga * i.qty, 0);
    }

    // hitung total menu yang dipesan
    function cartCount() {
        return Object.values(cart).reduce((s, i) => s + i.qty, 0);
    }

    //
    function updateCartFloat() {
        const count = cartCount();
        const total = cartTotal();
        document.getElementById('cartCount').textContent = count;
        document.getElementById('cartItems').textContent = count + ' item';
        document.getElementById('cartTotal').textContent = fmt(total);
        document.getElementById('cartTotalDrawer').textContent = fmt(total);
        const el = document.getElementById('cartFloat');
        count > 0 ? el.classList.add('visible') : el.classList.remove('visible');
    }

    // Menampilkan tombol tambah/kurang jumlah menu
    function renderQtyControl(id) {
        const qtyEl = document.getElementById('qty-' + id);
        if (!qtyEl) return;
        const item = cart[id];
        if (!item || item.qty === 0) {
            qtyEl.innerHTML = '<button class="btn-add" onclick="addToCart(' + id +
                ')"><i class="fas fa-plus"></i> Tambah</button>';
        } else {
            qtyEl.innerHTML =
                '<div style="display:flex;align-items:center;gap:6px">' +
                '<button class="btn-qty btn-qty-minus" onclick="changeQty(' + id +
                ',-1)"><i class="fas fa-minus" style="font-size:11px"></i></button>' +
                '<span class="qty-num">' + item.qty + '</span>' +
                '<button class="btn-qty btn-qty-plus" onclick="changeQty(' + id +
                ',1)"><i class="fas fa-plus" style="font-size:11px"></i></button>' +
                '</div>';
        }
    }

    // Tambah menu ke keranjang
    function addToCart(id) {
        const el = document.querySelector('.menu-item[data-id="' + id + '"]');
        if (!el) return;
        const nama = el.dataset.nama;
        const harga = parseInt(el.dataset.harga);
        const foto = el.dataset.foto;
        if (!cart[id]) cart[id] = {
            nama,
            harga,
            foto,
            qty: 0
        };
        cart[id].qty++;
        renderQtyControl(id);
        updateCartFloat();
        renderCartDrawer();
        // pulse animation
        const btn = document.getElementById('cartFloat');
        btn.style.transform = 'translateX(-50%) translateY(0) scale(1.04)';
        setTimeout(() => btn.style.transform = 'translateX(-50%) translateY(0) scale(1)', 180);
    }

    // Ubah jumlah menu di keranjang
    function changeQty(id, delta) {
        if (!cart[id]) return;
        cart[id].qty += delta;
        if (cart[id].qty <= 0) delete cart[id];
        renderQtyControl(id);
        updateCartFloat();
        renderCartDrawer();
    }

    // tampilikan isi keranjang
    function renderCartDrawer() {
        const list = document.getElementById('cartItemsList');
        const items = Object.entries(cart);
        if (items.length === 0) {
            list.innerHTML =
                '<div style="text-align:center;padding:40px;color:var(--text-muted)"><i class="fas fa-shopping-cart" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i><p>Keranjang masih kosong</p></div>';
            return;
        }
        list.innerHTML = items.map(([id, item]) =>
            '<div class="cart-item">' +
            '<div class="cart-item-img">' +
            (item.foto ? '<img src="' + item.foto + '" alt="" onerror="this.style.display=\'none\'">' : '') +
            '</div>' +
            '<div class="cart-item-info">' +
            '<h5>' + item.nama + '</h5>' +
            '<div class="price">' + fmt(item.harga) + '</div>' +
            '</div>' +
            '<div class="cart-item-qty">' +
            '<button class="btn-qty btn-qty-minus" onclick="changeQty(' + id +
            ',-1)"><i class="fas fa-minus" style="font-size:11px"></i></button>' +
            '<span class="qty-num">' + item.qty + '</span>' +
            '<button class="btn-qty btn-qty-plus" onclick="changeQty(' + id +
            ',1)"><i class="fas fa-plus" style="font-size:11px"></i></button>' +
            '</div>' +
            '</div>'
        ).join('');
    }

    // buka page keranjang
    function openCart() {
        renderCartDrawer();
        document.getElementById('cartOverlay').classList.add('show');
        document.getElementById('cartDrawer').classList.add('show');
    }

    // tutup page keranjang
    function closeCart() {
        document.getElementById('cartOverlay').classList.remove('show');
        document.getElementById('cartDrawer').classList.remove('show');
    }

    // checkout pesanan
    function checkout() {
        if (cartCount() === 0) return;
        const items = Object.values(cart).map(i => i.qty + 'x ' + i.nama).join('\n');
        alert('Pesanan:\n' + items + '\n\nTotal: ' + fmt(cartTotal()));
    }

    // 
    function scrollToKat(id) {
        const el = document.getElementById('kat-' + id);
        if (el) el.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        document.querySelectorAll('.kat-nav-item').forEach(a => a.classList.remove('active'));
        const navItem = document.querySelector('.kat-nav-item[data-kat="' + id + '"]');
        if (navItem) navItem.classList.add('active');
    }

    // 
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const katId = entry.target.dataset.katId;
                document.querySelectorAll('.kat-nav-item').forEach(a => a.classList.remove('active'));
                const navItem = document.querySelector('.kat-nav-item[data-kat="' + katId + '"]');
                if (navItem) {
                    navItem.classList.add('active');
                    navItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }
            }
        });
    }, {
        rootMargin: '-64px 0px -60% 0px',
        threshold: 0
    });

    document.querySelectorAll('.kategori-section').forEach(el => observer.observe(el));


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