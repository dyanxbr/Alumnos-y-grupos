</main>
    
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p class="mb-1">Sistema de Gestión Escolar &copy; <?php echo date('Y'); ?> | Todos los derechos reservados</p>
            <p class="mb-0 small text-white-50">Desarrollado con PHP (Frontend) y Node.js (API)</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Esperar 5 segundos y cerrar las alertas
        setTimeout(function() {
            // Buscamos elementos con la clase '.alert' de Bootstrap
            var alertas = document.querySelectorAll('.alert');
            
            alertas.forEach(function(alerta) {
                // Opción 1: Desvanecer con CSS puro
                alerta.style.transition = "opacity 0.5s ease";
                alerta.style.opacity = "0";
                
                // Opción 2: Usar la instancia de Bootstrap (si prefieres usar JS de Bootstrap)
                // var bsAlert = new bootstrap.Alert(alerta);
                // bsAlert.close();

                // Eliminar del DOM después de la transición
                setTimeout(function() {
                    alerta.remove();
                }, 500); // Espera a que termine la transición de opacidad
            });
        }, 5000); // 5000 milisegundos = 5 segundos
    </script>
</body>
</html>