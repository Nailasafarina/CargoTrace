# CargoTrace Production Deployment Guide

This guide provides step-by-step instructions for deploying **CargoTrace** in production environments using either **Docker Compose** or a **Traditional Linux VPS (Nginx + PHP 8.3-FPM + MySQL 8)**.

---

## Method 1: Containerized Deployment via Docker Compose (Recommended)

Docker Compose encapsulates PHP 8.3-FPM, Nginx, MySQL 8.0, and Redis into an isolated network.

### Prerequisites
- Docker Engine &ge; 24.0
- Docker Compose &ge; 2.20

### Deployment Steps
1. **Clone the repository onto the server**:
   ```bash
   git clone https://github.com/your-username/cargotrace.git /opt/cargotrace
   cd /opt/cargotrace
   ```

2. **Prepare Environment File**:
   ```bash
   cp .env.example .env
   ```
   Generate the application key locally or inside the container:
   ```bash
   docker compose run --rm app php artisan key:generate
   ```

3. **Build and Launch Containers in Background**:
   ```bash
   docker compose up -d --build
   ```

4. **Execute Database Migrations and Production Seeders**:
   ```bash
   docker compose exec app php artisan migrate --force --seed
   ```

5. **Warm Up Application Caches**:
   ```bash
   docker compose exec app php artisan config:cache
   docker compose exec app php artisan route:cache
   docker compose exec app php artisan view:cache
   docker compose exec app php artisan storage:link
   ```

6. **Verify Running Services**:
   ```bash
   docker compose ps
   ```
   Access the application at `http://<SERVER_IP>:8080`.

---

## Method 2: Traditional Linux VPS Deployment (Ubuntu 22.04 / 24.04 LTS)

### Prerequisites
- Ubuntu 22.04 LTS or 24.04 LTS
- PHP 8.3 with extensions: `php8.3-fpm`, `php8.3-mysql`, `php8.3-mbstring`, `php8.3-xml`, `php8.3-bcmath`, `php8.3-curl`, `php8.3-intl`, `php8.3-zip`, `php8.3-gd`, `php8.3-opcache`
- Nginx &ge; 1.22
- MySQL Server 8.0+
- Node.js &ge; 20.x & npm
- Composer &ge; 2.6

---

### Step 1: Clone Repository & Set Permissions
```bash
sudo git clone https://github.com/your-username/cargotrace.git /var/www/cargotrace
cd /var/www/cargotrace

# Assign web server ownership
sudo chown -R www-data:www-data /var/www/cargotrace
sudo chmod -R 775 /var/www/cargotrace/storage /var/www/cargotrace/bootstrap/cache
```

---

### Step 2: Install Composer Dependencies (Production Mode)
```bash
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
```

---

### Step 3: Compile Frontend Assets
```bash
npm ci
npm run build
```

---

### Step 4: Configure Production Environment (`.env`)
```bash
cp .env.example .env
php artisan key:generate
```

Edit `/var/www/cargotrace/.env` with production configurations:
```ini
APP_NAME=CargoTrace
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cargotrace.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cargotrace
DB_USERNAME=cargotrace_prod_user
DB_PASSWORD="STRONG_DB_PASSWORD"

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

### Step 5: Database Migration & Seeding
```bash
php artisan migrate --force --seed
php artisan storage:link
```

---

### Step 6: Optimize Laravel Framework
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Step 7: Nginx Virtual Host Configuration
Create `/etc/nginx/sites-available/cargotrace`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name cargotrace.yourdomain.com;
    root /var/www/cargotrace/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml application/json;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable virtual host and reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/cargotrace /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### Step 8: SSL / TLS Certificate (Let's Encrypt Certbot)
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d cargotrace.yourdomain.com
```

---

### Step 9: Configure Task Scheduling & Automation
Add Laravel's cron job to execute scheduled commands (such as `shipments:evaluate-delays`):
```bash
sudo crontab -u www-data -e
```
Add the following line:
```cron
* * * * * cd /var/www/cargotrace && php artisan schedule:run >> /dev/null 2>&1
```

---

## Production Verification Checklist
- [x] `APP_ENV=production` and `APP_DEBUG=false` confirmed.
- [x] Application key generated (`php artisan key:generate`).
- [x] Storage symlink created (`php artisan storage:link`).
- [x] Storage and cache writable by `www-data` (`chmod -R 775`).
- [x] Configurations and routes cached (`config:cache`, `route:cache`).
- [x] Cron scheduler active (`php artisan schedule:run`).
