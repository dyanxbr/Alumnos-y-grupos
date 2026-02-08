<?php
session_start();

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Datos del formulario
    $datos_grupo = [
        'id_carrera' => intval($_POST['id_carrera']),
        'id_grado'   => intval($_POST['id_grado']),
        'id_turno'   => intval($_POST['id_turno'])
    ];

    // 2. Enviar a la API Node.js
    $resultado = pedir_api('grupos', 'POST', $datos_grupo);

    // 3. Procesar respuesta
    if (isset($resultado['success']) && $resultado['success'] === true) {
        $_SESSION['mensaje'] = "Grupo creado con código: {$resultado['codigo']}";
        $_SESSION['tipo_mensaje'] = "exito";
        header("Location: index.php");
    } else {
        $error = $resultado['error'] ?? 'No se pudo crear el grupo';
        $_SESSION['mensaje'] = "Error: $error";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: registrar_grupo.php");
    }

    exit();

} else {
    header("Location: registrar_grupo.php");
    exit();
}
