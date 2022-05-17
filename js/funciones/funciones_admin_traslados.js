$(document).ready(function() {
  $('.select').select2();
  generar();

});
$(function() {
  //binding event click for button in modal form
  $(document).on("click", "#btnDelete", function(event) {
  });
  // Clean the modal form
  $(document).on('hidden.bs.modal', function(e) {
    var target = $(e.target);
    target.removeData('bs.modal').find(".modal-content").html('');
  });
  $(document).on("click", "#anular", function(event) {
    anular();
  });

});

function generar() {
  origen = $('#origen').val();
  pro = $('#pro').val();
  estado =$('#estado').val();
  dataTable = $('#editable2').DataTable().destroy()
  dataTable = $('#editable2').DataTable({
    language:
    {
      "sProcessing":     "Procesando...",
      "sLengthMenu":     "Mostrar _MENU_ registros",
      "sZeroRecords":    "No se encontraron resultados",
      "sEmptyTable":     "Ningún dato disponible en esta tabla",
      "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
      "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
      "sInfoPostFix":    "",
      "sSearch":         "Buscar:",
      "sUrl":            "",
      "sInfoThousands":  ",",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
          "sFirst":    "Primero",
          "sLast":     "Último",
          "sNext":     "Siguiente",
          "sPrevious": "Anterior"
      },
      "oAria": {
          "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
      }

    },
    "pageLength": 50,
    "order": [
      [0, 'desc'],
    ],
    "processing": true,
    "serverSide": true,
    "ajax": {
      url: "admin_traslados_dt.php",
      "data": {
        "origen": origen,
        "pro" : pro,
        "estado" : estado,
      },

      error: function() { // error handling
        //$(".editable2-error").html("");
        $("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
        $("#editable2_processing").css("display", "none");
        $(".editable2-error").remove();
      }
    },
    /*"aoColumnDefs": [
      { "bSearchable": false,"bSortable":false, "aTargets": [ 3] }
    ],*/
    "columnDefs": [{
      "targets": 1, //index of column starting from 0
      "render": function(data, type, full, meta) {
        if (data != null)
          return '<p class="text-success"><strong>' + data + '</strong></p>';
        else
          return '';
      }
    }]
  });
  dataTable.ajax.reload()
}

function anular() {
  var id_movimiento = $('#id_movimiento').val();
  var dataString = 'process=anular' + '&id_movimiento=' + id_movimiento;
  $.ajax({
    type: "POST",
    url: "anular_traslado.php",
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      display_notify(datax.typeinfo, datax.msg);
      if (datax.typeinfo != "Error") {
        setInterval("location.reload();", 1000);
        $('#viewModal').hide();
      }
    }
  });
}

$(document).on('change', '#origen', function(event) {
  generar();

});
$(document).on('change', '#pro', function(event) {
  generar();

});
$(document).on('change', '#estado', function(event) {
  generar();

});

$(document).on('click', '.imprimir_traslado', function(event) {
  imprimir_traslado($(this).attr("id_traslado"));
});

function imprimir_traslado(id_traslado){
	var datoss = "process=imprimir"+"&id_traslado="+id_traslado;
	$.ajax({
		type : "POST",
		url :"imprimir_traslado.php",
		data : datoss,
		dataType : 'json',
		success : function(datos) {
			var sist_ope = datos.sist_ope;
			var dir_print=datos.dir_print;
			var shared_printer_win=datos.shared_printer_win;
			var shared_printer_pos=datos.shared_printer_pos;

			if (sist_ope == 'win') {
				$.post("http://"+dir_print+"printvalewin1.php", {
					datosvale: datos.movimiento,
					shared_printer_win:shared_printer_win,
					shared_printer_pos:shared_printer_pos,
				})
			} else {
				$.post("http://"+dir_print+"printvale1.php", {
					datosvale: datos.movimiento
				});
			}

		}
	});
}
