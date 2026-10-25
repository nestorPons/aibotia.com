<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

const CACHE_FILE  = __DIR__ . '/data_cache.json';
const REMOTE_JSON = 'https://www.servientradas.com/ERP/3.1/empresas/003/001/plantillas/eventos_58.json';

// Si llega un parámetro link-html, forzamos la lectura remota de ese enlace
if (isset($_GET['link-html'])) {
    $url = urldecode($_GET['link-html']);
    $response = @file_get_contents($url);
} else {
    // Intentar servir desde el caché local
    if (file_exists(CACHE_FILE) && filesize(CACHE_FILE) > 0) {
        $response = @file_get_contents(CACHE_FILE);
    } else {
        // Si no existe o está vacío, obtener desde remoto
        $response = @file_get_contents(REMOTE_JSON);

        // Si se pudo obtener, guardar en caché para próximas veces
        if ($response !== false && strlen($response) > 0) {
            file_put_contents(CACHE_FILE, $response);
        }
    }
}

// Si sigue sin respuesta, devolver error
if ($response === false || strlen(trim($response)) === 0) {
    http_response_code(502);
    echo json_encode(["error" => "No se pudo obtener el JSON remoto ni el caché local."]);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");
echo $response;
