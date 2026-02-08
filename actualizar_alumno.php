<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Recolectar datos del Modal
    // OJO: En el formulario el input se llama 'id', pero la API espera 'id_alumno'
    $datos_editar = [
        'id_alumno' => $_POST['id'], 
        'nombre' => $_POST['nombre'],
        'apellido_p' => $_POST['apellido_p'],
        'apellido_m' => $_POST['apellido_m'],
        'id_grupo' => $_POST['grupo_id']
    ];

    // 2. Enviar a la ruta de actualización de la API
    $url = 'http://localhost:3000/api/alumnos/actualizar';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // Usamos POST porque así lo definimos en index.js
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos_editar));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $resultado = json_decode($respuesta, true);

    if ($http_code == 200 && isset($resultado['success']) && $resultado['success']) {
        $_SESSION['mensaje'] = "Datos actualizados correctamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar: " . ($resultado['error'] ?? 'Error de conexión');
        $_SESSION['tipo_mensaje'] = "error";
    }
    
    // Regresamos a la lista
    header("Location: alumnos_registrados.php");
    exit();
}
?>