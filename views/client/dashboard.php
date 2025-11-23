<?php
// views/client/dashboard.php
session_start();

// Seguridad: Si no está logueado, lo mandamos al login
if (!isset($_SESSION['user_id'])) {
    header("Location: /shadow_sts/views/auth/login.php");
    exit;
}

$page_css = 'global.css'; // Usamos el global por ahora
require_once '../../includes/header.php';
?>

<div class="container" style="padding: 50px 0; text-align: center;">
    <h2 style="font-family: var(--font-display); color: var(--color-shadow);">
        ¡Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
    </h2>
    <p style="margin-top: 20px; font-size: 1.2rem;">Has iniciado sesión correctamente en Shadow Tickets Support.</p>
    
    <div style="margin-top: 40px;">
        <a href="/shadow_sts/views/client/crear_ticket.php" class="btn-accent">Levantar nuevo Ticket</a>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>