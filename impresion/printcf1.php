<?php
header("Access-Control-Allow-Origin: *");
//windows
$tmpdir = sys_get_temp_dir();   # directorio temporal


$texto = strtoupper($_REQUEST['datosventa']);
$efectivo = $_REQUEST['efectivo'];
$cambio = $_REQUEST['cambio'];
$info = $_SERVER['HTTP_USER_AGENT'];

$line=str_repeat("_",40)."\n";
$line1=str_repeat("_",30)."\n";
$initialized = chr(27).chr(64);
$condensed1 =Chr(27). chr(15);
$condensed0 =Chr(27). chr(18);
$puerto=system('ls /dev/usb/lp*');
if ($puerto=='/dev/usb/lp0')
	$printer="/dev/usb/lp0";
else
	$printer="/dev/usb/lp1";
//echo puerto;

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
//$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");
$encoded = array("\xa5","A","E","I","O","U","\x9a","\xa5","A","E","I","O","U","\x9a");
$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($cliente,$fecha,$direccion,$registro,$nit,$giro_cte,$venta,$total)=explode("|",$textoencodificado);

$string="";
//$string.= chr(27).chr(64); // Reset to defaults printer lx-350
//$string.= chr(27).chr(97).chr(0); //Left
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
//$string.= chr(10); //Line Feed
$string.= chr(15); //condensed mode
$string.= chr(27).chr(77); //12 cpi
//headers cc ff
$string.= chr(10); //Line Feedh
$string.=chr(13).$fecha."\n"; //  Print text
$string.=chr(13).$cliente."\n"; //  Print text

$string.=chr(13).$direccion."\n"; //  Print text
$string.=chr(13).$nit."\n"; //  Print text
$string.=chr(13).$registro."\n"; //  Print text
$string.=chr(13).$giro_cte." "; //  Print text
$string.= chr(27).chr(50); //espacio entre lineas 6 x pulgada
$string.= chr(10); //Line Feed
$string.= chr(10); //Line Feed

$string.=chr(13).$venta; // Print text
$string.= chr(10); //Line Feed
//$string.= chr(10); //Line Feed
//$string.=chr(13).$total; // Print text

$string.= chr(10); //Line Feed
$string.= chr(12); //page Feed
//send data to USB printer
//linux
$fp=fopen($printer, 'wb');
fwrite($fp, $string);
fclose($fp);

?>
