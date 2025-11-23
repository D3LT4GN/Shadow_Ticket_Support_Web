<?php
// includes/header.php
if(session_status()=== PHP_SESSION_NONE){
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shadow Tickets Support</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Della+Respira&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/header.css">

    <?php 
    if (isset($page_css)) {
        echo '<link rel="stylesheet" href="/assets/css/' . $page_css . '">';
    }
    ?>
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <div class="logo">
                <a href="/index.php">
                    <h1>Shadow Tickets Support</h1>
                </a>
            </div>
            
            <nav class="main-nav">
                <ul class="nav-list">
                    <li><a href="/index.php">Inicio</a></li>
                    
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                        
                        <li><a href="/views/admin/dashboard.php">Dashboard</a></li>
                        <li>
                            <a href="/views/admin/dashboard.php" class="btn-accent">Admin Panel 🛠️</a>
                        </li>

                    <?php else: ?>
                        
                        <li><a href="#">Nosotros</a></li>
                        
                        <li class="dropdown-item">
                            <a href="#" class="dropdown-trigger">Tickets ▾</a>
                            <ul class="dropdown-menu">
                                <li><a href="/views/client/crear_ticket.php">Levantar Ticket</a></li>
                                <li><a href="/views/client/mis_tickets.php">Historial de Tickets</a></li>
                            </ul>
                        </li>
                        
                        <li><a href="#">Suscripción</a></li>
                        
                        <li>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                 <a href="/views/client/perfil.php" class="btn-accent">Mi Perfil</a>
                            <?php else: ?>
                                 <a href="/views/auth/login.php" class="btn-accent">Usuario</a>
                            <?php endif; ?>
                        </li>

                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-container"></main>