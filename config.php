<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_escolar');

// Conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8");

// Función para limpiar datos de entrada
function limpiar_dato($conn, $dato) {
    return htmlspecialchars(mysqli_real_escape_string($conn, trim($dato)));
}
?> 