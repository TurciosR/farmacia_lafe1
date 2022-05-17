<?php
    include ("_core.php");
    $requestData= $_REQUEST;
    $fini= $_REQUEST["fini"];
    $fin= $_REQUEST["fin"];
    require('ssp.customized.class.php' );
    // DB table to use
    $table= 'pedido';
    /*
    SELECT id_empleado_reloj,id_persona,horario_numero,descripcion FROM horario_individual
    WHERE id_empleado_reloj=1
    GROUP BY id_empleado_reloj,id_persona,horario_numero
    */
    // Table's primary key
    $primaryKey = 'id_pedido';
    // MySQL server connection information
     $sql_details = array(
     'user' => $username,
     'pass' =>$password,
     'db'   => $dbname,
     'host' => $hostname
    );
    $id_sucur=$_SESSION["id_sucursal"];
    if($fini!="" AND $fin!="")
    {
      $joinQuery=" FROM pedido  JOIN  cliente ON (pedido.id_cliente=cliente.id_cliente)";
	    $extraWhere=" pedido.fecha BETWEEN '$fini' AND '$fin' AND pedido.id_sucursal='$id_sucur'";
    }else {
      $joinQuery=" FROM pedido  JOIN  cliente ON (pedido.id_cliente=cliente.id_cliente)";
	    $extraWhere="pedido.id_sucursal='$id_sucur'";
    }
    $columns = array(
		array( 'db' => 'pedido.id_pedido', 'dt' => 0, 'field' => 'id_pedido' ),
    array( 'db' => 'cliente.nombre', 'dt' => 1, 'field' => 'nombre' ),
    array( 'db' => 'pedido.fecha', 'dt' => 2, 'field' => 'fecha' ),
    array( 'db' => 'pedido.fecha_entrega', 'dt' => 3, 'field' => 'fecha_entrega' ),
    array( 'db' => 'pedido.lugar_entrega', 'dt' => 4, 'field' => 'lugar_entrega' ),
    array( 'db' => 'pedido.numero', 'dt' => 5, 'field' => 'numero' ),
    array( 'db' => 'pedido.total', 'dt' => 6, 'field' => 'total' ),
    array(
        'db'        => 'pedido.estado',
        'dt'        => 7,
        'formatter' => function( $txt_estado, $row ) {
          if($txt_estado=='ANULADO' || $txt_estado=='FINALIZADO'){

            $campo="<td><h5 class='text-mutted'>".$txt_estado."</h5></td>";
          return $campo;}
          if($txt_estado=='PENDIENTE'){
            $campo="<td><h5 class='text-danger'>".$txt_estado."</h5></td>";
          return $campo;}
        }, 'field' => 'estado'
    ),
    array( 'db' => 'pedido.id_pedido','dt' => 8,
    'formatter' => function( $id_pedido, $row ){
      $menudrop="<div class='btn-group'>
      <a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
      <ul class='dropdown-menu dropdown-primary'>";
      $id_user=$_SESSION["id_usuario"];
      $admin=$_SESSION["admin"];
      $filename='editar_pedido.php';
      $sql = _query("SELECT estado FROM pedido WHERE id_pedido='$id_pedido'");
      $datosp = _fetch_array($sql);
      $txt_estado = $datosp["estado"];
      $link=permission_usr($id_user,$filename);
      if ($link!='NOT' || $admin=='1'){
        if($txt_estado=='ANULADO' || $txt_estado=='FINALIZADO'){

        }else
        {
            $menudrop.="<li><a  href='$filename?id_pedido=" .$row ['id_pedido']."'><i class='fa fa-check'></i> Editar</a></li>";
        }
        //}
      }
      $filename='anular_pedido.php';
      $link=permission_usr($id_user,$filename);
      if ($link!='NOT' || $admin=='1'){
        if($txt_estado=='ANULADO' || $txt_estado=='FINALIZADO'){

        }else
        {
          $menudrop.="<li><a data-toggle='modal' href='$filename?id_pedido=" .  $row ['id_pedido']."&process=formAnular"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-close\"></i> Anular</a></li>";
        }

      }
      $filename='reporte_pedido.php';
      $link=permission_usr($id_user,$filename);
      if ($link!='NOT' || $admin=='1'){
        if($txt_estado=='ANULADO'){

        }else
        {
          $menudrop.="<li><a  href='$filename?id_pedido=" .$row ['id_pedido']."' target='_blank'><i class='fa fa-reorder'></i> Reporte</a></li>";
        }

      }
        $filename='procesar_pedido.php';
        $link=permission_usr($id_user,$filename);
        if ($link!='NOT' || $admin=='1' ){

          if($txt_estado=='ANULADO' || $txt_estado=='FINALIZADO'){

          }else
          {
          $menudrop.="<li><a  href='$filename?id_pedido=" .$row ['id_pedido']."' ><i class='fa fa-file'></i> Procesar</a></li>";
          }
          }
          $filename='ver_pedido.php';
          $link=permission_usr($id_user,$filename);
          if ($link!='NOT' || $admin=='1' ){
            $menudrop.= "<li><a data-toggle='modal' href='ver_pedido.php?id_pedido=".$row['id_pedido']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-search\"></i> Ver Detalle</a></li>";
            }

            $menudrop.="</ul>
            </div>";
            return $menudrop;}, 'field' => 'id_pedido')

    		);
	   echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
?>
