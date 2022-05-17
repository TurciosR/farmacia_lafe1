<?php
include_once "_core.php";
include('num2letras.php');

include('facturacion_funcion_imprimir.php');
//errores
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

function initial()
{
  $title="Agregar Cotización";
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

  //array de tipo_pagos
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
            <input type="hidden" name="process" id="process" value="insert">

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
                              echo "<option value='".$row_emp["id_empleado"]."'>".$row_emp["nombre"]."</option>";
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
                              echo "<option value='".$row_cli["id_cliente"]."'>".$row_cli["nombre"]."</option>";
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div  class="form-group col-md-4">
                        <div class="form-group has-info">
                          <label>Fecha</label>
                          <input type='text' class='datepick form-control' id='fecha' name='fecha' value='<?php echo $fecha_actual; ?>'>
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
                          <input type="text"  class='form-control' value="3"  id="vigencia">
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group has-info">
                          <label>Items</label>
                          <input type="text"  class='form-control'  id="items" value=0 readOnly/>
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
                                  <th hidden class="success cell100 column10">Id</th>
                                  <th class='success  cell100 column30'>Descripci&oacute;n</th>
                                  <th class='success  cell100 column10'>Stock</th>
                                  <th class='success  cell100 column10'>Presentación</th>
                                  <th class='success  cell100 column10'>Descripción</th>
                                  <th class='success  cell100 column10'>Precio</th>
                                  <th hidden class='success  cell100 column10'></th>
                                  <th class='success  cell100 column10'>Cantidad</th>
                                  <th class='success  cell100 column10'>Subtotal</th>
                                  <th class='success  cell100 column10'>Acci&oacute;n</th>
                                </tr>
                              </thead>
                            </table>
                          </div>
                          <div class="table100-body js-pscroll">
                            <table>
                              <tbody id="inventable"></tbody>
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
              <input type='hidden' name='filas' id='filas' value='0'>
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
  $sql="SELECT cot FROM correlativo WHERE id_sucursal='$id_sucursal'";
  $result= _query($sql);
  $rows=_fetch_array($result);
  $ult=$rows['cot']+1;
  $len = strlen($ult);
  $nceros = 7 - $len;
  $numero_doc = "COT_".ceros_izquierda($nceros,$ult);
  $table_numdoc="correlativo";
  $data_numdoc = array(
    'cot' => $ult,
  );
  $where_clause_n="WHERE  id_sucursal='$id_sucursal'";
  $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);

  if ($cuantos>0)
  {
    $sql_fact="SELECT * FROM cotizacion WHERE id_cliente='$id_cliente' AND total='$total_venta'  AND id_sucursal='$id_sucursal' AND fecha='$fecha_movimiento'";
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
        'numero_doc' => $numero_doc,
        'total' => $total_venta,
        'impresa' => 0,
        'id_empleado' => $id_empleado,
        'id_vendedor'=>$id_vendedor,
        'id_sucursal' => $id_sucursal,
      );
      $insertar_fact = _insert($table_fact, $form_data_fact);
      $id_fact= _insert_id();
    }
    $array = json_decode($array_json, true);
    foreach ($array as $fila)
    {
      if ($fila['precio']>=0 && $fila['subtotal']>=0  && $fila['cantidad']>0)
      {
        $id_producto=$fila['id'];
        $cantidad=$fila['cantidad'];
        $precio_venta=$fila['precio'];
        $id_presentacion=$fila['id_presentacion'];
        $unidades=$fila['unidades'];
        $subtotal=$fila['subtotal'];
        $cantidad_real=$cantidad;

        $table_fact_det= 'cotizacion_detalle';
        $data_fact_det = array(
          'id_cotizacion' => $id_fact,
          'id_prod_serv' => $id_producto,
          'cantidad' => $cantidad_real,
          'precio_venta' => $precio_venta,
          'subtotal' => $subtotal,
          'tipo_prod_serv' => $tipoprodserv,
          'id_presentacion'=> $id_presentacion,
          'id_sucursal' => $id_sucursal,
        );
        if ($cantidad>0 && $id_fact > 0)
        {
          $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
          if(!$insertar_fact_det)
          {
            $insertar_fact_dett = false;
          }
        }
      } // if($fila['cantidad']>0 && $fila['precio']>0){
      } //foreach ($array as $fila){
        if ($insertar_numdoc  && $insertar_fact && $insertar_fact_dett)
        {
          _commit(); // transaction is committed
          $xdatos['typeinfo']='Success';
          $xdatos['msg']='Cotización Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
          $xdatos['factura']=$id_fact;
        }
        else
        {
          _rollback(); // transaction rolls back
          $xdatos['typeinfo']='Error';
          $xdatos['msg']='Cotización no pudo ser registrada!'.$insertar_fact."-".$insertar_numdoc."-".$insertar_fact_dett;
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

      $sql1 = "SELECT p.id_producto,p.id_categoria, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal, s.stock FROM producto AS p, stock as s WHERE p.id_producto=s.id_producto AND p.id_producto ='$id_producto'  AND s.id_sucursal='$id_sucursal'";
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
        $categoria=$row1["id_categoria"];

        $i=0;
        $unidadp=0;
        $preciop=0;
        $descripcionp=0;

        $select_rank="<select class='sel_r form-control'>";

        $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1 AND presentacion_producto.id_sucursal=$id_sucursal");
        $select="<select class='sel form-control'>";
        while ($row=_fetch_array($sql_p))
        {
          if ($i==0)
          {
            $unidadp=$row['unidad'];
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
            $select_rank.="</select>";
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
        $xdatos['categoria']= $categoria;
        $xdatos['select_rank']=$select_rank;

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
