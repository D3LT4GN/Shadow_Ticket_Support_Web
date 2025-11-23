<?php
// index.php

// 1. Definimos el CSS específico
// NOTA: Asegúrate de que el archivo en /assets/css/ se llame igual que esta variable.
// Si creamos 'home.css' antes, cámbialo aquí a 'home.css' o renombra tu archivo a 'index.css'.
$page_css = 'index.css'; 

// 3. Cargar el Header (Este archivo ya abre el HTML y carga los estilos)
require_once 'includes/header.php'; 
?>
<link rel="stylesheet" href="/assets/css/index.css">
<link rel="stylesheet" href="/assets/css/global.css">
<section class="container">
    <div class="hero">
        <h2>Bienvenido a Shadow Tickets Support</h2>
        
        <p class="hero-subtitle">
            Transformando el caos en un flujo de trabajo ordenado mediante metodología T-shirt Sizing.
        </p>

        <div class="demo-badges">
            <span class="badge-s">S - Pequeña</span>
            <span class="badge-m">M - Mediana</span>
            <span class="badge-l">L - Grande</span>
        </div>

        <div style="margin-top: 2rem; font-weight: bold;">
            <?php if(isset($pdo)): ?>
                <span style="color: var(--status-solved);">✅ Sistema Operativo y Conectado a BD</span>
            <?php elseif(isset($error_db)): ?>
                <span style="color: var(--status-open);">⚠️ Sin conexión: <?php echo $error_db; ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// 4. Cargar el Footer (Este archivo cierra el body y el html)
require_once 'includes/footer.php';
?>