<?php
require_once '../../config/security.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: /modules/timbangan/index.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id || !is_numeric($id)) {
    header('Location: /modules/timbangan/index.php');
    exit;
}

try {
    $db = getDBConnection();
    $stmt = $db->prepare("DELETE FROM timbangan_bulanan WHERE id_timbangan = ?");
    $stmt->execute([$id]);
    header('Location: /modules/timbangan/index.php?msg=Pengukuran+berhasil+dihapus&msg_type=success');
    exit;
} catch (PDOException $e) {
    error_log("Delete timbangan failed: " . $e->getMessage());
    header('Location: /modules/timbangan/index.php?msg=Gagal+menghapus+pengukuran&msg_type=danger');
    exit;
}
