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

$fotoBaru = $_FILES['foto_menu']['name'];
$tmp = $_FILES['foto_menu']['tmp_name'];

$folder = "../img/makanan/";

if($fotoBaru != ""){

    if(file_exists($folder.$dataLama['foto_menu'])){
        unlink($folder.$dataLama['foto_menu']);
    }

    $namaFileBaru = time()."_".$fotoBaru;
    move_uploaded_file($tmp, $folder.$namaFileBaru);

    mysqli_query($koneksi, "
        UPDATE menu SET
        nama_menu='$nama',
        foto_menu='$namaFileBaru',
        deskripsi='$deskripsi',
        harga='$harga',
        id_kategori='$kategori',
        id_toko='$toko'
        WHERE id_menu='$id'
    ");

} else {

    mysqli_query($koneksi, "
        UPDATE menu SET
        nama_menu='$nama',
        deskripsi='$deskripsi',
        harga='$harga',
        id_kategori='$kategori',
        id_toko='$toko'
        WHERE id_menu='$id'
    ");

}

    // hapus relasi rasa lama
    mysqli_query($koneksi, "
        DELETE FROM menu_rasa
        WHERE id_menu='$id'
    ");

    // simpan rasa baru
    foreach($rasa as $id_rasa){

        mysqli_query($koneksi, "
            INSERT INTO menu_rasa(id_menu,id_rasa)
            VALUES('$id','$id_rasa')
        ");

    }

echo "<script>
alert('Menu berhasil diupdate!');
window.location='menu.php';
</script>";

}
?>
