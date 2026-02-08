<?php
$apiUrl = 'http://localhost:3000/api'; 

$carreras = [];
$turnos = [];
$grados = [];

try {
    $carrerasData = @file_get_contents($apiUrl . '/carreras');
    $turnosData = @file_get_contents($apiUrl . '/turnos');
    $gradosData = @file_get_contents($apiUrl . '/grados');

    if ($carrerasData !== false) $carreras = json_decode($carrerasData, true) ?? [];
    if ($turnosData !== false) $turnos = json_decode($turnosData, true) ?? [];
    if ($gradosData !== false) $grados = json_decode($gradosData, true) ?? [];

} catch (Exception $e) {
    $carreras = [];
    $turnos = [];
    $grados = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Grupo - SisEscolar</title>
    <style>
        :root {
            --navy: #001f3f;
            --bone: #f5f5dc;
            --light-navy: #003366;
            --alert: #ff4136;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bone);
            color: var(--navy);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            transform: scale(0.9);
            transform-origin: top center;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 700px;
            border-top: 5px solid var(--navy);
        }

        h1 {
            text-align: center;
            color: var(--navy);
            margin-bottom: 5px;
        }

        .fecha-actual {
            text-align: right;
            font-size: 0.9em;
            color: #666;
            margin-bottom: 20px;
            font-style: italic;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        select, input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
        }

        select:focus, input:focus {
            border-color: var(--navy);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--navy);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background-color: var(--light-navy);
        }

        .alert-box {
            background-color: #ffebee;
            color: var(--alert);
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            display: none;
            border-left: 4px solid var(--alert);
            font-size: 0.9em;
        }

        @media (max-width: 600px) {
            body {
                transform: none; 
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Registrar Nuevo Grupo</h1>
    <div class="fecha-actual" id="fechaDisplay"></div>

    <form action="procesar_grupo.php" method="POST">
        <div class="form-group">
            <label for="nombre_grupo">Nombre del Grupo:</label>
            <input type="text" id="nombre_grupo" name="nombre_grupo" required placeholder="Ej. 1-A">
        </div>

        <div class="form-group">
            <label for="carrera">Carrera:</label>
            <select id="carrera" name="carrera_id" required>
                <option value="">Seleccione una carrera...</option>
                <?php foreach ($carreras as $carrera): ?>
                    <option value="<?= htmlspecialchars($carrera['id']) ?>">
                        <?= htmlspecialchars($carrera['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="grado">Grado:</label>
            <select id="grado" name="grado_id" required onchange="verificarTurno()">
                <option value="">Seleccione un grado...</option>
                <?php foreach ($grados as $grado): ?>
                    <option value="<?= htmlspecialchars($grado['id']) ?>" data-nivel="<?= htmlspecialchars($grado['nivel']) ?>">
                        <?= htmlspecialchars($grado['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="turno">Turno:</label>
            <select id="turno" name="turno_id" required>
                <option value="">Seleccione un turno...</option>
                <?php foreach ($turnos as $turno): ?>
                    <option value="<?= htmlspecialchars($turno['id']) ?>">
                        <?= htmlspecialchars($turno['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="turnoAlerta" class="alert-box">
            Nota: Para grados superiores (7° en adelante), el turno se asignará automáticamente a Vespertino según el reglamento.
        </div>

        <button type="submit">Guardar Grupo</button>
    </form>
</div>

<script>
    const fecha = new Date();
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fecha.toLocaleDateString('es-MX', opciones);
    
    document.getElementById('fechaDisplay').textContent = 
        fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);

    function verificarTurno() {
        const gradoSelect = document.getElementById('grado');
        const turnoSelect = document.getElementById('turno');
        const alerta = document.getElementById('turnoAlerta');
        
        const selectedOption = gradoSelect.options[gradoSelect.selectedIndex];
        const nivel = selectedOption.getAttribute('data-nivel'); 
        
        if (nivel && parseInt(nivel) >= 7) {
            alerta.style.display = 'block';
            
            for (let i = 0; i < turnoSelect.options.length; i++) {
                if (turnoSelect.options[i].text.toLowerCase().includes('vespertino')) {
                    turnoSelect.selectedIndex = i;
                    break;
                }
            }
        } else {
            alerta.style.display = 'none';
        }
    }
</script>

</body>
</html>