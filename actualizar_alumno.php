<?php
session_start();

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datos_editar = [
        'id_alumno'  => intval($_POST['id']),
        'nombre'     => $_POST['nombre'],
        'apellido_p' => $_POST['apellido_p'],
        'apellido_m' => $_POST['apellido_m'],
        'id_grupo'   => intval($_POST['grupo_id'])
    ];

    // USAMOS EL PUENTE CENTRAL
    $resultado = pedir_api('alumnos/actualizar', 'POST', $datos_editar);

    if (isset($resultado['success']) && $resultado['success'] === true) {
        $_SESSION['mensaje'] = "Datos actualizados correctamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        $error = $resultado['error'] ?? 'Error de conexión con la API';
        $_SESSION['mensaje'] = "Error al actualizar: $error";
        $_SESSION['tipo_mensaje'] = "error";
    }

    header("Location: alumnos_registrados.php");
    exit;
}
