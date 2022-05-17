<?php
include_once "_core.php";
include('num2letras.php');
//include("escpos-php/Escpos.php");
function initial()
{
  $id_devolucion=$_REQUEST["id_devolucion"];
  //permiso del script
  $id_user=$_SESSION["id_usuario"];
  $admin=$_SESSION["admin"];
  $uri = $_SERVER['SCRIPT_NAME'];
  $filename=get_name_script($uri);
  $links=permission_usr($id_user, $filename);
  $fecha=date('d-m-Y');
  $id_sucursal=$_SESSION['id_sucursal'];
  $sql0="SELECT devoluciones_vencimiento.fecha,devoluciones_vencimiento.numero_doc,devoluciones_vencimiento.total, proveedor.nombre FROM devoluciones_vencimiento LEFT JOIN proveedor ON proveedor.id_proveedor=devoluciones_vencimiento.id_proveedor WHERE devoluciones_vencimiento.id_devolucion=$id_devolucion";
  $result = _query($sql0);
  $row = _fetch_array($result);
  $num_fact_impresa=explode("_",$row['numero_doc']);
  $num_fact_impresa = intval($num_fact_impresa[0]).$num_fact_impresa[1];
  $nombre=$row['nombre'];
  $total=$row['total'];
  $fecha=$row['fecha'];
  $sql_ab = _query("SELECT SUM(abono) as abono FROM devoluciones_vencimiento_abono WHERE id_devolucion='$id_devolucion'");
  $dats_abono = _fetch_array($sql_ab);
  $abono = $dats_abono["abono"];
  $saldo = $total - $abono;
  ?>

  <div class="modal-header">
    <h4 class="modal-title">Abonos</h4>
  </div>

  <div class="modal-body">
    <div class="row">
      <div class="form-group col-md-6">
        <label>Total&nbsp;</label>
        <input type="text"  class='form-control input_header_panel' id="deuda" value='<?php echo number_format($saldo,2,".",""); ?>' readOnly />
      </div>
      <div class="form-group col-md-6">
        <label>Abonos &nbsp;</label>
        <input type="text"  class='form-control input_header_panel' id="abonos"  value='<?php echo number_format($abono,2,".",""); ?>' readOnly>
      </div>
      <?php if($saldo>0){ ?>
        <div class="form-group col-md-6">
          <label>Tipo Doc.</label>
          <select class="form-control select" id="tipo_doc" style="width:100%;">
            <option value="">Seleccione</option>
            <option value="CCF">CCF</option>
            <option value="COF">COF</option>
          </select>
        </div>
        <div class="form-group col-md-6">
          <label>Numero Doc.</label>
          <input type="text"  class='form-control input_header_panel' id="num_doc">
        </div>
        <div class="form-group col-md-6">
          <label>Monto</label>
          <input type="text"  class='form-control input_header_panel' id="monto">
        </div>
        <div class="form-group col-md-6">
          <br>
          <button class="btn btn-success" type="button" id="abonar" name="abonar" disabled>Abonar</button>
        </div>
      <?php } else { ?>
        <div class="alert alert-info">Cuenta Saldada</div>
      <?php } ?>
    </div>
    <?php    if ($links!='NOT' || $admin=='1') { ?>

      <div class="row" id="row1">
        <div class="col-md-12">
          <input type='hidden' name='id_factura' id='id_factura' value='<?php echo $id_devolucion; ?>'>
          <input type='hidden' name='nombre' id='nombre' value='<?php echo Mayu($nombre); ?>'>
          <input type='hidden' name='facts' id='facts' value='<?php echo $num_fact_impresa; ?>'>
          <input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename; ?>">
          <!--
          <h4>Fecha: &nbsp;<?php echo ED($fecha); ?></h4>
        -->
      </header>
      <section>
        <table class="table  table-striped">
          <thead>
            <tr>
              <th class="text-success col-md-2">Fecha</th>
              <th class="text-success col-md-2">Hora</th>
              <th class="text-success col-md-3">Tipo Doc</th>
              <th class="text-success col-md-2">Num. Doc</th>
              <th class="text-success col-md-2">Abono $</th>
              <th class="text-success col-md-1">Acción</th>
            </tr>
          </thead>
          <tbody id="appas">
            <?php
            $sql = _query("SELECT * FROM devoluciones_vencimiento_abono WHERE id_devolucion=$id_devolucion ORDER BY id_abono DESC");
            $tot = 0;
            while ($row = _fetch_array($sql)) {
              $tot += $row["abono"];
              echo "<tr>";
              echo "<td>".ED($row["fecha"])."</td>";
              echo "<td>".hora($row["hora"])."</td>";
              echo "<td>".$row["tipo_doc"]."</td>";
              echo "<td>".$row["num_doc"]."</td>";
              echo "<td class='mont'>".number_format($row["abono"], 2)."</td>";
              echo "<td>";

              if ($admin==1) {
                // code...
                echo "<a class='btn delee' id='".$row["id_abono"]."'><i class='fa fa-trash'></i></a>";
              }
              echo "</td>";
              echo "</tr>";
            } ?>
          </tbody>
          <tfoot>
            <tr>
              <th class="text-success" colspan="4">Total</th>
              <th class="text-success" id="total"><?php echo number_format($tot,2,".",""); ?></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </section>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-danger" id="clos" data-dismiss="modal">Salir</button>
</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
  $(".select").select2();
  $("#monto").numeric({negative:false,decimalPlaces:2});
});
</script>
<?php
} //permiso del script
else {

  $mensaje = mensaje_permiso();
  echo "<br><br>$mensaje</div></div></div></div>";
  include "footer.php";
}
}
function abonar()
{
  $id_empleado=$_SESSION["id_usuario"];
  $id_sucursal=$_SESSION["id_sucursal"];
  $id_devolucion = $_POST["id_factura"];
  $monto = $_POST["monto"];
  $num_doc = $_POST["num_doc"];
  $tipo_doc = $_POST["tipo_doc"];
  $fecha=date("Y-m-d");
  $hora=date("H:i:s");


  $nuevosaldo=0;
  _begin();
  $sql=_query("SELECT total FROM devoluciones_vencimiento WHERE id_devolucion=$id_devolucion");
  $row=_fetch_array($sql);
  $total=$row['total'];
  $sql_ab = _query("SELECT SUM(abono) as abono FROM devoluciones_vencimiento_abono WHERE id_devolucion='$id_devolucion'");
  $dats_abono = _fetch_array($sql_ab);
  $abono = $dats_abono["abono"];
  $saldo = $total - $abono;
  $num_fact_impresa=$row['numero_doc'];

  if ($monto<=$saldo)
  {

    $table = 'devoluciones_vencimiento_abono';
    $form_data = array(
      'id_devolucion' => $id_devolucion,
      'abono' => $monto,
      'fecha' => $fecha,
      'hora' => $hora,
      'tipo_doc' => $tipo_doc,
      'num_doc' => $num_doc,
    );
    $insertar1 = _insert($table, $form_data);
    $ex = 1;
    if ($insertar1)
    {
      $id_abono = _insert_id();
      $saldo -= $monto;
      if($saldo <= 0)
      {
        $table1 = 'devoluciones_vencimiento';
        $form_data1 = array(
          'finalizado' => 1,
        );
        $where = "id_devolucion='".$id_devolucion."'";
        $insertar = _update($table1, $form_data1 , $where);
        if(!$insertar)
        {
          $ex =0;
        }
      }
      if($ex)
      {
        _commit();
        $xdatos['typeinfo']='Success';
        $xdatos['msg']='Abono realizado con exito!';
        $xdatos["fecha"] = ED($fecha);
        $xdatos["hora"] = hora($hora);
        $xdatos["monto"] = number_format($monto,2);
        $xdatos["id_abono"] = $id_abono;
      }
      else {
        _rollback();
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Registro no pudo ser guardado!';
      }
    }
    else {
      _rollback();
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Registro no pudo ser guardado 1!';
    }
  }
  else {
    _rollback();
    $xdatos['typeinfo']='Error';
    $xdatos['msg']='El monto a abonar es superior al saldo pendiente!';
  }

  echo json_encode($xdatos);
}
function quitar()
{
  $id_devolucion = $_POST["id_factura"];
  $id_abono = $_POST["id_abono"];
  $monto = $_POST["monto"];
  $fecha=date("Y-m-d");
  $hora=date("H:i:s");
  $table1 = "devoluciones_vencimiento_abono";
  $where1 = "id_abono='".$id_abono."'";
  $delete1 = _delete($table1, $where1);
  $table1 = 'devoluciones_vencimiento';
  $form_data1 = array(
    'finalizado' => 0,
  );
  $where = "id_devolucion='".$id_devolucion."'";
  $insertar = _update($table1, $form_data1 , $where);
  if($delete1 && $insertar)
  {

    _commit();
    $xdatos['typeinfo']='Success';
    $xdatos['msg']='Abono eliminado correctamente!';
  }
  else
  {
    _rollback();
    $xdatos['typeinfo']='Error';
    $xdatos['msg']='Abono no pudo ser eliminado!';
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
      case 'val':
      cuentas_b();
      break;
      case 'abonar':
      abonar();
      break;
      case 'quitar':
      quitar();
      break;
    }

    //}
  }
  ?>
