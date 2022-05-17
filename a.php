<?php
include '_conexion.php';

$fecha_movimiento = date("Y-m-d");
/*sacamos todos los ids de los productos disponibles*/
$and="";

$and = " WHERE id_producto!=2616  AND id_producto!=1479";
$sql_prod = _query("SELECT id_producto FROM producto $and ORDER BY producto.id_producto DESC");

$i=1;
while($rowp=_fetch_array($sql_prod))
{
  /*obtenemos el id del producto*/
  $id_producto=$rowp['id_producto'];

  $sql_ts=_fetch_array(_query("SELECT SUM(stock.stock) as stock FROM stock WHERE id_producto=$id_producto AND id_sucursal=1"));
  $stock = $sql_ts['stock'];

  $sql_ta=_fetch_array(_query("SELECT SUM(stock_ubicacion.cantidad) as stock FROM stock_ubicacion WHERE id_producto=$id_producto AND id_sucursal=1"));
  $enlote = $sql_ta['stock'];

  if ($stock!=$enlote) {
    // code...
    if (true) {
      // code...
      $sql_ml=_fetch_array(_query("SELECT MAX(lote.vencimiento) as lol FROM lote WHERE id_producto=$id_producto"));

      $max_fecha=$sql_ml['lol'];

      echo $i." ".$id_producto." ".$stock." ".$enlote." ".$max_fecha."<br>";
      $i++;
    }
    else {
      /*inicia reconstruccion de lotes*/

      /*ponemos los lotes de ese producto a cero */
      $table="lote";
      $form_data = array(
        'cantidad' => 0,
        'estado' => "FINALIZADO",
      );
      $where_clause=" id_producto=$id_producto AND id_sucursal=1";
      $update = _update($table,$form_data,$where_clause);

      /*obtenemos la maxima fecha de vencimiento*/

      $sql_ml=_fetch_array(_query("SELECT MAX(lote.vencimiento) as lol FROM lote WHERE id_producto=$id_producto"));

      $max_fecha=$sql_ml['lol'];

      $sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
      $datos_lot = _fetch_array($sql_lot);
      $lotep = $datos_lot["ultimo"]+1;

      /*creamos un nuevo lote y le ponemos el stock que deberia tener*/
      $table="lote";
      $form_data = array(
        'cantidad' => $stock,
        'estado' => "VIGENTE",
        'numero' => $lotep,
        'fecha_entrada' => $fecha_movimiento,
        'vencimiento'=>$max_fecha,
        'id_sucursal' => 1,
        'id_presentacion' => 0,
        'id_producto' => $id_producto,


      );
      $insertar = _insert($table,$form_data);
    }
  }
}
 ?>
