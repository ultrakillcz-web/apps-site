# Copilot instructions for this repository

Purpose
- Small static site + PHP contact endpoint. Front-end is static; enviar.php sends mail using PHPMailer.

Big picture (what to know first)
- Front-end: index.html + assets (CSS/JS/images).
- Contact flow: assets/js/form-contact.js posts form data to enviar.php.
- Mail: enviar.php depends on vendored PHPMailer files (class.phpmailer.php, class.smtp.php, PHPMailerAutoload.php and a copy under phpmailer).
- Obsoleto is archival — do not change for active work.

Key files to inspect
- index.html — page markup and form fields.
- assets/js/form-contact.js — client validation and POST payload format.
- enviar.php — SMTP host/port/auth, mail assembly, error handling.
- class.phpmailer.php, class.smtp.php, phpmailer/ — PHPMailer copies to keep in sync.
- assets/css/switcher/switcher.css — optional UI panel; `inset-inline-start: 0;` places it left (change to `inset-inline-end: 0;` + flip the toggle button if you prefer it on the right).

Project-specific conventions & gotchas
- No Composer/autoloader; PHPMailer is vendored multiple places — update every copy when upgrading.
- Credentials are often in enviar.php. Search for `Host`, `Username`, `Password` before committing.
- Asset paths are relative to repo root; preserve layout when deploying.
- No CI/tests included — manual local testing required.

Dev & debug commands (PowerShell)
- Quick local server: from repo root run:
  php -S 0.0.0.0:8000
  then open http://localhost:8000/index.html
- SMTP debug: set `$mail->SMTPDebug = 2;` in enviar.php to see SMTP conversation.
- Find duplicate PHPMailer files:
  Get-ChildItem -Recurse -Filter "class.phpmailer.php"

Security & deployment notes
- Do not commit real SMTP credentials. If credentials are found in repo history, rotate immediately.
- Exclude Obsoleto from production deploys.
- Before exposing enviar.php publicly, add server-side validation and rate limiting.

If you want the PT‑BR translation or other edits, reply with `aplica-pt`. If you prefer I only change CSS, reply `move-switcher`.
