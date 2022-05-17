$(document).ready(function() {
  $('.select').select2();
  $('#numero_dias').numeric({decimal:false,negative:false});
  $("#flete").numeric({decimalPlaces:2,negative:false});

  $("#descuento").numeric({decimalPlaces:2,negative:false});
  $("#producto_buscar").typeahead({
    source: function(query, process) {
      $.ajax({
        url: 'autocomplete_producto.php',
        type: 'POST',
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
      agregar_producto(id_prod, descrip);
    }
  });

   $('.navbar-header .btn-primary').click();

});

$(document).on('change', '#destino', function(event) {
  $("#inventable>tbody").html("");

  setTimeout(
    function() {
      totales();
    },
    300
  );
});

$(document).on('change', '#estante', function(event) {

  tr=$(this).closest('tr');
  var id_estante = $(this).val();
  tr.find("#posicion").val("");
  tr.find("#posicion").empty().trigger('change');
  $.ajax({
    type: "POST",
    url: "ingreso_inventario.php",
    data: "process=val2&id_estante=" + id_estante,
    dataType: "JSON",
    success: function(datax) {
      if (datax.typeinfo == "Success") {
        tr.find("#posicion").html(datax.opt);
        tr.find("#posicion").trigger('change');
      }
    }
  });
});

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

// Agregar productos a la lista del inventario
function agregar_producto(id_prod, descrip) {
  id_ubicacion=$("#destino").val();
  var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod +"&id_ubicacion="+id_ubicacion;
  $.ajax({
    type: "POST",
    url: 'compras.php',
    data: dataString,
    dataType: 'json',
    success: function(data)
    {
      var cp = data.costop;
      var perecedero = data.perecedero;
      var select = data.select;
      var preciop = data.preciop;
      var unidadp = data.unidadp;
      var descripcionp = data.descripcionp;
      var exento =data.exento;
      var estante =data.estante;
      var posicion =data.posicion;
      var i=data.i;
      if (perecedero == 1)
      {
        caduca = "<div class='form-group'><input type='text' class='datepicker form-control vence' value=''></div>";
      }
      else
      {
        caduca = "<input type='hidden' class='vence' value='NULL'>";
      }
      var unit = "<input type='hidden' class='unidad' value='" + unidadp + "'>";
      var tr_add = "";

      tr_add += '<tr>';
      tr_add += '<td style="display:none;" class="id_p">' + id_prod + '</td>';
      tr_add += '<td>' + descrip + '</td>';
      tr_add += '<td>' + select + '</td>';
      tr_add += '<td style="display:none;" class="descp">' + descripcionp + '</td>';
      tr_add += "<td><div class=''>" + unit + "<input type='text'  class='form-control precio_compra' value='" + cp + "' style='width:80px;'></div></td>";
      tr_add += "<td><div class=''>"+"<input type='hidden' class='exento' value='"+exento+"'>"+"<input type='text'  class='form-control precio_venta' value='"+ preciop + "' style='width:80px;'></div></td>";
      tr_add += "<td><div class=''><input type='text'  class='form-control cant' style='width:60px;'></div></td>";
      tr_add += "<td class='col-xs-2'>" + caduca + '</td>';
      tr_add += "<td style='width:60px;'>" + estante + '</td>';
      tr_add += "<td style='width:60px;'>" + posicion + '</td>';
      tr_add += "<td class='Delete text-center'><a href='#'><i class='fa fa-trash'></i></a></td>";
      tr_add += '</tr>';
      if(i!=0)
      {
        if (id_prod != "")
        {
          $("#inventable").prepend(tr_add);
          $(".sel").select2();
          $(".sel2").select2();

          /*que no se vayan letras*/
          $(".precio_compra").numeric(
            {
              negative:false,
              decimalPlaces:4,
            });

          $(".precio_venta").numeric(
            {
              negative:false,
              decimalPlaces:4,
            });

          $(".cant").numeric(
            {
              decimal:false,
              negative:false,
              decimalPlaces:2,
            });
        }
        $('.datepicker').datepicker({
          format: 'yyyy-mm-dd',
          startDate: '1d'
        });
      }
      else
      {
        swal({
           title: "Error, producto sin presentaciones?",
           text: "Si presiona OK sera redireccionado para asignar presentaciones y costos ",
           type: "warning",
           showCancelButton: true
         }, function() {
           // Redirect the user
           //window.location.href = "";
           window.open('http://laboratorio.apps-oss.com/editar_producto.php?id_producto='+id_prod, '_blank');
         });
      }
    }
  });
  totales();
}
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#inventable", function() {
  totales();
});
$(document).on("keyup", ".cant, .precio_compra, .precio_venta", function() {
  totales();
});
// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function()
{
  $(this).parents("tr").remove();
  totales();
});
//Calcular Totales del grid
function totales()
{
  var subtotal = 0;
  var total = 0;
  var totalcantidad = 0;
  var subcantidad = 0;
  var total_dinero = 0;
  var total_cantidad = 0;
  var sub_exento=0;
  $("#inventable>tbody tr").each(function()
  {
    var compra = $(this).find(".precio_compra").val();
    var unidad = $(this).find(".unidad").val();
    var venta = $(this).find(".precio_venta").val();
    var cantidad = parseInt($(this).find(".cant").val());
    var vence = $(this).find(".vence").val();
    var exento = parseInt($(this).find(".exento").val());

    if (isNaN(cantidad) == true)
    {
      cantidad = 0;
    }
    subtotal = compra * cantidad;

    totalcantidad += cantidad;
    if (isNaN(subtotal) == true)
    {
      subtotal = 0;
    }

    if(exento==1)
    {
      sub_exento=sub_exento+subtotal;
    }
    else
    {
      total += subtotal;
    }

  });
  if (isNaN(total) == true)
  {
    total = 0;
  }
  sumas_sin_iva=total;
  sumas_sin_iva=round(total, 2);

  if(isNaN(sub_exento))
  {
    sub_exento=0;
  }

  tipo_doc=$('#tipo_doc').val();

  percepcion = $('#percepcion').val();

  var monto_percepcion = $('#monto_percepcion').val();
  var iva = $('#porc_iva').val();

  total_percepcion=0;
  var flete = parseFloat($("#flete").val());
  if (isNaN(flete)) {
    flete=0.00;
  }

  var descuento=parseFloat($("#descuento").val());
  if (isNaN(parseFloat(descuento))) {
    descuento=0.00;
  }

  var aplicar = $("#descuento_select").val();

  total=total+flete;

  if (aplicar=='g') {
    total=total-descuento;
  }

  iva = round((total * iva), 4);
  sub_exento = round(sub_exento, 2);

  if (total >= monto_percepcion)
    total_percepcion = round((total * percepcion), 4);

  total += total_percepcion;


  if(tipo_doc=='CCF')
  {
    total += iva;
  }
  else
  {
    iva=0;
  }




  total+= sub_exento;
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);

  if (aplicar=='f') {
    total_dinero=total_dinero-descuento;
    total_dinero = round(total_dinero,2);
  }

  $('#totcant').html(total_cantidad);
  $('#sumas_sin_iva').html(round(sumas_sin_iva,2));
  $('#subtotal').html(round((sumas_sin_iva+iva), 2));
  $('#iva').html(round(iva,2));
  $('#venta_exenta').html(sub_exento);
  $('#total_percepcion').html(round(total_percepcion, 2));
  $('#total_dinero').html(total_dinero);


}

$(document).on('keyup', '#flete', function(event) {
  totales();
});
$(document).on('keyup', '#descuento', function(event) {
  totales();
});
$(document).on('change', '#descuento_select', function(event) {
  totales();
});
// actualize table
$(document).on("click", "#submit1", function()
{
  $('#submit1').prop('disabled', true);
  senddata();
});

$(document).on('change', '#tipo_doc', function(event) {
  totales();
  /* Act on the event */
});
$(document).on('change', '#id_proveedor', function(event) {

  id_proveedor=$("#id_proveedor").val();

  $.ajax({
    url: 'compras.php',
    type: 'POST',
    data: 'process=datos_proveedores&id_proveedor=' + id_proveedor,
    dataType: 'JSON',
    async: true,
    success: function(data) {

      var percepcion = data.percepcion;

      $('#percepcion').val(percepcion);
      totales();
    }
  });


});

function senddata()
{
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var error  = false;
  var datos = "";
  var id = $("select#tipo_entrada option:selected").val(); //get the value

  $("#inventable>tbody tr").each(function()
  {
    var id_prod = $(this).find(".id_p").text();
    var id_presentacion = $(this).find(".sel").val();
    var compra = $(this).find(".precio_compra").val();
    var unidad = $(this).find(".unidad").val();
    var venta = $(this).find(".precio_venta").val();
    var cant = $(this).find(".cant").val();
    var vence = $(this).find(".vence").val();
    var exento = $(this).find(".exento").val();
    var estante = $(this).find("#estante").val();
    var posicion = $(this).find("#posicion").val();

    if (venta!="" &&parseFloat(venta) > 0 && cant != "" && parseInt(cant)>0)
    {
      datos += id_prod + "|" + compra + "|" + venta + "|" + cant + "|" + unidad + "|" + vence + "|" + id_presentacion + "|" + exento +"|" + estante +"|" + posicion + "#";
      i = i + 1;
    }
    else
    {
      error = true;
    }
  });

  var total = $('#total_dinero').text();
  var concepto = $('#concepto').val();
  var fecha1 = $('#fecha1').val();
  var destino = $('#destino').val();

  var flete = $("#flete").val();

  if (isNaN(parseFloat(flete))) {
    flete=0.00;
  }

  var descuento=$("#descuento").val();
  if (isNaN(parseFloat(descuento))) {
    descuento=0.00;
  }

  var tipo_descuento=$("#descuento_select").val();

  var proveedor=$('#id_proveedor').val();
  if (proveedor!=""&&proveedor!=0)
  {

  }
  else
  {
    error=true;
  }

  var tipo_doc =$('#tipo_doc').val();
  var numero_doc=$('#numero_doc').val();
  if (numero_doc!=""&&numero_doc!=0)
  {

  }
  else
  {
    error=true;
  }

  var sumas_sin_iva=$('#sumas_sin_iva').html();
  var subtotal=$('#subtotal').html();
  var iva=$('#iva').html();
  var venta_exenta=$('#venta_exenta').html();
  var total_percepcion=$('#total_percepcion').html();

  var dias_credito= parseInt($('#numero_dias').val());
  if(isNaN(dias_credito))
  {
    dias_credito=0;
  }

  if(i==0)
  {
    error=true;
  }





  var dataString =
  {
    'process': "insert",
    'datos': datos,
    'cuantos': i,
    'total': total,
    'fecha': fecha1,
    'concepto': concepto,
    'destino': destino,
    'proveedor':proveedor,
    'tipo_doc':tipo_doc,
    'numero_doc':numero_doc,
    'sumas_sin_iva':sumas_sin_iva,
    'subtotal':subtotal,
    'iva':iva,
    'venta_exenta':venta_exenta,
    'total_percepcion':total_percepcion,
    'dias_credito':dias_credito,
    'flete':flete,
    'descuento':descuento,
    'tipo_descuento':tipo_descuento,
  }
  if (!error)
  {
    swal({
        title: "¿Esta, seguro?",
        text: "Este proceso no puede ser revertido, si esta seguro presione OK y se procedera.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '',
        confirmButtonText: 'Si, Estoy seguro.',
        cancelButtonText: "No, cancelar y revisar.",
        closeOnConfirm: true,
        closeOnCancel: true
     }, function(isConfirm) {
       if (isConfirm){
         $.ajax({
           type: 'POST',
           url: "compras.php",
           data: dataString,
           dataType: 'json',
           success: function(datax)
           {
             display_notify(datax.typeinfo, datax.msg);
             if(datax.typeinfo == "Success")
             {
               setInterval("reload1();", 1000);
             }
           }
         });
        } else {
          $('#submit1').prop('disabled', "");
        }

     });
  }
  else
  {
    $('#submit1').prop('disabled', "");
    display_notify('Warning', 'Falta completar algun valor');
  }
}
function reload1()
{
  location.href = "compras.php";
}
$(document).on('change', '.sel', function(event)
{
  var id_presentacion = $(this).val();
  var a = $(this).parents("tr");
  $.ajax({
    url: 'compras.php',
    type: 'POST',
    dataType: 'json',
    data: 'process=getpresentacion' + "&id_presentacion=" + id_presentacion,
    success: function(data)
    {
      a.find('.descp').html(data.descripcion);
      a.find('.precio_venta').val(data.precio);
      a.find('.precio_compra').val(data.costo);
      a.find('.unidad').val(data.unidad);
      a.find('.precio_compra').val(data.costo);
    }
  });
  setTimeout(function() {
    totales();
  }, 1000);
});
function round(value, decimals)
{
  return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}
