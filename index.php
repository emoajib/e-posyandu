<?php
session_start();
require_once 'config/security.php';
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Posyandu</title>
    <link rel="manifest" href="/manifest.json">
</head>
<body>
    <h1>Selamat Datang di E-Posyandu</h1>
    <nav>
        <a href="/modules/balita/index.php">Data Balita</a>
        <a href="/modules/timbangan/index.php">Timbangan</a>
        <a href="/modules/gizi/index.php">Gizi</a>
        <a href="/modules/laporan/cetak.php">Laporan</a>
    </nav>
</body>
</html>
