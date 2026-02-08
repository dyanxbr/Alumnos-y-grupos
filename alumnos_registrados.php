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

// PROTECCIÓN COMPLETA
$lista_grupos = pedir_api('grupos');
$lista_alumnos = pedir_api('alumnos');

// Si falló, convertir a array vacío
if (!is_array($lista_grupos)) $lista_grupos = [];
if (!is_array($lista_alumnos)) $lista_alumnos = [];

$filtro_grupo_id = isset($_GET['grupo_id']) && $_GET['grupo_id'] != '' ? intval($_GET['grupo_id']) : 0;
$alumnos_a_mostrar = [];
$total_alumnos = 0; $alumnos_activos = 0; $alumnos_inactivos = 0;

foreach ($lista_alumnos as $alumno) {
    if ($filtro_grupo_id > 0 && isset($alumno['id_grupo']) && $alumno['id_grupo'] != $filtro_grupo_id) continue;
    $alumnos_a_mostrar[] = $alumno;
    $total_alumnos++;
    if (isset($alumno['estatus']) && $alumno['estatus'] == 1) $alumnos_activos++; else $alumnos_inactivos++;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio - SisEscolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-canvas: #f4f4f0; --navy-solid: #0f172a; --navy-light: #334155; --gold-line: #d97706; --white: #ffffff; --border-soft: rgba(15, 23, 42, 0.08); }
        body { background-color: var(--bg-canvas); color: var(--navy-solid); font-family: 'Inter', sans-serif; min-height: 100vh; }
        .serif-title { font-family: 'Playfair Display', serif; }
        .app-header { padding: 2.5rem 0; margin-bottom: 2rem; border-bottom: 1px solid rgba(15, 23, 42, 0.05); }
        .brand-name { font-size: 1.8rem; text-decoration: none; color: var(--navy-solid); display: flex; align-items: center; gap: 15px; }
        .brand-box { width: 40px; height: 40px; background: var(--navy-solid); color: var(--bg-canvas); display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-weight: bold; }
        .table-card { background: var(--white); border: 1px solid var(--border-soft); padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.01); }
        .custom-table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--navy-light); border-bottom: 2px solid var(--navy-solid); padding-bottom: 1rem; }
        .custom-table td { vertical-align: middle; font-size: 0.9rem; color: var(--navy-solid); padding: 1rem 0.5rem; border-bottom: 1px solid var(--border-soft); }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .bg-active { background-color: #dcfce7; color: #15803d; }
        .bg-inactive { background-color: #fee2e2; color: #b91c1c; }
        .search-input { border: 1px solid #cbd5e1; padding: 10px 15px; width: 100%; background: #f8fafc; font-size: 0.9rem; }
        .btn-action { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; border: none; transition: 0.2s; cursor: pointer; }
        .btn-edit { background: #e0f2fe; color: #0284c7; } .btn-edit:hover { background: #0284c7; color: white; }
        .btn-toggle { background: #f1f5f9; color: #64748b; } .btn-toggle:hover { background: #64748b; color: white; }
        .sidebar-card { background: var(--white); padding: 1.5rem; border: 1px solid var(--border-soft); margin-bottom: 1.5rem; }
        .stat-mini { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .stat-val { font-weight: 700; font-family: 'Playfair Display', serif; font-size: 1.2rem; }
    </style>
</head>
<body>
    <header class="container app-header">
        <a href="index.php" class="brand-name"><div class="brand-box">S</div><span class="serif-title">SisEscolar</span></a>
    </header>

    <main class="container pb-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="table-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="serif-title mb-0">Directorio de Alumnos</h4>
                        <span class="badge bg-dark rounded-0"><?php echo count($alumnos_a_mostrar); ?> Resultados</span>
                    </div>
                    <div class="mb-4"><input type="text" id="buscador" class="search-input" placeholder="Buscar..." onkeyup="filtrarTabla()"></div>
                    <div class="table-responsive">
                        <table class="table custom-table" id="tablaAlumnos">
                            <thead><tr><th>ID</th><th>Estudiante</th><th>Grupo</th><th>Estatus</th><th class="text-end">Acciones</th></tr></thead>
                            <tbody>
                                <?php if (count($alumnos_a_mostrar) > 0): ?>
                                    <?php foreach($alumnos_a_mostrar as $a): $activo = ($a['estatus'] == 1); ?>
                                        <tr>
                                            <td class="text-muted small">#<?php echo str_pad($a['id_alumno'], 3, '0', STR_PAD_LEFT); ?></td>
                                            <td><span class="fw-bold d-block"><?php echo $a['apellido_p'].' '.$a['apellido_m']; ?></span><span class="small text-muted"><?php echo $a['nombre']; ?></span></td>
                                            <td><span class="badge bg-light text-dark border fw-normal"><?php echo $a['codigo_grupo'] ?? 'Sin Asignar'; ?></span></td>
                                            <td><span class="status-badge <?php echo $activo ? 'bg-active' : 'bg-inactive'; ?>"><?php echo $activo ? 'Activo' : 'Baja'; ?></span></td>
                                            <td class="text-end">
                                                <button onclick='abrirModal(<?php echo json_encode($a); ?>)' class="btn-action btn-edit me-1"><i class="fas fa-pen small"></i></button>
                                                <button onclick="cambiarEstatus(<?php echo $a['id_alumno']; ?>)" class="btn-action btn-toggle"><i class="fas <?php echo $activo ? 'fa-ban' : 'fa-check'; ?> small"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">Sin datos.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="sidebar-card">
                    <h6 class="text-uppercase small fw-bold text-muted mb-3">Filtro</h6>
                    <form method="GET"><select name="grupo_id" class="form-select mb-3 rounded-0" onchange="this.form.submit()"><option value="">Todos</option><?php foreach($lista_grupos as $g): ?><option value="<?php echo $g['id_grupo']; ?>" <?php echo ($filtro_grupo_id == $g['id_grupo'])?'selected':''; ?>><?php echo $g['codigo_grupo']; ?></option><?php endforeach; ?></select></form>
                </div>
                <div class="sidebar-card">
                    <h6 class="text-uppercase small fw-bold text-muted mb-3">Resumen</h6>
                    <div class="stat-mini"><span>Total</span><span class="stat-val"><?php echo $total_alumnos; ?></span></div>
                </div>
                <div class="text-center mt-4"><a href="index.php" class="text-decoration-none fw-bold" style="color:var(--navy-solid)">Volver</a></div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title serif-title">Editar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4">
                    <form id="formEditar" action="actualizar_alumno.php" method="POST">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3"><label class="form-label small fw-bold">Nombre</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required></div>
                        <div class="row"><div class="col-6 mb-3"><label class="form-label small fw-bold">Apellido P.</label><input type="text" name="apellido_p" id="edit_p" class="form-control" required></div><div class="col-6 mb-3"><label class="form-label small fw-bold">Apellido M.</label><input type="text" name="apellido_m" id="edit_m" class="form-control" required></div></div>
                        <div class="mb-4"><label class="form-label small fw-bold">Grupo</label><select name="grupo_id" id="edit_grupo" class="form-select" required><?php foreach($lista_grupos as $g): ?><option value="<?php echo $g['id_grupo']; ?>"><?php echo $g['codigo_grupo']; ?></option><?php endforeach; ?></select></div>
                        <button type="submit" class="btn btn-dark w-100 rounded-0">GUARDAR</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filtrarTabla() { var filter = document.getElementById("buscador").value.toUpperCase(); var tr = document.getElementById("tablaAlumnos").getElementsByTagName("tr"); for (i = 1; i < tr.length; i++) { var td = tr[i].getElementsByTagName("td")[1]; if (td) { tr[i].style.display = (td.textContent.toUpperCase().indexOf(filter) > -1) ? "" : "none"; } } }
        function abrirModal(a) { document.getElementById('edit_id').value = a.id_alumno; document.getElementById('edit_nombre').value = a.nombre; document.getElementById('edit_p').value = a.apellido_p; document.getElementById('edit_m').value = a.apellido_m; if(a.id_grupo) document.getElementById('edit_grupo').value = a.id_grupo; new bootstrap.Modal(document.getElementById('modalEditar')).show(); }
        async function cambiarEstatus(id) { try { const res = await fetch(`http://localhost:3000/api/alumnos/${id}/estatus`, {method:'PATCH'}); const data = await res.json(); if(data.success) location.reload(); } catch(e) { alert('Error conexión'); } }
    </script>
</body>
</html>