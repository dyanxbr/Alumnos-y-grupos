    </main>
    
    <footer>
        <div class="container">
            <p>Sistema de Gestión Escolar &copy; <?php echo date('Y'); ?> | Todos los derechos reservados</p>
            <p>Desarrollado con PHP y MySQL</p>
        </div>
    </footer>
    
    <script>
        // Cerrar mensajes automáticamente después de 5 segundos
        setTimeout(function() {
            var mensajes = document.querySelectorAll('.mensaje');
            mensajes.forEach(function(mensaje) {
                mensaje.style.opacity = '0';
                mensaje.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    mensaje.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>