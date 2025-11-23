<?php
// controllers/ticket_create.php
session_start();

// Usamos __DIR__ para la conexión (Blindado contra errores de ruta)
require_once __DIR__ . '/../config/db_connect.php'; 

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recolección y Limpieza
    $id_cliente = $_SESSION['user_id'];
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $prioridad = (int) $_POST['prioridad'];
    $talla = (int) $_POST['talla'];
    
    // Estado por defecto: 1 = Abierto (Según tu script SQL)
    $estado_inicial = 1; 

    // Validación básica
    if (empty($titulo) || empty($descripcion)) {
        $_SESSION['error_ticket'] = "El título y la descripción son obligatorios.";
        header("Location: /views/client/crear_ticket.php");
        exit;
    }

    try {
        // 2. Inserción en Base de Datos
        // NOTA: 'NOW()' es una función de PostgreSQL para la fecha actual
        $sql = "INSERT INTO tickets (id_cliente, id_estado, id_prioridad, id_talla, titulo, descripcion, fecha_creacion) 
                VALUES (:cliente, :estado, :prioridad, :talla, :titulo, :desc, NOW())";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':cliente' => $id_cliente,
            ':estado' => $estado_inicial,
            ':prioridad' => $prioridad,
            ':talla' => $talla,
            ':titulo' => $titulo,
            ':desc' => $descripcion
        ]);

        // 3. Éxito
        // Por ahora redirigimos al dashboard o a la lista de tickets (que haremos en el Bloque 3)
        $_SESSION['msg'] = "¡Ticket creado exitosamente! Nuestro equipo lo revisará pronto.";
        $_SESSION['msg_type'] = "success";
        
        // Redirigir a 'mis_tickets.php' (El siguiente archivo que crearemos)
        // Si aún no existe, dará 404, pero sabremos que la inserción funcionó.
        header("Location: /views/client/mis_tickets.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error_ticket'] = "Error al guardar ticket: " . $e->getMessage();
        header("Location: /views/client/crear_ticket.php");
        exit;
    }
} else {
    header("Location: /views/client/crear_ticket.php");
    exit;
}
?>