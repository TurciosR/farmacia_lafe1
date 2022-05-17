<?php
include_once "_core.php";

function initial()
{
  $title = "Carga de Productos a Inventario";
  $_PAGE = array();
  $_PAGE ['title'] = $title;
  $_PAGE ['links'] = null;
  $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

  include_once "header.php";
  include_once "main_menu.php";

  $sql="SELECT * FROM producto";

  $result=_query($sql);
  $count=_num_rows($result);
  //permiso del script
  $id_user=$_SESSION["id_usuario"];
  $admin=$_SESSION["admin"];

  $uri = $_SERVER['SCRIPT_NAME'];
  $filename=get_name_script($uri);
  $links=permission_usr($id_user, $filename);
  $fecha_actual=date("Y-m-d");

  ?>
  <style media="screen">
    #inventable tr td:first-child
    {
      display:  none;
    }
    #inventable tr th:first-child
    {
      display:  none;
    }
    #inventable tr th:nth-child(4)
    {
      display:  none;
    }
    #inventable tr td:nth-child(4)
    {
      display:  none;
    }
  </style>
  <div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox">
          <div class="ibox-title">
            <h5><?php echo $title;?></h5>
          </div>
          <?php if ($links!='NOT' || $admin=='1') { ?>
            <div class="ibox-content">

              <div class='row' id='form_invent_inicial'>
                <div class="col-lg-6">
                  <div class="form-group has-info">
                    <label>Concepto</label>
                    <input type='text' class='form-control' value='INVENTARIO INICIAL' id='concepto' name='concepto'>
                  </div>
                </div>
                <div class="col-lg-3">
                  <div class="form-group has-info">
                    <label>Destino</label>
                    <select class="form-control select" style="width:100%;" id="destino" name="destino">
                      <?php
                      $sql = _query("SELECT * FROM ubicacion WHERE id_sucursal='$id_sucursal' ");

                      $id_z=0;
                      $i=0;
                      while($row = _fetch_array($sql))
                      {
                        echo "<option value='".$row["id_ubicacion"]."'>".$row["descripcion"]."</option>";

                        if ($i==0) {
                          $id_z=$row["id_ubicacion"];
                          $i++;
                        }
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class='col-lg-3'>
                  <div class='form-group has-info'>
                    <label>Fecha</label>
                    <input type='text' class='datepick form-control' value='<?php echo $fecha_actual; ?>' id='fecha1' name='fecha1'>
                  </div>
                </div>
              </div>
              <div class="row" id='buscador'>
                <div class="col-lg-6">
                  <div class='form-group has-info'><label>Buscar Producto o Servicio</label>
                    <input type="text" id="producto_buscar" name="producto_buscar" size="20" class="producto_buscar form-control" placeholder="Ingrese nombre de producto"  data-provide="typeahead">
                  </div>
                </div>

              </div>
              <div class="ibox">
                <div class="row">
                  <div class="ibox-content">
                    <!--load datables estructure html-->
                    <header>
                      <h4 class="text-navy">Lista de Productos</h4>
                    </header>
                    <section>
                      <table class="table table-striped table-bordered table-condensed" id="inventable">
                        <thead>
                          <tr>
                            <th class="col-lg-1">Id</th>
                            <th class="col-lg-4">Nombre</th>
                            <th class="col-lg-1">Presentación</th>
                            <th class="col-lg-1">Descripción</th>
                            <th class="col-lg-1">Prec. C</th>
                            <th class="col-lg-1">Prec. V</th>
                            <th class="col-lg-1">Cantidad</th>
                            <th class="col-lg-1">Vence</th>
                            <th class="col-lg-2">Estante</th>
                            <th class="col-lg-1">Posición</th>
                            <th class="col-lg-1">Acci&oacute;n</th>
                          </tr>
                        </thead>

                        <tfoot>
                          <tr>
                            <td></td>
                            <td>Total Dinero <strong>$</strong></td>
                            <td id='total_dinero'>$0.00</td>
                            <td></td>
                            <td colspan=2>Total Producto</td>
                            <td id='totcant'>0</td>
                            <td></td>
                            <td></td>
                          </tr>
                        </tfoot>
                        <tbody>
                        </tbody>
                      </table>
                      <input type="hidden" name="autosave" id="autosave" value="false-0">
                    </section>
                    <input type="hidden" name="process" id="process" value="insert"><br>
                    <div>

                      <input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                      <input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename ?> ">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div><!--div class='ibox-content'-->
        </div>
      </div>
    </div>
  </div>

  <?php
  include_once ("footer.php");
  echo "<script src='js/funciones/funciones_inventario_2.js'></script>";
} //permiso del script
else {
  echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
}
}

function insertar()
{
  $cuantos = $_POST['cuantos'];
  $datos = $_POST['datos'];
  $destino = $_POST['destino'];
  $estante = 0;
  $posicion = 0;

  $fecha = $_POST['fecha'];
  $total_compras = $_POST['total'];
  $concepto=$_POST['concepto'];
  $hora=date("H:i:s");
  $fecha_movimiento = date("Y-m-d");
  $id_empleado=$_SESSION["id_usuario"];

  $id_sucursal = $_SESSION["id_sucursal"];
  $sql_num = _query("SELECT ii FROM correlativo WHERE id_sucursal='$id_sucursal'");
  $datos_num = _fetch_array($sql_num);
  $ult = $datos_num["ii"]+1;
  $numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_II';
  $tipo_entrada_salida='ENTRADA DE INVENTARIO';

  _begin();
  $z=1;

  /*actualizar los correlativos de II*/
  $corr=1;
  $table="correlativo";
  $form_data = array(
    'ii' =>$ult
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
    $concepto='ENTRADA DE INVENTARIO';
  }
  $table='movimiento_producto';
  $form_data = array(
    'id_sucursal' => $id_sucursal,
    'correlativo' => $numero_doc,
    'concepto' => $concepto,
    'total' => $total_compras,
    'tipo' => 'ENTRADA',
    'proceso' => 'II',
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
  for ($i=0;$i<$cuantos ;$i++)
  {
    list($id_producto,$precio_compra,$precio_venta,$cantidad,$unidades,$fecha_caduca,$id_presentacion,$estante,$posicion)=explode('|',$lista[$i]);
    $sql_su="SELECT id_su, cantidad FROM stock_ubicacion WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal' AND id_ubicacion='$destino' AND id_estante='$estante' AND id_posicion='$posicion'";
    $stock_su=_query($sql_su);
    $nrow_su=_num_rows($stock_su);
    $id_su="";
    /*cantidad de una presentacion por la unidades que tiene*/
    $cantidad=$cantidad*$unidades;
    if($nrow_su >0)
    {
      $row_su=_fetch_array($stock_su);
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
        'id_ubicacion' => $destino,
        'id_estante' => $estante,
        'id_posicion' => $posicion,
      );
      $table_su = "stock_ubicacion";
      $insert_su = _insert($table_su, $form_data_su);
      $id_su=_insert_id();
    }
    if(!$insert_su)
    {
      $m=0;
    }
    $sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
    $stock2=_query($sql2);
    $row2=_fetch_array($stock2);
    $nrow2=_num_rows($stock2);
    if ($nrow2>0)
    {
      $existencias=$row2['stock'];
    }
    else
    {
      $existencias=0;
    }
    $sql_lot = _query("SELECT MAX(numero) AS ultimo FROM lote WHERE id_producto='$id_producto'");
    $datos_lot = _fetch_array($sql_lot);
    $lote = $datos_lot["ultimo"]+1;
    $table1= 'movimiento_producto_detalle';
    $cant_total=$cantidad+$existencias;
    $form_data1 = array(
      'id_movimiento'=>$id_movimiento,
      'id_producto' => $id_producto,
      'cantidad' => $cantidad,
      'costo' => $precio_compra,
      'precio' => $precio_venta,
      'stock_anterior'=>$existencias,
      'stock_actual'=>$cant_total,
      'lote' => $lote,
      'id_presentacion' => $id_presentacion,
      'fecha' => $fecha_movimiento,
      'hora' => $hora
    );
    $insert_mov_det = _insert($table1,$form_data1);
    if(!$insert_mov_det)
    {
      $j = 0;
    }
    $table2= 'stock';
    if($nrow2==0)
    {
      $cant_total=$cantidad;
      $form_data2 = array(
        'id_producto' => $id_producto,
        'stock' => $cant_total,
        'costo_unitario'=>$precio_compra,
        'precio_unitario'=>$precio_venta,
        'create_date'=>$fecha_movimiento,
        'update_date'=>$fecha_movimiento,
        'id_sucursal' => $id_sucursal
      );
      $insert_stock = _insert($table2,$form_data2 );
    }
    else
    {
      $cant_total=$cantidad+$existencias;
      $form_data2 = array(
        'id_producto' => $id_producto,
        'stock' => $cant_total,
        'costo_unitario'=>round(($precio_compra/$unidades),2),
        'precio_unitario'=>round(($precio_venta/$unidades),2),
        'update_date'=>$fecha_movimiento,
        'id_sucursal' => $id_sucursal
      );
      $where_clause="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";
      $insert_stock = _update($table2,$form_data2, $where_clause );
    }
    if(!$insert_stock)
    {
      $k = 0;
    }
    if ($fecha_caduca!="0000-00-00" && $fecha_caduca!="")
    {
      $sql_caduca="SELECT * FROM lote WHERE id_producto='$id_producto' and fecha_entrada='$fecha_movimiento' and vencimiento='$fecha_caduca' ";
      $result_caduca=_query($sql_caduca);
      $row_caduca=_fetch_array($result_caduca);
      $nrow_caduca=_num_rows($result_caduca);
      /*if($nrow_caduca==0){*/
      $table_perece= 'lote';

      if($fecha_movimiento>=$fecha_caduca)
      {
        $estado='VIGENTE';
      }
      else
      {
        $estado='VIGENTE';
      }
      $form_data_perece = array(
        'id_producto' => $id_producto,
        'referencia' => $numero_doc,
        'numero' => $lote,
        'fecha_entrada' => $fecha_movimiento,
        'vencimiento'=>$fecha_caduca,
        'precio' => $precio_compra,
        'cantidad' => $cantidad,
        'estado'=>$estado,
        'id_sucursal' => $id_sucursal,
        'id_presentacion' => $id_presentacion,
      );
      $insert_lote = _insert($table_perece,$form_data_perece );
    }
    else
    {
      $sql_caduca="SELECT * FROM lote WHERE id_producto='$id_producto' AND fecha_entrada='$fecha_movimiento'";
      $result_caduca=_query($sql_caduca);
      $row_caduca=_fetch_array($result_caduca);
      $nrow_caduca=_num_rows($result_caduca);
      $table_perece= 'lote';
      $estado='VIGENTE';

      $form_data_perece = array(
        'id_producto' => $id_producto,
        'referencia' => $numero_doc,
        'numero' => $lote,
        'fecha_entrada' => $fecha_movimiento,
        'vencimiento'=>$fecha_caduca,
        'precio' => $precio_compra,
        'cantidad' => $cantidad,
        'estado'=>$estado,
        'id_sucursal' => $id_sucursal,
        'id_presentacion' => $id_presentacion,
      );
      $insert_lote = _insert($table_perece,$form_data_perece );
    }
    if(!$insert_lote)
    {
      $l = 0;
    }

    $table="movimiento_stock_ubicacion";
    $form_data = array(
      'id_producto' => $id_producto,
      'id_origen' => 0,
      'id_destino'=> $id_su,
      'cantidad' => $cantidad,
      'fecha' => $fecha_movimiento,
      'hora' => $hora,
      'anulada' => 0,
      'afecta' => 0,
      'id_sucursal' => $id_sucursal,
      'id_presentacion'=> $id_presentacion,
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

    $precio_con_iva=0;
    $alias_tipodoc="CCF";
    if ($alias_tipodoc=="CCF") {
      // code...
      $precio_con_iva=$precio_compra*1.13;

    }
    else {
      // code...
      $precio_con_iva=$precio_compra;
      $precio_compra=$precio_compra/1.13;

    }

    $table_prese_pro="presentacion_producto";
    $form_data_p_p = array(
      'precio'=>$precio_venta,
      'costo'=>$precio_compra,
      'costo_s_iva'=>$precio_con_iva,
    );
    $where_clause_p_p="WHERE id_producto='$id_producto' and id_presentacion='$id_presentacion' AND id_sucursal='$id_sucursal'";
    $update_p_p = _update($table_prese_pro,$form_data_p_p, $where_clause_p_p );
    if($update_p_p){


    }else{
      $d=0;
    }

    //actualizar el precio en producto presentacion precio
    $table_prese_pre1="presentacion_producto_precio";
    $form_data_p_pre = array(
      'precio'=>$precio_venta
    );
    $where_clause_p_pre1="WHERE id_producto='$id_producto' and id_presentacion='$id_presentacion' AND id_sucursal='$id_sucursal' AND desde=0";
    $where_clause_p_pre2="WHERE id_producto='$id_producto' and id_presentacion='$id_presentacion' AND id_sucursal='$id_sucursal' AND desde=1";
    $update_p_pre1 = _update($table_prese_pre1,$form_data_p_pre, $where_clause_p_pre1 );
    $update_p_pre1 = _update($table_prese_pre1,$form_data_p_pre, $where_clause_p_pre2 );

    $sql_actuprecio1 = _query("SELECT id_prepd FROM $table_prese_pre1 $where_clause_p_pre1");
    while ($row_actu=_fetch_array($sql_actuprecio1)) {
      // code...
      $id_ppp=$row_actu['id_prepd'];
      $table_cambio="log_cambio_local";
        $form_data = array(
          'process' => 'update',
          'tabla' =>  "presentacion_producto_precio",
          'fecha' => date("Y-m-d"),
          'hora' => date('H:i:s'),
          'id_usuario' => $_SESSION['id_usuario'],
          'id_sucursal' => $_SESSION['id_sucursal'],
          'id_primario' =>$id_ppp,
          'prioridad' => "2"
        );
        $insert_cambio=_insert($table_cambio,$form_data);
        $id_cambio=_insert_id();

        $table_detalle_cambio="log_detalle_cambio_local";
        $form_data = array(
          'id_log_cambio' => 	$id_cambio,
          'tabla' => 'presentacion_producto_precio',
          'id_verificador' => $id_ppp
        );
        _insert($table_detalle_cambio,$form_data);
    }

    $sql_actuprecio1 = _query("SELECT id_prepd FROM $table_prese_pre1 $where_clause_p_pre2");
    while ($row_actu=_fetch_array($sql_actuprecio1)) {
      // code...
      $id_ppp=$row_actu['id_prepd'];
      $table_cambio="log_cambio_local";
        $form_data = array(
          'process' => 'update',
          'tabla' =>  "presentacion_producto_precio",
          'fecha' => date("Y-m-d"),
          'hora' => date('H:i:s'),
          'id_usuario' => $_SESSION['id_usuario'],
          'id_sucursal' => $_SESSION['id_sucursal'],
          'id_primario' =>$id_ppp,
          'prioridad' => "2"
        );
        $insert_cambio=_insert($table_cambio,$form_data);
        $id_cambio=_insert_id();

        $table_detalle_cambio="log_detalle_cambio_local";
        $form_data = array(
          'id_log_cambio' => 	$id_cambio,
          'tabla' => 'presentacion_producto_precio',
          'id_verificador' => $id_ppp
        );
        _insert($table_detalle_cambio,$form_data);
    }

  }
  if($insert_mov &&$corr &&$z && $j && $k && $l && $m)
  {
    _commit();
    $xdatos['typeinfo']='Success';
    $xdatos['msg']='Registro ingresado con exito!';
  }
  else
  {
    _rollback();
    $xdatos['typeinfo']='Error';
    $xdatos['msg']='Registro de no pudo ser ingresado!';
  }
  echo json_encode($xdatos);
}
function consultar_stock()
{
  $id_producto = $_REQUEST['id_producto'];
  $id_sucursal=$_SESSION['id_sucursal'];

  $i=0;
  $unidadp=0;
  $preciop=0;
  $costop=0;
  $descripcionp=0;

  $sql_p=_query("SELECT presentacion.nombre, prp.descripcion,prp.id_presentacion,prp.unidad,prp.costo,prp.precio FROM presentacion_producto AS prp JOIN presentacion ON presentacion.id_presentacion=prp.presentacion WHERE prp.id_producto=$id_producto AND prp.activo=1 AND prp.id_sucursal=$id_sucursal");
  $select="<select class='sel'>";
  while ($row=_fetch_array($sql_p))
  {
    if ($i==0)
    {
      $unidadp=$row['unidad'];
      $costop=$row['costo'];
      $preciop=$row['precio'];
      $descripcionp=$row['descripcion'];

      $xc=0;

			$sql_rank=_query("SELECT presentacion_producto_precio.id_prepd,presentacion_producto_precio.desde,presentacion_producto_precio.hasta,presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$row[id_presentacion] AND presentacion_producto_precio.id_sucursal=$_SESSION[id_sucursal] AND presentacion_producto_precio.precio!=0 ORDER BY presentacion_producto_precio.desde ASC LIMIT 1
				");

				while ($rowr=_fetch_array($sql_rank)) {
					# code...
					if($xc==0)
					{

						$preciop=$rowr['precio'];
					}
				}
    }
    $select.="<option value='".$row["id_presentacion"]."'>".$row["nombre"]." (".$row["unidad"].")</option>";
    $i=$i+1;
  }
  $select.="</select>";
  $xdatos['select']= $select;
  $xdatos['costop']= $costop;
  $xdatos['preciop']= $preciop;
  $xdatos['unidadp']= $unidadp;
  $xdatos['descripcionp']= $descripcionp;
  $xdatos['i']=$i;

  $id_z=$_REQUEST['id_ubicacion'];
  $id_estante=0;
  $id_posicion=0;

  $sql = _query("SELECT stock_ubicacion.id_su ,stock_ubicacion.id_estante,stock_ubicacion.id_posicion,stock_ubicacion.id_ubicacion,stock_ubicacion.cantidad FROM stock_ubicacion WHERE id_producto=$id_producto AND id_ubicacion=$id_z AND id_estante!=0 AND cantidad>0 ORDER BY id_su DESC  LIMIT 1");
  if (_num_rows($sql)>0) {
    while ($row = _fetch_array($sql)) {
      $id_estante=$row['id_estante'];
      $id_posicion=$row['id_posicion'];
    }
  }
  else {

    $sql = _query("SELECT stock_ubicacion.id_su ,stock_ubicacion.id_estante,stock_ubicacion.id_posicion,stock_ubicacion.id_ubicacion,stock_ubicacion.cantidad FROM stock_ubicacion WHERE id_producto=$id_producto AND id_ubicacion=$id_z AND id_estante!=0 ORDER BY id_su DESC  LIMIT 1");
    if (_num_rows($sql)>0) {
      while ($row = _fetch_array($sql)) {
        $id_estante=$row['id_estante'];
        $id_posicion=$row['id_posicion'];
      }
    }

  }

  $estante='';
    $estante.="<select style='width:100%;' class='form-control sel2' id='estante' name='estante'>
    <option value='0'>Seleccione</option>";
    $sql = _query("SELECT * FROM estante WHERE id_ubicacion='$id_z' ORDER BY descripcion ASC");
    while($row = _fetch_array($sql))
    {
      $selected="";
      if ($row["id_estante"]==$id_estante) {
        // code...
        $selected=" selected ";
      }
      $estante.= "<option $selected value='".$row["id_estante"]."'>".$row["descripcion"]."</option>";
    }
  $estante.='</select>';

  $xdatos['estante']=$estante;

  $posicion="<select style='width:100%;' class='form-control sel2' id='posicion' name='posicion'>
    <option value='0'>Seleccione</option>";
    $sql = _query("SELECT * FROM posicion WHERE id_estante='$id_estante'");
    while($row = _fetch_array($sql))
    {
      $selected="";
      if ($row["id_posicion"]==$id_posicion) {
        // code...
        $selected=" selected ";
      }
      $posicion.= "<option $selected value='".$row["id_posicion"]."'>".$row["posicion"]."</option>";
    }
  $posicion.='</select>';

  $xdatos['posicion']=$posicion;

  $sql_perece="SELECT * FROM producto WHERE id_producto='$id_producto'";
  $result_perece=_query($sql_perece);
  $row_perece=_fetch_array($result_perece);
  $perecedero=$row_perece['perecedero'];
  //$perecedero=0;
  $xdatos['perecedero'] = $perecedero;
  $xdatos['categoria']=$row_perece['id_categoria'];
  echo json_encode($xdatos);
}
function getpresentacion()
{
  $id_presentacion =$_REQUEST['id_presentacion'];
  $sql=_fetch_array(_query("SELECT * FROM presentacion_producto WHERE id_presentacion=$id_presentacion"));
  $precio=$sql['precio'];
  $unidad=$sql['unidad'];
  $descripcion=$sql['descripcion'];
  $costo=$sql['costo'];

  $xc=0;

  $sql_rank=_query("SELECT presentacion_producto_precio.id_prepd,presentacion_producto_precio.desde,presentacion_producto_precio.hasta,presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$id_presentacion AND presentacion_producto_precio.id_sucursal=$_SESSION[id_sucursal] AND presentacion_producto_precio.precio!=0 ORDER BY presentacion_producto_precio.desde ASC LIMIT 1
    ");

    while ($rowr=_fetch_array($sql_rank)) {
      # code...
      if($xc==0)
      {

        $precio=$rowr['precio'];
      }
    }
  $xdatos['precio']=$precio;
  $xdatos['costo']=$costo;
  $xdatos['unidad']=$unidad;
  $xdatos['descripcion']=$descripcion;
  echo json_encode($xdatos);
}

function estantes()
{
  $id_ubicacion = $_POST["id_ubicacion"];
  $sql = _query("SELECT * FROM estante WHERE id_ubicacion='$id_ubicacion' ORDER BY descripcion ASC");
  if(_num_rows($sql)>0)
  {
    $opt = "<option value='0'>Seleccione</option>";
    while ($row = _fetch_array($sql)) {
      $opt .="<option value='".$row["id_estante"]."'>".$row["descripcion"]."</option>";
    }
    $xdatos["typeinfo"] = "Success";
    $xdatos["opt"] = $opt;
  }
  else
  {
    $opt = "<option value='0'>Seleccione</option>";
    $xdatos["opt"] = $opt;
    $xdatos["typeinfo"] = "Success";
  }
  echo json_encode($xdatos);
}

function posicion()
{
  $id_estante = $_POST["id_estante"];
  $sql = _query("SELECT * FROM posicion WHERE id_estante='$id_estante' ORDER BY posicion ASC");
  if(_num_rows($sql)>0)
  {
    $opt = "<option value='0'>Seleccione</option>";
    while ($row = _fetch_array($sql)) {
      $opt .="<option value='".$row["id_posicion"]."'>".$row["posicion"]."</option>";
    }
    $xdatos["typeinfo"] = "Success";
    $xdatos["opt"] = $opt;
  }
  else
  {
    $opt = "<option value='0'>Seleccione</option>";
    $xdatos["opt"] = $opt;
    $xdatos["typeinfo"] = "Success";
  }
  echo json_encode($xdatos);
}

if (!isset($_REQUEST['process']))
{
  initial();
}
if (isset($_REQUEST['process']))
{
  switch ($_REQUEST['process'])
  {
    case 'insert':
    insertar();
    break;
    case 'consultar_stock':
    consultar_stock();
    break;
    case 'getpresentacion':
    getpresentacion();
    break;
    case'traerpaginador':
    traerpaginador();
    break;
    case 'val':
    estantes();
    break;
    case 'val2':
    posicion();
    break;
  }
}
?>
