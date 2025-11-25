<?php
/*Código de conección para la base  de datos*/
$host='db';
$dbname='db_sts_proyect';
$port='5432';
$user='postgres';
$password='123456789';

try {
    // Crear la cadena de conexión (DSN)
    $dsn = "pgsql:host=$host; port=$port; dbname=$dbname";
    
    // Crear la instancia PDO (Esto es lo que buscaba auth_login.php)
    $pdo = new PDO($dsn, $user, $password);
    
    // Configurar para que lance errores (Excepciones) en caso de fallo SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si falla la conexión, detenemos todo
    die("Error en la conexión en Docker: " . $e->getMessage());
}
?>