## Development Guide

This project is configured for UTF‑8 everywhere and provides local dev tools to preview the site and test the contact form safely.

### Prerequisites
- Python 3.9+ (for local static server)
- Optional: PHP 7.4+ (to run `enviar.php` locally)

### Editor Settings
- VS Code auto‑configured via `.vscode/settings.json`:
  - `files.encoding = utf8`
  - `files.autoGuessEncoding = false`
  - `files.eol = \n`
- Additional baseline in `.editorconfig` enforces UTF‑8 + LF.

### Running Locally (Static)
- PowerShell: `./serve.ps1 -Port 8000`
- Or: `python ./serve.py`
- Open: `http://127.0.0.1:8000`

The custom server sets `Content-Type` to `text/html; charset=utf-8` ensuring correct accents.

### Running Locally (PHP form)
- Create a `.env` from the provided `.env.example` and fill SMTP values if you want to actually send emails.
- Dev‑only safe testing (no email sent, logs only):
  - Dev mode activates when any of these is true:
    - `APP_ENV=development`
    - PHP built‑in server (`php -S 127.0.0.1:8000`)
    - Request from localhost with `?dev=1`
- Commands (PowerShell):
  - `Set-Item Env:APP_ENV development`
  - `php -S 127.0.0.1:8000`
  - Open `http://127.0.0.1:8000/index.html` and submit the form
  - Check `assets/dev/mail.log` for the simulated payload

### Apache Configuration
- `.htaccess` in the project root enforces UTF‑8 for `.html`/`.htm` and default charset.
- Ensure your virtual host allows overrides: `AllowOverride All`.

### Nginx Configuration
A sample server block is provided in `docs/nginx.conf.example`. Key points:
- Serves UTF‑8 by default (`charset utf-8;`)
- Sets correct `Content-Type` for HTML
- Optionally forwards PHP to a FastCGI upstream (uncomment if needed)

### SMTP via Environment
`enviar.php` reads SMTP settings from environment:
- `SMTP_HOST`, `SMTP_PORT`, `SMTP_SECURE`, `SMTP_AUTH` (true/false)
- `SMTP_USERNAME`, `SMTP_PASSWORD`
- `SMTP_FROM`, `SMTP_FROM_NAME`, `SMTP_TO` (comma-separated)
- `SMTP_DEBUG`

For security, never commit `.env`; it’s ignored by `.gitignore`.

### Troubleshooting
- Garbled accents in terminal: that’s a console rendering issue. Browser renders correctly under UTF‑8.
- If Python or PHP are not found, ensure they’re on your PATH.
- SMTP errors: set `SMTP_DEBUG=2` temporarily in your `.env` and check the server logs.

