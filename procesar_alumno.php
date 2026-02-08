<?php
session_start();

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Recolectar datos del formulario
    $datos_alumno = [
        'nombre'     => $_POST['nombre'],
        'apellido_p' => $_POST['apellido_p'],
        'apellido_m' => $_POST['apellido_m'],
        'id_grupo'   => intval($_POST['grupo_id'])
    ];

    // 2. Enviar a la API Node.js (Railway)
    $resultado = pedir_api('alumnos', 'POST', $datos_alumno);

    // 3. Procesar respuesta
    if (isset($resultado['success']) && $resultado['success'] === true) {
        $_SESSION['mensaje'] = "Alumno registrado correctamente.";
        $_SESSION['tipo_mensaje'] = "exito";
        header("Location: alumnos_registrados.php");
    } else {
        $error = $resultado['error'] ?? 'Error de conexión con la API';
        $_SESSION['mensaje'] = "Error al registrar: $error";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: registrar_alumno.php");
    }

    exit();

} else {
    header("Location: registrar_alumno.php");
    exit();
}
