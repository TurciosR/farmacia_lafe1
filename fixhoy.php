<?php
include_once '_conexion.php';

$sql  = _query("SELECT * FROM `movimiento_producto` WHERE concepto='VENTA' AND fecha='2020-10-08' AND correlativo NOT IN (SELECT factura.numero_doc FROM factura WHERE fecha='2020-10-08')");

//correlativos perdidos
while ($row = _fetch_array($sql))
{
  echo "$row[correlativo] <br>";
}

echo "<br>";
$sql  = _query("SELECT * FROM `movimiento_producto` WHERE concepto='VENTA' AND fecha='2020-10-08' AND correlativo NOT IN (SELECT factura.numero_doc FROM factura WHERE fecha='2020-10-08')");
while ($rows = _fetch_array($sql))
{

  $id_movimiento = $rows['id_movimiento'];
  //insertamos el total de la factura
  $tipo_impresion = $rows['proceso'];

  $num_fact_impresa ="";
  if ($tipo_impresion =='COF') {
    $tipo_entrada_salida='FACTURA CONSUMIDOR';
  }
  if ($tipo_impresion =='TIK') {
    $tipo_entrada_salida='TICKET';
    $porciones = explode("_", $rows['correlativo']);
    $num_fact_impresa = round($porciones[0]);
  }
  if ($tipo_impresion =='CCF') {
    $tipo_entrada_salida='CREDITO FISCAL';
  }

  $tipo_documento= $tipo_impresion;

  $table_fact= 'factura';
  $form_data_fact = array(
    'id_cliente' => 1,
    'fecha' => $rows['fecha'],
    'numero_doc' => $rows['correlativo'],
    'subtotal' => $rows['total'],
    'sumas'=>$rows['total'],
    'suma_gravado'=>$rows['total'],
    'iva' =>0,
    'retencion'=>0,
    'venta_exenta'=>0,
    'total_menos_retencion'=>$rows['total'],
    'total' => $rows['total'],
    'id_empleado' => $rows['id_empleado'],
    'id_sucursal' => $rows['id_sucursal'],
    'tipo' => $tipo_entrada_salida,
    'serie' => "",
    'num_fact_impresa' => $num_fact_impresa,
    'hora' => $rows['hora'],
    'finalizada' => '1',
    'abono'=>0,
    'saldo' => 0,
    'tipo_documento' => $tipo_documento,
    'id_apertura' => 651,
    'id_apertura_pagada' => 651,
    'caja' => 1,
    'credito' => 0,
    'turno' => 1,
    'cargo_tarjeta' => 0,
    'pago_tarjeta' => 0,
  );

  print_r($form_data_fact);

  echo "<br><br>";
  $insertar_fact = _insert($table_fact,$form_data_fact );
  $id_fact= _insert_id();

  //$id_fact = 1;
  $sql_detalil  = _query("SELECT * FROM movimiento_producto_detalle WHERE id_movimiento=$id_movimiento");

  while ($det  = _fetch_array($sql_detalil)) {

    $id_presentacion = $det['id_presentacion'];
    $id_producto = $det['id_producto'];

    $sql_costo=_fetch_array(_query("SELECT costo,unidad FROM presentacion_producto WHERE id_presentacion = $id_presentacion"));
    $precio_compra=$sql_costo['costo'];
    $table_fact_det= 'factura_detalle';
    $data_fact_det = array(
      'id_factura' => $id_fact,
      'id_prod_serv' => $id_producto,
      'cantidad' => $det['cantidad'],
      'precio_venta' => $det['precio'],
      'subtotal' => round(($det['cantidad']/$sql_costo['unidad'])*$det['precio'],2),
      'tipo_prod_serv' => "PRODUCTO",
      'id_empleado' => $rows['id_empleado'],
      'id_sucursal' => $rows['id_sucursal'],
      'fecha' =>  $rows['fecha'],
      'id_presentacion'=> $id_presentacion,
      'exento' => 0,
    );

    print_r($data_fact_det);
    echo "<br><br>";
    $insertar_fact_det = _insert($table_fact_det,$data_fact_det );
  }
  $foma = array('id_factura' => $id_fact, );
  _update("movimiento_producto",$foma,"id_movimiento=$id_movimiento");
}



function _update_s($table_name, $form_data, $where_clause='')
{
    // check for optional where clause
    $whereSQL = '';
    $form_data2=array();
	$variable='';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add key word
            $whereSQL = " WHERE ".$where_clause;
        } else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE ".$table_name." SET ";

    // loop and build the column /
    $sets = array();
    //begin modified
	foreach($form_data as $index=>$variable){
		$var1=preg_match('/\x{27}/u', $variable);
		$var2=preg_match('/\x{22}/u', $variable);
		if($var1==true || $var2==true){
		 $variable = addslashes($variable);
		}
		$form_data2[$index] = $variable;
    }
    foreach ( $form_data2 as $column => $value ) {
		$sets [] = $column . " = '" . $value . "'";
	}
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;

    // run and return the query result
    return _query($sql);
}


 ?>
