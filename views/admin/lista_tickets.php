<?php
// views/admin/lista_tickets.php
session_start();

// 1. Seguridad: Solo Admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /views/auth/login.php");
    exit;
}

// Reutilizamos el CSS de galería que ya creamos (¡Eficiencia!)
$page_css = 'galeria.css'; 
require_once '../../includes/header.php';
require_once '../../config/db_connect.php';

// --- LÓGICA DE FILTRADO ---
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : ''; // Por defecto mostrar todos
$filtro_prioridad = isset($_GET['prioridad']) ? $_GET['prioridad'] : '';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Consulta SQL Maestra (JOIN con Clientes para saber quién reporta)
$sql = "SELECT t.*, 
               e.nombre_estado, 
               p.nombre_prioridad, 
               tl.talla,
               c.nombre as nombre_cliente, 
               c.apellido_paterno
        FROM tickets t
        JOIN cat_estados e ON t.id_estado = e.id_estado
        JOIN cat_prioridades p ON t.id_prioridad = p.id_prioridad
        JOIN cat_tallas tl ON t.id_talla = tl.id_talla
        JOIN clientes c ON t.id_cliente = c.id_cliente
        WHERE 1=1"; // Truco para concatenar ANDs fácilmente

$params = [];

// Filtros Dinámicos
if (!empty($filtro_estado)) {
    $sql .= " AND t.id_estado = :estado";
    $params[':estado'] = $filtro_estado;
}

if (!empty($filtro_prioridad)) {
    $sql .= " AND t.id_prioridad = :prioridad";
    $params[':prioridad'] = $filtro_prioridad;
}

if (!empty($busqueda)) {
    $sql .= " AND (t.titulo ILIKE :busqueda OR c.nombre ILIKE :busqueda OR c.apellido_paterno ILIKE :busqueda)";
    $params[':busqueda'] = "%$busqueda%";
}

$sql .= " ORDER BY t.fecha_creacion DESC"; // Lo más urgente/nuevo arriba

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="/assets/css/galeria.css">
<link rel="stylesheet" href="/assets/css/global.css">

<section class="container" style="padding: 40px 0;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-family: var(--font-display); color: var(--color-shadow);">Centro de Comando</h2>
            <p style="color: #666;">Gestionando tickets globales</p>
        </div>
        <div class="toast-notification show" style="position: static; transform: none; box-shadow: none; border: 1px solid #ddd; padding: 10px;">
            📊 Total Tickets: <strong><?php echo count($tickets); ?></strong>
        </div>
    </div>

    <div class="filter-bar" style="border-left-color: var(--status-progress);"> <form action="" method="GET" class="filter-form">
            
            <div class="filter-group" style="flex: 2;">
                <label class="filter-label">Búsqueda Global</label>
                <input type="text" name="busqueda" class="filter-input" placeholder="Título o Nombre del Cliente..." value="<?php echo htmlspecialchars($busqueda); ?>">
            </div>

            <div class="filter-group">
                <label class="filter-label">Estado</label>
                <select name="estado" class="filter-select">
                    <option value="">Todos</option>
                    <option value="1" <?php if($filtro_estado==1) echo 'selected'; ?>>🔴 Abiertos</option>
                    <option value="2" <?php if($filtro_estado==2) echo 'selected'; ?>>🟡 En Progreso</option>
                    <option value="3" <?php if($filtro_estado==3) echo 'selected'; ?>>🟢 Resueltos</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Prioridad</label>
                <select name="prioridad" class="filter-select">
                    <option value="">Todas</option>
                    <option value="3" <?php if($filtro_prioridad==3) echo 'selected'; ?>>🔥 Alta</option>
                    <option value="2" <?php if($filtro_prioridad==2) echo 'selected'; ?>>⚡ Media</option>
                    <option value="1" <?php if($filtro_prioridad==1) echo 'selected'; ?>>🧊 Baja</option>
                </select>
            </div>

            <button type="submit" class="btn-filter">Filtrar</button>
            
            <?php if(!empty($_GET)): ?>
                <a href="/views/admin/lista_tickets.php" style="margin-left: 10px; color: var(--status-open);">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (count($tickets) > 0): ?>
        <div class="tickets-grid">
            <?php foreach ($tickets as $ticket): ?>
                
                <div class="ticket-card priority-<?php echo $ticket['id_prioridad']; ?>">
                    
                    <div class="card-header">
                        <span class="ticket-id">#<?php echo str_pad($ticket['id_ticket'], 4, '0', STR_PAD_LEFT); ?></span>
                        
                        <?php 
                            $bg_estado = 'var(--status-open)';
                            $txt_estado = 'Abierto';
                            if($ticket['id_estado'] == 2) { $bg_estado = 'var(--status-progress)'; $txt_estado = 'En Progreso'; }
                            if($ticket['id_estado'] == 3) { $bg_estado = 'var(--status-solved)'; $txt_estado = 'Resuelto'; }
                        ?>
                        <span class="priority-badge" style="background: <?php echo $bg_estado; ?>; color: white;">
                            <?php echo $txt_estado; ?>
                        </span>
                    </div>

                    <div style="font-size: 0.8rem; color: #888; margin-bottom: 5px;">
                        👤 <?php echo htmlspecialchars($ticket['nombre_cliente'] . ' ' . $ticket['apellido_paterno']); ?>
                    </div>

                    <h3 class="card-title">
                        <?php echo htmlspecialchars($ticket['titulo']); ?>
                    </h3>

                    <div class="card-meta">
                        <div class="meta-item">
                            <span class="talla-badge talla-<?php echo $ticket['talla']; ?>">
                                <?php echo $ticket['talla']; ?>
                            </span>
                            <span style="font-size: 0.8rem; margin-left: 5px;"><?php echo $ticket['nombre_prioridad']; ?></span>
                        </div>
                        <div class="meta-item">
                            📅 <?php echo date('d/m H:i', strtotime($ticket['fecha_creacion'])); ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="/controllers/admin/gestionar_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn-card" style="color: var(--color-shadow);">
                            🛠️ Gestionar Ticket →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state" style="background: white; padding: 50px; border-radius: 8px; text-align: center;">
            <h3>¡Todo tranquilo! 🌴</h3>
            <p>No hay tickets que coincidan con los filtros.</p>
        </div>
    <?php endif; ?>

</section>

<?php require_once '../../includes/footer.php'; ?>