<?php
// Inicia la sesión para recordar al usuario entre páginas
session_start();

// Datos de conexión a MySQL (valores por defecto de XAMPP)
const DB_HOST = 'localhost';
const DB_NAME = 'nike_db';
const DB_USER = 'root';
const DB_PASS = '';

// Devuelve la conexión a la base de datos (se crea una sola vez)
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}
// Escapa el texto para mostrarlo de forma segura en HTML
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
// Genera un token único por sesión para proteger los formularios
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
// Comprueba que el token enviado coincide con el de la sesión
function csrf_valido(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
