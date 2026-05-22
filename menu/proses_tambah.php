<?php
include '../koneksi.php';

if(isset($_POST['nama_menu'])){

    $nama = $_POST['nama_menu'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $rasa = $_POST['rasa'];
    $kategori = $_POST['id_kategori'];
    $toko = $_POST['id_toko'];

$foto_menu = $_POST['foto_menu'];

        mysqli_query($koneksi, "
        INSERT INTO menu 
        (
            nama_menu,
            foto_menu,
            deskripsi,
            harga,
            rasa,
            id_kategori,
            id_toko
        )
        VALUES
        (
            '$nama',
            '$foto_menu',
            '$deskripsi',
            '$harga',
            '$rasa',
            '$kategori',
            '$toko'
        )
    ");

    echo "<script>
        alert('Menu berhasil ditambahkan!');
        window.location='menu.php';
    </script>";

}
?>