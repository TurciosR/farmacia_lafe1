<?php
include_once "_core.php";

function initial()
{
  $title = "Compra de producto";
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

  $iva=0;
  $sql_iva="select iva,monto_retencion1,monto_retencion10,monto_percepcion FROM sucursal WHERE  id_sucursal=$_SESSION[id_sucursal]";
  $result_IVA=_query($sql_iva);
  $row_IVA=_fetch_array($result_IVA);
  $iva=$row_IVA['iva']/100;
  $monto_percepcion=$row_IVA['monto_percepcion'];

  ?>
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
                <div hidden class="col-lg-4">
                  <div class="form-group has-info">
                    <label>Concepto</label>
                    <input type='text' class='form-control' value='COMPRA DE PRODUCTO' id='concepto' name='concepto'>
                  </div>
                </div>

                <div class='col-lg-3'>
                  <div class='form-group has-info'>
                    <label>Proveedor</label>

                    <select class="form-control select " id="id_proveedor" name="id_proveedor">
                      <option value="">Seleccione</option>
                      <?php
                      $sql_proveedor=_query("SELECT proveedor.id_proveedor, proveedor.nombre FROM proveedor ORDER BY nombre");
                      while ($row=_fetch_array($sql_proveedor)) {
                        # code...
                        ?>
                        <option value="<?php echo $row['id_proveedor'] ?>"><?php echo $row['nombre'] ?></option>
                        <?php
                      }
                       ?>
                    </select>
                  </div>
                </div>
                <div class='col-lg-3'>
                  <div class='form-group has-info'>
                    <label>Documento</label>

                    <select class="form-control select " id="tipo_doc" name="tipo_doc">
                      <option value="CCF">COMPROBANTE DE CREDITO FISCAL</option>
                      <option value="COF">FACTURA DE CONSUMIDOR FINAL</option>
                    </select>
                  </div>
                </div>
                <div class='col-lg-3'>
                  <div class='form-group has-info'>
                    <label>Numero de Documento</label>
                    <input type="text" class="form-control" id="numero_doc" name="numero_doc" value="">
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
                <div class="col-lg-3">
                  <div class="form-group has-info">
                    <label>Destino</label>
                    <select class="form-control select" id="destino" name="destino">
                      <?php
                      $sql = _query("SELECT * FROM ubicacion WHERE id_sucursal='$id_sucursal' ORDER BY descripcion ASC");
                      while($row = _fetch_array($sql))
                      {
                        echo "<option value='".$row["id_ubicacion"]."'>".$row["descripcion"]."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-lg-3">
                  <label>Dias Credito</label>
                  <input type="text" class="form-control" id="numero_dias" name="numero_dias" value="">
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <label>Aplicar descuento sobre</label>
                  <select class="form-control select" id="descuento_select" name="descuento_select">
                    <option value="g">Total Gravado</option>
                    <option value="f">Total Facturado</option>
                  </select>

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

                      <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
                      <input type='hidden' name='monto_percepcion' id='monto_percepcion' value='<?php echo $monto_percepcion; ?>'>

                      <input type="hidden" id="percepcion" name="percepcion" value="0">

                      <table class="table table-striped table-bordered table-condensed" id="inventable">
                        <thead>
                          <tr>
                            <th style="display:none;">Id</th>
                            <th class="col-lg-4">Nombre</th>
                            <th class="col-lg-1">Presentación</th>
                            <th style="display:none;" class="col-lg-1">Descripción</th>
                            <th class="">Prec. C</th>
                            <th class="">Prec. V</th>
                            <th class="col-lg-1">Cantidad</th>
                            <th class="col-lg-1">Vence</th>
                            <th class="col-lg-2">Estante</th>
                            <th class="col-lg-1">Posición</th>
                            <th class="">Acci&oacute;n</th>
                          </tr>
                        </thead>

                        <tbody>
                        </tbody>
                      </table>
                      <table class="table table-striped table-bordered table-condensed">
                        <tr>
                          <td class="col-lg-1">Cantidad</td>
                          <td class="col-lg-2">Sumas</td>
                          <td class="col-lg-1">IVA</td>
                          <td class="col-lg-2">Subtotal</td>
                          <td class="col-lg-1">Venta Excenta</td>
                          <td class="col-lg-1">Flete</td>
                          <td class="col-lg-1">Descuento</td>
                          <td class="col-lg-1">Percepción</td>
                          <td class="col-lg-2">Total</td>
                        </tr>
                        <tr>
                          <td><strong></strong> <label id='totcant'>0.00</label> </td>
                          <td><strong>$</strong> <label id='sumas_sin_iva'>0.00</label> </td>
                          <td><strong>$</strong> <label id='iva'>0.00</label> </td>
                          <td><strong>$</strong> <label id='subtotal'>0.00</label> </td>
                          <td><strong>$</strong> <label id='venta_exenta'>0.00</label> </td>
                          <td><strong></strong> <label> <input class="form-control" id="flete" type="text" name="" value="0.00"> </label> </td>
                          <td><strong></strong> <label> <input class="form-control" id="descuento" type="text" name="" value="0.00"> </label> </td>

                          <td><strong>$</strong> <label id='total_percepcion'>0.00</label></td>
                          <td><strong>$</strong> <label id='total_dinero'>0.00</label> </td>
                        </tr>
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
  echo "<script src='js/funciones/funciones_compras.js'></script>";
} //permiso del script
else {

  echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
  include_once ("footer.php");
}
}

function insertar()
{
  $cuantos = $_POST['cuantos'];
  $datos = $_POST['datos'];
  $destino = $_POST['destino'];
  $fecha = $_POST['fecha'];
  $total_compras = $_POST['total'];
  $concepto=$_POST['concepto'];
  $hora=date("H:i:s");
  $fecha_movimiento = date("Y-m-d");
  $id_empleado=$_SESSION["id_usuario"];


  $id_proveedor=$_POST["proveedor"];
  $alias_tipodoc=$_POST['tipo_doc'];
  $numero_documen=$_POST['numero_doc'];

  $sumas_sin_iva=$_POST['sumas_sin_iva'];
  $subtotal=$_POST['subtotal'];
  $iva=$_POST['iva'];
  $venta_exenta=$_POST['venta_exenta'];
  $total_percepcion=$_POST['total_percepcion'];
  $dias_credito=$_POST['dias_credito'];

  $flete=$_POST['flete'];
  $descuento=$_POST['descuento'];
  $tipo_descuento=$_POST['tipo_descuento'];

  $id_sucursal = $_SESSION["id_sucursal"];
  $sql_num = _query("SELECT ii FROM correlativo WHERE id_sucursal='$id_sucursal'");
  $datos_num = _fetch_array($sql_num);
  $ult = $datos_num["ii"]+1;
  $numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_II';

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
    $concepto='COMPRA DE PRODUCTO';
  }
  $a = 1 ;

  /*insertar la compra*/
  $table_fc= 'compra';
  $form_data_fc = array(
      'id_proveedor' => $id_proveedor,
      'alias_tipodoc'=>$alias_tipodoc,
      'fecha' => $fecha,
      'fecha_ingreso' => $fecha_movimiento,
      'numero_doc' => $numero_documen,
      'total' => $total_compras,
      'total_percepcion'=>$total_percepcion,
      'id_empleado' => $id_empleado,
      'id_sucursal' => $id_sucursal,
      'iva' => $iva,
      'hora' => $hora,
      'dias_credito' => $dias_credito,
      'finalizada' =>1,
      'flete' => $flete,
      'descuento' => $descuento,
      'tipo_descuento' => $tipo_descuento,
      );
  //falta en compras vencimiento a 30, 60, 90 dias y vence iva
  $insertar_fc = _insert($table_fc, $form_data_fc);
  if ($insertar_fc) {
    # code...
  }
  else {
    # code...
    $a=0;

  }
  $id_fact= _insert_id();


  /*cuentas por pagar*/
  if ($dias_credito!=0) {
    # code...
    $table_cxp= 'cuenta_pagar';
    $fecha_vencimiento=sumar_dias_Ymd($fecha, $dias_credito);
    $form_data_cxp = array(
        'id_proveedor' => $id_proveedor,
        'alias_tipodoc'=>$alias_tipodoc,
        'fecha' => $fecha_movimiento,
        'fecha_vence' => $fecha_vencimiento,
        'numero_doc' => $numero_documen,
        'monto' => $total_compras,
        'saldo_pend'=> $total_compras,
        'id_empleado' => $id_empleado,
        'id_sucursal' => $id_sucursal,
        'hora' => $hora,
        'dias_credito' => $dias_credito,
        'id_compra' => $id_fact,
        );
    $insertar_cxp = _insert($table_cxp, $form_data_cxp);
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
    'id_proveedor' => $id_proveedor,
    'id_compra' => $id_fact,
  );
  $insert_mov =_insert($table,$form_data);
  $id_movimiento=_insert_id();
  $lista=explode('#',$datos);
  $j = 1 ;
  $k = 1 ;
  $l = 1 ;
  $m = 1 ;

  $b = 1 ;
  for ($i=0;$i<$cuantos ;$i++)
  {
    list($id_producto,$precio_compra,$precio_venta,$cantidad,$unidades,$fecha_caduca,$id_presentacion,$exento,$estante,$posicion)=explode('|',$lista[$i]);
    $sql_su="SELECT id_su, cantidad FROM stock_ubicacion WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal' AND id_ubicacion='$destino' AND id_estante=$estante AND id_posicion=$posicion";
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

    //detalle de compras
    $table_dc= 'detalle_compra';
    $form_data_dc = array(
        'id_compra' => $id_fact,
        'id_producto' => $id_producto,
        'cantidad' => $cantidad,
        'ultcosto' => $precio_compra,
        'subtotal' => round(($cantidad/$unidades)*$precio_compra,2),
        'exento' => $exento,
        'id_presentacion' => $id_presentacion,
    );
    $insertar_dc = _insert($table_dc, $form_data_dc);
    if ($insertar_dc) {
      # code...

    }
    else {
      # code...
      $b=0;
    }


    $precio_con_iva=0;
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
    $where_clause_p_pre1="WHERE id_producto='$id_producto' and id_presentacion='$id_presentacion' AND id_sucursal='$id_sucursal' AND desde=0 limit 2";
    $where_clause_p_pre2="WHERE id_producto='$id_producto' and id_presentacion='$id_presentacion' AND id_sucursal='$id_sucursal' AND desde=1 limit 2";
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
  if($insert_mov &&$insertar_fc && $insertar_dc &&$corr &&$z && $j && $k && $l && $m)
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
  $exento=0;

  $sql_p=_query("SELECT presentacion.nombre, prp.descripcion,prp.id_presentacion,prp.unidad,prp.costo,prp.precio FROM presentacion_producto AS prp JOIN presentacion ON presentacion.id_presentacion=prp.presentacion WHERE prp.id_producto=$id_producto AND prp.activo=1 AND prp.id_sucursal=$id_sucursal");
  $select="<select style='width:100%;' class='sel'>";
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
  $xdatos['i']= $i;


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
  $xdatos['perecedero'] = $perecedero;
  $xdatos['exento']= $exento;

  echo json_encode($xdatos);
}
function getpresentacion()
{
  $id_presentacion =$_REQUEST['id_presentacion'];
  $sql=_fetch_array(_query("SELECT * FROM presentacion_producto WHERE id_presentacion=$id_presentacion"));
  $precio=$sql['precio'];
  $unidad=$sql['unidad'];
  $descripcion=$sql['descripcion'];

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
  $costo=$sql['costo'];
  $xdatos['precio']=$precio;
  $xdatos['costo']=$costo;
  $xdatos['unidad']=$unidad;
  $xdatos['descripcion']=$descripcion;
  echo json_encode($xdatos);
}

function datos_proveedores()
{
    $id_proveedor = $_POST['id_proveedor'];
    $sql0="SELECT percibe  FROM proveedor  WHERE id_proveedor='$id_proveedor'";
    $result = _query($sql0);
    $numrows= _num_rows($result);
    $row = _fetch_array($result);

    $percibe=$row['percibe'];
    if ($percibe==1) {
        $percepcion=round(1/100, 2);
    } else {
        $percepcion=0;
    }


    $xdatos['percepcion'] = $percepcion;
    echo json_encode($xdatos); //Return the JSON Array
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
    case 'datos_proveedores':
    datos_proveedores();
    break;
  }
}
?>
