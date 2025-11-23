<?php
// views/admin/dashboard.php
session_start();

// 1. Seguridad: Solo Admins
// Si no hay sesión O el rol no es 'admin', fuera.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /views/auth/login.php");
    exit;
}

// Reutilizamos estilos globales por ahora
$page_css = 'global.css'; 
require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="/assets/css/galeria.css">
<link rel="stylesheet" href="/assets/css/global.css">

<section class="container" style="padding: 50px 0; text-align: center;">
    <div style="background: #fff; padding: 40px; border-radius: 10px; border-top: 5px solid var(--color-shadow); box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        
        <h1 style="font-family: var(--font-display); color: var(--color-shadow);">Panel de Administración</h1>
        <p style="font-size: 1.2rem; color: #666; margin-top: 10px;">
            Hola, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>. Tienes el control total. 🛠️
        </p>

        <div style="margin-top: 40px; display: flex; justify-content: center; gap: 20px;">
            <a href="/views/admin/lista_tickets.php" class="btn-accent">Gestionar Tickets</a>
            <a href="/controllers/auth_logout.php" class="btn-secondary" style="border: 2px solid var(--status-open); color: var(--status-open); padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">Cerrar Sesión</a>
        </div>

    </div>
</section>

<?php require_once '../../includes/footer.php'; ?>