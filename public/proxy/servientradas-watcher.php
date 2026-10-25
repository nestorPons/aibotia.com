<?php
/**
 * Servientradas Watcher (para cron en Hostinger)
 * Comprueba si ha cambiado el JSON remoto de Servientradas y lanza actualización en Palau.
 * Además, guarda el JSON localmente para servirlo sin pedirlo cada vez.
 */

// === CONFIGURACIÓN ===
const REMOTE_JSON = 'https://www.servientradas.com/ERP/3.1/empresas/003/001/plantillas/eventos_58.json';
const HASH_FILE   = __DIR__ . '/last_hash.txt';
const CACHE_FILE  = __DIR__ . '/data_cache.json';
const HOOK_URL    = 'https://palauvalencia.com/wp-content/plugins/servientradas-display-events/servientradas-update-events.php?key=palausecret123';
const LOG_FILE    = __DIR__ . '/watcher.log';

// === FUNCIONES ===
function log_msg($msg) {
    $time = date('[Y-m-d H:i:s]');
    file_put_contents(LOG_FILE, "$time $msg\n", FILE_APPEND);
}

// === INICIO ===
log_msg("Iniciando comprobación...");

// Obtener el JSON remoto
$json = @file_get_contents(REMOTE_JSON);
if ($json === false) {
    log_msg("❌ Error: No se pudo obtener el JSON remoto");
    exit;
}

// Calcular hash actual
$current_hash = hash('sha256', $json);

// Leer hash anterior
$last_hash = file_exists(HASH_FILE) ? trim(file_get_contents(HASH_FILE)) : null;

// Si es la primera vez, guardar hash y JSON
if ($last_hash === null) {
    file_put_contents(HASH_FILE, $current_hash);
    file_put_contents(CACHE_FILE, $json);
    log_msg("Primera ejecución. Hash y JSON guardados.");
    exit;
}

// Si el hash cambió, guardar nuevo JSON y lanzar actualización
if ($current_hash !== $last_hash) {
    log_msg("🔁 Cambio detectado. Guardando JSON y lanzando actualización...");
    file_put_contents(HASH_FILE, $current_hash);
    file_put_contents(CACHE_FILE, $json);

    // Llamar al endpoint de actualización del plugin
    $context = stream_context_create(['http' => ['timeout' => 15]]);
    $response = @file_get_contents(HOOK_URL, false, $context);

    if ($response === false) {
        log_msg("❌ Error al llamar al hook de actualización");
    } else {
        log_msg("✅ Hook ejecutado correctamente");
    }
} else {
    log_msg("Sin cambios detectados.");
}

log_msg("Comprobación completada.\n");
