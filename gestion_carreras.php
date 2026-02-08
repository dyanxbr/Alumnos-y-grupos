<?php
session_start();

// --- CONFIGURACIÓN Y API ---
define('API_URL', 'http://localhost:3000/api');

function pedir_api($endpoint) {
    $ch = curl_init(API_URL . '/' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}

// 1. OBTENER LISTA DE CARRERAS (Necesitas crear este endpoint simple en Node si no lo tienes,
// o usar el de catalogos. Asumiremos que /api/carreras devuelve todas).
// Si tu endpoint /api/carreras solo devuelve activas, crea uno /api/carreras/todas en node
// Ojo: Para gestión, necesitamos VER las inactivas también.
$lista_carreras = pedir_api('carreras-todas'); 

// NOTA: Si no tienes el endpoint 'carreras-todas', usa 'catalogos-registro' y extrae 'carreras'
// pero asegúrate que tu SQL en Node traiga TODO para el admin.
if(!$lista_carreras && isset($lista_carreras['carreras'])) {
    $lista_carreras = $lista_carreras['carreras'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carreras - SisEscolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        /* Estilos SisEscolar (Navy & Bone) */
        :root {
            --bg-canvas: #f4f4f0;
            --navy-solid: #0f172a;
            --navy-light: #334155;
            --gold-line: #d97706;
            --white: #ffffff;
            --border-soft: rgba(15, 23, 42, 0.08);
        }
        body { background-color: var(--bg-canvas); color: var(--navy-solid); font-family: 'Inter', sans-serif; min-height: 100vh; }
        .serif-title { font-family: 'Playfair Display', serif; }
        
        /* Header */
        .app-header { padding: 2.5rem 0; margin-bottom: 2rem; border-bottom: 1px solid rgba(15, 23, 42, 0.05); }
        .brand-name { font-size: 1.8rem; text-decoration: none; color: var(--navy-solid); display: flex; align-items: center; gap: 15px; }
        .brand-box { width: 40px; height: 40px; background: var(--navy-solid); color: var(--bg-canvas); display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-weight: bold; }

        /* Tabla */
        .table-card { background: var(--white); border: 1px solid var(--border-soft); padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.01); }
        .custom-table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--navy-light); border-bottom: 2px solid var(--navy-solid); padding-bottom: 1rem; }
        .custom-table td { vertical-align: middle; font-size: 0.9rem; color: var(--navy-solid); padding: 1rem 0.5rem; border-bottom: 1px solid var(--border-soft); }
        
        /* Badges */
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .bg-active { background-color: #dcfce7; color: #15803d; }
        .bg-inactive { background-color: #fee2e2; color: #b91c1c; }

        /* Botón Toggle */
        .btn-toggle { width: 35px; height: 35px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; transition: 0.2s; cursor: pointer; }
        .btn-on { background: #e0f2fe; color: #0284c7; }
        .btn-off { background: #f1f5f9; color: #94a3b8; }
        .btn-toggle:hover { transform: scale(1.1); }

        /* Sidebar */
        .sidebar-card { background: var(--white); padding: 1.5rem; border: 1px solid var(--border-soft); }
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
        <div class="row g-5">
            
            <div class="col-lg-8">
                <div class="table-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="serif-title mb-0">Oferta Académica</h4>
                        <span class="badge bg-dark rounded-0">Gestión de Carreras</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Siglas</th>
                                    <th>Nombre de la Carrera</th>
                                    <th>Estatus</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($lista_carreras): ?>
                                    <?php foreach($lista_carreras as $c): 
                                        $activo = ($c['estatus'] == 1);
                                    ?>
                                    <tr>
                                        <td class="text-muted small">#<?php echo $c['id_carrera']; ?></td>
                                        <td class="fw-bold"><?php echo $c['siglas']; ?></td>
                                        <td><?php echo $c['nombre_carrera']; ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $activo ? 'bg-active' : 'bg-inactive'; ?>">
                                                <?php echo $activo ? 'Visible' : 'Oculta'; ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button onclick="toggleCarrera(<?php echo $c['id_carrera']; ?>)" 
                                                    class="btn-toggle <?php echo $activo ? 'btn-on' : 'btn-off'; ?>"
                                                    title="<?php echo $activo ? 'Desactivar' : 'Activar'; ?>">
                                                <i class="fas <?php echo $activo ? 'fa-toggle-on' : 'fa-toggle-off'; ?>"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-4">No hay carreras registradas o error de conexión.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sidebar-card">
                    <h6 class="text-uppercase small fw-bold text-muted mb-3">Información Importante</h6>
                    <p class="small text-muted mb-3">
                        Al <strong>Desactivar</strong> una carrera:
                    </p>
                    <ul class="small text-muted ps-3 mb-4">
                        <li class="mb-2">La carrera ya no aparecerá en las opciones de registro.</li>
                        <li class="mb-2">Los <strong>grupos asociados</strong> a esta carrera se ocultarán automáticamente en el registro de alumnos.</li>
                        <li>Los datos históricos NO se borran, solo se ocultan.</li>
                    </ul>
                    
                    <a href="index.php" class="btn btn-outline-dark w-100 rounded-0 text-uppercase small fw-bold">
                        <i class="fas fa-arrow-left me-2"></i> Volver al Inicio
                    </a>
                </div>
            </div>

        </div>
    </main>

    <footer class="container text-center py-5 mt-5 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
        <small class="text-muted">SisEscolar © <?php echo date('Y'); ?></small>
    </footer>

    <script>
        async function toggleCarrera(id) {
            try {
                // Llamada a la API (Sin confirmar, directo como pediste)
                const res = await fetch(`http://localhost:3000/api/carreras/${id}/estatus`, { method: 'PATCH' });
                const data = await res.json();
                
                if(data.success) {
                    location.reload(); // Recarga inmediata
                } else {
                    alert('Error al actualizar');
                }
            } catch(e) {
                console.error(e);
                alert('Error de conexión');
            }
        }
    </script>
</body>
</html>