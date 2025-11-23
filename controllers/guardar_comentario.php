<?php
// controllers/guardar_comentario.php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

// 1. Validar sesión
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: /views/auth/login.php");
    exit;
}

$id_usuario = $_SESSION['user_id'];
$rol_usuario = $_SESSION['user_role']; // 'admin' o 'cliente'
$id_ticket = $_POST['id_ticket'];
$comentario = trim($_POST['comentario']);
$origen = isset($_POST['origen']) ? $_POST['origen'] : 'client'; // Para saber a dónde redirigir

if (!empty($comentario)) {
    try {
        $sql = "";
        $params = [
            ':ticket' => $id_ticket,
            ':msg' => $comentario
        ];

        // 2. Lógica Bifurcada según el ROL
        if ($rol_usuario == 'admin') {
            // Es Admin: Guardamos en id_administrador
            $sql = "INSERT INTO comentarios (id_ticket, id_administrador, comentario, fecha_creacion) 
                    VALUES (:ticket, :id_user, :msg, NOW())";
            $params[':id_user'] = $id_usuario;

        } else {
            // Es Cliente: Guardamos en id_cliente
            $sql = "INSERT INTO comentarios (id_ticket, id_cliente, comentario, fecha_creacion) 
                    VALUES (:ticket, :id_user, :msg, NOW())";
            $params[':id_user'] = $id_usuario;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

    } catch (PDOException $e) {
        // Error silencioso
    }
}

// 3. Redirección Inteligente
if ($origen == 'admin') {
    header("Location: /views/admin/gestionar_ticket.php?id=" . $id_ticket);
} else {
    header("Location: /views/client/detalle_ticket.php?id=" . $id_ticket);
}
exit;
?>