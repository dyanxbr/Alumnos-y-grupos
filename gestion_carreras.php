<?php
session_start();

/* ===============================
   CONFIGURACIÓN API
================================ */
define('API_URL', 'https://api-alumnos-production-cdcc.up.railway.app/api');

/* ===============================
   FUNCIÓN PARA PEDIR A LA API
================================ */
function pedir_api($endpoint) {
    $ch = curl_init(API_URL . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}

/* ===============================
   OBTENER CARRERAS (TODAS)
================================ */
$lista_carreras = pedir_api('/carreras-todas');
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
        :root {
            --bg-canvas: #f4f4f0;
            --navy-solid: #0f172a;
            --navy-light: #334155;
            --white: #ffffff;
            --border-soft: rgba(15, 23, 42, 0.08);
        }

        body {
            background-color: var(--bg-canvas);
            color: var(--navy-solid);
            font-family: 'Inter', sans-serif;
        }

        .serif-title { font-family: 'Playfair Display', serif; }

        .app-header {
            padding: 2.5rem 0;
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
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-weight: bold;
        }

        .table-card {
            background: var(--white);
            border: 1px solid var(--border-soft);
            padding: 2rem;
        }

        .custom-table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--navy-light);
            border-bottom: 2px solid var(--navy-solid);
        }

        .custom-table td {
            vertical-align: middle;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border-soft);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .bg-active { background-color: #dcfce7; color: #15803d; }
        .bg-inactive { background-color: #fee2e2; color: #b91c1c; }

        .btn-toggle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn-on { background: #e0f2fe; color: #0284c7; }
        .btn-off { background: #f1f5f9; color: #94a3b8; }

        .sidebar-card {
            background: var(--white);
            padding: 1.5rem;
            border: 1px solid var(--border-soft);
        }
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
                <div class="d-flex justify-content-between mb-4">
                    <h4 class="serif-title mb-0">Oferta Académica</h4>
                    <span class="badge bg-dark rounded-0">Gestión de Carreras</span>
                </div>

                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Siglas</th>
                                <th>Nombre</th>
                                <th>Estatus</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($lista_carreras)): ?>
                            <?php foreach ($lista_carreras as $c):
                                $activo = ($c['estatus'] == 1);
                            ?>
                                <tr>
                                    <td class="text-muted">#<?= $c['id_carrera'] ?></td>
                                    <td class="fw-bold"><?= $c['siglas'] ?></td>
                                    <td><?= $c['nombre_carrera'] ?></td>
                                    <td>
                                        <span class="status-badge <?= $activo ? 'bg-active' : 'bg-inactive' ?>">
                                            <?= $activo ? 'Visible' : 'Oculta' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button onclick="toggleCarrera(<?= $c['id_carrera'] ?>)"
                                                class="btn-toggle <?= $activo ? 'btn-on' : 'btn-off' ?>">
                                            <i class="fas <?= $activo ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    No hay carreras registradas o error de conexión.
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
                <h6 class="text-uppercase small fw-bold text-muted mb-3">Información</h6>
                <ul class="small text-muted ps-3">
                    <li>Las carreras ocultas no aparecen en registros.</li>
                    <li>No se elimina información histórica.</li>
                </ul>
                <a href="index.php" class="btn btn-outline-dark w-100 mt-3 rounded-0">
                    Volver al Inicio
                </a>
            </div>
        </div>

    </div>
</main>


<script>
async function toggleCarrera(id) {
    try {
        const res = await fetch(
            `https://api-alumnos-production-cdcc.up.railway.app/api/carreras/${id}/estatus`,
            { method: 'PATCH' }
        );

        const data = await res.json();

        if (data.success) {
            location.reload();
        } else {
            alert('No se pudo actualizar el estatus');
        }
    } catch (error) {
        alert('Error de conexión con la API');
    }
}
</script>

</body>
</html>
