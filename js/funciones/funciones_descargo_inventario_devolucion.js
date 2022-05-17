$(document).ready(function() {
 $('.select').select2();
 //searchFilter();
 $("#origen").change(function(){
  //searchFilter();
  $('#inventable tr').remove("");
  $("#filas").val("0");
  setTimeout(function functionName() {
   totales();
  },250)
 });
 $("#codigo").keyup(function(evt)
 {
  var code = $(this).val();
  if (evt.keyCode == 13)
  {
   if($(this).val()!="")
   {
    agregar_producto(code,"", "C");
   }
   $(this).val("");
  }
 });
 $('html,body').animate({
  scrollTop: $(".focuss").offset().top
 }, 1500);
 $("#scrollable-dropdown-menu #producto_buscar").typeahead({
  highlight: true,
 },
 {
  limit:100,
  name: 'productos',
  display: 'producto',
  source: function show(q, cb, cba) {
     var url = 'autocomplete_producto2.php'+"?query="+q;
     $.ajax({ url: url })
       .done(function(res) {
         cba(JSON.parse(res));
       })
       .fail(function(err) {
         alert(err);
       });
   }
 }).on('typeahead:selected', onAutocompleted);

 function onAutocompleted($e, datum) {
   $('.typeahead').typeahead('val', '');
   var prod0=datum.producto;
    var prod= prod0.split("|");
    var id_prod = prod[0];
    var descrip = prod[1];
    agregar_producto(id_prod, descrip ,"D");
 }
 $("#codigo").focus();
 });
$(function() {
 $(document).on("click", ".btnDelete", function(event) {
  $(this).closest('tr').remove();

  setTimeout(function functionName() {
   totales();

  },250)
 });
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
$(document).keydown(function(e){
 if(e.which == 113){ //F2 Guardar
  $("#submit1").click();
  e.stopPropagation();
  e.preventDefault();
 }
 if(e.which == 115){ //F4 Guardar
  location.replace('dashboard.php');
  e.stopPropagation();
  e.preventDefault();
 }
 if (e.which == 114) { //F3 salir
  if ($('#a').attr('hidden')) {
   $('#a').removeAttr('hidden');
   $('#b').attr('hidden', 'hidden');
   $('#codigo').focus();
  } else {
   $('#b').removeAttr('hidden');
   $('#a').attr('hidden', 'hidden');
   $('#producto_buscar').focus();
  }
  e.stopPropagation();
  e.preventDefault();
 }
 if (e.which == 46) /*suprimir*/
 {
  $("#inventable tr:first-child").remove();
  totales();
  var filas = $("#filas").val();
  filas --;
  $("#filas").val(filas);
  e.preventDefault();
  if ($('#b').attr('hidden')) {
   $('#codigo').focus();
  } else {
   $('#producto_buscar').focus();
  }
 }
});

// Agregar productos a la lista del inventario
function agregar_producto(id_prod, descrip, tipo) {
 var ubicacion = $("#origen").val();
 var dataString = 'process=consultar_stock&tipo='+tipo+'&id_producto='+id_prod+'&ubicacion='+ubicacion;
 $.ajax({
  type: "POST",
  url: 'descargo_inventario_devolucion.php',
  data: dataString,
  dataType: 'json',
  success: function(data)
  {
   if(data.typeinfo == "Success")
   {
    var cp = data.costop;
    var perecedero = data.perecedero;
    var select = data.select;
    var preciop = data.preciop;
    var unidadp = data.unidadp;
    var descripcionp = data.descripcionp;
    var i = data.i;
    var stock = data.stock;
    var descrip = data.descrip;
    var id_prod = data.id_p;
    var categoria=data.categoria;
    if (perecedero == 1)
   {
    caduca = "<div class='form-group'><input type='text' class='datepicker form-control vence' value=''></div>";
   }
   else
   {
    caduca = "<input type='hidden' class='vence' value='NULL'>";
   }
   var filas = $("#filas").val()
   filas ++;
   var unit = "<input type='hidden' class='unidad' value='"+unidadp+"'>";
   var tr_add = "";
   tr_add += '<tr class="row100" id="'+filas+'">';
   tr_add += '<td style="display:none;" class="col-lg-1 id_p">'+id_prod+'</td>';
   tr_add += '<td class="col-lg-3">'+descrip+'</td>';
   tr_add += '<td class="col-lg-1">'+select+'</td>';
   tr_add += '<td class="descp col-lg-1">'+descripcionp+'</td>';
   tr_add += "<td class='col-lg-1'><input type='text' class='form-control cant' style='width:100%'></div></td>";
   tr_add += "<td class='col-lg-1'><input type='text' class='form-control lote' style='width:100%'></div></td>";
   tr_add += "<td class='col-lg-1'><input type='text' class='form-control venci cal' style='width:100%'></div></td>";
   tr_add += "<td class='col-lg-1'><div >" + unit + "<input type='text'  class='form-control precio_compra' value='" + cp + "' style='width:80px;' ></div></td>";
   tr_add += "<td class='col-lg-1' style='display:none;'><div ><input type='text'  class='form-control precio_venta' value='" + preciop + "' style='width:80px;' readOnly></div></td>";
   tr_add += "<td class='col-lg-1'><div><input type='text' class='form-control exis text-right' value='"+round(stock,0)+"' style='width:80px;' readOnly></div></td>";
   tr_add += "<td class=' col-lg-1 Delete text-center'><a href='#'><i class='fa fa-trash'></i></a></td>";
   tr_add += '</tr>';
   $("#inventable").prepend(tr_add);
   $(".sel").select2();

   /*que no se vayan letras*/
   $("#filas").val(filas)
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
    });
    $('.cal').datepicker({
      format: 'yyyy-mm-dd',
      startDate: '1d'
    });

    $("#inventable #"+filas).find(".sel").select2("open");
   }
   else
   {
    display_notify(data.typeinfo, data.msg);
   }
  }
 });
 totales();
}
$(document).on('select2:close', '.sel', function()
{
		var tr =$(this).parents("tr");
		tr.find(".cant").focus();
});
$(document).on('keyup', '.cant', function(evt) {
  fila = $(this).closest('tr');
  id_producto = fila.find('.id_producto').val();
  existencia = parseInt(fila.find('.exis').val());
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

  if (evt.keyCode == 13) {
    num = parseFloat($(this).val());
    if (isNaN(num)) {
      num = 0;
    }
    if ($(this).val() != "" && num > 0)
    {
      if ($('#b').attr('hidden')) {
        $('#codigo').focus();
      } else {
        $('#producto_buscar').focus();
      }
    }
  }
});
//Evento que se activa al perder el foco en precio de venta y cantidad:
$(document).on("blur", "#inventable", function() {
 totales();
});

// Evento que selecciona la fila y la elimina de la tabla
$(document).on("click", ".Delete", function()
{
 $(this).parents("tr").remove();
 totales();
});

$(document).on('keyup', '.precio_compra', function(event) {
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
  $("#inventable tr").each(function()
  {
    var tr = $(this);
    var compra = parseFloat($(this).find(".precio_compra").val());
    var unidad = $(this).find(".unidad").val();
    var venta = parseFloat($(this).find(".precio_venta").val());
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
  total_dinero = round(total,4);
  total_cantidad = round(totalcantidad,4);

  $('#total_dinero').html("<strong>" + total_dinero.toFixed(4) + "</strong>");
  $('#totcant').html(total_cantidad);

}
// actualize table
$(document).on("click", "#submit1", function()
{
  if($("#inventable tr").length>0)
  {
    senddata();
  }
  else {
    display_notify("Error", "Debe agregar productos a la lista");
    $('#submit1').attr('disabled', false);
  }
});

function senddata()
{
 $('#submit1').prop('disabled', true);
 //Calcular los valores a guardar de cada item del inventario
 var i = 0;
 var error = false;
 var datos = "";
 var origen =$('#origen').val();
 var iden = $("select#tipo option:selected").val(); //get the value

 var id_proveedor = $("#id_proveedor").val();
 var numero_doc = $("#numero_doc").val();
 var tipo_doc = $("#doc_tipe").val();
 $("#inventable tr").each(function()
 {
   var id_prod = $(this).find(".id_p").text();
   var id_presentacion = $(this).find(".sel").val();
   var compra = $(this).find(".precio_compra").val();
   var unidad = $(this).find(".unidad").val();
   var venta = $(this).find(".precio_venta").val();
   var cant = $(this).find(".cant").val();

   var lote = $(this).find(".lote").val();
    var venci = $(this).find(".venci").val();

   var vence = "";
   if (cant != "" && parseInt(cant)>0)
   {
    datos += id_prod+"|"+compra+"|"+venta+"|"+cant+"|"+unidad+"|"+vence+"|"+id_presentacion+"|"+lote+"|"+venci+"#";
    i = i+1;
   }
   else
   {
    error = true;
   }

 });

 var total = $('#total_dinero').text();
 var concepto = $('#concepto').val();
 var fecha1 = $('#fecha1').val();

 if (id_proveedor=="") {
   error = true;
 }

 if (numero_doc=="") {
   error = true;
 }

 if (tipo_doc=="") {
   error = true;
 }

 var dataString =
 {
  'process': "insert",
  'datos': datos,
  'cuantos': i,
  'total': total,
  'fecha': fecha1,
  'concepto': concepto,
  'origen': origen,
  'iden': iden,
  'id_proveedor' : id_proveedor,
  'numero_doc' : numero_doc,
  'tipo_doc' : tipo_doc,
 }
 if (!error&&i>0)
 {
  $.ajax({
   type: 'POST',
   url: "descargo_inventario_devolucion.php",
   data: dataString,
   dataType: 'json',
   success: function(datax)
   {
    display_notify(datax.typeinfo, datax.msg);
    if(datax.typeinfo == "Success")
    {
     setInterval("reload1();", 1000);
    }
    else
    {
      $('#submit1').attr('disabled', false);
    }
  }
  });
 }
 else
 {
  display_notify('Warning', 'Falta completar algun valor !');
  $('#submit1').prop('disabled', "");
 }
}
$(document).on('select2:close', '.sel', function()
{
		var tr =$(this).parents("tr");
		tr.find(".cant").focus();
});
$(document).on('keyup', '.cant', function(event)
{
 fila = $(this).closest('tr');
 id_producto = fila.find('.id_producto').val();
 existencia = parseInt(fila.find('.exis').text());
 a_cant=$(this).val();
 unidad= parseInt(fila.find('.unidad').val());
 a_cant=parseInt(a_cant*unidad);

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
 location.href = "admin_devolucion.php";
}
$(document).on('change', '.sel', function(event)
{
  var id_presentacion = $(this).val();
  var a = $(this).parents("tr");
  $.ajax({
    url: 'descargo_inventario_devolucion.php',
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

      fila = a;
      id_producto = fila.find('.id_producto').val();
      existencia = parseInt(fila.find('.exis').val());
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
