<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');


$pdf=new fPDF('L','mm', 'Letter');
$pdf->SetMargins(10,5);
$pdf->SetTopMargin(2);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,1);
$pdf->AddFont("latin","","latin.php");
$id_ubicacion=$_REQUEST['u'];
$ubi='';
if ($id_ubicacion!="") {
  $ubi=" AND su.id_ubicacion=$id_ubicacion ";
}
$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";
$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
//$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
$direccion = utf8_decode(Mayu(utf8_decode(trim($row_emp["direccion"]))));
$nrc = $row_emp['nrc'];
$nit = $row_emp['nit'];
$whatsapp=$row_emp["whatsapp"];
$email=$row_emp["email"];
$depa = $row_emp["id_departamento"];
$muni = $row_emp["id_municipio"];
$telefono1 = $row_emp["telefono1"];
$telefono2 = $row_emp["telefono2"];

$sql2 = _query("SELECT dep.* FROM departamento as dep WHERE dep.id_departamento='$depa'");
$row2 = _fetch_array($sql2);
$departamento = $row2["nombre_departamento"];

$iftike = $_REQUEST["tiket"];
if($iftike == 1)
{
  $extra = "";
}
else
{
    $extra = " AND tipo_documento != 'TIK'";
}
$min = $_REQUEST["l"];
$fini = date("Y-m-d");
$fin = $_REQUEST["ffin"];
$fini1 = ED($_REQUEST["fini"]);
$fin1 = ED($_REQUEST["ffin"]);
$logo = "img/logo_sys.png";

$title = $nombre_a;
$titulo = "REPORTE DE INVENTARIO";
if($fini!="")
{
    list($a,$m,$d) = explode("-", $fini);

    $fech="AL $d DE ".meses($m)." DE $a";

}
$impress = "REPORTE DE INVENTARIO ".$fech;


$existenas = "";
if($min>0)
{
    $existenas = "CANTIDAD: $min";
}

$pdf->AddPage();
$pdf->SetFont('Arial','',10);
//$pdf->Image($logo,9,4,45,18);
$set_x = 5;
$set_y = 6;

    //Encabezado General
    //Encabezado General
$pdf->SetFont('Arial','',16);
$pdf->SetXY($set_x, $set_y+5);
$pdf->MultiCell(280,6,"FARMACIAS LA FE",0,'C',0);
$pdf->SetXY($set_x+28, $set_y+11);
$pdf->SetFont('Arial','',8);
$pdf->SetXY($set_x+98, $set_y+5);
$pdf->SetXY($set_x+28, $set_y+14);
//$pdf->Cell(220,5,Mayu("PBX: ".$telefono1." / ".$telefono2),0,1,'C');
$plus = 0;
$pdf->SetXY($set_x+28, $set_y+17-$plus);
$pdf->SetXY($set_x+28, $set_y+20-$plus);
$pdf->SetXY($set_x, $set_y+20);
$pdf->Cell(280,6,utf8_decode($titulo),0,1,'C');
$pdf->SetXY($set_x, $set_y+23);
$pdf->Cell(280,6,$fech,0,1,'C');

    ///////////////////////////////////////////////////////////////////////

$set_x = 5;
$set_y = 40;

$pdf->SetFont('Arial','',8);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(20,5,utf8_decode("CODIGO"),0,1,'C',0);
$pdf->SetXY($set_x+20,$set_y);
$pdf->Cell(80,5,utf8_decode("PRODUCTO"),0,1,'C',0);
$pdf->SetXY($set_x+115, $set_y);

$pdf->Cell(35,5,utf8_decode("PRESENTACIÓN"),0,1,'C',0);
$pdf->SetXY($set_x+135, $set_y);
$pdf->SetXY($set_x+170, $set_y);
$pdf->SetXY($set_x+205, $set_y);
$pdf->SetXY($set_x+210, $set_y);
$pdf->Cell(15,5,utf8_decode("PRECIO"),0,1,'C',0);
$pdf->SetXY($set_x+235, $set_y);
$pdf->Cell(15,5,utf8_decode("EXISTENCIA"),0,1,'C',0);
$pdf->SetXY($set_x+250, $set_y);
$pdf->Cell(20,5,utf8_decode("TOTAL($)"),0,1,'R',0);
$pdf->Line($set_x,$set_y+5,$set_x+270,$set_y+5);
    //$pdf->SetTextColor(0,0,0);
$set_y = 45;
$linea = 0;
$linea_acumulada = 0;
$page = 0;
$j = 0;
$total_general = 0;
$sql_stock = _query("SELECT pr.id_producto,pr.descripcion, pr.barcode, c.nombre_cat as cat, SUM(su.cantidad) as cantidad,
estante.descripcion AS estante, posicion.posicion
FROM producto AS pr
JOIN stock_ubicacion AS su ON  pr.id_producto=su.id_producto
LEFT JOIN estante ON estante.id_estante=su.id_estante
LEFT JOIN posicion ON posicion.id_posicion=su.id_posicion
LEFT join categoria as c on pr.id_categoria=c.id_categoria
WHERE  su.cantidad>0 $ubi AND su.id_sucursal='$_SESSION[id_sucursal]'  GROUP BY pr.id_producto ORDER BY pr.descripcion ");
$contar = _num_rows($sql_stock);
if($contar > 0)
{
  while ($row = _fetch_array($sql_stock))
  {
    $id_producto = $row['id_producto'];
    $descripcion=$row["descripcion"];
    $cat = $row['cat'];
    $barcode = $row['barcode'];
    $existencias = $row['cantidad'];
    $estante=$row['estante'];
    $posicion=$row['posicion'];

    if ($estante==''&&$posicion=='') {
      // code...
      $estante='NO ASIGNADO';
      $posicion='';
    }
    else {
      $posicion='POSICIÓN '.$posicion;
    }

    $sql_pres = _query("SELECT pp.*, p.nombre as descripcion_pr FROM presentacion_producto as pp, presentacion as p WHERE pp.presentacion=p.id_presentacion AND pp.id_producto='$id_producto' AND  pp.activo=1  ORDER BY pp.unidad DESC");
    $npres = _num_rows($sql_pres);


    $exis = 0;
    $n=0;
    $p = 0;
    $s = 0;
    while ($rowb = _fetch_array($sql_pres))
    {
        if($page==0)
        {
            $salto = 160;
        }
        else
        {
            $salto = 195;
        }
        if($linea>=$salto)
        {
          $page++;
          $pdf->AddPage();
          $set_y = 6;
          $set_x = 5;
              //Encabezado General
          $linea=0;
          $j = 0;
                //$pdf->SetFont('Latin','',8);
      }
      $unidad = $rowb["unidad"];
      $costo = $rowb["costo"];
      $precio = $rowb["precio"];

      $xc=0;

      $sql_rank=_query("SELECT presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$rowb[id_presentacion] AND presentacion_producto_precio.id_sucursal=$_SESSION[id_sucursal] AND presentacion_producto_precio.precio!=0 ORDER BY presentacion_producto_precio.desde ASC LIMIT 1
        ");

        while ($rowr=_fetch_array($sql_rank)) {
          # code...
          if($xc==0)
          {

            $precio=$rowr['precio'];
          }
        }

      $descripcion_pr = $rowb["descripcion"];
      $presentacion = $rowb["descripcion_pr"];
      if($existencias >= $unidad)
      {
          $exis = intdiv($existencias, $unidad);
          $existencias = $existencias%$unidad;
      }
      else
      {
          $exis =  0;
      }
      $total_costo = round(($precio) * $exis, 4);
      $total_general += $total_costo;
      $pdf->SetXY($set_x+120, $set_y+$linea+$p);
      $pdf->Cell(35,5,utf8_decode($presentacion),0,1,'L',0);
      $pdf->SetXY($set_x+135, $set_y+$linea+$p);
      $pdf->SetXY($set_x+170, $set_y+$linea+$p);
      $pdf->SetXY($set_x+205, $set_y+$linea+$p);
      $pdf->SetXY($set_x+210, $set_y+$linea+$p);
      $pdf->Cell(15,5,utf8_decode(number_format($precio, 2)),0,1,'C',0);
      $pdf->SetXY($set_x+235, $set_y+$linea+$p);
      $pdf->Cell(15,5,utf8_decode($exis),0,1,'C',0);
      $pdf->SetXY($set_x+250, $set_y+$linea+$p);
      $pdf->Cell(20,5,utf8_decode(number_format($total_costo, 4)),0,1,'R',0);
      $p += 5;
      $s += 1;
  }
  $j++;
  $pdf->SetXY($set_x, $set_y+$linea);
  $pdf->Cell(24,5*$s,utf8_decode($barcode),"TB",1,'L',0);
  $pdf->SetXY($set_x+24,$set_y+$linea);
  $pdf->Cell(245,5*$s,utf8_decode($descripcion),"TB",1,'L',0);
  $cc = (5 * $s);
  $linea += (5*$s);
  $linea_acumulada += $linea;
  if($j == 1)
  {
    $pdf->SetXY(4, 210);
    $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
    $pdf->SetXY(260, 210);
    $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
}
}
$pdf->Line($set_x,$set_y+$linea,$set_x+270,$set_y+$linea);
$pdf->SetXY($set_x, $set_y+$linea);
$pdf->Cell(245,5,utf8_decode("TOTAL"),0,1,'L',0);
$pdf->SetXY($set_x+245, $set_y+$linea);
$pdf->Cell(25,5,utf8_decode("$".number_format($total_general, 2)),0,1,'R',0);
}





ob_clean();
$pdf->Output("reporte_valoracion_inventario.pdf","I");
