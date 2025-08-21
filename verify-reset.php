<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Código - GTM</title>
    <link rel="icon" href="/ESTADIAS/docs/iconGTM.png">
    <link rel="stylesheet" href="public/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="public/css/separate/pages/login.min.css">
    <link rel="stylesheet" href="public/css/main.css">
    <style>
        .code-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 10px;
            font-weight: bold;
            margin: 20px 0;
        }
        .step-indicator { margin-bottom: 20px; }
        .step { display: inline-block; padding: 5px 10px; margin: 0 5px; border-radius: 15px; }
        .step.active { background: #007bff; color: white; }
        .step.inactive { background: #e9ecef; color: #6c757d; }
        .step.completed { background: #28a745; color: white; }
        .timer { font-size: 14px; color: #dc3545; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="page-center">
        <div class="page-center-in">
            <div class="container-fluid">
                <!-- Indicador de pasos -->
                <div class="step-indicator text-center">
                    <span class="step completed">1. Solicitar</span>
                    <span class="step active">2. Verificar</span>
                    <span class="step inactive">3. Nueva Contraseña</span>
                </div>

                <header class="sign-title">Verificar Código</header>
                <div class="sign-subtitle">Ingresa el código de 6 dígitos que enviamos a tu correo</div>
                
                <form class="sign-box" id="verifyForm">
                    <input type="hidden" name="user_email" id="user_email" value="">
                    
                    <div class="form-group">
                        <label>Código de verificación:</label>
                        <input type="text" class="form-control code-input" id="reset_code" name="reset_code" 
                               placeholder="000000" maxlength="6" pattern="[0-9]{6}" required>
                        <div class="timer text-center">
                            <i class="fa fa-clock-o"></i>
                            <span id="timer">El código expira en: <strong id="countdown">15:00</strong></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-rounded btn-primary" id="verifyBtn">
                            <i class="fa fa-check"></i> Verificar Código
                        </button>
                        <button type="button" class="btn btn-rounded btn-outline-secondary" id="resendBtn">
                            <i class="fa fa-refresh"></i> Reenviar Código
                        </button>
                    </div>
                </form>
                
                <!-- Área de mensajes -->
                <div id="messageArea"></div>
                
                <div class="text-center">
                    <a href="reset-password.html" class="btn btn-link">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                    <a href="index.php" class="btn btn-link">
                        <i class="fa fa-home"></i> Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="public/js/lib/jquery/jquery.min.js"></script>
    <script src="public/js/lib/bootstrap/bootstrap.min.js"></script>
    <script>
        let countdownTimer;
        let timeRemaining = 15 * 60; // 15 minutos en segundos
        
        $(document).ready(function() {
            // Obtener email de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const email = urlParams.get('email');
            
            if (!email) {
                showMessage('Error: No se especificó el correo electrónico', 'danger');
                setTimeout(() => {
                    window.location.href = 'reset-password.html';
                }, 3000);
                return;
            }
            
            $('#user_email').val(email);
            startCountdown();
            
            // Solo permitir números en el campo de código
            $('#reset_code').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    $('#verifyBtn').focus();
                }
            });
            
            // Manejar envío del formulario
            $('#verifyForm').on('submit', function(e) {
                e.preventDefault();
                verifyCode();
            });
            
            // Manejar reenvío de código
            $('#resendBtn').on('click', function() {
                resendCode();
            });
        });
        
        function verifyCode() {
            const email = $('#user_email').val();
            const code = $('#reset_code').val().trim();
            
            if (code.length !== 6) {
                showMessage('El código debe tener 6 dígitos', 'warning');
                return;
            }
            
            $('#verifyBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verificando...');
            
            $.ajax({
                url: 'controller/reset_password.php',
                type: 'POST',
                data: {
                    action: 'verify_code',
                    user_email: email,
                    reset_code: code
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showMessage(response.message, 'success');
                        clearInterval(countdownTimer);
                        
                        // Redirigir a la página de nueva contraseña
                        setTimeout(function() {
                            window.location.href = 'new-password.php?email=' + encodeURIComponent(email) + 
                                                  '&token=' + encodeURIComponent(response.reset_token);
                        }, 2000);
                    } else {
                        showMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    showMessage('Error al verificar el código. Intenta nuevamente.', 'danger');
                },
                complete: function() {
                    $('#verifyBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Verificar Código');
                }
            });
        }
        
        function resendCode() {
            const email = $('#user_email').val();
            
            $('#resendBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enviando...');
            
            $.ajax({
                url: 'controller/reset_password.php',
                type: 'POST',
                data: {
                    action: 'request_reset',
                    user_email: email
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showMessage('Nuevo código enviado a tu correo', 'success');
                        // Reiniciar contador
                        timeRemaining = 15 * 60;
                        startCountdown();
                        $('#reset_code').val('').focus();
                    } else {
                        showMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    showMessage('Error al reenviar el código', 'danger');
                },
                complete: function() {
                    $('#resendBtn').prop('disabled', false).html('<i class="fa fa-refresh"></i> Reenviar Código');
                }
            });
        }
        
        function startCountdown() {
            countdownTimer = setInterval(function() {
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                
                $('#countdown').text(
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0')
                );
                
                if (timeRemaining <= 0) {
                    clearInterval(countdownTimer);
                    $('#timer').html('<span class="text-danger">Código expirado</span>');
                    $('#verifyBtn').prop('disabled', true);
                }
                
                timeRemaining--;
            }, 1000);
        }
        
        function showMessage(message, type) {
            const alertClass = 'alert-' + type;
            const iconClass = type === 'success' ? 'fa-check-circle' : 
                             type === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle';
            
            const messageHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fa ${iconClass}"></i> ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            
            $('#messageArea').html(messageHtml);
        }
    </script>
</body>
</html>
