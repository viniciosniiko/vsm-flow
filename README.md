# VSM Flow Starter

Starter Laravel para criação visual de fluxos de chatbot com simulador estilo WhatsApp.

## Como criar o projeto Laravel base

No Laragon:

```bash
cd C:/laragon/www
composer create-project laravel/laravel vsm-flow
cd vsm-flow
```

Copie os arquivos deste starter por cima do projeto Laravel criado.

Depois rode:

```bash
php artisan migrate
php artisan serve
```

Acesse:

```text
http://localhost:8000
```

## Railway

Crie um projeto separado do VSM Academy no Railway.

Variáveis principais:

```env
APP_NAME="VSM Flow"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://SEU-PROJETO.up.railway.app
DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Comandos Railway:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
