<?php
header("Access-Control-Allow-Origin: *");
require_once("_conexion.php");

if (isset($_REQUEST['hash'])) {
  if ($_REQUEST['hash']=='d681824931f81f6578e63fd7e35095af') {
    // code...
    $sql=_fetch_array(_query("SELECT id_sucursal FROM access_conf"));
    $id_sucursal=$sql['id_sucursal'];
    $inver = _query("SELECT COUNT(producto.id_producto) as cant FROM stock JOIN producto WHERE stock.id_producto=producto.id_producto AND stock.stock<producto.minimo");
    $row_inver = _fetch_array($inver);
    $inversion = round($row_inver["cant"],2);
    $xdatos['data']=$inversion;
    echo json_encode($xdatos);
  }

}
 ?>
