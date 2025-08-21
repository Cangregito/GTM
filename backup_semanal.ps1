# Script PowerShell para backup automático semanal
# Guardar como: backup_semanal.ps1

param(
    [string]$Descripcion = "Backup automatico semanal"
)

$fecha = Get-Date -Format "yyyyMMdd_HHmmss"
$carpetaBackup = "backups"

# Crear carpetas si no existen
if (!(Test-Path "$carpetaBackup\database")) { New-Item -ItemType Directory -Path "$carpetaBackup\database" -Force }
if (!(Test-Path "$carpetaBackup\files")) { New-Item -ItemType Directory -Path "$carpetaBackup\files" -Force }

Write-Host "🔄 Iniciando backup semanal..." -ForegroundColor Yellow

# Backup de base de datos
Write-Host "📊 Creando backup de base de datos..." -ForegroundColor Green
$sqlFile = "$carpetaBackup\database\backup_semanal_$fecha.sql"
& "C:\xampp\mysql\bin\mysqldump.exe" -u root gtm_db | Out-File -FilePath $sqlFile -Encoding UTF8

# Backup de archivos si existen
if (Test-Path "public\uploads\evidencia\*") {
    Write-Host "📁 Creando backup de archivos..." -ForegroundColor Green
    $zipFile = "$carpetaBackup\files\backup_evidencias_semanal_$fecha.zip"
    Compress-Archive -Path "public\uploads\evidencia\*" -DestinationPath $zipFile -Force
}

# Limpiar backups antiguos (más de 60 días para semanales)
Write-Host "🧹 Limpiando backups antiguos..." -ForegroundColor Yellow
$fechaLimite = (Get-Date).AddDays(-60)
Get-ChildItem "$carpetaBackup\database\backup_semanal_*.sql" | Where-Object { $_.LastWriteTime -lt $fechaLimite } | Remove-Item -Force
Get-ChildItem "$carpetaBackup\files\backup_evidencias_semanal_*.zip" | Where-Object { $_.LastWriteTime -lt $fechaLimite } | Remove-Item -Force

# Crear log del backup
$logContent = @"
# Backup Semanal - $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")

**Descripción:** $Descripcion
**Archivos creados:**
- Base de datos: $sqlFile
$(if (Test-Path "backups\files\backup_evidencias_semanal_$fecha.zip") { "- Archivos: backups\files\backup_evidencias_semanal_$fecha.zip" })

**Estado:** ✅ Completado exitosamente
"@

$logContent | Out-File -FilePath "$carpetaBackup\ultimo_backup_semanal.log" -Encoding UTF8

Write-Host "✅ Backup semanal completado: $fecha" -ForegroundColor Green
Write-Host "📁 Archivos guardados en: $carpetaBackup" -ForegroundColor Cyan
