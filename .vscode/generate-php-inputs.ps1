# .vscode/generate-php-inputs.ps1
# Scans the workspace for .php files (excluding .git/node_modules/vendor/Obsoleto)
# and updates .vscode/launch.json input "phpScript" options.
try {
    $scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
    $workspaceRoot = Resolve-Path (Join-Path $scriptDir "..") | Select-Object -ExpandProperty Path
} catch {
    Write-Error "Cannot resolve workspace root. Run this script from .vscode folder."
    exit 1
}

$phpFiles = Get-ChildItem -Path $workspaceRoot -Recurse -Include *.php -ErrorAction SilentlyContinue |
    Where-Object { $_.FullName -notmatch '(\.git|node_modules|vendor|Obsoleto)' } |
    ForEach-Object {
        $rel = $_.FullName.Substring($workspaceRoot.Length+1).Replace('\','/')
        $rel
    } | Sort-Object -Unique

if (-not $phpFiles) {
    Write-Host "No .php files found under $workspaceRoot"
    exit 0
}

$launchPath = Join-Path $scriptDir "launch.json"
if (-not (Test-Path $launchPath)) {
    Write-Error "launch.json not found at $launchPath"
    exit 1
}

$jsonText = Get-Content $launchPath -Raw
$json = $null
try { $json = $jsonText | ConvertFrom-Json -ErrorAction Stop } catch { Write-Error "launch.json is not valid JSON"; exit 1 }

$options = @()
foreach ($f in $phpFiles) { $options += $f }

$found = $false
for ($i = 0; $i -lt $json.inputs.Count; $i++) {
    if ($json.inputs[$i].id -eq "phpScript") {
        $json.inputs[$i].options = $options
        $found = $true
        break
    }
}

if (-not $found) {
    $newInput = [pscustomobject]@{
        id = "phpScript"
        type = "pickString"
        description = "Select PHP script to launch with Xdebug (generated)"
        options = $options
    }
    $json.inputs += $newInput
}

$json | ConvertTo-Json -Depth 10 | Set-Content -Path $launchPath -Encoding UTF8
Write-Host "Updated launch.json with $($options.Count) php options."
