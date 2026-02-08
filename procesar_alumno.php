<?php
session_start();

// Validar que vengan datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Recolectar datos del formulario HTML
    $datos_alumno = [
        'nombre' => $_POST['nombre'],
        'apellido_p' => $_POST['apellido_p'],
        'apellido_m' => $_POST['apellido_m'],
        'id_grupo' => $_POST['grupo_id']
    ];

    // 2. Preparar el envío a la API (Node.js)
    $url = 'http://localhost:3000/api/alumnos';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos_alumno));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    // 3. Ejecutar envío
    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // 4. Verificar resultado
    $resultado = json_decode($respuesta, true);

    if ($http_code == 200 && isset($resultado['success']) && $resultado['success']) {
        $_SESSION['mensaje'] = "Alumno registrado correctamente.";
        $_SESSION['tipo_mensaje'] = "exito";
        header("Location: alumnos_registrados.php"); // Te manda a la lista
    } else {
        $_SESSION['mensaje'] = "Error al registrar: " . ($resultado['error'] ?? 'Error desconocido');
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: registrar_alumno.php"); // Te regresa al formulario
    }
    exit();

} else {
    // Si intentan entrar directo sin enviar formulario
    header("Location: registrar_alumno.php");
    exit();
}
?>