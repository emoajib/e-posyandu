<?php
session_start();
require_once '../../config/security.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /modules/balita/tambah.php');
    exit;
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    die('CSRF validation failed');
}

$namaBalita = sanitizeInput($_POST['nama_balita'] ?? '');
$jenisKelamin = sanitizeInput($_POST['jenis_kelamin'] ?? '');
$tglLahir = sanitizeInput($_POST['tgl_lahir'] ?? '');
$namaIbu = sanitizeInput($_POST['nama_ibu'] ?? '');
$namaAyah = sanitizeInput($_POST['nama_ayah'] ?? '');
$noKk = sanitizeInput($_POST['no_kk'] ?? '');

$errors = [];
if (empty($namaBalita)) {
    $errors[] = 'Nama balita wajib diisi.';
}
if (empty($jenisKelamin) || !in_array($jenisKelamin, ['L', 'P'])) {
    $errors[] = 'Jenis kelamin tidak valid.';
}
if (empty($tglLahir)) {
    $errors[] = 'Tanggal lahir wajib diisi.';
}
if (empty($namaIbu)) {
    $errors[] = 'Nama ibu wajib diisi.';
}

if (!empty($errors)) {
    $errorStr = implode(', ', $errors);
    header("Location: /modules/balita/tambah.php?msg=" . urlencode($errorStr) . "&msg_type=danger");
    exit;
}

try {
    $db = getDBConnection();
    $stmt = $db->prepare("INSERT INTO data_balita (nama_balita, jenis_kelamin, tgl_lahir, nama_ibu, nama_ayah, no_kk) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$namaBalita, $jenisKelamin, $tglLahir, $namaIbu, $namaAyah, $noKk]);
    header('Location: /modules/balita/index.php?msg=Data+berhasil+disimpan&msg_type=success');
    exit;
} catch (PDOException $e) {
    error_log("Insert failed: " . $e->getMessage());
    header('Location: /modules/balita/tambah.php?msg=Gagal+menyimpan+data&msg_type=danger');
    exit;
}
