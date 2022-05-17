<?php
 header("Access-Control-Allow-Origin: *");
 header('Access-Control-Allow-Methods: GET, POST');
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

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Buscar</h4>
</div>
<div class="modal-body">
	<!--div class="wrapper wrapper-content  animated fadeInRight"-->
	<div class="row" id="row1">
		<!--div class="col-lg-12"-->
		<?php

		?>
		<div class="row">
      <div class="col-lg-4">
				<label>Sucursal</label>
        <br>
        <select class="selectable_suc form-control" style="width:100%" name="">
          <?php
          $swlo = _query("SELECT sucursal.descripcion,sucursal.id_sucursal from sucursal where id_sucursal!=$_SESSION[id_sucursal]");
          while($rowad=_fetch_array($swlo))
          {
            ?>
            <option value="<?php echo $rowad['id_sucursal'] ?>"> <?php echo $rowad['descripcion'] ?> </option>
            <?php
          }
           ?>
        </select>
			</div>
			<div class="col-lg-8">
				<label>Producto</label>
				<input type="text" class="form-control external" id="external" name="external" value="">
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 pre-scrollable">
				<table class="table table-hover table-striped">
					<thead>
						<th class="col-lg-3">Marca</th>
						<th class="col-lg-5">Producto</th>
						<th class="col-lg-2">Existencias</th>
						<th class="col-lg-2">Precio</th>
					</thead>
					<tbody class="extern">

					</tbody>
				</table>
			</div>
		</div>
	</div>
		<!--/div-->
		<!--/div-->
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>

<script type="text/javascript">
	$(document).keydown(function(event) {

		if (event.keyCode==13) {
			$('.external').focus();
		}

	});
  $(document).ready(function() {
    $(".selectable_suc").select2();
  });
</script>

<!--/modal-footer -->

<?php

}



if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
		}
	}
}

?>
