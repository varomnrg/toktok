<?php
require_once 'config.php';

if ($_POST) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['nama_lengkap'];
        $_SESSION['admin_username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Toko Elektronik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-500 to-blue-900 min-h-screen flex items-center justify-center font-sans">

    <div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-md">
        <div class="text-center mb-6">
            <i class="fas fa-mobile-alt text-4xl text-blue-500 mb-2"></i>
            <h2 class="text-2xl font-semibold text-gray-800">Admin Panel</h2>
            <p class="text-sm text-gray-500">Toko Elektronik Management System</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-md mb-4 flex items-center gap-2 border border-red-300">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">
                    <i class="fas fa-user mr-1"></i> Username
                </label>
                <input type="text" id="username" name="username" required
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    <i class="fas fa-lock mr-1"></i> Password
                </label>
                <input type="password" id="password" name="password" required
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-700 text-white font-semibold py-2.5 rounded-md
                shadow-md hover:shadow-xl active:scale-95
                hover:bg-blue-800 transition-all duration-200 ease-in-out
                transform hover:-translate-y-1">
                <i class="fas fa-sign-in-alt mr-2"></i> Masuk
            </button>

        </form>

        <div class="bg-blue-50 mt-6 p-4 rounded-md border-l-4 border-blue-400">
            <h4 class="text-blue-600 font-semibold mb-1"><i class="fas fa-info-circle"></i> Demo Account</h4>
            <p class="text-sm text-gray-700"><strong>Username:</strong> admin</p>
            <p class="text-sm text-gray-700"><strong>Password:</strong> admin123</p>
        </div>
    </div>

</body>

</html>