<?php
	include ("_core.php");
	/*
	SELECT movimientos.id_movimiento,movimientos.fecha,movimientos.hora,usuario.nombre,movimientos.concepto,movimientos.total,SUM(movimiento_producto.entrada) as entrada,SUM(mp.salida) AS salida FROM movimientos JOIN usuario ON usuario.id_usuario=movimientos.id_usuario JOIN movimiento_producto ON movimiento_producto.id_movimiento=movimientos.id_movimiento JOIN movimiento_producto as mp ON mp.id_movimiento=movimientos.id_movimiento GROUP BY movimiento_producto.id_movimiento
	*/

	$requestData= $_REQUEST;
	$fechai= $_REQUEST['fechai'];
	$fechaf= $_REQUEST['fechaf'];
	$lab= $_REQUEST['lab'];

	require('ssp.customized.class.php' );
	// DB table to use
	$table = 'devoluciones_vencimiento';
	// Table's primary key
	$primaryKey = 'id_devolucion';

	// MySQL server connection information
	$sql_details = array(
  'user' => $username,
  'pass' => $password,
  'db'   => $dbname,
  'host' => $hostname
  );

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$id_sucursal=$_SESSION['id_sucursal'];

	$joinQuery = "
	FROM devoluciones_vencimiento JOIN proveedor ON devoluciones_vencimiento.id_proveedor=proveedor.id_proveedor JOIN usuario ON usuario.id_usuario=devoluciones_vencimiento.id_empleado
	";
	$extraWhere = " devoluciones_vencimiento.fecha_ingreso BETWEEN '$fechai' AND '$fechaf' AND devoluciones_vencimiento.id_sucursal=$id_sucursal";
	if($lab != ""){
		$extraWhere .= " AND proveedor.nombre LIKE '%".$lab."%'";
	}
	$columns = array(
	array( 'db' => 'devoluciones_vencimiento.id_devolucion', 'dt' => 0, 'field' => 'id_devolucion' ),
	array( 'db' => 'devoluciones_vencimiento.fecha_ingreso', 'dt' => 1, 'field' => 'fecha_ingreso' ),
	array( 'db' => 'devoluciones_vencimiento.hora', 'dt' => 2, 'field' => 'hora' ),
	array( 'db' => 'usuario.nombre', 'dt' => 3, 'field' => 'nombre'),
	array( 'db' => 'proveedor.nombre', 'dt' => 4, 'field' => 'prov', 'as' => 'prov' ),
	array( 'db' => 'devoluciones_vencimiento.total', 'dt' =>5, 'field' => 'total' ),
	array( 'db' => 'devoluciones_vencimiento.finalizado', 'dt' =>6, 'formatter' => function( $estado, $row ){
		$label = "<label class='badge badge-danger'>Pendiente</label>";
		if($estado)
		{
			$label = "<label class='badge badge-success'>Finalizado</label>";
		}
		return $label; }, 'field' => 'finalizado' ),
	array( 'db' => 'devoluciones_vencimiento.alias_tipodoc', 'dt' =>7, 'field' => 'alias_tipodoc' ),
	array( 'db' => 'devoluciones_vencimiento.numero_doc ', 'dt' =>8, 'field' => 'numero_doc' ),
	array( 'db' => 'devoluciones_vencimiento.id_devolucion', 'dt' => 9, 'formatter' => function( $id_movimiento, $row ){
		$menudrop="<div class='btn-group'>
			<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
			<ul class='dropdown-menu dropdown-primary'>";
			include ("_core.php");
			$id_user=$_SESSION["id_usuario"];
			$id_sucursal=$_SESSION['id_sucursal'];
			$admin=$_SESSION["admin"];

								$filename='abono_devolucion.php';
								$link=permission_usr($id_user,$filename);
								if ($link!='NOT' || $admin=='1' )
									$menudrop.="<li><a data-toggle='modal' href='$filename?id_devolucion=$id_movimiento'  data-target='#viewModalFact' data-refresh='true'><i class=\"fa fa-check\"></i> Abonos</a></li>";
							$menudrop.="</ul>
						</div>";
						return $menudrop;},
						'field' => 'id_devolucion' ),
	);
	//echo json_encode(
	//SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
?>
