function init() {
  $("#ticket_form").on("submit", function (e) {
    save(e);
  });
}

$(".summernote").summernote({
  height: 150, // set editor height
  minHeight: null, // set minimum height of editor
  maxHeight: null, // set maximum height of editor
  focus: true, // set focus to editable area after initializing summernote
});
$.post("../../controller/categoria.php?op=combo", function (data, status) {
  $("#categoria_id").html(data);
});

function save(e) {
  e.preventDefault();
  var formData = new FormData($("#ticket_form")[0]);
  $.ajax({
  url: "../../controller/ticket.php?op=insert",
  type: "POST",
  data: formData,
  contentType: false,
  processData: false,
  success: function(datos){
    $('ticket_titulo').val('');
    $('#ticket_descripcion').val('');
    swal("Correcto!", "Registrado Correctamente.", "success");
  }
});

}

init();
