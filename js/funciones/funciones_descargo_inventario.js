$(document).ready(function() {
  $('.select').select2();
  searchFilter();
  $("#origen").change(function(){
    searchFilter();
    $('loadtable>tbody').html("");
  });
  $('#producto_buscar').on('keyup', function(event) {
     searchFilter();
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
function searchFilter(page_num)
{
  page_num = page_num ? page_num : 0;
  var producto_buscar = $('#producto_buscar').val();
  var origen = $('#origen').val();
  //var limite = $('#limite').val();
  getData(producto_buscar,origen,page_num)
}

function getData(producto_buscar,origen,page_num){
  var sortBy = 'asc';//$('#sortBy').val();
  var records = 50;//$('#records').val();
  urlprocess = "descargo_inventario.php";
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: {
      process: 'traerdatos',
      page: page_num,
      producto_buscar: producto_buscar,
      origen: origen,
      sortBy: sortBy,
      records: records
    },
    beforeSend: function()
    {
      $('.loading-overlay').show();
    },
    success: function(html)
    {
        $('#mostrardatos').find('input:checkbox:not(:checked)').closest('tr').remove();
        $('#mostrardatos').append(html);
        $(".sel").select2();
        $(".cant").numeric({decimal:false,negative:false});
    }
  });
  $.ajax({
    type: 'POST',
    url: "descargo_inventario.php",
    data: {
      process: 'traerpaginador',
      page: page_num,
      producto_buscar: producto_buscar,
      origen: origen,
      sortBy: sortBy,
      records: records
    },
    success: function(value)
    {
      $('#paginador').html(value);
    }
  });
}

// Agregar productos a la lista del inventario
function agregar_producto(id_prod, descrip) {
  var dataString = 'process=consultar_stock' + '&id_producto=' + id_prod;
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
      tr_add += "<td><div class='col-xs-1'><input type='text'  class='form-control cant' style='width:60px;'></div></td>";
      tr_add += "<td class='col-xs-2'>" + caduca + '</td>';
      tr_add += "<td class='Delete text-center'><a href='#'><i class='fa fa-trash'></i></a></td>";
      tr_add += '</tr>';
      if (id_prod != "")
      {
        $("#inventable").prepend(tr_add);
        $(".sel").select2();

        /*que no se vayan letras*/
        $(".precio_compra").numeric(
          {
            negative:false,
            decimalPlaces:2,
          });

        $(".precio_venta").numeric(
          {
            negative:false,
            decimalPlaces:2,
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
  });
  totales();
}
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#inventable", function() {
  totales();
});
$(document).on("keyup", ".precio_compra, .precio_venta", function() {
  totales();
});

// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function()
{
  $(this).parents("tr").remove();
  totales();
});
$(document).on("click", ".cheke", function()
{
  var tr = $(this).parents("tr");
  if($(this).is(":checked"))
  {
    tr.find(".cant").attr("readOnly", false);
  }
  else
  {
    tr.find(".cant").attr("readOnly", true);
  }
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
  $("#loadtable>tbody tr").each(function()
  {
    if($(this).find('.cheke').prop('checked'))
    {
      var compra = $(this).find(".precio_compra").text();
      var unidad = $(this).find(".unidad").val();
      var venta = $(this).find(".precio_venta").text();
      var cantidad = parseInt($(this).find(".cant").val());

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
      total += subtotal;
    }
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
  senddata();
});

function senddata()
{
  $('#submit1').prop('disabled', true);
  //Calcular los valores a guardar de cada item del inventario
  var i = 0;
  var error  = false;
  var datos = "";
  var origen =$('#origen').val();
  var iden = $("select#tipo option:selected").val(); //get the value

  $("#loadtable>tbody tr").each(function()
  {
    if($(this).find('.cheke').prop('checked'))
    {
      var id_prod = $(this).find(".id_producto").val();
      var id_presentacion = $(this).find(".sel").val();
      var compra = $(this).find(".precio_compra").text();
      var unidad = $(this).find(".unidad").val();
      var venta = $(this).find(".precio_venta").text();
      var cant = $(this).find(".cant").val();
      var vence = "";
      if (venta!="" &&parseFloat(venta) > 0 && cant != "" && parseInt(cant)>0)
      {
        datos += id_prod + "|" + compra + "|" + venta + "|" + cant + "|" + unidad + "|" + vence + "|" + id_presentacion + "#";
        i = i + 1;
      }
      else
      {
        error = true;
      }
    }
  });

  var total = $('#total_dinero').text();
  var concepto = $('#concepto').val();
  var fecha1 = $('#fecha1').val();

  var dataString =
  {
    'process': "insert",
    'datos': datos,
    'cuantos': i,
    'total': total,
    'fecha': fecha1,
    'concepto': concepto,
    'origen': origen,
    'iden': iden
  }
  if (!error&&i>0)
  {
    $.ajax({
      type: 'POST',
      url: "descargo_inventario.php",
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
    display_notify('Warning', 'Falta completar algun valor de cantidad !');
    $('#submit1').prop('disabled', "");
  }
}

$(document).on('keyup', '.cant', function(event)
{
  fila = $(this).closest('tr');
  id_producto = fila.find('.id_producto').val();
  existencia = parseInt(fila.find('.exis').text());
  a_cant=$(this).val();
  unidad= parseInt(fila.find('.unidad').val());
  a_cant=parseInt(a_cant*unidad);

  console.log(a_cant);
  a_asignar=0;

  $('table tr').each(function(index) {

    if($(this).find('.id_producto').val()==id_producto)
    {
      t_cant=parseInt($(this).find('.cant').val());
      if(isNaN(t_cant))
      {
        t_cant=0;
      }
      t_unidad=parseInt($(this).find('.unidad').val());
      if(isNaN(t_unidad))
      {
        t_unidad=0;
      }
      t_cant=parseInt((t_cant*t_unidad));
      a_asignar=a_asignar+t_cant;
      a_asignar=parseInt(a_asignar);
    }
  });
  console.log(existencia);
  console.log(a_asignar);

  if(a_asignar>existencia)
  {
      val = existencia-(a_asignar-a_cant);
      val = val/unidad;
      val=Math.trunc(val);
      val =parseInt(val);
      $(this).val(val);
      setTimeout(function() {totales();}, 1000);
  }
  else
  {
    totales();
  }

});

function reload1()
{
  location.href = "descargo_inventario.php";
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
      a.find('.precio_venta').text(data.precio);
      a.find('.unidad').val(data.unidad);
      a.find('.precio_compra').text(data.costo);

      fila = a;
      id_producto = fila.find('.id_producto').val();
      existencia = parseInt(fila.find('.exis').text());
      a_cant=parseInt(fila.find('.cant').val());
      unidad= parseInt(fila.find('.unidad').val());

      a_cant=parseInt(a_cant*data.unidad);
      console.log(a_cant);

      a_asignar=0;

      $('table tr').each(function(index) {

        if($(this).find('.id_producto').val()==id_producto)
        {
          t_cant=parseInt($(this).find('.cant').val());
          if(isNaN(t_cant))
          {
            t_cant=0;
          }
          t_unidad=parseInt($(this).find('.unidad').val());
          if(isNaN(t_unidad))
          {
            t_unidad=0;
          }
          t_cant=parseInt((t_cant*t_unidad));
          a_asignar=a_asignar+t_cant;
          a_asignar=parseInt(a_asignar);
        }
      });
      console.log(existencia);
      console.log(a_asignar);

      if(a_asignar>existencia)
      {
          val = existencia-(a_asignar-a_cant);
          val = val/unidad;
          val=Math.trunc(val);
          val =parseInt(val);
          fila.find('.cant').val(val);
      }

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
