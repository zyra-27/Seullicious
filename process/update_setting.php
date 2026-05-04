<?php
include "../config/koneksi.php";

mysqli_query($koneksi,"
UPDATE setting SET
nama_toko='$_POST[nama_toko]',
alamat='$_POST[alamat]',
no_hp='$_POST[no_hp]',
metode='$_POST[metode]',
pajak='$_POST[pajak]'
WHERE id=1
");

header("Location: ../user/setting.php");