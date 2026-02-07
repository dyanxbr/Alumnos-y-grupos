<?php
include 'header.php';
?>

    <div class="inicio-container">
        <h2>Bienvenido al Sistema de Gestión Escolar</h2>
        <p>Administra grupos y alumnos de manera eficiente con esta herramienta. Selecciona una opción del menú o de las tarjetas a continuación.</p>
        
        <div class="menu-cards">
            <a href="registrar_grupo.php" class="card">
                <i class="fas fa-users"></i>
                <h3>Registrar Grupo</h3>
                <p>Registra nuevos grupos combinando carrera, turno y grado.</p>
            </a>
            
            <a href="registrar_alumno.php" class="card">
                <i class="fas fa-user-plus"></i>
                <h3>Registrar Alumno</h3>
                <p>Agrega nuevos alumnos asignándolos a un grupo existente.</p>
            </a>
            
            <a href="alumnos_registrados.php" class="card">
                <i class="fas fa-list"></i>
                <h3>Alumnos Registrados</h3>
                <p>Visualiza, modifica y cambia el estado de los alumnos registrados.</p>
            </a>
        </div>
    </div>

<?php
include 'footer.php';
?>