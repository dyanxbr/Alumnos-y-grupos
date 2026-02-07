<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $nombre = limpiar_dato($conn, $_POST['nombre']);
    $apellidos = limpiar_dato($conn, $_POST['apellidos']);
    $grupo_id = limpiar_dato($conn, $_POST['grupo_id']);
    
    // Actualizar datos del alumno
    $sql = "UPDATE alumnos SET nombre = '$nombre', apellidos = '$apellidos', grupo_id = $grupo_id WHERE id = $id";
    
    if ($conn->query($sql)) {
        $_SESSION['mensaje'] = "Datos del alumno actualizados exitosamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar los datos: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "error";
    }
    
    header("Location: alumnos_registrados.php");
    exit();
}
?>