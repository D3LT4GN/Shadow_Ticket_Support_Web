<?php
// views/client/crear_ticket.php
session_start();

// 1. Seguridad: Solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit;
}

$page_css = 'ticket.css'; // Cargamos el CSS específico
require_once '../../includes/header.php';

// Capturar errores si el controlador nos devuelve
$error_msg = "";
if (isset($_SESSION['error_ticket'])) {
    $error_msg = $_SESSION['error_ticket'];
    unset($_SESSION['error_ticket']);
}
?>

<link rel="stylesheet" href="/assets/css/ticket.php">
<link rel="stylesheet" href="/assets/css/global.css">
<section class="container ticket-wrapper">
    <div class="ticket-card">
        <div class="ticket-header">
            <h2>Levantar Nuevo Ticket</h2>
            <p>Describe tu incidencia y clasifícala para ayudarte mejor.</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert-error" style="margin-bottom: 20px; padding: 10px; background: #fee; color: red; border: 1px solid red; border-radius: 4px;">
                <strong>⚠️ Atención:</strong> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form action="/controllers/ticket_create.php" method="POST">
            
            <div class="form-group">
                <label class="form-label">Título del Problema</label>
                <input type="text" name="titulo" class="form-input" placeholder="Ej: Error al generar reporte de ventas..." required>
            </div>

            <div class="form-group">
                <label class="form-label">Prioridad del Negocio</label>
                <select name="prioridad" class="form-select" required>
                    <option value="1">Baja - Puede esperar un poco</option>
                    <option value="2" selected>Media - Afecta mi trabajo parcial</option>
                    <option value="3">Alta - Sistema detenido / Urgente</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Complejidad Estimada (T-shirt Sizing)</label>
                <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Selecciona una talla basada en el esfuerzo que crees que requiere:</p>
                
                <div class="sizing-options">
                    <input type="radio" name="talla" id="talla_s" value="1" required>
                    <label for="talla_s" class="size-card">
                        <strong>S</strong>
                        <span>Pequeña</span>
                    </label>

                    <input type="radio" name="talla" id="talla_m" value="2">
                    <label for="talla_m" class="size-card">
                        <strong>M</strong>
                        <span>Mediana</span>
                    </label>

                    <input type="radio" name="talla" id="talla_l" value="3">
                    <label for="talla_l" class="size-card">
                        <strong>L</strong>
                        <span>Grande</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Descripción Detallada</label>
                <textarea name="descripcion" class="form-textarea" placeholder="Explica qué estabas haciendo cuando ocurrió el problema, qué mensajes aparecieron, etc." required></textarea>
            </div>

            <div class="form-actions">
                <a href="/views/client/mis_tickets.php" style="color: #666; margin-right: 15px; text-decoration: none;">Cancelar</a>
                <button type="submit" class="btn-accent">🚀 Crear Ticket</button>
            </div>

        </form>
    </div>
</section>

<?php require_once '../../includes/footer.php'; ?>