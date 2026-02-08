<?php
session_start();

/* =========================
   CONFIGURACIÓN API
========================= */
define('API_URL', 'https://api-alumnos-production-cdcc.up.railway.app/api');

function pedir_api($endpoint) {
    $ch = curl_init(API_URL.'/'.$endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res ? json_decode($res, true) : [];
}

/* =========================
   DATOS
========================= */
$grupos  = pedir_api('grupos');
$alumnos = pedir_api('alumnos');

if (!is_array($grupos))  $grupos  = [];
if (!is_array($alumnos)) $alumnos = [];

$filtro = isset($_GET['grupo_id']) ? intval($_GET['grupo_id']) : 0;

$lista = [];
$total = $activos = $bajas = 0;

foreach ($alumnos as $a) {
    if ($filtro && ($a['id_grupo'] ?? 0) != $filtro) continue;

    $lista[] = $a;
    $total++;
    ($a['estatus'] ?? 0) == 1 ? $activos++ : $bajas++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Directorio - SisEscolar</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<style>
:root{
    --navy:#0f172a;
    --light:#f4f4f0;
    --border:#e5e7eb;
}
body{background:var(--light);font-family:Inter,sans-serif}
.brand{font-family:"Playfair Display",serif}
.table-card{background:#fff;border:1px solid var(--border);padding:2rem}
.status{padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600}
.active{background:#dcfce7;color:#15803d}
.inactive{background:#fee2e2;color:#b91c1c}
.btn-action{width:32px;height:32px;border-radius:50%;border:none}
.sidebar{background:#fff;border:1px solid var(--border);padding:1.5rem}
</style>
</head>

<body>

<header class="container py-4 mb-4 border-bottom">
    <h2 class="brand">SisEscolar</h2>
</header>

<main class="container pb-5">
<div class="row g-4">

<!-- TABLA -->
<div class="col-lg-8">
<div class="table-card">

<div class="d-flex justify-content-between mb-3">
    <h5 class="brand mb-0">Directorio de Alumnos</h5>
    <span class="badge bg-dark rounded-0"><?= count($lista) ?> registros</span>
</div>

<table class="table align-middle">
<thead>
<tr>
<th>ID</th>
<th>Alumno</th>
<th>Grupo</th>
<th>Estatus</th>
<th class="text-end">Acción</th>
</tr>
</thead>
<tbody>

<?php if($lista): foreach($lista as $a): $on = ($a['estatus']==1); ?>
<tr>
<td class="text-muted">#<?= str_pad($a['id_alumno'],3,'0',STR_PAD_LEFT) ?></td>
<td>
<strong><?= $a['apellido_p'].' '.$a['apellido_m'] ?></strong><br>
<small class="text-muted"><?= $a['nombre'] ?></small>
</td>
<td><span class="badge bg-light text-dark border"><?= $a['codigo_grupo'] ?? '—' ?></span></td>
<td><span class="status <?= $on?'active':'inactive' ?>"><?= $on?'Activo':'Baja' ?></span></td>
<td class="text-end">
<button class="btn-action bg-light" onclick="toggleAlumno(<?= $a['id_alumno'] ?>)">
<i class="fas <?= $on?'fa-ban':'fa-check' ?>"></i>
</button>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="5" class="text-center text-muted py-4">Sin alumnos</td></tr>
<?php endif; ?>

</tbody>
</table>

</div>
</div>

<!-- SIDEBAR -->
<div class="col-lg-4">

<div class="sidebar mb-4">
<h6 class="text-uppercase small fw-bold mb-3">Filtrar por grupo</h6>
<form method="GET">
<select name="grupo_id" class="form-select" onchange="this.form.submit()">
<option value="">Todos</option>
<?php foreach($grupos as $g): ?>
<option value="<?= $g['id_grupo'] ?>" <?= $filtro==$g['id_grupo']?'selected':'' ?>>
<?= $g['codigo_grupo'] ?>
</option>
<?php endforeach; ?>
</select>
</form>
</div>

<div class="sidebar">
<h6 class="text-uppercase small fw-bold mb-3">Resumen</h6>
<div class="d-flex justify-content-between"><span>Total</span><strong><?= $total ?></strong></div>
<div class="d-flex justify-content-between"><span>Activos</span><strong><?= $activos ?></strong></div>
<div class="d-flex justify-content-between"><span>Bajas</span><strong><?= $bajas ?></strong></div>
</div>

</div>
</div>
</main>

<script>
async function toggleAlumno(id){
    const f = new FormData();
    f.append('id',id);

    const r = await fetch('toggle_alumno.php',{
        method:'POST',
        body:f
    });

    const d = await r.json();
    if(d.success) location.reload();
    else alert('No se pudo cambiar el estatus');
}
</script>

</body>
</html>
