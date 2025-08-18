var tabla;

function init() {
    // Inicializa la tabla cuando se carga la página
    cargarTabla();
}

function cargarTabla() {
    tabla = $('#usuarios_eliminados_data').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax": {
            url: '/ESTADIAS/controller/usuario.php?op=listar_eliminados',
            type: "POST",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
}

function restaurar(user_id) {
    swal({
        title: "¿Está seguro?",
        text: "¿Desea restaurar este usuario eliminado?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Sí, restaurar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    },
    function(isConfirm) {
        if (isConfirm) {
            $.post("/ESTADIAS/controller/usuario.php?op=restaurar", {user_id: user_id}, function(data) {
                if (data.status == "success") {
                    swal("Restaurado", "El usuario ha sido restaurado correctamente", "success");
                    tabla.ajax.reload();
                } else {
                    swal("Error", "No se pudo restaurar el usuario", "error");
                }
            }, "json");
        }
    });
}

init();
