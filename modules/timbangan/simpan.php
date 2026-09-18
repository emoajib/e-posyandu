<?php
require_once '../../config/security.php';
require_once '../../config/database.php';
require_once '../../config/igrc_data.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /modules/timbangan/tambah.php');
    exit;
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    die('CSRF validation failed');
}

$idBalita = $_POST['id_balita'] ?? 0;
$umurBulan = $_POST['umur_bulan'] ?? 0;
$beratBadan = $_POST['berat_badan'] ?? 0;
$tinggiBadan = $_POST['tinggi_badan'] ?? 0;
$tglPeriksa = sanitizeInput($_POST['tgl_periksa'] ?? '');

$errors = [];
if (!$idBalita || !is_numeric($idBalita)) {
    $errors[] = 'Pilih balita.';
}
if (!validateAge($umurBulan)) {
    $errors[] = 'Umur bulan harus antara 1-60.';
}
if (!validateWeight($beratBadan)) {
    $errors[] = 'Berat badan tidak valid.';
}
if (!validateHeight($tinggiBadan)) {
    $errors[] = 'Tinggi badan tidak valid.';
}
if (empty($tglPeriksa)) {
    $errors[] = 'Tanggal periksa wajib diisi.';
}

if (!empty($errors)) {
    $errorStr = implode(', ', $errors);
    header("Location: /modules/timbangan/tambah.php?msg=" . urlencode($errorStr) . "&msg_type=danger");
    exit;
}

$gender = '';
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT jenis_kelamin FROM data_balita WHERE id_balita = ?");
    $stmt->execute([$idBalita]);
    $balita = $stmt->fetch();
    if ($balita) {
        $gender = $balita['jenis_kelamin'];
    }
} catch (PDOException $e) {
    error_log("Fetch gender failed: " . $e->getMessage());
}

$statusGizi = 'Normal / Sehat';
if ($gender && $tinggiBadan > 0 && $umurBulan >= 0 && $umurBulan <= 60) {
    if (isStunting($umurBulan, $tinggiBadan, $gender)) {
        $statusGizi = 'Indikasi Stunting';
    }
}

try {
    $stmt = $db->prepare("INSERT INTO timbangan_bulanan (id_balita, umur_bulan, berat_badan, tinggi_badan, status_gizi, tgl_periksa) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$idBalita, $umurBulan, $beratBadan, $tinggiBadan, $statusGizi, $tglPeriksa]);
    header('Location: /modules/timbangan/index.php?msg=Pengukuran+berhasil+disimpan&msg_type=success');
    exit;
} catch (PDOException $e) {
    error_log("Insert timbangan failed: " . $e->getMessage());
    header('Location: /modules/timbangan/tambah.php?msg=Gagal+menyimpan+pengukuran&msg_type=danger');
    exit;
}
