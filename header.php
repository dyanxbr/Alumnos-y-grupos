<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Escolar</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><i class="fas fa-school"></i> Sistema de Gestión Escolar</h1>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="registrar_grupo.php"><i class="fas fa-users"></i> Registrar Grupo</a></li>
                    <li><a href="registrar_alumno.php"><i class="fas fa-user-plus"></i> Registrar Alumno</a></li>
                    <li><a href="alumnos_registrados.php"><i class="fas fa-list"></i> Alumnos Registrados</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje <?php echo $_SESSION['tipo_mensaje']; ?>">
                <p><?php echo $_SESSION['mensaje']; ?></p>
                <?php unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']); ?>
            </div>
        <?php endif; ?>