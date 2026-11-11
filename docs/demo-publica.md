# Demo pública de ClientManager

## Demo incluida en el despliegue habitual

El login enlaza a `/demo-publica/index.html`. Las páginas de esa carpeta contienen únicamente datos ficticios y se sirven como archivos estáticos, sin sesión ni base de datos. Incluyen listas paginadas, fichas, la vista de clientes asignados y reportes con gráficas y búsqueda de asesores. Los filtros de período y sucursal conservan sus controles visuales, sobre un conjunto fijo de datos de ejemplo.

La carpeta queda publicada al desplegar esta versión. No necesita nuevas variables de entorno. Para regenerarla desde la instancia local ficticia en el puerto 8766, ejecuta `python3 tools/export_public_demo.py` después del build. El exportador requiere Beautiful Soup (`beautifulsoup4`) y comprueba que las páginas proceden del modo de demostración. El servidor publicado utiliza los HTML ya generados.

## Instancia dinámica opcional

Publica la demo como una instancia independiente. La página de acceso presenta «Explorar demo» y abre una sesión de visita sin pedir credenciales. Los cambios de clientes, el registro, la recuperación de contraseña y el perfil quedan fuera de la demo.

## Preparar la base ficticia

Crea un esquema MySQL vacío llamado `clientmanager_public_demo`. Con una cuenta de instalación que tenga acceso a ese esquema, configura `CLIENTMANAGER_DEMO=true` junto con `DEMO_DB_HOST`, `DEMO_DB_PORT`, `DEMO_DB_USERNAME` y `DEMO_DB_PASSWORD`. Para un socket local puedes usar `DEMO_DB_SOCKET`.

```bash
php artisan migrate --force
php artisan db:seed --class=PublicDemoSeeder --force
npm ci
npm run build
```

Para servir la demo, cambia a una cuenta MySQL con permiso **SELECT** sobre ese esquema. Configura un `APP_KEY` propio, `APP_URL` con el dominio de la demo y `SESSION_COOKIE=clientmanager_public_demo_session`. Usa `SESSION_DRIVER=file` y `CACHE_STORE=file` con almacenamiento escribible, o un almacén de sesiones y caché dedicado. Configura el servidor web para servir `public/` mediante HTTPS, con `APP_DEBUG=false` y `SESSION_SECURE_COOKIE=true`. Comparte la raíz de ese dominio.

En la instancia habitual, deja `CLIENTMANAGER_DEMO` desactivado. Si la instalación usa caché de configuración, reconstruye la caché con las variables de cada instancia.
