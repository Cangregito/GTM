# Sistema de Recuperación de Contraseñas - GTM

## 📋 Descripción

Sistema completo de recuperación de contraseñas que permite a los usuarios restablecer su contraseña de forma segura mediante un proceso de verificación en 3 pasos.

## 🚀 Características

- **Proceso de 3 pasos**: Solicitud → Verificación → Nueva Contraseña
- **Códigos de verificación**: De 6 dígitos con expiración de 15 minutos
- **Tokens seguros**: Para el cambio de contraseña con expiración de 30 minutos
- **Validación de contraseñas**: Requisitos de seguridad en tiempo real
- **Notificaciones por email**: Sistema de envío de códigos (en desarrollo)
- **Interfaz intuitiva**: Con indicadores de progreso y validación visual
- **Limpieza automática**: Eventos programados para limpiar datos expirados

## 📁 Archivos del Sistema

### Vistas
- `reset-password.html` - Página inicial para solicitar reset
- `verify-reset.php` - Página de verificación de código
- `new-password.php` - Página para establecer nueva contraseña

### Controladores
- `controller/reset_password.php` - Lógica principal del sistema

### Modelos
- Métodos agregados a `models/Usuario.php`:
  - `verificar_usuario_existe()`
  - `guardar_codigo_reset()`
  - `verificar_codigo_reset()`
  - `guardar_token_reset()`
  - `verificar_token_reset()`
  - `actualizar_password()`
  - `limpiar_reset_data()`

### Base de Datos
- `database/reset_password_tables.sql` - Script de creación de tablas

## 🛠️ Instalación

### 1. Ejecutar Script SQL

```sql
-- Ejecutar el archivo database/reset_password_tables.sql en tu base de datos
source database/reset_password_tables.sql;
```

### 2. Configurar Correo (Opcional)

Para habilitar el envío real de correos, edita `libs/SimpleMailer.php`:

```php
private $username = 'tu-correo@gmail.com';
private $password = 'tu-app-password';
```

**Nota**: En desarrollo, los correos se guardan en `logs/emails.log`

## 🔄 Flujo de Funcionamiento

### Paso 1: Solicitar Reset
1. Usuario ingresa su correo en `reset-password.html`
2. Sistema verifica si el correo existe
3. Se genera código de 6 dígitos válido por 15 minutos
4. Se envía código al correo (o se guarda en log)

### Paso 2: Verificar Código
1. Usuario ingresa código en `verify-reset.php`
2. Sistema valida código y tiempo de expiración
3. Se genera token seguro válido por 30 minutos
4. Redirección a página de nueva contraseña

### Paso 3: Nueva Contraseña
1. Usuario establece nueva contraseña en `new-password.php`
2. Validación de fortaleza en tiempo real
3. Verificación de token de seguridad
4. Actualización de contraseña y limpieza de datos temporales

## 🔒 Seguridad

- **Códigos temporales**: Expiración automática de 15 minutos
- **Tokens únicos**: Generados con `random_bytes(32)`
- **Hash de contraseñas**: Usando `password_hash()` con bcrypt
- **Limpieza automática**: Evento programado cada hora
- **Validación múltiple**: En frontend y backend
- **Rate limiting**: Prevención de spam (implícito por expiración)

## 📊 Tablas de Base de Datos

### tm_reset_password
- `id` - ID único
- `user_email` - Correo del usuario
- `reset_code` - Código de 6 dígitos
- `reset_expiry` - Fecha de expiración
- `created_at` - Fecha de creación
- `used` - Estado de uso (0/1)

### tm_reset_tokens
- `id` - ID único
- `user_email` - Correo del usuario
- `reset_token` - Token de 64 caracteres
- `token_expiry` - Fecha de expiración
- `created_at` - Fecha de creación
- `used` - Estado de uso (0/1)

## 🎨 Características de UI

- **Indicador de progreso**: Muestra el paso actual del proceso
- **Validación en tiempo real**: Para códigos y contraseñas
- **Medidor de fortaleza**: Para contraseñas con requisitos visuales
- **Contador regresivo**: Para expiración de códigos
- **Botones de toggle**: Para mostrar/ocultar contraseñas
- **Mensajes informativos**: Con iconos y colores apropiados

## 🔧 Configuración Adicional

### Variables de Entorno (Opcional)
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-correo@gmail.com
MAIL_PASSWORD=tu-app-password
```

### Personalización
- Modificar tiempo de expiración en `controller/reset_password.php`
- Cambiar longitud de código (actualmente 6 dígitos)
- Personalizar plantilla de email
- Ajustar requisitos de contraseña

## 📝 Logs

- **Correos**: `logs/emails.log` - Registro de correos enviados
- **Errores**: Logs estándar de PHP para debugging

## 🚨 Consideraciones

1. **En Desarrollo**: Los correos se guardan en logs en lugar de enviarse
2. **Producción**: Configurar credenciales SMTP reales
3. **Limpieza**: El evento automático requiere `event_scheduler = ON`
4. **Seguridad**: Cambiar credenciales por defecto antes de producción

## ✅ Estado

- ✅ Sistema funcional completo
- ✅ Interfaz de usuario terminada
- ✅ Validaciones implementadas
- ✅ Base de datos configurada
- ⚠️ Sistema de correos en modo desarrollo
- ✅ Documentación completa

## 📞 Soporte

Para dudas o problemas con el sistema de reset de password, revisar:
1. Logs en `logs/emails.log`
2. Consola del navegador para errores JS
3. Logs de PHP para errores del servidor
