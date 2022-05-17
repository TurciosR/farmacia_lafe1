<?php
include("_core.php");
$requestData= $_REQUEST;
require('ssp.customized.class.php');
// DB table to use
$table = 'producto';
/*
$hostname = "localhost";
$username = "libreria";
$password = "L1br3r1@18";
$dbname
*/
// Table's primary key
$primaryKey = 'id_producto';
// MySQL server connection information
$sql_details = array(
  'user' => $username,
  'pass' => $password,
  'db'   => $dbname,
  'host' => $hostname
);
$joinQuery=" FROM producto as pr left JOIN proveedor as p ON p.id_proveedor=pr.id_proveedor LEFT JOIN categoria as cat ON (pr.id_categoria=cat.id_categoria)";
$extraWhere="";

$columns = array(
  array( 'db' => '`pr`.`id_producto`', 'dt' => 0, 'field' => 'id_producto'),
  array( 'db' => '`pr`.`barcode`',  'dt' => 1, 'field' => 'barcode'),
  array( 'db' => '`pr`.`descripcion`',   'dt' => 2, 'field' => 'descripcion'),
  array( 'db' => '`pr`.`composicion`',   'dt' => 3, 'field' => 'composicion'),
  array( 'db' => '`cat`.`nombre_cat`',   'dt' => 4, 'field' => 'nombre_cat'),
  array( 'db' => '`p`.`nombre`',   'dt' => 5, 'field' => 'nombre'),
  array( 'db' => 'id_producto','dt' => 6,
  'formatter' => function ($id_producto, $row) {
      $menudrop="<div class='btn-group'>
    <a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
    <ul class='dropdown-menu dropdown-primary'>";
      $id_user=$_SESSION["id_usuario"];
      $admin=$_SESSION["admin"];
      $filename='anular_factura.php';
      $link=permission_usr($id_user, $filename);
      $filename='editar_producto.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.="<li><a href=\"editar_producto.php?id_producto=".$row['id_producto']."\"><i class=\"fa fa-pencil\"></i> Editar</a></li>";
      }
      /*$filename='borrar_producto.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.="<li><a data-toggle='modal' href='borrar_producto.php?id_producto=" .  $row ['id_producto']."&process=formDelete"."' data-target='#deleteModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>";
      }*/
      $filename='ver_producto.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.= "<li><a data-toggle='modal' href='ver_producto.php?id_producto=".$row['id_producto']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-search\"></i> Ver Detalle</a></li>";
      }

      /*$filename='precio_producto.php';
      $link=permission_usr($id_user, $filename);
      if ($link!='NOT' || $admin=='1') {
          $menudrop.="<li><a href=\"precio_producto.php?id_producto=".$row['id_producto']."\"><i class=\"fa fa-money\"></i> Precios</a></li>";
      }*/


      $menudrop.="</ul>
          </div>";
      return $menudrop;
  }, 'field' => 'id_producto')
        );
        echo json_encode(
          SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
        );
