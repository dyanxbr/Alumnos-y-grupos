<?php
include 'header.php';
include 'config.php';

// Obtener datos para los select
$carreras = $conn->query("SELECT * FROM carreras ORDER BY nombre");
$turnos = $conn->query("SELECT * FROM turnos ORDER BY nombre");
$grados = $conn->query("SELECT * FROM grados ORDER BY nombre");
?>

    <div class="form-container">
        <h2><i class="fas fa-users"></i> Registrar Nuevo Grupo</h2>
        
        <form action="procesar_grupo.php" method="POST">
            <div class="form-group">
                <label for="carrera_id"><i class="fas fa-graduation-cap"></i> Carrera:</label>
                <select name="carrera_id" id="carrera_id" required>
                    <option value="">Selecciona una carrera</option>
                    <?php while($carrera = $carreras->fetch_assoc()): ?>
                        <option value="<?php echo $carrera['id']; ?>"><?php echo $carrera['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="turno_id"><i class="fas fa-clock"></i> Turno:</label>
                <select name="turno_id" id="turno_id" required>
                    <option value="">Selecciona un turno</option>
                    <?php while($turno = $turnos->fetch_assoc()): ?>
                        <option value="<?php echo $turno['id']; ?>"><?php echo $turno['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="grado_id"><i class="fas fa-layer-group"></i> Grado:</label>
                <select name="grado_id" id="grado_id" required>
                    <option value="">Selecciona un grado</option>
                    <?php while($grado = $grados->fetch_assoc()): ?>
                        <option value="<?php echo $grado['id']; ?>"><?php echo $grado['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Crear Grupo Automáticamente
            </button>
        </form>
    </div>

<?php
include 'footer.php';
?>