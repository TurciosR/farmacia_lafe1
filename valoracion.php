<?php
include "_conexion.php";
$sqlJoined=" SELECT pr.id_producto,pr.descripcion, pr.barcode, c.nombre_cat as cat, SUM(su.cantidad) as cantidad,
estante.descripcion AS estante, posicion.posicion
FROM producto AS pr, categoria AS c, stock_ubicacion AS su LEFT JOIN estante ON estante.id_estante=su.id_estante
LEFT JOIN posicion ON posicion.id_posicion=su.id_posicion
WHERE pr.id_producto=su.id_producto
AND pr.id_categoria=c.id_categoria
AND su.cantidad>0
AND su.id_sucursal='1' GROUP BY pr.id_producto";
$query = _query($sqlJoined);
$num_rows = _num_rows($query);
$ttot=0;
if ($num_rows > 0)
{
  while ($row = _fetch_array($query)) {
    $id_producto = $row['id_producto'];
    $existencias = $row['cantidad'];

    $sql_pres = _query("SELECT pp.*, p.nombre as descripcion_pr FROM presentacion_producto as pp, presentacion as p WHERE pp.presentacion=p.id_presentacion AND pp.id_producto='$id_producto' AND pp.id_sucursal='1' ORDER BY pp.unidad DESC");
    $npres = _num_rows($sql_pres);
    $exis = 0;
    $n=0;

    while ($rowb = _fetch_array($sql_pres))
    {
      $unidad = $rowb["unidad"];
      $costo = $rowb["costo"];
      $precio = $rowb["precio"];

      $xc=0;

      $sql_rank=_query("SELECT presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$rowb[id_presentacion] AND presentacion_producto_precio.id_sucursal='1' AND presentacion_producto_precio.precio>=0 ORDER BY presentacion_producto_precio.desde ASC LIMIT 1
        ");

        while ($rowr=_fetch_array($sql_rank)) {
          # code...
          if ($xc==0) {
            $precio=$rowr['precio'];
          }
        }
        $descripcion_pr = $rowb["descripcion"];
        $presentacion = $rowb["descripcion_pr"];
        if ($existencias >= $unidad) {
          $exis = intdiv($existencias, $unidad);
          $existencias = $existencias%$unidad;
        } else {
          $exis =  0;
        }
        if ($n>0) {
        }
      }
      $ttot += $precio * $exis;
    }
  }
  echo $ttot;
