# AppsGroup Website

Static site for AppsGroup with a contact form powered by PHPMailer.

- Development setup and details: see `DEVELOPMENT.md`
- Local static preview: `./serve.ps1 -Port 8000` (PowerShell) or `python ./serve.py`
- Contact form (safe dev mode): use PHP built-in server with `APP_ENV=development` or `?dev=1` from localhost

## Quick Start
- Python: `python -m http.server 8000` then open `http://127.0.0.1:8000`
- UTF‑8 server (recommended): `./serve.ps1 -Port 8000`
- PHP dev mode (no email sent):
  - PowerShell: `Set-Item Env:APP_ENV development; php -S 127.0.0.1:8000`
  - Open `http://127.0.0.1:8000/index.html` and submit the form

For full instructions, environment variables, and production server notes (Apache/Nginx), read `DEVELOPMENT.md`.
