<?php
include '../koneksi.php';

if(isset($_POST['update'])) {

$id = $_POST['id_toko'];

$nama = $_POST['nama_toko'];
$no_telp = $_POST['no_telepon'];
$lokasi = $_POST['lokasi'];
$jam_buka = $_POST['jam_buka'];
$jam_tutup = $_POST['jam_tutup'];
$status = $_POST['status_halal'];

$foto = $_FILES['foto_outlet']['name'];

$queryFoto = "";

if($foto != ''){

    $tmp = $_FILES['foto_outlet']['tmp_name'];

    move_uploaded_file($tmp, "../img/pict/".$foto);

    $queryFoto = ", foto_outlet='$foto'";
}

$mitra = $_POST['mitra'] ?? [];
$metode = $_POST['metode'] ?? [];

mysqli_query($koneksi, "
    UPDATE toko SET
        nama_toko='$nama',
        no_telepon='$no_telp',
        lokasi='$lokasi',
        jam_buka='$jam_buka',
        jam_tutup='$jam_tutup',
        status_halal='$status'
        $queryFoto
    WHERE id_toko='$id'
");

mysqli_query($koneksi, "DELETE FROM toko_mitra WHERE id_toko='$id'");

foreach($mitra as $m){
    mysqli_query($koneksi, "
        INSERT INTO toko_mitra VALUES('$id','$m')
    ");
}

mysqli_query($koneksi, "DELETE FROM metode_toko WHERE id_toko='$id'");

foreach($metode as $mp){
    mysqli_query($koneksi, "
        INSERT INTO metode_toko VALUES('$id','$mp')
    ");
}

echo "<script>
alert('Data toko berhasil diupdate!');
window.location.href='toko.php';
</script>";

}
?>