<?php
session_start();
require_once '../../config/security.php';
require_once '../../config/database.php';
$db = getDBConnection();
$stmt = $db->query("SELECT * FROM data_balita ORDER BY created_at DESC");
$children = $stmt->fetchAll();
?>
