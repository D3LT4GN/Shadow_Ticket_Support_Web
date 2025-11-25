<?php
// controllers/admin/users_controller.php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

// 1. Seguridad: Solo Admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /views/auth/login.php");
    exit;
}

// 2. Acción: Eliminar Usuario
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    try {
        $id_cliente = $_GET['id'];
        // Eliminamos al cliente (La BD hará borrado en cascada de tickets por la FK)
        $sql = "DELETE FROM clientes WHERE id_cliente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_cliente]);
        
        $_SESSION['toast_msg'] = "Cliente eliminado correctamente.";
        $_SESSION['toast_type'] = "success";

    } catch (PDOException $e) {
        $_SESSION['toast_msg'] = "Error al eliminar: " . $e->getMessage();
        $_SESSION['toast_type'] = "error";
    }
    
    header("Location: /views/admin/clientes.php");
    exit;
}

// 3. Acción: Crear Usuario (Alta Rápida)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    // Datos opcionales para evitar errores de NOT NULL en BD
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : 0; 

    try {
        // Validar si el correo ya existe
        $check = $pdo->prepare("SELECT id_cliente FROM clientes WHERE email = :email");
        $check->execute([':email' => $email]);
        
        if ($check->rowCount() > 0) {
            throw new Exception("El correo ya está registrado.");
        }

        // Insertar nuevo cliente
        $sql = "INSERT INTO clientes (nombre, apellido_paterno, apellido_materno, email, pass, telefono, empresa, fecha_registro) 
                VALUES (:nom, :ape, '', :email, :pass, :tel, 'Sin asignar', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nombre,
            ':ape' => $apellido,
            ':email' => $email,
            ':pass' => $password, // Recuerda: En producción usar password_hash()
            ':tel' => $telefono
        ]);

        $_SESSION['toast_msg'] = "Cliente registrado exitosamente.";
        $_SESSION['toast_type'] = "success";

    } catch (Exception $e) {
        $_SESSION['toast_msg'] = "Error: " . $e->getMessage();
        $_SESSION['toast_type'] = "error";
    }

    header("Location: /views/admin/clientes.php");
    exit;
}
?>