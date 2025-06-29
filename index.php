<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT COUNT(*) as total_produk FROM produk");
$total_produk = $stmt->fetch()['total_produk'];

$stmt = $pdo->query("SELECT SUM(stok) as total_stok FROM produk");
$total_stok = $stmt->fetch()['total_stok'] ?? 0;

$stmt = $pdo->query("SELECT SUM(harga * stok) as total_nilai FROM produk");
$total_nilai = $stmt->fetch()['total_nilai'] ?? 0;

$stmt = $pdo->query("SELECT kategori, COUNT(*) as jumlah FROM produk GROUP BY kategori");
$data_kategori = $stmt->fetchAll();

$stmt = $pdo->query("SELECT nama_produk, stok FROM produk ORDER BY stok DESC LIMIT 5");
$produk_terlaris = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Toko Elektronik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md h-screen fixed top-0 left-0">
        <div class="flex flex-col items-center py-6 border-b">
            <i class="fas fa-mobile-alt text-blue-500 text-3xl"></i>
            <h3 class="text-xl font-bold mt-2">Toko Elektronik</h3>
        </div>
        <nav class="mt-4">
            <a href="index.php" class="flex items-center px-6 py-3 text-blue-500 font-semibold bg-indigo-100">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="admin.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                <i class="fas fa-box mr-2"></i> Kelola Produk
            </a>
            <a href="logout.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-sm text-gray-500">Selamat datang di panel admin Toko Elektronik</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600 font-medium">Admin</span>
                <a href="logout.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-md text-sm">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-5 flex items-center space-x-4">
                <i class="fas fa-box text-2xl text-indigo-500"></i>
                <div>
                    <h3 class="text-xl font-bold"><?= $total_produk ?></h3>
                    <p class="text-base text-gray-600">Total Produk</p>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-5 flex items-center space-x-4">
                <i class="fas fa-warehouse text-2xl text-green-500"></i>
                <div>
                    <h3 class="text-xl font-bold"><?= $total_stok ?></h3>
                    <p class="text-sm text-gray-600">Total Stok</p>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-5 flex items-center space-x-4">
                <i class="fas fa-money-bill-wave text-2xl text-yellow-500"></i>
                <div>
                    <h3 class="text-xl font-bold"><?= formatRupiah($total_nilai) ?></h3>
                    <p class="text-sm text-gray-600">Nilai Inventaris</p>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-5">
                <h3 class="text-lg font-semibold mb-3"><i class="fas fa-chart-pie mr-1 text-blue-500"></i> Distribusi
                    Produk per Kategori</h3>
                <div class="h-64">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-5 col-span-2">
                <h3 class="text-lg font-semibold mb-3"><i class="fas fa-chart-bar mr-1 text-blue-600"></i> Top 5 Produk
                    (Stok Tertinggi)</h3>
                <div class="h-64">
                    <canvas id="stokChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script>
        const kategoriData = <?= json_encode($data_kategori) ?>;
        const kategoriLabels = kategoriData.map(item => item.kategori);
        const kategoriValues = kategoriData.map(item => item.jumlah);

        new Chart(document.getElementById('kategoriChart'), {
            type: 'doughnut',
            data: {
                labels: kategoriLabels,
                datasets: [{
                    data: kategoriValues,
                    backgroundColor: ['#6366F1', '#EC4899', '#F59E0B', '#10B981', '#3B82F6'],
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        const stokData = <?= json_encode($produk_terlaris) ?>;
        const stokLabels = stokData.map(item => item.nama_produk);
        const stokValues = stokData.map(item => item.stok);

        new Chart(document.getElementById('stokChart'), {
            type: 'bar',
            data: {
                labels: stokLabels,
                datasets: [{
                    label: 'Stok',
                    data: stokValues,
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>

</html>