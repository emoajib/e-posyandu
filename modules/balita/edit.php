<?php
require_once '../../config/security.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id || !is_numeric($id)) {
    header('Location: /modules/balita/index.php');
    exit;
}

$db = getDBConnection();
$child = null;
$errors = [];

try {
    $stmt = $db->prepare("SELECT * FROM data_balita WHERE id_balita = ?");
    $stmt->execute([$id]);
    $child = $stmt->fetch();

    if (!$child) {
        header('Location: /modules/balita/index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Fetch balita failed: " . $e->getMessage());
    header('Location: /modules/balita/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }

    $namaBalita = sanitizeInput($_POST['nama_balita'] ?? '');
    $jenisKelamin = sanitizeInput($_POST['jenis_kelamin'] ?? '');
    $tglLahir = sanitizeInput($_POST['tgl_lahir'] ?? '');
    $namaIbu = sanitizeInput($_POST['nama_ibu'] ?? '');
    $namaAyah = sanitizeInput($_POST['nama_ayah'] ?? '');
    $noKk = sanitizeInput($_POST['no_kk'] ?? '');

    if (empty($namaBalita) || empty($jenisKelamin) || empty($tglLahir) || empty($namaIbu)) {
        $errors[] = 'Semua field wajib diisi.';
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE data_balita SET nama_balita = ?, jenis_kelamin = ?, tgl_lahir = ?, nama_ibu = ?, nama_ayah = ?, no_kk = ? WHERE id_balita = ?");
            $stmt->execute([$namaBalita, $jenisKelamin, $tglLahir, $namaIbu, $namaAyah, $noKk, $id]);
            header('Location: /modules/balita/index.php?msg=Data+berhasil+diupdate&msg_type=success');
            exit;
        } catch (PDOException $e) {
            error_log("Update failed: " . $e->getMessage());
            $errors[] = 'Gagal mengupdate data.';
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Balita - E-Posyandu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">E-Posyandu</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/modules/balita/index.php">Kembali</a>
                <a class="nav-link" href="/modules/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width:600px;">
        <h2 class="mb-4">Edit Data Balita</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="mb-3">
                        <label for="nama_balita" class="form-label">Nama Balita *</label>
                        <input type="text" class="form-control" id="nama_balita" name="nama_balita" value="<?= htmlspecialchars($child['nama_balita']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin *</label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="L" <?= $child['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= $child['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tgl_lahir" class="form-label">Tanggal Lahir *</label>
                        <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="<?= htmlspecialchars($child['tgl_lahir']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="nama_ibu" class="form-label">Nama Ibu *</label>
                        <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="<?= htmlspecialchars($child['nama_ibu']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="nama_ayah" class="form-label">Nama Ayah</label>
                        <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="<?= htmlspecialchars($child['nama_ayah'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="no_kk" class="form-label">No KK</label>
                        <input type="text" class="form-control" id="no_kk" name="no_kk" value="<?= htmlspecialchars($child['no_kk'] ?? '') ?>">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Update</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
