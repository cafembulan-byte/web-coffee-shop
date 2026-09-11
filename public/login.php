<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app-config.php';
require SUMMIT_PRIVATE_ROOT . '/src/functions.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $account = findUserByEmail($email);

    if ($account && password_verify($password, $account['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $account['id'], 'name' => $account['name'], 'role' => $account['role'], 'email' => $account['email']];
        header('Location: ' . ($account['role'] === 'admin' ? 'admin.php' : 'index.php'));
        exit;
    }

    $error = 'That email and password combination was not recognised.';
}
?><!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign in | Summit Split</title><link rel="stylesheet" href="style.css"></head>
<body>
    <header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">SS</span><span>Summit Split</span></a><a href="index.php">Back to routes</a></header>
    <main class="auth-wrap">
        <section class="auth-card">
            <p class="eyebrow">Route desk</p><h1>Sign in</h1><p class="intro-copy">Access your route tools and trail administration.</p>
            <?php if (isset($_GET['registered'])): ?><p class="form-success" role="status">Akun berhasil dibuat. Silakan masuk.</p><?php endif; ?>
            <?php if ($error): ?><p class="form-error" role="alert"><?= e($error) ?></p><?php endif; ?>
            <form method="post">
                <label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" required value="<?= e($_POST['email'] ?? '') ?>">
                <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
                <button type="submit">Sign in</button>
            </form>
            <p class="demo-note">Belum punya akun? <a href="register.php">Daftar sebagai member</a></p>
        </section>
    </main>
</body>
</html>