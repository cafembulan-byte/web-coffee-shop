<?php

declare(strict_types=1);

function database(): PDO
{
    static $connection;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $databaseDirectory = (defined('SUMMIT_PRIVATE_ROOT') ? SUMMIT_PRIVATE_ROOT : dirname(__DIR__)) . '/database';
    if (!is_dir($databaseDirectory)) {
        mkdir($databaseDirectory, 0750, true);
    }

    $connection = new PDO('sqlite:' . $databaseDirectory . '/summit.sqlite');
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $connection->exec(file_get_contents($databaseDirectory . '/schema.sql'));
    seedDemoAccounts($connection);

    return $connection;
}

function seedDemoAccounts(PDO $connection): void
{
    $accounts = [
        ['Trail Admin', 'admin@example.com', 'summit-admin', 'admin'],
        ['Trail Hiker', 'hiker@example.com', 'summit-user', 'user'],
    ];
    $statement = $connection->prepare(
        'INSERT OR IGNORE INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)'
    );

    foreach ($accounts as [$name, $email, $password, $role]) {
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);
    }
}

function findUserByEmail(string $email): ?array
{
    $statement = database()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => strtolower(trim($email))]);
    $user = $statement->fetch();

    return $user ?: null;
}

function createMember(string $name, string $email, string $password): bool
{
    $statement = database()->prepare(
        'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)'
    );

    return $statement->execute([
        'name' => trim($name),
        'email' => strtolower(trim($email)),
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'user',
    ]);
}

function allMembers(): array
{
    return database()->query(
        'SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC, id DESC'
    )->fetchAll();
}