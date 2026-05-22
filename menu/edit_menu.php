<?php 
include '../layout/header.php';
include '../layout/sidebar.php';
include '../koneksi.php';

$id = $_GET['id'];
$menu = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT * FROM menu WHERE id_menu = '$id'
"));

$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
$toko = mysqli_query($koneksi, "SELECT * FROM toko");
?>

<main class="main-content">

<div class="form-page">
    <div class="form-card">
        <div class="form-title">
            <h2>Edit Menu</h2>
            <p>Ubah data menu street food</p>
        </div>

        <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_menu" value="<?= $menu['id_menu']; ?>">

            <!-- NAMA -->
            <div class="mb-3">
                <label class="form-label">Nama Menu</label>
                <input type="text" name="nama_menu" value="<?= $menu['nama_menu']; ?>"
                class="form-control" required>
            </div>

            <!-- FOTO SAAT INI -->
            <div class="mb-3">

                <label class="form-label">Foto Saat Ini</label>
                <br>

                <img 
                    src="<?= $menu['foto_menu']; ?>"
                    width="150"
                    style="border-radius:10px;"
                >

            </div>

            <!-- FOTO BARU -->
            <div class="mb-3">
                <label class="form-label">Ganti Foto (opsional)</label>
                <input type="file" name="foto_menu" class="form-control">
            </div>

            <!-- DESKRIPSI -->
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" required>
                <?= $menu['deskripsi']; ?>
                </textarea>
            </div>

            <!-- HARGA -->
            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" name="harga" value="<?= $menu['harga']; ?>"
                class="form-control" required>
            </div>

            <!-- RASA -->
            <div class="mb-3">
                <label class="form-label">Rasa</label>
                <input type="text" name="rasa" value="<?= $menu['rasa']; ?>"
                class="form-control" required>
            </div>
            <!-- KATEGORI -->
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="id_kategori" class="form-select" required>
                    <?php while($k = mysqli_fetch_assoc($kategori)) { ?>
                        <option value="<?= $k['id_kategori']; ?>"
                            <?= $k['id_kategori']==$menu['id_kategori']?'selected':'' ?>>
                            <?= $k['kategori_makanan']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <!-- TOKO -->
            <div class="mb-3">
                <label class="form-label">Toko</label>
                <select name="id_toko" class="form-select" required>
                    <?php while($t = mysqli_fetch_assoc($toko)) { ?>
                        <option value="<?= $t['id_toko']; ?>"
                            <?= $t['id_toko']==$menu['id_toko']?'selected':'' ?>>
                            <?= $t['nama_toko']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <!-- BUTTON -->
            <div class="form-action">
                <button type="submit" class="form-button primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Update
                </button>

                <a href="menu.php" class="form-button secondary text-decoration-none">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>

</main>

<?php include '../layout/footer.php'; ?>