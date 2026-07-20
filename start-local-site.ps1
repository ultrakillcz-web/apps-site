# start-local-site.ps1
# Faz backup do CSS, verifica porta 8000, inicia servidor (PHP se existir, senao Python),
# abre navegador e exibe um resumo do status.

$root = (Resolve-Path (Split-Path -Parent $PSCommandPath)).ProviderPath
$cssRel = 'assets\css\switcher\switcher.css'
$cssPath = Join-Path -Path $root -ChildPath $cssRel

if (-not (Test-Path -Path $cssPath)) {
    Write-Error "Arquivo CSS nao encontrado em: $cssPath"
    exit 1
}

$ts = Get-Date -Format 'yyyyMMdd-HHmmss'
$bakPath = "$cssPath.$ts.bak"
Copy-Item -Path $cssPath -Destination $bakPath -Force
Write-Host "Backup criado em: $bakPath"

$port = 8000
$ownerPid = $null

try {
    $conn = Get-NetTCPConnection -LocalPort $port -ErrorAction SilentlyContinue
    if ($conn) {
        $ownerPid = $conn[0].OwningProcess
        Write-Host "Porta ${port}: em uso pelo PID $ownerPid"
        try {
            Get-Process -Id $ownerPid -ErrorAction Stop | Select-Object Id, ProcessName
        } catch {}

        $killAnswer = Read-Host "Deseja encerrar este processo? (S/N)"
        if ($killAnswer -match '^[sS]') {
            Stop-Process -Id $ownerPid -Force
            Start-Sleep -Seconds 1
            Write-Host "Processo $ownerPid terminado."
            $ownerPid = $null
        } else {
            Write-Host "Processo mantido. Saindo."
            exit 1
        }
    } else {
        Write-Host "Porta ${port}: livre"
    }
} catch {
    Write-Warning "Get-NetTCPConnection falhou; usando netstat como fallback."
    $ns = netstat -ano | findstr ":$port"
    if ($ns) {
        Write-Host "netstat output:`n$ns"
        $parts = $ns -split '\s+' | Where-Object { $_ -ne '' }
        $maybePid = $parts[-1]
        if ($maybePid -as [int]) {
            $ownerPid = [int]$maybePid
            Write-Host "Possivel PID: $ownerPid"
        }
    } else {
        Write-Host "Porta ${port}: livre (netstat nao retornou nada)"
    }
}

$phpCmd = Get-Command php -ErrorAction SilentlyContinue
$pythonCmd = Get-Command python -ErrorAction SilentlyContinue
$use = $null
$proc = $null

if ($phpCmd) {
    Write-Host "PHP encontrado em: $($phpCmd.Source). Usando PHP built-in server."
    $use = 'php'
} elseif ($pythonCmd) {
    Write-Host "PHP nao encontrado. Python encontrado em: $($pythonCmd.Source). Usando Python http.server."
    $use = 'python'
} else {
    Write-Error "Nem PHP nem Python encontrados no PATH. Instale um deles ou use Live Server no VSCode."
    exit 1
}

Push-Location -Path $root
try {
    if ($use -eq 'php') {
        $serverArgs = "-S localhost:$port -t `"$root`""
        Write-Host "Iniciando: php $serverArgs"
        $proc = Start-Process -FilePath 'php' -ArgumentList $serverArgs -WorkingDirectory $root -NoNewWindow -PassThru
    } else {
        $serverArgs = "-m http.server $port --bind 127.0.0.1"
        Write-Host "Iniciando: python $serverArgs"
        $proc = Start-Process -FilePath 'python' -ArgumentList $serverArgs -WorkingDirectory $root -NoNewWindow -PassThru
    }

    Start-Sleep -Seconds 1

    if ($proc -and $proc.Id) {
        Write-Host "Servidor iniciado. PID = $($proc.Id)"
    } else {
        Write-Warning "Nao foi possivel iniciar o processo do servidor (Start-Process nao retornou PID)."
    }

    $test = Test-NetConnection -ComputerName 'localhost' -Port $port -WarningAction SilentlyContinue
    if ($test) {
        Write-Host "Teste de conexao: TcpTestSucceeded = $($test.TcpTestSucceeded)"
    } else {
        Write-Warning "Test-NetConnection nao disponivel ou falhou."
    }

    $url = "http://localhost:$port/index.html"
    try {
        Start-Process $url
        Write-Host "Abrindo $url no navegador padrao..."
    } catch {
        Write-Warning "Falha ao abrir navegador automaticamente. Abra manualmente: $url"
    }

    Write-Host "`nResumo:"
    Write-Host "  Root: $root"
    Write-Host "  CSS backup: $bakPath"
    if ($proc -and $proc.Id) {
        Write-Host "  Server: $use (PID $($proc.Id))"
        Write-Host "  Para parar o servidor execute: Stop-Process -Id $($proc.Id) -Force"
    } else {+

