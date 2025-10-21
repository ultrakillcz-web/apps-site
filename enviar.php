<?php
require 'PHPMailerAutoload.php';

// Simple .env loader (no external dependency)
function loadEnv($file)
{
    if (!is_file($file)) return;
    $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) return;
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        if (strlen($val) >= 2 && (($val[0] === '"' && substr($val, -1) === '"') || ($val[0] === "'" && substr($val, -1) === "'"))) {
            $val = substr($val, 1, -1);
        }
        if ($key !== '') {
            putenv($key . '=' . $val);
            $_ENV[$key] = $val;
        }
    }
}

function env($key, $default = null) {
    $v = getenv($key);
    return ($v === false || $v === null || $v === '') ? $default : $v;
}

// Load .env from project root
loadEnv(__DIR__ . DIRECTORY_SEPARATOR . '.env');

// Dev-only mode: prevent real sends when developing locally
// Enabled if any of the following are true:
// - APP_ENV=development
// - Running with PHP built-in server (cli-server)
// - Query flag dev=1 coming from localhost
$remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$devMode = (env('APP_ENV') === 'development')
    || (PHP_SAPI === 'cli-server')
    || ((isset($_GET['dev']) || isset($_POST['dev'])) && in_array($remote, ['127.0.0.1','::1']));

// PHPMailer 5.2 via SMTP (env-driven with safe defaults)
$mail = new PHPMailer;
$mail->isSMTP();
$mail->SMTPDebug = (int) env('SMTP_DEBUG', '0');
$mail->Host     = env('SMTP_HOST', 'smtp.smallsites.com.br');
$mail->SMTPAuth = filter_var(env('SMTP_AUTH', 'true'), FILTER_VALIDATE_BOOLEAN);
$secure         = env('SMTP_SECURE', ''); // '', 'tls' or 'ssl'
if ($secure) { $mail->SMTPSecure = $secure; }
$mail->Port     = (int) env('SMTP_PORT', '587');
$mail->Username = env('SMTP_USERNAME', 'contato@smallsites.com.br');
$mail->Password = env('SMTP_PASSWORD', 'Small@123');

$fromEmail = env('SMTP_FROM', $mail->Username);
$fromName  = isset($_POST['nome']) ? $_POST['nome'] : env('SMTP_FROM_NAME', 'Contato');
$mail->From     = $fromEmail; // For PHPMailer 5.x, assign From directly
$mail->FromName = $fromName;

// Recipients (comma-separated)
$toList = env('SMTP_TO', 'mateus@agenciasmall.com.br');
foreach (explode(',', $toList) as $addr) {
    $addr = trim($addr);
    if ($addr !== '') $mail->addAddress($addr);
}

if (!empty($_POST['email'])) { $mail->addReplyTo($_POST['email']); }

$mail->isHTML(true);
$mail->Subject = isset($_POST['assunto']) ? $_POST['assunto'] : '';
$mail->Body    = isset($_POST['corpo']) ? $_POST['corpo'] : '';

// Dev-mode: log payload and simulate success (no real email is sent)
if ($devMode) {
    $logDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'dev';
    if (!is_dir($logDir)) { @mkdir($logDir, 0777, true); }
    $logFile = $logDir . DIRECTORY_SEPARATOR . 'mail.log';
    $payload  = "==== DEV MODE - Simulated email ====" . PHP_EOL;
    $payload .= 'Date: ' . date('c') . PHP_EOL;
    $payload .= 'FromEmail: ' . $fromEmail . PHP_EOL;
    $payload .= 'FromName: ' . $fromName . PHP_EOL;
    $payload .= 'ReplyTo: ' . ($_POST['email'] ?? '') . PHP_EOL;
    $payload .= 'To: ' . $toList . PHP_EOL;
    $payload .= 'Assunto: ' . ($_POST['assunto'] ?? '') . PHP_EOL;
    $payload .= 'Corpo: ' . str_replace(["\r","\n"], [' ',' '], ($_POST['corpo'] ?? '')) . PHP_EOL . PHP_EOL;
    @file_put_contents($logFile, $payload, FILE_APPEND);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'DEV MODE: mensagem simulada como enviada';
    exit;
}

// Production: send
if (!$mail->send()) {
    echo 'Mensagem nao enviada, por favor tente novamente mais tarde';
    echo ' | Mailer Error_log: ' . $mail->ErrorInfo;
} else {
    echo "<script>window.location='index.html';alert('Mensagem enviada com sucesso! Obrigado pelo contato.');</script>";
}

?>
