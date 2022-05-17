<?php
include_once "_core.php";
function initial()
{
    $title='Agregar Producto';
    $_PAGE = array();
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
    $id_sucursal=$_SESSION['id_sucursal'];
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];

    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);

    $arrayPr = array();
    $qpresentacion=_query("SELECT * FROM presentacion ORDER BY nombre");
    $arrayPr[""] = "Seleccione";
    while ($row_pr=_fetch_array($qpresentacion)) {
        $idPr=$row_pr['id_presentacion'];
        $description=$row_pr['nombre'];
        $arrayPr[$idPr] = $description;
    }
    $arrayCat = array();
    $arrayCat[""] = "Seleccione";
    $qcategoria=_query("SELECT * FROM categoria ORDER BY nombre_cat");
    while ($row_cat=_fetch_array($qcategoria)) {
        $idCat=$row_cat['id_categoria'];
        $description=$row_cat['nombre_cat'];
        $arrayCat[$idCat] = $description;
    } ?>

    <style media="screen">
      #ygg tr td:nth-child(12)
      {
        display: none;
      }
  		#ygg tr th:nth-child(12)
      {
        display: none;
      }
      #ygg tr td:nth-child(13)
      {
        display: none;
      }
  		#ygg tr th:nth-child(13)
      {
        display: none;
      }
    </style>
	<div class="gray-bg">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox">
					<?php
                    if ($links!='NOT' || $admin=='1') {
                        ?>
						<div class="ibox-title">
							<div class="row">
                <div class="col-lg-10">
                  <h5><?php echo $title; ?> </h5>
                </div>
                <div class="col-lg-2">
                  <a href="admin_producto.php">
                    <button style="margin:0px;" type="button" class="btn btn-danger pull-right" name="button"> <i class="fa  fa-mail-reply"></i> Salir</button>
                  </a>
                </div>
              </div>
						</div>
						<input type="hidden" id="id_producto" name="id_producto" value="0">
						<input type="hidden" id="actual" name="actual" value="">
						<div class="ibox-content">
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group has-info single-line">
											<label>Código de Barra</label>
											<input type="text" placeholder="Digite Código de Barra" class="form-control" id="barcode" name="barcode">
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group has-info single-line">
											<label>Descripción</label>
											<input type="text" placeholder="Descripcion" class="form-control" id="descripcion" name="descripcion">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group has-info single-line">
											<label>Marca</label>
											<input type="text" placeholder="Marca" class="form-control" id="marca" name="marca">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group has-info single-line">
											<label>Stock Minimo</label>
											<input type="text" placeholder="Minimo" class="form-control" id="minimo" name="minimo">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group has-info single-line">
											<label>Proveedor</label>
											<select class="form-control select2" id="proveedor" name='proveedor'>
												<option value="">Seleccione</option>
												<?php
                                                $sql = _query("SELECT * FROM proveedor ORDER BY nombre ASC");
                        while ($row = _fetch_array($sql)) {
                            echo "<option value='".$row["id_proveedor"]."'>".$row["nombre"]."</option>";
                        } ?>
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group has-info single-line">
											<label>Categoría</label>
											<?php
                                            $select=crear_select2("id_categoria", $arrayCat, "", "width:100%;", 1);
                        echo $select; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group has-info single-line">
											<label class="control-label">Exento IVA</label>
											<div class='checkbox i-checks'>
												<label>
													<input type='checkbox'  id='exento' name='exento' value='1'><i></i>
												</label>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group has-info single-line">
											<label class="control-label">Producto perecedero</label>
											<div class='checkbox i-checks'>
												<label>
													<input type='checkbox'  id='perecedero' name='perecedero' value='1'><i></i>
												</label>
											</div>
										</div>
									</div>
								</div>

                <div class="row">
									<div class="col-lg-12">
										<div class="form-group has-info single-line">
											<label>Composición (Maximo 4 lineas)</label>
											<textarea class="form-control" id="composicion" name="composicion" rows="4" cols="80"></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12"><br>
                    <div class=" alert-warning text-center " style="font-weight: bold; border: 2px solid #8a6d3b; border-radius: 25px; margin-bottom:20px;"><h3>Presentaciones</h3></div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-2">
										<div class="form-group has-info single-line">
											<label>Presentación</label>
											<?php
                                            $select=crear_select2("id_presentacion", $arrayPr, "", "width:100%;", 1);
                        echo $select; ?>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group has-info single-line">
											<label>Descripción</label>
											<input type="text" name="desc_pre" id="desc_pre" class="form-control clear">
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group has-info single-line">
											<label>Unidades</label>
											<input type="text" name="unidad_pre" id="unidad_pre" class="form-control clear">
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group has-info single-line">
											<label>Costo</label>
											<input type="text" name="costo_pre" id="costo_pre" class="form-control clear">
										</div>
									</div>
									<div hidden class="col-md-2">
										<div class="form-group has-info single-line">
											<label>Precio</label>
											<input type="text" name="precio_pre" id="precio_pre" class="form-control clear">
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group has-info single-line">
											<label>Código de Barra</label>
											<br>
											<input type="text" name="bar" id="bar" class="form-control clear">
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group has-info">
											<br>
											<br>
											<a  class="btn btn-primary" id="add_pre"><i class="fa fa-plus"></i> Agregar</a>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<table id="ygg" class="table table-hover table-striped table-bordered">
											<thead>
												<tr>
													<th class="col-md-1">Cod. de Barra</th>
													<th class="col-md-1">Presentación</th>
													<th class="col-md-1">Descripción</th>
													<th class="col-md-1">Unidad</th>
													<th class="col-md-1">Costo</th>
													<th class="col-md-1">Precio M</th>
													<th class="col-md-1">Precio 1</th>
													<th class="col-md-1">Precio 2</th>
													<th class="col-md-1">Precio 3</th>
													<th class="col-md-1">Precio 4</th>
													<th class="col-md-1">Precio 5</th>
													<th class="col-md-1">Precio 6</th>
													<th class="col-md-1">Precio 7</th>
													<th class="col-md-1">Acción</th>
												</tr>
											</thead>
											<tbody id="presentacion_table">

											</tbody>
										</table>
									</div>
								</div>
								<div>
									<input type="hidden" name="process" id="process" value="insert"><br>
									<button type="button" class="btn btn-primary m-t-n-xs" id="submit1" name="submit1">Guardar</button>
								</div>

							<div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								<div class='modal-dialog modal-md'>
									<div class='modal-content modal-md'></div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							</div><!-- /.modal -->
						</div>
					</div>
				</div>
		<?php
        include_once("footer.php");
                        echo "<script src='js/funciones/funciones_producto.js'></script>";
                    } else {
                        echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
                        include_once("footer.php");
                    }
}
function insertar()
{
    $id_producto=$_POST["id_producto"];
    $descripcion=$_POST["descripcion"];
    $barcode=$_POST["barcode"];
    $marca=$_POST["marca"];
    $minimo=$_POST["minimo"];
    $id_sucursal=$_SESSION["id_sucursal"];
    $id_categoria=$_POST["id_categoria"];
    $tipo_prod_servicio="PRODUCTO";
    $perecedero=$_POST["perecedero"];
    $proveedor=$_POST["proveedor"];
    $descripcion=trim($descripcion);
    $barcode=trim($barcode);
    $name_producto="";
    $exento=$_POST["exento"];
    $fecha_hoy=date("Y-m-d");
    $lista = $_POST["lista"];
    $cuantos = $_POST["cuantos"];
    $composicion = $_POST["composicion"];
    $upds_server="";

    if ($perecedero==0) {
        $fecha_vencimiento=null;
    }
    _begin();


    $sql_result=_query("SELECT id_producto,descripcion,barcode FROM producto WHERE descripcion='$descripcion'");
    $numrows=_num_rows($sql_result);
    $row_update=_fetch_array($sql_result);
    $id_update=$row_update["id_producto"];
    $name_producto=trim($row_update["descripcion"]);
    $descrip_producto_existe=false;
    if ($name_producto!="" && $descripcion!="") {
        $descrip_producto_existe=true;
    }
    if ($barcode=="") {
        $barcodeexiste=false;
    }
    if ($barcode!="") {
        $sql_barcode="SELECT id_producto,descripcion,barcode FROM producto WHERE barcode='$barcode'";
        $sql_result_barcode=_query($sql_barcode);
        $numrows_barcode=_num_rows($sql_result_barcode);
        if ($numrows_barcode>0) {
            $barcodeexiste=true;
        } else {
            $barcodeexiste=false;
        }
    }
    $descripcion=strtoupper($descripcion);


    $table = 'producto';
    $form_data = array(
        'descripcion' => $descripcion,
        'barcode' => $barcode,
        'marca' => $marca,
        'minimo' => $minimo,
        'exento' => $exento,
        'estado' => 1,
        'id_proveedor' => $proveedor,
        'id_categoria' => $id_categoria,
        'perecedero' => $perecedero,
        'composicion' => $composicion,
    );
    if (!$descrip_producto_existe) {
        if (!$barcodeexiste) {
            $insertar =_insert($table, $form_data);


            if ($insertar) {
                $id_producto2 = _insert_id();
                $xdatos['id_producto']=$id_producto2;

                $table_cambio="log_cambio_local";
        				$form_data = array(
        					'process' => 'insert',
        					'tabla' =>  "producto",
        					'fecha' => date("Y-m-d"),
        					'hora' => date('H:i:s'),
        					'id_usuario' => $_SESSION['id_usuario'],
        					'id_sucursal' => $_SESSION['id_sucursal'],
        					'id_primario' =>$id_producto2,
        					'prioridad' => "1"
        				);
        				$insert_cambio=_insert($table_cambio,$form_data);
        				$id_cambio=_insert_id();

        				$table_detalle_cambio="log_detalle_cambio_local";
        				$form_data = array(
        					'id_log_cambio' => 	$id_cambio,
        					'tabla' => 'producto',
        					'id_verificador' => $id_producto2
        				);
        				_insert($table_detalle_cambio,$form_data);

                $explora = explode(";", $lista);
                $c = count($explora);
                $n = 0;
                for ($i=0; $i < $c - 1 ; $i++) {
                    $ex = explode(",", $explora[$i]);
                    $id_presen = $ex[0];
                    $des = $ex[1];
                    $uni = $ex[2];
                    $pre = $ex[3];
                    $cost = $ex[5];
                    $bar=$ex[6];
                    $precios=$ex[7];
                    $precio_m=$ex[8];

                    $tabla_p = "presentacion_producto";
                    $form_pre = array(
                            'id_producto' => $id_producto2,
                            'presentacion' => $id_presen,
                            'descripcion' => $des,
                            'unidad' => $uni,
                            'precio' => $pre,
                            'costo' => $cost,
                            'costo_s_iva' => $cost*1.13,
                            'activo' => 1,
                            'id_sucursal'=>$id_sucursal,
                            'barcode' => $bar,
                            'precio_marca' => $precio_m,
                        );
                    $insert_pre = _insert($tabla_p, $form_pre);
                    $id_presenta=_insert_id();

                    $table_detalle_cambio="log_detalle_cambio_local";
                    $form_data = array(
                      'id_log_cambio' => 	$id_cambio,
                      'tabla' => "presentacion_producto",
                      'id_verificador' => $id_presenta
                    );
                    $insertar_dc=_insert($table_detalle_cambio,$form_data);


                    if ($insert_pre) {
                        $n++;
                    }

                    $precios_ins = explode("#", $precios);
                    $c2 = count($precios_ins);
										$vrr = true;
										$precioprime = 0;
                    for ($w=0; $w < $c2 - 1 ; $w++)
										{
                        $exa = explode("|", $precios_ins[$w]);
                        if($vrr)
                        {
                          $precioprime = $exa[2];
                          $vrr = false;
                        }
                        $table="presentacion_producto_precio";
                        $form_data = array(
                                'id_producto' => $id_producto2,
                                'id_presentacion' => $id_presenta,
                                'id_sucursal' => $id_sucursal,
                                'precio' => $exa[2],
                                'desde' => $exa[0],
                                'hasta' => $exa[1],
                            );

                        $insr=_insert($table, $form_data);
                        $id_ppp=_insert_id();
          							$table_detalle_cambio="log_detalle_cambio_local";
          							$form_data = array(
          								'id_log_cambio' => 	$id_cambio,
          								'tabla' => "presentacion_producto_precio",
          								'id_verificador' => $id_ppp
          							);
          							$insertar_dc=_insert($table_detalle_cambio,$form_data);
                    }
										$form_preupd = array(
											'precio' => $precioprime,
										);
										$whereupd = "id_presentacion='$id_presenta'";
										$updpre = _update($tabla_p, $form_preupd, $whereupd);
                }
                if ($n == ($c-1)) {
                    $form_data2 = array(
                    'id_producto' => $id_producto2,
                    'stock' => 0,
                    'costo_unitario'=>0,
                    'precio_unitario'=>0,
                    'create_date'=>$fecha_hoy,
                    'update_date'=>$fecha_hoy,
                    'id_sucursal' =>$id_sucursal,
                  );
                    $insert_stock = _insert("stock", $form_data2);
                    $id_stock=_insert_id();

                    $sql1=_query("SELECT presentacion_producto_precio.precio
											FROM presentacion_producto_precio
											WHERE presentacion_producto_precio.id_presentacion=
											(SELECT presentacion_producto.id_presentacion
												FROM presentacion_producto
												WHERE presentacion_producto.id_producto=$id_producto2
												AND presentacion_producto.id_sucursal=$id_sucursal
												ORDER BY presentacion_producto.unidad ASC LIMIT 1)
												ORDER BY presentacion_producto_precio.desde ASC LIMIT 1");
                    if (_num_rows($sql1)>0) {
                        // code...
                        $a=_fetch_array($sql1);
                        $table='stock';
                        $form_data = array(
                        'precio_unitario' => $a['precio'],
                    );
                        $where_clause="id_producto='".$id_producto2."'";
                        $update=_update($table, $form_data, $where_clause);
                    }

                    $xdatos['typeinfo']='Success';
                    $xdatos['msg']='Registro ingresado con exito!';
                    $xdatos['process']='insert';
                    _commit();
                } else {
                    _rollback();
                    $xdatos['typeinfo']='Error';
                    $xdatos['msg']='Registro no pudo ser ingresado !';
                    $xdatos['process']='insert';
                }
            } else {
                _rollback();
                $xdatos['typeinfo']='Error';
                $xdatos['msg']='Registro no pudo ser ingresado !';
                $xdatos['process']='insert';
            }
        } else {
            _rollback();
            $xdatos['typeinfo']='Error';
            $xdatos['msg']='El Barcode ya está asignado a otro producto!';
            $xdatos['process']='existbarcode';
        }
    } else {
        _rollback();
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Ya existe un producto registrado con estos datos!';
        $xdatos['process']='noinsert';
    }

    echo json_encode($xdatos);
}
function lista()
{
    $lista = "";
    $sql_presentacion = _query("SELECT * FROM presentacion");
    $cuenta = _num_rows($sql_presentacion);
    if ($cuenta > 0) {
        $lista.= "<select id='presen' class='col-md-12 select2 valcel'>";
        $lista.= "<option value='0'>Seleccione</option>";
        while ($row = _fetch_array($sql_presentacion)) {
            $id_presentacion = $row["id_presentacion"];
            $descripcion = $row["descripcion_pr"];
            $lista.= "<option value=".$id_presentacion.">".$descripcion."</option>";
        }
        $lista.="</select>";
    }
    $xdatos['select'] = $lista;
    echo json_encode($xdatos);
}

if (!isset($_POST['process'])) {
    initial();
} else {
    if (isset($_POST['process'])) {
        switch ($_POST['process']) {
            case 'insert':
            insertar();
            break;
            case 'lista':
            lista();
            break;
        }
    }
}
?>
