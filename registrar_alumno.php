<?php
include 'header.php';
include 'config.php';

// Obtener grupos para el select
$grupos = $conn->query("
    SELECT g.id, g.nombre_grupo, c.nombre as carrera, t.nombre as turno, gr.nombre as grado 
    FROM grupos g
    JOIN carreras c ON g.carrera_id = c.id
    JOIN turnos t ON g.turno_id = t.id
    JOIN grados gr ON g.grado_id = gr.id
    ORDER BY g.nombre_grupo
");
?>

    <div class="form-container">
        <h2><i class="fas fa-user-plus"></i> Registrar Nuevo Alumno</h2>
        
        <form action="procesar_alumno.php" method="POST">
            <div class="form-group">
                <label for="nombre"><i class="fas fa-id-card"></i> Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="apellidos"><i class="fas fa-id-card"></i> Apellidos:</label>
                <input type="text" name="apellidos" id="apellidos" required>
            </div>
            
            <div class="form-group">
                <label for="grupo_id"><i class="fas fa-users"></i> Grupo:</label>
                <select name="grupo_id" id="grupo_id" required>
                    <option value="">Selecciona un grupo</option>
                    <?php while($grupo = $grupos->fetch_assoc()): ?>
                        <option value="<?php echo $grupo['id']; ?>">
                            <?php echo $grupo['nombre_grupo'] . " - " . $grupo['carrera'] . " (" . $grupo['turno'] . " - " . $grupo['grado'] . ")"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Registrar Alumno
            </button>
        </form>
    </div>

<?php
include 'footer.php';
?>