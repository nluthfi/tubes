<?php 
include '../layout/header.php';
include '../layout/sidebar.php';
include '../koneksi.php';
?>

<main class="main-content">
    <div class="mb-4">
        <h2 class="fw-bold">STREET FOOD</h2>
    </div>

    <div class="table-container">
        <!-- HEADER -->
        <div class="table-header">
            <div>
                <h4>Data Menu</h4>
                <p>Daftar menu street food</p>
            </div>

            <a href="tambah_menu.php">
                <button class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Data
                </button>
            </a>
        </div>
        <!-- TABLE -->
        <div class="table-responsive">
            <div class="table-scroll">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Toko</th>
                            <th>Detail</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "
                            SELECT *
                            FROM menu m
                            LEFT JOIN kategori k ON m.id_kategori = k.id_kategori
                            LEFT JOIN toko t ON m.id_toko = t.id_toko
                        ");
                        $no = 1;

                        while($row = mysqli_fetch_assoc($query)){
                        ?>

                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nama_menu']; ?></td>
                            <td><?= $row['nama_toko']; ?></td>
                            <td>
                                <button class="btn btn-detail"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detail<?= $row['id_menu']; ?>">
                                    <i class="fa-solid fa-circle-info"></i>
                                    Detail
                                </button>
                            </td>
                            <td>
                                <a href="edit_menu.php?id=<?= $row['id_menu']; ?>">
                                    <button class="btn-action edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </a>

                                <a href="hapus_menu.php?id=<?= $row['id_menu']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    <button class="btn-action delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
$query2 = mysqli_query($koneksi, "
    SELECT 
        m.id_menu,
        m.nama_menu,
	    m.foto_menu,
        m.deskripsi,
        m.harga,
        m.rasa,
        k.kategori_makanan,
        t.nama_toko
    FROM menu m
    LEFT JOIN kategori k ON m.id_kategori = k.id_kategori
    LEFT JOIN toko t ON m.id_toko = t.id_toko
");

while($row = mysqli_fetch_assoc($query2)){
?>

<div class="modal fade" id="detail<?= $row['id_menu']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <!-- HEADER -->
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Detail Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <div class="detail-item">
                    <span class="detail-label">Nama Menu</span>
                    <span class="detail-value"><?= $row['nama_menu']; ?></span>
                </div>

            <div class="text-center mb-3">
                <img 
                    src="<?= $row['foto_menu']; ?>"
                    alt="<?= $row['nama_menu']; ?>"
                    style="
                        width:100%;
                        max-width:300px;
                        border-radius:12px;
                        object-fit:cover;
                    "
                >
            </div> 

                <div class="detail-item">
                    <span class="detail-label">Deskripsi</span>
                    <span class="detail-value"><?= $row['deskripsi']; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Harga</span>
                    <span class="detail-value">Rp <?= number_format($row['harga'],0,',','.'); ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Rasa</span>
                    <span class="detail-value"><?= $row['rasa']; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Kategori</span>
                    <span class="detail-value"><?= $row['kategori_makanan']; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Toko</span>
                    <span class="detail-value"><?= $row['nama_toko']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php include '../layout/footer.php'; ?>