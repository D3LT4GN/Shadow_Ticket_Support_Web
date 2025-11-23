<?php
// controllers/admin/update_ticket.php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

// Seguridad Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /views/auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_ticket = $_POST['id_ticket'];
    $id_estado = $_POST['id_estado'];
    $id_prioridad = $_POST['id_prioridad'];
    $notas = trim($_POST['notas_admin']); // <--- NUEVO DATO

    try {
        // Actualizamos el ticket
        // También asignamos al admin actual como el responsable (id_administrador_asignado)
        $sql = "UPDATE tickets SET 
                id_estado = :estado, 
                id_prioridad = :prioridad,
                id_administrador_asignado = :admin,
                notas_admin = :notas,  /* <--- NUEVA COLUMNA */
                fecha_actualizada = NOW()
                WHERE id_ticket = :id";

        // Si el estado es 3 (Resuelto), podríamos actualizar fecha_cierre, 
        // pero por simplicidad usaremos fecha_actualizada.
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':estado' => $id_estado,
            ':prioridad' => $id_prioridad,
            ':admin' => $_SESSION['user_id'],
            ':notas' => $notas,       /* <--- NUEVO PARÁMETRO */
            ':id' => $id_ticket
        ]);

        $_SESSION['toast_msg'] = "Ticket actualizado correctamente.";

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar.";
    }

    // Volver a la gestión
    header("Location: /views/admin/lista_tickets.php");
    exit;
}
?>