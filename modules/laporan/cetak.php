<?php
session_start();
require_once '../../config/security.php';
require_once '../../config/database.php';
$db = getDBConnection();
// JOIN query for reports
$stmt = $db->query("
    SELECT b.nama, b.tanggal_lahir, t.tanggal, t.berat, t.tinggi
    FROM data_balita b
    JOIN timbangan_bulanan t ON b.id = t.balita_id
    ORDER BY t.tanggal DESC
");
$reports = $stmt->fetchAll();
?>
