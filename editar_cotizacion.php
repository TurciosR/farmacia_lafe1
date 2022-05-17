<?php
include_once "_core.php";
include('num2letras.php');

include('facturacion_funcion_imprimir.php');
//errores
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

function initial()
{
  $title="Editar Cotización";
  $_PAGE = array();
  $_PAGE ['title'] = $title;
  $_PAGE ['links'] = null;
  $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/bootstrap-checkbox/bootstrap-checkbox.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/plugins/perfect-scrollbar/perfect-scrollbar.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/util.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/main.css">';

  include_once "header.php";
  include_once "main_menu.php";

  $id_usuario=$_SESSION["id_usuario"];
  $fecha_actual=date("Y-m-d");
  //permiso del script
  $id_user=$_SESSION["id_usuario"];
  $admin=$_SESSION["admin"];
  $uri = $_SERVER['SCRIPT_NAME'];
  $filename=get_name_script($uri);
  $links=permission_usr($id_user, $filename);
  $id_sucursal=$_SESSION['id_sucursal'];

  $id_cotizacion = $_REQUEST["id_cotizacion"];
  $sql_cot = _query("SELECT id_cliente, id_vendedor, fecha, vigencia FROM cotizacion WHERE id_cotizacion='$id_cotizacion'");
  $dat_cot = _fetch_array($sql_cot);
  $id_cliente = $dat_cot["id_cliente"];
  $id_vendedor = $dat_cot["id_vendedor"];
  $fecha = $dat_cot["fecha"];
  $vigencia = $dat_cot["vigencia"];
  $sql_nitm = _num_rows(_query("SELECT * FROM cotizacion_detalle WHERE id_cotizacion='$id_cotizacion'"));
  //impuestos
  $sql_iva="SELECT iva,monto_retencion1,monto_retencion10,monto_percepcion FROM sucursal WHERE id_sucursal='$id_sucursal'";
  $result_IVA=_query($sql_iva);
  $row_IVA=_fetch_array($result_IVA);
  $iva=$row_IVA['iva']/100;
  $monto_retencion1=$row_IVA['monto_retencion1'];
  $monto_retencion10=$row_IVA['monto_retencion10'];
  $monto_percepcion=$row_IVA['monto_percepcion'];
  //caja
  //SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND id_empleado = '$id_user'

  ?>

  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-2"></div>
  </div>
  <div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox">
          <?php  if ($links!='NOT' || $admin=='1') { ?>

            <input type='hidden' name='urlprocess' id='urlprocess' value="<?php echo $filename; ?>">
            <input type='hidden' name='id_cotizacion' id='id_cotizacion' value="<?php echo $id_cotizacion; ?>">
            <input type="hidden" name="process" id="process" value="edit">

            <div class="ibox-content">
              <section>

                <div class="panel">
                  <input type='hidden' name='caja' id='caja' value='<?php echo $caja; ?>'>
                  <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
                  <input type='hidden' name='monto_retencion1' id='monto_retencion1' value='100'>
                  <input type='hidden' name='monto_retencion10' id='monto_retencion10' value='100'>
                  <input type='hidden' name='monto_percepcion' id='monto_percepcion' value='100'>
                  <input type='hidden' name='porc_retencion1' id='porc_retencion1' value=0>
                  <input type='hidden' name='porc_retencion10' id='porc_retencion10' value=0>
                  <input type='hidden' name='porc_percepcion' id='porc_percepcion' value=0>
                  <input type='hidden' name='porcentaje_descuento' id='porcentaje_descuento' value=0>

                  <div class="widget-content">
                    <div class="row">
                      <div  class="form-group col-md-4">
                        <div class="form-group has-info">
                          <label>Seleccione Vendedor</label>
                          <select class="form-control select" name="vendedor" id="vendedor">
                            <option value="">Seleccione</option>
                            <?php
                            $sqlemp=_query("SELECT id_empleado, nombre FROM empleado WHERE id_sucursal='$id_sucursal' AND id_tipo_empleado=2");
                            while($row_emp = _fetch_array($sqlemp))
                            {
                              echo "<option value='".$row_emp["id_empleado"]."'";
                              if( $row_emp["id_empleado"]==$id_vendedor)
                              {
                                echo " selected ";
                              }
                              echo ">".$row_emp["nombre"]."</option>";
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div id='form_datos_cliente' class="form-group col-md-4">
                        <div class="form-group has-info">
                          <label>Cliente&nbsp;</label>
                          <select class="form-control select" name="id_cliente" id="id_cliente">
                            <option value="">Seleccione</option>
                            <?php
                            $sqlcli=_query("SELECT * FROM cliente WHERE id_sucursal='$id_sucursal' ORDER BY nombre");
                            while($row_cli = _fetch_array($sqlcli))
                            {
                              echo "<option value='".$row_cli["id_cliente"]."'";
                              if($row_cli["id_cliente"]==$id_cliente)
                              {
                                echo " selected ";
                              }
                              echo ">".$row_cli["nombre"]."</option>";
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div  class="form-group col-md-4">
                        <div class="form-group has-info">
                          <label>Fecha</label>
                          <input type='text' class='datepick form-control' id='fecha' name='fecha' value='<?php echo $fecha; ?>'>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-6">
                        <div class="form-group has-info">
                          <label id='buscar_habilitado'>Buscar Producto (Descripci&oacute;n)</label>
                          <input type="text" id="producto_buscar" name="producto_buscar"  class="form-control" placeholder="Ingrese Descripcion de producto" data-provide="typeahead" style="border-radius:0px">
                          <input type="text" id="barcode" name="barcode" class="form-control" placeholder="Ingrese  barcode producto" style="border-radius:0px">
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group has-info">
                          <label>Vigencia (Dias)</label>
                          <input type="text"  class='form-control'  id="vigencia" value="<?php echo $vigencia; ?>">
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group has-info">
                          <label>Items</label>
                          <input type="text"  class='form-control'  id="items"  readOnly value="<?php echo $sql_nitm; ?>">
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="title-action" id='botones'>
                          <button type="submit" id="submit1" name="submit1" class="btn btn-primary"><i class="fa fa-save"></i> F9 Guardar</button>
                        </div>
                      </div>
                      <div class="form-group col-md-6" hidden>
                        <br>
                        <a name="button" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Verificar Stock</a>
                      </div>
                    </div>

                  </div>
                  <!-- fin buscador Superior -->
                  <div class="row">
                    <div class="col-md-12">
                      <div class="wrap-table1001">
                        <div class="table100 ver1 m-b-10">
                          <div class="table100-head">
                            <table id="inventable1">
                              <thead>
                                <tr class="row100 head">
                                  <th class="success cell100 column10">Id</th>
                                  <th class='success  cell100 column20'>Descripci&oacute;n</th>
                                  <th class='success  cell100 column10'>Stock</th>
                                  <th class='success  cell100 column10'>Presentación</th>
                                  <th class='success  cell100 column10'>Descripción</th>
                                  <th class='success  cell100 column10'>Precio</th>
                                  <th class='success  cell100 column10'>Cantidad</th>
                                  <th class='success  cell100 column10'>Subtotal</th>
                                  <th class='success  cell100 column10'>Acci&oacute;n</th>
                                </tr>
                              </thead>
                            </table>
                          </div>
                          <div class="table100-body js-pscroll">
                            <table>
                              <tbody id="inventable">
                                <?php
                                $sql_det = _query("SELECT id_detalle, id_prod_serv, cantidad, precio_venta, id_presentacion, subtotal FROM cotizacion_detalle WHERE id_cotizacion='$id_cotizacion'");
                                $filas = 0;
                                while ($row = _fetch_array($sql_det))
                                {
                                  $id_producto = $row["id_prod_serv"];
                                  $id_detalle = $row["id_detalle"];
                                  $cantidad_s = $row["cantidad"];
                                  $subt_mostrar = $row["subtotal"];
                                  $precio_venta = $row["precio_venta"];
                                  $id_presentacion = $row["id_presentacion"];
                                  $sql1 = "SELECT p.id_producto, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal, s.stock FROM producto AS p, stock as s WHERE p.id_producto=s.id_producto AND p.id_producto ='$id_producto' AND p.id_sucursal='$id_sucursal' AND s.id_sucursal='$id_sucursal'";
                                  $stock1=_query($sql1);
                                  $row1=_fetch_array($stock1);
                                  $nrow1=_num_rows($stock1);
                                  if ($nrow1>0)
                                  {
                                    $barcode = $row1["barcode"];
                                    $descripcion = $row1["descripcion"];
                                    $stock= $row1["stock"];

                                    $i=0;
                                    $unidadp=0;
                                    $preciop=0;
                                    $descripcionp=0;

                                    $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1");
                                    $select="<select class='sel form-control'>";
                                    while ($row2=_fetch_array($sql_p))
                                    {
                                      $select.="<option value='$row2[id_presentacion]'";
                                      if($row2["id_presentacion"] == $id_presentacion)
                                      {
                                        $select.= " selected ";
                                        $unidadp=$row2['unidad'];
                                        $preciop=$row2['precio'];
                                        $descripcionp=$row2['descripcion'];
                                      }
                                      $select.=">$row2[nombre]</option>";
                                    }
                                    $cantidades = "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal' id='cant' name='cant' value='".$cantidad_s."' style='width:60px;'></div></td>";
                                    $tr_add = '';
                                    $tr_add .= "<tr class='row100 head' id='".$filas."' id_detalle='".$id_detalle."'>";
                                    $tr_add .= "<td class='cell100 column10 text-success id_pps'><input type='hidden' id='unidades' name='unidades' value='".$unidadp."'>".$id_producto."</td>";
                                    $tr_add .= "<td class='cell100 column20 text-success'>".$descripcion.'</td>';
                                    $tr_add .= "<td class='cell100 column10 text-success' id='cant_stock'>".$stock."</td>";
                                    $tr_add .= "<td class='cell100 column10 text-success preccs'>".$select."</td>";
                                    $tr_add .= "<td class='cell100 column10 text-success descp'><input type'text' id='dsd' value='".$descripcionp."' class='form-control' readonly></td>";
                                    $tr_add .= "<td class='cell100 column10 text-success'><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='".$precio_venta."'><input type='text'  class='form-control decimal'  id='precio_venta' name='precio_venta' value='".$preciop."'></td>";
                                    $tr_add .= $cantidades;
                                    $tr_add .= "<td class='ccell100 column10'><input type='hidden'  id='subt_iva' name='subt_iva' value='0.0'><input type='text'  class='decimal form-control' id='subtotal_fin' name='subtotal_fin'  value='".$subt_mostrar."'readOnly></td>";
                                    $tr_add .= '<td class="cell100 column10 Delete_bd text-center"><input id="delprod_bd" type="button" class="btn btn-danger fa"  value="&#xf1f8;"></td>';
                                    $tr_add .= '</tr>';
                                    $filas ++;
                                    echo $tr_add;
                                  }
                                }
                                  ?>
                                </tbody>
                              </table>
                            </div>
                            <div class="table101-body">
                              <table>
                                <tbody>
                                  <tr class='red'>
                                    <td class="cell100 column100">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td class='cell100 column50 text-bluegrey'  id='totaltexto'>&nbsp;</td>
                                    <td class='cell100 column15 leftt  text-bluegrey ' >CANT. PROD:</td>
                                    <td class='cell100 column10 text-right text-danger' id='totcant'>0.00</td>
                                    <td class="cell100 column10  leftt text-bluegrey ">TOTALES $:</td>
                                    <td class='cell100 column15 text-right text-green' id='total_gravado'>0.00</td>

                                  </tr>
                                  <tr>
                                    <td class="cell100 column15 leftt text-bluegrey ">SUMAS (SIN IVA) $:</td>
                                    <td  class="cell100 column10 text-right text-green" id='total_gravado_sin_iva'>0.00</td>
                                    <td class="cell100 column15  leftt  text-bluegrey ">IVA  $:</td>
                                    <td class="cell100 column10 text-right text-green " id='total_iva'>0.00</td>
                                    <td class="cell100 column15  leftt text-bluegrey ">SUBTOTAL  $:</td>
                                    <td class="cell100 column10 text-right  text-green" id='total_gravado_iva'>0.00</td>
                                    <td class="cell100 column15 leftt  text-bluegrey ">VENTA EXENTA $:</td>
                                    <td class="cell100 column10  text-right text-green" id='total_exenta'>0.00</td>
                                  </tr>
                                  <tr>
                                    <td class="cell100 column15 leftt text-bluegrey ">PERCEPCION $:</td>
                                    <td class="cell100 column10 text-right  text-green"  id='total_percepcion'>0.00</td>
                                    <td class="cell100 column15  leftt  text-bluegrey ">RETENCION $:</td>
                                    <td class="cell100 column10 text-right text-green" id='total_retencion'>0.00</td>
                                    <td class="cell100 column15 leftt text-bluegrey ">DESCUENTO $:</td>
                                    <td class="cell100 column10  text-right text-green"  id='total_final'>0.00</td>
                                    <td class="cell100 column15 leftt  text-bluegrey">A PAGAR $:</td>
                                    <td class="cell100 column10  text-right text-green"  id='monto_pago'>0.00</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </section>
                <input type='hidden' name='totalfactura' id='totalfactura' value='0'>
                <input type='hidden' name='filas' id='filas' value='<?php echo $sql_nitm; ?>'>
              </div>
            </div>
            <!--<div class='ibox float-e-margins' -->
          </div>
          <!--div class='col-lg-12'-->
        </div>
        <!--div class='row'-->
      </div>
      <!--div class='wrapper wrapper-content  animated fadeInRight'-->
      <?php
      include_once ("footer.php");
      echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
      echo "<script src='js/plugins/bootstrap-checkbox/bootstrap-checkbox.js'></script>";
      echo '<script src="js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
      <script src="js/funciones/main.js"></script>';
      echo "<script src='js/funciones/util.js'></script>";
      echo "<script src='js/funciones/funciones_cotizacion.js'></script>";
    } //permiso del script
    else
    {
      echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
      include_once ("footer.php");
    }
  }
  function insertar()
  {
    //date_default_timezone_set('America/El_Salvador');
    $fecha_movimiento= $_POST['fecha_movimiento'];
    $id_cliente=$_POST['id_cliente'];
    $id_cotizacion=$_POST['id_cotizacion'];
    $total_venta = $_POST['total_venta'];
    $id_vendedor=$_POST['id_vendedor'];
    $vigencia=$_POST['vigencia'];
    $cuantos = $_POST['cuantos'];
    $array_json=$_POST['json_arr'];
    //  IMPUESTOS
    $total_iva= $_POST['total_iva'];
    $total_retencion= $_POST['total_retencion'];
    $total_percepcion= $_POST['total_percepcion'];

    $id_empleado=$_SESSION["id_usuario"];
    $id_sucursal=$_SESSION["id_sucursal"];
    $fecha_actual = date('Y-m-d');

    $tipoprodserv = "PRODUCTO";

    $insertar_fact=false;
    $insertar_fact_dett=true;
    $insertar_numdoc =false;

    $hora=date("H:i:s");
    $xdatos['typeinfo']='';
    $xdatos['msg']='';
    $xdatos['process']='';

    _begin();
    if ($cuantos>0)
    {
      $sql_fact="SELECT * FROM cotizacion WHERE id_cliente='$id_cliente' AND total='$total_venta'  AND id_sucursal='$id_sucursal' AND fecha='$fecha_movimiento' AND id_cotizacion!='$id_cotizacion'";
      $id_fact = 0;
      $result_fact=_query($sql_fact);
      $nrows_fact=_num_rows($result_fact);
      if ($nrows_fact==0)
      {
        $table_fact= 'cotizacion';
        $form_data_fact = array(
          'id_cliente' => $id_cliente,
          'fecha' => $fecha_movimiento,
          'hora' => $hora,
          'vigencia' => $vigencia,
          'total' => $total_venta,
          'impresa' => 0,
          'id_empleado' => $id_empleado,
          'id_vendedor'=>$id_vendedor,
          'id_sucursal' => $id_sucursal,
        );
        $where = "id_cotizacion='".$id_cotizacion."'";
        $insertar_fact = _update($table_fact, $form_data_fact, $where);
      }
      $array = json_decode($array_json, true);
      foreach ($array as $fila)
      {
        if ($fila['precio']>=0 && $fila['subtotal']>=0  && $fila['cantidad']>0)
        {
          $id_detalle=$fila['id_detalle'];
          $id_producto=$fila['id'];
          $cantidad=$fila['cantidad'];
          $precio_venta=$fila['precio'];
          $id_presentacion=$fila['id_presentacion'];
          $unidades=$fila['unidades'];
          $subtotal=$fila['subtotal'];
          $cantidad_real=$cantidad;

          $table_fact_det= 'cotizacion_detalle';
          $data_fact_det = array(
            'id_cotizacion' => $id_cotizacion,
            'id_prod_serv' => $id_producto,
            'cantidad' => $cantidad_real,
            'precio_venta' => $precio_venta,
            'subtotal' => $subtotal,
            'tipo_prod_serv' => $tipoprodserv,
            'id_presentacion'=> $id_presentacion,
            'id_sucursal' => $id_sucursal,
          );
          if ($cantidad>0)
          {
            if($id_detalle > 0)
            {
              $where1 = "id_detalle='".$id_detalle."'";
              $insertar_fact_det = _update($table_fact_det, $data_fact_det, $where1);
            }
            else
            {
              $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
            }
            if(!$insertar_fact_det)
            {
              $insertar_fact_dett = false;
            }
          }
        } // if($fila['cantidad']>0 && $fila['precio']>0){
        } //foreach ($array as $fila){
          if ($insertar_fact && $insertar_fact_dett)
          {
            _commit(); // transaction is committed
            $xdatos['typeinfo']='Success';
            $xdatos['msg']='Cotización modificada con exito!';
            $xdatos['factura']=$id_fact;
          }
          else
          {
            _rollback(); // transaction rolls back
            $xdatos['typeinfo']='Error';
            $xdatos['msg']='Cotización no pudo ser modificada!'.$insertar_fact."-".$insertar_fact_dett;
          }
        }
        echo json_encode($xdatos);
      }
      function consultar_stock()
      {
        $id_producto = $_REQUEST['id_producto'];
        $id_usuario=$_SESSION["id_usuario"];
        $id_sucursal=$_SESSION['id_sucursal'];
        $precio=0;

        $sql1 = "SELECT p.id_producto, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal, s.stock FROM producto AS p, stock as s WHERE p.id_producto=s.id_producto AND p.id_producto ='$id_producto' AND p.id_sucursal='$id_sucursal' AND s.id_sucursal='$id_sucursal'";
        $stock1=_query($sql1);
        $row1=_fetch_array($stock1);
        $nrow1=_num_rows($stock1);
        if ($nrow1>0)
        {
          $perecedero=$row1['perecedero'];
          $barcode = $row1["barcode"];
          $descripcion = $row1["descripcion"];
          $estado = $row1["estado"];
          $perecedero = $row1["perecedero"];
          $exento = $row1["exento"];
          $stock= $row1["stock"];

          $i=0;
          $unidadp=0;
          $preciop=0;
          $descripcionp=0;

          $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1");
          $select="<select class='sel form-control'>";
          while ($row=_fetch_array($sql_p))
          {
            if ($i==0)
            {
              $unidadp=$row['unidad'];
              $preciop=$row['precio'];
              $descripcionp=$row['descripcion'];
            }
            $select.="<option value='$row[id_presentacion]'>$row[nombre]</option>";
            $i=$i+1;
          }
          $select.="</select>";
          $xdatos['perecedero']=$perecedero;
          $xdatos['descripcion']= $descripcion;
          $xdatos['select']= $select;
          $xdatos['stock']= $stock;
          $xdatos['preciop']= $preciop;
          $xdatos['unidadp']= $unidadp;
          $xdatos['descripcionp']= $descripcionp;

          echo json_encode($xdatos); //Return the JSON Array
        }
      }
      function total_texto()
      {
        $total=$_REQUEST['total'];
        list($entero, $decimal)=explode('.', $total);
        $enteros_txt=num2letras($entero);
        $decimales_txt=num2letras($decimal);

        if ($entero>1) {
          $dolar=" dolares";
        } else {
          $dolar=" dolar";
        }
        $cadena_salida= "Son: ".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.";
        echo $cadena_salida;
      }
      function buscarBarcode()
      {
        $query = trim($_POST['id_producto']);
        $sql0="SELECT id_producto as id, descripcion, barcode, tipo_prod_servicio FROM producto  WHERE barcode='$query'";
        $result = _query($sql0);
        $numrows= _num_rows($result);

        $array_prod = array();
        $array_prod="";
        while ($row = _fetch_array($result)) {
          $barcod=" [".$row['barcode']."] ";
          $id_prod =$row['id'];
        }
        $xdatos['id_prod']=$id_prod;
        echo json_encode($xdatos); //Return the JSON Array
      }
      function agregar_cliente()
      {
        //$id_cliente=$_POST["id_cliente"];
        $nombre=$_POST["nombress"];
        $apellido=$_POST["apellidos"];
        $dui=$_POST["dui"];
        $tel1=$_POST["tel1"];
        $tel2=$_POST["tel2"];

        $sql_result=_query("SELECT * FROM cliente WHERE nombre='$nombre'");
        $numrows=_num_rows($sql_result);
        $row_update=_fetch_array($sql_result);
        $id_cliente=$row_update["id_cliente"];
        $name_cliente=$row_update["nombre"];


        //'id_cliente' => $id_cliente,
        $table = 'cliente';
        $form_data = array(
          'nombre' => $nombre,
          'apellido' => $apellido,
          'dui' => $dui,
          'telefono1' => $tel1,
          'telefono2' => $tel2,
        );

        if ($numrows == 0 && trim($nombre)!='') {
          $insertar = _insert($table, $form_data);
          $id_cliente=_insert_id();
          if ($insertar) {
            $xdatos['typeinfo']='Success';
            $xdatos['msg']='Registro insertado con exito!';
            $xdatos['process']='insert';
            $xdatos['id_client']=  $id_cliente;
          } else {
            $xdatos['typeinfo']='Error';
            $xdatos['msg']='Registro no insertado !';
          }
        } else {
          $xdatos['typeinfo']='Error';
          $xdatos['msg']='Registro no insertado !';
        }
        echo json_encode($xdatos);
      }
      function getpresentacion()
      {
        $id_presentacion =$_REQUEST['id_presentacion'];
        $sql=_fetch_array(_query("SELECT * FROM presentacion_producto WHERE id_presentacion=$id_presentacion"));
        $precio=$sql['precio'];
        $unidad=$sql['unidad'];
        $descripcion=$sql['descripcion'];
        $des = "<input type='text' id='ss' class='form-control' value='".$descripcion."' readonly>";
        $xdatos['precio']=$precio;
        $xdatos['unidad']=$unidad;
        $xdatos['descripcion']=$des;
        echo json_encode($xdatos);
      }
      function elim_det()
      {
        $id_detalle =$_REQUEST['id_detalle'];
        $where = "id_detalle='".$id_detalle."'";
        $table = "cotizacion_detalle";
        $del = _delete($table, $where);
        if($del)
        {
          $xdatos["typeinfo"] = "Success";
        }
        else
        {
          $xdatos["typeinfo"] = "Error";
        }
        echo json_encode($xdatos);
      }
      //functions to load
      if (!isset($_REQUEST['process'])) {
        initial();
      }
      //else {
      if (isset($_REQUEST['process'])) {
        switch ($_REQUEST['process']) {
          case 'insert':
          insertar();
          break;
          case 'consultar_stock':
          consultar_stock();
          break;
          case 'total_texto':
          total_texto();
          break;
          case 'del':
          elim_det();
          break;
          case 'buscarBarcode':
          buscarBarcode();
          break;
          case 'getpresentacion':
          getpresentacion();
          break;
          case 'agregar_cliente':
          agregar_cliente();
          break;
        }
      }
      ?>
