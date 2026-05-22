<?php
include '../koneksi.php';

if(isset($_POST['id_menu'])){

    $id = $_POST['id_menu'];

    $nama = $_POST['nama_menu'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $rasa = $_POST['rasa'];
    $kategori = $_POST['id_kategori'];
    $toko = $_POST['id_toko'];

    $dataLama = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT foto_menu FROM menu WHERE id_menu='$id'
    "));

    $foto_menu = $_POST['foto_menu'];

    if($foto_menu != "" && !filter_var($foto_menu, FILTER_VALIDATE_URL)){

        echo "<script>
            alert('Link foto tidak valid!');
            window.history.back();
        </script>";

        exit;
    }

    // JIKA FOTO DIISI
    if($foto_menu != ""){

        mysqli_query($koneksi, "
            UPDATE menu SET
            nama_menu='$nama',
            foto_menu='$foto_menu',
            deskripsi='$deskripsi',
            harga='$harga',
            rasa='$rasa',
            id_kategori='$kategori',
            id_toko='$toko'
            WHERE id_menu='$id'
        ");

    } else {

        // JIKA FOTO TIDAK DIUBAH
        mysqli_query($koneksi, "
            UPDATE menu SET
            nama_menu='$nama',
            deskripsi='$deskripsi',
            harga='$harga',
            rasa='$rasa',
            id_kategori='$kategori',
            id_toko='$toko'
            WHERE id_menu='$id'
        ");

    }

    echo "<script>
    alert('Menu berhasil diupdate!');
    window.location='menu.php';
    </script>";

}
?>