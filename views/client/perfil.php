<?php
// views/client/perfil.php
session_start();

// 1. Seguridad: Si no hay sesión, ¡fuera!
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit;
}

$page_css = 'perfil.css'; // Cargamos el estilo nuevo
require_once '../../includes/header.php';
require_once '../../config/db_connect.php'; // Necesitamos conexión para sacar los datos

// 2. CRUD (READ): Obtener datos actuales del usuario
$id_usuario = $_SESSION['user_id'];
$sql = "SELECT * FROM clientes WHERE id_cliente = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id_usuario);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Capturar mensajes de éxito/error (Flash messages)
$msg = "";
$msg_type = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    $msg_type = $_SESSION['msg_type']; // 'success' o 'error'
    unset($_SESSION['msg']);
    unset($_SESSION['msg_type']);
}
?>
<link rel="stylesheet" href="/assets/css/perfil.css">
<link rel="stylesheet" href="/assets/css/global.css">
<section class="container profile-wrapper">
    <div class="profile-card">
        <div class="profile-header">
            <h2>Mi Perfil</h2>
            <a href="/controllers/auth_logout.php" class="btn-secondary">Cerrar Sesión</a>
        </div>

        <?php if($msg): ?>
            <div style="padding: 10px; margin-bottom: 20px; border-radius: 4px; 
                background-color: <?php echo ($msg_type == 'success') ? 'var(--status-solved)' : 'var(--status-open)'; ?>; 
                color: white; font-weight: bold;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form action="/controllers/client_update.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-input" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Segundo Nombre</label>
                    <input type="text" name="segundo_nombre" class="form-input" value="<?php echo htmlspecialchars($usuario['segundo_nombre']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" class="form-input" value="<?php echo htmlspecialchars($usuario['apellido_paterno']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Apellido Materno</label>
                    <input type="text" name="apellido_materno" class="form-input" value="<?php echo htmlspecialchars($usuario['apellido_materno']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico (No editable)</label>
                    <input type="email" class="form-input" value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="number" name="telefono" class="form-input" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Empresa</label>
                    <input type="text" name="empresa" class="form-input" value="<?php echo htmlspecialchars($usuario['empresa']); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-accent">Guardar Cambios</button>
            </div>
        </form>
    </div>
</section>

<?php require_once '../../includes/footer.php'; ?>