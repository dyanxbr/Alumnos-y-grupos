<?php
session_start();

/* =========================
   CONFIGURACIÓN API
========================= */
define('API_URL', 'https://api-alumnos-production-cdcc.up.railway.app/api');

function pedir_api($endpoint) {
    $ch = curl_init(API_URL . '/' . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_FAILONERROR => true
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data ? json_decode($data, true) : [];
}

/* =========================
   OBTENER DATOS
========================= */
$lista_grupos  = pedir_api('grupos');
$lista_alumnos = pedir_api('alumnos');

if (!is_array($lista_grupos))  $lista_grupos = [];
if (!is_array($lista_alumnos)) $lista_alumnos = [];

/* =========================
   FILTRO
========================= */
$filtro_grupo_id = isset($_GET['grupo_id']) ? intval($_GET['grupo_id']) : 0;

$alumnos_a_mostrar = [];
$total_alumnos = $alumnos_activos = $alumnos_inactivos = 0;

foreach ($lista_alumnos as $a) {
    if ($filtro_grupo_id && ($a['id_grupo'] ?? 0) != $filtro_grupo_id) continue;

    $alumnos_a_mostrar[] = $a;
    $total_alumnos++;
    ($a['estatus'] ?? 0) == 1 ? $alumnos_activos++ : $alumnos_inactivos++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Directorio - SisEscolar</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
body{background:#f4f4f0;font-family:Inter,sans-serif}
.table-card{background:#fff;padding:2rem;border:1px solid #e5e7eb}
.status-badge{padding:4px 10px;border-radius:20px;font-size:.75rem;font-weight:600}
.bg-active{background:#dcfce7;color:#15803d}
.bg-inactive{background:#fee2e2;color:#b91c1c}
.btn-action{width:32px;height:32px;border-radius:50%;border:none}
</style>
</head>

<body>

<div class="container py-4">
<h3 class="mb-4">Directorio de Alumnos</h3>

<div class="table-card">
<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Grupo</th>
<th>Estatus</th>
<th class="text-end">Acción</th>
</tr>
</thead>
<tbody>

<?php if ($alumnos_a_mostrar): foreach ($alumnos_a_mostrar as $a): 
    $activo = ($a['estatus'] == 1);
?>
<tr>
<td>#<?= str_pad($a['id_alumno'],3,'0',STR_PAD_LEFT) ?></td>
<td>
<strong><?= $a['apellido_p'].' '.$a['apellido_m'] ?></strong><br>
<small><?= $a['nombre'] ?></small>
</td>
<td><?= $a['codigo_grupo'] ?? '—' ?></td>
<td>
<span class="status-badge <?= $activo?'bg-active':'bg-inactive' ?>">
<?= $activo?'Activo':'Baja' ?>
</span>
</td>
<td class="text-end">
<button class="btn-action bg-light" onclick="cambiarEstatus(<?= $a['id_alumno'] ?>)">
<i class="fas <?= $activo?'fa-ban':'fa-check' ?>"></i>
</button>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="5" class="text-center text-muted">Sin alumnos</td></tr>
<?php endif; ?>

</tbody>
</table>
</div>
</div>

<script>
/* =========================
   API URL GLOBAL
========================= */
const API_URL = "https://api-alumnos-production-cdcc.up.railway.app/api";

/* =========================
   CAMBIAR ESTATUS
========================= */
async function cambiarEstatus(id) {
    const form = new FormData();
    form.append('id', id);

    const res = await fetch('toggle_alumno.php', {
        method: 'POST',
        body: form
    });

    const data = await res.json();
    if (data.success) location.reload();
    else alert('Error');
}

</script>

</body>
</html>
