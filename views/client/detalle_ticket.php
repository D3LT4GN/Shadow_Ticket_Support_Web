<?php
// views/client/detalle_ticket.php
session_start();
require_once '../../config/db_connect.php'; // Usamos el de PDO

if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit;
}

// Validar ID del ticket en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /views/client/mis_tickets.php");
    exit;
}

$id_ticket = $_GET['id'];
$id_cliente = $_SESSION['user_id'];

try {
    // 1. Obtener Info del Ticket (Solo si pertenece al cliente)
    $sql = "SELECT t.*, e.nombre_estado, p.nombre_prioridad 
            FROM tickets t
            JOIN cat_estados e ON t.id_estado = e.id_estado
            JOIN cat_prioridades p ON t.id_prioridad = p.id_prioridad
            WHERE t.id_ticket = :id AND t.id_cliente = :cliente";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_ticket, ':cliente' => $id_cliente]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        // Si no encuentra el ticket o no es suyo
        header("Location: /views/client/mis_tickets.php");
        exit;
    }

    // 2. Obtener Comentarios (Chat)
    // Ordenados cronológicamente (viejos arriba, nuevos abajo)
    $sql_chat = "SELECT c.*, a.Nombre as nombre_admin, cl.nombre as nombre_cliente
                 FROM comentarios c
                 LEFT JOIN Administrador a ON c.id_administrador = a.id_administrador
                 LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
                 WHERE c.id_ticket = :id
                 ORDER BY c.fecha_creacion ASC";
                 
    $stmt_chat = $pdo->prepare($sql_chat);
    $stmt_chat->execute([':id' => $id_ticket]);
    $mensajes = $stmt_chat->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$page_css = 'detalle.css';
require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="/assets/css/detalle.css">
<link rel="stylesheet" href="/assets/css/global.css">

<section class="container detail-wrapper">
    
    <div class="info-panel">
        <div class="ticket-meta-header">
            <?php if (!empty($ticket['notas_admin'])): ?>
                <div style="background-color: #fff3cd; border-left: 5px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 20px; color: #856404;">
                    <strong style="display: block; margin-bottom: 5px;">🔔 Nota del Soporte:</strong>
                    <?php echo nl2br(htmlspecialchars($ticket['notas_admin'])); ?>
                </div>
            <?php endif; ?>
            <a href="/views/client/mis_tickets.php" style="text-decoration: none; color: #999; font-size: 0.9rem;">← Volver</a>
            <div style="margin-top: 10px;">
                <span class="badge-l" style="background: #eee; color: #555;">#<?php echo str_pad($ticket['id_ticket'], 4, '0', STR_PAD_LEFT); ?></span>
                <h2 style="margin-top: 10px;"><?php echo htmlspecialchars($ticket['titulo']); ?></h2>
            </div>
        </div>

        <?php 
            $estado = $ticket['nombre_estado'];
            $step1 = 'active'; // Abierto siempre activo al inicio
            $step2 = '';
            $step3 = '';
            
            if ($estado == 'En Progreso' || $estado == 'Resuelto') {
                $step1 = 'completed';
                $step2 = 'active';
            }
            if ($estado == 'Resuelto') {
                $step2 = 'completed';
                $step3 = 'active completed';
            }
        ?>
        <div class="timeline">
            <div class="step <?php echo $step1; ?>">
                <div class="step-circle">1</div>
                <div class="step-label">Recibido</div>
            </div>
            <div class="step <?php echo $step2; ?>">
                <div class="step-circle">2</div>
                <div class="step-label">Revisión</div>
            </div>
            <div class="step <?php echo $step3; ?>">
                <div class="step-circle">3</div>
                <div class="step-label">Resuelto</div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <h4 style="color: var(--color-shadow);">Descripción Original:</h4>
            <p style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-top: 10px; color: #555;">
                <?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?>
            </p>
        </div>

        <div style="margin-top: 20px; font-size: 0.9rem; color: #777;">
            <p><strong>Prioridad:</strong> <?php echo $ticket['nombre_prioridad']; ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?></p>
        </div>
    </div>

    <div class="chat-panel">
        <div class="chat-header">
            <div class="avatar-support">STS</div>
            <div>
                <div style="font-weight: bold;">Soporte Shadow</div>
                <div style="font-size: 0.8rem; opacity: 0.8;">En línea</div>
            </div>
        </div>

        <div class="chat-messages" id="chatBox">
            <div class="message support">
                Hola, hemos recibido tu ticket. Un agente lo revisará pronto. Puedes escribir más detalles aquí.
                <span class="msg-time"><?php echo date('H:i', strtotime($ticket['fecha_creacion'])); ?></span>
            </div>

            <?php foreach($mensajes as $msg): ?>
                <?php 
                    // Determinar si es mensaje MÍO (Cliente) o de ELLOS (Admin)
                    // Si id_cliente tiene valor, es mío. Si id_admin tiene valor, es soporte.
                    $tipo = ($msg['id_cliente'] == $id_cliente) ? 'me' : 'support';
                ?>
                <div class="message <?php echo $tipo; ?>">
                    <?php echo nl2br(htmlspecialchars($msg['comentario'])); ?>
                    <span class="msg-time"><?php echo date('H:i', strtotime($msg['fecha_creacion'])); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <form action="/controllers/guardar_comentario.php" method="POST" class="chat-input-area">
            <input type="hidden" name="id_ticket" value="<?php echo $id_ticket; ?>">
            <input type="text" name="comentario" class="chat-input" placeholder="Escribe un mensaje..." required autocomplete="off">
            <button type="submit" class="btn-send">➤</button>
        </form>
    </div>

</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chatBox = document.getElementById("chatBox");
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>

<?php require_once '../../includes/footer.php'; ?>