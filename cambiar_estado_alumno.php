<?php
session_start();

// Verificamos que venga el ID. El parámetro 'estado' ya no es estrictamente necesario 
// para la lógica porque la API hace un "toggle" (cambio automático), 
// pero lo podemos dejar si quieres validar la intención.
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 1. Configurar la URL de la API
    $url_api = "http://localhost:3000/api/alumnos/" . $id . "/estatus";
    
    // 2. Inicializar cURL
    $ch = curl_init($url_api);
    
    // Configuración para método PATCH
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH"); // Importante: Método PATCH
    
    // 3. Ejecutar la petición
    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // 4. Procesar respuesta
    $datos = json_decode($respuesta, true);
    
    if ($http_code == 200 && isset($datos['success']) && $datos['success'] == true) {
        // La API nos devuelve 'nuevoEstatus' (1 o 0). Usamos eso para el mensaje.
        // 1 = Activo, 0 = Inactivo
        $accion = ($datos['nuevoEstatus'] == 1) ? 'activado' : 'inactivado';
        
        $_SESSION['mensaje'] = "Alumno $accion exitosamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        // Manejo de errores
        $error_msg = isset($datos['error']) ? $datos['error'] : "Error desconocido al conectar con la API.";
        $_SESSION['mensaje'] = "Error al cambiar el estado: " . $error_msg;
        $_SESSION['tipo_mensaje'] = "error";
    }
} else {
    $_SESSION['mensaje'] = "No se proporcionó un ID de alumno.";
    $_SESSION['tipo_mensaje'] = "error";
}

// 5. Redireccionar de vuelta a la lista
header("Location: alumnos_registrados.php");
exit();
?>