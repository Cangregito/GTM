var tablas;
function init(){
}

$(document).ready(function() {
    // user_id ya viene de PHP por sesión
    
    // Inicializar DataTable
    cargarTabla();
    
    // Evento para filtrar por prioridad
    $('#filtro_prioridad').change(function() {
        tablas.draw();
    });
});

function cargarTabla() {
    tablas = $('#ticket_data').DataTable({
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
            url: '/ESTADIAS/controller/ticket.php?op=listar_cerrados',
            type: "POST",
            dataType: "json",
            data: function(d) { 
                return {
                    user_id: user_id,
                    rol_id: rol_id,
                    prioridad: $('#filtro_prioridad').val()
                };
            },
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "destroy": true,
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

// Función para el botón "Ver" en la tabla
function ver(tick_id) {
    window.open('http://localhost/ESTADIAS/view/detalleTicket?ID=' + tick_id, '_blank');
}

// Función para el botón "Evidencias" en la tabla
function evidencia(tick_id) {
    window.open('http://localhost/ESTADIAS/view/VerCerrado/evidencia?ID=' + tick_id, '_blank');
}

init();
