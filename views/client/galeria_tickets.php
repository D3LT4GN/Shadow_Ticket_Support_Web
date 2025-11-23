<?php
// views/client/galeria_tickets.php
$page_css = 'galeria.css'; // Cargamos el estilo nuevo
require_once '../../includes/header.php';
require_once '../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /views/auth/login.php");
    exit;
}

$id_cliente = $_SESSION['user_id'];

// --- LÓGICA DE FILTRADO (PHP PURO) ---

// 1. Capturamos los valores del filtro (si existen)
$filtro_texto = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$filtro_prioridad = isset($_GET['prioridad']) ? $_GET['prioridad'] : '';
$filtro_talla = isset($_GET['talla']) ? $_GET['talla'] : '';
$filtro_orden = isset($_GET['orden']) ? $_GET['orden'] : 'DESC'; // Por defecto lo más nuevo

// 2. Construimos la consulta SQL dinámica
// Base de la consulta
$sql = "SELECT t.*, e.nombre_estado, p.nombre_prioridad, tl.talla 
        FROM tickets t
        JOIN cat_estados e ON t.id_estado = e.id_estado
        JOIN cat_prioridades p ON t.id_prioridad = p.id_prioridad
        JOIN cat_tallas tl ON t.id_talla = tl.id_talla
        WHERE t.id_cliente = :id_cliente";

// Agregamos condiciones según los filtros
$params = [':id_cliente' => $id_cliente];

if (!empty($filtro_texto)) {
    // Busca por ID (si es número) o por Título (texto)
    if (is_numeric($filtro_texto)) {
        $sql .= " AND t.id_ticket = :texto";
    } else {
        $sql .= " AND t.titulo ILIKE :texto"; // ILIKE es case-insensitive en Postgres
    }
    $params[':texto'] = is_numeric($filtro_texto) ? $filtro_texto : "%$filtro_texto%";
}

if (!empty($filtro_prioridad)) {
    $sql .= " AND t.id_prioridad = :prioridad";
    $params[':prioridad'] = $filtro_prioridad;
}

if (!empty($filtro_talla)) {
    $sql .= " AND t.id_talla = :talla";
    $params[':talla'] = $filtro_talla;
}

// Agregamos Ordenamiento
if ($filtro_orden == 'ASC') {
    $sql .= " ORDER BY t.fecha_creacion ASC"; // Más antiguos primero
} else {
    $sql .= " ORDER BY t.fecha_creacion DESC"; // Más nuevos primero
}

// 3. Ejecutamos la consulta
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al filtrar: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="/assets/css/galeria.css">
<link rel="stylesheet" href="/assets/css/global.css">

<section class="container" style="padding: 40px 0;">
    
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-family: var(--font-display); color: var(--color-shadow);">Galería de Tickets</h2>
        <a href="/views/client/mis_tickets.php" style="font-size: 0.9rem; color: #666;">Ver como lista simple</a>
    </div>

    <div class="filter-bar">
        <form action="" method="GET" class="filter-form">
            
            <div class="filter-group" style="flex: 2;">
                <label class="filter-label">Buscar</label>
                <input type="text" name="busqueda" class="filter-input" placeholder="Título o ID del ticket..." value="<?php echo htmlspecialchars($filtro_texto); ?>">
            </div>

            <div class="filter-group">
                <label class="filter-label">Prioridad</label>
                <select name="prioridad" class="filter-select">
                    <option value="">Todas</option>
                    <option value="1" <?php if($filtro_prioridad==1) echo 'selected'; ?>>Baja</option>
                    <option value="2" <?php if($filtro_prioridad==2) echo 'selected'; ?>>Media</option>
                    <option value="3" <?php if($filtro_prioridad==3) echo 'selected'; ?>>Alta</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Talla (Complejidad)</label>
                <select name="talla" class="filter-select">
                    <option value="">Todas</option>
                    <option value="1" <?php if($filtro_talla==1) echo 'selected'; ?>>S - Pequeña</option>
                    <option value="2" <?php if($filtro_talla==2) echo 'selected'; ?>>M - Mediana</option>
                    <option value="3" <?php if($filtro_talla==3) echo 'selected'; ?>>L - Grande</option>
                </select>
            </div>

            <button type="submit" class="btn-filter">🔍 Filtrar</button>
            
            <?php if(!empty($_GET)): ?>
                <a href="galeria_tickets.php" style="margin-left: 10px; color: var(--status-open); font-size: 0.9rem;">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (count($tickets) > 0): ?>
        <div class="tickets-grid">
            <?php foreach ($tickets as $ticket): ?>
                <div class="ticket-card priority-<?php echo $ticket['id_prioridad']; ?>">
                    
                    <div class="card-header">
                        <span class="ticket-id">#<?php echo str_pad($ticket['id_ticket'], 4, '0', STR_PAD_LEFT); ?></span>
                        <span class="priority-badge"><?php echo $ticket['nombre_prioridad']; ?></span>
                    </div>

                    <h3 class="card-title">
                        <?php echo htmlspecialchars($ticket['titulo']); ?>
                    </h3>

                    <div class="card-meta">
                        <div class="meta-item">
                            <span class="talla-badge talla-<?php echo $ticket['talla']; ?>" title="Complejidad">
                                <?php echo $ticket['talla']; ?>
                            </span>
                        </div>
                        <div class="meta-item">
                            📅 <?php echo date('d/m/Y', strtotime($ticket['fecha_creacion'])); ?>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                         <?php 
                            $color_estado = 'var(--status-open)';
                            if($ticket['nombre_estado'] == 'En Progreso') $color_estado = 'var(--status-progress)';
                            if($ticket['nombre_estado'] == 'Resuelto') $color_estado = 'var(--status-solved)';
                        ?>
                        <span style="color: <?php echo $color_estado; ?>; font-weight: bold; font-size: 0.9rem;">
                            ● <?php echo $ticket['nombre_estado']; ?>
                        </span>
                    </div>

                    <div class="card-footer">
                        <a href="/views/client/detalle_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn-card">Ver Detalle →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; color: #777;">
            <h3>No se encontraron tickets</h3>
            <p>Intenta ajustar los filtros de búsqueda.</p>
        </div>
    <?php endif; ?>

</section>

<?php require_once '../../includes/footer.php'; ?>