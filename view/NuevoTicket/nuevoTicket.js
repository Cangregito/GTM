function init() {
  $("#ticket_form").on("submit", function (e) {
    save(e);
  });
}

$(document).ready(function() {
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
  // Actualiza el textarea con el HTML de Summernote
  $('#ticket_descripcion').val($('.summernote').summernote('code'));
  var formData = new FormData($("#ticket_form")[0]);
  $.ajax({
    url: "../../controller/ticket.php?op=insert",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function(datos){
      $('#ticket_titulo').val('');
      $('#ticket_descripcion').val('');
      swal("Correcto!", "Registrado Correctamente.", "success");
    }
  });
}

init();
