<?php
// config.php - EL PUENTE ENTRE PHP Y NODE.JS

// Definimos dónde está escuchando el Chef (Node.js)
define('API_URL', 'http://localhost:3000/api');

// Función maestra para hacer pedidos (GET, POST, PUT, PATCH)
function pedir_api($endpoint, $metodo = 'GET', $datos = []) {
    // Iniciamos la llamada (cURL)
    $ch = curl_init(API_URL . $endpoint);
    
    // Configuración básica
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Si no es GET, configuramos los datos a enviar
    if ($metodo !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
    }
    
    // Ejecutar y cerrar
    $respuesta = curl_exec($ch);
    
    // Verificar si Node.js está apagado
    if ($respuesta === false) {
        die('Error: No se pudo conectar con la API (Node.js). ¿Está encendida la terminal negra?');
    }
    
    curl_close($ch);
    
    // Devolver los datos como Array de PHP
    return json_decode($respuesta, true);
}
?>