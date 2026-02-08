<?php
// Verificamos si la sesión ya está iniciada para evitar errores
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Escolar</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styles.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Ajustes rápidos para que el menú se vea bien con Bootstrap */
        header { background-color: #0d6efd; padding: 15px 0; margin-bottom: 20px; }
        header h1 { color: white; margin: 0; font-size: 1.5rem; }
        nav ul { margin: 0; padding: 0; list-style: none; display: flex; gap: 15px; }
        nav a { color: rgba(255,255,255,0.8); text-decoration: none; font-weight: 500; }
        nav a:hover { color: white; }
        .container { max-width: 1000px; margin: 0 auto; padding: 0 15px; }
        
        /* Encabezado flexbox */
        header .container { display: flex; justify-content: space-between; align-items: center; }
        
        @media (max-width: 768px) {
            header .container { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><i class="fas fa-school"></i> Gestión Escolar</h1>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="registrar_grupo.php"><i class="fas fa-users"></i> Grupos</a></li>
                    <li><a href="registrar_alumno.php"><i class="fas fa-user-plus"></i> Alumnos</a></li>
                    <li><a href="alumnos_registrados.php"><i class="fas fa-list"></i> Lista</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <?php 
                $clase_alerta = ($_SESSION['tipo_mensaje'] == 'exito') ? 'alert-success' : 'alert-danger';
            ?>
            <div class="alert <?php echo $clase_alerta; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['mensaje']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                
                <?php 
                // Limpiamos el mensaje después de mostrarlo
                unset($_SESSION['mensaje']); 
                unset($_SESSION['tipo_mensaje']); 
                ?>
            </div>
        <?php endif; ?>