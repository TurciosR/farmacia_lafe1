$(document).ready(function() {
  generar();

  $('#minimo').numeric({
    negative: false,
    decimal: false
  });
  $('#unidad_pre').numeric({
    negative: false,
    decimal: false
  });
  $('#precio_pre').numeric({
    negative: false,
    decimalPlaces: 4
  });
  $('#costo_pre').numeric({
    negative: false,
    decimalPlaces: 4
  });
  $('#id_categoria').select2();
  $('#id_proveedor').select2();
  $(".select2").select2({
    placeholder: {
      id: '',
      text: 'Seleccione',
    },
    allowClear: true,
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

  $(document).on("click", "#add_p", function(event) {
    valor = $("#pr").val();
    unidad_pre =$("#un").val();
    val="";
    $("#presentacion_table tr").each(function() {
      var id_pp = $(this).find(".presentacion").val();
      if (id_pp == valor) {
        if (unidad_pre == $(this).find(".unidad_p").text()) {
          a=$(this).closest('tr');

          $("#precios>tbody tr").each(function() {
            val+=$(this).find("td:eq(0)").text()+"|"+$(this).find("td:eq(1)").text()+"|"+$(this).find("td:eq(2)").text()+"#";
          });
          a.find(".precios_pre").val(val);
        }
      }

    });
    $(".close").click();
  });

});

$(document).on('click', '#add', function(event) {
  var id_presentacion = $("#presen").val();
  var desde = $("#desde").val();
  var hasta = $("#hasta").val();
  var precio = $("#precio").val();
  var id_producto = $("#id_producto").val();

  err = 0;
  msg = "";

  if (desde != "") {
    if (hasta != "") {
      if (precio != "") {
        if(id_producto!=0)
        {
          $.ajax({
            url: 'precio_producto.php',
            type: 'POST',
            dataType: 'json',
            data: "process=" + "insert" + "&id_presentacion=" + id_presentacion + "&desde=" + desde + "&hasta=" + hasta + "&precio=" + precio + "&id_producto=" + id_producto,
            success: function(datax) {
              $("#desde").val("");
              $("#hasta").val("");
              $("#precio").val("");

              display_notify(datax.typeinfo, datax.msg);
              $.ajax({
                url: 'precio_producto.php',
                type: 'POST',
                dataType: 'json',
                data: "process=" + "change" + "&id_presentacion=" + id_presentacion,
                success: function(datax) {
                  $("#precios>tbody").html(datax.valores);

                }
              });
            }
          });
        }
        else
        {
          $("#desde").val("");
          $("#hasta").val("");
          $("#precio").val("");
          tar="<tr><td>"+desde+"</td><td>"+hasta+"</td><td>"+precio+"</td><td class='text-center'>"+"<a class=' Delete'><i class='fa fa-trash'></i></a>"+"</td></tr>"
          $("#precios>tbody").append(tar);
        }

      } else {
        err = 1;
        msg = "No digito precio";
      }
    } else {
      err = 1;
      msg = "No digito hasta";

    }
  } else {
    err = 1;
    msg = "No digito desde";
  }

  if (err == 1) {
    display_notify("Error", msg);
  }


});

$(document).on('click', '.del', function(event) {
  var id_presentacion = $("#presen").val();
  var id_producto = $("#id_producto").val();

  var id_prepd = $(this).parents('tr').find('.id_prepp').val();
  console.log($(this).parents('tr').find("td:eq(0) input").val());

  $.ajax({
    url: 'precio_producto.php',
    type: 'POST',
    dataType: 'json',
    data: "process=" + "del" + "&id_prepd=" + id_prepd + "&id_producto=" + id_producto,
    success: function(datax) {

      display_notify(datax.typeinfo, datax.msg);
      $.ajax({
        url: 'precio_producto.php',
        type: 'POST',
        dataType: 'json',
        data: "process=" + "change" + "&id_presentacion=" + id_presentacion,
        success: function(datax) {
          $("#precios>tbody").html(datax.valores);

        }
      });
    }
  });

});

function generar() {
  dataTable = $('#editable2').DataTable().destroy()
  dataTable = $('#editable2').DataTable({
    "pageLength": 50,
    "order": [
      [0, 'asc'],
      [1, 'asc']
    ],
    "processing": true,
    "serverSide": true,
    "ajax": {
      url: "admin_producto_dt.php",

      error: function() { // error handling
        //$(".editable2-error").html("");
        $("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
        $("#editable2_processing").css("display", "none");
        $(".editable2-error").remove();
      }
    },
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

$(document).on('click', '#submit1', function(event) {
  var descripcion = $('#descripcion').val();
  var proveedor = $('#proveedor').val();
  var id_categoria = $('#id_categoria').val();

  if(descripcion!="")
  {
    if(proveedor!="")
    {
      if(id_categoria!="")
      {
        senddata();
      }
      else
      {
        display_notify("Error","Falta seleccionar una categoria");

      }
    }
    else
    {
      display_notify("Error","Falta seleccionar el proveedor");

    }
  }
  else
  {
    display_notify("Error","Falta la descripcion");

  }
});


function senddata() {
  //var name=$('#name').val();
  if ($("#presentacion_table tr").length > 0) {
    var minimo = $('#minimo').val();
    var descripcion = $('#descripcion').val();
    var barcode = $('#barcode').val();
    var proveedor = $('#proveedor').val();
    var marca = $('#marca').val();
    var id_categoria = $('#id_categoria').val();
    var lista = "";
    var cuantos = 0;

    err=0;


    //Get the value from form if edit or insert
    var process = $('#process').val();
    var perecedero = $('#perecedero:checked').val();
    var exento = $('#exento:checked').val();
    var composicion =$('#composicion').val();


    if (process == 'insert') {
      var id_producto = 0;
      var urlprocess = 'agregar_producto.php';
    }
    if (process == 'edited') {
      var estado = $('#activo:checked').val();
      if (estado == undefined) {
        estado = 0;
      } else {
        estado = 1;
      }
      var id_producto = $('#id_producto').val();
      var urlprocess = 'editar_producto.php';
    }
    $("#presentacion_table tr").each(function() {
			var exis = $(this).attr("class");
      var id_pp = $(this).find(".presentacion").val();
      var des = $(this).find(".descripcion_p").html();
      var unidad_p = $(this).find(".unidad_p").html();
      var precio_p = 0;
      var costo = $(this).find(".costo").html();
      var bar =$(this).find(".bar").html();
      var precios_pre=$(this).find(".precios_pre").val();
      var precio_m=$(this).find('.pre_m').text();
      if (process == 'insert') {
        precios_pre=""
        for (var l = 1; l < 8; l++) {
          desde=0;
          hasta=0;
          if (l==1) {
            desde=0;
            hasta=3;
          }
          else {
            if (l==2) {
              desde=1;
              hasta=6;
            }
            else {
              if (l==3) {
                desde=1;
                hasta=12;
              }
              else {
                desde=1;
                hasta=(999-7+l);

              }

            }
          }
          va=".pre"+l;
          numv=$(this).find($(va)).text();

          if (isNaN(parseFloat(numv))) {
            numv=0.00;
          }
          precios_pre+=desde+"|"+hasta+"|"+numv+"#";
        }

      }
			if(exis == 'exis')
		 	{
		 		var id_prp = $(this).find(".id_pres_prod").val();
		 	}
		 	else
		 	{
		 		var id_prp = 0;
		 	}
      console.log(precios_pre);
      if (precios_pre!="") {
        lista += id_pp + "," + des + "," + unidad_p + "," + precio_p + "," + id_prp + "," + costo + "," + bar+"," + precios_pre+"," + precio_m+";";
        cuantos += 1;
      }
      else
      {
        err=1;
      }

    });
    var dataString = 'process=' + process + '&id_producto=' + id_producto + '&barcode=' + barcode + '&descripcion=' + descripcion;
    dataString += '&exento=' + exento + '&proveedor=' + proveedor + '&id_categoria=' + id_categoria + '&perecedero=' + perecedero + '&lista=' + lista;
    dataString += '&marca=' + marca + '&minimo=' + minimo + '&cuantos=' + cuantos + '&estado=' + estado+ '&composicion=' + composicion;;
    if(err==0)/**/
    {
      $.ajax({
        type: 'POST',
        url: urlprocess,
        data: dataString,
        dataType: 'json',
        success: function(datax) {
          process = datax.process;
          id_producto2 = datax.id_producto;
          //var maxid=datax.max_id;
          display_notify(datax.typeinfo, datax.msg);

          if (datax.typeinfo == "Success") {
            setInterval("reload1();", 1000);
          }
        }
      });
    }
    else
    {
      display_notify("Warning", "Debe ingresar al menos un precio");
    }

  } else {
    display_notify("Warning", "Debe ingresar al menos una presentacion");
  }
}

function reload1() {
  location.href = 'admin_producto.php';
}

$(document).on('click', '.elmpre', function(event) {

  var tr = $(this).parents("tr").find('.id_pres_prod').val();

  console.log(tr);

  swal({
    title: "¿Esta seguro?",
    text: "Esto eliminara esta presentacion de manera permanente",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '',
    confirmButtonText: 'Borrar',
    cancelButtonText: 'Cancelar',
    closeOnConfirm: false,
    closeOnCancel: true
  }, function(isConfirm) {
    if (isConfirm) {
      $.ajax({
        url: 'editar_producto.php',
        type: 'POST',
        dataType: 'json',
        data: {
          process: "borrar_presentacion",
          id_presentacion: tr
        },
        success: function(datax) {
          if (datax.typeinfo == "Success") {
            display_notify(datax.typeinfo, datax.msg);
            setInterval("location.reload()", 1000);
          }
          else {
            display_notify(datax.typeinfo, datax.msg);
          }
        }
      });
    } else {}

  });
});
$(document).on("click", ".deactive", function(){
	var id = $(this).attr("id");
	var td = $(this).parents("td");
	var tr = $(this).parents("tr");
	var fila = "<a class='activate' id='"+id+"'><i class='fa fa-eye-slash'></i></a> <a class='elmpre' title='Eliminar'><i class='fa fa-times iconsa'></i></a>";
	$.ajax({
		type: 'POST',
		url: 'editar_producto.php',
		data: 'process=deactive&id_pres='+id,
		dataType: 'JSON',
		success : function(datax)
		{
			if(datax.typeinfo == "Success")
			{
				tr.css('background',  '#CDCDCD');
				td.html(fila);
			}
			else
			{
				display_notify("Error", "Ocurrio un error inesperado, intente nuevamente");
			}
		}
	});
});

$(document).on("click", ".activate", function(){
	var id = $(this).attr("id");
	var tr = $(this).parents("tr");
	var td = $(this).parents("td");
	var fila = "<a class='deactive' id='"+id+"'><i class='fa fa-eye'></i></a> <a class='elmpre' title='Eliminar'><i class='fa fa-times iconsa'></i></a>";
	$.ajax({
		type: 'POST',
		url: 'editar_producto.php',
		data: 'process=active&id_pres='+id,
		dataType: 'JSON',
		success : function(datax)
		{
			if(datax.typeinfo == "Success")
			{
				tr.css('background', '#BDECB6');
				td.html(fila);
			}
			else
			{
				display_notify("Error", "Ocurrio un error inesperado, intente nuevamente");
			}
		}
	});
});
function deleted() {
  var id_producto = $('#id_producto').val();
  var dataString = 'process=deleted' + '&id_producto=' + id_producto;
  $.ajax({
    type: "POST",
    url: "borrar_producto.php",
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      display_notify(datax.typeinfo, datax.msg);
      setInterval("location.reload();", 1000);
      $('#deleteModal').hide();
    }
  });
}
$(document).on("click", "#btnAgregar", function() {
  $.ajax({
    type: "POST",
    url: "agregar_producto.php",
    data: "process=lista",
    dataType: 'json',
    success: function(datax) {

    }
  });
})
$(document).on("click", ".Delete", function() {
  $(this).parents("tr").remove();
});

$(document).on("click", "#add_pre", function() {
  var id_producto = $("#id_producto").val();
  var id_presentacion = $("#id_presentacion").val();
  var desc_pre = $("#desc_pre").val();
  var unidad_pre = $("#unidad_pre").val();
  var precio_pre = $("#precio_pre").val();
  var costo_p = $("#costo_pre").val();
  var valor = $("#id_presentacion").val();
  var bar=$("#bar").val();
  var proceso=$("#process").val();
  if (id_presentacion != "" && desc_pre != "" && unidad_pre != "" && valor != "") {
    var exis = false;
    $("#presentacion_table tr").each(function() {
      var id_pp = $(this).find(".presentacion").val();
      console.log(id_pp);
      console.log(valor);
      if (id_pp == valor) {
        if (unidad_pre == $(this).find(".unidad_p").text()) {
          exis = true;
        }
      }

    });
    if (exis)
		{
      display_notify("Warning", "Ya agrego una presentacion con estas caracteristicas");
    } else {
      if(proceso=="insert")
      {
        var text_select = $("#id_presentacion option:selected").html();
        var fila = "<tr>";
        fila += "<td class='bar'>" + bar + "</td>";
        fila += "<td><input type='hidden' class='presentacion' value='" + valor + "'>"+"<input type='hidden' class='precios_pre' value='" + "" + "'>" + text_select + "</td>";
        fila += "<td class='descripcion_p'>" + desc_pre + "</td>";
        fila += "<td class='unidad_p'>" + unidad_pre + "</td>";
        fila += "<td class='costo ed'>" + costo_p + "</td>";
        fila += "<td class='ed pre_m'>" + "0.0000" + "</td>";
        fila += "<td class='ed pre1'>" + "0.0000" + "</td>";
        fila += "<td class='ed pre2'>" + "0.0000" + "</td>";
        fila += "<td class='ed pre3'>" + "0.0000" + "</td>";
        fila += "<td class='ed pre4'>" + "0.0000" + "</td>";
        fila += "<td class='ed pre5'>" + "0.0000" + "</td>";
        fila += "<td class='ed pre6'>" + "0.0000" + "</td>";
        fila += "<td class='ed pre7'>" + "0.0000" + "</td>";

        /*
        fila += "<td class='precio_p text-center'>" + "<a data-toggle='modal' class='a' href='precios_modal.php?unidad="+unidad_pre+"&presentacion="+valor+"&id_producto="+id_producto+"' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-plus\"></i> Precios</a>" + "</td>";
        */
        fila += "<td class='delete text-center'><a class=' Delete'><i class='fa fa-trash'></i> Borrar</a></td>";
        $("#presentacion_table").append(fila);
        $(".clear").val("");
        $("#id_presentacion").val("");
        $("#id_presentacion").trigger('change');
      }
      else
      {
        $.ajax({
          url: 'editar_producto.php',
          type: 'POST',
          dataType: 'json',
          data: "process=add_pre"+"&id_producto="+id_producto+"&presentacion="+id_presentacion+"&descripcion="+desc_pre+"&unidad="+unidad_pre+"&costo="+costo_p+"&barcode="+bar,
          success: function(datax)
          {
            display_notify(datax.typeinfo,datax.msg);
            if(datax.typeinfo=="Success")
            {
              $.ajax({
                url: 'editar_producto.php',
                type: 'POST',
                dataType: 'json',
                data: "process=datos"+"&id_producto="+id_producto,
                success:  function(datax)
                {
                  $("#presentacion_table").html(datax.datos);
                }
              });
            }
          }
        });
      }
    }
  } else {
    display_notify("Error", "Por favor complete todos los campos");
  }
});
$('html').click(function() {
  /* Aqui se esconden los menus que esten visibles*/
  var number = $('#value').val();
  var a = $('#value').closest('td');
  var idtransace = a.closest('tr').attr('class');
  if (isNaN(parseFloat(number))) {

    if (!a.hasClass('prea')) {
      a.html("0.0000")
    }
    else {
      a.html(a.attr('prea'));
    }

  }
  else {

    a.html(number);
    if (a.hasClass('precio')) {

      if (parseFloat(number)==parseFloat(a.attr('prea'))) {
        console.log("mismo valor");
      }
      else {
        console.log("valor nuevo actualizando");

        $.ajax({
          url: 'editar_producto.php',
          type: 'POST',
          dataType: 'json',
          data: {process: 'actu_ppp',id_ppp: a.attr('id_prepd'), precio: number},
          success: function (xdatos) {
          }
        })

      }
    }

  }

});
$(document).on('click', '.a', function(event) {

});
$(document).on('click', 'td', function(e) {
  if ($(this).hasClass('ed')) {
    var av = $(this).html();
    console.log(av);
    $(this).html('');
    $(this).html('<input class="form-control in" type="text" id="value" name="value" value="">');
    if (av==0) {

    }
    else {
      $('#value').val(av);
    }

    $('#value').focus();
    $('#value').numeric({
      negative: false,
      decimalPlaces: 4
    });
    e.stopPropagation();
  }
  if ($(this).hasClass('ed2')) {
    var av = $(this).html();
    $(this).html('');
    $(this).html('<input class="form-control in" type="text" id="value" name="value" value="">');
    $('#value').val(av);
    $('#value').focus();
    $('#value').numeric({
      negative: false,
      decimalPlaces: 2
    });
    e.stopPropagation();
  }
  if ($(this).hasClass('ed3')) {
    var av = $(this).html();
    $(this).html('');
    $(this).html('<input class="form-control in" type="text" id="value" name="value" value="">');
    $('#value').val(av);
    $('#value').focus();
    e.stopPropagation();
  }

  if ($(this).hasClass('nm')) {
    var av = $(this).html();
    $(this).html('');
    $(this).html('<input class="form-control in" type="text" id="value" name="value" value="">');
    $('#value').val(av);
    $('#value').focus();
    $('#value').numeric({
      negative: false,
      decimal: false
    });
    e.stopPropagation();
  }
});
