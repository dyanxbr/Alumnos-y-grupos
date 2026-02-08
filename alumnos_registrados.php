<?php
session_start();

/* =========================
   CONFIG API
========================= */
define('API_URL','https://api-alumnos-production-cdcc.up.railway.app/api');

function api($ep){
    $c=curl_init(API_URL.'/'.$ep);
    curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>5]);
    $r=curl_exec($c); curl_close($c);
    return $r?json_decode($r,true):[];
}

/* =========================
   DATOS
========================= */
$grupos  = api('grupos');
$alumnos = api('alumnos');

$filtro = intval($_GET['grupo_id'] ?? 0);
$lista=[]; $total=$act=$baja=0;

foreach($alumnos as $a){
    if($filtro && ($a['id_grupo']??0)!=$filtro) continue;
    $lista[]=$a;
    $total++;
    ($a['estatus']==1)?$act++:$baja++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Directorio - SisEscolar</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<style>
body{background:#f4f4f0;font-family:Inter,sans-serif}
.brand{font-family:'Playfair Display',serif}
.table-card{background:#fff;padding:2rem;border:1px solid #e5e7eb}
.status{padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600}
.active{background:#dcfce7;color:#15803d}
.inactive{background:#fee2e2;color:#b91c1c}
.btn-action{width:32px;height:32px;border-radius:50%;border:none}
.sidebar{background:#fff;padding:1.5rem;border:1px solid #e5e7eb}
</style>
</head>

<body>

<header class="container py-4 mb-4 border-bottom d-flex justify-content-between">
    <h2 class="brand mb-0">SisEscolar</h2>
    <a href="javascript:history.back()" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Regresar
    </a>
</header>

<main class="container pb-5">
<div class="row g-4">

<div class="col-lg-8">
<div class="table-card">

<div class="d-flex justify-content-between mb-3">
<h5 class="brand mb-0">Directorio de Alumnos</h5>
<span class="badge bg-dark rounded-0"><?=count($lista)?> registros</span>
</div>

<table class="table align-middle">
<thead>
<tr>
<th>ID</th><th>Alumno</th><th>Grupo</th><th>Estatus</th><th class="text-end">Acciones</th>
</tr>
</thead>
<tbody>

<?php foreach($lista as $a): $on=($a['estatus']==1); ?>
<tr>
<td>#<?=str_pad($a['id_alumno'],3,'0',STR_PAD_LEFT)?></td>
<td><strong><?=$a['apellido_p'].' '.$a['apellido_m']?></strong><br>
<small class="text-muted"><?=$a['nombre']?></small></td>
<td><span class="badge bg-light text-dark border"><?=$a['codigo_grupo']??'—'?></span></td>
<td><span class="status <?=$on?'active':'inactive'?>"><?=$on?'Activo':'Baja'?></span></td>
<td class="text-end">
<button class="btn-action bg-info text-white me-1" onclick='editar(<?=json_encode($a)?>)'>
<i class="fas fa-pen"></i>
</button>
<button class="btn-action bg-light" onclick="toggleAlumno(<?=$a['id_alumno']?>)">
<i class="fas <?=$on?'fa-ban':'fa-check'?>"></i>
</button>
</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>
</div>

<div class="col-lg-4">
<div class="sidebar mb-4">
<h6 class="text-uppercase small fw-bold mb-3">Filtrar por grupo</h6>
<form method="GET">
<select name="grupo_id" class="form-select" onchange="this.form.submit()">
<option value="">Todos</option>
<?php foreach($grupos as $g): ?>
<option value="<?=$g['id_grupo']?>" <?=$filtro==$g['id_grupo']?'selected':''?>><?=$g['codigo_grupo']?></option>
<?php endforeach; ?>
</select>
</form>
</div>

<div class="sidebar">
<h6 class="text-uppercase small fw-bold mb-3">Resumen</h6>
<div class="d-flex justify-content-between"><span>Total</span><strong><?=$total?></strong></div>
<div class="d-flex justify-content-between"><span>Activos</span><strong><?=$act?></strong></div>
<div class="d-flex justify-content-between"><span>Bajas</span><strong><?=$baja?></strong></div>
</div>
</div>
</div>
</main>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title brand">Editar Alumno</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="actualizar_alumno.php">
<div class="modal-body">
<input type="hidden" name="id" id="edit_id">
<div class="mb-3"><label class="form-label">Nombre</label><input class="form-control" name="nombre" id="edit_nombre" required></div>
<div class="row">
<div class="col-6 mb-3"><label>Apellido P.</label><input class="form-control" name="apellido_p" id="edit_p" required></div>
<div class="col-6 mb-3"><label>Apellido M.</label><input class="form-control" name="apellido_m" id="edit_m" required></div>
</div>
<div class="mb-3">
<label>Grupo</label>
<select name="grupo_id" id="edit_grupo" class="form-select">
<?php foreach($grupos as $g): ?>
<option value="<?=$g['id_grupo']?>"><?=$g['codigo_grupo']?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<div class="modal-footer">
<button class="btn btn-dark w-100">Guardar</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function editar(a){
    edit_id.value=a.id_alumno;
    edit_nombre.value=a.nombre;
    edit_p.value=a.apellido_p;
    edit_m.value=a.apellido_m;
    if(a.id_grupo) edit_grupo.value=a.id_grupo;
    new bootstrap.Modal(modalEditar).show();
}

async function toggleAlumno(id){
    const f=new FormData(); f.append('id',id);
    const r=await fetch('toggle_alumno.php',{method:'POST',body:f});
    const d=await r.json();
    if(d.success) location.reload();
    else alert('Error');
}
</script>

</body>
</html>
