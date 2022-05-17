<?php
header("Access-Control-Allow-Origin: *");
require_once("_conexion.php");

if (isset($_REQUEST['hash'])) {
  if ($_REQUEST['hash']=='d681824931f81f6578e63fd7e35095af') {
    // code...
    $sql=_fetch_array(_query("SELECT id_sucursal FROM access_conf"));
    $id_sucursal=$sql['id_sucursal'];
    $inver = _query("SELECT SUM(stock.stock*stock.precio_unitario) AS inversion FROM stock INNER JOIN presentacion_producto ON presentacion_producto.id_producto=stock.id_producto WHERE stock.id_sucursal='$id_sucursal' AND presentacion_producto.unidad=1 AND presentacion_producto.id_sucursal='$id_sucursal'");
    $row_inver = _fetch_array($inver);
    $inversion = round($row_inver["inversion"],2);
    $xdatos['data']=$inversion;
    echo json_encode($xdatos);
  }

}
 ?>
