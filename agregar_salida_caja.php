<?php
include ("_core.php");

function initial(){
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$id_sucursal=$_SESSION['id_sucursal'];
	date_default_timezone_set('America/El_Salvador');
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script

	//include ('facturacion_funcion_imprimir.php');
	//$sql="SELECT * FROM factura WHERE id_factura='$id_factura'";
	$sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND id_empleado=$id_user");
	$cuenta = _num_rows($sql_apertura);
	$row_apertura = _fetch_array($sql_apertura);
	$id_apertura = $row_apertura["id_apertura"];
	$empleado = $row_apertura["id_empleado"];
	$turno = $row_apertura["turno"];
	$fecha_apertura = $row_apertura["fecha"];
	$hora_apertura = $row_apertura["hora"];
	$monto_apertura = $row_apertura["monto_apertura"];

	$hora_actual = date('H:i:s');
	if($cuenta > 0)
	{
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Costos</h4>
</div>
<div class="modal-body">
	<!--div class="wrapper wrapper-content  animated fadeInRight"-->
	<div class="row" id="row1">
		<!--div class="col-lg-12"-->
		<?php

		?>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group has-info single-line">
					<label>Tipo Documento </label>
					<select class="form-control select" style="width: 100%" name="tipo_doc" id="tipo_doc">
						<option value="CCF">Credito Fiscal</option>
						<option value="COF">Factura</option>
						<option value="RE">Recibo</option>
						<option value="VAL">Vale</option>
					</select>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group has-info single-line">
					<label>Tipo</label>
					<select class="select" style="width: 100%" id="tipo" name="tipo">
						<?php
						$sql=_query("SELECT * FROM movimiento_caja_tipo WHERE ingreso=0 ORDER BY TIPO ASC");

						while($row=_fetch_array($sql))
						{
							echo"<option value='$row[id_tipo]'>$row[tipo]</option>";
						}
						 ?>
					</select>
				</div>
			</div>
			<div class="col-md-4">
				<div class="has-info single-line">
					<label>Numero de Documento</label>
					<input type="text" name="n_doc" id="n_doc" class="form-control">
				</div>
			</div>
		</div>
		<div class="row">
					<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Concepto</label>
	          	<input type='text'  class='form-control' id='concepto' name='concepto'>
	          </div>
					</div>
					<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Proveedor/Otro </label> <input type='text'  class='form-control' id='proveedor' name='proproveedor'>
	          </div>
					</div>
    	</div>
    	<div class="row">
    			<div class="col-md-6">
	          <div class="form-group has-info single-line">
	          	<label>Monto </label> <input type='text'  class='form-control numeric' id='monto' name='monto'>
	          </div>
					</div>
					<div class="col-md-6 caja_iva">
	          <div class="form-group has-info single-line">
	          	<label>Recibe </label> <input type='text'  class='form-control' id='recibe' name='recibe'>
	          </div>
					</div>
    	</div>


			<div class="row">

    	</div>
	</div>
		<!--/div-->
		<!--/div-->
	<input type="hidden" name="id_empleado" id="id_empleado" value="<?php echo $empleado;?>">
	<input type="hidden" name="turno" id="turno" value="<?php echo $turno;?>">
	<input type="hidden" name="id_apertura" id="id_apertura" value="<?php echo $id_apertura;?>">
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnSalida">Guardar</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>
<script type="text/javascript">
	$(".numeric").numeric(
		{
			negative:false,
		}
	);
	$(".select").select2();
</script>
<!--/modal-footer -->

<?php


}
else
{
	echo "<div></div><br><br><div class='alert alert-warning text-center'>No se ha encontrado una apertura vigente.</div>";
}
}

function salida()
{
	date_default_timezone_set("America/El_Salvador");
	$id_empleado = $_POST["id_empleado"];
	$id_apertura = $_POST["id_apertura"];
	$turno = $_POST["turno"];
	$concepto = $_POST["concepto"];
	$monto = $_POST["monto"];
	$id_sucursal=$_SESSION['id_sucursal'];
	$proveedor = $_POST["proveedor"];
	$tipo_doc = $_POST["tipo_doc"];
	$n_doc = $_POST["n_doc"];
	$recibe = $_POST["recibe"];
	$id_tipo=$_POST['id_tipo'];
	$autoriza = "";

	$fecha = date("Y-m-d");
	$hora = date("H:i:s");
	$iva = 0;
	if($tipo_doc == "CCF")
	{
		$iva = round($monto - ($monto / 1.13), 2);
	}
	$tabla = "mov_caja";
	$form_data = array(
		'fecha' => $fecha,
		'hora' => $hora,
		'valor' => $monto,
		'concepto' => $concepto,
		'id_empleado' => $id_empleado,
		'id_sucursal' => $id_sucursal,
		'salida' => 1,
		'turno' => $turno,
		'id_apertura' => $id_apertura,
		'nombre_proveedor' => $proveedor,
		'nombre_autoriza' => $autoriza,
		'alias_tipodoc' => $tipo_doc,
		'numero_doc' => $n_doc,
		'iva' => $iva,
		'nombre_recibe' => $recibe,
		'id_tipo' => $id_tipo
		);
	$insetar = _insert($tabla, $form_data);
	$id_mov= _insert_id();
	if($insetar)
	{
		$xdatos['typeinfo']='Success';
		$xdatos['msg']='Vale agregado correctamente !';
		$xdatos['process']='insert';
		$xdatos['id_mov']=$id_mov;

	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Error al realizar el vale !'._error();
	}
	echo json_encode($xdatos);
}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'salida' :
				salida();
				break;
		}
	}
}

?>
