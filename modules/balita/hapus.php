<?php
require_once '../../config/security.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: /modules/balita/index.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id || !is_numeric($id)) {
    header('Location: /modules/balita/index.php');
    exit;
}

try {
    $db = getDBConnection();
    $stmt = $db->prepare("DELETE FROM data_balita WHERE id_balita = ?");
    $stmt->execute([$id]);
    header('Location: /modules/balita/index.php?msg=Data+berhasil+dihapus&msg_type=success');
    exit;
} catch (PDOException $e) {
    error_log("Delete failed: " . $e->getMessage());
    header('Location: /modules/balita/index.php?msg=Gagal+menghapus+data&msg_type=danger');
    exit;
}
