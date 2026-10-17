#!/bin/sh
set -e

# Ensure the web server can write to storage and cache directories
# (they live on a host-owned bind mount). Non-fatal so the script also
# works when the container is started as a non-root user (e.g. composer).
chmod -R a+rwX storage bootstrap/cache 2>/dev/null || true

exec "$@"
