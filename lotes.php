<?php
header("Access-Control-Allow-Origin: *");
require_once("_conexion.php");

if (isset($_REQUEST['hash'])) {
  if ($_REQUEST['hash']=='d681824931f81f6578e63fd7e35095af') {
    // code...
    $hoy=date("Y-m-d");
    $man=sumar_meses($hoy,3);
    $sql=_fetch_array(_query("SELECT id_sucursal FROM access_conf"));
    $id_sucursal=$sql['id_sucursal'];
    $inver = _query("SELECT COUNT(lote.id_sucursal) as cant, lote.id_sucursal FROM lote JOIN producto WHERE lote.id_producto=producto.id_producto AND lote.estado='VIGENTE' AND vencimiento BETWEEN '$hoy' AND '$man' AND lote.id_sucursal!=0  GROUP BY id_sucursal");
    $row_inver = _fetch_array($inver);
    $inversion = round($row_inver["cant"],2);
    $xdatos['data']=$inversion;
    echo json_encode($xdatos);
  }

}
 ?>
