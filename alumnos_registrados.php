<?php
include 'header.php';
include 'config.php';

// Obtener grupos para el filtro
$grupos = $conn->query("
    SELECT g.id, g.nombre_grupo, c.nombre as carrera 
    FROM grupos g
    JOIN carreras c ON g.carrera_id = c.id
    ORDER BY g.nombre_grupo
");

// Determinar si hay filtro por grupo
$filtro_grupo = isset($_GET['grupo_id']) && $_GET['grupo_id'] != '' ? intval($_GET['grupo_id']) : 0;

// Construir consulta con o sin filtro
$sql = "
    SELECT a.id, a.nombre, a.apellidos, a.estado, a.fecha_registro,
           g.nombre_grupo, c.nombre as carrera, t.nombre as turno, gr.nombre as grado
    FROM alumnos a
    JOIN grupos g ON a.grupo_id = g.id
    JOIN carreras c ON g.carrera_id = c.id
    JOIN turnos t ON g.turno_id = t.id
    JOIN grados gr ON g.grado_id = gr.id
";

if ($filtro_grupo > 0) {
    $sql .= " WHERE a.grupo_id = $filtro_grupo";
}

$sql .= " ORDER BY a.apellidos, a.nombre";
$alumnos = $conn->query($sql);

// Obtener estadísticas
$total_alumnos = $conn->query("SELECT COUNT(*) as total FROM alumnos")->fetch_assoc()['total'];
$alumnos_activos = $conn->query("SELECT COUNT(*) as total FROM alumnos WHERE estado = 'activo'")->fetch_assoc()['total'];
$alumnos_inactivos = $conn->query("SELECT COUNT(*) as total FROM alumnos WHERE estado = 'inactivo'")->fetch_assoc()['total'];
?>

    <div class="tabla-container">
        <h2><i class="fas fa-list"></i> Alumnos Registrados</h2>
        
        <!-- Filtro por grupo -->
        <div class="form-group" style="margin-bottom: 30px;">
            <label for="filtro_grupo"><i class="fas fa-filter"></i> Filtrar por grupo:</label>
            <form method="GET" action="alumnos_registrados.php" style="display: flex; gap: 10px;">
                <select name="grupo_id" id="filtro_grupo" onchange="this.form.submit()">
                    <option value="">Todos los grupos</option>
                    <?php while($grupo = $grupos->fetch_assoc()): ?>
                        <option value="<?php echo $grupo['id']; ?>" <?php echo ($filtro_grupo == $grupo['id']) ? 'selected' : ''; ?>>
                            <?php echo $grupo['nombre_grupo'] . " - " . $grupo['carrera']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn"><i class="fas fa-filter"></i> Filtrar</button>
                <?php if ($filtro_grupo > 0): ?>
                    <a href="alumnos_registrados.php" class="btn btn-danger"><i class="fas fa-times"></i> Quitar filtro</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Estadísticas -->
        <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
            <div style="background-color: #eaffea; padding: 15px; border-radius: 5px; flex: 1; min-width: 200px;">
                <h3 style="color: #27ae60; margin-bottom: 5px;">Total Alumnos</h3>
                <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $total_alumnos; ?></p>
            </div>
            <div style="background-color: #eaffea; padding: 15px; border-radius: 5px; flex: 1; min-width: 200px;">
                <h3 style="color: #27ae60; margin-bottom: 5px;">Alumnos Activos</h3>
                <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $alumnos_activos; ?></p>
            </div>
            <div style="background-color: #ffeaea; padding: 15px; border-radius: 5px; flex: 1; min-width: 200px;">
                <h3 style="color: #e74c3c; margin-bottom: 5px;">Alumnos Inactivos</h3>
                <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $alumnos_inactivos; ?></p>
            </div>
        </div>
        
        <!-- Tabla de alumnos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Grupo</th>
                    <th>Carrera</th>
                    <th>Turno</th>
                    <th>Grado</th>
                    <th>Estado</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($alumnos->num_rows > 0): ?>
                    <?php while($alumno = $alumnos->fetch_assoc()): ?>
                        <tr class="<?php echo $alumno['estado'] == 'activo' ? 'alumno-activo' : 'alumno-inactivo'; ?>">
                            <td><?php echo $alumno['id']; ?></td>
                            <td><?php echo $alumno['nombre']; ?></td>
                            <td><?php echo $alumno['apellidos']; ?></td>
                            <td><?php echo $alumno['nombre_grupo']; ?></td>
                            <td><?php echo $alumno['carrera']; ?></td>
                            <td><?php echo $alumno['turno']; ?></td>
                            <td><?php echo $alumno['grado']; ?></td>
                            <td>
                                <span class="estado <?php echo $alumno['estado']; ?>">
                                    <?php echo $alumno['estado'] == 'activo' ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($alumno['fecha_registro'])); ?></td>
                            <td class="acciones">
                                <a href="#" onclick="cargarDatosAlumno(<?php echo $alumno['id']; ?>)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditar">
                                    <i class="fas fa-edit"></i> Modificar
                                </a>
                                
                                <?php if ($alumno['estado'] == 'activo'): ?>
                                    <a href="cambiar_estado_alumno.php?id=<?php echo $alumno['id']; ?>&estado=inactivo" class="btn btn-sm btn-danger">
                                        <i class="fas fa-user-slash"></i> Inactivar
                                    </a>
                                <?php else: ?>
                                    <a href="cambiar_estado_alumno.php?id=<?php echo $alumno['id']; ?>&estado=activo" class="btn btn-sm btn-success">
                                        <i class="fas fa-user-check"></i> Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 30px;">
                            No hay alumnos registrados <?php echo $filtro_grupo > 0 ? 'en este grupo' : ''; ?>.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Modal para editar alumno -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-edit"></i> Modificar Datos del Alumno</h2>
            <form id="formEditarAlumno" action="actualizar_alumno.php" method="POST">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_nombre">Nombre:</label>
                    <input type="text" name="nombre" id="edit_nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_apellidos">Apellidos:</label>
                    <input type="text" name="apellidos" id="edit_apellidos" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_grupo_id">Grupo:</label>
                    <select name="grupo_id" id="edit_grupo_id" required>
                        <option value="">Selecciona un grupo</option>
                        <?php 
                        // Re-cargar grupos para el modal
                        $grupos_modal = $conn->query("
                            SELECT g.id, g.nombre_grupo, c.nombre as carrera 
                            FROM grupos g
                            JOIN carreras c ON g.carrera_id = c.id
                            ORDER BY g.nombre_grupo
                        ");
                        while($grupo = $grupos_modal->fetch_assoc()): ?>
                            <option value="<?php echo $grupo['id']; ?>">
                                <?php echo $grupo['nombre_grupo'] . " - " . $grupo['carrera']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>

    <script>
        // Funciones para el modal
        var modal = document.getElementById("modalEditar");
        var span = document.getElementsByClassName("close")[0];
        
        function cargarDatosAlumno(id) {
            // En una implementación real, aquí harías una petición AJAX para obtener los datos del alumno
            // Por ahora, mostraremos el modal
            modal.style.display = "block";
            
            // Para una implementación completa, necesitarías:
            // 1. Hacer una petición AJAX a un endpoint que devuelva los datos del alumno
            // 2. Rellenar los campos del formulario con los datos recibidos
            // 3. Enviar el formulario para actualizar
        }
        
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <style>
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            position: relative;
        }
        
        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        
        .close:hover {
            color: #333;
        }
        
        .estado {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        
        .estado.activo {
            background-color: #d4edda;
            color: #155724;
        }
        
        .estado.inactivo {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>

<?php
include 'footer.php';
?>