var dataTable ="";
$(document).ready(function() {
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	generar();
});
function generar(){
	fechai=$("#fecha_inicio").val();
	fechaf=$("#fecha_fin").val();
	lab=$("#lab").val();
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
			"pageLength": 50,
			"order":[[ 0, 'desc' ]],
			"processing": true,
			"serverSide": true,
			"searching": false,
			"ajax":{
					url :"admin_devolucion_dt.php?fechai="+fechai+"&fechaf="+fechaf+"&lab="+lab, // json datasource
					//url :"admin_factura_rangos_dt.php", // json datasource
					//type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
						}
					},

				} );

		dataTable.ajax.reload()
	//}
}
$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event) {
		deleted();
	});

	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
});
$(document).on("click", "#btnMostrar", function(event) {
	generar();
});
$(document).on("click", "#btnAnular", function(event) {
	anular();
});
$(document).on("click", "#btnDelete", function(event) {
	deleted();
});
$(document).on("click", "#clos", function(event) {
	location.reload();
});
$(document).on("click", "#abon", function(event) {
	if ($("#banco").val() != "") {
		if ($("#cuenta").val() != "") {
			if ($("#cheque").val() != "") {
				if ($("#monto").val() != "") {
					send();
				} else {
					display_notify("Error", "Por favor ingrese el monto del cheque");
				}
			} else {
				display_notify("Error", "Por favor ingrese el numero de cheque");
			}
		} else {
			display_notify("Error", "Por favor seleccione una cuenta");
		}
	} else {
		display_notify("Error", "Por favor seleccione un banco");
	}
});


function send() {
  var id_factura = $('#id_factura').val();
  var monto = $('#monto').val();
  var tipo_doc = $('#tipo_doc').val();
  var num_doc = $('#num_doc').val();
  var nombre = $('#nombre').val();
  var facts = $('#facts').val();
  $("#monto").val("");

  var dataString = 'process=abonar'+'&id_factura='+id_factura+"&monto="+monto+"&tipo_doc="+tipo_doc+"&num_doc="+num_doc;
  $.ajax({
    type: "POST",
    url: "abono_devolucion.php",
    data: dataString,
    dataType: 'JSON',
    success: function(datax) {
      //display_notify(datax.typeinfo,datax.msg);
      if (datax.typeinfo == "Success")
      {
        //setInterval("reload1();", 1000);
        //$("#clos").click();
        var fila = "<tr>";
            fila += "<td>" + datax.fecha + "</td>";
            fila += "<td>" + datax.hora + "</td>";
            fila += "<td>" + tipo_doc + "</td>";
            fila += "<td>" + num_doc + "</td>";
            fila += "<td class='mont'>" + datax.monto + "</td>";
            fila += "<td><a class='btn delee' id='" + datax.id_abono + "'><i class='fa fa-trash'></i></a></td>";
            fila += "</tr>";
        if ($("#appas tr").length > 0) {
          $("#appas > tr:first").before(fila);
        } else {
          $("#appas").append(fila);
        }
        var tot = parseFloat($("#total").text());
        var deuda = parseFloat($("#deuda").val());
        var abonos = parseFloat($("#abonos").val());
        tot += parseFloat(datax.monto);
        deuda -= parseFloat(datax.monto);
        abonos += parseFloat(datax.monto);
        $("#total").text(round(tot, 2));
        $("#deuda").val(round(deuda, 2));
        $("#abonos").val(round(abonos, 2));
        if (deuda == 0) {
          $("#monto").attr("readonly", true);
          $("#abonar").attr("disabled", true);
        }

      } else {
        $("#abonar").attr("disabled", false);
      }
    }
  });
}


$(document).on('keyup', '#monto', function(event) {
  if (event.keyCode == 13) {
    $("#abonar").click();
  }
  $("#abonar").attr("disabled", false);
  var monto = round(parseFloat($(this).val()), 2);
  var deuda = round(parseFloat($('#deuda').val()), 2);
  if (monto > deuda) {
    $(this).val(deuda);
  }
});
$(document).on('click', '#abonar', function(event) {
  $("#abonar").attr("disabled", true);
  var id_factura = $('#id_factura').val();
  var monto = $('#monto').val();
  var val1 = 0;
  if (monto != undefined && monto != 0 && monto != '') {
    send();
  } else {
    display_notify('Error', 'No ha ingresado un monto para abonar');
    val1 = 1;
  }

});
$(document).on('click', '.delee', function(event) {
  var id_factura = $('#id_factura').val();
  var id_abono = $(this).attr("id");
  var fila = $(this).parents("tr");
  var monto = parseFloat(fila.find(".mont").text());

  var dataString = 'process=quitar' + '&id_abono=' + id_abono + '&id_factura=' + id_factura + '&monto=' + monto;

  $.ajax({
    type: "POST",
    url: "abono_devolucion.php",
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      if (datax.typeinfo == "Success") {
        fila.remove();
        var tot = parseFloat($("#total").text());
        var deuda = parseFloat($("#deuda").val());
        var abonos = parseFloat($("#abonos").val());
        tot -= monto;
        deuda += monto;
        abonos -= monto;
        $("#deuda").val(round(deuda, 2));
        $("#abonos").val(round(abonos, 2));
        $("#total").text(round(tot, 2));
        if (deuda > 0) {
          $("#monto").attr("readonly", false);
        }
      }
      //display_notify(datax.typeinfo, datax.msg);
    }
  });


});

function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}
