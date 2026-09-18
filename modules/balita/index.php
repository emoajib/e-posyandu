<?php
require_once '../../config/security.php';
require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}

$db = getDBConnection();
$children = [];
$message = '';

if (isset($_GET['msg'])) {
    $msgType = $_GET['msg_type'] ?? 'info';
    $message = htmlspecialchars($_GET['msg']);
}

try {
    $stmt = $db->query("SELECT * FROM data_balita ORDER BY tgl_daftar DESC");
    $children = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Failed to fetch balita: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Balita - E-Posyandu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .table-container { margin-top: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">E-Posyandu</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Dashboard</a>
                <a class="nav-link" href="/modules/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container table-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Data Balita</h2>
            <a href="/modules/balita/tambah.php" class="btn btn-success">+ Tambah Balita</a>
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
                                <th>Jenis Kelamin</th>
                                <th>Umur</th>
                                <th>Nama Ibu</th>
                                <th>Tgl Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($children)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada data balita</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($children as $index => $child): ?>
                                    <?php
                                    $birthDate = new DateTime($child['tgl_lahir']);
                                    $today = new DateTime();
                                    $age = $birthDate->diff($today)->y;
                                    $genderLabel = $child['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan';
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($child['nama_balita']) ?></td>
                                        <td><?= $genderLabel ?></td>
                                        <td><?= $age ?> tahun</td>
                                        <td><?= htmlspecialchars($child['nama_ibu']) ?></td>
                                        <td><?= htmlspecialchars($child['tgl_daftar']) ?></td>
                                        <td>
                                            <a href="/modules/balita/edit.php?id=<?= $child['id_balita'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="/modules/balita/hapus.php?id=<?= $child['id_balita'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data <?= htmlspecialchars($child['nama_balita']) ?>?')">Hapus</a>
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
