<?php
session_start();

// --- CONEXIÓN API ---
define('API_URL', 'https://api-alumnos-production-cdcc.up.railway.app/api');

function pedir_api($endpoint) {
    $ch = curl_init(API_URL . '/' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Si tarda más de 2 seg, se rinde
    $data = curl_exec($ch);
    
    // Si falla la conexión, devuelve null
    if ($data === false) { curl_close($ch); return null; }
    
    curl_close($ch);
    return json_decode($data, true);
}

// Obtener datos de la API de forma segura
$data = pedir_api('catalogos-registro');

// PROTECCIÓN: Si $data es null, usamos arrays vacíos para que no falle el foreach
$carreras = ($data && isset($data['carreras'])) ? $data['carreras'] : [];
$turnos   = ($data && isset($data['turnos']))   ? $data['turnos']   : [];
$grados   = ($data && isset($data['grados']))   ? $data['grados']   : [];

$api_online = ($data !== null);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Grupo - SisEscolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-canvas: #f4f4f0; --navy-solid: #0f172a; --navy-light: #334155; --gold-line: #d97706; --white: #ffffff; --input-border: #cbd5e1; }
        body { background-color: var(--bg-canvas); color: var(--navy-solid); font-family: 'Inter', sans-serif; min-height: 100vh; }
        .serif-title { font-family: 'Playfair Display', serif; }
        .app-header { padding: 2.5rem 0; margin-bottom: 2rem; border-bottom: 1px solid rgba(15, 23, 42, 0.05); }
        .brand-name { font-size: 1.8rem; text-decoration: none; color: var(--navy-solid); display: flex; align-items: center; gap: 15px; }
        .brand-box { width: 40px; height: 40px; background: var(--navy-solid); color: var(--bg-canvas); display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-weight: bold; }
        .form-card { background: var(--white); border: 1px solid rgba(0,0,0,0.05); padding: 3rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .icon-header { font-size: 1.5rem; color: var(--navy-solid); margin-bottom: 1rem; display: inline-block; padding-bottom: 5px; border-bottom: 3px solid var(--gold-line); }
        .form-label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: var(--navy-light); font-weight: 600; margin-bottom: 0.5rem; }
        .form-control, .form-select { border-radius: 0; border: 1px solid var(--input-border); padding: 0.75rem 1rem; font-size: 1rem; background-color: #fcfcfc; transition: all 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--navy-solid); box-shadow: none; background-color: var(--white); }
        .btn-navy { background-color: var(--navy-solid); color: var(--white); border: none; padding: 1rem 2rem; border-radius: 0; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem; font-weight: 600; width: 100%; transition: background 0.3s; }
        .btn-navy:hover { background-color: #1e293b; color: var(--white); }
        .sidebar-info { background-color: #e2e8f0; padding: 2rem; height: 100%; color: var(--navy-light); }
        .info-item { margin-bottom: 2rem; border-left: 3px solid var(--navy-solid); padding-left: 1rem; }
        .info-title { font-weight: 700; color: var(--navy-solid); display: block; margin-bottom: 5px; }
        .info-desc { font-size: 0.9rem; line-height: 1.5; }
        .code-example { background: var(--navy-solid); color: var(--white); padding: 5px 10px; font-family: monospace; font-size: 0.8rem; border-radius: 4px; }
        .vespertino-alert { background-color: #fff1f2; border: 1px solid #fda4af; color: #9f1239; padding: 10px; font-size: 0.85rem; margin-top: 10px; border-left: 3px solid #be123c; display: none; }
    </style>
</head>
<body>
    <header class="container app-header">
        <a href="index.php" class="brand-name"><div class="brand-box">S</div><span class="serif-title">SisEscolar</span></a>
    </header>

    <main class="container pb-5">
        <?php if (!$api_online): ?>
            <div class="alert alert-danger mb-4 rounded-0 border-0">
                <i class="fas fa-exclamation-triangle me-2"></i> Error de conexión: No se pudo cargar el catálogo. Asegúrate de que <code>node index.js</code> esté corriendo.
            </div>
        <?php endif; ?>

        <div class="row g-0">
            <div class="col-lg-8 pe-lg-5">
                <div class="form-card">
                    <i class="fas fa-layer-group icon-header"></i>
                    <h2 class="serif-title mb-4">Configuración de Grupo</h2>
                    <form action="procesar_grupo.php" method="POST">
                        <div class="mb-4">
                            <label for="id_carrera" class="form-label">Programa Académico</label>
                            <select name="id_carrera" id="id_carrera" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach($carreras as $c): ?>
                                    <option value="<?php echo $c['id_carrera']; ?>"><?php echo $c['nombre_carrera']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="id_grado" class="form-label">Grado</label>
                            <select name="id_grado" id="id_grado" class="form-select" required onchange="verificarTurno()">
                                <option value="">Seleccione...</option>
                                <?php foreach($grados as $g): ?>
                                    <option value="<?php echo $g['id_grado']; ?>" data-numero="<?php echo $g['numero_grado']; ?>"><?php echo $g['numero_grado']; ?>° Cuatrimestre</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label for="id_turno" class="form-label">Turno</label>
                            <select name="id_turno" id="id_turno" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach($turnos as $t): ?>
                                    <option value="<?php echo $t['id_turno']; ?>"><?php echo $t['nombre_turno']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div id="msg-vespertino" class="vespertino-alert">
                                <i class="fas fa-exclamation-circle me-1"></i> <strong>Aviso:</strong> Grados avanzados (7°+) se asignan a Vespertino.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-navy" <?php echo !$api_online ? 'disabled' : ''; ?>>Generar Grupo</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="sidebar-info">
                    <h4 class="serif-title mb-4">Reglas</h4>
                    <div class="info-item"><span class="info-title">Código Automático</span><span class="info-desc">Ejemplo: <span class="code-example">ISC1001-V</span></span></div>
                    <div class="mt-5 text-center"><a href="index.php" class="text-decoration-none fw-bold" style="color:var(--navy-solid)">Volver al Inicio</a></div>
                </div>
            </div>
        </div>
    </main>
    <script>
        function verificarTurno() {
            var sel = document.getElementById('id_grado');
            var aviso = document.getElementById('msg-vespertino');
            if (sel.selectedIndex > 0 && parseInt(sel.options[sel.selectedIndex].getAttribute('data-numero')) >= 7) {
                aviso.style.display = 'block';
            } else { aviso.style.display = 'none'; }
        }
    </script>
</body>
</html>