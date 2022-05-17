<?php
include_once "_core.php";

function initial()
{
    $title = "Descargo de Productos de Inventario";
    $_PAGE = array();
    $_PAGE ['title'] = $title;
    $_PAGE ['links'] = null;
    $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/typeahead.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

    $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/main_co.css">';
    $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/util_co.css">';

    include_once "header.php";

    $sql="SELECT * FROM producto";

    $result=_query($sql);
    $count=_num_rows($result);
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];

    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);
    $fecha_actual=date("Y-m-d"); ?>

<style media="screen">
  #inventable input
  {
    font-size: 12px;
  }
</style>
<div class="gray-bg">
  <div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox">
          <div class="ibox-title">
            <h5><?php echo $title; ?></h5>
          </div>
          <?php if ($links!='NOT' || $admin=='1') {
        ?>
          <div class="ibox-content">
            <div class='row focuss' id='form_invent_inicial'>
              <div class="col-lg-3">
                <div class="form-group has-info">
                  <label>Concepto</label>
                  <input type='text' class='form-control' value='DEVOLUCION A PROVEEDOR' id='concepto' name='concepto'>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group has-info">
                  <label>Tipo</label>
                  <select class="form-control select" id="tipo" name="tipo">
                    <option value="VENCIMIENTO">VENCIMIENTO</option>
                    <option value="DETERIORO">DETERIORO</option>
                    <option value="ERROR EN LA ENTREGA DEL PRODUCTO">ERROR EN LA ENTREGA DEL PRODUCTO</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-3">
                <div class='form-group has-info'><label>Origen</label>
                  <select name='origen' id="origen" class="form-control select">
                    <?php
                    $id_sucursal=$_SESSION['id_sucursal'];
                    $sql = _query("SELECT * FROM ubicacion WHERE id_sucursal='$id_sucursal' ORDER BY descripcion ASC");
                    while ($row = _fetch_array($sql)) {
                        echo "<option value='".$row["id_ubicacion"]."'>".$row["descripcion"]."</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class='col-lg-3'>
                <div class='form-group has-info'>
                  <label>Fecha Documento</label>
                  <input type='text' class='datepick form-control' value='<?php echo $fecha_actual; ?>' id='fecha1' name='fecha1'>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3">
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
              <div class="col-lg-3">
                <label>Numero Doc.</label>
                <input type="text" class="form-control" id="numero_doc" name="numero_doc" value="">
              </div>
              <div class="col-lg-6">
                <label>Documento Proporcionado Por el Proveedor.</label>
                <input type="text" class="form-control" id="doc_tipe" name="doc_tipe" value="NOTA DE DEVOLUCION">
              </div>
            </div>
            <div class="row" id='buscador'>
              <div class="col-lg-4">
                <div hidden id="a">
                  <label>Buscar Producto (Código)</label>
                  <input type="text" id="codigo" name="codigo" style="width:100% !important" class="form-control usage" placeholder="Ingrese Código de producto" style="border-radius:0px">
                </div>
                <div  id="b">
                  <label id='buscar_habilitado'>Buscar Producto (Descripción)</label>
                  <div id="scrollable-dropdown-menu">
                    <input type="text" id="producto_buscar" name="producto_buscar" style="width:100% !important" class=" form-control usage typeahead" placeholder="Ingrese la Descripción de producto" data-provide="typeahead"
                      style="border-radius:0px">
                  </div>
                </div><br>
              </div>
              <div class="col-lg-5">
              </div>
              <div class="col-lg-3">
                <input type="hidden" name="process" id="process" value="insert">
                <br>
                <a class="btn btn-danger pull-right" style="margin-left:2%;" href="dashboard.php" id='salir'><i class="fa fa-mail-reply"></i> F4 Salir</a>
                <button type="button" id="submit1" class="btn btn-primary  pull-right"><i class="fa fa-save"></i> F2 Guardar</button>
                <input type='hidden' name='urlprocess' id='urlprocess' value="<?php echo $filename ?> ">
                <input type="hidden" name="filas" id="filas" value="0">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <header>
                  <h4 class="text-navy">Lista de Productos</h4>
                </header>
                <div class='widget-content' id="content">
                  <div class="wrap-table1001">
                    <div class="table100 ver1 m-b-10">
                      <div class="table100-head">
                        <table class="table table-striped" id='inventable1'>
                          <thead class=''>
                            <tr class='row100 head'>
                              <th class="col-lg-1" style="display:none;">Id</th>
                              <th class="col-lg-3">Descripción</th>
                              <th class="col-lg-1">Presentación</th>
                              <th class="col-lg-1">Detalle</th>
                              <th class="col-lg-1">Cantidad</th>
                              <th class="col-lg-1">Lote</th>
                              <th class="col-lg-1">Vencimiento</th>
                              <th class="col-lg-1">Costo</th>
                              <th class="col-lg-1" style='display:none;'>Precio</th>
                              <th class="col-lg-1">Exis Unid.</th>
                              <th class="col-lg-1">Accion</th>
                            </tr>
                          </thead>
                        </table>
                      </div>
                      <div class="table100-body js-pscroll">
                        <table>
                          <tbody id="inventable">
                          </tbody>
                        </table>
                      </div>

                      <div class="table101-body">
                        <table>
                          <thead>
                          <tbody>
                            <tr>
                              <td class="cell100 column100 ">&nbsp;</td>
                            </tr>
                            <tr>
                              <td class='cell100 column50 text-bluegrey tr_bb' id='totaltexto'>&nbsp;</td>
                              <td class="cell100 column15 leftt text-bluegrey">CANT. PROD: </td>
                              <td class="cell100 column10 text-right text-green" id='totcant'>0</td>
                              <td class='cell100 column15 leftt  text-bluegrey  tr_bb'>TOTAL</td>
                              <td class='cell100 column10 text-right text-danger  tr_bb' id='total_dinero'>0.00</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--div class='ibox-content'-->
      </div>
    </div>
    <?php
  include_once("footera.php");
        echo "<script src='js/funciones/funciones_descargo_inventario_devolucion.js'></script>";
    } //permiso del script
    else {
        echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
        include_once("footer.php");
    }
}
function insertar()
{
    $cuantos = $_POST['cuantos'];
    $datos = $_POST['datos'];
    $origen = $_POST['origen'];
    $fecha = $_POST['fecha'];
    $total_compras = $_POST['total'];
    $concepto=$_POST['concepto'];
    $hora=date("H:i:s");
    $fecha_movimiento = date("Y-m-d");
    $id_empleado=$_SESSION["id_usuario"];

    $id_proveedor =$_REQUEST['id_proveedor'];
    $numero_nd = $_REQUEST['numero_doc'];
    $tipo_doc= $_REQUEST['tipo_doc'];

    $id=$_POST['iden'];

    $id_sucursal = $_SESSION["id_sucursal"];
    $sql_num = _query("SELECT di FROM correlativo WHERE id_sucursal='$id_sucursal'");
    $datos_num = _fetch_array($sql_num);
    $ult = $datos_num["di"]+1;
    $numero_doc=str_pad($ult, 7, "0", STR_PAD_LEFT).'_DI';
    $tipo_entrada_salida='DESCARGO DE INVENTARIO';

    _begin();
    $z=1;
    $up=1;

    /*actualizar los correlativos de DI*/
    $corr=1;
    $table="correlativo";
    $form_data = array(
    'di' =>$ult
    );
    $where_clause_c="id_sucursal='".$id_sucursal."'";
    $up_corr=_update($table, $form_data, $where_clause_c);
    if ($up_corr) {
        # code...
    } else {
        $corr=0;
    }
    if ($concepto=='') {
        $concepto='DESCARGO DE INVENTARIO';
    }

    $concepto=$concepto."|".$id;
    $table='movimiento_producto';
    $form_data = array(
    'id_sucursal' => $id_sucursal,
    'correlativo' => $numero_doc,
    'concepto' => $concepto,
    'total' => $total_compras,
    'tipo' => 'SALIDA',
    'proceso' => 'DI',
    'referencia' => $numero_doc,
    'id_empleado' => $id_empleado,
    'fecha' => $fecha_movimiento,
    'hora' => $hora,
    'id_suc_origen' => $id_sucursal,
    'id_suc_destino' => $id_sucursal,
    'id_proveedor' => 0,
  );
    $insert_mov =_insert($table, $form_data);
    $id_movimiento=_insert_id();

    $form_data_dev = array(
      'id_sucursal' => $id_sucursal,
      'id_proveedor' => $id_proveedor,
      'numero_doc' => $numero_nd,
      'fecha' => $fecha,
      'total' => $total_compras,
      'hora' => date("H:i:s"),
      'fecha_ingreso' => $fecha_movimiento,
      'id_empleado' => $id_empleado,
      'alias_tipodoc' => $tipo_doc,
      'id_movimiento' => $id_movimiento,
    );
    _insert("devoluciones_vencimiento",$form_data_dev);
    $id_dev=_insert_id();

    $lista=explode('#', $datos);
    $j = 1 ;
    $k = 1 ;
    $l = 1 ;
    $m = 1 ;

    for ($i=0;$i<$cuantos ;$i++) {
        list($id_producto, $precio_compra, $precio_venta, $cantidad, $unidades, $fecha_caduca, $id_presentacion, $lote,$vencimiento)=explode('|', $lista[$i]);

        $id_producto;
        $cantidad=$cantidad*$unidades;
        $a_transferir=$cantidad;

        $sql=_query("SELECT * FROM stock_ubicacion WHERE stock_ubicacion.id_producto=$id_producto AND stock_ubicacion.id_ubicacion=$origen AND stock_ubicacion.cantidad!=0 ORDER BY id_posicion DESC ,id_estante DESC ");

        while ($rowsu=_fetch_array($sql)) {
            # code...

            $id_su1=$rowsu['id_su'];
            $stock_anterior=$rowsu['cantidad'];

            if ($a_transferir!=0) {
                # code...

                $transfiriendo=0;
                $nuevo_stock=$stock_anterior-$a_transferir;
                if ($nuevo_stock<0) {
                    # code...
                    $transfiriendo=$stock_anterior;
                    $a_transferir=$a_transferir-$stock_anterior;
                    $nuevo_stock=0;
                } else {
                    if ($nuevo_stock>0) {
                        # code...
                        $transfiriendo=$a_transferir;
                        $a_transferir=0;
                        $nuevo_stock=$stock_anterior-$transfiriendo;
                    } else {
                        # code...
                        $transfiriendo=$stock_anterior;
                        $a_transferir=0;
                        $nuevo_stock=0;
                    }
                }

                $table="stock_ubicacion";
                $form_data = array(
          'cantidad' => $nuevo_stock,
        );
                $where_clause="id_su='".$id_su1."'";
                $update=_update($table, $form_data, $where_clause);
                if ($update) {
                    # code...
                } else {
                    $up=0;
                }

                $table="movimiento_stock_ubicacion";
                $form_data = array(
          'id_producto' => $id_producto,
          'id_origen' => $id_su1,
          'id_destino'=> 0,
          'cantidad' => $cantidad,
          'fecha' => $fecha,
          'hora' => $hora,
          'anulada' => 0,
          'afecta' => 0,
          'id_sucursal' => $id_sucursal,
          'id_presentacion'=> $id_presentacion,
          'id_mov_prod' => $id_movimiento,
        );

                $insert_mss =_insert($table, $form_data);

                if ($insert_mss) {
                    # code...
                } else {
                    # code...
                    $z=0;
                }
            }
        }

        $sql2="SELECT stock FROM stock WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
        $stock2=_query($sql2);
        $row2=_fetch_array($stock2);
        $nrow2=_num_rows($stock2);
        if ($nrow2>0) {
            $existencias=$row2['stock'];
        } else {
            $existencias=0;
        }



        $table1= 'movimiento_producto_detalle';
        $cant_total=$existencias-$cantidad;
        $form_data1 = array(
      'id_movimiento'=>$id_movimiento,
      'id_producto' => $id_producto,
      'cantidad' => $cantidad,
      'costo' => $precio_compra,
      'precio' => $precio_venta,
      'stock_anterior'=>$existencias,
      'stock_actual'=>$cant_total,
      'lote' => 0,
      'id_presentacion' => $id_presentacion,
      'hora'=>date("H:i:s"),
      'fecha' =>date("Y-m-d"),
    );
        $insert_mov_det = _insert($table1, $form_data1);
        if (!$insert_mov_det) {
            $j = 0;
        }

        $form_data_detalle = array(
          'id_sucursal' => $id_sucursal,
          'id_devolucion' => $id_dev,
          'id_producto' => $id_producto,
          'cantidad' => $cantidad,
          'ultcosto' => $precio_compra,
          'precio_venta' => $precio_venta,
          'lote' => $lote,
          'vencimiento' => $vencimiento,
          'id_presentacion' => $id_presentacion,
        );
        _insert("devoluciones_vencimiento_detalle",$form_data_detalle);



        $table2= 'stock';
        if ($nrow2==0) {
            $cant_total=$cantidad;
            $form_data2 = array(
        'id_producto' => $id_producto,
        'stock' => $cant_total,
        'costo_unitario'=>round(($precio_compra/$unidades), 2),
        'precio_unitario'=>round(($precio_venta/$unidades), 2),
        'create_date'=>$fecha_movimiento,
        'update_date'=>$fecha_movimiento,
        'id_sucursal' => $id_sucursal
      );
            $insert_stock = _insert($table2, $form_data2);
        } else {
            $cant_total=$existencias-$cantidad;
            $form_data2 = array(
        'id_producto' => $id_producto,
        'stock' => $cant_total,
        'costo_unitario'=>round(($precio_compra/$unidades), 2),
        'precio_unitario'=>round(($precio_venta/$unidades), 2),
        'update_date'=>$fecha_movimiento,
        'id_sucursal' => $id_sucursal
      );
            $where_clause="WHERE id_producto='$id_producto' and id_sucursal='$id_sucursal'";
            $insert_stock = _update($table2, $form_data2, $where_clause);
        }
        if (!$insert_stock) {
            $k = 0;
        }

        /*actualizando el stock del local de venta*/
        $num=_query("SELECT ubicacion.id_ubicacion FROM ubicacion WHERE id_sucursal=$id_sucursal AND bodega=0");

        if (_num_rows($num)>0) {
            // code...
            $sql1a=_fetch_array(_query("SELECT ubicacion.id_ubicacion FROM ubicacion WHERE id_sucursal=$id_sucursal AND bodega=0"));
            $id_ubicaciona=$sql1a['id_ubicacion'];
            $sql2a=_fetch_array(_query("SELECT SUM(stock_ubicacion.cantidad) as stock FROM stock_ubicacion WHERE id_producto=$id_producto AND stock_ubicacion.id_ubicacion=$id_ubicaciona"));
            $table='stock';
            $form_data = array(
            'stock_local' => $sql2a['stock'],
          );
            $where_clause="id_producto='".$id_producto."' AND id_sucursal=$id_sucursal";
            $updatea=_update($table, $form_data, $where_clause);
            /*finalizando we*/
        }

        /*arreglando problema con lotes de nuevo*/
        $cantidad_a_descontar=$cantidad;
        $sql=_query("SELECT id_lote, id_producto, fecha_entrada, vencimiento, cantidad
    FROM lote
    WHERE id_producto='$id_producto'
    AND id_sucursal='$id_sucursal'
    AND cantidad>0
    AND estado='VIGENTE'
    ORDER BY vencimiento");

        $contar=_num_rows($sql);

        if ($contar>0) {
            # code...
            while ($row=_fetch_array($sql)) {
                # code...
                $entrada_lote=$row['cantidad'];
                if ($cantidad_a_descontar>0) {
                    # code...
                    if ($entrada_lote==0) {
                        $table='lote';
                        $form_dat_lote=$arrayName = array(
                          'estado' => 'FINALIZADO',
                      );
                        $where = " WHERE id_lote='$row[id_lote]'";
                        $insert=_update($table, $form_dat_lote, $where);
                    } else {
                        if (($entrada_lote-$cantidad_a_descontar)>0) {
                            # code...
                            $table='lote';
                            $form_dat_lote=$arrayName = array(
                              'cantidad'=>($entrada_lote-$cantidad_a_descontar),
                              'estado' => 'VIGENTE',
                          );
                            $cantidad_a_descontar=0;

                            $where = " WHERE id_lote='$row[id_lote]'";
                            $insert=_update($table, $form_dat_lote, $where);
                        } else {
                            # code...
                            if (($entrada_lote-$cantidad_a_descontar)==0) {
                                # code...
                                $table='lote';
                                $form_dat_lote=$arrayName = array(
                                'cantidad'=>($entrada_lote-$cantidad_a_descontar),
                                'estado' => 'FINALIZADO',
                            );
                                $cantidad_a_descontar=0;

                                $where = " WHERE id_lote='$row[id_lote]'";
                                $insert=_update($table, $form_dat_lote, $where);
                            } else {
                                $table='lote';
                                $form_dat_lote=$arrayName = array(
                                'cantidad'=>0,
                                'estado' => 'FINALIZADO',
                            );
                                $cantidad_a_descontar=$cantidad_a_descontar-$entrada_lote;
                                $where = " WHERE id_lote='$row[id_lote]'";
                                $insert=_update($table, $form_dat_lote, $where);
                            }
                        }
                    }
                }
                if (!$insert) {
                    $l = 0;
                }
            }
        }
        /*fin arreglar problema con lotes*/
    }
    if ($insert_mov &&$corr &&$z && $j && $k && $l && $m) {
        _commit();
        $xdatos['typeinfo']='Success';
        $xdatos['msg']='Registro ingresado con éxito!';
    } else {
        _rollback();
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Registro de no pudo ser ingresado!';
    }
    echo json_encode($xdatos);
}
function consultar_stock()
{
  $id_producto = $_REQUEST['id_producto'];
  $ubicacion = $_REQUEST['ubicacion'];
  $tipo = $_REQUEST['tipo'];
  $id_sucursal=$_SESSION['id_sucursal'];
  $id_usuario=$_SESSION['id_usuario'];
  $id_presentacione=0;
  $r_precios=_fetch_array(_query("SELECT precios FROM usuario WHERE id_usuario=$id_usuario"));
  $precios=$r_precios['precios'];
  $limit="LIMIT ".$precios;
  if($tipo == "D")
  {
    $clause = "p.id_producto = '$id_producto'";
  }
  else
  {
    $sql_aux= _query("SELECT id_producto FROM producto WHERE codart='$id_producto'");
    echo _error();
    if(_num_rows($sql_aux)>0)
    {
      $dats_aux = _fetch_array($sql_aux);
      $id_producto = $dats_aux["id_producto"];
      $clause = "p.id_producto = '$id_producto'";
    }
    else
    {
      $sql_aux = _query("SELECT id_presentacion, id_producto FROM presentacion_producto WHERE barcode='$id_producto' AND activo='1'");
      if(_num_rows($sql_aux)>0)
      {
        $dats_aux = _fetch_array($sql_aux);
        $id_producto = $dats_aux["id_producto"];
        $id_presentacione = $dats_aux["id_presentacion"];
        $clause = "p.id_producto = '$id_producto'";
      }
      else
      {
        $clause = "p.barcode = '$id_producto'";
      }
    }
  }
  $sql1 = "SELECT p.id_producto, p.descripcion
           FROM producto AS p
           WHERE $clause";
  $stock1=_query($sql1);
  if (_num_rows($stock1)>0)
  {
    $row1=_fetch_array($stock1);
    $descipcion = $row1["descripcion"];
    $id_producto = $row1["id_producto"];
    $sql_exis = _query("SELECT sum(cantidad) as stock FROM stock_ubicacion WHERE id_producto = '$id_producto' AND id_ubicacion='$ubicacion'");
    $datos_exis = _fetch_array($sql_exis);
    $stockv = $datos_exis["stock"];
    if($stockv>0)
    {
      $i=0;
      $unidadp=0;
      $preciop=0;
      $costop=0;
      $descripcionp=0;
      $anda = "";
      if($id_presentacione > 0)
      {
        $anda = " AND prp.id_presentacion = '$id_presentacione'";
      }
      $sql_p=_query("SELECT presentacion.nombre, prp.descripcion,
                     prp.id_presentacion,prp.unidad,prp.costo,prp.precio
                     FROM presentacion_producto AS prp
                     JOIN presentacion ON presentacion.id_presentacion=prp.presentacion
                     WHERE prp.id_producto='$id_producto'
                     AND prp.activo=1
                     AND prp.id_sucursal='$id_sucursal'
                     $anda");
      $select="<select class='sel form-control'>";
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
      $xdatos['stock']= $stockv;
      $xdatos['select']= $select;
      $xdatos['descrip']= $descipcion;
      $xdatos['id_p']= $id_producto;
      $xdatos['costop']= $costop;
      $xdatos['preciop']= $preciop;
      $xdatos['unidadp']= $unidadp;
      $xdatos['descripcionp']= $descripcionp;
      $xdatos['i']=$i;

      $sql_perece="SELECT * FROM producto WHERE id_producto='$id_producto'";
      $result_perece=_query($sql_perece);
      $row_perece=_fetch_array($result_perece);
      $perecedero=$row_perece['perecedero'];
      $xdatos['perecedero'] = $perecedero;
      $xdatos['categoria']=$row_perece['id_categoria'];
      $xdatos['typeinfo']="Success";
      echo json_encode($xdatos);
    }
    else
    {
      $sql_exis = _query("SELECT stock FROM stock WHERE id_producto = '$id_producto'");
      $datos_exis = _fetch_array($sql_exis);
      $stockv = $datos_exis["stock"];
      if($stockv>0)
      {
        $xdatos['typeinfo']="Error";
        $xdatos['msg']="El producto seleccionado no posee existencias en esta ubicacion";
        echo json_encode($xdatos);
      }
      else
      {
        $xdatos['typeinfo']="Error";
        $xdatos['msg']="El producto seleccionado no posee existencias";
        echo json_encode($xdatos);
      }
    }
  }
  else
  {
    $xdatos['typeinfo']="Error";
    $xdatos['msg']="El codigo ingresado no pertenece a ningun producto";
    echo json_encode($xdatos);
  }
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
if (!isset($_REQUEST['process'])) {
    initial();
}
if (isset($_REQUEST['process'])) {
    switch ($_REQUEST['process']) {
    case 'insert':
    insertar();
    break;
    case 'consultar_stock':
    consultar_stock();
    break;
    case 'getpresentacion':
    getpresentacion();
    break;
    case 'traerdatos':
    traerdatos();
    break;
    case'traerpaginador':
    traerpaginador();
    break;
  }
}
?>
