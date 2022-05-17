<?php
header("Access-Control-Allow-Origin: *");
require_once("_conexion.php");

if (isset($_REQUEST['hash'])) {
  if ($_REQUEST['hash']=='d681824931f81f6578e63fd7e35095af') {
    // code...
    $xdatos['data']='';
    $info='';

    $sql=_query("SELECT producto.descripcion,producto.minimo,stock.stock FROM stock JOIN producto WHERE stock.id_producto=producto.id_producto AND stock.stock<producto.minimo");
    while($ra=_fetch_array($sql))
    {
      $info.="
    <tr><td>".$ra['descripcion']."</td><td>".$ra['minimo']."</td><td><span class=\"label label-danger\">".number_format($ra['stock'],0,"","")."</span></td></tr>";
    }
    $xdatos['data']=$info;
    echo json_encode($xdatos);
  }

}
 ?>
