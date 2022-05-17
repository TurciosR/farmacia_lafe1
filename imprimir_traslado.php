<?php
include ("_core.php");
function initial(){
	$id_traslado = $_REQUEST ['id_traslado'];
	//$sql="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$sql="SELECT producto.descripcion,presentacion.nombre,presentacion_producto.unidad,traslado_detalle.cantidad
	FROM traslado_detalle
	JOIN producto ON producto.id_producto=traslado_detalle.id_producto
	JOIN presentacion_producto ON presentacion_producto.id_presentacion=traslado_detalle.id_presentacion
	JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE traslado_detalle.id_traslado=$id_traslado
		";
	$result = _query( $sql );
	$count = _num_rows( $result );

	$sql_suc=_query("SELECT CONCAT('Sucursal ',sucursal.n_sucursal,' ',sucursal.direccion) as destino FROM traslado JOIN sucursal ON traslado.id_sucursal_destino=sucursal.id_sucursal WHERE traslado.id_traslado=$id_traslado");


?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Ver detalle</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<th>Descripción</th>
							<th>Presentación</th>
							<th>Unidad</th>
							<th>Cantidad</th>

						</tr>
					</thead>
					<tbody>
							<?php
								if ($count > 0) {
									$su=_fetch_array($sql_suc);
									for($i = 0; $i < $count; $i ++) {
										$row = _fetch_array ( $result, $i );

										?>
										<tr>
											<td><?php echo $row['descripcion'] ?></td>
											<td><?php echo $row['nombre'] ?></td>

											<td style="text-align: right"><?php echo $row['unidad'] ?></td>
											<td style="text-align: right"><?php $a=$row['cantidad']/$row['unidad']; echo $a ?></td>
										</tr>
										<?php
									}
									?>
									<tr>
										<td colspan="4"><strong>Destino:</strong> <?php echo $su['destino'] ?></td>
									</tr>
									<?php
								}
							?>
						</tbody>
				</table>
			</div>
		</div>
		</div>

</div>
<div class="modal-footer">
	<button type="button" id_traslado="<?php echo $id_traslado ?>" class="btn btn-primary imprimir_traslado"> <i class="fa fa-print"></i> Imprimir</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->

<?php

}

function imprimir()
{
	$id_traslado=$_REQUEST['id_traslado'];

	$sql="SELECT producto.descripcion,presentacion.nombre,presentacion_producto.unidad,traslado_detalle.cantidad
	FROM traslado_detalle
	JOIN producto ON producto.id_producto=traslado_detalle.id_producto
	JOIN presentacion_producto ON presentacion_producto.id_presentacion=traslado_detalle.id_presentacion
	JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE traslado_detalle.id_traslado=$id_traslado
		";
	$result = _query( $sql );
	$count = _num_rows( $result );

	$sql_sucr=_fetch_array(_query("SELECT CONCAT('Sucursal ',sucursal.descripcion) as destino FROM traslado JOIN sucursal ON traslado.id_sucursal_destino=sucursal.id_sucursal WHERE traslado.id_traslado=$id_traslado"));
	$sql_suce=_fetch_array(_query("SELECT CONCAT('Sucursal ',sucursal.descripcion) as origen FROM traslado JOIN sucursal ON traslado.id_sucursal_origen=sucursal.id_sucursal WHERE traslado.id_traslado=$id_traslado"));
	$sql_emple=_fetch_array(_query("SELECT traslado.empleado_envia as envia,fecha,hora FROM traslado WHERE traslado.id_traslado=$id_traslado"));

	$id_sucursal=$_SESSION['id_sucursal'];
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	//$sql_dir_print="SELECT * FROM `config_dir` WHERE `id_sucursal`=1 ";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$info_mov="TRASLADO"."\n";
	$info_mov.="Origen: $sql_suce[origen]\n";
	$info_mov.="Destino: $sql_sucr[destino]\n";
	$info_mov.="Envia: $sql_emple[envia]\n";
	$info_mov.="Fecha: ".ED($sql_emple["fecha"])." Hora ".hora($sql_emple["hora"])."\n";

	$info_mov.="\n";

	$i=0;
	$j=0;
	$info_mov.=str_pad("DESCRIPCION",35," ",STR_PAD_RIGHT)."".str_pad(" CANT",5," ",STR_PAD_LEFT)."\n";
	$info_mov.=str_pad("-",40,"-",STR_PAD_RIGHT)."\n";
	while ($row=_fetch_array($result)) {
		// code...
		$i++;
		$j=$j+round($row['cantidad']/$row['unidad'],2);
			$info_mov.=str_pad(substr($row['descripcion'],0,35),35," ",STR_PAD_RIGHT);
			$info_mov.=str_pad(round($row['cantidad']/$row['unidad'],2) ,5," ",STR_PAD_LEFT)."\n";

			$info_mov.=str_pad("  ".$row['nombre']."(".($row['unidad']).")" ,40," ",STR_PAD_RIGHT)."\n";
	}

	$info_mov.=str_pad("Items: $i",40," ",STR_PAD_RIGHT)."\n";
	$info_mov.=str_pad("Cantidad: $j",40," ",STR_PAD_RIGHT)."\n";


	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
	$so_cliente='win';
	else
	$so_cliente='lin';
	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['movimiento'] =$info_mov;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);


}

function imprimir_transferencia()
{
	$id_movimiento=$_REQUEST['id_movimiento'];

	$sql = _query("SELECT producto.descripcion, ubicacion.descripcion as origen,est.descripcion as eo ,pos.posicion as po,ubi.descripcion as destino,estante.descripcion as ed,posicion.posicion as pd,movimiento_stock_ubicacion.cantidad,presentacion_producto.unidad,presentacion.nombre FROM movimiento_stock_ubicacion JOIN producto ON producto.id_producto=movimiento_stock_ubicacion.id_producto LEFT JOIN stock_ubicacion ON stock_ubicacion.id_su=movimiento_stock_ubicacion.id_origen LEFT JOIN ubicacion ON stock_ubicacion.id_ubicacion = ubicacion.id_ubicacion LEFT JOIN stock_ubicacion AS su ON su.id_su=movimiento_stock_ubicacion.id_destino LEFT JOIN ubicacion as ubi ON ubi.id_ubicacion=su.id_ubicacion LEFT JOIN estante ON estante.id_estante=su.id_estante LEFT JOIN posicion ON posicion.id_posicion=su.id_posicion LEFT JOIN estante AS est ON est.id_estante=stock_ubicacion.id_estante LEFT JOIN posicion as pos ON stock_ubicacion.id_posicion=pos.id_posicion JOIN presentacion_producto ON movimiento_stock_ubicacion.id_presentacion=presentacion_producto.id_presentacion JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE movimiento_stock_ubicacion.id_sucursal=$_SESSION[id_sucursal] AND movimiento_stock_ubicacion.id_mov_prod=$id_movimiento");

	$sql2=_query("SELECT movimiento_producto.fecha,movimiento_producto.hora, ori.descripcion as origen,des.descripcion as destino,usuario.nombre,empleado.nombre as empleado
	FROM movimiento_producto
	JOIN usuario ON usuario.id_usuario=movimiento_producto.id_empleado
	LEFT JOIN empleado ON empleado.id_empleado=usuario.id_empleado
	JOIN movimiento_stock_ubicacion ON movimiento_producto.id_movimiento=movimiento_stock_ubicacion.id_mov_prod
	JOIN stock_ubicacion as ubi ON ubi.id_su=movimiento_stock_ubicacion.id_origen
	JOIN ubicacion as ori ON ori.id_ubicacion= ubi.id_ubicacion
	JOIN stock_ubicacion as ubi2 ON ubi2.id_su=movimiento_stock_ubicacion.id_destino
	JOIN ubicacion as des ON des.id_ubicacion= ubi2.id_ubicacion
	WHERE movimiento_stock_ubicacion.id_mov_prod=$id_movimiento LIMIT 1");

	$id_sucursal=$_SESSION['id_sucursal'];
	//directorio de script impresion cliente
	$sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
	//$sql_dir_print="SELECT * FROM `config_dir` WHERE `id_sucursal`=1 ";
	$result_dir_print=_query($sql_dir_print);
	$row0=_fetch_array($result_dir_print);
	$dir_print=$row0['dir_print_script'];
	$shared_printer_win=$row0['shared_printer_matrix'];
	$shared_printer_pos=$row0['shared_printer_pos'];

	$info_mov="TRANSFERENCIA"."\n";

	while ($rowa=_fetch_array($sql2)) {
		// code...
		$info_mov.="Origen: $rowa[origen]\n";
		$info_mov.="Destino: $rowa[destino]\n";
		if ($rowa["empleado"]=="") {
			// code...
			$info_mov.="Envia: $rowa[nombre]\n";
		}
		else {
			// code...

			$info_mov.="Envia: $rowa[empleado]\n";
		}

		$info_mov.="Fecha: ".ED($rowa["fecha"])." Hora ".hora($rowa["hora"])."\n";
	}


	$info_mov.="\n";

	$info_mov.=str_pad("DESCRIPCION",35," ",STR_PAD_RIGHT)."".str_pad(" CANT",5," ",STR_PAD_LEFT)."\n";
	$info_mov.=str_pad("-",40,"-",STR_PAD_RIGHT)."\n";
	$i=0;
	$j=0;
	while ($row=_fetch_array($sql)) {
		// code...
			$i++;
			$j=$j+round($row['cantidad']/$row['unidad'],2);
			$info_mov.=str_pad(substr($row['descripcion'],0,35),35," ",STR_PAD_RIGHT);
			$info_mov.=str_pad(round($row['cantidad']/$row['unidad'],2) ,5," ",STR_PAD_LEFT)."\n";
			$info_mov.=str_pad("  ".$row['nombre']."(".($row['unidad']).")" ,40," ",STR_PAD_RIGHT)."\n";
	}

	$info_mov.=str_pad("Items: $i",40," ",STR_PAD_RIGHT)."\n";
	$info_mov.=str_pad("Cantidad: $j",40," ",STR_PAD_RIGHT)."\n";



	//Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
	$info = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($info, 'Windows') == TRUE)
	$so_cliente='win';
	else
	$so_cliente='lin';
	$nreg_encode['shared_printer_win'] =$shared_printer_win;
	$nreg_encode['shared_printer_pos'] =$shared_printer_pos;
	$nreg_encode['dir_print'] =$dir_print;
	$nreg_encode['movimiento'] =$info_mov;
	$nreg_encode['sist_ope'] =$so_cliente;
	echo json_encode($nreg_encode);


}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'imprimir' :
				imprimir();
				break;
			case 'imprimir_transferencia' :
				imprimir_transferencia();
				break;
		}
	}
}

?>
