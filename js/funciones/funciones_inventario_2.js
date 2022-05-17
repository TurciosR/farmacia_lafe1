$(document).ready(function() {
  $('.select').select2();
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

$(document).on('change', '#destino', function(event) {
  var id_ubicacion = $(this).val();

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
// Agregar productos a la lista del inventario
function agregar_producto(id_prod, descrip) {
  id_ubicacion=$("#destino").val();
  var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod + "&id_ubicacion="+id_ubicacion;
  $.ajax({
    type: "POST",
    url: 'ingreso_inventario.php',
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
      var i = data.i;
      var categoria=data.categoria;
      var estante =data.estante;
      var posicion =data.posicion;

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
      tr_add += '<td class="id_p">' + id_prod + '</td>';
      tr_add += '<td>' + descrip + '</td>';
      tr_add += '<td>' + select + '</td>';
      tr_add += '<td class="descp">' + descripcionp + '</td>';
      tr_add += "<td><div class='col-xs-1'>" + unit + "<input type='text'  class='form-control precio_compra' value='" + cp + "' style='width:80px;'></div></td>";
      tr_add += "<td><div class='col-xs-1'><input type='text'  class='form-control precio_venta' value='" + preciop + "' style='width:80px;'></div></td>";
      tr_add += "<td><div class='col-xs-1'><input type='text'  class='form-control cant "+categoria+" ' style='width:60px;'></div></td>";
      tr_add += "<td class='col-xs-2'>" + caduca + '</td>';
      tr_add += "<td>" + estante + '</td>';
      tr_add += "<td>" + posicion + '</td>';
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

            if(categoria==86)
            {
              $(".86").numeric(
                {
                  negative:false,
                  decimalPlaces:4,
                });
            }
            else
            {
              $(".cant").numeric(
                {
                  decimal:false,
                  negative:false,
                });
                $(".86").numeric(
                  {
                    negative:false,
                    decimalPlaces:4,
                  });
            }

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
           window.open('editar_producto.php?id_producto='+id_prod, '_blank');
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
  $("#inventable>tbody tr").each(function()
  {
    var compra = $(this).find(".precio_compra").val();
    var unidad = $(this).find(".unidad").val();
    var venta = $(this).find(".precio_venta").val();
    var cantidad = parseFloat($(this).find(".cant").val());
    var cantidad =round(cantidad,4);
    var vence = $(this).find(".vence").val();
    subtotal = compra * cantidad;
    if (isNaN(cantidad) == true)
    {
      cantidad = 0;
    }
    totalcantidad += cantidad;
    if (isNaN(subtotal) == true)
    {
      subtotal = 0;
    }
    total += subtotal;
  });
  if (isNaN(total) == true)
  {
    total = 0;
  }
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);

  $('#total_dinero').html("<strong>" + total_dinero + "</strong>");
  $('#totcant').html(total_cantidad);

}
// actualize table
$(document).on("click", "#submit1", function()
{
  $('#submit1').attr('disabled', true);
  senddata();
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

    var estante = $(this).find("#estante").val();
    var posicion = $(this).find("#posicion").val();
    if (venta!="" &&parseFloat(venta) > 0 && cant != "" && parseFloat(cant)!=0)
    {
      datos += id_prod + "|" + compra + "|" + venta + "|" + cant + "|" + unidad + "|" + vence + "|" + id_presentacion +"|" + estante +"|" + posicion + "#";
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
  var estante = $('#estante').val();
  var posicion = $('#posicion').val();

  var dataString =
  {
    'process': "insert",
    'datos': datos,
    'cuantos': i,
    'total': total,
    'fecha': fecha1,
    'concepto': concepto,
    'destino': destino,
    'estante': estante,
    'posicion': posicion

  }
  if (!error)
  {
    $.ajax({
      type: 'POST',
      url: "ingreso_inventario.php",
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
  }
  else
  {
    display_notify('Warning', 'Falta completar algun valor de precio o cantidad!');
    $('#submit1').removeAttr('disabled');

  }
}
function reload1()
{
  location.href = "ingreso_inventario.php";
}
$(document).on('change', '.sel', function(event)
{
  var id_presentacion = $(this).val();
  var a = $(this).parents("tr");
  $.ajax({
    url: 'ingreso_inventario.php',
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
