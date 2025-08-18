function init() {
  $("#ticket_form").on("submit", function (e) {
    save(e);
  });
}

$(document).ready(function() {
    // Inicializar Summernote
    $('.summernote').summernote({
        height: 180,
        placeholder: 'Describe tu problema o adjunta imágenes aquí...',
        callbacks: {
            onImageUpload: function(files) {
                for (let i = 0; i < files.length; i++) {
                    uploadImage(files[i], this);
                }
            }
        }
    });
    
    // Mostrar consejos de validación cuando el usuario interactúe con el formulario
    $('#ticket_form').find('input, select, .note-editor').on('focus click', function() {
        $('#validation-summary').fadeIn(300);
    });
    
    // Animaciones al hacer focus en los campos
    $('#ticket_form').find('input, select').on('focus', function() {
        $(this).addClass('focused-field').css({
            'transition': 'transform 0.3s, box-shadow 0.3s',
            'transform': 'translateY(-2px)',
            'box-shadow': '0 4px 8px rgba(0,0,0,0.1)'
        });
    }).on('blur', function() {
        $(this).removeClass('focused-field').css({
            'transform': '',
            'box-shadow': ''
        });
    });
});

function uploadImage(file, editor) {
    var data = new FormData();
    data.append("file", file);
    $.ajax({
        url: '/ESTADIAS/controller/upload_summernote.php', // Debes tener este endpoint
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        type: "POST",
        success: function(url) {
            console.log('Imagen subida:', url);
            $(editor).summernote('insertImage', url);
        },
        error: function() {
            alert('No se pudo subir la imagen');
        }
    });
}

$.post("../../controller/categoria.php?op=combo", function (data, status) {
  $("#categoria_id").html(data);
});

function save(e) {
  e.preventDefault();
  
  // Validación de campos
  var titulo = $('#ticket_titulo').val();
  var descripcion = $('.summernote').summernote('code');
  var categoria = $('#categoria_id').val();
  
  // Verificar campo título
  if (!titulo || titulo.trim() === '') {
    animateValidation('#ticket_titulo', 'Por favor ingrese un título para el ticket');
    return false;
  }
  
  // Verificar categoría
  if (!categoria || categoria === '0' || categoria === '') {
    animateValidation('#categoria_id', 'Por favor seleccione una categoría');
    return false;
  }
  
  // Verificar descripción (eliminar etiquetas HTML para verificar si está vacío)
  var descripcionTexto = $('<div>').html(descripcion).text().trim();
  if (!descripcionTexto || descripcionTexto === 'Descripción' || descripcionTexto === '') {
    // Animación para el editor Summernote
    $('.note-editor').addClass('error-shake');
    setTimeout(function() {
      $('.note-editor').removeClass('error-shake');
    }, 600);
    
    swal({
      title: "Campo requerido",
      text: "Por favor ingrese una descripción para el ticket",
      type: "warning",
      showCancelButton: false,
      confirmButtonText: "Entendido",
      closeOnConfirm: true
    });
    return false;
  }
  
  // Si todo está validado, enviar formulario
  // Actualiza el textarea con el HTML de Summernote
  $('#ticket_descripcion').val(descripcion);
  var formData = new FormData($("#ticket_form")[0]);
  
  // Mostrar animación de carga
  swal({
    title: "Procesando",
    text: "Espere un momento mientras registramos su ticket...",
    type: "info",
    showConfirmButton: false,
    allowOutsideClick: false
  });
  
  $.ajax({
    url: "/ESTADIAS/controller/ticket.php?op=insert",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function(response){
      try {
        var data = JSON.parse(response);
        
        if(data.success) {
          // Limpieza y mensaje de éxito
          $('#ticket_titulo').val('');
          $('.summernote').summernote('code', '');
          
          swal({
            title: "¡Ticket Registrado!",
            text: "Su ticket ha sido registrado correctamente con el número #" + data.ticket_id,
            type: "success",
            confirmButtonText: "Continuar",
            closeOnConfirm: true
          });
        } else {
          swal({
            title: "Error",
            text: "No se pudo registrar el ticket. Intente nuevamente.",
            type: "error"
          });
        }
      } catch(e) {
        swal({
          title: "Error",
          text: "Ocurrió un error al procesar la respuesta del servidor",
          type: "error"
        });
      }
    },
    error: function(xhr, status, error) {
      swal({
        title: "Error de Conexión",
        text: "No se pudo conectar con el servidor. Verifique su conexión e intente nuevamente.",
        type: "error"
      });
    }
  });
}

// Función para animar validación con efecto shake
function animateValidation(selector, message) {
  $(selector).addClass('error-shake');
  
  // Agregar borde rojo
  $(selector).css('border', '1px solid #ff5b57');
  
  // Mostrar mensaje de error con fadeIn
  if ($(selector).next('.error-message').length === 0) {
    $('<div class="error-message" style="color: #ff5b57; margin-top: 5px; display: none;"><i class="fa fa-warning"></i> ' + message + '</div>')
      .insertAfter(selector)
      .fadeIn(300);
  }
  
  // Quitar animación después de completarse
  setTimeout(function() {
    $(selector).removeClass('error-shake');
  }, 600);
  
  // Restaurar estilos cuando el campo reciba foco
  $(selector).one('focus change', function() {
    $(this).css('border', '');
    $(this).next('.error-message').fadeOut(200, function() {
      $(this).remove();
    });
  });
  
  // Mostrar mensaje SweetAlert
  swal({
    title: "Campo requerido",
    text: message,
    type: "warning",
    showCancelButton: false,
    confirmButtonText: "Entendido",
    closeOnConfirm: true
  });
}

init();
