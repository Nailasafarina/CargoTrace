import { execSync, spawn } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const rootDir = process.cwd();
const hotFile = path.join(rootDir, 'public', 'hot');
const envFile = path.join(rootDir, '.env');
const envExampleFile = path.join(rootDir, '.env.example');
const sqliteFile = path.join(rootDir, 'database', 'database.sqlite');
const vendorAutoload = path.join(rootDir, 'vendor', 'autoload.php');
const manifestFile = path.join(rootDir, 'public', 'build', 'manifest.json');

if (fs.existsSync(hotFile)) {
    fs.unlinkSync(hotFile);
}

function resolvePhpBinary() {
    const candidates = [
        '/usr/local/bin/php',
        '/root/.config/herd-lite/bin/php',
        '/usr/bin/php',
    ];
    for (const candidate of candidates) {
        if (fs.existsSync(candidate)) {
            return candidate;
        }
    }
    try {
        return execSync('command -v php', { encoding: 'utf8' }).trim();
    } catch {
        return null;
    }
}

function ensurePhpAndComposer() {
    let phpBin = resolvePhpBinary();
    if (!phpBin) {
        console.log('[CargoTrace] Installing PHP 8.4 & Composer...');
        execSync('TERM=xterm /bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"', {
            stdio: 'inherit',
            cwd: rootDir,
        });
        if (fs.existsSync('/root/.config/herd-lite/bin/php')) {
            execSync('ln -sf /root/.config/herd-lite/bin/php /usr/local/bin/php');
        }
        if (fs.existsSync('/root/.config/herd-lite/bin/composer')) {
            execSync('ln -sf /root/.config/herd-lite/bin/composer /usr/local/bin/composer');
        }
        phpBin = resolvePhpBinary();
    }
    return phpBin || 'php';
}

function ensureEnvFile() {
    if (!fs.existsSync(envFile)) {
        const base = fs.existsSync(envExampleFile)
            ? fs.readFileSync(envExampleFile, 'utf8')
            : '';

        const lines = base
            .split(/\r?\n/)
            .filter((line) => line.trim() !== '' && !line.startsWith('DB_DATABASE='));

        const overrides = {
            APP_NAME: 'CargoTrace',
            APP_ENV: 'local',
            APP_DEBUG: 'true',
            APP_FAKER_LOCALE: 'en_US',
            APP_LOCALE: 'en',
            APP_FALLBACK_LOCALE: 'en',
            BCRYPT_ROUNDS: '4',
            DB_CONNECTION: 'sqlite',
            CACHE_STORE: 'database',
            SESSION_DRIVER: 'database',
            QUEUE_CONNECTION: 'database',
            LOG_CHANNEL: 'stack',
            LOG_STACK: 'single',
            BOOST_ENABLED: 'false',
        };

        const map = new Map();
        for (const line of lines) {
            const idx = line.indexOf('=');
            if (idx > 0) {
                const k = line.slice(0, idx).trim();
                const v = line.slice(idx + 1).trim();
                if (v !== '') {
                    map.set(k, v);
                }
            }
        }
        for (const [k, v] of Object.entries(overrides)) {
            if (!map.get(k)) {
                map.set(k, v);
            }
        }

        const out = Array.from(map.entries())
            .map(([k, v]) => `${k}=${v}`)
            .join('\n') + '\n';
        fs.writeFileSync(envFile, out, 'utf8');
    }
}

const phpBin = ensurePhpAndComposer();
ensureEnvFile();

if (!fs.existsSync(vendorAutoload)) {
    console.log('[CargoTrace] Installing PHP dependencies...');
    execSync('COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction', {
        stdio: 'inherit',
        cwd: rootDir,
    });
}

let envContents = fs.readFileSync(envFile, 'utf8');
if (!/^APP_KEY=/m.test(envContents)) {
    envContents += 'APP_KEY=\n';
    fs.writeFileSync(envFile, envContents, 'utf8');
}
if (!/^APP_KEY=base64:.+/m.test(envContents)) {
    execSync(`${phpBin} artisan key:generate --force`, {
        stdio: 'inherit',
        cwd: rootDir,
    });
}

if (!fs.existsSync(sqliteFile) || fs.statSync(sqliteFile).size === 0) {
    fs.mkdirSync(path.dirname(sqliteFile), { recursive: true });
    fs.closeSync(fs.openSync(sqliteFile, 'w'));
    execSync(`${phpBin} artisan migrate:fresh --seed --force`, {
        stdio: 'inherit',
        cwd: rootDir,
    });
}

if (!fs.existsSync(manifestFile)) {
    execSync('npx vite build', {
        stdio: 'inherit',
        cwd: rootDir,
    });
}

// Parse optional --port / --host flags safely
const args = process.argv.slice(2);
let port = process.env.PORT || '3000';
let host = process.env.HOST || '0.0.0.0';

for (let i = 0; i < args.length; i++) {
    const arg = args[i];
    if (arg.startsWith('--port=')) {
        port = arg.split('=')[1];
    } else if (arg === '--port' && args[i + 1]) {
        port = args[++i];
    } else if (arg.startsWith('--host=')) {
        host = arg.split('=')[1];
    } else if (arg === '--host' && args[i + 1]) {
        host = args[++i];
    }
}

const child = spawn(phpBin, ['artisan', 'serve', `--host=${host}`, `--port=${port}`], {
    stdio: 'inherit',
    cwd: rootDir,
    env: process.env,
});

const forwardSignal = (signal) => {
    if (!child.killed) {
        child.kill(signal);
    }
};

process.on('SIGINT', () => forwardSignal('SIGINT'));
process.on('SIGTERM', () => forwardSignal('SIGTERM'));

child.on('exit', (code) => {
    process.exit(code ?? 0);
});
