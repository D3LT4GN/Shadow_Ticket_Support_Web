<?php
// controllers/auth_login.php
session_start();
require_once __DIR__ . '/../config/db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $_SESSION['error_login'] = "Por favor, ingresa usuario y contraseña.";
        header("Location: /views/auth/login.php");
        exit;
    }

    try {
        // --- INTENTO 1: BUSCAR EN CLIENTES ---
        $sql_client = "SELECT id_cliente, nombre, apellido_paterno, email 
                       FROM clientes 
                       WHERE email = :email AND pass = :pass";
        $stmt = $pdo->prepare($sql_client);
        $stmt->execute([':email' => $email, ':pass' => $password]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            // ES UN CLIENTE
            $_SESSION['user_id'] = $cliente['id_cliente'];
            $_SESSION['user_name'] = $cliente['nombre'] . ' ' . $cliente['apellido_paterno'];
            $_SESSION['user_role'] = 'cliente';
            
            // Mensaje Toast
            $_SESSION['welcome_toast'] = [
                'title' => "¡Bienvenido, " . $cliente['nombre'] . "!",
                'type'  => "Cuenta de Cliente"
            ];

            header("Location: /views/client/dashboard.php"); 
            exit;
        }

        // --- INTENTO 2: BUSCAR EN ADMINISTRADORES ---
        // Nota: En tu DB la tabla es 'Administrador' y columnas 'Correo'/'Pass'
        $sql_admin = "SELECT id_administrador, Nombre, Apellido, Correo 
                      FROM Administrador 
                      WHERE Correo = :email AND Pass = :pass";
        $stmt = $pdo->prepare($sql_admin);
        $stmt->execute([':email' => $email, ':pass' => $password]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // ES UN ADMINISTRADOR
            $_SESSION['user_id'] = $admin['id_administrador'];
            $_SESSION['user_name'] = $admin['Nombre'] . ' ' . $admin['Apellido'];
            $_SESSION['user_role'] = 'admin'; // Rol diferenciado

            // Mensaje Toast
            $_SESSION['welcome_toast'] = [
                'title' => "¡Bienvenido, " . $admin['Nombre'] . "!",
                'type'  => "Cuenta de Administrador"
            ];

            // Redirige al nuevo Dashboard de Admin (que crearemos abajo)
            header("Location: /views/admin/dashboard.php");
            exit;
        }

        // SI LLEGAMOS AQUÍ, NO EXISTE EN NINGUNA TABLA
        $_SESSION['error_login'] = "Credenciales incorrectas.";
        header("Location: /views/auth/login.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['error_login'] = "Error: " . $e->getMessage();
        header("Location: /views/auth/login.php");
        exit;
    }
} else {
    header("Location: /views/auth/login.php");
    exit;
}
?>