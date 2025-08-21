# ⚠️ ANÁLISIS: Vaciar la tabla tm_ticket

## 🔍 Situación Actual

**Datos encontrados en tu sistema:**
- 📊 **63 tickets** en `tm_ticket`
- 📝 **33 detalles** en `td_ticketdetalle` 
- 📎 **6 evidencias** en `tm_evidencia`

## ❌ PROBLEMAS que OCURRIRÁN si vacías `tm_ticket`:

### 1. 🔗 **Integridad Referencial Rota**

#### **Tabla tm_evidencia:**
- ❌ **6 evidencias quedarán huérfanas**
- ❌ **Foreign Key Constraint violado** (fk_evidencia_ticket)
- ❌ **Sistema de evidencias dejará de funcionar**

#### **Tabla td_ticketdetalle:**
- ❌ **33 detalles de tickets quedarán sin referencia**
- ❌ **Historial de conversaciones perdido**
- ❌ **Posibles errores al cargar detalles**

### 2. 💥 **Funcionalidades Afectadas**

#### **Dashboard:**
- ❌ Gráficos de estadísticas mostrarán 0
- ❌ Contadores de tickets se resetearán
- ❌ Reportes históricos desaparecerán

#### **Módulo de Evidencias:**
- ❌ Enlaces a evidencias existentes fallarán
- ❌ Error 500 al intentar acceder a evidencias
- ❌ Archivos físicos quedarán sin referencia

#### **Consulta de Tickets:**
- ❌ Página de consulta mostrará vacía
- ❌ Enlaces de detalles generarán errores
- ❌ Historial de tickets perdido

#### **Sistema de Notificaciones:**
- ❌ Referencias a tickets en notificaciones fallarán
- ❌ Posibles errores en queries del sistema

### 3. 🗂️ **Archivos Huérfanos**

- ❌ Archivos de evidencias en `/public/uploads/evidencia/` sin referencia
- ❌ Imágenes subidas en Summernote sin contexto
- ❌ Posibles archivos temporales sin limpiar

## ✅ SOLUCIONES RECOMENDADAS:

### 🛡️ **Opción 1: BACKUP Completo (RECOMENDADO)**

```sql
-- 1. Crear backup de todas las tablas relacionadas
CREATE TABLE backup_tm_ticket AS SELECT * FROM tm_ticket;
CREATE TABLE backup_td_ticketdetalle AS SELECT * FROM td_ticketdetalle;
CREATE TABLE backup_tm_evidencia AS SELECT * FROM tm_evidencia;

-- 2. Vaciar en orden correcto (respetando foreign keys)
DELETE FROM tm_evidencia;
DELETE FROM td_ticketdetalle;  
DELETE FROM tm_ticket;

-- 3. Reiniciar AUTO_INCREMENT
ALTER TABLE tm_ticket AUTO_INCREMENT = 1;
ALTER TABLE td_ticketdetalle AUTO_INCREMENT = 1;
ALTER TABLE tm_evidencia AUTO_INCREMENT = 1;
```

### 🧹 **Opción 2: Limpieza Selectiva**

```sql
-- Solo eliminar tickets antiguos (ej: más de 6 meses)
DELETE FROM tm_evidencia WHERE ticket_id IN (
    SELECT ticket_id FROM tm_ticket 
    WHERE fech_crea < DATE_SUB(NOW(), INTERVAL 6 MONTH)
);

DELETE FROM td_ticketdetalle WHERE ticket_id IN (
    SELECT ticket_id FROM tm_ticket 
    WHERE fech_crea < DATE_SUB(NOW(), INTERVAL 6 MONTH)
);

DELETE FROM tm_ticket 
WHERE fech_crea < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

### 🔄 **Opción 3: Reset con Datos de Prueba**

```sql
-- 1. Limpiar todo
DELETE FROM tm_evidencia;
DELETE FROM td_ticketdetalle;
DELETE FROM tm_ticket;

-- 2. Insertar datos de prueba
INSERT INTO tm_ticket (user_id, cat_id, ticket_titulo, ticket_descripcion, tik_estado, fech_crea, estado, prioridad) 
VALUES 
(1, 1, 'Ticket de Prueba 1', 'Descripción de prueba 1', 'Abierto', NOW(), 1, 'Media'),
(1, 2, 'Ticket de Prueba 2', 'Descripción de prueba 2', 'Abierto', NOW(), 1, 'Alta');
```

## 🚨 **ANTES DE HACER CUALQUIER COSA:**

### 1. **Backup Manual Completo:**
```bash
# Exportar toda la base de datos
C:\xampp\mysql\bin\mysqldump.exe -u root gtm_db > backup_gtm_db_$(date).sql
```

### 2. **Verificar Dependencias:**
```sql
-- Ver todas las foreign keys
SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE REFERENCED_TABLE_NAME = 'tm_ticket';
```

### 3. **Limpiar Archivos Físicos:**
```bash
# Limpiar uploads de evidencias
Remove-Item "public\uploads\evidencia\*" -Force
```

## 💡 **RECOMENDACIÓN FINAL:**

**NO vacíes la tabla directamente con `DELETE FROM tm_ticket`** porque:

1. ❌ Violará las foreign keys
2. ❌ Causará errores en el sistema
3. ❌ Perderás datos importantes
4. ❌ Archivos quedarán huérfanos

**En su lugar, usa la Opción 1 (Backup + Limpieza ordenada)** que te permitirá:

1. ✅ Mantener integridad referencial
2. ✅ Conservar backup por seguridad  
3. ✅ Limpiar archivos huérfanos
4. ✅ Reiniciar contadores correctamente

¿Necesitas que te ayude a ejecutar alguna de estas opciones de forma segura?
