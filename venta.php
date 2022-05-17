<?php
include_once "_core.php";
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
include('num2letras.php');
include('facturacion_funcion_imprimir.php');
function initial()
{
  //$id_factura=$_REQUEST["id_factura"];
  $title="Venta";
  $_PAGE = array();
  $_PAGE ['title'] = $title;
  $_PAGE ['links'] = null;
  $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/typeahead.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2-bootstrap.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/bootstrap-checkbox/bootstrap-checkbox.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/plugins/perfect-scrollbar/perfect-scrollbar.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/util.css">';
  $_PAGE ['links'] .= '<link rel="stylesheet" type="text/css" href="css/main.css">';
  include_once "header.php";
  //include_once "main_menu.php";
  date_default_timezone_set('America/El_Salvador');
  $fecha_actual = date('Y-m-d');

  $id_sucursal=$_SESSION['id_sucursal'];
  //permiso del script
  $id_user=$_SESSION["id_usuario"];
  $sql_apertura = _query("SELECT * FROM apertura_caja WHERE vigente = 1 AND id_sucursal = '$id_sucursal' AND fecha='$fecha_actual' AND id_empleado = '$id_user'");
  $cuenta = _num_rows($sql_apertura);

  $turno_vigente=0;
  if ($cuenta>0) {
    $row_apertura = _fetch_array($sql_apertura);
    $id_apertura = $row_apertura["id_apertura"];
    $turno = $row_apertura["turno"];
    $caja = $row_apertura["caja"];
    $fecha_apertura = $row_apertura["fecha"];
    $hora_apertura = $row_apertura["hora"];
    $turno_vigente = $row_apertura["vigente"];
  }

  //impuestos
  $sql_iva="SELECT iva,monto_retencion1,monto_retencion10,monto_percepcion,cargo_tarjeta FROM sucursal WHERE id_sucursal='$id_sucursal'";
  $result_IVA=_query($sql_iva);
  $row_IVA=_fetch_array($result_IVA);
  $iva=$row_IVA['iva']/100;
  $monto_retencion1=$row_IVA['monto_retencion1'];
  $monto_retencion10=$row_IVA['monto_retencion10'];
  $monto_percepcion=$row_IVA['monto_percepcion'];
  $cargo_tarjeta=$row_IVA['cargo_tarjeta']/100;
  /////////////////////////////////////////////////////
  $admin=$_SESSION["admin"];
  $uri = $_SERVER['SCRIPT_NAME'];
  $filename=get_name_script($uri);
  $links=permission_usr($id_user, $filename);
  $id_usuario=$id_user;

  $fecha_actual=date("Y-m-d");
  //array clientes

  //clientes

  //factura
  ?>

  <style media="screen">
  .sweet-alert i
  {
    color:#FF0000;
  }
  </style>
  <style media="screen">
  span.select2-container--open {
    z-index: 9999 !important;
  }
  #d_d td
  {
    font-size: 12px;
    font-weight: bold;
  }
  </style>

  <div class="gray-bg">
    <div class="wrapper wrapper-content  animated fadeInRight">
      <div class="row">
        <div class="col-lg-12">
          <div class="ibox ">
            <?php
            //permiso del script
            if ($links!='NOT' || $admin=='1') {
              if ($turno_vigente=='1' ){
                ?>
                <div class="ibox-content">
                  <input type="hidden" id="fecha" value="<?php echo $fecha_actual; ?>">
                  <div class="row focuss"><br>
                    <div class="form-group col-md-5">
                      <div id="a">
                        <label id='buscar_habilitado'>Buscar Producto (Descripci&oacute;n)</label>
                        <div id="scrollable-dropdown-menu">
                          <input type="text" id="producto_buscar" name="producto_buscar"  style="width:100% !important" class=" form-control usage typeahead" placeholder="Ingrese Descripcion de producto" data-provide="typeahead" style="border-radius:0px">
                        </div>
                      </div>

                      <div hidden id="b">
                        <label id='buscador_composicion'>Buscar Producto (Composición)</label>
                        <div id="scrollable-dropdown-menu">
                          <input type="text" id="composicion" name="composicion"  style="width:100% !important" class=" form-control usage typeahead" placeholder="Ingrese la Composicion del producto" data-provide="typeahead" style="border-radius:0px">
                        </div>
                      </div>
                    </div>

                    <div class="col-md-7"><br>


                      <a class="btn btn-danger pull-right" style="margin-left:1%;" href="dashboard.php" id='salir'><i class="fa fa-mail-reply"></i> F4 Salir</a>
                      <button type="button" id="preventa" style="margin-left:1%;" name="preventa" class="btn btn-success pull-right usage"><i class="fa fa-save"></i> F8 Guardar</button>
                      <button type="button" id="borrar_preven" style="margin-left:1%;" name="borrar_preven" class="btn btn-success pull-right usage"><i class="fa fa-trash"></i> F6 Borrar </button>
                      <button type="button" id="submit1" name="submit1" class="btn btn-primary pull-right usage"><i class="fa fa-check"></i> F2 Pagar</button>

                      <?php
                      $filename='agregar_ingreso_caja.php';
                      $link=permission_usr($id_user,$filename);
                      if ($link!='NOT' || $admin=='1' ){
                        echo "
                        <a id='xa' data-toggle='modal' href='agregar_ingreso_caja_v.php'  style='margin-right:1%;'  data-target='#viewModal2' data-refresh='true' class='btn btn-warning pull-right'><i class='fa fa-plus icon-large'></i> F10 Ingreso</a>
                        ";
                      }
                      $filename='agregar_salida_caja.php';
                      $link=permission_usr($id_user,$filename);
                      if ($link!='NOT' || $admin=='1' ){
                        echo "
                        <a id='xb' data-toggle='modal' href='agregar_salida_caja_v.php' style='margin-right:1%;' data-target='#salidaModal' data-refresh='true' class='btn btn-danger pull-right'><i class='fa fa-minus icon-large'></i> F9 Vale</a>
                        ";
                      }
                      ?>

                      <a  data-toggle='modal' id="xc" href='modal_busqueda.php' style='margin-right:1%;' data-target='#busqueda' data-refresh='true' class='btn btn-info pull-right'><i class='fa fa-search icon-large'></i> F3</a>

                    </div>
                  </div>
                  <div class="row">

                    <div  class="form-group col-md-2">
                      <div class="form-group has-info">
                        <label>Referencia</label>
                        <input type="text" class="form-control" id="num_ref" name="num_ref" value="">

                      </div>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>Referencia</label>
                      <!--
                      <input type="text" name="n_ref" id="n_ref" class="form-control usage" style="border-radius:0px;">
                    -->
                    <select class="form-control select_r" name="n_ref" id="n_ref">
                      <option value="0">Seleccione</option>
                      <?php
                      $fecha_actual = date("Y-m-d");
                      $sql="SELECT cliente.nombre, factura.total,numero_ref FROM factura JOIN cliente ON cliente.id_cliente=factura.id_cliente WHERE numero_ref!=0 AND fecha='".date("Y-m-d")."' AND finalizada!=1 AND factura.id_sucursal=$id_sucursal";

                      $result=_query($sql);
                      $cuenta = _num_rows($result);
                      echo _error();
                      if($cuenta > 0)
                      {
                        while ($row = _fetch_array($result))
                        {
                          echo "<option value='".$row['numero_ref']."'>";
                          echo "".$row['numero_ref']." | ";
                          echo "".utf8_decode(Mayu(utf8_decode($row['nombre'])))." | ";
                          echo "$".number_format($row['total'],2)."";
                          echo "</option>";


                        }
                      }
                      ?>
                    </select>
                  </div>

                  <div hidden  class="form-group col-md-2">
                    <div class="form-group has-info">
                      <label>Seleccione Vendedor</label>
                      <input type="text" name="vendedor" id="vendedor" value="">
                      <!--
                      <select class="form-control select usage" name="vendedor" id="vendedor">
                      <option value="">Seleccione</option>
                    -->
                    <?php
                    /*  $sqlemp=_query("SELECT id_empleado, nombre FROM empleado WHERE id_sucursal='$id_sucursal' AND id_tipo_empleado=2");
                    while($row_emp = _fetch_array($sqlemp))
                    {
                    echo "<option value='".$row_emp["id_empleado"]."'>".$row_emp["nombre"]."</option>";
                  }*/
                  ?>
                </select>
              </div>
            </div>
            <div id='form_datos_cliente' class="form-group col-md-3">
              <div class="form-group has-info">
                <label>Cliente&nbsp;</label>
                <select class="form-control select usage" name="id_cliente" id="id_cliente">
                  <option value="">Seleccione</option>
                  <?php
                  $sqlcli=_query("SELECT * FROM cliente WHERE id_sucursal='$id_sucursal' ORDER BY nombre");
                  while($row_cli = _fetch_array($sqlcli))
                  {
                    echo "<option value='".$row_cli["id_cliente"]."'";
                    if($row_cli["id_cliente"] == 1)
                    {
                      echo " selected ";
                    }
                    echo ">".$row_cli["nombre"]."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div  class="form-group col-md-2">
              <div class="form-group has-info">
                <label>Tipo Impresi&oacuten</label>
                <select name='tipo_impresion' id='tipo_impresion' class='select form-control usage'>
                  <option value='TIK' selected>COBRO</option>
                  <option value='TIK'>TICKET</option>
                  <option value='COF'>FACTURA</option>
                  <option value='CCF'>CREDITO FISCAL</option>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group has-info">
                <label>Seleccione tipo de pago</label><br>
                <select name='con_pago' id='con_pago' class='select form-control usage'>
                  <option value='0' selected>Contado</option>
                  <option value='1' >Credito</option>
                  <option value='2' >Tarjeta</option>
                  <option value='3' >Cheque</option>
                  <option value='4' >Bitcoin</option>

                </select>
              </div>
            </div>

          </div>
          <!--load datables estructure html-->
          <header>
            <section>
              <input type='hidden' name='porc_iva' id='porc_iva' value='<?php echo $iva; ?>'>
              <input type='hidden' name='monto_retencion1' id='monto_retencion1' value='<?php echo $monto_retencion1 ?>'>
              <input type='hidden' name='monto_retencion10' id='monto_retencion10' value='<?php echo $monto_retencion10 ?>'>
              <input type='hidden' name='monto_percepcion' id='monto_percepcion' value='100'>
              <input type='hidden' name='porc_retencion1' id='porc_retencion1' value=0>
              <input type='hidden' name='porc_retencion10' id='porc_retencion10' value=0>
              <input type='hidden' name='porc_percepcion' id='porc_percepcion' value=0>
              <input type='hidden' name='porcentaje_descuento' id='porcentaje_descuento' value=0>
              <input type="hidden" name="cargo_tarjeta" id="cargo_tarjeta" value="<?php echo $cargo_tarjeta ?>">

              <div class="">
                <div class="row">
                  <div class="col-md-9">
                    <div class="wrap-table1001">
                      <div class="table100 ver1 m-b-10">
                        <div class="table100-head">
                          <table id="inventable1">
                            <thead>
                              <tr class="row100 head">
                                <th hidden class="success cell100 column10">Id</th>
                                <th class='success  cell100 column30'>Descripci&oacute;n</th>
                                <th class='success  cell100 column10'>Stock</th>
                                <th class='success  cell100 column10'>Cantidad</th>
                                <th class='success  cell100 column10'>Presentación</th>
                                <th class='success  cell100 column10'>Descripción</th>
                                <th class='success  cell100 column10'>Precio</th>
                                <th hidden class='success  cell100 column10'></th>
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
                            <tbody id="d_d">
                              <tr>

                              </tr>
                              <tr>
                                <td class='cell100 column50 text-bluegrey'  id='totaltexto'>&nbsp;</td>
                                <td class='cell100 column15 leftt  text-bluegrey ' >CANT. PROD:</td>
                                <td class='cell100 column10 text-right text-danger' id='totcant'>0.00</td>
                                <td class="cell100 column15  leftt text-bluegrey "> SUMAS $:</td>
                                <td class='cell100 column10 text-right text-green' id='total_gravado'>0.00</td>

                              </tr>
                              <tr>
                                <td class="cell100 column15 leftt text-bluegrey ">GRAVADO $:</td>
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
                                <td class="cell100 column10 text-right text-green" > <input id='total_retencion' style="width:100%;height:24px;font-size:12px;" type="text" class="decimal text-right form-control" name="" value=""> </td>
                                <td class="cell100 column15 leftt text-bluegrey ">CARGO TARJETA $:</td>
                                <td class="cell100 column10  text-right text-green"  id='ct'>0.00</td>
                                <td class="cell100 column15 leftt  text-bluegrey">A PAGAR $:</td>
                                <td class="cell100 column10  text-right text-green"  id='monto_pago'>0.00</td>
                              </tr>
                              <tr hidden>
                                <td class="cell100 column15 leftt text-bluegrey ">DESCUENTO $:</td>
                                <td class="cell100 column10  text-right text-green"  id='total_final'>0.00</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="wrap-table1001">
                      <div class="table100 ver1 m-b-10">
                        <div class="table100-head">
                          <table id="inventable1">
                            <thead>
                              <tr class="row100 head">
                                <th class="success cell100 column100 text-center">PAGO Y CAMBIO</th>
                              </tr>
                            </thead>
                          </table>
                        </div>
                        <div class="table101-body">
                          <table>
                            <tbody>
                              <tr>
                                <td class='cell100 column70 text-success'>FECHA:</td>
                                <td class='cell100 column30'><input type="text" id="fecha_fact" class="txt_box2"  value="<?php echo date("d-m-Y"); ?>"></td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>CORRELATIVO:</td>
                                <td class='cell100 column30'><input autocomplete="new-password" type="text" id="corr_in" class="txt_box2"  value="" readOnly></td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>TOTAL: $</td>
                                <td class='cell100 column30'><input autocomplete="new-password" type="text" id="tot_fdo" class="txt_box2"   value="" readOnly></td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>NUM. DOCUMENTO: </td>
                                <td class='cell100 column30'><input autocomplete="new-password" type="text" id="numdoc" class="txt_box2"   value="" readOnly></td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>CLIENTE: </td>
                                <td class='cell100 column30'><input autocomplete="new-password" type="text" id="nomcli" class="txt_box2"  value="" readOnly></td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>DIRECCION: </td>
                                <td class='cell100 column30'><input autocomplete="new-password" type="text" id="dircli" class="txt_box2"  value="" readOnly></td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>NIT: </td>
                                <td class='cell100 column30'><input autocomplete="new-password" type="text" id="nitcli" class="txt_box2"    value="" readOnly></td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>NRC: </td>
                                <td class='cell100 column30'><input autocomplete="new-password" type="text" id="nrccli" class="txt_box2"   value="" readOnly></td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>EFECTIVO: $</td>
                                <td class='cell100 column30'> <input autocomplete="new-password" type="text" id="efectivov" readOnly class="txt_box2"   value=""> </td>
                              </tr>
                              <tr>
                                <td class='cell100 column70 text-success'>CAMBIO: $</td>
                                <td class='cell100 column30'><input autocomplete="new-password" type="text" id="cambiov" class="txt_box2"   value="" readOnly></td>
                              </tr>

                            </tbody>
                          </table>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
                <?php

                echo "<input type='hidden' name='id_empleado' id='id_empleado' >";
                echo "<input type='hidden' name='numero_doc' id='numero_doc' >";
                echo "<input type='hidden' name='id_factura' id='id_factura' >";
                echo "<input type='hidden' name='urlprocess' id='urlprocess' value='$filename'>"; ?>
                <input type='hidden' name='totalfactura' id='totalfactura' value='0'>
                <input type="hidden" id="imprimiendo" name="imprimiendo" value="0">

                <input type='hidden' name='id_apertura' id='id_apertura' value='<?php echo $id_apertura; ?>'>
                <input type='hidden' name='turno' id='turno' value='<?php echo $turno; ?>'>
                <input type='hidden' name='caja' id='caja' value='<?php echo $caja; ?>'>
              </div>
              <!--div class="table-responsive m-t"-->
            </section>

          </div>
          <!--div class='ibox-content'-->
          <!-- Modal -->
          <div class='modal fade' id='salidaModal' style="overflow:hidden;" role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
              <div class='modal-content modal-lg'></div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
          </div><!-- /.modal -->
          <div class='modal fade' id='viewModal2' style="overflow:hidden;" role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
              <div class='modal-content'></div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
          </div><!-- /.modal -->
          <div class='modal fade' id='busqueda' style="overflow:hidden;" role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
              <div class='modal-content modal-lg'></div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
          </div><!-- /.modal -->
          <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content modal-md">
                <div class="modal-header">
                  <h4 class="modal-title" id="myModalLabel">Pago y Cambio</h4>
                </div>
                <div class="modal-body">
                  <div class="wrapper wrapper-content  animated fadeInRight">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label><h5 class='text-navy'>Numero factura Interno:</h5></label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group" id='fact_num'></div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label><h5 class='text-navy'>Facturado $:</h5></label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="text" id="facturado" name="facturado" value=0 class="form-control decimal" readonly>
                        </div>
                      </div>
                    </div>

                    <div class="row" id='fact_cf'>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label><strong><h5 class='text-danger'>Num. Factura/ Credito Fiscal/ Nota de Envio: </h5></strong></label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="text" id='num_doc_fact' name='num_doc_fact' value='' class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row" id='ccf'>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label><strong><h5 class='text-navy'>Nombre de Cliente Credito Fiscal: </h5></strong></label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="text" id='nombreape' name='nombreape' value='' class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>NIT Cliente</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type='text' placeholder='NIT Cliente' class='form-control' id='nit' name='nit' value=''>
                        </div>
                      </div>


                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Registro Cliente(NRC)</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type='text' placeholder='Registro (NRC) Cliente' class='form-control' id='nrc' name='nrc' value=''>
                        </div>
                      </div>


                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Efectivo $</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="text" id="efectivo" name="efectivo" value="" class="form-control decimal" autofocus>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Cambio $</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <input type="text" id="cambio" name="cambio" value=0 placeholder="cambio" class="form-control decimal" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" id="btnPrintFact">Imprimir</button>
                  <button type="button" class="btn btn-warning" id="btnEsc">Salir</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-container">
            <div class="modal fade" id="clienteModal" tabindex="-2" role="dialog" aria-labelledby="myModalCliente" aria-hidden="true">
              <div class="modal-dialog model-sm">
                <div class="modal-content"> </div>
              </div>
            </div>
          </div>
        </div>
        <!--<div class='ibox float-e-margins' -->
      </div>
      <!--div class='col-lg-12'-->
      <!--div class='row'-->
      <!--div class='wrapper wrapper-content  animated fadeInRight'-->

      <?php
    }   //apertura de caja
    else {
      echo "<br><br><div class='alert alert-warning'><h3 class='text-danger'> No Hay Apertura de Caja vigente para este turno!!! aperture <a href='admin_corte.php'>aquí</a>  </h3></div></div></div></div></div>";
      include_once("footer.php");
    }  //apertura de caja
    include_once("footer.php");
    $r = rand(1,999);
    echo "<script src='js/funciones/venta.js?$r'></script>";
    echo "<script src='js/plugins/arrowtable/arrow-table.js'></script>";
    echo "<script src='js/plugins/bootstrap-checkbox/bootstrap-checkbox.js'></script>";
    echo '<script src="js/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="js/funciones/main.js"></script>';
    echo "<script src='js/funciones/util.js'></script>";
  } //permiso del script
  else {
    echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
    include_once("footer.php");
  }
}

function cargar_data()
{
  $id_sucursal = $_SESSION["id_sucursal"];
  $n_ref = $_POST["n_ref"];
  $fecha = date("Y-m-d");

  /////////////////////// FACTURA
  $sql_fact="SELECT factura.id_factura, factura.id_cliente,factura.id_empleado,
  factura.fecha,  factura.numero_doc, factura.tipo_documento
  FROM factura WHERE numero_ref = $n_ref AND fecha = '$fecha' AND finalizada != 1";
  //echo $sql_fact;
  $result_fact=_query($sql_fact);
  $count_fact=_num_rows($result_fact);

  if($count_fact > 0)
  {
    $row_fact=_fetch_array($result_fact);
    $fecha=$row_fact['fecha'];
    $id_factura = $row_fact["id_factura"];
    $numero_doc=$row_fact['numero_doc'];
    $alias_tipodoc = $row_fact["tipo_documento"];
    $id_empleado=$row_fact['id_empleado'];

    /////////////////////////CLIENTE
    $sql_cliente1="SELECT cliente.id_cliente,cliente.retiene,cliente.retiene10,factura.id_factura,factura.fecha,
    cliente.nombre
    FROM cliente,factura
    where id_factura='$id_factura'
    and factura.id_cliente=cliente.id_cliente
    ORDER BY cliente.nombre";
    //echo $sql_cliente1;
    $id_cliente=0;
    $nombre_cliente = "";
    $retencion1=0;
    $retencion10=0;

    $qcliente=_query($sql_cliente1);
    while ($row_cliente=_fetch_array($qcliente)) {
      $id_cliente=$row_cliente['id_cliente'];
      $nombre_cliente=$row_cliente['nombre'];
      if ($row_cliente['retiene']==1) {
        # code...
        $retencion1=0.01;
      }
      if ($row_cliente['retiene10']==1) {
        # code...
        $retencion1=0.1;
      }
    }
    //////////////DETALLE FACTURA
    $sql_fact_det="SELECT factura.id_factura, factura.id_cliente,factura.id_empleado,  factura.fecha, factura.numero_doc, factura.total,
    factura.id_usuario, factura.anulada, factura.id_usuario, factura.finalizada, factura.id_sucursal,
    factura_detalle.id_factura_detalle, factura_detalle.id_prod_serv,factura_detalle.cantidad,
    factura_detalle.precio_venta, factura_detalle.subtotal, factura_detalle.tipo_prod_serv,
    producto.descripcion, producto.id_producto,producto.id_categoria,producto.exento,factura_detalle.id_presentacion
    FROM factura
    JOIN factura_detalle  ON factura.id_factura=factura_detalle.id_factura
    JOIN producto  ON producto.id_producto=factura_detalle.id_prod_serv
    WHERE factura.id_factura='$id_factura'
    AND factura.id_sucursal='$id_sucursal'

    ";

    $result_fact_det=_query($sql_fact_det);
    $count_fact_det=_num_rows($result_fact_det);
    //echo $sql_fact_det;
    $total=0;
    $lista = "";
    for ($i=0;$i<$count_fact_det;$i++) {
      $row=_fetch_array($result_fact_det);
      $numero_doc=$row['numero_doc'];
      $id_factura=$row['id_factura'];
      $id_producto=$row['id_prod_serv'];
      $tipo_prod=$row['tipo_prod_serv'];
      $anulada=$row['anulada'];
      $cantidad=$row['cantidad'];
      $precio_venta=$row['precio_venta'];
      $subtotal=$row['subtotal'];
      $categoria=$row['id_categoria'];
      $total=$row['total'];
      $id_usuario=$row['id_usuario'];
      $id_empleado=$row['id_empleado'];
      $id_producto=$row['id_producto'];
      $id_pre = $row["id_presentacion"];
      $total=sprintf("%.2f", $total);
      $exento=$row['exento'];



      $sql_ss=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1 AND  presentacion_producto.id_sucursal=$id_sucursal");
      //echo "SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1";
      $y = 0;
      $unidadp = 0;
      $preciop = 0;
      $select_rank="<select class='sel_r form-control'>";
      $select="<select class='sel form-control'>";
      while ($rowx=_fetch_array($sql_ss)) {
        # code...
        if ($y==0) {
          # code...
          $unidadp=$rowx['unidad'];
          $preciop=$rowx['precio'];
          $descripcionp=$rowx['descripcion'];


          $xc=0;

          $sql_rank=_query("SELECT presentacion_producto_precio.id_prepd,presentacion_producto_precio.desde,presentacion_producto_precio.hasta,presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$row[id_presentacion] AND presentacion_producto_precio.precio>=$precio_venta  AND presentacion_producto_precio.id_sucursal=$_SESSION[id_sucursal] ORDER BY presentacion_producto_precio.desde ASC
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
          $select.="<option value='$rowx[id_presentacion]'";
          if($id_pre == $rowx["id_presentacion"])
          {
            $select.="selected";
          }
          $select.=">$rowx[nombre]</option>";
          $y=$y+1;

        }
        $select.="</select>";
        $sql_cc = _query("SELECT * FROM presentacion_producto WHERE id_presentacion = '$id_pre'");
        $roq = _fetch_array($sql_cc);
        $unidadq=$roq['unidad'];
        $precioq=$roq['precio'];
        $descripcionq=$roq['descripcion'];
        $cc = $cantidad / $unidadq;
        //$unidades=round($row['unidad'], 2);

        //$id_posicion=$row['id_posicion'];
        $descripcion=$row['descripcion'];
        //$id_marca=$row['id_marca'];

        $sql_s = _fetch_array(_query("SELECT p.id_sucursal,SUM(su.cantidad) as stock FROM producto AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto ='$id_producto' AND u.bodega=0 AND su.id_sucursal=$id_sucursal"));
        $stock_r=$sql_s['stock'];

        $hoy=date("Y-m-d");
        $sql_res_pre=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reserva FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_sucursal=$id_sucursal AND factura.fecha = '$hoy' AND factura.finalizada=0 "));
        $reserva=$sql_res_pre['reserva'];

        $sql_res_esto=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reservado FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_factura=$id_factura"));
        $reservado=$sql_res_esto['reservado'];


        $existencias=$stock_r+$reservado-$reserva;

        $descprod=$descripcion;
        //$ubica=ubicacionn($id_posicion);
        $ubicacion="";

        if ($existencias<=$cantidad) {
          $existencias=$cantidad;
        }
        $sqkl=_fetch_array(_query("SELECT iva FROM sucursal WHERE id_sucursal=$id_sucursal"));
        $iva=$sqkl['iva']/100;
        $iva=1+$iva;

        $descripcion.=$ubicacion;
        $lista.= "<tr class='row100 head'>";
        $lista.= "<td hidden class='cell100 column10 text-success id_pps'><input type='hidden' id='unidades' name='unidades' value='" . $unidadq . "'>".$id_producto."</td>";
        $lista.= "<td class='cell100 column30 text-success'>".$descripcion."<input type='hidden' id='exento' name='exento' value='".$exento."'>"."</td>";


        $lista.= "<td class='cell100 column10 text-success' id='cant_stock'>".$existencias."</td>";
        $lista.= "<td class='cell100 column10 text-success'><div class='col-xs-2'><input type='text'  class='form-control decimal $categoria cant' id='cant' name='cant' value=".$cc." style='width:60px;'></div></td>";

        $lista.= "<td class='cell100 column10 text-success preccs'>".$select."</td>";
        $lista.= "<td class='cell100 column10 text-success descp'>"."<input type'text' id='dsd' value='" . $descripcionp. "' class='form-control' readonly>"."</td>";
        $lista.= "<td class='cell100 column10 text-success rank_s'>".  $select_rank . "</td>";
        $lista.= "<td hidden class='cell100 column10 text-success'><input type='hidden'  id='precio_venta_inicial' name='precio_venta_inicial' value='".$precio_venta."' ><input type='hidden'  id='precio_sin_iva' name='precio_sin_iva' value='" . round(($precio_venta/$iva),8,PHP_ROUND_HALF_DOWN) . "'><input type='text'  class='form-control decimal' readOnly  id='precio_venta' name='precio_venta' value='".$precio_venta."' style='width:80px;'></td>";

        $lista.= "<td class='ccell100 column10'>"."<input type='hidden'  id='subtotal_fin' name='subtotal_fin' value='".$subtotal."'>" . "<input type='text'  class='decimal form-control' id='subtotal_mostrar' name='subtotal_mostrar'  value='" . round($subtotal,2) . "'readOnly>"."</td>";
        $lista.= "<td class='cell100 column10 Delete text-center'><input id='delprod' type='button' class='btn btn-danger fa'  value='&#xf1f8;'></td>";
        $lista.= "</tr>";
      }
      $select_vendedor="";
      $sqlemp=_query("SELECT id_empleado, nombre FROM empleado WHERE id_sucursal='$id_sucursal' AND id_tipo_empleado=2");
      while($row_emp = _fetch_array($sqlemp))
      {
        if ($row_emp["id_empleado"]==$id_empleado) {
          $select_vendedor .= "<option value='".$row_emp["id_empleado"]."' selected>".$row_emp["nombre"]."</option>";
        }
        else {
          $select_vendedor .= "<option value='".$row_emp["id_empleado"]."'>".$row_emp["nombre"]."</option>";
        }

      }

      $select_cliente="";
      $select_cliente="<option value=''>Seleccione</option>";
      $sqlcli=_query("SELECT * FROM cliente WHERE id_sucursal='$id_sucursal' ORDER BY nombre");
      while($row_cli = _fetch_array($sqlcli))
      {
        if ($row_cli["id_cliente"]==$id_cliente) {
          # code...
          $select_cliente.= "<option value='".$row_cli["id_cliente"]."' selected>".$row_cli["nombre"]."</option>";
        }
        else
        {
          $select_cliente.= "<option value='".$row_cli["id_cliente"]."'>".$row_cli["nombre"]."</option>";
        }

      }

      $select_tipo_impresion="";

      if ("TIK"==$alias_tipodoc)
      {
        # code...
        $select_tipo_impresion.="<option value='TIK' selected>COBRO</option>";
        $select_tipo_impresion.="<option value='TIK'>TICKET</option>";
      }
      else {
        # code...
        $select_tipo_impresion.="<option value='TIK'>COBRO</option>";
        $select_tipo_impresion.="<option value='TIK'>TICKET</option>";
      }

      if ("COF"==$alias_tipodoc) {
        # code...
        $select_tipo_impresion.="<option value='COF' selected>FACTURA CONSUMIDOR FINAL</option>";
      }
      else {
        # code...
        $select_tipo_impresion.="<option value='COF'>FACTURA CONSUMIDOR FINAL</option>";
      }

      if ("CCF"==$alias_tipodoc) {
        # code...
        $select_tipo_impresion.="<option value='CCF' selected  >CREDITO FISCAL</option>";
      }
      else {
        # code...
        $select_tipo_impresion.="<option value='CCF'>CREDITO FISCAL</option>";
      }





      $xdatos['typeinfo'] = "Success";
      $xdatos['msg'] = "";
      $xdatos['id_cliente'] = $id_cliente;
      $xdatos['select_cliente'] = $select_cliente;
      $xdatos['select_tipo_impresion'] = $select_tipo_impresion;
      $xdatos['select_vendedor'] = $select_vendedor;
      $xdatos['nombre_cliente'] = $nombre_cliente;
      $xdatos['alias_tipodoc'] = $alias_tipodoc;
      $xdatos['lista'] = $lista;
      $xdatos['id_empleado'] = $id_empleado;
      $xdatos['numero_doc'] = $numero_doc;
      $xdatos['id_factura'] = $id_factura;
      $xdatos['retencion1']= $retencion1;
      $xdatos['retencion10']= $retencion10;
    }
    else
    {
      $xdatos['typeinfo'] = "Error";
      $xdatos['msg'] = "No se encontro documento";
      $xdatos['id_cliente'] = "";
      $xdatos['nombre_cliente'] = "";
      $xdatos['alias_tipodoc'] = "";
      $xdatos['lista'] = "";
      $xdatos['id_empleado'] = "";
      $xdatos['numero_doc'] = "";
      $xdatos['id_factura'] = "";
      $xdatos['retencion1']= 0;
      $xdatos['retencion10']= 0;
    }
    echo json_encode($xdatos);
  }
  function consultar_stock()
  {
    $id_producto = $_REQUEST['id_producto'];
    $id_usuario=$_SESSION["id_usuario"];
    $r_precios=_fetch_array(_query("SELECT precios FROM usuario WHERE id_usuario=$id_usuario"));
    $precios=$r_precios['precios'];
    $limit="";
    if ($precios==0) {
      $limit="LIMIT 1";
    }
    $id_sucursal=$_SESSION['id_sucursal'];
    $id_factura=$_REQUEST['id_factura'];
    $precio=0;
    $categoria="";

    $sql1 = "SELECT p.id_producto,p.id_categoria, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal,SUM(su.cantidad) as stock FROM producto AS p JOIN stock_ubicacion as su ON su.id_producto=p.id_producto JOIN ubicacion as u ON u.id_ubicacion=su.id_ubicacion  WHERE  p.id_producto ='$id_producto' AND u.bodega=0 AND su.id_sucursal=$id_sucursal";
    $stock1=_query($sql1);
    $row1=_fetch_array($stock1);
    $nrow1=_num_rows($stock1);
    if ($nrow1>0)
    {
      $hoy=date("Y-m-d");
      $perecedero=$row1['perecedero'];
      $barcode = $row1["barcode"];
      $descripcion = $row1["descripcion"];
      $estado = $row1["estado"];
      $perecedero = $row1["perecedero"];
      $exento = $row1["exento"];
      $categoria=$row1['id_categoria'];
      $sql_res_pre=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reserva FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_sucursal=$id_sucursal AND factura.fecha = '$hoy' AND factura.finalizada=0 "));
      $reserva=$sql_res_pre['reserva'];

      $sql_res_esto=_fetch_array(_query("SELECT SUM(factura_detalle.cantidad) as reservado FROM factura JOIN factura_detalle ON factura_detalle.id_factura=factura.id_factura WHERE factura_detalle.id_prod_serv=$id_producto AND factura.id_factura=$id_factura"));
      $reservado=$sql_res_esto['reservado'];


      $stock= $row1["stock"]-$reserva+$reservado;
      if($stock<0)
      {
        $stock=0;
      }

      $i=0;
      $unidadp=0;
      $preciop=0;
      $descripcionp=0;
      $select_rank="<select class='sel_r form-control'>";
      $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1 AND presentacion_producto.id_sucursal=$id_sucursal ORDER BY presentacion_producto.unidad ASC");
      $select="<select class='sel form-control'>";
      while ($row=_fetch_array($sql_p))
      {
        if ($i==0)
        {
          $unidadp=$row['unidad'];
          $preciop=$row['precio'];
          $descripcionp=$row['descripcion'];

          $xc=0;

          $sql_rank=_query("SELECT presentacion_producto_precio.id_prepd,presentacion_producto_precio.desde,presentacion_producto_precio.hasta,presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$row[id_presentacion] AND presentacion_producto_precio.id_sucursal=$_SESSION[id_sucursal] AND presentacion_producto_precio.precio!=0 ORDER BY presentacion_producto_precio.desde ASC $limit
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
            $select_rank.="<option value='0.0'>0.0</option>";
            $select_rank.="</select>";
          }
          $select.="<option value='$row[id_presentacion]'>$row[nombre]</option>";
          $i=$i+1;
        }


        $select.="</select>";
        $xdatos['perecedero']=$perecedero;
        $xdatos['descripcion']= $descripcion;
        $xdatos['select']= $select;
        $xdatos['select_rank']= $select_rank;
        $xdatos['stock']= $stock;
        $xdatos['preciop']= $preciop;

        $sql_e=_fetch_array(_query("SELECT exento FROM producto WHERE id_producto=$id_producto"));
        $exento=$sql_e['exento'];
        if ($exento==1) {
          # code...
          $xdatos['preciop_s_iva']=$preciop;
        }
        else {
          # code...
          $sqkl=_fetch_array(_query("SELECT iva FROM sucursal WHERE id_sucursal=$id_sucursal"));
          $iva=$sqkl['iva']/100;
          $iva=1+$iva;
          $xdatos['preciop_s_iva']= round(($preciop/$iva),8,PHP_ROUND_HALF_DOWN);
        }
        $xdatos['unidadp']= $unidadp;
        $xdatos['descripcionp']= $descripcionp;
        $xdatos['exento']=$exento;
        $xdatos['categoria']=$categoria;

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
      $cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
      echo $cadena_salida;
    }

    function numero_tiquete($ult_doc, $tipo)
    {
      $ult_doc=trim($ult_doc);
      $len_ult_valor=strlen($ult_doc);
      $long_num_fact=10;
      $long_increment=$long_num_fact-$len_ult_valor;
      $valor_txt="";
      if ($len_ult_valor<$long_num_fact) {
        for ($j=0;$j<$long_increment;$j++) {
          $valor_txt.="0";
        }
      } else {
        $valor_txt="";
      }
      $valor_txt=$valor_txt.$ult_doc."_".$tipo;
      return $valor_txt;
    }

    function insertar()
    {
      //date_default_timezone_set('America/El_Salvador');
      $fecha_movimiento= $_POST['fecha_movimiento'];

      $id_cliente=$_POST['id_cliente'];
      $id_factura=$_POST['id_factura'];

      $id_vendedor=$_POST['id_vendedor'];
      $cuantos = $_POST['cuantos'];
      $array_json=$_POST['json_arr'];
      $fecha=date("Y-m-d");
      $fecha_movimiento=$fecha;
      //  IMPUESTOS
      $total_percepcion= $_POST['total_percepcion'];

      $subtotal=$_POST['subtotal'];
      $sumas=$_POST['sumas'];
      $suma_gravada=$_POST['suma_gravada'];
      $iva= $_POST['iva'];
      $retencion= $_POST['retencion'];
      $venta_exenta= $_POST['venta_exenta'];
      $total_menos_retencion=$_POST['total'];
      $total = $retencion+$_POST['total'];

      $id_empleado=$_SESSION["id_usuario"];
      if($id_vendedor == "")
      {
        $id_vendedor = $id_empleado;
      }
      $id_sucursal=$_SESSION["id_sucursal"];
      $fecha_actual = date('Y-m-d');
      $tipoprodserv = "PRODUCTO";
      $cargo_tarjeta=$_POST['cargo_tarjeta'];
      $credito=$_POST['credito'];
      $pago_targeta=0;

      if ($credito==2) {
        // code...
        $pago_targeta=1;
        $credito==0;
      }


      $id_apertura=$_POST['id_apertura'];
      $turno=$_POST['turno'];
      $caja=$_POST['caja'];
      $tipo_documento=$_POST['tipo_impresion'];
      $tipo_impresion=$tipo_documento;

      $insertar_fact=false;
      $insertar_fact_dett=true;
      $insertar_numdoc =false;

      $hora=date("H:i:s");
      $xdatos['typeinfo']='';
      $xdatos['msg']='';
      $xdatos['process']='';



      _begin();

      $a=1;
      $b=1;
      $c=1;
      $z=1;
      $j = 1 ;
      $k = 1 ;
      $l = 1 ;


      $tipo_entrada_salida='';

      $sql="select * from correlativo WHERE id_sucursal=$id_sucursal";
      $result= _query($sql);
      $rows=_fetch_array($result);
      $nrows=_num_rows($result);
      $ult_ccf=$rows['ccf']+1;
      $ult_cof=$rows['cof']+1;

      $numero_doc="";
      $num_fact_impresa='';

      $table_numdoc="correlativo";
      $data_numdoc="";

      if ($tipo_impresion =='COF') {
        $tipo_entrada_salida='FACTURA CONSUMIDOR';
        $data_numdoc = array(
          'cof' => $ult_cof
        );
        $numero_doc=numero_tiquete($ult_cof, $tipo_impresion);
      }
      if ($tipo_impresion =='TIK') {
        $sql_corre = _query("SELECT * FROM caja WHERE id_caja = '$caja'");
        $row_corre = _fetch_array($sql_corre);
        $correlativo_dispo = $row_corre["correlativo_dispo"];
        $tipo_entrada_salida='TICKET';
        $data_numdoc = array(
          'correlativo_dispo' => $correlativo_dispo+1,
        );
        $num_fact_impresa=$correlativo_dispo;
        $numero_doc=numero_tiquete($correlativo_dispo, $tipo_impresion);
      }
      if ($tipo_impresion =='CCF') {
        $tipo_entrada_salida='CREDITO FISCAL';
        $data_numdoc = array(
          'ccf' => $ult_ccf
        );
        $numero_doc=numero_tiquete($ult_ccf, $tipo_impresion);
      }

      if($tipo_impresion != "TIK")
      {
        $where_clause_n=" WHERE id_sucursal='$id_sucursal'";
        $insertar_numdoc = _update($table_numdoc, $data_numdoc, $where_clause_n);
      }
      else
      {
        $tab = 'caja';
        $where_clause_c=" WHERE id_caja='$caja'";
        $insertar_numdoc = _update($tab, $data_numdoc, $where_clause_c);
      }


      $abono=0;
      $saldo=0;

      $serie="";
      $ultimo=0;

      if ($tipo_impresion == "TIK") {
        # code...
        $sql_corre = _query("SELECT * FROM caja WHERE id_caja = '$caja'");
        $row_corre = _fetch_array($sql_corre);
        $serie = $row_corre["serie"];

      }
      elseif ($tipo_impresion == "COF") {
        # code...
        $swl =_fetch_array(_query("SELECT * FROM sucursal where id_sucursal=$id_sucursal "));
        $serie=$swl['serie_cof'];


        $sql_ult=_query("SELECT MAX(CONVERT(num_fact_impresa,UNSIGNED INTEGER)) as ultimo FROM factura WHERE id_sucursal=$id_sucursal AND tipo_documento='COF' ");

        $num_rows_ul=_num_rows($sql_ult);
        if ($num_rows_ul>0) {
          # code...
          $ul=_fetch_array($sql_ult);
          $ultimo=$ul['ultimo'];
        }

      }
      else {
        # code...
        $swl =_fetch_array(_query("SELECT * FROM sucursal where id_sucursal=$id_sucursal "));
        $serie=$swl['serie_ccf'];

        $sql_ult=_query("SELECT MAX(CONVERT(num_fact_impresa,UNSIGNED INTEGER)) as ultimo FROM factura WHERE id_sucursal=$id_sucursal AND tipo_documento='CCF' ");

        $num_rows_ul=_num_rows($sql_ult);
        if ($num_rows_ul>0) {
          # code...
          $ul=_fetch_array($sql_ult);
          $ultimo=$ul['ultimo'];
        }

      }





      if($credito==1)
      {
        $saldo=$total_menos_retencion;
      }
      $id_fact="";
      if ($id_factura=="") {
        # code...
        $table_fact= 'factura';
        $form_data_fact = array(
          'id_cliente' => $id_cliente,
          'fecha' => $fecha_movimiento,
          'numero_doc' => $numero_doc,
          'subtotal' => $subtotal,
          'sumas'=>$sumas,
          'suma_gravado'=>$suma_gravada,
          'iva' =>$iva,
          'retencion'=>$retencion,
          'venta_exenta'=>$venta_exenta,
          'total_menos_retencion'=>round($total_menos_retencion-$cargo_tarjeta,4),
          'total' => round($total-$cargo_tarjeta,4),
          'id_usuario'=>$id_empleado,
          'id_empleado' => $id_vendedor,
          'id_sucursal' => $id_sucursal,
          'tipo' => $tipo_entrada_salida,
          'serie' => $serie,
          'num_fact_impresa' => $num_fact_impresa,
          'hora' => $hora,
          'finalizada' => '1',
          'abono'=>$abono,
          'saldo' => $saldo,
          'tipo_documento' => $tipo_documento,
          'id_apertura' => $id_apertura,
          'id_apertura_pagada' => $id_apertura,
          'caja' => $caja,
          'credito' => $credito,
          'turno' => $turno,
          'cargo_tarjeta' => $cargo_tarjeta,
          'pago_tarjeta' => $pago_targeta,
        );
        $insertar_fact = _insert($table_fact,$form_data_fact );
        $id_fact= _insert_id();

        if (!$insertar_fact) {
          # code...
          $b=0;
        }
      }
      else {
        # code...
        $table_fact= 'factura';
        $form_data_fact = array(
          'id_cliente' => $id_cliente,
          'fecha' => $fecha_movimiento,
          'numero_doc' => $numero_doc,
          'subtotal' => $subtotal,
          'sumas'=>$sumas,
          'suma_gravado'=>$suma_gravada,
          'iva' =>$iva,
          'retencion'=>$retencion,
          'venta_exenta'=>$venta_exenta,
          'total_menos_retencion'=>round($total_menos_retencion-$cargo_tarjeta,4),
          'total' => round($total-$cargo_tarjeta,4),
          'id_usuario'=>$id_empleado,
          'id_empleado' => $id_vendedor,
          'id_sucursal' => $id_sucursal,
          'tipo' => $tipo_entrada_salida,
          'serie' => $serie,
          'num_fact_impresa' => $num_fact_impresa,
          'hora' => $hora,
          'finalizada' => '1',
          'abono'=>$abono,
          'saldo' => $saldo,
          'tipo_documento' => $tipo_documento,
          'id_apertura' => $id_apertura,
          'id_apertura_pagada' => $id_apertura,
          'caja' => $caja,
          'credito' => $credito,
          'turno' => $turno,
          'cargo_tarjeta' => $cargo_tarjeta,
          'pago_tarjeta' => $pago_targeta,
        );
        $whereclause="id_factura='".$id_factura."'";
        $insertar_fact = _update($table_fact,$form_data_fact,$whereclause );
        $id_fact= $id_factura;

        if (!$insertar_fact) {
          # code...
          $b=0;
        }
        $table="factura_detalle";
        $where_clause="id_factura='".$id_fact."'";
        $delete=_delete($table,$where_clause);
        if (!$delete) {
          # code...
          $b=0;
        }

      }

      $cre=1;
      if($credito==1)
      {
        $table="credito";
        $form_data = array(
          'id_cliente' => $id_cliente,
          'fecha' => $fecha_movimiento,
          'tipo_doc' => $tipo_documento,
          'numero_doc' => $numero_doc,
          'id_factura' => $id_fact,
          'dias' =>  '30',
          'total' => $total_menos_retencion,
          'abono' => 0,
          'saldo' => $total_menos_retencion,
          'finalizada' => 0,
          'id_sucursal' => $id_sucursal,
        );
        $insert=_insert($table,$form_data);
        if ($insert) {
          # code...
        }
        else {
          # code...
          $cre=0;
        }
      }

      $table='movimiento_producto';
      $form_data = array(
        'id_sucursal' => $id_sucursal,
        'correlativo' => $numero_doc,
        'concepto' => "VENTA",
        'total' => $total,
        'tipo' => 'SALIDA',
        'proceso' => $tipo_documento,
        'referencia' => $numero_doc,
        'id_empleado' => $id_empleado,
        'fecha' => $fecha,
        'hora' => $hora,
        'id_suc_origen' => $id_sucursal,
        'id_suc_destino' => $id_sucursal,
        'id_proveedor' => 0,
        'id_factura' => $id_fact,
      );
      $insert_mov =_insert($table,$form_data);
      $x=1;
      if ($insert_mov) {
        # code...
      }
      else {
        # code...
        $x=0;
      }

      $id_movimiento=_insert_id();

      if ($cuantos>0)
      {
        $array = json_decode($array_json, true);
        foreach ($array as $fila)
        {
          /*$id_producto,$precio_compra,$precio_venta,$cantidad,$unidades,$fecha_caduca,$id_presentacion*/

          $id_producto=$fila['id'];
          $unidades=$fila['unidades'];
          $subtotal=$fila['subtotal'];
          $cantidad=$fila['cantidad'];
          $id_presentacion=$fila['id_presentacion'];
          $cantidad_real=$cantidad*$unidades;
          $exento=$fila['exento'];
          $precio_venta=$fila['precio'];

          $sql_costo=_fetch_array(_query("SELECT costo FROM presentacion_producto WHERE id_presentacion = $id_presentacion"));
          $precio_compra=$sql_costo['costo'];
          $table_fact_det= 'factura_detalle';
          $data_fact_det = array(
            'id_factura' => $id_fact,
            'id_prod_serv' => $id_producto,
            'cantidad' => $cantidad_real,
            'precio_venta' => $precio_venta,
            'subtotal' => $subtotal,
            'tipo_prod_serv' => $tipoprodserv,
            'id_empleado' => $id_empleado,
            'id_sucursal' => $id_sucursal,
            'fecha' => $fecha_movimiento,
            'id_presentacion'=> $id_presentacion,
            'exento' => $exento,
          );
          $insertar_fact_det = _insert($table_fact_det,$data_fact_det );
          if (!$insertar_fact_det) {
            # code...
            $c=0;
          }

          $id_producto;
          $cantidad=$cantidad*$unidades;
          $a_transferir=$cantidad;

          $orig=_fetch_array(_query("SELECT ubicacion.id_ubicacion FROM ubicacion WHERE ubicacion.bodega=0 AND ubicacion.id_sucursal=$id_sucursal"));
          $origen=$orig['id_ubicacion'];

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
                'cantidad' => $transfiriendo,
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
          $nrow2=_num_rows($stock2);
          if ($nrow2>0)
          {
            $row2=_fetch_array($stock2);
            $existencias=$row2['stock'];
          }
          else
          {
            $existencias=0;
          }

          /*significa que no hay suficientes unidades en el stock_ubicacion para realizar el descargo*/
          if ($a_transferir>0) {
            /*verificamos si se desconto algo de stock_ubicacion*/

            if($a_transferir!=$cantidad)
            {/*si entra aca significa que se descontaron algunas unidades de stock_ubicacion y hay que descontarlas de stock y lote*/
              /*se insertara la diferencia entre el stock_ubicacion y la cantidad a descontar en la tabla de movimientos pendientes*/
              $table1= 'movimiento_producto_detalle';
              $cant_total=$existencias-($cantidad-$a_transferir);
              $form_data1 = array(
                'id_movimiento'=>$id_movimiento,
                'id_producto' => $id_producto,
                'cantidad' => ($cantidad-$a_transferir),
                'costo' => $precio_compra,
                'precio' => $precio_venta,
                'stock_anterior'=>$existencias,
                'stock_actual'=>$cant_total,
                'lote' => 0,
                'id_presentacion' => $id_presentacion,
                'fecha' =>  $fecha,
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
                $form_data2 = array(
                  'id_producto' => $id_producto,
                  'stock' => 0,
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
              $cantidad_a_descontar=($cantidad-$a_transferir);
              $sql=_query("SELECT id_lote, id_producto, fecha_entrada, vencimiento, cantidad
                FROM lote
                WHERE id_producto='$id_producto'
                AND id_sucursal='$id_sucursal'
                AND cantidad>0
                AND estado='VIGENTE'
                ORDER BY vencimiento");


                $contar=_num_rows($sql);
                $insert=1;
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

                $table1= 'movimiento_producto_pendiente';
                $cant_total=$existencias-$cantidad;
                $form_data1 = array(
                  'id_movimiento'=>$id_movimiento,
                  'id_producto' => $id_producto,
                  'id_presentacion' => $id_presentacion,
                  'cantidad' => $a_transferir,
                  'costo' => $precio_compra,
                  'precio' => $precio_venta,
                  'fecha' =>  $fecha,
                  'hora' => $hora,
                  'id_sucursal' => $id_sucursal
                );
                $insert_mov_det = _insert($table1,$form_data1);
                if(!$insert_mov_det)
                {
                  $j = 0;
                }

              }
              else
              {/*significa que no hay nada en stock_ubicacion y no se puede descontar de stock_ubicacion ni de stock*/
                /*se insertara todo en la tabla de movimientos pendientes*/

                $table1= 'movimiento_producto_pendiente';
                $cant_total=$existencias-$cantidad;
                $form_data1 = array(
                  'id_movimiento'=>$id_movimiento,
                  'id_producto' => $id_producto,
                  'id_presentacion' => $id_presentacion,
                  'cantidad' => $cantidad,
                  'costo' => $precio_compra,
                  'precio' => $precio_venta,
                  'fecha' =>  $fecha,
                  'hora' => $hora,
                  'id_sucursal' => $id_sucursal
                );
                $insert_mov_det = _insert($table1,$form_data1);
                if(!$insert_mov_det)
                {
                  $j = 0;
                }
              }
            }

            /*Hay suficientes unidades en  el stock_ubicacion para realizar el descargo y se procede normalmente*/
            else {

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
                'fecha' =>  $fecha,
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
                $insert=1;
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


            } //foreach ($array as $fila){
              if ($a&&$b&&$c&&$x&&$z&&$k&&$j&&$l&&$cre)
              {
                _commit(); // transaction is committed
                $xdatos['typeinfo']='Success';
                $xdatos['msg']='Registro  Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
                $xdatos['numdoc']=$numero_doc;
                $xdatos['id_factura']=$id_fact;
                $xdatos['ultimo']=$ultimo+1;
              }
              else
              {
                _rollback(); // transaction rolls back
                $xdatos['typeinfo']='Error';
                $xdatos['msg']='Registro no pudo ser ingresado!'.$a."-".$b."-".$c."-".$x."-".$z."-".$k."-".$j."-".$l;
              }
            }
            echo json_encode($xdatos);
          }

              function imprimir_fact()
              {
                $numero_doc = $_POST['numero_doc'];
                $tipo_impresion= $_POST['tipo_impresion'];
                $id_factura= $_POST['num_doc_fact'];
                $id_sucursal=$_SESSION['id_sucursal'];
                $numero_factura_consumidor = $_POST['numero_factura_consumidor'];
                $direccion=$_POST['direccion'];
                $fecha_fact=MD($_POST['fecha_fact']);

                $nombreape= $_POST['nombreape'];
                if ($tipo_impresion=='COF') {
                  $tipo_entrada_salida="FACTURA CONSUMIDOR";
                }
                if ($tipo_impresion=='TIK') {
                  $tipo_entrada_salida="TICKET";
                }
                if ($tipo_impresion=='CCF') {
                  $tipo_entrada_salida="CREDITO FISCAL";
                  $nit= $_POST['nit'];
                  $nrc= $_POST['nrc'];
                }
                //Valido el sistema operativo y lo devuelvo para saber a que puerto redireccionar
                $info = $_SERVER['HTTP_USER_AGENT'];
                if (strpos($info, 'Windows') == true) {
                  $so_cliente='win';
                } else {
                  $so_cliente='lin';
                }

                $sql_fact="SELECT * FROM factura WHERE id_factura='$id_factura'";
                $result_fact=_query($sql_fact);
                $nrows_fact=_num_rows($result_fact);
                if ($nrows_fact>0)
                {
                  $dats_ft = _fetch_array($result_fact);
                  $fecha_ant = $dats_ft["fecha"];
                  if($fecha_fact == "")
                  {
                    $fecha_fact = $fecha_ant;
                  }
                  $table_fact= 'factura';

                  if ($tipo_impresion=='TIK')
                  {
                    $form_data_fact = array(
                      'impresa' => '1',
                      'fecha' => $fecha_fact,
                    );
                    $where_clause="id_factura='$id_factura'";
                    $actualizar = _update($table_fact, $form_data_fact, $where_clause);
                  }
                  else
                  {
                    # code...
                    $form_data_fact = array(
                      'impresa' => '1',
                      'fecha' => $fecha_fact,
                      'num_fact_impresa'=>$numero_factura_consumidor,
                      'nombre' => $nombreape,
                      'direccion' => $direccion,
                    );
                    $where_clause="id_factura='$id_factura'";
                    $actualizar = _update($table_fact, $form_data_fact, $where_clause);
                  }

                }
                //cambiar numero documento impreso para mostrar en reporte kardex
                /*$where_clause1="WHERE
                tipo_entrada_salida='$tipo_entrada_salida'
                AND numero_doc='$numero_doc'
                AND fecha_movimiento='$fecha_movimiento'
                ";

                $table1= 'movimiento_producto';
                $form_data1 = array(
                'numero_doc'=>$id_factura,
              );
              $insertar1 = _update($table1, $form_data1, $where_clause1);*/

              if ($tipo_impresion=='COF') {
                $info_facturas=print_fact($id_factura, $tipo_impresion,$nombreape,$direccion);
              }
              if ($tipo_impresion=='ENV') {
                $info_facturas=print_envio($id_factura, $tipo_impresion);
              }

              if ($tipo_impresion=='CCF') {
                $info_facturas=print_ccf($id_factura, $tipo_impresion, $nit, $nrc, $nombreape,$direccion);
              }
              //directorio de script impresion cliente
              $headers="";
              $footers="";
              if ($tipo_impresion=='TIK') {
                $info_facturas=print_ticket($id_factura, $tipo_impresion);
                $sql_pos="SELECT *  FROM sucursal  WHERE id_sucursal='$id_sucursal' ";
                $result_pos=_query($sql_pos);
                $row1=_fetch_array($result_pos);
                $headers=$row1['descripcion']."|".Mayu($row1['direccion'])."|".$row1['giro']."|";
                $footers="GRACIAS POR SU COMPRA, VUELVA PRONTO......"."|";
              }

              $sql_dir_print="SELECT *  FROM config_dir WHERE id_sucursal='$id_sucursal'";
              $result_dir_print=_query($sql_dir_print);
              $row_dir_print=_fetch_array($result_dir_print);
              $dir_print=$row_dir_print['dir_print_script'];
              $shared_printer_win=$row_dir_print['shared_printer_matrix'];
              $shared_printer_pos=$row_dir_print['shared_printer_pos'];
              $nreg_encode['shared_printer_win'] =$shared_printer_win;
              $nreg_encode['shared_printer_pos'] =$shared_printer_pos;
              $nreg_encode['dir_print'] =$dir_print;
              $nreg_encode['facturar'] =$info_facturas;
              $nreg_encode['sist_ope'] =$so_cliente;
              $nreg_encode['headers'] =$headers;
              $nreg_encode['footers'] =$footers;

              echo json_encode($nreg_encode);
            }
            function agregar_cliente()
            {
              //$id_cliente=$_POST["id_cliente"];
              $nombre=$_POST["nombress"];
              $dui=$_POST["dui"];
              $tel1=$_POST["tel1"];
              $tel2=$_POST["tel2"];


              $var1=preg_match('/\x{27}/u', $nombre);
              $var2=preg_match('/\x{22}/u', $nombre);
              if ($var1==true || $var2==true) {
                $nombre =stripslashes($nombre);
              }
              $sql_result=_query("SELECT * FROM cliente WHERE nombre='$nombre'");
              $numrows=_num_rows($sql_result);
              $row_update=_fetch_array($sql_result);
              $id_cliente=$row_update["id_cliente"];
              $name_cliente=$row_update["nombre"];


              //'id_cliente' => $id_cliente,
              $table = 'cliente';
              $form_data = array(
                'nombre' => $nombre,
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
            function mostrar_datos_cliente()
            {
              $id_cliente=$_POST['id_client'];

              $sql="SELECT * FROM cliente
              WHERE
              id_cliente='$id_cliente'";
              $result=_query($sql);
              $count=_num_rows($result);
              if ($count > 0) {
                for ($i = 0; $i < $count; $i ++) {
                  $row = _fetch_array($result);
                  $id_cliente=$row["id_cliente"];
                  $nombre=$row["nombre"];
                  $apellido="";
                  $nit=$row["nit"];
                  $dui=$row["dui"];
                  $direccion=$row["direccion"];
                  $telefono1=$row["telefono1"];
                  $giro=$row["giro"];
                  $registro=$row["nrc"];

                }
              }
              $xdatos['nit']= $nit;
              $xdatos['registro']= $registro;
              $xdatos['nombreape']=   $nombre." ".$apellido;
              echo json_encode($xdatos); //Return the JSON Array
            }
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            function consultar_stock1(){
              $id_producto = $_REQUEST['id_producto'];
              $id_usuario=$_SESSION["id_usuario"];
              $id_sucursal=$_SESSION['id_sucursal'];


              $iva=13/100;
              $precio=0;

              //if ($tipo =='PRODUCTO'){
              //ojo !!!!!!!!!!!!!!!!!!!!!!
              //utilidad teneindo precio venta y costo  : utlidad=(precio_venta-costo)/costo;
              /*$sql1="SELECT producto.id_producto,producto.descripcion,producto.unidad,producto.exento,producto.id_posicion,
              producto.utilidad_activa,producto.utilidad_seleccion,producto.porcentaje_utilidad1,producto.descripcion,
              producto.porcentaje_utilidad2,producto.porcentaje_utilidad3,
              producto.porcentaje_utilidad4,producto.imagen,producto.combo,producto.perecedero,
              stock.stock,stock.costo_promedio,
              stock.utilidad, stock.pv_base, stock.precio_mayoreo,  stock.porc_desc_base , stock.stock_minimo,
              stock.pv_desc_base ,  stock.porc_desc_max ,  stock.pv_desc_max,
              stock.precio_oferta,stock.fecha_ini_oferta,stock.fecha_fin_oferta
              FROM producto JOIN stock ON producto.id_producto=stock.id_producto
              WHERE producto.id_producto='$id_producto'
              AND stock.id_sucursal='$id_sucursal'
              ";*/
              $sql1 = "SELECT p.id_producto, p.barcode, p.descripcion, p.estado, p.perecedero, p.exento, p.id_categoria, p.id_sucursal,s.id_stock,s.stock, s.id_sucursal, s.precio_unitario, s.costo_unitario FROM producto AS p, stock AS s WHERE p.id_producto = s.id_producto AND p.id_producto ='$id_producto' AND s.id_sucursal='$id_sucursal'";
              $stock1=_query($sql1);
              $row1=_fetch_array($stock1);
              $nrow1=_num_rows($stock1);
              if ($nrow1>0){
                //$unidades=$row1['unidad'];
                //$utilidad_activa=$row1['utilidad_activa'];
                //$utilidad_seleccion=$row1['utilidad_seleccion'];
                $perecedero=$row1['perecedero'];
                $barcode = $row1["barcode"];
                $descripcion = $row1["descripcion"];
                $estado = $row1["estado"];
                $perecedero = $row1["perecedero"];
                $exento = $row1["exento"];
                $id_stock = $row1["id_stock"];
                $stock = $row1["stock"];
                $precio_unitario = $row1["precio_unitario"];
                $costo_unitario = $row1["costo_unitario"];

                //precio de venta
                $fecha_hoy=date("Y-m-d");
                $fecha_hoy2=date("d-m-Y");

                //consultar si es perecedero
                $sql_existencia = "SELECT su.id_producto, su.cantidad, su.id_ubicacion, u.id_ubicacion, u.bodega  FROM stock_ubicacion as su, ubicacion as u WHERE su.id_producto = '$id_producto' AND su.id_ubicacion = u.id_ubicacion AND u.bodega != 1 ORDER BY su.id_su ASC";
                $resul_existencia = _query($sql_existencia);
                $cuenta_existencia = _num_rows($resul_existencia);
                $existencia_real = 0;
                if($cuenta_existencia > 0)
                {
                  while ($row_ex = _fetch_array($resul_existencia))
                  {
                    $cantidad_ex = $row_ex["cantidad"];
                    $existencia_real += $cantidad_ex;
                  }
                }
                $fecha_caducidad="0000-00-00";
                $stock_fecha=0;
                if($perecedero==1){
                  $sql_perecedero="SELECT id_lote, id_producto, fecha_entrada, precio, cantidad, estado, numero, id_sucursal, vencimiento, referencia FROM lote WHERE id_producto='$id_sucursal' AND id_sucursal='$id_sucursal' AND estado='VIGENTE' AND (vencimiento>='$fecha_hoy' OR  vencimiento='0000-00-00') ORDER BY vencimiento ASC";
                  $result_perecedero=_query($sql_perecedero);
                  $array_fecha=array();
                  $array_stock=array();
                  $nrow_perecedero=_num_rows($result_perecedero);
                  if($nrow_perecedero>0){
                    for ($i=0;$i<$nrow_perecedero;$i++){
                      $row_perecedero=_fetch_array($result_perecedero);
                      //$costos_pu=array($pu1,$pu2,$pu3,$pu4);
                      $entrada=$row_perecedero['cantidad'];
                      $id_lote_prod=$row_perecedero['id_lote'];
                      $fecha_caducidad=$row_perecedero['vencimiento'];
                      if($fecha_caducidad=="")
                      $fecha_caducidad="0000-00-00";
                      $fecha_caducidad=ED($fecha_caducidad);
                      $stock_fecha=$entrada-$salida;
                      $array_fecha[] =$id_lote_prod."|".$fecha_caducidad;
                      $array_stock[] =$id_lote_prod."|".$fecha_caducidad."|".$stock_fecha;
                    }
                  }

                }
                else{
                  $array_fecha="";
                  $array_stock="";
                }
              }
              //$ubicacion=ubicacionn($id_posicion);
              //si no hay stock devuelve cero a todos los valores !!!
              if ($nrow1==0){
                $existencias=0;
                $precio_venta=0;
                $costos_pu=array(0,0,0,0);
                $precios_vta=array(0,0,0,0);
                $cp=0;
                $iva=0;
                $unidades=" ";
                $imagen='';
                $combo=0;
                $fecha_caducidad='0000-00-00';
                $stock_fecha=0;
                $oferta=0;
              }
              //}
              //$xdatos['mayoreo'] = $mayoreo;
              /*if($mayoreo)
              {
              $sql = _query("SELECT precio FROM precio_producto WHERE id_producto='$id_producto' AND '1' BETWEEN desde AND hasta");
              if(_num_rows($sql)>0)
              {
              $datos = _fetch_array($sql);
              $precio = $datos["precio"];
              $xdatos["precio"] = $precio;
            }
            else
            {
            $xdatos["precio"] = 0;
          }
        }
        if(!$mayoreo && $precio>0)
        {

        $xdatos["typeinfo"] = 'Success';
      }*/
      /*inicio modificacion presentacion*/
      $i=0;
      $unidadp=0;
      $preciop=0;
      $descripcionp=0;

      $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto='$id_producto' AND presentacion_producto.activo=1");
      $select="<select class='sel form-control'>";
      while ($row=_fetch_array($sql_p)) {
        # code...
        if ($i==0) {
          # code...
          $unidadp=$row['unidad'];
          $preciop=$row['precio'];
          $descripcionp=$row['descripcion'];
        }


        $select.="<option value='$row[id_presentacion]'>$row[nombre]</option>";
        $i=$i+1;

      }
      $select.="</select>";
      /*fin modificacion presentacion*/

      //$precio_venta=round($precio_venta,2);
      $xdatos['existencias'] = $existencia_real;
      //$xdatos['precio_venta'] = $precio_unitario;
      //$xdatos['costo_prom'] = $cp;
      //$xdatos['iva'] = $iva;
      //$xdatos['unidades'] = $unidades;
      //$xdatos['imagen'] = $imagen;
      //$xdatos['combo'] = $combo;
      $xdatos['fecha_caducidad'] = $fecha_caducidad;
      $xdatos['stock_fecha'] =$stock_fecha;
      //$xdatos['oferta'] =$oferta;
      //$xdatos['precio_oferta'] =$precio_oferta;
      //$xdatos['porc_desc_base']=$porc_desc_base;
      //$xdatos['porc_desc_max']=$porc_desc_max;
      $xdatos['perecedero']=$perecedero;
      $xdatos['fechas_vence'] = $array_fecha;
      $xdatos['stock_vence'] = $array_stock;
      //$xdatos['fecha_ini_oferta']=$fecha_ini_oferta;
      //$xdatos['fecha_fin_oferta']=$fecha_fin_oferta2;
      $xdatos['fecha_hoy']= $fecha_hoy;
      //$xdatos['precios_vta']= $precios;
      //$xdatos['ubicacion']= $ubicacion;
      $xdatos['descripcion']= $descripcion;
      $xdatos['select']= $select;
      $xdatos['preciop']= $preciop;
      $xdatos['unidadp']= $unidadp;
      $xdatos['descripcionp']= $descripcionp;

      echo json_encode($xdatos); //Return the JSON Array
    }
    function cons_rank()
    {
      $id_sucursal = $_SESSION["id_sucursal"];



      $id_producto=$_POST['id_producto'];
      $id_presentacion=$_POST['id_presentacion'];
      $cantidad=$_POST['cantidad'];

      $id_usuario=$_SESSION["id_usuario"];
      $r_precios=_fetch_array(_query("SELECT precios FROM usuario WHERE id_usuario=$id_usuario"));
      $precios=$r_precios['precios'];
      $limit="";
      if ($precios==0) {
        $limit="AND $cantidad >= desde";
      }

      $select_rank="<select class='sel_r precio_r form-control'>";
      $sql_rank=_query("SELECT id_prepd,desde,hasta,precio
        FROM presentacion_producto_precio
        WHERE id_presentacion=$id_presentacion
        AND id_sucursal=$id_sucursal
        AND precio!=0
        ORDER BY presentacion_producto_precio.desde ASC

        ");
        $xc = 0;
        $preciop = 0;
        if(_num_rows($sql_rank)>0)
        {
          while ($rowr=_fetch_array($sql_rank))
          {
            $select_rank.="<option value='$rowr[precio]'";
            if(!$xc)
            {
              $select_rank.=" selected ";
              $preciop=$rowr['precio'];
              $xc = 1;
            }
            $select_rank.=">$rowr[precio]</option>";
          }
        }
        else
        {
          $sqlq = _query("SELECT precio FROM presentacion_producto WHERE id_presentacion='$id_presentacion'");
          $datsq = _fetch_array($sqlq);
          $preciop=$datsq['precio'];
          $select_rank.="<option value='$datsq[precio]' selected>$datsq[precio]</option>";
        }
        $select_rank.="</select>";
        $xdatos["precio"] = $preciop;
        $xdatos["precios"] = $select_rank;
        echo json_encode($xdatos); //Return the JSON Array
      }
      function getpresentacion()
      {
        $id_sucursal=$_SESSION['id_sucursal'];
        $id_presentacion =$_REQUEST['id_presentacion'];
        $cant =$_REQUEST['cant'];
        $sql=_fetch_array(_query("SELECT * FROM presentacion_producto WHERE id_presentacion=$id_presentacion"));
        $precio=$sql['precio'];
        $unidad=$sql['unidad'];
        $descripcion=$sql['descripcion'];
        $id_producto=$sql['id_producto'];
        $sql_e=_fetch_array(_query("SELECT exento FROM producto WHERE id_producto=$id_producto"));
        $exento=$sql_e['exento'];

        $select_rank="<select class='sel_r precio_r form-control'>";
        $xc=0;
        $id_sucursal = $_SESSION['id_sucursal'];

        $id_usuario=$_SESSION["id_usuario"];
        $r_precios=_fetch_array(_query("SELECT precios FROM usuario WHERE id_usuario=$id_usuario"));
        $precios=$r_precios['precios'];
        $limit="";
        if ($precios==0) {
          $limit="AND '$cant' >= desde";
        }

        $sql_rank=_query("SELECT id_prepd,desde,hasta,precio
          FROM presentacion_producto_precio
          WHERE id_presentacion=$id_presentacion
          AND id_sucursal=$id_sucursal
          $limit
          AND precio>0
          ORDER BY presentacion_producto_precio.desde ASC");

          while ($rowr=_fetch_array($sql_rank))
          {
            # code...
            $select_rank.="<option value='$rowr[precio]'";
            if(!$xc)
            {
              $select_rank.=" selected ";
              $precio=$rowr['precio'];
              $xc=1;
            }
            $select_rank.=">$rowr[precio]</option>";
          }
          if (_num_rows($sql_rank)==0) {
            # code...
            $select_rank.="<option value='$precio'";
            $select_rank.="selected";
            $select_rank.=">$precio</option>";
          }
          $select_rank.="<option value='0.0'>0.0</option>";
          $select_rank.="</select>";

          $des = "<input type='text' id='ss' class='txt_box form-control' value='".$descripcion."' readonly>";
          $xdatos['precio']=$precio;

          if ($exento==1) {
            # code...
            $xdatos['preciop_s_iva']=$precio;
          }
          else {
            # code...
            $sqkl=_fetch_array(_query("SELECT iva FROM sucursal WHERE id_sucursal=$id_sucursal"));
            $iva=$sqkl['iva']/100;
            $iva=1+$iva;
            $xdatos['preciop_s_iva']= round(($precio/$iva),8,PHP_ROUND_HALF_DOWN);
          }
          $xdatos['unidad']=$unidad;
          $xdatos['descripcion']=$des;
          $xdatos['descripcion']=$des;
          $xdatos['select_rank']=$select_rank;
          echo json_encode($xdatos);
        }
        function insertar_preventa()
        {
          $actualizacion_preventa=0;
          $id_referencia_log=0;
          $insert_ref=0;
          //date_default_timezone_set('America/El_Salvador');
          $id_factura=$_POST['id_factura'];
          $fecha_movimiento= $_POST['fecha_movimiento'];
          $id_cliente=$_POST['id_cliente'];

          $id_vendedor=$_SESSION['id_usuario'];
          $cuantos = $_POST['cuantos'];
          $array_json=$_POST['json_arr'];
          //  IMPUESTOS
          $total_percepcion= $_POST['total_percepcion'];

          $subtotal=$_POST['subtotal'];
          $sumas=$_POST['sumas'];
          $suma_gravada=$_POST['suma_gravada'];
          $iva= $_POST['iva'];
          $retencion= $_POST['retencion'];
          $venta_exenta= $_POST['venta_exenta'];
          $total_menos_retencion=$_POST['total'];
          $total = $retencion+$_POST['total'];

          $id_empleado=$_SESSION["id_usuario"];
          $id_sucursal=$_SESSION["id_sucursal"];
          $fecha_actual = date('Y-m-d');
          $fecha_movimiento=$fecha_actual;
          $tipoprodserv = "PRODUCTO";

          $insertar_fact=false;
          $insertar_fact_dett=true;
          $insertar_numdoc =false;

          $hora=date("H:i:s");
          $xdatos['typeinfo']='';
          $xdatos['msg']='';
          $xdatos['process']='';

          _begin();

          $a=1;
          $b=1;
          $c=1;

          if($id_factura==0)
          {
            $hoy = date("Y-m-d");
            $sql="SELECT MAX(numero_ref) as ref FROM factura WHERE id_sucursal='$id_sucursal' AND fecha='$hoy'";
            $result= _query($sql);
            $rows=_fetch_array($result);
            $ult=$rows['ref']+1;
            $numero_doc = str_pad($ult,7,"0",STR_PAD_LEFT)."_REF";
          }
          else
          {
            $sql_num=_fetch_array(_query("SELECT * FROM factura where id_factura=$id_factura"));
            $numero_doc=$sql_num['numero_doc'];
            $ult=$sql_num['numero_ref'];
            $actualizacion_preventa=1;
          }

          $abono=0;
          $saldo=0;
          $tipo_documento=$_POST['tipo_impresion'];
          $tipo_entrada_salida='NUM. REFERENCIA INTERNA';

          if ($id_factura=="0") {
            # code...
            $table_fact= 'factura';
            $form_data_fact = array(
              'id_cliente' => $id_cliente,
              'fecha' => $fecha_actual,
              'numero_doc' => $numero_doc,
              'referencia' => $numero_doc,
              'numero_ref' => $ult,
              'subtotal' => $subtotal,
              'sumas'=>$sumas,
              'suma_gravado'=>$suma_gravada,
              'iva' =>$iva,
              'retencion'=>$retencion,
              'venta_exenta'=>$venta_exenta,
              'total_menos_retencion'=>$total_menos_retencion,
              'total' => $total,
              'id_usuario'=>$id_empleado,
              'id_empleado' => $id_vendedor,
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

            $form_data_fact["actualizacion"]=$actualizacion_preventa;
            $insert_ref= _insert("referencia",$form_data_fact );
            $id_referencia_log=_insert_id();

            if (!$insertar_fact) {
              # code...
              $b=0;
            }

            //verificar si existe un correlativo
            $sqer = _query("SELECT * FROM factura WHERE numero_ref=$ult AND fecha='$fecha_actual' AND id_factura!=$id_fact");

            if(_num_rows($sqer)>0)
            {
              $sql="SELECT MAX(numero_ref) as ref FROM factura WHERE id_sucursal='$id_sucursal' AND fecha='$hoy'";
              $result= _query($sql);
              $rows=_fetch_array($result);
              $ult=$rows['ref']+1;
              $numero_doc = str_pad($ult,7,"0",STR_PAD_LEFT)."_REF";

              $form_data_fact = array(
                'numero_doc' => $numero_doc,
                'referencia' => $numero_doc,
                'numero_ref' => $ult,
              );
              $whereclause="id_factura='".$id_fact."'";
              $insertar_fact = _update($table_fact,$form_data_fact,$whereclause );
            }
          }
          else {
            # code...
            $table_fact= 'factura';
            $form_data_fact = array(
              'id_cliente' => $id_cliente,
              'fecha' => $fecha_actual,
              'numero_doc' => $numero_doc,
              'referencia' => $numero_doc,
              'numero_ref' => $ult,
              'subtotal' => $subtotal,
              'sumas'=>$sumas,
              'suma_gravado'=>$suma_gravada,
              'iva' =>$iva,
              'retencion'=>$retencion,
              'venta_exenta'=>$venta_exenta,
              'total_menos_retencion'=>$total_menos_retencion,
              'total' => $total,
              'id_usuario'=>$id_empleado,
              'id_empleado' => $id_vendedor,
              'id_sucursal' => $id_sucursal,
              'tipo' => $tipo_entrada_salida,
              'hora' => $hora,
              'finalizada' => '0',
              'abono'=>$abono,
              'saldo' => $saldo,
              'tipo_documento' => $tipo_documento,
            );
            $whereclause="id_factura='".$id_factura."'";
            $insertar_fact = _update($table_fact,$form_data_fact,$whereclause );
            $id_fact= $id_factura;

            $form_data_fact["actualizacion"]=$actualizacion_preventa;
            $insert_ref= _insert("referencia",$form_data_fact );
            $id_referencia_log=_insert_id();

            if (!$insertar_fact) {
              # code...
              $b=0;
            }
            $table="factura_detalle";
            $where_clause="id_factura='".$id_fact."'";
            $delete=_delete($table,$where_clause);
            if (!$delete) {
              # code...
              $b=0;
            }

          }

          if ($cuantos>0)
          {
            $array = json_decode($array_json, true);
            foreach ($array as $fila)
            {
              $id_producto=$fila['id'];
              $unidades=$fila['unidades'];
              $subtotal=$fila['subtotal'];
              $cantidad=$fila['cantidad'];
              $id_presentacion=$fila['id_presentacion'];
              $cantidad_real=$cantidad*$unidades;
              $exento=$fila['exento'];
              $precio_venta=$fila['precio'];

              $table_fact_det= 'factura_detalle';
              $data_fact_det = array(
                'id_factura' => $id_fact,
                'id_prod_serv' => $id_producto,
                'cantidad' => $cantidad_real,
                'precio_venta' => $precio_venta,
                'subtotal' => $subtotal,
                'tipo_prod_serv' => $tipoprodserv,
                'id_empleado' => $id_empleado,
                'id_sucursal' => $id_sucursal,
                'fecha' => $fecha_movimiento,
                'id_presentacion'=> $id_presentacion,
                'exento' => $exento,
              );
              $insertar_fact_det = _insert($table_fact_det,$data_fact_det );

              $data_fact_det['id_factura']=$id_referencia_log;
              _insert("referencia_detalle",$data_fact_det );

              if (!$insertar_fact_det) {
                # code...
                $c=0;
              }

            } //foreach ($array as $fila){
              if ($a&&$b&&$c)
              {
                _commit(); // transaction is committed
                $xdatos['typeinfo']='Success';
                $xdatos['msg']='Referenca Numero: <strong>'.$numero_doc.'</strong>  Guardado con Exito !';
                $xdatos['referencia']=$ult;
                $xdatos['tot']=number_format($total,2);
              }
              else
              {
                _rollback(); // transaction rolls back
                $xdatos['typeinfo']='Error';
                $xdatos['msg']='Registro no pudo ser ingresado!'.$a."-".$b."-".$c;
              }
            }
            echo json_encode($xdatos);
          }
          function borrar_preventa()
          {
            _begin();
            $id_factura=$_REQUEST['id_factura'];
            $table='factura';
            $where_clause="id_factura='".$id_factura."'";
            $delete=_delete($table,$where_clause);
            if($delete)
            {
              $table="factura_detalle";
              $where_clause="id_factura='".$id_factura."'";
              $delete=_delete($table,$where_clause);
              if($delete)
              {
                _commit();
                $xdatos['typeinfo']="Success";
                $xdatos['msg']="Registro eliminado correctamente";
              }
              else
              {
                _rollback();
                $xdatos['typeinfo']="Error";
                $xdatos['msg']="Error al insertar el registro";
              }
            }
            else{
              _rollback();
              $xdatos['typeinfo']="Error";
              $xdatos['msg']="Error al insertar el registro";

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
              case 'formEdit':
                initial();
                break;
                case 'insert':
                insertar();
                break;
                case 'insertar_venta':
                insertar_venta();
                break;
                case 'mostrar_datos_cliente':
                mostrar_datos_cliente();
                break;
                case 'consultar_stock':
                consultar_stock();
                break;
                case 'cargar_empleados':
                cargar_empleados();
                break;
                case 'cargar_precios':
                cargar_precios();
                break;
                case 'total_texto':
                total_texto();
                break;

                case 'imprimir_fact':
                imprimir_fact();
                break;
                case 'print2':
                print2(); //Generacion de los datos de factura que se retornan para otro script que imprime!!!
                break;
                case 'mostrar_numfact':
                mostrar_numfact();
                break;
                case 'reimprimir':
                reimprimir();
                break;
                case 'agregar_cliente':
                agregar_cliente();
                break;
                case 'cargar_data':
                cargar_data();
                break;
                case 'consultar_stock1':
                consultar_stock1();
                break;
                case 'cons_rank':
                cons_rank();
                break;
                case 'getpresentacion':
                getpresentacion();
                break;
                case 'insert_preventa':
                insertar_preventa();
                break;
                case 'borrar_preventa':
                borrar_preventa();
                break;
              }

              //}
            }
            ?>
