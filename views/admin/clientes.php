<?php
// views/admin/clientes.php
session_start();

// Seguridad Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /views/auth/login.php");
    exit;
}

require_once '../../config/db_connect.php';
$page_css = 'global.css'; // Usamos estilos base
require_once '../../includes/header.php';

// Obtener lista de clientes
try {
    $sql = "SELECT * FROM clientes ORDER BY fecha_registro DESC";
    $stmt = $pdo->query($sql);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error cargando clientes.";
}

// Mensajes Flash (Toast simulación visual rápida)
if (isset($_SESSION['toast_msg'])) {
    $msg = $_SESSION['toast_msg'];
    $type = $_SESSION['toast_type']; // success o error
    unset($_SESSION['toast_msg']);
    unset($_SESSION['toast_type']);
}
?>

<!-- Estilos inline rápidos para esta vista admin -->
<style>
    .admin-panel-grid { display: grid; gap: 40px; margin-top: 30px; }
    .card-admin { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 4px solid var(--color-shadow); }
    .table-admin { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .table-admin th, .table-admin td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    .table-admin th { background: #f8f9fa; color: var(--color-shadow); }
    .btn-delete { color: #e74c3c; text-decoration: none; font-weight: bold; font-size: 0.9rem; border: 1px solid #e74c3c; padding: 4px 8px; border-radius: 4px; }
    .btn-delete:hover { background: #e74c3c; color: white; }
</style>

<section class="container" style="padding: 40px 0;">
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-family: var(--font-display); color: var(--color-shadow);">Administrar Clientes</h2>
        <a href="/views/admin/dashboard.php" style="color: #666; text-decoration: none;">← Volver al Dashboard</a>
    </div>

    <!-- Mensaje de Feedback -->
    <?php if(isset($msg)): ?>
        <div style="padding: 15px; margin-top: 20px; border-radius: 5px; color: white; background-color: <?php echo ($type=='success')?'var(--status-solved)':'var(--status-open)'; ?>;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="admin-panel-grid">
        
        <!-- 1. FORMULARIO DE ALTA RÁPIDA -->
        <div class="card-admin">
            <h3 style="margin-bottom: 15px; color: var(--color-accent);">➕ Dar de Alta Nuevo Cliente</h3>
            <form action="/controllers/admin/users_controller.php" method="POST" style="display: flex; gap: 15px; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 0.85rem; font-weight: bold;">Nombre</label>
                    <input type="text" name="nombre" placeholder="Ej: Juan" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 0.85rem; font-weight: bold;">Apellido</label>
                    <input type="text" name="apellido" placeholder="Ej: Pérez" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="flex: 1; min-width: 250px;">
                    <label style="font-size: 0.85rem; font-weight: bold;">Correo Electrónico</label>
                    <input type="email" name="email" placeholder="cliente@empresa.com" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 0.85rem; font-weight: bold;">Contraseña Temporal</label>
                    <input type="text" name="password" placeholder="Ej: 12345" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="width: 100%; text-align: right; margin-top: 10px;">
                    <button type="submit" class="btn-accent">Registrar Cliente</button>
                </div>
            </form>
        </div>

        <!-- 2. TABLA DE CLIENTES EXISTENTES -->
        <div class="card-admin" style="border-top-color: var(--status-progress);">
            <h3 style="margin-bottom: 15px;">📂 Clientes Registrados (<?php echo count($clientes); ?>)</h3>
            
            <div style="overflow-x: auto;">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Correo</th>
                            <th>Empresa</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clientes as $c): ?>
                            <tr>
                                <td>#<?php echo $c['id_cliente']; ?></td>
                                <td><?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido_paterno']); ?></td>
                                <td><?php echo htmlspecialchars($c['email']); ?></td>
                                <td><?php echo htmlspecialchars($c['empresa'] ?? 'N/A'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($c['fecha_registro'])); ?></td>
                                <td>
                                    <a href="/controllers/admin/users_controller.php?action=delete&id=<?php echo $c['id_cliente']; ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('¿Estás seguro de borrar a este cliente? Se borrarán también sus tickets.');">
                                       🗑️ Borrar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<?php require_once '../../includes/footer.php'; ?>