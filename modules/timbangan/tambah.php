<?php
require_once '../../config/security.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

$errors = [];
$balitaList = [];
$message = '';

try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT id_balita, nama_balita FROM data_balita ORDER BY nama_balita");
    $balitaList = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Failed to fetch balita list: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }

    $idBalita = $_POST['id_balita'] ?? 0;
    $umurBulan = $_POST['umur_bulan'] ?? 0;
    $beratBadan = $_POST['berat_badan'] ?? 0;
    $tinggiBadan = $_POST['tinggi_badan'] ?? 0;
    $tglPeriksa = sanitizeInput($_POST['tgl_periksa'] ?? '');

    if (!$idBalita || !is_numeric($idBalita)) {
        $errors[] = 'Pilih balita.';
    }
    if (!validateAge($umurBulan)) {
        $errors[] = 'Umur bulan harus antara 1-60.';
    }
    if (!validateWeight($beratBadan)) {
        $errors[] = 'Berat badan harus antara 0.5-200 kg.';
    }
    if (!validateHeight($tinggiBadan)) {
        $errors[] = 'Tinggi badan harus antara 10-200 cm.';
    }
    if (empty($tglPeriksa)) {
        $errors[] = 'Tanggal periksa wajib diisi.';
    }

    if (empty($errors)) {
        require_once '../../config/igrc_data.php';

        $gender = '';
        try {
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
        if ($gender && $tinggiBadan > 0) {
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
            $errors[] = 'Gagal menyimpan pengukuran.';
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
    <title>Tambah Pengukuran - E-Posyandu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">E-Posyandu</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/modules/timbangan/index.php">Kembali</a>
                <a class="nav-link" href="/modules/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width:600px;">
        <h2 class="mb-4">Tambah Pengukuran Timbangan</h2>

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
                        <label for="id_balita" class="form-label">Pilih Balita *</label>
                        <select class="form-select" id="id_balita" name="id_balita" required>
                            <option value="">-- Pilih Balita --</option>
                            <?php foreach ($balitaList as $b): ?>
                                <option value="<?= $b['id_balita'] ?>"><?= htmlspecialchars($b['nama_balita']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="umur_bulan" class="form-label">Umur (bulan) *</label>
                        <input type="number" class="form-control" id="umur_bulan" name="umur_bulan" min="1" max="60" required>
                    </div>

                    <div class="mb-3">
                        <label for="berat_badan" class="form-label">Berat Badan (kg) *</label>
                        <input type="number" step="0.01" class="form-control" id="berat_badan" name="berat_badan" min="0.5" max="200" required>
                    </div>

                    <div class="mb-3">
                        <label for="tinggi_badan" class="form-label">Tinggi Badan (cm) *</label>
                        <input type="number" step="0.01" class="form-control" id="tinggi_badan" name="tinggi_badan" min="10" max="200" required>
                    </div>

                    <div class="mb-3">
                        <label for="tgl_periksa" class="form-label">Tanggal Periksa *</label>
                        <input type="date" class="form-control" id="tgl_periksa" name="tgl_periksa" required>
                    </div>

                    <div class="alert alert-info">
                        <small>Status gizi akan ditentukan otomatis berdasarkan IGRC 2018. Jika tinggi badan di bawah -2 SD untuk umur dan jenis kelamin, status akan ditandai sebagai "Indikasi Stunting".</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Simpan Pengukuran</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
