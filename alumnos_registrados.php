<?php
session_start();

/* =========================
   CONFIG API (TU LÓGICA)
========================= */
define('API_URL','https://api-alumnos-production-cdcc.up.railway.app/api');

function api($ep){
    $c=curl_init(API_URL.'/'.$ep);
    curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>5]);
    $r=curl_exec($c); 
    curl_close($c);
    // Corrección pequeña para evitar error si la API falla
    return $r ? json_decode($r,true) : [];
}

/* =========================
   DATOS (TU LÓGICA)
========================= */
$grupos  = api('grupos');
$alumnos = api('alumnos');

$filtro = intval($_GET['grupo_id'] ?? 0);
$lista=[]; 
$total=0; 
$act=0; 
$baja=0;

// Tu ciclo original para filtrar y contar
if(is_array($alumnos)) {
    foreach($alumnos as $a){
        if($filtro && ($a['id_grupo']??0)!=$filtro) continue;
        $lista[]=$a;
        $total++;
        ($a['estatus']==1) ? $act++ : $baja++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Directorio - SisEscolar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        /* ESTILOS DISEÑO "NAVY & BONE" */
        :root {
            --bg-canvas: #f4f4f0;
            --navy-solid: #0f172a;
            --navy-light: #334155;
            --gold-line: #d97706;
            --white: #ffffff;
            --border-soft: rgba(15, 23, 42, 0.08);
        }

        body {
            background-color: var(--bg-canvas);
            color: var(--navy-solid);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        h1, h2, h3, h4, .serif-title, .brand-name { font-family: 'Playfair Display', serif; }

        /* HEADER */
        .app-header {
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        }
        .brand-name {
            font-size: 1.8rem; text-decoration: none; color: var(--navy-solid);
            display: flex; align-items: center; gap: 15px;
        }
        .brand-box {
            width: 40px; height: 40px; background: var(--navy-solid); color: var(--bg-canvas);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif; font-weight: bold;
        }

        /* TABLA Y CARDS */
        .table-card {
            background: var(--white);
            border: 1px solid var(--border-soft);
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.01);
        }

        .custom-table th {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            color: var(--navy-light); border-bottom: 2px solid var(--navy-solid); padding-bottom: 1rem;
        }
        
        .custom-table td {
            vertical-align: middle; font-size: 0.9rem; color: var(--navy-solid);
            padding: 1rem 0.5rem; border-bottom: 1px solid var(--border-soft);
        }

        /* BADGES Y BOTONES */
        .status-badge {
            padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
        }
        .bg-active { background-color: #dcfce7; color: #15803d; }
        .bg-inactive { background-color: #fee2e2; color: #b91c1c; }

        .btn-action {
            width: 30px; height: 30px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 50%; border: none; transition: 0.2s; cursor: pointer;
        }
        .btn-edit { background: #e0f2fe; color: #0284c7; }
        .btn-edit:hover { background: #0284c7; color: white; }
        
        .btn-toggle { background: #f1f5f9; color: #64748b; }
        .btn-toggle:hover { background: #64748b; color: white; }

        /* SIDEBAR */
        .sidebar-card {
            background: var(--white); padding: 1.5rem; border: 1px solid var(--border-soft); margin-bottom: 1.5rem;
        }
        .stat-mini {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0; border-bottom: 1px solid #f1f5f9;
        }
        .stat-val { font-weight: 700; font-family: 'Playfair Display', serif; font-size: 1.2rem; }
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
                        <h4 class="serif-title mb-0">Directorio de Alumnos</h4>
                        <span class="badge bg-dark rounded-0"><?=count($lista)?> Resultados</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Estudiante</th>
                                    <th>Grupo</th>
                                    <th>Estatus</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($lista) > 0): ?>
                                    <?php foreach($lista as $a): 
                                        $on = ($a['estatus']==1); 
                                    ?>
                                        <tr>
                                            <td class="text-muted small">#<?=str_pad($a['id_alumno'],3,'0',STR_PAD_LEFT)?></td>
                                            <td>
                                                <span class="fw-bold d-block"><?=$a['apellido_p'].' '.$a['apellido_m']?></span>
                                                <span class="small text-muted"><?=$a['nombre']?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border fw-normal">
                                                    <?=$a['codigo_grupo']??'Sin Asignar'?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?=$on?'bg-active':'bg-inactive'?>">
                                                    <?=$on?'Activo':'Baja'?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button onclick='editar(<?=json_encode($a)?>)' class="btn-action btn-edit me-1" title="Editar">
                                                    <i class="fas fa-pen small"></i>
                                                </button>
                                                
                                                <button onclick="toggleAlumno(<?=$a['id_alumno']?>)" class="btn-action btn-toggle" title="Cambiar Estatus">
                                                    <i class="fas <?=$on?'fa-ban':'fa-check'?> small"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            No se encontraron alumnos con los filtros actuales.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="sidebar-card">
                    <h6 class="text-uppercase small fw-bold text-muted mb-3">Filtrar por Grupo</h6>
                    <form method="GET">
                        <select name="grupo_id" class="form-select mb-3 rounded-0" onchange="this.form.submit()">
                            <option value="">Todos los grupos</option>
                            <?php foreach($grupos as $g): ?>
                                <option value="<?=$g['id_grupo']?>" <?=$filtro==$g['id_grupo']?'selected':''?>>
                                    <?=$g['codigo_grupo']?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if($filtro > 0): ?>
                            <a href="?" class="btn btn-outline-danger btn-sm w-100 rounded-0">Limpiar Filtro</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="sidebar-card">
                    <h6 class="text-uppercase small fw-bold text-muted mb-3">Resumen</h6>
                    
                    <div class="stat-mini">
                        <span>Total Alumnos</span>
                        <span class="stat-val"><?=$total?></span>
                    </div>
                    <div class="stat-mini text-success">
                        <span>Activos</span>
                        <span class="stat-val"><?=$act?></span>
                    </div>
                    <div class="stat-mini text-danger border-0">
                        <span>Bajas</span>
                        <span class="stat-val"><?=$baja?></span>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none text-muted small fw-bold">
                        <i class="fas fa-arrow-left me-2"></i> VOLVER AL DASHBOARD
                    </a>
                </div>

            </div>
        </div>
    </main>

    <footer class="container text-center py-5 mt-5 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
        <small class="text-muted">SisEscolar © <?=date('Y')?></small>
    </footer>

    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title serif-title">Editar Alumno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formEditar" method="POST" action="actualizar_alumno.php">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nombre</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control rounded-0" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold text-muted">Apellido P.</label>
                                <input type="text" name="apellido_p" id="edit_p" class="form-control rounded-0" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold text-muted">Apellido M.</label>
                                <input type="text" name="apellido_m" id="edit_m" class="form-control rounded-0" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Grupo</label>
                            <select name="grupo_id" id="edit_grupo" class="form-select rounded-0">
                                <?php foreach($grupos as $g): ?>
                                    <option value="<?=$g['id_grupo']?>"><?=$g['codigo_grupo']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-dark w-100 rounded-0">GUARDAR CAMBIOS</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editar(a){
            document.getElementById('edit_id').value = a.id_alumno;
            document.getElementById('edit_nombre').value = a.nombre;
            document.getElementById('edit_p').value = a.apellido_p;
            document.getElementById('edit_m').value = a.apellido_m;
            if(a.id_grupo) document.getElementById('edit_grupo').value = a.id_grupo;
            
            var myModal = new bootstrap.Modal(document.getElementById('modalEditar'));
            myModal.show();
        }

        async function toggleAlumno(id){
            // Usamos FormData como en tu código reciente
            const f = new FormData(); 
            f.append('id', id);
            
            try {
                // Asegúrate que este archivo exista, o ajusta la ruta a tu API directa si prefieres
                const r = await fetch('toggle_alumno.php', {
                    method: 'POST',
                    body: f
                });
                const d = await r.json();
                
                if(d.success) {
                    location.reload();
                } else {
                    alert('Error al actualizar: ' + (d.error || 'Desconocido'));
                }
            } catch(e) {
                console.error(e);
                alert('Error de conexión');
            }
        }
    </script>
</body>
</html>