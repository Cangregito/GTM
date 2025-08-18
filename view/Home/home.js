// Variable global para el gráfico
var morrisChart = null;

$(document).ready(function() {
    // Esperar un momento para asegurarse de que todos los elementos del DOM estén listos
    setTimeout(function() {
        // Cargar datos según el rol del usuario
        if (rol_id == 2) {
            // Para soporte, mostrar todos los tickets
            cargarDatosSoporte();
        } else {
            // Para usuario normal, mostrar solo sus tickets
            cargarDatosUsuario();
        }
    }, 300);
    
    // Agregar manejador para el botón de recarga
    $("#btnRecargar").click(function() {
        // Limpiar el gráfico actual si existe
        if (morrisChart) {
            // Limpiar el contenedor
            $('#chart-categorias').empty();
            morrisChart = null;
        }
        
        // Volver a cargar datos según el rol
        if (rol_id == 2) {
            cargarDatosSoporte();
        } else {
            cargarDatosUsuario();
        }
    });
    
    // Manejar cambios de tamaño de ventana para hacer el gráfico responsive
    $(window).on('resize', function() {
        if (morrisChart) {
            morrisChart.redraw();
        }
    });
});

function cargarDatosUsuario() {
    // Cargar totales para el usuario
    $.ajax({
        url: '/ESTADIAS/controller/dashboard.php?op=ticket_totales_x_usuario',
        type: 'POST',
        data: { user_id: user_id },
        dataType: 'json',
        success: function(data) {
            // Actualizar tarjetas de resumen
            $('#total-tickets').html(data.total);
            $('#tickets-abiertos').html(data.abiertos);
            $('#tickets-cerrados').html(data.cerrados);
        },
        error: function(xhr, status, error) {
            // Manejar errores silenciosamente
        }
    });
    
    // Cargar datos por categoría para el usuario
    $.ajax({
        url: '/ESTADIAS/controller/dashboard.php?op=ticket_totales_x_categoria_usuario',
        type: 'POST',
        data: { user_id: user_id },
        dataType: 'json',
        success: function(data) {
            // Generar gráfico por categoría
            generarGraficoCategorias(data);
        },
        error: function(xhr, status, error) {
            // Manejar errores silenciosamente
        }
    });
}

function cargarDatosSoporte() {
    // Cargar totales generales
    $.ajax({
        url: '/ESTADIAS/controller/dashboard.php?op=ticket_totales_general',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            // Actualizar tarjetas de resumen
            $('#total-tickets').html(data.total);
            $('#tickets-abiertos').html(data.abiertos);
            $('#tickets-cerrados').html(data.cerrados);
        },
        error: function(xhr, status, error) {
            // Manejar errores silenciosamente
        }
    });
    
    // Cargar datos por categoría generales
    $.ajax({
        url: '/ESTADIAS/controller/dashboard.php?op=ticket_totales_x_categoria_general',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            // Generar gráfico por categoría
            generarGraficoCategorias(data);
        },
        error: function(xhr, status, error) {
            // Manejar errores silenciosamente
        }
    });
}

function generarGraficoCategorias(data) {
    // Verificar que hay datos para mostrar
    if (!data || data.length === 0) {
        $('#chart-categorias').html('<div class="alert alert-info">No hay datos disponibles para mostrar.</div>');
        return;
    }
    
    try {
        // Asegurarse de que el contenedor exista
        if ($('#chart-categorias').length === 0) {
            return;
        }
        
        // Limpiar el contenedor antes de crear un nuevo gráfico
        $('#chart-categorias').empty();
        
        // Verificar que data sea un array y tenga el formato correcto
        let chartData = [];
        
        // Si data es un objeto (no un array), conviértelo a array
        if (!Array.isArray(data)) {
            if (data.error) {
                $('#chart-categorias').html('<div class="alert alert-danger">Error en los datos: ' + data.error + '</div>');
                return;
            }
            
            // Si es un objeto con propiedades, convertirlo a formato adecuado
            if (typeof data === 'object') {
                // Intentar extraer los datos en formato correcto para Morris.js
                for (let key in data) {
                    if (key !== 'error' && key !== 'status') {
                        chartData.push({
                            'categoria': key,
                            'total': parseInt(data[key]) || 0
                        });
                    }
                }
            }
        } else {
            // Asegurarse de que cada objeto en el array tenga las propiedades necesarias
            chartData = data.map(item => {
                return {
                    'categoria': item.categoria || 'Sin categoría',
                    'total': parseInt(item.total) || 0
                };
            });
        }
        
        // Verificar que tenemos datos para mostrar después del procesamiento
        if (chartData.length === 0) {
            $('#chart-categorias').html('<div class="alert alert-info">No hay datos disponibles para mostrar.</div>');
            return;
        }
        
        // Si ya existe un gráfico, destruirlo
        if (morrisChart) {
            $('#chart-categorias').empty();
            morrisChart = null;
        }
        
        // Crear gráfico de barras con Morris.js
        morrisChart = Morris.Bar({
            element: 'chart-categorias',
            data: chartData,
            xkey: 'categoria',
            ykeys: ['total'],
            labels: ['Total de Tickets'],
            barColors: ['#3c8dbc'],
            hideHover: 'auto',
            resize: true,
            gridTextSize: 11,
            gridTextColor: '#5e6166',
            parseTime: false // Importante para evitar que Morris intente parsear fechas
        });
    } catch (error) {
        $('#chart-categorias').html('<div class="alert alert-danger">Error al generar el gráfico</div>');
    }
}
