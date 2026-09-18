<?php
require_once '../../config/security.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

$db = getDBConnection();
$measurements = [];
$message = '';

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

try {
    $stmt = $db->query("
        SELECT t.id_timbangan, b.nama_balita, b.jenis_kelamin, t.umur_bulan, t.berat_badan, t.tinggi_badan, t.status_gizi, t.tgl_periksa
        FROM timbangan_bulanan t
        JOIN data_balita b ON t.id_balita = b.id_balita
        ORDER BY t.tgl_periksa DESC, t.id_timbangan DESC
    ");
    $measurements = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Failed to fetch timbangan: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengukuran Timbangan - E-Posyandu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .table-container { margin-top: 20px; }
        .badge-stunting { background-color: #dc3545; }
        .badge-normal { background-color: #28a745; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">E-Posyandu</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Dashboard</a>
                <a class="nav-link" href="/modules/balita/index.php">Data Balita</a>
                <a class="nav-link" href="/modules/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container table-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Data Pengukuran Timbangan</h2>
            <a href="/modules/timbangan/tambah.php" class="btn btn-success">+ Tambah Pengukuran</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($measurements)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada data pengukuran</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($measurements as $index => $row): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($row['nama_balita']) ?></td>
                                        <td><?= $row['jenis_kelamin'] === 'L' ? 'L' : 'P' ?></td>
                                        <td><?= htmlspecialchars($row['umur_bulan']) ?></td>
                                        <td><?= htmlspecialchars($row['berat_badan']) ?></td>
                                        <td><?= htmlspecialchars($row['tinggi_badan']) ?></td>
                                        <td>
                                            <?php
                                            $isStunting = strpos($row['status_gizi'], 'Stunting') !== false;
                                            ?>
                                            <span class="badge <?= $isStunting ? 'badge-stunting' : 'badge-normal' ?>">
                                                <?= htmlspecialchars($row['status_gizi']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row['tgl_periksa']) ?></td>
                                        <td>
                                            <a href="/modules/timbangan/hapus.php?id=<?= $row['id_timbangan'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pengukuran ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
