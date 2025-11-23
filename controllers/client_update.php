<?php
// controllers/client_update.php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recolectar datos
    $id = $_SESSION['user_id'];
    $nombre = trim($_POST['nombre']);
    $segundo = trim($_POST['segundo_nombre']);
    $paterno = trim($_POST['apellido_paterno']);
    $materno = trim($_POST['apellido_materno']);
    $telefono = trim($_POST['telefono']);
    $empresa = trim($_POST['empresa']);

    try {
        // 2. Query UPDATE
        $sql = "UPDATE clientes SET 
                nombre = :nom, 
                segundo_nombre = :seg, 
                apellido_paterno = :pat, 
                apellido_materno = :mat, 
                telefono = :tel, 
                empresa = :emp 
                WHERE id_cliente = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nombre,
            ':seg' => $segundo,
            ':pat' => $paterno,
            ':mat' => $materno,
            ':tel' => $telefono,
            ':emp' => $empresa,
            ':id'  => $id
        ]);

        // 3. Feedback positivo
        $_SESSION['msg'] = "¡Tus datos han sido actualizados correctamente!";
        $_SESSION['msg_type'] = "success";
        
        // Actualizamos el nombre en la sesión también por si cambió
        $_SESSION['user_name'] = $nombre . ' ' . $paterno;

    } catch (PDOException $e) {
        $_SESSION['msg'] = "Error al actualizar: " . $e->getMessage();
        $_SESSION['msg_type'] = "error";
    }

    // 4. Redirigir de vuelta al perfil
    header("Location: /views/client/perfil.php");
    exit;
}
?>