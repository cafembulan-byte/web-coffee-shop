<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app-config.php';
require SUMMIT_PRIVATE_ROOT . '/src/functions.php';

$error = null;
$name = trim((string) ($_POST['name'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (mb_strlen($name) < 2) {
        $error = 'Nama minimal 2 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Masukkan email yang valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirmation) {
        $error = 'Konfirmasi password tidak sama.';
    } elseif (findUserByEmail($email)) {
        $error = 'Email tersebut sudah terdaftar.';
    } else {
        createMember($name, $email, $password);
        header('Location: login.php?registered=1');
        exit;
    }
}
?><!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Daftar Member | Summit Split</title><link rel="stylesheet" href="style.css"></head>
<body>
    <header class="site-header"><a class="brand" href="index.php"><span class="brand-mark">SS</span><span>Summit Split</span></a><a href="index.php">Kembali ke rute</a></header>
    <main class="auth-wrap">
        <section class="auth-card">
            <p class="eyebrow">Summit club</p><h1>Daftar member</h1><p class="intro-copy">Simpan profil pendakianmu dan akses perbandingan rute kapan saja.</p>
            <?php if ($error): ?><p class="form-error" role="alert"><?= e($error) ?></p><?php endif; ?>
            <form method="post">
                <label for="name">Nama</label><input id="name" name="name" type="text" autocomplete="name" required value="<?= e($name) ?>">
                <label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" required value="<?= e($email) ?>">
                <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
                <label for="password_confirmation">Ulangi password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
                <button type="submit">Buat akun member</button>
            </form>
            <p class="demo-note">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        </section>
    </main>
</body>
</html>