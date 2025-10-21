Param(
  [int]$Port = 8000
)

$env:PORT = $Port
Write-Host "Starting UTF-8 server on http://127.0.0.1:$Port" -ForegroundColor Green
python .\serve.py

