$(document).ready(function() {
  //busqueda con el plugin autocomplete typeahead
  $("#producto_buscar").typeahead({
    source: function(query, process) {
      $.ajax({
        type: 'POST',
        url: 'facturacion_autocomplete1.php',
        data: 'query=' + query,
        dataType: 'JSON',
        async: true,
        success: function(data) {
          process(data);
        }
      });
    },
    updater: function(selection) {
      var prod0 = selection;
      var prod = prod0.split("|");
      var id_prod = prod[0];
      var descrip = prod[1];
      if (id_prod != 0) {
        addProductList(id_prod);
        $('input#producto_buscar').val("");
      } else {
        $('input#producto_buscar').focus();
        $('input#producto_buscar').val("");
      }
      // agregar_producto_lista(id_prod, descrip, isbarcode);
    }
  });
  $('#producto_buscar').focus();
  $("#barcode").hide();
  $('#tipo0').on('ifChecked', function(event) {
    $("#buscar_habilitado").html("Buscar Producto  (Por Descripcion)")
    $("#producto_buscar").val("");
    $("#producto_buscar").show()
    $("#producto_buscar").focus();
    $("#barcode").hide();
  });
  $('#tipo1').on('ifChecked', function(event) {
    $("#buscar_habilitado").html("Buscar Producto  (Por Barcode)")
    $("#barcode").val("");
    $("#barcode").show()
    $("#barcode").focus();
    $("#producto_buscar").hide();
  });
  if ($("#process").val() == "insert" || $("#process").val() == "edit") {
    $('#inventable').arrowTable();
  } else {
    generar();
  }
  $('#total_dinero').html("<strong>0</strong>")

  $(".decimal").numeric({
    negative: false
  });

  //Fin busqueda por la caja de texto solo para barcode
  $("#submit1").on("click", function(e) {
    if ($('#items').val() != 0) {
      e.preventDefault();
      senddata();
    } else {
      var typeinfo = 'Error';
      var msg = 'Debe registrar al menos un producto para la venta !';
      display_notify(typeinfo, msg);
    }
  });
  $("#btnMostrar").on("click", function() {
    generar();
  });
  $(".select").select2({
    placeholder: {
      id: '',
      text: 'Seleccione',
    },
    allowClear: true,
  });
  if($("#process").val() == "edit")
  {
    totales();
  }
});
$(document).keydown(function(e) {
  if (e.which == 120) { //F9 guarda factura
    e.preventDefault();
    if ($('#totalfactura').val() != 0 && $("#items").val() > 0) {
      senddata();
    } else {
      display_notify('Error', 'Debe haber al menos un producto registrado');
    }
  }
});
var valor = "";
//evento que captura el texto al pegar y lo envia a otro evt de busqueda de barcode
$("#barcode").bind('paste', function(e) {
  var pasteData = e.originalEvent.clipboardData.getData('text')
  valor = $(this).val();
  if (pasteData.length >= 2) {
    searchBarcode(pasteData);
    $('#barcode').val("");
    $('#barcode').focus();
  }
})
//evento al keyup para buscar si el barcode es de longitud mayor igual a 1 caracteres
$('#barcode').on('keyup', function(event) {
  //alert("buscar por barcode")
  if (event.which && this.value.length >= 2 && event.which === 13) {
    valor = $(this).val();
    $('#barcode').val(valor)
    searchBarcode($(this).val());
    $('#barcode').val("");
    $('#barcode').focus();
  }
});

function searchBarcode(barcode) {
  //evento para buscar por el barcode
  urlprocess = $('#urlprocess').val();
  var dataString = 'process=buscarBarcode' + '&id_producto=' + barcode;
  $.ajax({
    type: "POST",
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      var id_producto = datax.id_prod;
      addProductList(id_producto);
      $('#barcode').val("");
      $('#barcode').focus();
    }

  });
}

$(document).on("change", "#id_cliente", function() {
  datos_clientes();
  totales();
});

$(document).on("focus", " #btnSelect", function() {
  $(this).addClass('btn-warning');
  $(this).removeClass('btn-primary');
})

$(document).on("blur", " #btnSelect", function() {
  $(this).removeClass('btn-warning');
  $(this).addClass('btn-primary');
});
//
$(function() {
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

// Evento para agregar elementos al grid de factura
function addProductList(id_prod) {
  $('#inventable').find('tr#filainicial').remove();
  id_prod = $.trim(id_prod);
  urlprocess = $('#urlprocess').val();
  var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod;
  $.ajax({
    type: "POST",
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(data) {
      var precio_venta = data.precio_venta;
      var unidades = data.unidades;
      var existencias = data.stock;
      var perecedero = data.perecedero;
      var descrip_only = data.descripcion;
      var fecha_fin_oferta = data.fecha_fin_oferta;
      var categoria = data.categoria;

      var filas = parseInt($("#filas").val());

      var subtotal = subt(data.preciop, 1);
      subt_mostrar = subtotal.toFixed(2);
      var cantidades = "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal "+categoria+"' id='cant' name='cant' value='1' style='width:60px;'></div></td>";

      tr_add = '';
      tr_add += "<tr class='row100 head' id='" + filas + "'>";
      tr_add += "<td hidden class='cell100 column10 text-success id_pps'><input type='hidden' id='unidades' name='unidades' value='" + data.unidadp + "'>" + id_prod + "</td>";
      tr_add += "<td class='cell100 column30 text-success'>" + descrip_only + '</td>';
      tr_add += "<td class='cell100 column10 text-success' id='cant_stock'>" + existencias + "</td>";
      tr_add += "<td class='cell100 column10 text-success preccs'>" + data.select + "</td>";
      tr_add += "<td class='cell100 column10 text-success descp'><input type'text' id='dsd' value='" + data.descripcionp + "' class='form-control' readonly></td>";
      tr_add += "<td class='cell100 column10 text-success rank_s'>" + data.select_rank + "</td>";
      tr_add += "<td hidden class='cell100 column10 text-success'><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='" + data.preciop + "'><input type='text'  class='form-control decimal ' readOnly id='precio_venta' name='precio_venta' value='" + data.preciop + "'></td>";

      tr_add += cantidades;
      tr_add += "<td class='ccell100 column10'>" + "<input type='hidden'  id='subt_iva' name='subt_iva' value='0.0'>" + "<input type='text'  class='decimal form-control' id='subtotal_fin' name='subtotal_fin'  value='" + subt_mostrar + "'readOnly></td>";
      tr_add += '<td class="cell100 column10 Delete text-center"><input id="delprod" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
      tr_add += '</tr>';
      //numero de filas
      filas++;


      $("#inventable").append(tr_add);

      if(categoria==86)
      {
        $(".decimal").numeric({decimal:false,negative:false});
        $(".86").numeric({decimalPlaces:4,negative:false});
      }
      else
      {
        $(".decimal").numeric({decimal:false,negative:false});
        $(".86").numeric({decimalPlaces:4,negative:false});
      }

      $('#items').val(filas);
      totales();
      scrolltable();
    }
  });
  totales();
}

$(document).on('change', '.sel', function(event) {
  var id_presentacion = $(this).val();
  var a = $(this);
  console.log(id_presentacion);

  $.ajax({
    url: 'preventa.php',
    type: 'POST',
    dataType: 'json',
    data: 'process=getpresentacion' + "&id_presentacion=" + id_presentacion,
    success: function(data) {
      a.closest('tr').find('.descp').html(data.descripcion);
      a.closest('tr').find('#precio_venta').val(data.precio);
      a.closest('tr').find('#unidades').val(data.unidad);
      a.closest('tr').find(".rank_s").html(data.select_rank);
      var tr = a.closest('tr');
      actualiza_subtotal(tr);
    }
  });
  setTimeout(function() {
    totales();
  }, 1000);

});
// reemplazar valores de celda cantidades
function setRowCant(rowId) {
  var stock1 = 0
  var tr = $('#inventable tr:nth-child(' + rowId + ')')

  stock1 = $('#inventable tr:nth-child(' + rowId + ')').find('#cant_stock').text()
  //}
  stock1 = tr.find('#cant_stock').text();
  stock1 = parseInt(stock1)

  var cantidad_anterior = tr.find("#cant").val();
  var cantidad_nueva = parseFloat(cantidad_anterior) + 1;

  tr.find("#cant").val(cantidad_nueva);
  actualiza_subtotal(tr);
}
$("#inventable").on('keyup', '#cant', function() {
  totales();
});
$("#inventable").on('keyup', '#precio_venta', function() {
  totales();
});

// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function() {
  $(this).parents("tr").remove();
  totales();
});
$(document).on("click", ".Delete_bd", function() {
  var tr = $(this).parents("tr");
  id_detalle = tr.attr("id_detalle");
  $.ajax({
    type:'POST',
    url:'editar_cotizacion.php',
    data:'process=del&id_detalle='+id_detalle,
    dataType:'JSON',
    success: function(datax)
    {
      if(datax.typeinfo == "Success")
      {
        tr.remove();
      }
    }
  });
  totales();
});
$(document).on('change', '.sel_prec', function() {
  var tr = $(this).parents("tr");
  var precio = $(this).find(':selected').val();
  tr.find("#precio_venta").val(precio);
  actualiza_subtotal(tr);
});
$(document).on("keyup", "#cant, #precio_venta", function() {
  fila = $(this).closest('tr');
  id_producto = fila.find('.id_pps').text();
  var tr = $(this).parents("tr");
	id_presentacion_p = tr.find('.sel').val();
  a_cant=$(this).val();
  unidad= parseInt(fila.find('#unidades').val());
  a_cant=parseFloat(a_cant*unidad);
	a_cant=round(a_cant, 4);
	//Ranking de precios
	$.ajax({
			type:'POST',
			url:'venta.php',
			data:'process=cons_rank&id_producto='+id_producto+'&id_presentacion='+id_presentacion_p+'&cantidad='+a_cant,
			dataType:'JSON',
			success:function(datax)
			{
				tr.find(".rank_s").html(datax.precios);
				tr.find("#precio_venta").val(datax.precio);
			}
	});
	setTimeout(function(){ actualiza_subtotal(tr); }, 300);
});

function actualiza_subtotal(tr) {
  var iva = parseFloat($('#porc_iva').val());
  var precio_sin_iva = 0
  var subotal_sin_iva = 0;
  var existencias = tr.find('#cant_stock').text();
  var cantidad = tr.find('#cant').val();
  if (isNaN(cantidad) || cantidad == "") {
    cantidad = 0;
  }
  var precio = tr.find('#precio_venta').val();
  var precio_oculto = tr.find('#precio_venta').val();

  if (isNaN(precio) || precio == "") {
    precio = 0;
  }
  var subtotal = subt(cantidad, precio);
  var subt_mostrar = subtotal.toFixed(2);
  tr.find("#subtotal_fin").val(subt_mostrar);
  tr.find("#subt_iva").val(subt_mostrar);
  totales();
}

function totales() {
  //impuestos
  var iva = $('#porc_iva').val();
  var porc_percepcion = $("#porc_percepcion").val();
  var id_tipodoc = $("#tipo_impresion option:selected").val();
  var monto_retencion1 = $('#monto_retencion1').val();
  var monto_retencion10 = $('#monto_retencion10').val();
  var monto_percepcion = $('#monto_percepcion').val();
  var porcentaje_descuento = parseFloat($("#porcentaje_descuento").val());

  var total_sin_iva = 0;
  //fin impuestos
  var urlprocess = $('#urlprocess').val();
  var i = 0,
    total = 0;
  totalcantidad = 0;

  var total_gravado = 0;
  var total_exento = 0;
  var subt_gravado = 0;
  var subt_exento = 0;
  var subtotal = 0;
  var total_descto = 0;
  var total_sin_descto = 0;
  var subt_descto = 0;
  var total_final = 0;
  var subtotal_sin_iva = 0;
  var StringDatos = '';
  var filas = 0;
  var total_iva = 0;
  $("#inventable tr").each(function() {
    subt_cant = $(this).find("#cant").val()

    if (isNaN(subt_cant) || subt_cant == "") {
      subt_cant = 0;
    }
    subtotal_final = $(this).find("#subtotal_fin").val()
    total_final += parseFloat(subtotal_final);
    totalcantidad += parseFloat(subt_cant);
    subtotal_sin_iva = subtotal_final;
    total_sin_iva += parseFloat(subtotal_sin_iva);
    filas += 1;
  });

  total_final = round(total_final, 4);
  //descuento
  var total_descuento = 0;
  if (porcentaje_descuento > 0.0) {
    total_descuento = (porcentaje_descuento / 100) * total_final
  } else {
    total_descuento = 0;
  }
  var total_descuento_mostrar = total_descuento.toFixed(2)
  var total_mostrar = total_final.toFixed(2)
  totcant_mostrar = totalcantidad.toFixed(2)

  $('#totcant').text(totcant_mostrar);
  $('#totfin').text(total_mostrar);
  $('#totalfactura').val(total_mostrar);

  total_dinero = total_final.toFixed(2);

  var total_sin_iva_mostrar = total_sin_iva.toFixed(2);
  $('#total_gravado_sin_iva').html(total_sin_iva_mostrar);
  txt_war = "class='text-danger'"
  var total_iva_mostrar = 0.00;

  $('#total_gravado').html(total_mostrar);
  total_exento = 0;
  $('#total_exento').html(total_exento);
  $('#total_exenta').html(total_exento);

  $('#total_iva').html(total_iva_mostrar);
  total_gravado_iva = total_sin_iva + total_iva;
  //total_gravado_iva=  total_sin_iva*(1+parseFloat(iva));
  total_gravado_iva_mostrar = total_gravado_iva.toFixed(2);
  $('#total_gravado_iva').html(total_gravado_iva_mostrar); //total gravado con iva

  var total_retencion1 = 0
  var total_retencion10 = 0
  var total_percepcion = 0
  if (total_gravado >= monto_retencion1)
    total_retencion1 = total_gravado * porc_retencion1;
  if (total_gravado >= monto_retencion10)
    total_retencion10 = total_gravado * porc_retencion10;
  total_percepcion_mostrar = total_percepcion.toFixed(2);
  var total_final = (total_final - total_descuento + total_percepcion) - (total_retencion1 + total_retencion10);

  total_final_mostrar = total_final.toFixed(2);
  $('#total_percepcion').html(total_percepcion_mostrar);
  total_retencion1_mostrar = total_retencion1.toFixed(2);
  total_retencion10_mostrar = total_retencion10.toFixed(2);
  $('#total_retencion').html('0.00');
  if (parseFloat(total_retencion1) > 0.0)
    $('#total_retencion').html(total_retencion1_mostrar);
  if (parseFloat(total_retencion10) > 0.0)
    $('#total_retencion').html(total_retencion10_mostrar);
  //total final
  $('#total_final').html(total_descuento_mostrar);
  $('#totalfactura').val(total_final_mostrar);

  $('#totcant').html(totalcantidad);
  $('#items').val(filas);
  $('#totaltexto').load(urlprocess, {
    'process': 'total_texto',
    'total': total_final_mostrar
  });
  $('#monto_pago').html(total_final_mostrar);
}
//cantidad restar en td stock
//datos de clientes
function datos_clientes() {
  var id_cliente = $("select#id_cliente option:selected").val();
  var urlprocess = $('#urlprocess').val();
  dataString = {
    process: "datos_clientes",
    id_cliente: id_cliente
  };
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      porc_percepcion = datax.percepcion;
      porc_retencion1 = datax.retencion1;
      porc_retencion10 = datax.retencion10;
      porcentaje_descuento = datax.porcentaje_descuento;
      $("#porc_retencion1").val(porc_retencion1);
      $("#porc_retencion10").val(porc_retencion10);
      $("#porc_percepcion").val(porc_percepcion);
      $("#porcentaje_descuento").val(porcentaje_descuento);
      totales();
    }
  });
}

$(document).on('keyup', '#cant', function() {
  a = $(this);
  id = $(this).closest('tr').find('td:eq(0)').text();
  stock = parseInt($(this).closest('tr').find('#cant_stock').text());
  avender = 0;
  $("#inventable tr").each(function(index) {

    if ($(this).find('td:eq(0)').text() == id) {
      cant = parseInt($(this).find('#cant').val());
      unidad = parseInt($(this).find('#unidades').val());
      avender = avender + (parseInt(cant * unidad));
    }

  });
  totales();
});
//Calcular Totales del grid

function senddata() {
  //Obtener los valores a guardar de cada item facturado
  var procces = $("#process").val();
  var i = 0;
  var StringDatos = "";
  var id = '1';
  var id_empleado = 0;
  var id_cliente = $("#id_cliente option:selected").val();
  var items = $("#items").val();
  var msg = "";
  //IMPUESTOS
  var total_retencion = $('#total_retencion').text();
  var total_percepcion = $('#total_percepcion').text();
  var total_iva = $('#total_iva').text();
  var id_vendedor = $("#vendedor option:selected").val();

  var vigencia = $('#vigencia').val();
  var total_venta = $('#totalfactura').val();
  var fecha_movimiento = $("#fecha").val();
  var id_prod = 0;
  if (fecha_movimiento == '' || fecha_movimiento == undefined) {
    var typeinfo = 'Warning';
    msg = 'Seleccione una Fecha!';
    display_notify(typeinfo, msg);
  }
  var verificaempleado = 'noverificar';
  var verifica = [];
  var array_json = new Array();
  $("#inventable tr").each(function(index) {
      var id_detalle = $(this).attr("id_detalle");
      if(id_detalle == undefined)
      {
        id_detalle = "";
      }
      var id = $(this).find("td:eq(0)").text();
      var id_presentacion = $(this).find('.sel').val();
      var precio_venta = $(this).find("#precio_venta").val();
      var cantidad = $(this).find("#cant").val();
      var unidades = $(this).find("#unidades").val()
      var subtotal = $(this).find("#subtotal_fin").val()

      if (cantidad && precio_venta) {
        var obj = new Object();
        obj.id_detalle = id_detalle;
        obj.id = id;
        obj.precio = precio_venta;
        obj.cantidad = cantidad;
        obj.unidades = unidades;
        obj.subtotal = subtotal;
        obj.id_presentacion = id_presentacion;
        //convert object to json string
        text = JSON.stringify(obj);
        array_json.push(text);
        i = i + 1;
      }
  });
  json_arr = '[' + array_json + ']';
  if(procces == "insert")
  {
    var urlprocess = "agregar_cotizacion.php";
    var id_cotizacion = "";
  }
  else
  {
    var urlprocess = "editar_cotizacion.php";
    var id_cotizacion = $("#id_cotizacion").val();
  }
  var dataString = 'process=insert' + '&cuantos=' + i + '&fecha_movimiento=' + fecha_movimiento;
  dataString += '&id_cliente=' + id_cliente + '&total_venta=' + total_venta;
  dataString += '&id_vendedor=' + id_vendedor + '&json_arr=' + json_arr;
  dataString += '&total_retencion=' + total_retencion;
  dataString += '&total_percepcion=' + total_percepcion;
  dataString += '&total_iva=' + total_iva;
  dataString += '&items=' + items;
  dataString += '&id_cotizacion=' + id_cotizacion;
  dataString += '&vigencia=' + vigencia;

  var sel_vendedor = 1;
  if (id_vendedor == "") {
    msg = 'Seleccione un Vendedor!';
    sel_vendedor = 0;
  }
  if (id_cliente == "") {
    msg = 'Seleccione un Cliente!';
    sel_vendedor = 0;
  }
  if (vigencia == "") {
    msg = 'Ingrese el numero de dias de vigencia!';
    sel_vendedor = 0;
  }
  if (sel_vendedor == 1) {
    $("#inventable tr").remove();
    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        display_notify(datax.typeinfo, datax.msg);
        if (datax.typeinfo == "Success") {
          setInterval("reload1();", 1000);
        }
      }
    });
  } else {
    display_notify('Warning', msg);
  }
}

function remover_filas()
{
  $("#inventable tr").remove();
}

function reload1() {
  location.href = "admin_cotizacion.php";
}
$(document).on("click", "#btnAddClient", function(event) {
  agregarcliente();
});

function agregarcliente() {
  urlprocess = $('#urlprocess').val();
  var nombress = $(".modal-body #nombress").val();
  var apellidos = $(".modal-body #apellidos").val();
  var duii = $(".modal-body #duii").val();
  var tel1 = $(".modal-body #tel1").val();
  var tel2 = $(".modal-body #tel2").val();
  var dataString = 'process=agregar_cliente' + '&nombress=' + nombress + '&apellidos=' + apellidos;
  dataString += '&dui=' + duii + '&tel1=' + tel1 + '&tel2=' + tel2;
  $.ajax({
    type: "POST",
    url: urlprocess,
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      var process = datax.process;
      var id_client = datax.id_client;
      // Agragar datos a select2
      //var nombreape = nombress + " " + apellidoss;
      $("#id_cliente").append("<option value='" + id_client + "' selected>" + nombress + " " + apellidos + "</option>");
      $("#id_cliente").trigger('change');

      //Cerrar Modal
      $('#clienteModal').modal('hide');
      //Agregar NRC y NIT al form de Credito Fiscal
      display_notify(datax.typeinfo, datax.msg);
      $(document).on('hidden.bs.modal', function(e) {
        var target = $(e.target);
        target.removeData('bs.modal').find(".modal-content").html('');
      });
    }
  });
}
$(document).on("click", "#btnEsc2", function(event) {
  $('#clienteModal').modal('hide');
  //reload1();
});

function generar() {
  fechai = $("#fini").val();
  fechaf = $("#fin").val();
  dataTable = $('#editable2').DataTable().destroy()
  dataTable = $('#editable2').DataTable({
    "pageLength": 50,
    "order": [
      [3, 'desc']
    ],
    "processing": true,
    "serverSide": true,
    "ajax": {
      url: "admin_cotizacion_dt.php?fechai=" + fechai + "&fechaf=" + fechaf, // json datasource
      error: function() { // error handling
        $(".editable2-error").html("");
        $("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="9">No se encontró información segun busqueda </th></tr></tbody>');
        $("#editable2_processing").css("display", "none");
        $(".editable2-error").remove();
      }
    },
    "language": {
      "url": "js/Spanish.json"
    },
  });
  dataTable.ajax.reload();

}
function deleted()
{
  var id_cotizacion = $('#id_cotizacion').val();
  var dataString = 'process=deleted' + '&id_cotizacion=' + id_cotizacion;
  $.ajax({
    type: "POST",
    url: "borrar_cotizacion.php",
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      display_notify(datax.typeinfo, datax.msg);
      if (datax.typeinfo != "Error")
      {
        setInterval("location.reload();", 1000);
        $('#btncerr').click();
      }
    }
  });
}
$(document).on('change', '.sel_r', function(event) {
	var a = $(this).closest('tr');
	precio=parseFloat($(this).val());
	a.find('#precio_venta').val(precio);
	a.find("#precio_sin_iva").val(precio/1.13);
	actualiza_subtotal(a);
});
