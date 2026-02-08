<?php
define('API_URL', 'https://api-alumnos-production-cdcc.up.railway.app/api');

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success'=>false]);
    exit;
}

$ch = curl_init(API_URL."/alumnos/$id/estatus");
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => "PATCH",
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response ?: json_encode(['success'=>false]);
