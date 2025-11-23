<?php
// includes/footer.php
?>
<link rel="stylesheet" href="/assets/css/footer.css">
    </main> <?php if (isset($_SESSION['welcome_toast'])): ?>
        <div id="welcomeToast" class="toast-notification">
            <div class="toast-icon">👋</div>
            <div class="toast-body">
                <strong><?php echo $_SESSION['welcome_toast']['title']; ?></strong>
                <span><?php echo $_SESSION['welcome_toast']['type']; ?></span>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('welcomeToast');
                
                // 1. Mostrar (Deslizar hacia adentro)
                setTimeout(() => {
                    toast.classList.add('show');
                }, 500); // Espera medio segundo después de cargar

                // 2. Ocultar (Deslizar hacia afuera) después de 4 segundos
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 4500);
            });
        </script>
        
        <?php 
        // Borramos la variable para que no salga cada vez que recargue la página
        unset($_SESSION['welcome_toast']); 
        ?>
    <?php endif; ?>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Shadow Tickets Support. Transformando el caos en orden.</p>
        </div>
    </footer>

</body>
</html>