<?php
session_start();

// 🔗 URL BASE DE LA API (Railway)
define('API_URL', 'https://api-alumnos-production-cdcc.up.railway.app/api');

// Verificamos que venga el ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 1. Construir URL de la API correctamente
    $url_api = API_URL . "/alumnos/" . $id . "/estatus";
    
    // 2. Inicializar cURL
    $ch = curl_init($url_api);
    
    // Método PATCH
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    
    // 3. Ejecutar petición
    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // 4. Procesar respuesta
    $datos = json_decode($respuesta, true);
    
    if ($http_code === 200 && isset($datos['success']) && $datos['success'] === true) {
        $accion = (isset($datos['nuevoEstatus']) && $datos['nuevoEstatus'] == 1)
            ? 'activado'
            : 'inactivado';
        
        $_SESSION['mensaje'] = "Alumno $accion exitosamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        $error_msg = $datos['error'] ?? 'Error desconocido al conectar con la API.';
        $_SESSION['mensaje'] = "Error al cambiar el estado: $error_msg";
        $_SESSION['tipo_mensaje'] = "error";
    }
} else {
    $_SESSION['mensaje'] = "No se proporcionó un ID de alumno.";
    $_SESSION['tipo_mensaje'] = "error";
}

// 5. Redireccionar
header("Location: alumnos_registrados.php");
exit;
