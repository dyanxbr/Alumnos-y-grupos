<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = limpiar_dato($conn, $_POST['nombre']);
    $apellidos = limpiar_dato($conn, $_POST['apellidos']);
    $grupo_id = limpiar_dato($conn, $_POST['grupo_id']);
    
    // Insertar el nuevo alumno
    $sql = "INSERT INTO alumnos (nombre, apellidos, grupo_id, estado) 
            VALUES ('$nombre', '$apellidos', $grupo_id, 'activo')";
    
    if ($conn->query($sql)) {
        $_SESSION['mensaje'] = "Alumno '$nombre $apellidos' registrado exitosamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        $_SESSION['mensaje'] = "Error al registrar el alumno: " . $conn->error;
        $_SESSION['tipo_mensaje'] = "error";
    }
    
    header("Location: registrar_alumno.php");
    exit();
}
?>