<?php
// views/client/mis_tickets.php
$page_css = 'mis_tickets.css';
require_once '../../includes/header.php';
require_once '../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit;
}

$id_cliente = $_SESSION['user_id'];

// Mensajes flash (Éxito al crear ticket)
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
    unset($_SESSION['msg_type']);
}

// Consulta SQL para traer tickets del usuario [cite: 44, 46]
// Hacemos JOIN para mostrar nombres en vez de IDs
try {
    $sql = "SELECT t.id_ticket, t.titulo, t.fecha_creacion, 
                   e.nombre_estado, 
                   p.nombre_prioridad 
            FROM tickets t
            JOIN cat_estados e ON t.id_estado = e.id_estado
            JOIN cat_prioridades p ON t.id_prioridad = p.id_prioridad
            WHERE t.id_cliente = :id
            ORDER BY t.fecha_creacion DESC"; // Los más nuevos primero

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_cliente);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = "Error al cargar tickets: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="/assets/css/mis_tickets.css">
<link rel="stylesheet" href="/assets/css/global.css">
<section class="container history-wrapper">
    
    <div class="history-header">
        <h2>Historial de Tickets</h2>
        <a href="/views/client/crear_ticket.php" class="btn-accent">Levantar Nuevo +</a>
    </div>

    <?php if ($msg): ?>
        <div style="background-color: var(--status-solved); color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold;">
            ✅ <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <?php if (count($tickets) > 0): ?>
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th># ID</th>
                        <th>Asunto</th>
                        <th>Prioridad</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?php echo $ticket['id_ticket']; ?></td>
                            <td><?php echo htmlspecialchars($ticket['titulo']); ?></td>
                            
                            <td><?php echo $ticket['nombre_prioridad']; ?></td>
                            
                            <td><?php echo date('d/m/Y', strtotime($ticket['fecha_creacion'])); ?></td>
                            
                            <td>
                                <?php 
                                    $clase_estado = 'status-abierto'; // Default rojo
                                    if ($ticket['nombre_estado'] == 'En Progreso') $clase_estado = 'status-proceso';
                                    if ($ticket['nombre_estado'] == 'Resuelto') $clase_estado = 'status-resuelto';
                                ?>
                                <span class="status-badge <?php echo $clase_estado; ?>">
                                    <?php echo $ticket['nombre_estado']; ?>
                                </span>
                            </td>
                            
                            <td>
                                <a href="/views/client/detalle_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="action-link"" class="action-link">Ver Detalle</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>Aún no has levantado ningún ticket.</p>
                <p style="margin-top: 10px;">¡Todo parece estar funcionando bien! 🎉</p>
            </div>
        <?php endif; ?>
    </div>

</section>

<?php require_once '../../includes/footer.php'; ?>