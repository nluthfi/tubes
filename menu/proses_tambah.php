<?php
include '../koneksi.php';

if(isset($_POST['nama_menu'])){

    $nama = $_POST['nama_menu'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $rasa = $_POST['rasa'];
    $kategori = $_POST['id_kategori'];
    $toko = $_POST['id_toko'];

    $foto = $_FILES['foto_menu']['name'];
    $tmp  = $_FILES['foto_menu']['tmp_name'];

    $folder = "../img/makanan/";

    $namaFileBaru = time() . "_" . $foto;

    $path = $folder . $namaFileBaru;

    if(move_uploaded_file($tmp, $path)){

        mysqli_query($koneksi, "
            INSERT INTO menu 
            (nama_menu, foto_menu, deskripsi, harga, rasa, id_kategori, id_toko)
            VALUES
            ('$nama', '$namaFileBaru', '$deskripsi', '$harga', '$rasa', '$kategori', '$toko')
        ");

        echo "<script>
            alert('Menu berhasil ditambahkan!');
            window.location='menu.php';
        </script>";

    } else {
        echo "<script>
            alert('Upload foto gagal!');
            window.history.back();
        </script>";
    }

}
?>