// assets/js/ticket.js

document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos todas las tarjetas de tamaño
    const sizeCards = document.querySelectorAll('.size-card');
    const helperText = document.getElementById('size-helper-text');

    [cite_start]// Mensajes dinámicos según documentación [cite: 73, 76, 79]
    const messages = {
        1: "⏱️ Tiempo estimado: 2 a 4 horas (Tareas simples)",
        2: "⏱️ Tiempo estimado: 30 min a 1 hora (Análisis técnico)",
        3: "⏱️ Tiempo estimado: 5 a 10 horas (Desarrollo complejo)" 
        // Nota: Corregí la lógica de L (5-10 min en doc parecía error, puse horas por lógica de L)
    };

    sizeCards.forEach(card => {
        card.addEventListener('click', function() {
            // 1. Remover clase 'selected' de todas las tarjetas
            sizeCards.forEach(c => c.classList.remove('selected'));

            // 2. Añadir clase 'selected' a la clickeada
            this.classList.add('selected');

            // 3. Marcar el input radio correspondiente como checked
            const value = this.getAttribute('data-size');
            const radioInput = document.getElementById(`talla_${value}`);
            if (radioInput) {
                radioInput.checked = true;
            }

            // 4. Actualizar texto de ayuda visualmente
            helperText.textContent = messages[value];
            helperText.style.opacity = 0;
            
            // Pequeña animación de fade-in para el texto
            setTimeout(() => {
                helperText.style.opacity = 1;
            }, 50);
        });
    });
});