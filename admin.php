<?php
require_once 'config.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$message_type = '';

// Proses tambah produk
if ($_POST && $_POST['action'] == 'add') {
    $kode_produk = sanitize($_POST['kode_produk']);
    $nama_produk = sanitize($_POST['nama_produk']);
    $kategori = sanitize($_POST['kategori']);
    $harga = floatval($_POST['harga']);
    $thumbnail = sanitize($_POST['thumbnail']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $stok = intval($_POST['stok']);

    try {
        $stmt = $pdo->prepare("INSERT INTO produk (kode_produk, nama_produk, kategori, harga, thumbnail, deskripsi, stok) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kode_produk, $nama_produk, $kategori, $harga, $thumbnail, $deskripsi, $stok]);
        $message = "Produk berhasil ditambahkan!";
        $message_type = "success";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'kode_produk')) {
            $message = "Kode produk sudah digunakan, silakan gunakan yang lain.";
        } else {
            $message = "Error: " . $e->getMessage();
        }
        $message_type = "error";
    }
}

// Proses edit produk
if ($_POST && $_POST['action'] == 'edit') {
    $id = intval($_POST['id']);
    $kode_produk = sanitize($_POST['kode_produk']);
    $nama_produk = sanitize($_POST['nama_produk']);
    $kategori = sanitize($_POST['kategori']);
    $harga = floatval($_POST['harga']);
    $thumbnail = sanitize($_POST['thumbnail']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $stok = intval($_POST['stok']);

    try {
        $stmt = $pdo->prepare("UPDATE produk SET kode_produk=?, nama_produk=?, kategori=?, harga=?, thumbnail=?, deskripsi=?, stok=? WHERE id=?");
        $stmt->execute([$kode_produk, $nama_produk, $kategori, $harga, $thumbnail, $deskripsi, $stok, $id]);
        $message = "Produk berhasil diupdate!";
        $message_type = "success";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'kode_produk')) {
            $message = "Kode produk sudah digunakan, silakan gunakan yang lain.";
        } else {
            $message = "Error: " . $e->getMessage();
        }
        $message_type = "error";
    }

}

// Proses hapus produk
if ($_GET && $_GET['action'] == 'delete' && $_GET['id']) {
    $id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Produk berhasil dihapus!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Ambil data produk
$stmt = $pdo->query("SELECT * FROM produk ORDER BY created_at DESC");
$produk_list = $stmt->fetchAll();

// Ambil data untuk edit
$edit_data = null;
if ($_GET && $_GET['action'] == 'edit' && $_GET['id']) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

$editing = $edit_data !== null;
$action = $editing ? 'edit' : 'add';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Toko Elektronik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body.modal-open {
            overflow: hidden;
        }

        .modal-backdrop {
            backdrop-filter: blur(4px);
        }

        .modal-enter {
            animation: modalEnter 0.3s ease-out;
        }

        .modal-exit {
            animation: modalExit 0.3s ease-in;
        }

        @keyframes modalEnter {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes modalExit {
            from {
                opacity: 1;
                transform: scale(1) translateY(0);
            }

            to {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex">
    <aside class="w-64 bg-white shadow-md h-screen fixed top-0 left-0">
        <div class="flex flex-col items-center py-6 border-b">
            <i class="fas fa-mobile-alt text-blue-500 text-3xl"></i>
            <h3 class="text-xl font-bold mt-2">Toko Elektronik</h3>
        </div>
        <nav class="mt-4">
            <a href="index.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                <i class="fas fa-box mr-2"></i> Dashboard
            </a>
            <a href="admin.php" class="flex items-center px-6 py-3 text-blue-500 font-semibold bg-indigo-100">
                <i class="fas fa-tachometer-alt mr-2"></i> Kelola Produk
            </a>
            <a href="logout.php" class="flex items-center px-6 py-3 text-gray-700 hover:bg-gray-100">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </nav>
    </aside>

    <main class="flex-1 ml-64 p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Produk</h1>
                <p class="text-sm text-gray-500">Kelola produk elektronik anda di sini</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600 font-medium">Admin</span>
                <a href="logout.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-md text-sm">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div
                class="my-4 p-4 rounded-lg text-sm 
                    <?= $message_type === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-6">
            <button
                class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-700 transition duration-200"
                onclick="openModal('addModal')">
                <i class="fas fa-plus mr-1"></i> Tambah Produk
            </button>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full table-auto text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Thumbnail</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach ($produk_list as $produk): ?>
                        <tr class="border-b hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3">
                                <img src="<?= $produk['thumbnail'] ?>" alt="<?= $produk['nama_produk'] ?>"
                                    class="w-14 h-14 rounded object-cover shadow-sm" />
                            </td>
                            <td class="px-4 py-3 font-medium"><?= $produk['kode_produk'] ?></td>
                            <td class="px-4 py-3"><?= $produk['nama_produk'] ?></td>
                            <td class="px-4 py-3">
                                <span
                                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"><?= $produk['kategori'] ?></span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-green-600"><?= formatRupiah($produk['harga']) ?></td>
                            <td class="px-4 py-3">
                                <span class="<?= $produk['stok'] > 0 ? 'text-green-600' : 'text-red-600' ?> font-medium">
                                    <?= $produk['stok'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 space-x-2">
                                <button onclick="showProductDetail(<?= htmlspecialchars(json_encode($produk)) ?>)"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                                <a href="admin.php?action=edit&id=<?= $produk['id'] ?>"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-xs transition duration-200">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <a href="admin.php?action=delete&id=<?= $produk['id'] ?>"
                                    onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs transition duration-200">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal add -->
    <div id="addModal"
        class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop flex items-center justify-center z-50 p-4 <?= $editing ? '' : 'hidden' ?>">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden modal-enter">

            <div class="bg-gradient-to-r from-blue-500 to-indigo-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <i class="fas fa-<?= $editing ? 'edit' : 'plus' ?> text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white"><?= $editing ? 'Edit Produk' : 'Tambah Produk Baru' ?>
                        </h2>
                        <p class="text-indigo-100 text-sm">
                            <?= $editing ? 'Perbarui informasi produk' : 'Isi detail produk yang akan ditambahkan' ?>
                        </p>
                    </div>
                </div>
                <button class="text-white hover:text-indigo-200 text-2xl font-bold transition duration-200"
                    onclick="closeModal('addModal')">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <form method="POST" action="admin.php" class="space-y-6">
                    <input type="hidden" name="action" value="<?= $action ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-barcode text-gray-400 mr-1"></i> Kode Produk
                            </label>
                            <input type="text" name="kode_produk" value="<?= $edit_data['kode_produk'] ?? '' ?>"
                                required
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 transition duration-200"
                                placeholder="Masukkan kode produk">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag text-gray-400 mr-1"></i> Nama Produk
                            </label>
                            <input type="text" name="nama_produk" value="<?= $edit_data['nama_produk'] ?? '' ?>"
                                required
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 transition duration-200"
                                placeholder="Masukkan nama produk">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-list text-gray-400 mr-1"></i> Kategori
                            </label>
                            <input type="text" name="kategori" value="<?= $edit_data['kategori'] ?? '' ?>" required
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 transition duration-200"
                                placeholder="Masukkan kategori produk">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-dollar-sign text-gray-400 mr-1"></i> Harga
                            </label>
                            <input type="number" name="harga" value="<?= $edit_data['harga'] ?? '' ?>" required min="0"
                                step="0.01"
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 transition duration-200"
                                placeholder="Masukkan harga produk">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-boxes text-gray-400 mr-1"></i> Stok
                        </label>
                        <input type="number" name="stok" value="<?= $edit_data['stok'] ?? '' ?>" required min="0"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 transition duration-200"
                            placeholder="Masukkan jumlah stok">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-image text-gray-400 mr-1"></i> Link Gambar
                        </label>
                        <input type="url" name="thumbnail" value="<?= $edit_data['thumbnail'] ?? '' ?>" required
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 transition duration-200"
                            placeholder="https://example.com/image.jpg">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-gray-400 mr-1"></i> Deskripsi
                        </label>
                        <textarea name="deskripsi" rows="4"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200 focus:ring-2 transition duration-200 resize-none"
                            placeholder="Masukkan deskripsi produk..."><?= $edit_data['deskripsi'] ?? '' ?></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeModal('addModal')"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-lg transition duration-200 font-medium shadow-lg">
                            <i class="fas fa-<?= $editing ? 'save' : 'plus' ?> mr-2"></i>
                            <?= $editing ? 'Perbarui Produk' : 'Tambah Produk' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal detail -->
    <div id="detailModal"
        class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden modal-enter">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Detail Produk</h2>
                        <p class="text-blue-100 text-sm">Informasi lengkap produk</p>
                    </div>
                </div>
                <button class="text-white hover:text-blue-200 text-2xl font-bold transition duration-200"
                    onclick="closeModal('detailModal')">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1">
                        <div class="bg-gray-100 rounded-lg p-4 text-center">
                            <img id="detail-image" src="" alt=""
                                class="max-w-full h-64 object-cover rounded-lg mx-auto shadow-md">
                        </div>
                    </div>
                    <div class="lg:col-span-2 space-y-4">
                        <div class="border-b pb-4">
                            <h3 id="detail-name" class="text-2xl font-bold text-gray-800 mb-2"></h3>
                            <div class="flex items-center space-x-4">
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-mono">
                                    <i class="fas fa-barcode mr-1"></i>
                                    <span id="detail-code"></span>
                                </span>
                                <span id="detail-category"
                                    class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center space-x-2 mb-1">
                                    <i class="fas fa-dollar-sign text-green-600"></i>
                                    <span class="text-sm font-medium text-green-700">Harga</span>
                                </div>
                                <div id="detail-price" class="text-2xl font-bold text-green-600"></div>
                            </div>

                            <div id="detail-stock-container" class="p-4 rounded-lg border">
                                <div class="flex items-center space-x-2 mb-1">
                                    <i class="fas fa-boxes"></i>
                                    <span class="text-sm font-medium">Stok</span>
                                </div>
                                <div id="detail-stock" class="text-2xl font-bold"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 mb-3">
                                <i class="fas fa-align-left text-gray-600"></i>
                                <span class="font-semibold text-gray-700">Deskripsi</span>
                            </div>
                            <div id="detail-description"
                                class="text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-lg"></div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                                <div>
                                    <i class="fa-solid fa-calendar-days mr-1"></i>
                                    <span>Dibuat: </span>
                                    <span id="detail-created" class="font-medium text-xs"></span>
                                </div>
                                <div>
                                    <i class="fa-solid fa-calendar-days mr-1"></i>
                                    <span>Diperbarui: </span>
                                    <span id="detail-updated" class="font-medium text-xs"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t">
                <button type="button" onclick="closeModal('detailModal')"
                    class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition duration-200 font-medium">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            const modalContent = modal.querySelector('.bg-white');

            modal.classList.remove('hidden');
            document.body.classList.add('modal-open');

            modalContent.classList.add('modal-enter');
            modalContent.classList.remove('modal-exit');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const modalContent = modal.querySelector('.bg-white');

            modalContent.classList.add('modal-exit');
            modalContent.classList.remove('modal-enter');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('modal-open');

                if (window.location.search.includes('action=edit')) {
                    window.history.replaceState({}, document.title, 'admin.php');
                }
            }, 300);
        }

        function showProductDetail(product) {
            document.getElementById('detail-image').src = product.thumbnail;
            document.getElementById('detail-image').alt = product.nama_produk;
            document.getElementById('detail-name').textContent = product.nama_produk;
            document.getElementById('detail-code').textContent = product.kode_produk;
            document.getElementById('detail-category').textContent = product.kategori;

            document.getElementById('detail-price').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(product.harga);

            const stockElement = document.getElementById('detail-stock');
            const stockContainer = document.getElementById('detail-stock-container');
            stockElement.textContent = product.stok + ' unit';

            if (product.stok > 0) {
                stockContainer.className = 'p-4 rounded-lg border bg-green-50 border-green-200';
                stockElement.className = 'text-2xl font-bold text-green-600';
                stockContainer.querySelector('i').className = 'fas fa-boxes text-green-600';
                stockContainer.querySelector('span').className = 'text-sm font-medium text-green-700';
            } else {
                stockContainer.className = 'p-4 rounded-lg border bg-red-50 border-red-200';
                stockElement.className = 'text-2xl font-bold text-red-600';
                stockContainer.querySelector('i').className = 'fas fa-boxes text-red-600';
                stockContainer.querySelector('span').className = 'text-sm font-medium text-red-700';
            }
            document.getElementById('detail-description').textContent = product.deskripsi || 'Tidak ada deskripsi';
            document.getElementById('detail-created').textContent = product.created_at ?
                new Date(product.created_at).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }) : 'Tidak tersedia';

            document.getElementById('detail-updated').textContent = product.updated_at ?
                new Date(product.updated_at).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }) : 'Tidak tersedia';

            openModal('detailModal');
        }

        document.getElementById('addModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal('addModal');
            }
        });

        document.getElementById('detailModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal('detailModal');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (!document.getElementById('addModal').classList.contains('hidden')) {
                    closeModal('addModal');
                }
                if (!document.getElementById('detailModal').classList.contains('hidden')) {
                    closeModal('detailModal');
                }
            }
        });

        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'edit') {
                openModal('addModal');
            }
        }
    </script>
</body>

</html>