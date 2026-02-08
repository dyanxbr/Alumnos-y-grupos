<?php
session_start();

// --- CONEXIÓN API ---
define('API_URL', 'http://localhost:3000/api');

function pedir_api($endpoint) {
    $ch = curl_init(API_URL . '/' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Espera máximo 2 segundos
    $data = curl_exec($ch);
    curl_close($ch);
    
    // Si no hay respuesta (API apagada), devolvemos null
    if ($data === false) return null;
    
    return json_decode($data, true);
}

// --- OBTENCIÓN DE DATOS SEGURA ---
$data_grupos = pedir_api('grupos');
$data_alumnos = pedir_api('alumnos');

// CORRECCIÓN: Verificamos si es un array antes de usar count()
// Si $data_grupos es null, ponemos 0 y evitamos el error.
$total_grupos = is_array($data_grupos) ? count($data_grupos) : 0;
$total_alumnos = is_array($data_alumnos) ? count($data_alumnos) : 0;

// Detectar si la API está en línea (Si devolvió algo distinto a null)
$api_online = ($data_grupos !== null);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisEscolar - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-canvas: #f4f4f0;      /* Color Hueso/Papel */
            --navy-solid: #0f172a;     /* Azul Marino Profundo */
            --navy-light: #334155;     /* Gris Azulado */
            --gold-line: #d97706;      /* Línea dorada */
            --white: #ffffff;
        }

        body {
            background-color: var(--bg-canvas);
            color: var(--navy-solid);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        h1, h2, h3, h4, .serif-title {
            font-family: 'Playfair Display', serif;
        }

        /* HEADER MINIMALISTA */
        .app-header {
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        }

        .brand-name {
            font-size: 1.8rem;
            text-decoration: none;
            color: var(--navy-solid);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .brand-box {
            width: 40px; 
            height: 40px; 
            background: var(--navy-solid); 
            color: var(--bg-canvas);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-weight: bold;
        }

        /* --- COLUMNA IZQUIERDA (EL "TWO") --- */
        .static-card {
            background: var(--white);
            border: 1px solid rgba(0,0,0,0.05);
            padding: 2.5rem;
            height: 100%;
            transition: border-color 0.2s ease;
            position: relative;
        }

        .static-card:hover {
            border-color: var(--navy-solid);
            cursor: pointer;
        }

        .icon-frame {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--navy-solid);
            display: inline-block;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--gold-line);
        }

        .card-head { font-size: 1.25rem; margin-bottom: 0.5rem; font-weight: 600; }
        .card-body-text { font-size: 0.95rem; color: var(--navy-light); line-height: 1.6; }

        .static-arrow {
            position: absolute;
            bottom: 2rem;
            right: 2rem;
            color: var(--navy-light);
            font-size: 1.2rem;
        }
        
        .static-card:hover .static-arrow {
            color: var(--navy-solid);
        }

        /* --- COLUMNA DERECHA (EL "ONE") --- */
        .sidebar-panel {
            background-color: var(--navy-solid);
            color: var(--bg-canvas);
            padding: 2.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .date-big {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            line-height: 1;
            margin-bottom: 0;
        }
        .date-month {
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 0.8rem;
            opacity: 0.7;
            margin-bottom: 3rem;
            display: block;
        }

        .stat-row {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .stat-val { font-size: 2.5rem; font-family: 'Playfair Display', serif; }
        .stat-lbl { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; }

        .status-dot {
            width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block; margin-right: 8px;
        }

        a { text-decoration: none; color: inherit; }
    </style>
</head>
<body>

    <header class="container app-header">
        <a href="index.php" class="brand-name">
            <div class="brand-box">S</div>
            <span class="serif-title">SisEscolar</span>
        </a>
    </header>

    <main class="container pb-5">
        
        <?php if (!$api_online): ?>
            <div class="alert alert-danger mb-4 rounded-0 border-0">
                <i class="fas fa-exclamation-triangle me-2"></i> 
                <strong>Sin conexión:</strong> No se detecta la API de Node.js en ejecución. Asegúrate de correr <code>node index.js</code>.
            </div>
        <?php endif; ?>

        <div class="row g-0">
            
            <div class="col-lg-8 pe-lg-5">
                
                <h4 class="serif-title mb-4">Panel de Gestión</h4>

                <div class="row g-4">
                    <div class="col-md-6">
                        <a href="registrar_grupo.php">
                            <div class="static-card">
                                <i class="fas fa-shapes icon-frame"></i>
                                <div class="card-head serif-title">Grupos</div>
                                <p class="card-body-text">Configura aulas, grados y turnos.</p>
                                <i class="fas fa-arrow-right static-arrow"></i>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="registrar_alumno.php">
                            <div class="static-card">
                                <i class="fas fa-user-graduate icon-frame"></i>
                                <div class="card-head serif-title">Alumnos</div>
                                <p class="card-body-text">Registro de nuevos estudiantes.</p>
                                <i class="fas fa-arrow-right static-arrow"></i>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="gestion_carreras.php">
                            <div class="static-card">
                                <i class="fas fa-university icon-frame"></i>
                                <div class="card-head serif-title">Oferta Académica</div>
                                <p class="card-body-text">Activar o desactivar carreras del plan.</p>
                                <i class="fas fa-arrow-right static-arrow"></i>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="alumnos_registrados.php">
                            <div class="static-card">
                                <i class="fas fa-th-list icon-frame"></i>
                                <div class="card-head serif-title">Directorio</div>
                                <p class="card-body-text">Consulta y edición de matrícula.</p>
                                <i class="fas fa-arrow-right static-arrow"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="sidebar-panel">
                    
                    <div>
                        <div class="date-big"><?php echo date('d'); ?></div>
                        <span class="date-month"><?php echo date('F Y'); ?></span>
                    </div>

                    <div>
                        <div class="stat-row">
                            <div class="stat-val"><?php echo $total_alumnos; ?></div>
                            <span class="stat-lbl">Estudiantes Activos</span>
                        </div>

                        <div class="stat-row">
                            <div class="stat-val"><?php echo $total_grupos; ?></div>
                            <span class="stat-lbl">Grupos Abiertos</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top border-opacity-10" style="border-color: rgba(255,255,255,0.1) !important;">
                        <small style="opacity: 0.5; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">Estado de Conexión</small>
                        <div class="mt-2 d-flex align-items-center" style="font-size: 0.85rem;">
                            <span class="status-dot" style="background-color: <?php echo $api_online ? '#4ade80' : '#f87171'; ?>;"></span>
                            API Node.js
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <footer class="container text-center py-5 mt-5 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
        <small class="text-muted">SisEscolar © <?php echo date('Y'); ?></small>
    </footer>

</body>
</html>