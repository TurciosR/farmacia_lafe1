$(document).ready(function() {
  $(".datepick").datepicker();
  $(".select").select2();
  $(".numeric").numeric({
    negative: false,
  });

  $('#formulario').validate({
    rules: {
      fecha: {
        required: true,
      },
      empleado: {
        required: true,
      },
      turno: {
        required: true,
      },
      monto_apertura: {
        required: true,
      },
    },
    messages: {
      fecha: "Por favor ingrese la fecha de apertura",
      empleado: "Por favor seleccione el empleado",
      turno: "Por favor seleccione el turno",
      monto_apertura: "Ingrese el monto de apertura",
      /*
      password: {
      	required: "Por favor ingrese su password",
      	minlength: "Su password debe de tener como minimo 5 caracteres"
      */
    },

    submitHandler: function(form) {
      apertura();
    }
  });
});

function apertura() {
  var form = $("#formulario");
  var formdata = false;
  if (window.FormData) {
    formdata = new FormData(form[0]);
  }
  var formAction = form.attr('action');
  var caja = $("#caja").val();
  var empleado = $("#empleado_text").val();
  var monto_apertura = parseFloat($("#monto_apertura").val()).toFixed(2);
  if (caja != "" && caja != 0) {
    $.ajax({
      type: 'POST',
      url: 'apertura_caja.php',
      cache: false,
      data: formdata ? formdata : form.serialize(),
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(data)
      {
        display_notify(data.typeinfo, data.msg, data.process);
        if (data.typeinfo == "Success") {
					var now = new Date(Date.now());
	        var ap = "AM";
	        var hor = now.getHours();
	        if(hor>=12)
	        {
	          ap = "PM";
	          if(hor>12)
	          {
	            hor -= 12;
	          }
	        }
          minut = now.getMinutes();
          if(minut<10)
          {
            minut = "0"+minut;
          }
          var hora = hor + ":" +minut+" "+ap;
	        msg = "FARMACIA LA FE 1: APERTURA DE CAJA "+hora+"; REALIZADO POR: "+empleado+",  MONTO DE APERTURA: "+monto_apertura;
          msg1 = "FARMACIA LA FE 1: HAY UNA CANTIDAD DE "+data.prods+" PRODUCTOS QUE VENCEN EN LOS PROXIMOS 10 DIAS.";
          msg2 = "FARMACIA LA FE 1: SU INVERSION A LA FECHA "+data.fecha+" ES DE "+data.inversion;
          var array_json = new Array();

	        var obj = new Object();
	        obj.mensaje = msg;
	        obj.numero = "79379816";
	        text=JSON.stringify(obj);
	        array_json.push(text);

	        var obj = new Object();
	        obj.mensaje = msg;
	        obj.numero = "76185847";
	        text=JSON.stringify(obj);
	        array_json.push(text);

	        var obj = new Object();
	        obj.mensaje = msg;
	        obj.numero = "78714232";
	        text=JSON.stringify(obj);
	        array_json.push(text);

          var obj = new Object();
	        obj.mensaje = msg1;
	        obj.numero = "79379816";
	        text=JSON.stringify(obj);
	        array_json.push(text);

	        var obj = new Object();
	        obj.mensaje = msg1;
	        obj.numero = "76185847";
	        text=JSON.stringify(obj);
	        array_json.push(text);

	        var obj = new Object();
	        obj.mensaje = msg1;
	        obj.numero = "78714232";
	        text=JSON.stringify(obj);
	        array_json.push(text);

          var obj = new Object();
	        obj.mensaje = msg2;
	        obj.numero = "79379816";
	        text=JSON.stringify(obj);
	        array_json.push(text);

	        var obj = new Object();
	        obj.mensaje = msg2;
	        obj.numero = "76185847";
	        text=JSON.stringify(obj);
	        array_json.push(text);

	        var obj = new Object();
	        obj.mensaje = msg2;
	        obj.numero = "78714232";
	        text=JSON.stringify(obj);
	        array_json.push(text);

	        msgs = '['+array_json+']';
	        $.post("sms/save.php", {
	          process: "save",
	          n_sms:"9",
	          msgs:msgs,
	        });
          setInterval("reload1();", 1000);
        }
      }
    });
  } else {
    display_notify("Error", "Debe de seleccionar una caja");
  }

}

function reload1() {
  location.href = 'admin_corte.php';
}
