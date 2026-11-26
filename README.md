# Kennisbank

Laravel 13 on PHP 8.4 with PostgreSQL 17, Redis 7.4, and an optional Cloudflare Tunnel.

## Run with Docker Compose

1. Copy `.env.example` to `.env`.
2. Set `APP_KEY` to `base64:` followed by the output of `openssl rand -base64 32`. Set `DB_PASSWORD` and `REDIS_PASSWORD` to separate random values, for example from two runs of `openssl rand -hex 32`.
3. Run `docker compose up -d --build`. The app runs its migrations on startup.
4. Open [localhost:8000](http://localhost:8000), or check [the health endpoint](http://localhost:8000/up).

The app binds to the host's loopback interface. Use `APP_PORT` to change the port, and update `APP_URL` to match. Compose places PostgreSQL and Redis on an internal network and publishes neither port. Named volumes persist database data, Redis data, and Laravel application storage. Keep the same `APP_KEY` between deployments.

## Cloudflare Tunnel

Create a remotely managed tunnel and add a published application route with hostname `app.example.com` and origin `http://app:80`. Use the tunnel's connector token in the stack environment:

```dotenv
COMPOSE_PROFILES=cloudflare
CLOUDFLARE_TUNNEL_TOKEN=
APP_URL=https://app.example.com
SESSION_SECURE_COOKIE=true
```

Fill in the token, then run `docker compose up -d --build` again. Check the connector with `docker compose logs cloudflared` and open [app.example.com](https://app.example.com). Cloudflare connects to the app on the Compose network, so the route uses container port 80 even when the loopback port differs.

## Deploy with Coolify

Create a Git-backed **Docker Compose** application and select `docker-compose.yml`. Assign your domain to the `app` service on container port `80`. Leave `APP_PORT` unset so Docker chooses a free loopback port while Coolify routes traffic to the container.

Coolify generates and stores the Laravel application key and separate PostgreSQL and Redis passwords. For an existing installation, keep its `APP_KEY`, `DB_PASSWORD`, and `REDIS_PASSWORD` in Coolify and enable them for both build and runtime so the stack continues to use those values.

The app defaults to the URL supplied by Coolify. For an HTTPS custom domain, set `APP_URL` to the public HTTPS URL and `SESSION_SECURE_COOKIE=true`.

## Deploy with Coolify and Railpack

1. Create a Git-backed application with **Railpack** as its build strategy, `/` as its base directory, and `80` as its exposed container port. Configure the domain and use `/up` for the HTTP health check.
2. Start private PostgreSQL 17 and Redis 7.4 resources on the same Coolify destination. Add their internal URLs as `DB_URL` and `REDIS_URL` in the application's runtime environment.
3. Set a persistent runtime `APP_KEY` using `base64:` followed by `openssl rand -base64 32` output. Reuse the existing key when moving a deployment. Set `APP_URL` to the public URL and enable `SESSION_SECURE_COOKIE=true` for HTTPS.
4. Mount persistent storage at `/app/storage/app`. Restore any existing database or application files before moving traffic. Deploy and verify `/up`.

Railpack builds the Laravel app and Vite assets and runs migrations on startup. Keep the application key and database URLs out of build-time variables.

## Deploy with Dokploy

Create a Git-backed **Docker Compose** service and set the Compose path to `docker-compose.yml`. Set the values above in the service's **Environment** tab, including the required app key and passwords. If using Cloudflare, add the profile and connector token there, then deploy. If Dokploy's host already uses port 8000, choose a free loopback port with `APP_PORT`.
