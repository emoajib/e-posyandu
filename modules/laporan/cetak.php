<?php
session_start();
require_once '../../config/security.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

$db = getDBConnection();
$reports = [];
$monthFilter = $_GET['bulan'] ?? date('Y-m');

try {
    $stmt = $db->prepare("
        SELECT t.id_timbangan, b.nama_balita, b.jenis_kelamin, b.tgl_lahir, t.umur_bulan, t.berat_badan, t.tinggi_badan, t.status_gizi, t.tgl_periksa
        FROM timbangan_bulanan t
        JOIN data_balita b ON t.id_balita = b.id_balita
        WHERE DATE_FORMAT(t.tgl_periksa, '%Y-%m') = ?
        ORDER BY t.tgl_periksa DESC, b.nama_balita
    ");
    $stmt->execute([$monthFilter]);
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Report query failed: " . $e->getMessage());
}

$totalChildren = count(array_unique(array_column($reports, 'nama_balita')));
$stuntingCount = 0;
foreach ($reports as $r) {
    if (strpos($r['status_gizi'], 'Stunting') !== false) {
        $stuntingCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - E-Posyandu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .print-header { text-align: center; margin-bottom: 30px; }
        .print-header h2 { color: #0d6efd; }
        .summary-box { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .summary-box .stat { text-align: center; }
        .summary-box .stat-number { font-size: 2em; font-weight: bold; }
        .summary-box .stat-label { color: #6c757d; font-size: 0.9em; }
        @media print {
            body { background: white; }
            .no-print { display: none; }
            .print-header { margin-bottom: 20px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
            .card { box-shadow: none !important; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary no-print">
        <div class="container">
            <a class="navbar-brand" href="/">E-Posyandu</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Dashboard</a>
                <a class="nav-link" href="/modules/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="print-header">
            <h2>Laporan Pengukuran Timbangan</h2>
            <p class="text-muted">E-Posyandu - Sistem Informasi Kesehatan Anak</p>
        </div>

        <div class="summary-box row g-3 no-print">
            <div class="col-md-4">
                <div class="stat">
                    <div class="stat-number text-primary"><?= $totalChildren ?></div>
                    <div class="stat-label">Anak Terukur</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat">
                    <div class="stat-number text-success"><?= count($reports) ?></div>
                    <div class="stat-label">Total Pengukuran</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat">
                    <div class="stat-number text-danger"><?= $stuntingCount ?></div>
                    <div class="stat-label">Indikasi Stunting</div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-3 no-print">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="bulan" class="form-label">Filter Bulan</label>
                        <input type="month" class="form-control" id="bulan" name="bulan" value="<?= htmlspecialchars($monthFilter) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="window.print()" class="btn btn-success">Cetak</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5>Data Pengukuran - <?= htmlspecialchars($monthFilter) ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Balita</th>
                                <th>JK</th>
                                <th>Umur (bln)</th>
                                <th>Berat (kg)</th>
                                <th>Tinggi (cm)</th>
                                <th>Status Gizi</th>
                                <th>Tgl Periksa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Tidak ada data untuk periode ini</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($reports as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_balita']) ?></td>
                                        <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                        <td><?= htmlspecialchars($row['umur_bulan']) ?></td>
                                        <td><?= htmlspecialchars($row['berat_badan']) ?></td>
                                        <td><?= htmlspecialchars($row['tinggi_badan']) ?></td>
                                        <td>
                                            <?php
                                            $isStunting = strpos($row['status_gizi'], 'Stunting') !== false;
                                            ?>
                                            <span class="badge <?= $isStunting ? 'bg-danger' : 'bg-success' ?>">
                                                <?= htmlspecialchars($row['status_gizi']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row['tgl_periksa']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4 shadow">
            <div class="card-header bg-info text-white">
                <h5>Ringkasan Status Gizi</h5>
            </div>
            <div class="card-body">
                <?php
                $normalCount = 0;
                $stuntingCount2 = 0;
                foreach ($reports as $r) {
                    if (strpos($r['status_gizi'], 'Stunting') !== false) {
                        $stuntingCount2++;
                    } else {
                        $normalCount++;
                    }
                }
                $total = count($reports);
                $normalPct = $total > 0 ? round(($normalCount / $total) * 100, 1) : 0;
                $stuntingPct = $total > 0 ? round(($stuntingCount2 / $total) * 100, 1) : 0;
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Normal / Sehat: <?= $normalCount ?> (<?= $normalPct ?>%)</h6>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width: <?= $normalPct ?>%"><?= $normalPct ?>%</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Indikasi Stunting: <?= $stuntingCount2 ?> (<?= $stuntingPct ?>%)</h6>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-danger" style="width: <?= $stuntingPct ?>%"><?= $stuntingPct ?>%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
