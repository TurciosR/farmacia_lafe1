<?php
include '../_conexion.php';
include 'mail.php';
function send()
{
	$id_activo = file_get_contents('http://demos.apps-oss.com/sms/get.php');
	$sql_host = _query("SELECT ruta AS url FROM rutas WHERE id='$id_activo'");
	$datos_host = _fetch_array($sql_host);
	$host = $datos_host["url"];

	$sql_sms = _query("SELECT * FROM configuracion WHERE id_configuracion=1");
	$datos_sms = _fetch_array($sql_sms);
	$client = $datos_sms["user"];
	$hash = $datos_sms["hash"];

	$sql = _query("SELECT id_sms, telefono as numero, mensaje, lote FROM sms WHERE estado=0");
	$n = _num_rows($sql);
	//Nombre del cliente conf manual
	$cliente = ucwords($client);
	if($n>0)
	{
		$lote = "";
		$sql_lot = _query("SELECT DISTINCT(lote) as lote FROM sms WHERE estado=0");
		while($dt_lot = _fetch_array($sql_lot))
		{
			$lote .= $dt_lot["lote"].", ";
		}
		$lote = substr($lote ,0 ,-2);

		$texto = "";
		$sql_msg = _query("SELECT DISTINCT(mensaje) as mensaje FROM sms WHERE estado=0");
		while($dt_msg = _fetch_array($sql_msg))
		{
			$texto .= $dt_msg["mensaje"].", ";
		}
		$texto = substr($texto ,0 ,-2);
		$ids = array();
		$data = array();
		$n=0;
		while($datos = _fetch_array($sql))
		{
			$ids[] = $datos["id_sms"];
			$data[] = $datos;
			$n++;
		}

		$datos_post = http_build_query(
		    array(
		        'hash' => $hash,
		        'cliente' => $client,
		        'process' => 'inject',
		        'data' => $data,
		        'n_sms' => $n
		    )
		);
		$opciones = array('http' =>
		    array(
		        'method'  => 'POST',
		        'header'  => 'Content-type: application/x-www-form-urlencoded',
		        'content' => $datos_post
		    )
		);

		$host="http://sms1.apps-oss.com";
		$contexto = stream_context_create($opciones);
		$resultado = file_get_contents($host.'/api/sms_inject.php', false, $contexto);

		$asunto = "INFORMACION DE ESTADO API SMS";
		$headers = "From: info@opensolutionsystems.com". "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$destino = "nobenavides17@gmail.com, alduvi11@gmail.com";
		$message = msg($cliente, $n, $lote, $texto);
	//	mail($destino,$asunto,$message,$headers);
		echo "$resultado";
		if(trim($resultado) == "All changes commited")
		{
			$table = "sms";
			$upa = 0;
			foreach ($ids as $id)
			{
				$form_data = array(
					'estado' => 1
				);
				$where = 'id_sms="'.$id.'"';
				$update = _update($table,$form_data, $where);
				if(!$update)
					$upa = 1;
			}
			if(!$upa)
				$xdata["typeinfo"] = "Success";
			else
				$xdata["typeinfo"] = "Error";
		}
		else
		{
			$xdata["typeinfo"] = "Error";
		}
	}
	else
	{
		$xdata["typeinfo"] = "No SMS";
	}
	echo json_encode($xdata);
}
if(!isset($_POST['process']))
{
	send();
}
else
{
	if(isset($_POST['process']))
	{
		switch ($_POST['process'])
		{
			case 'send':
			send();
			break;
		}
	}
}
?>
