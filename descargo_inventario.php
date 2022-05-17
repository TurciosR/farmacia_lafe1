<?php
include_once "_core.php";

function initial()
{
  $title = "Descargo de Productos de Inventario";
  $_PAGE = array();
  $_PAGE ['title'] = $title;
  $_PAGE ['links'] = null;
  $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
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
                <div class="col-lg-4">
                  <div class="form-group has-info">
                    <label>Concepto</label>
                    <input type='text' class='form-control' value='DESCARGO DE PRODUCTOS' id='concepto' name='concepto'>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group has-info">
                    <label>Tipo</label>
                    <select class="form-control select" id="tipo" name="tipo">
                      <option value="VENCIMIENTO">VENCIMIENTO</option>
                      <option value="DESCARTE">DESCARTE</option>
                      <option value="PRODUCTO DAÑADO">PRODUCTO DAÑADO</option>
                      <option value="CONSUMO INTERNO">CONSUMO INTERNO</option>
                    </select>
                  </div>
                </div>
                <div class='col-lg-4'>
                  <div class='form-group has-info'>
                    <label>Fecha</label>
                    <input type='text' class='datepick form-control' value='<?php echo $fecha_actual; ?>' id='fecha1' name='fecha1'>
                  </div>
                </div>
              </div>
              <div class="row" id='buscador'>

                <div class="col-lg-4">
                  <div class='form-group has-info'><label>Origen</label>
                    <select name='origen' id="origen" class="form-control select">
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
                <div class="col-lg-8">
                  <div class='form-group has-info'><label>Buscar Productos</label>
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


                  <div  class='widget-content' id="content">
                    <div class="row">
                  <div class="col-md-12">

                    <table class="table table-striped" id='loadtable'>
                      <thead class='thead1'>
                        <tr class='tr1'>
                          <th class="text-success col-lg-5">Descripción</th>
                          <th class="text-success col-lg-1 text-center">Presentación</th>
                          <th class="text-success col-lg-1 text-center">Detalle</th>
                          <th class="text-success col-lg-1 text-center">Costo</th>
                          <th class="text-success col-lg-1 text-center">Precio</th>
                          <th class="text-success col-lg-1 text-center">Exis Unid.</th>
                          <th class="text-success col-lg-1 text-center">Cantidad</th>
                          <th class="text-success col-lg-1 text-center"></th>
                        </tr>
                      </thead>
                      <tbody class='tbody1 ' id="mostrardatos">
                      </tbody>
                    </table>
                  </div>
                </div>
                <!--/div-->

              </div>
              <div id="paginador"></div>
              <div class="widget-content" >
                <label>Total: </label>
                <label id="total_dinero"> </label>
              </div>
              <div class="widget-content" >
                <label>Cantidad: </label>
                <label id="totcant"></label>
              </div>
                    <input type="hidden" name="process" id="process" value="insert"><br>
                    <div class="widget-content">
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
  echo "<script src='js/funciones/funciones_descargo_inventario.js'></script>";
} //permiso del script
else
{
    echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
    include_once ("footer.php");
}
}
function traerdatos()
{
  $start = !empty($_POST['page'])?$_POST['page']:0;
  $limit =$_POST['records'];
  $sortBy = $_POST['sortBy'];
  $producto_buscar = $_POST['producto_buscar'];
  $origen = $_POST['origen'];

  $sqlJoined="SELECT pr.id_producto,pr.descripcion, pr.barcode FROM
  producto AS pr, stock_ubicacion as su";
  //  $sqlParcial=get_sql($keywords, $id_color, $estilo, $talla, $barcode, $limite);
  $sqlParcial= get_sql($start,$limit,$producto_buscar,$origen,$sortBy);
  $groupBy="";
  $limitSQL= " LIMIT $start,$limit ";
  $sql_final= $sqlJoined." ".$sqlParcial." ".$groupBy." ".$limitSQL;
  $query = _query($sql_final);
  $num_rows = _num_rows($query);
  $filas=0;
  if ($num_rows > 0)
  {
    while ($row = _fetch_array($query))
    {
      $id_producto = $row['id_producto'];
      $sql_existencia = _query("SELECT sum(cantidad) as existencia FROM stock_ubicacion WHERE id_producto='$id_producto' AND stock_ubicacion.id_ubicacion='$origen'");
      $dt_existencia = _fetch_array($sql_existencia);
      $existencia = $dt_existencia["existencia"];
      $descripcion=$row["descripcion"];
      $barcode = $row['barcode'];
      $sql_p=_query("SELECT presentacion.nombre, prp.descripcion,prp.id_presentacion,prp.unidad,prp.costo,prp.precio
                            FROM presentacion_producto AS prp
                            JOIN presentacion ON presentacion.id_presentacion=prp.presentacion
                            WHERE prp.id_producto=$id_producto
                            AND prp.activo=1 AND prp.id_sucursal=$_SESSION[id_sucursal]");
      $i=0;
      $unidadp=0;
      $costop=0;
      $preciop=0;
      $descripcionp="";
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
      $input = "<input type='text' readonly class='cant form-control numeric' style='width:100%;'>";
      ?>
      <tr>
        <td class='col-lg-5'> <input type='hidden' class='id_producto' name='' value='<?php echo $id_producto ?>'> <input type='hidden' class="unidad" value='<?php echo $unidadp; ?>'><?php echo $descripcion; ?></td>
        <td class='col-lg-1 text-center'><?php echo $select; ?></td>
        <td class='col-lg-1 text-center descp'><?php echo $descripcionp; ?></td>
        <td class='col-lg-1 text-center precio_compra'><?php echo $costop; ?></td>
        <td class='col-lg-1 text-center precio_venta'><?php echo $preciop; ?></td>
        <td class='col-lg-1 text-center exis'><?php echo $existencia; ?></td>
        <td class='col-lg-1 text-center'><?php echo $input; ?></td>
        <td class='col-lg-1 text-center'> <input type="checkbox" class='form-control cheke' name="" value=""></td>
      </tr>
      <?php
      $filas+=1;
    }
  }
}
function get_sql($start,$limit,$producto_buscar,$origen,$sortBy)
{
  $andSQL='';
  $id_sucursal= $_SESSION['id_sucursal'];
  $whereSQL=" WHERE pr.id_producto=su.id_producto
  AND su.id_ubicacion = '$origen'
  AND su.cantidad >= 0
  AND su.id_ubicacion=$origen
  AND su.id_sucursal = '$id_sucursal'";
  $andSQL.= "AND  pr.descripcion LIKE '$producto_buscar%'";
  $orderBy="";
  $sql_parcial=$whereSQL.$andSQL.$orderBy;
  return $sql_parcial;
}
function traerpaginador()
{
  $start = !empty($_POST['page'])?$_POST['page']:0;
  $limit =$_POST['records'];
  $sortBy = $_POST['sortBy'];
  $producto_buscar= $_POST['producto_buscar'];
  $origen= $_POST['origen'];
  $limite=50;
  $whereSQL =$andSQL =  $orderSQL = '';
  if(isset($_POST['page']))
  {
    //Include pagination class file
    include('Pagination.php');
    //get partial values from sql sentence
    $sqlParcial=get_sql($start,$limit,$producto_buscar,$origen,$sortBy);
    //get number of rows
    $sql1="SELECT COUNT(*) as numRecords  FROM producto AS pr, stock_ubicacion AS su";
    $sql_numrows=$sql1.$sqlParcial;
    $queryNum = _query($sql_numrows);
    if(_num_rows($queryNum)>0)
    {
      $resultNum = _fetch_array($queryNum);
      $rowCount = $resultNum['numRecords'];
    }
    else
    {
        $rowCount = 0;
    }
    //initialize pagination class
    $pagConfig = array(
      'currentPage' => $start,
      'totalRows' => $rowCount,
      'perPage' => $limit,
      'link_func' => 'searchFilter'
    );
    $pagination =  new Pagination($pagConfig);
    echo $pagination->createLinks();
    echo '<input type="hidden" id="cuantos_reg"  value="'.$rowCount.'">';
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

  $id=$_POST['iden'];

  $id_sucursal = $_SESSION["id_sucursal"];
  $sql_num = _query("SELECT di FROM correlativo WHERE id_sucursal='$id_sucursal'");
  $datos_num = _fetch_array($sql_num);
  $ult = $datos_num["di"]+1;
  $numero_doc=str_pad($ult,7,"0",STR_PAD_LEFT).'_DI';
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
  $up_corr=_update($table,$form_data,$where_clause_c);
  if ($up_corr) {
    # code...
  }
  else {
    $corr=0;
  }
  if ($concepto=='')
  {
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
    list($id_producto,$precio_compra,$precio_venta,$cantidad,$unidades,$fecha_caduca,$id_presentacion)=explode('|',$lista[$i]);

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
        }
        else
        {
          if ($nuevo_stock>0) {
            # code...
            $transfiriendo=$a_transferir;
            $a_transferir=0;
            $nuevo_stock=$stock_anterior-$transfiriendo;
          }
          else {
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
        $update=_update($table,$form_data,$where_clause);
        if ($update) {
          # code...
        }
        else {
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

        $insert_mss =_insert($table,$form_data);

        if ($insert_mss) {
          # code...
        }
        else {
          # code...
          $z=0;
        }

      }

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
        'costo_unitario'=>round(($precio_compra/$unidades),2),
        'precio_unitario'=>round(($precio_venta/$unidades),2),
        'create_date'=>$fecha_movimiento,
        'update_date'=>$fecha_movimiento,
        'id_sucursal' => $id_sucursal
      );
      $insert_stock = _insert($table2,$form_data2 );
    }
    else
    {
      $cant_total=$existencias-$cantidad;
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
                      $insert=_update($table,$form_dat_lote,$where);
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
                          $insert=_update($table,$form_dat_lote,$where);
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
                            $insert=_update($table,$form_dat_lote,$where);
                          }
                          else
                          {
                            $table='lote';
                            $form_dat_lote=$arrayName = array(
                                'cantidad'=>0,
                                'estado' => 'FINALIZADO',
                            );
                            $cantidad_a_descontar=$cantidad_a_descontar-$entrada_lote;
                            $where = " WHERE id_lote='$row[id_lote]'";
                            $insert=_update($table,$form_dat_lote,$where);
                          }
                      }
                  }
              }
          }
      }
      /*fin arreglar problema con lotes*/
    if(!$insert)
    {
      $l = 0;
    }

  }
  if($insert_mov &&$corr &&$z && $j && $k && $l && $m)
  {
    _commit();
    $xdatos['typeinfo']='Success';
    $xdatos['msg']='Registro ingresado con éxito!';
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

  $sql_p=_query("SELECT presentacion.nombre, prp.descripcion,prp.id_presentacion,prp.unidad,prp.costo,prp.precio FROM presentacion_producto AS prp JOIN presentacion ON presentacion.id_presentacion=prp.presentacion WHERE prp.id_producto=$id_producto AND prp.activo=1");
  $select="<select class='sel'>";
  while ($row=_fetch_array($sql_p))
  {
    if ($i==0)
    {
      $unidadp=$row['unidad'];
      $costop=$row['costo'];
      $preciop=$row['precio'];
      $descripcionp=$row['descripcion'];
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

  $sql_perece="SELECT * FROM producto WHERE id_producto='$id_producto'";
  $result_perece=_query($sql_perece);
  $row_perece=_fetch_array($result_perece);
  $perecedero=$row_perece['perecedero'];
  $xdatos['perecedero'] = $perecedero;
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
    case 'traerdatos':
    traerdatos();
    break;
    case'traerpaginador':
    traerpaginador();
    break;
  }
}
?>
