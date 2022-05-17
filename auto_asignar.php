<?php
include_once '_core.php';
function insertar()
{

	$concepto="ASIGNACION AUTOMATICA";
	$fecha=date("Y-m-d");
	$id_sucursal=$_SESSION['id_sucursal'];
	$id_empleado=$_SESSION['id_usuario'];
	$hora=date("H:i:s");
	$z=1;
	$m=1;


	_begin();
	$sql_num = _query("SELECT ai FROM correlativo WHERE id_sucursal='$id_sucursal'");
	$datos_num = _fetch_array($sql_num);
	$ult = $datos_num["ai"]+1;
	$numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_AI';
	$tipo_entrada_salida='ASIGNACION DE INVENTARIO';

	/*actualizar los correlativos de AI*/
	$corr=1;
	$up=1;

	$table="correlativo";
	$form_data = array(
		'ai' =>$ult
	);
	$where_clause_c="id_sucursal='".$id_sucursal."'";
	$up_corr=_update($table,$form_data,$where_clause_c);
	if ($up_corr) {
		# code...
	}
	else {
		$corr=0;
	}

	$table='movimiento_producto';
	$form_data = array(
		'id_sucursal' => $id_sucursal,
		'correlativo' => $numero_doc,
		'concepto' => $concepto,
		'total' => 0,
		'tipo' => 'ASIGNACION',
		'proceso' => 'AI',
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

  /*
  obj.id_producto = id_producto;
  obj.cantidad = t_cant;
  obj.id_estante= id_estante;
  obj.id_posicion=id_posicion;
  obj.id_presentacion=id_presentacion;
  */

  $sql_no_asignados = _query("SELECT * FROM stock_ubicacion WHERE id_sucursal=$id_sucursal AND id_estante=0 AND id_posicion=0 AND cantidad !=0");
	while ($fila = _fetch_array($sql_no_asignados)) {

  	$id_ubicacion=$fila['id_ubicacion'];
		$id_producto=$fila['id_producto'];
    /*vaciamos las ubicaciones no asignadas*/
		$sql=_fetch_array(_query("SELECT * FROM stock_ubicacion WHERE id_su=$fila[id_su]"));
		$id_su1=$sql['id_su'];
		$stock_anterior=$sql['cantidad'];
		$nuevo_stock=$stock_anterior-$fila['cantidad'];
		$table="stock_ubicacion";
		$form_data = array(
			'cantidad' => $nuevo_stock,
		);
		$where_clause="id_su='".$id_su1."'";
		$update=_update($table,$form_data,$where_clause);
		if ($update) {
			# code...
		}
		else {
			$up=0;
		}

		/*Verificar tabla stock_ubicacion*/
		$id_su="";
    $nrow_su=0;
    $id_estante=0;
    $id_posicion=0;

    $sql_sn = _query("SELECT stock_ubicacion.id_su ,stock_ubicacion.id_estante,stock_ubicacion.id_posicion,stock_ubicacion.id_ubicacion,stock_ubicacion.cantidad FROM stock_ubicacion WHERE id_producto=$id_producto AND id_ubicacion=$id_ubicacion AND id_estante!=0 AND cantidad>0 ORDER BY id_su DESC  LIMIT 1");
    $nrow_su = _num_rows($sql_sn);
    if (_num_rows($sql_sn)>0) {
    }
    else {
      $sql_sn = _query("SELECT stock_ubicacion.id_su ,stock_ubicacion.id_estante,stock_ubicacion.id_posicion,stock_ubicacion.id_ubicacion,stock_ubicacion.cantidad FROM stock_ubicacion WHERE id_producto=$id_producto AND id_ubicacion=$id_ubicacion AND id_estante!=0 ORDER BY id_su DESC  LIMIT 1");
      $nrow_su = _num_rows($sql_sn);
    }

		/*cantidad de una presentacion por la unidades que tiene desde javascript*/
		$cantidad=$fila['cantidad'];
		if($nrow_su >0)
		{
			$row_su=_fetch_array($sql_sn);
			$cant_exis = $row_su["cantidad"];
			$id_su = $row_su["id_su"];
			$cant_new = $cant_exis + $cantidad;
			$form_data_su = array(
				'cantidad' => $cant_new,
			);
			$table_su = "stock_ubicacion";
			$where_su = "id_su='".$id_su."'";
			$insert_su = _update($table_su, $form_data_su, $where_su);
		}
		else
		{
			$form_data_su = array(
				'id_producto' => $id_producto,
				'id_sucursal' => $id_sucursal,
				'cantidad' => $cantidad,
			);
			$table_su = "stock_ubicacion";
      $where_su = "id_su='".$id_su1."'";
      $id_su = $id_su1;
			$insert_su = _update($table_su, $form_data_su, $where_su);
		}
		if(!$insert_su)
    {
      $m=0;
    }

		$table="movimiento_stock_ubicacion";
    $form_data = array(
      'id_producto' => $id_producto,
      'id_origen' => $id_su1,
      'id_destino'=> $id_su,
      'cantidad' => $cantidad,
      'fecha' => $fecha,
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

	if($corr&&$z&&$m)
	{
		$xdatos['typeinfo']='Success';
		$xdatos['msg']='Asignacion Realizada con exito!';
		$xdatos['process']='insert';
		_commit();
	}
	else
	{
		_rollback();
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Asignacion no pudo ser realizada !';
		$xdatos['process']='insert';
	}

	echo json_encode($xdatos);
}

if(isset($_REQUEST['process']))
{
  switch ($_REQUEST['process'])
  {
    case 'asignar':
    insertar();
    break;
  }
}
 ?>
