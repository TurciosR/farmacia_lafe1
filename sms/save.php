<?php
include "../_conexion.php";
function save()
{
  //_begin();
  $num = $_POST["n_sms"];
  $msgs = $_POST["msgs"];
  //Ultimo Lote existente
  $sql_lote = _query("SELECT max(lote) as lote FROM sms");
  $datos_lote = _fetch_array($sql_lote);
  $lote = $datos_lote["lote"] + 1;
  //Numero de mensajes a guardar
  $n_mensajes = $num;
  //Contador de mensajes
  $sms = 0 ;
  //Ciclo de gardado de informacion
  $fecha = date("Y-m-d");
  $hora = date("H:i:s");

  $array_sms = json_decode($msgs, true);
  foreach ($array_sms as $valores)
  {
    //Texto y numero del mensaje
    $mensaje = $valores["mensaje"];
    $numero = $valores["numero"];
    //Form de mensajes
    $form_data_msg = array(
      'telefono' => $numero,
      'mensaje' => $mensaje,
      'estado' => 0,
      'lote' => $lote,
      'fecha' => $fecha,
      'hora' => $hora
    );
    $tabla_msg = "sms";
    $insert_msg = _insert($tabla_msg, $form_data_msg);
    if($insert_msg)
    {
      $sms++;
    }
  }

  $sql_sms = _query("SELECT sms FROM configuracion WHERE id_configuracion=1");
  $datos_sms = _fetch_array($sql_sms);
  $sms_e = $datos_sms["sms"];
  if($sms_e != "Ilimitado")
  {
    $sms1 = $sms_e - $sms;
  }
  else
  {
    $sms1 = "Ilimitado";
  }
  $tabla_sms = "configuracion";
  $form_data_sms = array(
    'sms' => $sms
  );
  $where_sms = "id_configuracion='1'";
  $update_sms = _update($tabla_sms, $form_data_sms, $where_sms);

  if($sms == $n_mensajes && $update_sms)
  {
    //_commit();
    $xdatos["typeinfo"]="Success";
    $xdatos["msg"]="Informacion actualizada correctamente!";
  }
  else
  {
    //_rollback();
    $xdatos["typeinfo"]="Error";
    $xdatos["msg"]="Informacion no pudo ser actualizada!"._error();
  }
  echo json_encode($xdatos);
}
if(!isset($_POST['process']))
{
  save();
}
else
{
  if(isset($_POST['process']))
  {
    switch ($_POST['process'])
    {
      case 'save':
      save();
      break;
    }
  }
}
?>
