<?php
include_once "_core.php";
function initial()
{
	$title='Reposición de Producto otras Sucursales';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/upload_file/fileinput.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	?>

	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox">
					<?php
					if ($links!='NOT' || $admin=='1'){
						?>
						<div class="ibox-title">
							<h5><?php echo $title ?></h5>
						</div>
						<div class="ibox-content">
              <div class="row">
                <div class="col-lg-3">
                  <label>Sucursal</label>
                  <select class="form-control" id="id_sucs" name="id_sucs">
                    <?php
                    $sql=_query("SELECT * FROM sucursal  WHERE id_sucursal!='$_SESSION[id_sucursal]'");
                    while ($row=_fetch_array($sql)) {
                      // code...
                      ?>
                      <option value="<?php echo $row['id_sucursal'] ?>"><?php echo $row['descripcion'] ?></option>
                      <?php
                    }
                     ?>
                  </select>
                </div>
                <div class="col-lg-3">
                  <br>
                  <button class="btn btn-primary" type="button" id="buscar" name="buscar">Buscar</button>
                </div>
              </div>
              <div class="row">
                <br>
                <div class="col-lg-12">
                  <table id="products" class="table table-bordered">
                    <thead>
                      <th class="col-lg-6">Producto</th>
                      <th class="col-lg-3">Existencia Minima</th>
                      <th class="col-lg-3">Existencia Actual</th>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>

						</div>
				</div>
			</div>
		</div>
	</div>
	<?php
		include_once ("footer.php");
    ?>
    <script>
    $("#id_sucs").select2();
    $(document).on('click', '#buscar', function(event) {
      $('#products>tbody').html("");
    	var id_suc = $("#id_sucs").val();
    		$.ajax({
    			url: 'http://lafe.apps-oss.com/pasarela_reposicion.php',
    			type: 'POST',
    			dataType: 'json',
    			data: {hash: 'd681824931f81f6578e63fd7e35095af',id_sucursal: id_suc},
    			success: function(datax) {
    				$('#products>tbody').html(datax.data);
    			}
    		})

    });

    $(document).on('change', '#id_sucs', function(event) {
      $('#products>tbody').html("");
    	var id_suc = $("#id_sucs").val();
    		$.ajax({
    			url: 'http://lafe.apps-oss.com/pasarela_reposicion.php',
    			type: 'POST',
    			dataType: 'json',
    			data: {hash: 'd681824931f81f6578e63fd7e35095af',id_sucursal: id_suc},
    			success: function(datax) {
    				$('#products>tbody').html(datax.data);
    			}
    		})

    });
    </script>
    <?php
	}
	else
	{
		echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
		include_once ("footer.php");
	}
}

if(!isset($_POST['process']))
{
	initial();
}
else
{
	if(isset($_POST['process']))
	{
		switch ($_POST['process'])
		{
      default:
      break;
		}
	}
}
?>
