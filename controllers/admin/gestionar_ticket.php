<?php
// views/admin/gestionar_ticket.php
session_start();
require_once '../../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /views/auth/login.php");
    exit;
}

$id_ticket = $_GET['id'];

// 1. Obtener Info del Ticket + Cliente
$sql = "SELECT t.*, c.nombre as nombre_cliente, c.apellido_paterno, c.email, c.telefono, c.empresa, 
               tl.talla, tl.descripcion as desc_talla
        FROM tickets t
        JOIN clientes c ON t.id_cliente = c.id_cliente
        JOIN cat_tallas tl ON t.id_talla = tl.id_talla
        WHERE t.id_ticket = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_ticket]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) die("Ticket no encontrado");

// 2. Obtener Chat
$sql_chat = "SELECT c.*, a.Nombre as nombre_admin 
             FROM comentarios c
             LEFT JOIN Administrador a ON c.id_administrador = a.id_administrador
             WHERE c.id_ticket = :id
             ORDER BY c.fecha_creacion ASC";
$stmt_chat = $pdo->prepare($sql_chat);
$stmt_chat->execute([':id' => $id_ticket]);
$mensajes = $stmt_chat->fetchAll(PDO::FETCH_ASSOC);

$page_css = 'detalle.css'; // Reusamos CSS
require_once '../../includes/header.php';
?>

<style>
    .admin-control-panel {
        background: #f8f9fa;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }
    .control-label { font-size: 0.85rem; font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
    .control-select { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ccc; }
    .client-info-box { background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
</style>

<section class="container detail-wrapper">
    
    <div class="info-panel" style="border-top-color: var(--status-progress);">
        <a href="/views/admin/lista_tickets.php" style="text-decoration: none; color: #999;">← Volver al Tablero</a>
        
        <div style="margin-top: 15px;">
            <span class="badge-l">#<?php echo str_pad($ticket['id_ticket'], 4, '0', STR_PAD_LEFT); ?></span>
            <h2 style="margin-top: 10px;"><?php echo htmlspecialchars($ticket['titulo']); ?></h2>
        </div>

        <div class="client-info-box">
            <strong>👤 Datos del Cliente:</strong><br>
            <?php echo htmlspecialchars($ticket['nombre_cliente'] . ' ' . $ticket['apellido_paterno']); ?><br>
            🏢 <?php echo htmlspecialchars($ticket['empresa']); ?><br>
            📧 <?php echo htmlspecialchars($ticket['email']); ?><br>
            📞 <?php echo htmlspecialchars($ticket['telefono']); ?>
        </div>

        <div class="admin-control-panel">
            <h4 style="margin-bottom: 15px; color: var(--color-shadow);">🛠️ Acciones de Soporte</h4>
            
            <form action="/controllers/admin/update_ticket.php" method="POST">
                <input type="hidden" name="id_ticket" value="<?php echo $id_ticket; ?>">
                
                <label class="control-label">Estado del Ticket</label>
                <select name="id_estado" class="control-select">
                    <option value="1" <?php if($ticket['id_estado']==1) echo 'selected'; ?>>🔴 Abierto</option>
                    <option value="2" <?php if($ticket['id_estado']==2) echo 'selected'; ?>>🟡 En Progreso</option>
                    <option value="3" <?php if($ticket['id_estado']==3) echo 'selected'; ?>>🟢 Resuelto</option>
                </select>

                <label class="control-label">Prioridad</label>
                <select name="id_prioridad" class="control-select">
                    <option value="1" <?php if($ticket['id_prioridad']==1) echo 'selected'; ?>>Baja</option>
                    <option value="2" <?php if($ticket['id_prioridad']==2) echo 'selected'; ?>>Media</option>
                    <option value="3" <?php if($ticket['id_prioridad']==3) echo 'selected'; ?>>Alta</option>
                </select>
                
                <label class="control-label" style="margin-top: 15px;">📝 Nota Rápida para el Cliente</label>
                <textarea name="notas_admin" class="control-select" rows="3" placeholder="Ej: Se requiere acceso remoto..."><?php echo htmlspecialchars($ticket['notas_admin']); ?></textarea>

                <button type="submit" class="btn-accent" style="width: 100%;">💾 Actualizar Ticket</button>
            </form>
        </div>

        <div style="margin-top: 20px;">
            <h4>Descripción del Problema:</h4>
            <p style="background: #fff; padding: 10px; border: 1px solid #eee; border-radius: 4px; color: #555;">
                <?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?>
            </p>
            <div style="margin-top: 10px; font-size: 0.85rem; color: #888;">
                Talla: <strong><?php echo $ticket['talla']; ?></strong> (<?php echo $ticket['desc_talla']; ?>)
            </div>
        </div>
    </div>

    <div class="chat-panel">
        <div class="chat-header" style="background: #2c3e50;"> <div class="avatar-support" style="color: #2c3e50;">CLI</div>
            <div>
                <div style="font-weight: bold;">Chat con Cliente</div>
                <div style="font-size: 0.8rem; opacity: 0.8;">Ticket #<?php echo $ticket['id_ticket']; ?></div>
            </div>
        </div>

        <div class="chat-messages" id="chatBox">
            <?php foreach($mensajes as $msg): ?>
                <?php 
                    // LÓGICA INVERSA:
                    // Si tiene id_administrador, soy YO (me).
                    // Si tiene id_cliente, es ÉL (support/cliente).
                    $tipo = ($msg['id_administrador']) ? 'me' : 'support';
                    
                    // Ajuste visual: los mensajes del cliente (support) se verán blancos
                    // Los míos (me) se verán azules.
                ?>
                <div class="message <?php echo $tipo; ?>">
                    <?php if($tipo == 'me'): ?>
                        <strong style="font-size: 0.7rem; display:block; color: #eee;">Tú (Soporte)</strong>
                    <?php endif; ?>
                    
                    <?php echo nl2br(htmlspecialchars($msg['comentario'])); ?>
                    <span class="msg-time"><?php echo date('H:i', strtotime($msg['fecha_creacion'])); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <form action="/controllers/guardar_comentario.php" method="POST" class="chat-input-area">
            <input type="hidden" name="id_ticket" value="<?php echo $id_ticket; ?>">
            <input type="hidden" name="origen" value="admin">
            
            <input type="text" name="comentario" class="chat-input" placeholder="Escribe una respuesta oficial..." required autocomplete="off">
            <button type="submit" class="btn-send" style="background: #2c3e50;">➤</button>
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