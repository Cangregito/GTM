@echo off
REM Script para crear backups automáticos del sistema GTM
REM Usar: backup.bat [opcional: descripcion]

set FECHA=%date:~6,4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set FECHA=%FECHA: =0%

REM Crear carpetas si no existen
if not exist "backups\database" mkdir "backups\database"
if not exist "backups\files" mkdir "backups\files"

REM Backup de base de datos
echo Creando backup de base de datos...
C:\xampp\mysql\bin\mysqldump.exe -u root gtm_db > "backups\database\backup_gtm_db_%FECHA%.sql"

REM Backup de archivos de evidencias (si existen)
if exist "public\uploads\evidencia\*.*" (
    echo Creando backup de archivos...
    powershell -Command "Compress-Archive -Path 'public\uploads\evidencia\*' -DestinationPath 'backups\files\backup_evidencias_%FECHA%.zip' -Force"
)

REM Limpiar backups antiguos (más de 30 días)
echo Limpiando backups antiguos...
forfiles /p "backups\database" /s /m backup_*.sql /d -30 /c "cmd /c del @path" 2>nul
forfiles /p "backups\files" /s /m backup_*.zip /d -30 /c "cmd /c del @path" 2>nul

echo.
echo ✅ Backup completado: %FECHA%
echo 📁 Base de datos: backups\database\backup_gtm_db_%FECHA%.sql
if exist "backups\files\backup_evidencias_%FECHA%.zip" echo 📁 Archivos: backups\files\backup_evidencias_%FECHA%.zip
echo.
pause
