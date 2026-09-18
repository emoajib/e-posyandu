<?php
session_start();
require_once '../../config/security.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Gizi - E-Posyandu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .nutrition-card { transition: transform 0.2s; }
        .nutrition-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">E-Posyandu</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Dashboard</a>
                <a class="nav-link" href="/modules/balita/index.php">Data Balita</a>
                <a class="nav-link" href="/modules/timbangan/index.php">Timbangan</a>
                <a class="nav-link" href="/modules/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Informasi Gizi & MPASI</h2>
        <p class="lead">Panduan pemberian Makanan Pendamping ASI (MPASI) berdasarkan usia anak.</p>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card nutrition-card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <h5>0-6 Bulan</h5>
                    </div>
                    <div class="card-body">
                        <h6>ASI Eksklusif</h6>
                        <ul class="list-unstyled">
                            <li>• ASI saja, tidak perlu MPASI</li>
                            <li>• Berikan ASI sesuai kebutuhan</li>
                            <li>• Vitamin D 400 IU/hari</li>
                            <li>• Tidak perlu air putih</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card nutrition-card shadow h-100">
                    <div class="card-header bg-info text-white">
                        <h5>6-12 Bulan</h5>
                    </div>
                    <div class="card-body">
                        <h6>Mulai MPASI</h6>
                        <ul class="list-unstyled">
                            <li>• Perkenalkan makanan lunak</li>
                            <li>• Mulai dari single food</li>
                            <li>• Contoh: pisang, alpukat, nasi tim</li>
                            <li>• Tetap berikan ASI</li>
                            <li>• 2-3 kali makan pendamping</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card nutrition-card shadow h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5>12-24 Bulan</h5>
                    </div>
                    <div class="card-body">
                        <h6>Perluas Variasi</h6>
                        <ul class="list-unstyled">
                            <li>• Tingkatkan frekuensi makan</li>
                            <li>• 3 kali makan + 2x snack</li>
                            <li>• Perkenalkan semua kelompok makanan</li>
                            <li>• Protein: telur, ikan, ayam</li>
                            <li>• Sayur dan buah berbagai warna</li>
                            <li>• Tetap berikan ASI</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4 shadow">
            <div class="card-header bg-dark text-white">
                <h5>Tabel Rekomendasi Makanan Berdasarkan Usia</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Usia</th>
                                <th>Jenis Makanan</th>
                                <th>Contoh</th>
                                <th>Frekuensi</th>
                                <th>Tekstur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>6-8 Bulan</td>
                                <td>Puree/blender halus</td>
                                <td>Nasi tim, pisang, wortel</td>
                                <td>1-2x/hari</td>
                                <td>Halus, licin</td>
                            </tr>
                            <tr>
                                <td>9-11 Bulan</td>
                                <td>Lunak, dihancurkan</td>
                                <td>Nasi lembut, ayam suwir</td>
                                <td>2-3x/hari</td>
                                <td>Lembut, potong kecil</td>
                            </tr>
                            <tr>
                                <td>12-18 Bulan</td>
                                <td>Keluarga, dihancurkan</td>
                                <td>Semua makanan keluarga</td>
                                <td>3x/hari + snack</td>
                                <td>Lunak, potong kecil</td>
                            </tr>
                            <tr>
                                <td>19-24 Bulan</td>
                                <td>Bervariasi</td>
                                <td>Semua makanan kecuali berbahaya</td>
                                <td>3x/hari + 2x snack</td>
                                <td>Biasa, hindari tersedak</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4 shadow">
            <div class="card-header bg-primary text-white">
                <h5>Tips Penting</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li>Perkenalkan makanan baru satu per satu</li>
                            <li>Tunggu 3-5 hari sebelum makanan baru</li>
                            <li>Amati reaksi alergi (ruam, muntah, diare)</li>
                            <li>Jangan menambahkan gula dan garam</li>
                            <li>Pastikan kebersihan alat makan</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li>Hindari makanan yang mudah tersedak</li>
                            <li>Jangan memberikan madu sebelum 12 bulan</li>
                            <li>Pastikan anak cukup ASI</li>
                            <li>Berikan air putih setelah makan MPASI</li>
                            <li>Jadwalkan makan secara teratur</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
