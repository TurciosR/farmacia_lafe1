var dataTable ="";

$(document).ready(function()
{
	$(".date").datepicker();

	// Clean the modal form
	generar();
});
$(".datepick").datepicker({
		format: 'dd-mm-yyyy',
		language:'es',
	});
function generar(){
	fechai=$("#fecha_inicio").val();
	fechaf=$("#fecha_fin").val();
	id_proveedor=$("#id_proveedor").val();
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
			"pageLength": 50,
			"order":[[ 7, 'desc' ], [ 6, 'asc' ]],
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"admin_cxp_dt.php?fechai="+fechai+"&fechaf="+fechaf+"&id_proveedor="+id_proveedor, // json datasource
					//url :"admin_factura_rangos_dt.php", // json datasource
					//type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
						}
					}
				} );

		dataTable.ajax.reload()
	//}
}
$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event) {
		deleted();
	});
	$(document).on("click", "#abon", function(event) {
			if($("#descuento").val()!="")
			{
				if($("#monto").val()!="")
				{
					send();
				}
				else
				{
					display_notify("Error", "Por favor ingrese el monto del descuento");
				}
			}
			else
			{
				display_notify("Error", "Por favor seleccione un tipo de descuento");
			}
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
$(document).on("click", "#closing", function(event) {
	reload1();
});
function send()
{
	var idtransace = $('#idtransace').val();
	var tipo_descuento = $('#descuento').val();
	var numero_doc = $('#numero_doc').val();
	var monto = $('#monto').val();
	var dataString = 'process=descontar'+'&idtransace='+idtransace+"&tipo_descuento="+tipo_descuento+"&numero_doc="+numero_doc+"&monto="+monto;


	$.ajax({
		type : "POST",
		url : "descontar.php",
		data : dataString,
		dataType : 'JSON',
		success: function(datax)
		{
			display_notify(datax.typeinfo,datax.msg);
			if(datax.typeinfo == "Success")
			{
				/*setInterval("reload1();", 1000);
				$("#clos").click();*/
				var idtransace=$('#idtransace').val();
				$.ajax({
					type : "POST",
					url : "descontar.php",
					data : 'process=refresh'+'&idtransace='+idtransace,
					dataType : 'JSON',
					success: function(datax)
					{
						$('#cuerpo_tabla').html(datax.opt);
						$('#total_descuentos').html(datax.tot);
						$('#numero_doc').val('');
						$('#monto').val('');
						$('#saldo_pendiente').val(datax.saldo_pend);
						$('.select').val('').trigger('change');
					}
				});

			}
		}
	});
}
function reload1(){
  var id = $('#id_proveedor').val();
	location.href = 'admin_cxp.php?id_proveedor='+id;
}
