<?php
// config.php
// PUENTE ENTRE PHP (Frontend) Y NODE.JS (API)

// --- URL BASE DE LA API (Railway) ---
define('API_URL', 'https://api-alumnos-production-cdcc.up.railway.app/api');

// --- FUNCIÓN CENTRAL PARA CONSUMIR LA API ---
function pedir_api(string $endpoint, string $metodo = 'GET', array $datos = [])
{
    $url = rtrim(API_URL, '/') . '/' . ltrim($endpoint, '/');

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CUSTOMREQUEST => $metodo,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]);

    // Enviar cuerpo solo si NO es GET
    if ($metodo !== 'GET' && !empty($datos)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
    }

    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($respuesta === false) {
        curl_close($ch);
        return null; // API no disponible
    }

    curl_close($ch);

    // Respuesta válida pero con error HTTP
    if ($http_code < 200 || $http_code >= 300) {
        return [
            'error' => 'Error API',
            'status' => $http_code,
            'response' => json_decode($respuesta, true)
        ];
    }

    return json_decode($respuesta, true);
}
