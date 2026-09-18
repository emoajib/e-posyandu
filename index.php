<?php
require_once 'config/security.php';
require_once 'config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

$db = getDBConnection();
$totalBalita = 0;
$todayMeasurements = 0;
$recentActivity = [];

try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM data_balita");
    $totalBalita = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) as total FROM timbangan_bulanan WHERE DATE(tgl_periksa) = CURDATE()");
    $todayMeasurements = (int)$stmt->fetchColumn();

    $stmt = $db->query("
        SELECT b.nama_balita, t.berat_badan, t.tinggi_badan, t.status_gizi, t.tgl_periksa
        FROM timbangan_bulanan t
        JOIN data_balita b ON t.id_balita = b.id_balita
        ORDER BY t.tgl_periksa DESC, t.id_timbangan DESC
        LIMIT 5
    ");
    $recentActivity = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Dashboard query failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Posyandu - Dashboard</title>
    <link rel="manifest" href="/manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-stat { transition: transform 0.2s; }
        .card-stat:hover { transform: translateY(-5px); }
        .nav-link { margin-right: 15px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">E-Posyandu</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                <a class="nav-link" href="/modules/balita/index.php">Data Balita</a>
                <a class="nav-link" href="/modules/timbangan/index.php">Timbangan</a>
                <a class="nav-link" href="/modules/gizi/index.php">Gizi</a>
                <a class="nav-link" href="/modules/laporan/cetak.php">Laporan</a>
                <a class="nav-link" href="/modules/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Dashboard</h2>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card card-stat text-white bg-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Balita</h5>
                        <h2><?= $totalBalita ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stat text-white bg-success">
                    <div class="card-body text-center">
                        <h5 class="card-title">Pengukuran Hari Ini</h5>
                        <h2><?= $todayMeasurements ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stat text-white bg-info">
                    <div class="card-body text-center">
                        <h5 class="card-title">Kegiatan Terakhir</h5>
                        <h2><?= count($recentActivity) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5>Kegiatan Terakhir</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentActivity)): ?>
                    <p class="text-muted">Belum ada data pengukuran.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Balita</th>
                                    <th>Berat (kg)</th>
                                    <th>Tinggi (cm)</th>
                                    <th>Status Gizi</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nama_balita']) ?></td>
                                        <td><?= htmlspecialchars($row['berat_badan']) ?></td>
                                        <td><?= htmlspecialchars($row['tinggi_badan']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= strpos($row['status_gizi'], 'Stunting') !== false ? 'danger' : 'success' ?>">
                                                <?= htmlspecialchars($row['status_gizi']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row['tgl_periksa']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
