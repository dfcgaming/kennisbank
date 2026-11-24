# Kennisbank

Laravel 13 on PHP 8.4 with PostgreSQL 17, Redis 7.4, and an optional Cloudflare Tunnel.

## Run with Docker Compose

1. Run `docker compose up -d --build`. The setup service generates the Laravel key and the PostgreSQL and Redis passwords on first startup. The app then runs its migrations.
2. Open [localhost:8000](http://localhost:8000), or check [the health endpoint](http://localhost:8000/up).

Copy `.env.example` to `.env` if you want to customize the stack. The app binds to the host's loopback interface. Use `APP_PORT` to change the port, and update `APP_URL` to match. Compose places PostgreSQL and Redis on an internal network and publishes neither port.

The setup service creates a private `runtime.env` in a named volume. Laravel reads it at startup, and PostgreSQL and Redis each get their own credential file. Back up the credential volume alongside the data volumes.

To supply your own credentials, set `APP_KEY`, `DB_PASSWORD`, and `REDIS_PASSWORD` before the first startup. If upgrading a deployment that already initialized PostgreSQL, supply its existing Laravel key and PostgreSQL password, and retain the existing Redis password if you set one. The setup service saves those values for subsequent deploys.

## Cloudflare Tunnel

Create a remotely managed tunnel and add a published application route with hostname `app.example.com` and origin `http://app:80`. Use the tunnel's connector token in the stack environment:

```dotenv
COMPOSE_PROFILES=cloudflare
CLOUDFLARE_TUNNEL_TOKEN=
APP_URL=https://app.example.com
SESSION_SECURE_COOKIE=true
```

Fill in the token, then run `docker compose up -d --build` again. Check the connector with `docker compose logs cloudflared` and open [app.example.com](https://app.example.com). Cloudflare connects to the app on the Compose network, so the route uses container port 80 even when the loopback port differs.

## Deploy with Coolify or Dokploy

In Coolify, create a Git-backed application with the **Docker Compose** build pack. In Dokploy, create a Git-backed **Docker Compose** service. In either platform, select `docker-compose.yml` from the repository. For a fresh deployment, leave the Laravel key and passwords unset. If using Cloudflare, set the profile and connector token in the platform's environment settings.

The app listens on container port 80. Use that port if routing through the platform's proxy. For multiple deployments on the same host, assign each a different `APP_PORT`, or set `APP_PORT=0` for a Docker-assigned loopback port.
