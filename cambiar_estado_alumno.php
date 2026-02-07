<?php
session_start();
include 'config.php';

if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id = intval($_GET['id']);
    $estado = limpiar_dato($conn, $_GET['estado']);
    
    // Validar que el estado sea válido
    if ($estado != 'activo' && $estado != 'inactivo') {
        $_SESSION['mensaje'] = "Estado no válido.";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: alumnos_registrados.php");
        exit();
    }
    
    // Actualizar estado del alumno
    $sql = "UPDATE alumnos SET estado = '$estado' WHERE id = $id";
    
    if ($conn->query($sql)) {
        $accion = $estado == 'activo' ? 'activado' : 'inactivado';
        $_SESSION['mensaje'] = "Alumno $accion exitosamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        $_SESSION['mensaje'] = "Error al cambiar el estado: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "error";
    }
}

header("Location: alumnos_registrados.php");
exit();
?>