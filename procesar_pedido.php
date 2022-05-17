  <?php
  include_once "_core.php";

  function initial()
  {
    $title = "Procesar pedido";
    $_PAGE = array();
    $_PAGE ['title'] = $title;
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

    include_once "header.php";
    include_once "main_menu.php";
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $id_sucursal=$_SESSION["id_sucursal"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);
    $fecha_actual=date("Y-m-d");
    //para llenar fieds
    $id_pedido=$_REQUEST["id_pedido"];
    $sql_pedido=_fetch_array(_query("SELECT c.id_cliente, c.nombre, p.lugar_entrega, p.fecha, p.fecha_entrega, p.total, p.reservado
      FROM pedido as p, cliente as c
      WHERE p.id_pedido='$id_pedido'
      AND p.id_cliente=c.id_cliente
      AND p.id_sucursal='$id_sucursal'"));

    $sql_ref=_fetch_array(_query("SELECT SUM(factura.numero_ref) as numero FROM factura WHERE factura.fecha='$fecha_actual' AND factura.id_sucursal=$id_sucursal"));
    $numero=$sql_ref['numero'];

    if ($numero==0) {
      # code...
      $table_numdoc="correlativo";
      $data_numdoc = array(
        'ref' => 0,
      );
      $where_clause_n="WHERE  id_sucursal='$id_sucursal'";

      $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);
    }
    ?>

    <div class="row wrapper border-bottom white-bg page-heading">
      <div class="col-lg-2"></div>
    </div>
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
                      <label>Cliente</label>
                      <input type='text' class='form-control' readonly id='cliente_buscar' name='cliente_buscar' value="<?php echo $sql_pedido['id_cliente']."|".$sql_pedido['nombre'];?>">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group has-info">
                      <label>Lugar de entrega</label>
                      <input type="text" class='form-control' readonly name="lugar_entrega" id="lugar_entrega" value="<?php echo $sql_pedido["lugar_entrega"];?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class='col-lg-6'>
                    <div class='form-group has-info'>
                      <label>Fecha</label>
                      <input type='text' class='datepick form-control'  readonly value="<?php echo $sql_pedido["fecha"];?>" id='fecha' name='fecha'>
                    </div>
                  </div>
                  <div class='col-lg-6'>
                    <div class='form-group has-info'>
                      <label>Fecha entrega</label>
                      <input type='text' class='datepick form-control' readonly value="<?php echo $sql_pedido["fecha_entrega"];?>" id='fecha2' name='fecha2'>
                    </div>
                  </div>
                </div>
                <div class="row" id='buscador'>
                  <div class="col-lg-6">
                    <div class='form-group has-info'><label>Buscar Productos</label>
                      <input type="text" id="producto_buscar_proceso"  name="producto_buscar_proceso" size="20" class="producto_buscar form-control" placeholder="Ingrese nombre de producto"  data-provide="typeahead">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group has-info single-line">
                        <div class='checkbox i-checks'>
                          <label id='frentex'>
                            <?php if($sql_pedido['reservado']==1){ ?>
                              <input type='checkbox' id='checkreser' name='checkreser' checked value="0"><strong> Reservado</strong>
                            <?php }else{?>
                              <input type='checkbox' id='checkreser' name='checkreser'  value="0"><strong> Reservado</strong>
                            <?php }?>
                          </label>
                        </div>
                        <input type='hidden' id='reservado' name='reservado' value="<?php echo $sql_pedido['reservado'];?>">
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
                        <table class="table table-striped table-bordered table-condensed">
                          <thead>
                            <tr>
                              <th class="col-lg-1">Id</th>
                              <th class="col-lg-5">Nombre</th>
                              <th class="col-lg-1">Presentación</th>
                              <th class="col-lg-1">Descripción</th>
                              <th class="col-lg-2">Prec. V</th>
                              <th class="col-lg-1">Stock</th>
                              <th class="col-lg-1">Cantidad. P</th>
                              <th class="col-lg-1">Cantidad. E</th>
                              <th class="col-lg-1">Subtotal</th>
                            </tr>
                          </thead>

                          <tbody id="pedidotable">
                          <?php

                            $sql_p=_query("SELECT producto.id_producto,producto.id_categoria, producto.descripcion AS producto, presentacion.nombre,presentacion_producto.id_presentacion ,presentacion_producto.descripcion, presentacion_producto.unidad ,pedido_detalle.id_pedido_detalle,pedido_detalle.precio_venta, pedido_detalle.cantidad, pedido_detalle.subtotal, stock.stock
                              FROM pedido_detalle
                              JOIN producto ON (pedido_detalle.id_producto=producto.id_producto)
                              JOIN presentacion_producto ON (pedido_detalle.id_presentacion=presentacion_producto.id_presentacion)
                              JOIN presentacion ON (presentacion_producto.presentacion=presentacion.id_presentacion)
                              JOIN stock ON (pedido_detalle.id_producto=stock.id_producto)
                              WHERE pedido_detalle.id_pedido='$id_pedido'");
                            $cantidad=0;
                             while ( $filas=_fetch_array($sql_p))
                             {
                               $id_producto=$filas['id_producto'];
                               $id_sucursal=$_SESSION['id_sucursal'];

                               $stock=0;

                               $sql1 = "SELECT p.id_producto, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal,SUM(su.cantidad) as stock FROM producto AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto ='$id_producto' AND u.bodega=0 AND su.id_sucursal=$id_sucursal";
                               $stock1=_query($sql1);
                               $row1=_fetch_array($stock1);
                               $nrow1=_num_rows($stock1);
                               if ($nrow1>0)
                               {
                                 $hoy=date("Y-m-d");

                                 $sql_res_pre=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reserva FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_sucursal=$id_sucursal AND factura.fecha = '$hoy' AND factura.finalizada=0 "));
                                 $reserva=$sql_res_pre['reserva'];
                                 $reserva=round($reserva,0);
                                 $stock= $row1["stock"]-$reserva;
                               }

                              $id_presentacion=$filas['id_presentacion'];
                              echo "<tr id_pedido_detalle='".$filas['id_pedido_detalle']."'>";
                              echo "<td class='id_p'>".$filas['id_producto']."</td>";
                              echo "<td>".$filas['producto']."</td>";
                              echo "<td>";
                              $sql_prese=_query("SELECT presentacion.nombre as presentacion, prp.id_presentacion, prp.unidad
                                FROM presentacion_producto AS prp
                                JOIN presentacion ON presentacion.id_presentacion=prp.presentacion, producto
                                WHERE prp.id_producto='$id_producto' AND prp.activo=1 AND producto.id_producto='$id_producto' AND prp.id_sucursal='$id_sucursal'");
                              echo "<select class='sel'>";
                              while ($row=_fetch_array($sql_prese))
                              {
                                echo "<option value='".$row["id_presentacion"]."'";
                                if($id_presentacion==$row["id_presentacion"] ){ echo " selected "; }
                              echo ">".$row["presentacion"]."(".$row["unidad"].")</option>";
                              }
                              echo "</select>";
                              "</td>";

                              $select_rank="<select class='sel_r precio_r form-control'>";
                              $select_rank.="<option value='$filas[precio_venta]'";
                              $select_rank.="selected";
                              $select_rank.=">$filas[precio_venta]</option>";
                              $select_rank.="</select>";

                              echo "<td class='descp'>".$filas['descripcion']."</td>";
                              echo "<td class='rank_s'>$select_rank</td>";
                              echo "<td><div class='col-xs-1'><input type='hidden' class='unidad' value='".$filas['unidad']."'><input type='text' class='existencia' style='width:80px;' readonly value='".$stock."'></div></td>";
                              echo "<td><div class='col-xs-1'><input type='text' value='".round($filas['cantidad'],4)."' readonly class='form-control' style='width:60px;'></div></td>";
                              echo "<td><div class='col-xs-1'><input type='text' value='0'  class='form-control cant $filas[id_categoria]' style='width:60px;'></div></td>";
                              echo "<td class='col-xs-2'><input type='text'readonly class='form-control vence subt' readonly  value='".$filas['subtotal']."'></td>";
                              echo "</tr>";
                              $cantidad+=$filas['cantidad'];
                              }
                          ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="7">Total<strong></strong></td>
                              <td id='totcant'><?php echo $cantidad;?></td>
                              <td id='total_dinero'><?php echo $sql_pedido['total'];?></td>
                            </tr>
                          </tfoot>
                        </table>
                        <input type="hidden" name="autosave" id="autosave" value="false-0">
                      </section>
                      <input type="hidden" id="id_pedido" name="id_pedido"  value="<?php echo $id_pedido;?>" />
                        <input type="submit" id="submit2" name="submit2" value="Procesar" class="btn btn-primary m-t-n-xs" />
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div><!--div class='ibox-content'-->
          </div>
        </div>
      </div>


    <?php
    include_once ("footer.php");
    echo "<script src='js/funciones/funciones_pedido.js'></script>";
  } //permiso del script
  else {
    echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
  }
  }

  function procesar()
  {
    $cuantos = $_POST['cuantos'];
    $datos = $_POST['datos'];
    $total_compras = $_POST['total'];
    $id_cliente=$_POST['id_cliente'];
    $id_pedido=$_POST['id_pedido'];
    $id_empleado=$_SESSION["id_usuario"];
    $id_sucursal = $_SESSION["id_sucursal"];
    $reservado = $_POST["reservado"];


    _begin();
    $z=1;
    //Validamos si es resarvado para descontar del stock
    $reserva=false;
    if($reservado==1)
    {
      $reserva=true;
    }


    $a=1;
    $sql="SELECT ref FROM correlativo WHERE id_sucursal='$id_sucursal'";
    $result= _query($sql);
    $rows=_fetch_array($result);
    $ult=$rows['ref']+1;
    $numero_doc = str_pad($ult,7,"0",STR_PAD_LEFT)."_REF";
    $table_numdoc="correlativo";
    $data_numdoc = array(
      'ref' => $ult,
    );
    $where_clause_n="WHERE  id_sucursal='$id_sucursal'";
    $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);

    if (!$insertar_numdoc) {
  		# code...
  		$a=0;
  	}


    $abono=0;
  	$saldo=0;
  	$tipo_documento="COF";
  	$tipo_entrada_salida='NUM. REFERENCIA INTERNA';
    $b=1;

    $fecha=date("Y-m-d");
    $hora=date("H:i:s");
  	$table_fact= 'factura';
  	$form_data_fact = array(
  		'id_cliente' => $id_cliente,
  		'fecha' => $fecha,
  		'numero_doc' => $numero_doc,
      'referencia' => $numero_doc,
      'numero_ref' => $ult,
  		'subtotal' => $total_compras,
  		'sumas'=>$total_compras,
      'suma_gravado'=>$total_compras,
  		'iva' =>0,
  		'retencion'=>0,
      'venta_exenta'=>0,
      'total_menos_retencion'=>$total_compras,
  		'total' => $total_compras,
  		'id_usuario'=>$id_empleado,
  		'id_empleado' => 0,
  		'id_sucursal' => $id_sucursal,
  		'tipo' => $tipo_entrada_salida,
  		'hora' => $hora,
  		'finalizada' => '0',
  		'abono'=>$abono,
  		'saldo' => $saldo,
  		'tipo_documento' => $tipo_documento,
  	);
  	$insertar_fact = _insert($table_fact,$form_data_fact );
  	$id_fact= _insert_id();

  	if (!$insertar_fact) {
  		# code...
  		$b=0;
  	}

    $table='pedido';
    $form_data = array(
      'total' => $total_compras,
      'estado' => 'FINALIZADO',
      'reservado' => 0,
    );
    $where_clause_c="id_pedido='".$id_pedido."' AND id_sucursal='".$id_sucursal."'";
    $up_pedido=_update($table,$form_data,$where_clause_c);
    $lista=explode('#',$datos);
    $m = 1 ;
    if(!$up_pedido){
      $m=0;
    }


    for ($i=0;$i<$cuantos ;$i++)
    {
      list($id_producto,$precio_compra,$precio_venta,$cantidad,$sutto,$id_presentacion,$id_pedido_detalle,$unidades)=explode('|',$lista[$i]);
        $tablee='pedido_detalle';
        $form_data_detalle = array(
          'cantidad_enviar' => $cantidad,
          'subtotal'=>$sutto,
        );
        if($id_pedido_detalle>0)
        {
          $where_clause_d="id_pedido_detalle='".$id_pedido_detalle."'";
          $up_pedido_detalle=_update($tablee,$form_data_detalle,$where_clause_d);
        }else
        {
        $table_i='pedido_detalle';
        $form_data_detalle_i = array(
          'id_pedido' => $id_pedido,
          'id_producto' => $id_producto,
          'id_presentacion' => $id_presentacion,
          'cantidad_enviar' => $cantidad,
          'precio_venta' => $precio_venta,
          'subtotal'=>$sutto,
        );
        $insert_pedido_detalle = _insert($table_i,$form_data_detalle_i);
        }

        $subtotal=$sutto;
        $cantidad_real=$cantidad*$unidades;


        $table_fact_det= 'factura_detalle';
        $data_fact_det = array(
          'id_factura' => $id_fact,
          'id_prod_serv' => $id_producto,
          'cantidad' => $cantidad_real,
          'precio_venta' => $precio_venta,
          'subtotal' => $subtotal,
          'tipo_prod_serv' => "PRODUCTO",
          'id_empleado' => $id_empleado,
          'id_sucursal' => $id_sucursal,
          'fecha' => $fecha,
          'id_presentacion'=> $id_presentacion,
          'exento' => 0,
        );
        $insertar_fact_det = _insert($table_fact_det,$data_fact_det );
        if (!$insertar_fact_det) {
          # code...
          $c=0;
        }

        /*if($reserva==true)
        {
            $get_stock=_query("SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'");
            $get_value=_fetch_array($get_stock);
            $value_stock=$get_value['stock'];
            $stock_opera=$value_stock-$cantidad;
            $table_stock='stock';
            $form_data_stock = array(
              'stock' => $stock_opera,
            );
            $where_clause_stock="id_producto='".$id_producto."' AND id_sucursal='".$id_sucursal."'";
            $updating_stock=_update($table_stock,$form_data_stock,$where_clause_stock);
        }*/
      }
    if($m)
    {
      _commit();
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Referenca Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
      $xdatos['referencia']=$ult;

    }
    else
    {
      _rollback();
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Proceso no pudo ser ingresado!'._error();
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

    $stock=0;

    $sql1 = "SELECT p.id_producto, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal,SUM(su.cantidad) as stock FROM producto AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto ='$id_producto' AND u.bodega=0 AND su.id_sucursal=$id_sucursal";
    $stock1=_query($sql1);
    $row1=_fetch_array($stock1);
    $nrow1=_num_rows($stock1);
    if ($nrow1>0)
    {
      $hoy=date("Y-m-d");

      $sql_res_pre=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reserva FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_sucursal=$id_sucursal AND factura.fecha = '$hoy' AND factura.finalizada=0 "));
      $reserva=$sql_res_pre['reserva'];
      $reserva=round($reserva,0);
      $stock= $row1["stock"]-$reserva;
    }

    $sql_p=_query("SELECT presentacion.nombre, prp.descripcion,prp.id_presentacion,prp.unidad,prp.costo,prp.precio
      FROM presentacion_producto AS prp
      JOIN presentacion ON presentacion.id_presentacion=prp.presentacion
      WHERE prp.id_producto=$id_producto AND prp.activo=1 AND prp.id_sucursal=$_SESSION[id_sucursal]");
    $select="<select class='sel'>";
    $select_rank="<select class='sel_r precio_r form-control'>";
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
            $select_rank.="<option value='$rowr[precio]'";
            if($xc==0)
            {
              $select_rank.="selected";
              $preciop=$rowr['precio'];
            }
            $select_rank.=">$rowr[precio]</option>";
          }
          if (_num_rows($sql_rank)==0) {
            # code...
            $select_rank.="<option value='$preciop'";
            $select_rank.="selected";
            $select_rank.=">$preciop</option>";
          }
          $select_rank.="</select>";
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
    $xdatos['stock']=$stock;

    $sql_perece="SELECT * FROM producto WHERE id_producto='$id_producto'";
    $result_perece=_query($sql_perece);
    $row_perece=_fetch_array($result_perece);
    $nombrep=$row_perece['descripcion'];
    $xdatos['nombre'] = $nombrep;
    $xdatos['categoria'] = $row_perece['id_categoria'];
    $xdatos['select_rank'] = $select_rank;
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
    $xdatos['precio']=$precio;
    $xdatos['costo']=$costo;
    $xdatos['unidad']=$unidad;
    $xdatos['descripcion']=$descripcion;
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
      case 'procesar_pedido':
      procesar();
      break;
      case 'consultar_stock':
      consultar_stock();
      break;
      case 'getpresentacion':
      getpresentacion();
      break;
    }
  }
  ?>
