<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Contraseña - GTM</title>
    <link rel="icon" href="/ESTADIAS/docs/iconGTM.png">
    <link rel="stylesheet" href="public/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="public/css/separate/pages/login.min.css">
    <link rel="stylesheet" href="public/css/main.css">
    <style>
        .step-indicator { margin-bottom: 20px; }
        .step { display: inline-block; padding: 5px 10px; margin: 0 5px; border-radius: 15px; }
        .step.active { background: #007bff; color: white; }
        .step.inactive { background: #e9ecef; color: #6c757d; }
        .step.completed { background: #28a745; color: white; }
        .password-strength {
            margin-top: 10px;
            font-size: 12px;
        }
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        .requirements {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        .requirement {
            display: block;
            margin: 2px 0;
        }
        .requirement.met {
            color: #28a745;
        }
        .requirement.met::before {
            content: "✓ ";
            font-weight: bold;
        }
        .requirement:not(.met)::before {
            content: "✗ ";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="page-center">
        <div class="page-center-in">
            <div class="container-fluid">
                <!-- Indicador de pasos -->
                <div class="step-indicator text-center">
                    <span class="step completed">1. Solicitar</span>
                    <span class="step completed">2. Verificar</span>
                    <span class="step active">3. Nueva Contraseña</span>
                </div>

                <header class="sign-title">Nueva Contraseña</header>
                <div class="sign-subtitle">Establece tu nueva contraseña segura</div>
                
                <form class="sign-box" id="passwordForm">
                    <input type="hidden" name="user_email" id="user_email" value="">
                    <input type="hidden" name="reset_token" id="reset_token" value="">
                    
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   placeholder="Ingresa tu nueva contraseña" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword1">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <div class="requirements">
                            <span class="requirement" id="req-length">Al menos 6 caracteres</span>
                            <span class="requirement" id="req-uppercase">Una letra mayúscula</span>
                            <span class="requirement" id="req-lowercase">Una letra minúscula</span>
                            <span class="requirement" id="req-number">Un número</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Confirma tu nueva contraseña" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword2">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-text" id="passwordMatch"></div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-rounded btn-success" id="submitBtn" disabled>
                            <i class="fa fa-lock"></i> Establecer Nueva Contraseña
                        </button>
                    </div>
                </form>
                
                <!-- Área de mensajes -->
                <div id="messageArea"></div>
                
                <div class="text-center">
                    <a href="index.php" class="btn btn-link">
                        <i class="fa fa-home"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="public/js/lib/jquery/jquery.min.js"></script>
    <script src="public/js/lib/bootstrap/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Obtener parámetros de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const email = urlParams.get('email');
            const token = urlParams.get('token');
            
            if (!email || !token) {
                showMessage('Error: Parámetros inválidos', 'danger');
                setTimeout(() => {
                    window.location.href = 'reset-password.html';
                }, 3000);
                return;
            }
            
            $('#user_email').val(email);
            $('#reset_token').val(token);
            
            // Validación en tiempo real
            $('#new_password').on('input', function() {
                checkPasswordStrength($(this).val());
                checkPasswordMatch();
            });
            
            $('#confirm_password').on('input', function() {
                checkPasswordMatch();
            });
            
            // Toggle password visibility
            $('#togglePassword1').on('click', function() {
                togglePasswordVisibility('#new_password', this);
            });
            
            $('#togglePassword2').on('click', function() {
                togglePasswordVisibility('#confirm_password', this);
            });
            
            // Manejar envío del formulario
            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();
                resetPassword();
            });
        });
        
        function checkPasswordStrength(password) {
            const requirements = {
                length: password.length >= 6,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password)
            };
            
            // Actualizar indicadores visuales
            $('#req-length').toggleClass('met', requirements.length);
            $('#req-uppercase').toggleClass('met', requirements.uppercase);
            $('#req-lowercase').toggleClass('met', requirements.lowercase);
            $('#req-number').toggleClass('met', requirements.number);
            
            // Calcular fortaleza
            const metCount = Object.values(requirements).filter(Boolean).length;
            let strengthText = '';
            let strengthClass = '';
            
            if (password.length === 0) {
                strengthText = '';
            } else if (metCount < 2) {
                strengthText = 'Muy débil';
                strengthClass = 'strength-weak';
            } else if (metCount < 3) {
                strengthText = 'Débil';
                strengthClass = 'strength-weak';
            } else if (metCount < 4) {
                strengthText = 'Media';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Fuerte';
                strengthClass = 'strength-strong';
            }
            
            $('#passwordStrength').html(`Fortaleza: <span class="${strengthClass}">${strengthText}</span>`);
            
            checkPasswordMatch();
        }
        
        function checkPasswordMatch() {
            const password = $('#new_password').val();
            const confirmPassword = $('#confirm_password').val();
            const isValid = password.length >= 6 && password === confirmPassword && 
                           confirmPassword.length > 0;
            
            if (confirmPassword.length === 0) {
                $('#passwordMatch').text('');
            } else if (password === confirmPassword) {
                $('#passwordMatch').html('<span class="text-success"><i class="fa fa-check"></i> Las contraseñas coinciden</span>');
            } else {
                $('#passwordMatch').html('<span class="text-danger"><i class="fa fa-times"></i> Las contraseñas no coinciden</span>');
            }
            
            // Habilitar/deshabilitar botón
            const allRequirementsMet = $('#req-length, #req-uppercase, #req-lowercase, #req-number')
                .toArray().every(el => $(el).hasClass('met'));
            
            $('#submitBtn').prop('disabled', !(isValid && allRequirementsMet));
        }
        
        function togglePasswordVisibility(inputSelector, button) {
            const input = $(inputSelector);
            const icon = $(button).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        }
        
        function resetPassword() {
            const email = $('#user_email').val();
            const token = $('#reset_token').val();
            const newPassword = $('#new_password').val();
            const confirmPassword = $('#confirm_password').val();
            
            if (newPassword !== confirmPassword) {
                showMessage('Las contraseñas no coinciden', 'warning');
                return;
            }
            
            $('#submitBtn').prop('disabled', true)
                          .html('<i class="fa fa-spinner fa-spin"></i> Actualizando...');
            
            $.ajax({
                url: 'controller/reset_password.php',
                type: 'POST',
                data: {
                    action: 'reset_password',
                    user_email: email,
                    reset_token: token,
                    new_password: newPassword,
                    confirm_password: confirmPassword
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showMessage(response.message, 'success');
                        
                        // Redirigir al login después de 3 segundos
                        setTimeout(function() {
                            window.location.href = 'index.php?m=password_reset_success';
                        }, 3000);
                    } else {
                        showMessage(response.message, 'danger');
                        $('#submitBtn').prop('disabled', false)
                                      .html('<i class="fa fa-lock"></i> Establecer Nueva Contraseña');
                    }
                },
                error: function() {
                    showMessage('Error al actualizar la contraseña. Intenta nuevamente.', 'danger');
                    $('#submitBtn').prop('disabled', false)
                                  .html('<i class="fa fa-lock"></i> Establecer Nueva Contraseña');
                }
            });
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
