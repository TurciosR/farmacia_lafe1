<?php
include_once "_conexion.php";
include_once '_core.php';


$cuantos = 0;
$datos = "";
$destino = 2;
$fecha = date("Y-m-d");
$total_compras = 0.00;
$concepto="CONTEO BODEGA";
$hora=date("H:i:s");
$fecha_movimiento = date("Y-m-d");
$id_empleado=-1;;


$id_sucursal = 1;
$sql_num = _query("SELECT aj FROM correlativo WHERE id_sucursal='$id_sucursal'");
$datos_num = _fetch_array($sql_num);
$ult = $datos_num["aj"]+1;
$numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_AJ';

_begin();

$z=1;

/*actualizar los correlativos de II*/
$corr=1;
$table="correlativo";
$form_data = array(
  'aj' =>$ult
);
$where_clause_c="id_sucursal='".$id_sucursal."'";
$up_corr=_update($table,$form_data,$where_clause_c);
if ($up_corr) {
  # code...
}
else {
  $corr=0;
}
if ($concepto=='')
{
  $concepto='AJUSTE DE INVENTARIO';
}

$table='movimiento_producto';
$form_data = array(
  'id_sucursal' => $id_sucursal,
  'correlativo' => $numero_doc,
  'concepto' => $concepto,
  'total' =>  0,
  'tipo' => 'AJUSTE',
  'proceso' => 'AJ',
  'referencia' => $numero_doc,
  'id_empleado' => $id_empleado,
  'fecha' => $fecha,
  'hora' => $hora,
  'id_suc_origen' => $id_sucursal,
  'id_suc_destino' => $id_sucursal,
  'id_proveedor' => 0,
);
$insert_mov =_insert($table,$form_data);
$id_movimiento=_insert_id();
$lista=explode('#',$datos);
$j = 1 ;
$k = 1 ;
$l = 1 ;
$m = 1 ;

$var1=0;
$var2=0;

$sql_prod=_query("SELECT stock_ubicacion.id_producto, SUM(stock_ubicacion.cantidad) FROM stock_ubicacion JOIN producto ON producto.id_producto=stock_ubicacion.id_producto WHERE id_ubicacion=2 GROUP BY stock_ubicacion.id_producto HAVING SUM(stock_ubicacion.cantidad) >0");

while ($rows_p=_fetch_array($sql_prod)) {
  // code...
  $id_producto=$rows_p['id_producto'];
  $sql_detail= _fetch_array(_query("SELECT presentacion_producto.id_presentacion,(presentacion_producto.costo/presentacion_producto.unidad) as costo,(presentacion_producto_precio.precio/presentacion_producto.unidad) as precio FROM presentacion_producto JOIN presentacion_producto_precio ON presentacion_producto_precio.id_presentacion=presentacion_producto.id_presentacion WHERE presentacion_producto.id_producto=$id_producto LIMIT 1"));

  $precio_compra=$sql_detail['costo'];
  $precio_venta=$sql_detail['precio'];
  $sql_e_m=_query("SELECT stock_ubicacion.id_su,stock_ubicacion.cantidad FROM stock_ubicacion WHERE stock_ubicacion.id_producto=$id_producto AND stock_ubicacion.id_ubicacion=$destino AND cantidad>0");
  while ($row_e_m=_fetch_array($sql_e_m)) {
    # code...

    /*arreglando problema con lotes de nuevo*/
    $cantidad_a_descontar=$row_e_m['cantidad'];
    $sql=_query("SELECT id_lote, id_producto, fecha_entrada, vencimiento, cantidad
    FROM lote
    WHERE id_producto='$id_producto'
    AND id_sucursal='$id_sucursal'
    AND cantidad>0
    ORDER BY vencimiento");

    $contar=_num_rows($sql);

      if ($contar>0) {
          # code...
          while ($row=_fetch_array($sql)) {
              # code...
              $entrada_lote=$row['cantidad'];
              if ($cantidad_a_descontar>0) {
                  # code...
                  if ($entrada_lote==0) {
                      $table='lote';
                      $form_dat_lote=$arrayName = array(
                          'estado' => 'FINALIZADO',
                      );
                      $where = " WHERE id_lote='$row[id_lote]'";
                      $insert=_update($table,$form_dat_lote,$where);
                  } else {
                      if (($entrada_lote-$cantidad_a_descontar)>0) {
                          # code...
                          $table='lote';
                          $form_dat_lote=$arrayName = array(
                              'cantidad'=>($entrada_lote-$cantidad_a_descontar),
                              'estado' => 'VIGENTE',
                          );
                          $cantidad_a_descontar=0;

                          $where = " WHERE id_lote='$row[id_lote]'";
                          $insert=_update($table,$form_dat_lote,$where);
                      } else {
                          # code...
                          if (($entrada_lote-$cantidad_a_descontar)==0) {
                            # code...
                            $table='lote';
                            $form_dat_lote=$arrayName = array(
                                'cantidad'=>($entrada_lote-$cantidad_a_descontar),
                                'estado' => 'FINALIZADO',
                            );
                            $cantidad_a_descontar=0;

                            $where = " WHERE id_lote='$row[id_lote]'";
                            $insert=_update($table,$form_dat_lote,$where);
                          }
                          else
                          {
                            $table='lote';
                            $form_dat_lote=$arrayName = array(
                                'cantidad'=>0,
                                'estado' => 'FINALIZADO',
                            );
                            $cantidad_a_descontar=$cantidad_a_descontar-$entrada_lote;
                            $where = " WHERE id_lote='$row[id_lote]'";
                            $insert=_update($table,$form_dat_lote,$where);
                          }
                      }
                  }
              }
          }
      }
      /*fin arreglar problema con lotes*/
    if(!$insert)
    {
      $l = 0;
    }

    /*obtener el valor del stock*/
    $sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
    $stock2=_query($sql2);
    $row2=_fetch_array($stock2);
    $nrow2=_num_rows($stock2);
    $existencias=0;
    if ($nrow2>0)
    {
      $existencias=$row2['stock'];
    }
    else
    {
      $existencias=0;
    }

    /*realizar el movimiento de vaciado del stock*/
    $cant_new=$existencias-$row_e_m['cantidad'];
    $table1= 'movimiento_producto_detalle';
    $form_data1 = array(
      'id_movimiento'=>$id_movimiento,
      'id_producto' => $id_producto,
      'cantidad' => $row_e_m['cantidad'],
      'costo' => $precio_compra,
      'precio' => $precio_venta,
      'stock_anterior'=>$existencias,
      'stock_actual'=>$cant_new,
      'lote' => 0,
      'id_presentacion' => 0,
    );
    $insert_mov_det = _insert($table1,$form_data1);
    if(!$insert_mov_det)
    {
      $j = 0;
    }

    //actualizar stock restando el valor de la ubicación especifica;
    $table= 'stock';
    $form_data = array(
       'stock' => $cant_new,
       'update_date'=>$fecha_movimiento,
      );
    $where_clause="id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
    $updateP=_update($table, $form_data, $where_clause);

    /*vaciamos la ubicaciones donde se encuentra ese producto ya sea de bodega local u otro*/
    $form_data_su = array(
      'cantidad' => 0,
    );
    $table_su = "stock_ubicacion";
    $where_su = "id_su='".$row_e_m['id_su']."'";
    $insert_su = _update($table_su, $form_data_su, $where_su);

    //registramos la salida

    $table="movimiento_stock_ubicacion";
    $form_data = array(
      'id_producto' => $id_producto,
      'id_origen' => $row_e_m['id_su'],
      'id_destino'=> 0,
      'cantidad' => $row_e_m['cantidad'],
      'fecha' => $fecha_movimiento,
      'hora' => $hora,
      'anulada' => 0,
      'afecta' => 0,
      'id_sucursal' => $id_sucursal,
      'id_presentacion'=> 0,
      'id_mov_prod' => $id_movimiento,
    );

    $insert_mss =_insert($table,$form_data);

    if ($insert_mss) {
      # code...
    }
    else {
      # code...
      $z=0;
    }


  }
}

echo "aca";

_commit();



 ?>
