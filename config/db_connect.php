<?php
/* config/db_connect.php - Versión Híbrida (Local + Nube) */

// 1. Intentamos leer variables de entorno (Configuración de Render)
// 2. Si no existen, usamos los valores fijos de Docker Local ('db', '123456789')
$host     = getenv('DB_HOST') ? getenv('DB_HOST') : 'db';
$dbname   = getenv('DB_NAME') ? getenv('DB_NAME') : 'db_sts_proyect';
$port     = getenv('DB_PORT') ? getenv('DB_PORT') : '5432';
$user     = getenv('DB_USER') ? getenv('DB_USER') : 'postgres';
$password = getenv('DB_PASS') ? getenv('DB_PASS') : '123456789';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En producción, es mejor no mostrar el error detallado al público, pero para debug está bien.
    die("Error de Conexión: " . $e->getMessage());
}
?>