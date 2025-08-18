# Módulo de Evidencias para Tickets Cerrados

Este módulo permite subir y gestionar evidencias (facturas, fotos, recibos) para los tickets cerrados en el sistema de soporte.

## Requisitos previos

1. Estructura de base de datos actualizada
2. Estructura de carpetas para almacenar archivos

## Pasos de implementación

### 1. Crear la tabla en la base de datos

Ejecuta el siguiente script SQL en tu base de datos:

```sql
CREATE TABLE tm_evidencia (
    evidencia_id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    tipo_evidencia VARCHAR(50) NOT NULL, 
    descripcion TEXT,
    archivo_nombre VARCHAR(255) NOT NULL,
    archivo_ruta VARCHAR(255) NOT NULL,
    archivo_extension VARCHAR(10) NOT NULL,
    fecha_subida DATETIME NOT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    
    CONSTRAINT fk_evidencia_ticket FOREIGN KEY (ticket_id) REFERENCES tm_ticket (ticket_id),
    CONSTRAINT fk_evidencia_usuario FOREIGN KEY (user_id) REFERENCES tm_usuario (user_id)
);

CREATE INDEX idx_evidencia_ticket ON tm_evidencia(ticket_id);
CREATE INDEX idx_evidencia_user ON tm_evidencia(user_id);
CREATE INDEX idx_evidencia_estado ON tm_evidencia(estado);
```

### 2. Verificar permisos de carpeta

Asegúrate que la carpeta `public/uploads/evidencia` tenga permisos de escritura adecuados.

### 3. Estructura de archivos

- **Modelo**: `models/Evidencia.php`
- **Controlador**: `controller/evidencia.php`
- **Vista**: `view/VerCerrado/evidencia/index.php`
- **JavaScript**: `view/VerCerrado/evidencia/evidencia.js`

### 4. Uso del módulo

1. Navegar a la lista de tickets cerrados
2. Hacer clic en un ticket
3. Seleccionar "Subir Evidencia"
4. Completar el formulario y subir el archivo

### 5. Tipos de archivos permitidos

- Imágenes: JPG, PNG
- Documentos: PDF

### 6. Consideraciones de seguridad

- Validación de tipos de archivos
- Límite de tamaño: 5MB por archivo
- Nombres de archivo aleatorios para evitar conflictos

## Roles y permisos

- **Gerentes (rol_id=1)**: Pueden ver y subir evidencias para tickets cerrados
- **Soporte (rol_id=2)**: Pueden ver las evidencias pero no añadir nuevas
