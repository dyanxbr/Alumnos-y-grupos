<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $carrera_id = limpiar_dato($conn, $_POST['carrera_id']);
    $turno_id = limpiar_dato($conn, $_POST['turno_id']);
    $grado_id = limpiar_dato($conn, $_POST['grado_id']);
    
    // Obtener nombres para crear el nombre del grupo
    $carrera = $conn->query("SELECT nombre FROM carreras WHERE id = $carrera_id")->fetch_assoc();
    $turno = $conn->query("SELECT nombre FROM turnos WHERE id = $turno_id")->fetch_assoc();
    $grado = $conn->query("SELECT nombre FROM grados WHERE id = $grado_id")->fetch_assoc();
    
    // Generar nombre del grupo (ejemplo: ING-1A)
    $iniciales_carrera = substr($carrera['nombre'], 0, 2);
    $iniciales_turno = substr($turno['nombre'], 0, 1);
    $numero_grado = preg_replace('/[^0-9]/', '', $grado['nombre']);
    
    // Verificar si ya existe un grupo con la misma combinación
    $verificar = $conn->query("SELECT * FROM grupos WHERE carrera_id = $carrera_id AND turno_id = $turno_id AND grado_id = $grado_id");
    
    if ($verificar->num_rows > 0) {
        $_SESSION['mensaje'] = "Ya existe un grupo con esta combinación.";
        $_SESSION['tipo_mensaje'] = "error";
    } else {
        // Generar letra del grupo (A, B, C, etc.)
        $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
        $letra_grupo = $letras[0]; // Por defecto A
        
        // Contar cuántos grupos hay con el mismo grado y carrera para asignar letra
        $contar_grupos = $conn->query("SELECT COUNT(*) as total FROM grupos WHERE carrera_id = $carrera_id AND grado_id = $grado_id");
        $resultado = $contar_grupos->fetch_assoc();
        if ($resultado['total'] < count($letras)) {
            $letra_grupo = $letras[$resultado['total']];
        }
        
        $nombre_grupo = strtoupper($iniciales_carrera) . "-" . $numero_grado . $letra_grupo;
        
        // Insertar el nuevo grupo
        $sql = "INSERT INTO grupos (carrera_id, turno_id, grado_id, nombre_grupo) 
                VALUES ($carrera_id, $turno_id, $grado_id, '$nombre_grupo')";
        
        if ($conn->query($sql)) {
            $_SESSION['mensaje'] = "Grupo '$nombre_grupo' creado exitosamente.";
            $_SESSION['tipo_mensaje'] = "exito";
        } else {
            $_SESSION['mensaje'] = "Error al crear el grupo: " . $conn->error;
            $_SESSION['tipo_mensaje'] = "error";
        }
    }
    
    header("Location: registrar_grupo.php");
    exit();
}
?>