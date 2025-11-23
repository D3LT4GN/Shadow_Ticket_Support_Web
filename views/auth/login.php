<?php
// views/auth/login.php
require_once '../../includes/header.php';
// CORRECCIÓN: Usamos __DIR__ para asegurar que encuentre el header sin importar la URL


// Capturamos mensajes de error...
$error_msg = "";
// Capturamos mensajes de error de la sesión (si existen) y los borramos para que no salgan siempre
$error_msg = "";
if (isset($_SESSION['error_login'])) {
    $error_msg = $_SESSION['error_login'];
    unset($_SESSION['error_login']); // Limpiar el error después de leerlo
}
?>
<link rel="stylesheet" href="/assets/css/login.css">
<link rel="stylesheet" href="/assets/css/global.css">

<section class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h2>Bienvenido</h2>
            <p>Ingresa tus credenciales para gestionar tus tickets</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert-error">
                <strong>⚠️ Error:</strong> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form action="/controllers/auth_login.php" method="POST">
            
            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico (Usuario)</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@shadow.com" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-accent btn-block">Ingresar al Sistema</button>
            
        </form>
    </div>
</section>

<?php
require_once '../../includes/footer.php';
?>