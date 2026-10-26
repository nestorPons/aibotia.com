# Changelog

## 2026-10-26
- Unificado `docker-compose.yml` + `docker-compose.prod.yml` en un único `docker-compose.yml`.
- Eliminado contenedor `phpmyadmin` y su router Traefik (`pma.aibotia.com`).
- Eliminado `docker-compose.prod.yml`.
- Actualizado `.github/workflows/deploy.yml` a `docker compose up -d --build`.
- Actualizado `.dockerignore`.
- Corregida regla Traefik: `Host(\`aibotia.com\`) || Host(\`www.aibotia.com\`)` (Host solo admite 1 argumento).
- Producción `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://aibotia.com`, `APP_KEY` generada.
- Añadida migración con tablas `sessions` y `password_reset_tokens` (faltaban y causaban error 500).
- Recargado nginx tras recrear contenedores (caché DNS del upstream `app`).
- Landing (`welcome`): sustituidos emojis y Font Awesome por SVG inline mediante el componente `<x-icon>` (los iconos se veían como cuadrados cuando fallaba la fuente).
- Añadido componente `resources/views/components/aibot-icon.blade.php` (`<x-aibot-icon>`) para evitar la colisión con el componente `x-icon` de WireUI Heroicons (provocaba el error `components.outline.robot not found`).
- Eliminado el CDN de Font Awesome de `welcome.blade.php`.
- Certificado TLS de `aibotia.com` + `www.aibotia.com` emitido por Let's Encrypt tras propagarse el DNS y reiniciar Traefik (antes servía el certificado por defecto de Traefik).
- Desplegados manualmente en producción `welcome.blade.php` y `aibot-icon.blade.php`; verificados 10 SVG inline y respuesta HTTP 200.
- Verificado el certificado TLS: cadena completa hasta ISRG Root X1, SSL Labs nota A y carga correcta en Chromium/Firefox. El aviso "Not secure" restante es caché DNS/TLS del navegador del usuario.
- Creado en producción el usuario demo `demo@aibotia.com` (no admin, email verificado, equipo personal). Credenciales entregadas al usuario.
- `App\Models\User` implementa `FilamentUser` con `canAccessPanel()` (en producción Filament bloqueaba el panel con 403).
- `bootstrap/app.php`: añadido `trustProxies(at: '*')` para que Traefik transmita `X-Forwarded-Proto` y Filament genere assets HTTPS.
- `AppServiceProvider`: forzado `URL::forceScheme('https')` en producción (corregía `css/custom.css` en HTTP por registrarse antes de ejecutarse `TrustProxies`).
- `ApiService`: constructor tolerante a `API_BASE_URL` vacío y `try/catch` en las peticiones para no romper el panel si la API no está disponible.
- `qr-display`: corregido estado de conexión cuando la API no responde (antes mostraba "vinculado" por un valor string truthy).
- Verificado login demo end-to-end: `/admin/login` → `/admin/inicio` sin errores, sin recursos HTTP y con estilos/CSS cargados.
- Corregido error 500 post-deploy por `bootstrap/cache` no escribible (rsync lo cambiaba a uid 1001): excluido `bootstrap/cache/` del rsync y añadido `chown`+`optimize:clear` como `www-data` en el workflow.
