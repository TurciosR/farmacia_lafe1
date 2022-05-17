<?php
header("Access-Control-Allow-Origin: *");
require_once("_conexion.php");

if (isset($_REQUEST['hash'])) {
  if ($_REQUEST['hash']=='d681824931f81f6578e63fd7e35095af') {
    // code...
    $sql=_query("SELECT producto.id_producto, producto.descripcion,producto.marca, stock.stock FROM producto JOIN stock ON stock.id_producto=producto.id_producto WHERE producto.descripcion LIKE '%$_REQUEST[q]%'");
    $xdatos['data']='';
    $info='';
    $n=_num_rows($sql);
    if ($n>0) {
      // code...
      while($rows=_fetch_array($sql))
      {
        $i=0;
        $id_producto=$rows['id_producto'];
        $unidadp=0;
        $preciop=0;
        $descripcionp=0;
        $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1 ORDER BY presentacion_producto.unidad ASC");
        while ($row=_fetch_array($sql_p))
        {
          if ($i==0)
          {
            $unidadp=$row['unidad'];
            $preciop=$row['precio'];

            $xc=0;

            $sql_rank=_query("SELECT presentacion_producto_precio.id_prepd,presentacion_producto_precio.desde,presentacion_producto_precio.hasta,presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$row[id_presentacion]  AND presentacion_producto_precio.precio!=0 ORDER BY presentacion_producto_precio.desde ASC limit 1");

              while ($rowr=_fetch_array($sql_rank)) {
                # code...
                if($xc==0)
                {
                  $preciop=$rowr['precio'];
                }
              }
            }
            $i=$i+1;
          }
        $info.="<tr><td>$rows[marca]</td><td>$rows[descripcion]</td><td>$rows[stock]</td><td>".number_format($preciop,2)."</td></tr>";
      }
      $xdatos['data']=$info;
    }

    echo json_encode($xdatos);
  }

}
 ?>
