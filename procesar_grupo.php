<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $datos_grupo = [
        'id_carrera' => $_POST['id_carrera'],
        'id_grado' => $_POST['id_grado'],
        'id_turno' => $_POST['id_turno']
    ];

    $url = 'http://localhost:3000/api/grupos';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos_grupo));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $respuesta = curl_exec($ch);
    $resultado = json_decode($respuesta, true);
    curl_close($ch);

    if (isset($resultado['success']) && $resultado['success']) {
        $_SESSION['mensaje'] = "Grupo creado con código: " . $resultado['codigo'];
        $_SESSION['tipo_mensaje'] = "exito";
        header("Location: index.php");
    } else {
        $_SESSION['mensaje'] = "Error: " . ($resultado['error'] ?? 'No se pudo crear');
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: registrar_grupo.php");
    }
    exit();
}
?>