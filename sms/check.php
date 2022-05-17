<?php
include '../_conexion.php';
function check()
{
	$id_activo = file_get_contents('http://demos.apps-oss.com/sms/get.php');
	$sql_host = _query("SELECT ruta AS url FROM rutas WHERE id='$id_activo'");
	$datos_host = _fetch_array($sql_host);
	$host = $datos_host["url"];
	$sql_sms = _query("SELECT * FROM configuracion WHERE id_configuracion=1");
	$datos_sms = _fetch_array($sql_sms);
	$client = $datos_sms["user"];
	$hash = $datos_sms["hash"];
	$datos_post = http_build_query(
			array(
					'hash' => $hash,
					'cliente' => $client,
					'process' => 'availability',
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
	$xdata["typeinfo"] = trim($resultado);
	echo json_encode($xdata);
}
if(!isset($_POST['process']))
{
    check();
}
else
{
    if(isset($_POST['process']))
    {
        switch ($_POST['process'])
        {
            case 'check':
                check();
                break;
        }
    }
}
?>
