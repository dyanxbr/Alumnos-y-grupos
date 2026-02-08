<?php
session_start();
// --- CONEXIÓN API ---
define('API_URL', 'https://api-alumnos-production-cdcc.up.railway.app/api');

function pedir_api($endpoint) {
    $ch = curl_init(API_URL . '/' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    $data = curl_exec($ch);
    if ($data === false) { curl_close($ch); return null; }
    curl_close($ch);
    return json_decode($data, true);
}

// PROTECCIÓN: Si devuelve null, asignamos array vacío
$grupos = pedir_api('grupos');
if (!is_array($grupos)) $grupos = [];

$api_online = (count($grupos) > 0 || $grupos !== null);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Alumno - SisEscolar</title>
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
    </style>
</head>
<body>
    <header class="container app-header">
        <a href="index.php" class="brand-name"><div class="brand-box">S</div><span class="serif-title">SisEscolar</span></a>
    </header>

    <main class="container pb-5">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje'] == 'exito' ? 'success' : 'danger'; ?> mb-4 rounded-0 border-0">
                <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
            </div>
        <?php endif; ?>

        <div class="row g-0">
            <div class="col-lg-8 pe-lg-5">
                <div class="form-card">
                    <i class="far fa-id-card icon-header"></i>
                    <h2 class="serif-title mb-4">Inscripción de Alumno</h2>
                    <form action="procesar_alumno.php" method="POST">
                        <div class="mb-4"><label class="form-label">Nombre(s)</label><input type="text" name="nombre" class="form-control" required></div>
                        <div class="row mb-4">
                            <div class="col-md-6"><label class="form-label">Apellido Paterno</label><input type="text" name="apellido_p" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Apellido Materno</label><input type="text" name="apellido_m" class="form-control" required></div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Grupo</label>
                            <select name="grupo_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach($grupos as $grupo): ?>
                                    <option value="<?php echo $grupo['id_grupo']; ?>"><?php echo $grupo['codigo_grupo'] . ' - ' . $grupo['nombre_carrera']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-navy" <?php echo !$api_online ? 'disabled' : ''; ?>>Registrar Alumno</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="sidebar-info">
                    <h4 class="serif-title mb-4">Ayuda</h4>
                    <div class="info-item"><span class="info-title">Grupos</span><span class="info-desc">Solo aparecen grupos activos.</span></div>
                    <div class="mt-5 text-center"><a href="index.php" class="text-decoration-none fw-bold" style="color:var(--navy-solid)">Volver al Inicio</a></div>
                </div>
            </div>
        </div>
    </main>
    <footer class="container text-center py-5 mt-5 border-top" style="border-color: rgba(0,0,0,0.05) !important;"><small class="text-muted">SisEscolar © <?php echo date('Y'); ?></small></footer>
</body>
</html>