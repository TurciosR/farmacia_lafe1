<?php
require('_core.php');
require('fpdf/fpdf.php');

$pdf=new fPDF('P','mm', 'Letter');
$pdf->SetMargins(10,5);
$pdf->SetTopMargin(2);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,1);
$pdf->AddFont("latin","","latin.php");
$id_sucursalr = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursalr'";
$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$empresa = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
$sucursal = "SUCURSAL: ".utf8_decode(Mayu(utf8_decode(trim($row_emp["direccion"]))));
$tel1 = $row_emp['telefono1'];
$tel2 = $row_emp['telefono2'];
if($tel1 != "")
{
  if($tel2 !="")
  {
    $telefonos="TEL. ".$tel1.", ".$tel2;
  }
  else
  {
    $telefonos="TEL. ".$tel1;
  }
}
else
{
  if($tel2 !="")
  {
    $telefonos="TEL. ".$tel2;
  }
  else
  {
    $telefonos="";
  }
}
$logo = "img/logo_sys.jpg";
$impress = "Impreso: ".date("d/m/Y");

$id_cotizacion=$_REQUEST['id_cotizacion'];
$sql="SELECT co.fecha, co.total, co.numero_doc, co.vigencia, c.nombre as cliente, e.nombre as vendedor, u.nombre as empleado
FROM cotizacion AS co
JOIN cliente AS c ON c.id_cliente=co.id_cliente
JOIN empleado AS e ON e.id_empleado=co.id_vendedor
JOIN usuario AS u ON u.id_usuario=co.id_empleado
WHERE co.id_cotizacion='$id_cotizacion'";

$result=_query($sql);
$row=_fetch_array($result);

$fecha=$row['fecha'];
$total=$row['total'];
$numero_doc=$row['numero_doc'];
$cliente=$row['cliente'];
$vendedor=$row['vendedor'];
$empleado=$row['empleado'];
$vigencia=$row['vigencia'];

$titulo=utf8_decode("COTIZACIÓN: ").$numero_doc;

$pdf->AddPage();
$pdf->SetFont('Latin','',10);
$pdf->Image($logo,9,4,50,18);
$set_x = 0;
$set_y = 6;
//Encabezado General
$pdf->SetFont('Latin','',12);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(220,6,$empresa,0,1,'C');
$pdf->SetFont('Latin','',10);
$pdf->SetXY($set_x, $set_y+5);
$pdf->Cell(220,6,$sucursal,0,1,'C');
$pdf->SetXY($set_x, $set_y+10);
$pdf->Cell(220,6,$telefonos,0,1,'C');
$pdf->SetXY($set_x, $set_y+15);
$pdf->Cell(220,6,$titulo,0,1,'C');

$set_x = 10;
$pdf->SetXY($set_x, $set_y+30);
$pdf->Cell(220,6,"CLIENTE: ".utf8_decode(Mayu(utf8_decode($cliente))),0,1,'L');
$pdf->SetXY($set_x, $set_y+35);
$pdf->Cell(220,6,"VENDEDOR: ".utf8_decode(Mayu(utf8_decode($vendedor))),0,1,'L');
$pdf->SetXY($set_x, $set_y+40);
$pdf->Cell(220,6,"FECHA: ".ED($fecha),0,1,'L');

$set_y = 55;
$set_x = 10;
//$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Latin','',8);
$pdf->SetXY($set_x, $set_y);
$pdf->Cell(20,5,"CANTIDAD",1,1,'C',0);
$pdf->SetXY($set_x+20, $set_y);
$pdf->Cell(70,5,"DETALLE",1,1,'C',0);
$pdf->SetXY($set_x+90, $set_y);
$pdf->Cell(30,5,utf8_decode("PRESENTACIÓN"),1,1,'C',0);
$pdf->SetXY($set_x+120, $set_y);
$pdf->Cell(30,5,utf8_decode("DESCRIPCIÓN"),1,1,'C',0);
$pdf->SetXY($set_x+150, $set_y);
$pdf->Cell(20,5,"PRECIO",1,1,'C',0);
$pdf->SetXY($set_x+170, $set_y);
$pdf->Cell(25,5,"SUBTOTAL",1,1,'C',0);
//$pdf->SetTextColor(0,0,0);
$set_y = 60;
$page = 0;
$j=0;
$mm = 0;
$i = 0;
$subtt = 0;
$result1 = _query("SELECT dc.id_prod_serv, pr.descripcion, dc.cantidad, dc.precio_venta, dc.id_presentacion, dc.subtotal FROM cotizacion_detalle as dc, producto as pr WHERE pr.id_producto=dc.id_prod_serv AND dc.id_cotizacion='$id_cotizacion'");
if(_num_rows($result1)>0)
{
  while($row = _fetch_array($result1))
  {
    if($page==0)
    $salto = 40;
    else
    $salto = 46;
    if($j==$salto)
    {
      $page++;
      $pdf->AddPage();
      $pdf->SetFont('Latin','',10);
      $pdf->Image($logo,9,4,50,18);
      $set_x = 0;
      $set_y = 6;
      //Encabezado General
      $pdf->SetFont('Latin','',12);
      $pdf->SetXY($set_x, $set_y);
      $pdf->Cell(220,6,$empresa,0,1,'C');
      $pdf->SetFont('Latin','',10);
      $pdf->SetXY($set_x, $set_y+5);
      $pdf->Cell(220,6,$sucursal,0,1,'C');
      $pdf->SetXY($set_x, $set_y+10);
      $pdf->Cell(220,6,$telefonos,0,1,'C');
      $pdf->SetXY($set_x, $set_y+15);
      $pdf->Cell(220,6,$titulo,0,1,'C');
      $mm = 0;
      $i = 0;
    }
    $id_producto = $row["id_prod_serv"];
    $cantidad_s = $row["cantidad"];
    $subt_mostrar = $row["subtotal"];
    $subtt += $subt_mostrar;
    $precio_venta = $row["precio_venta"];
    $id_presentacion = $row["id_presentacion"];
    $descripcion = utf8_decode(Mayu(utf8_decode($row["descripcion"])));

    $sql_p=_query("SELECT presentacion.nombre, presentacion_producto.descripcion,presentacion_producto.id_presentacion,presentacion_producto.unidad,presentacion_producto.precio FROM presentacion_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_presentacion='$id_presentacion' AND presentacion_producto.activo=1");
    $row2=_fetch_array($sql_p);
    $presentacion = utf8_decode(Mayu(utf8_decode($row2['nombre'])));
    $descripcionp = utf8_decode(Mayu(utf8_decode($row2['descripcion'])));

      $pdf->SetFont('Latin','',8);
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(20,5,$cantidad_s,1,0,'C',0);
      $pdf->SetXY($set_x+20, $set_y+$mm);
      $pdf->Cell(70,5,$descripcion,1,0,'L',0);
      $pdf->SetXY($set_x+90, $set_y+$mm);
      $pdf->Cell(30,5,$presentacion,1,0,'C',0);
      $pdf->SetXY($set_x+120, $set_y+$mm);
      $pdf->Cell(30,5,$descripcionp,1,0,'C',0);
      $pdf->SetXY($set_x+150, $set_y+$mm);
      $pdf->Cell(20,5,"$".number_format($precio_venta,2,".",","),1,0,'C',0);
      $pdf->SetXY($set_x+170, $set_y+$mm);
      $pdf->Cell(25,5,"$".number_format($subt_mostrar,2,".",","),1,0,'C',0);
      $mm += 5;
      $i++;
      $j++;
      if($j==1)
      {
        //Fecha de impresion y numero de pagina
        $pdf->SetXY(4, 270);
        $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
        $pdf->SetXY(193, 270);
        $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
      }
    }
    $rest = 25 - $i;
    if($rest>0)
    {
      for($n=0; $n<$rest; $n++)
      {
        $pdf->SetFont('Latin','',8);
        $pdf->SetXY($set_x, $set_y+$mm);
        $pdf->Cell(20,5,"",1,0,'C',0);
        $pdf->SetXY($set_x+20, $set_y+$mm);
        $pdf->Cell(70,5,"",1,0,'L',0);
        $pdf->SetXY($set_x+90, $set_y+$mm);
        $pdf->Cell(30,5,"",1,0,'C',0);
        $pdf->SetXY($set_x+120, $set_y+$mm);
        $pdf->Cell(30,5,"",1,0,'C',0);
        $pdf->SetXY($set_x+150, $set_y+$mm);
        $pdf->Cell(20,5,"",1,0,'C',0);
        $pdf->SetXY($set_x+170, $set_y+$mm);
        $pdf->Cell(25,5,"",1,0,'C',0);
        $mm += 5;
      }
    }
      $pdf->SetFont('Latin','',8);
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(170,5,"TOTAL",1,1,'C',0);
      $pdf->SetXY($set_x+170, $set_y+$mm);
      $pdf->Cell(25,5,"$".number_format($subtt,2,".",","),1,1,'C',0);
      $mm += 15;
      $pdf->SetFont('Latin','',10);
      $pdf->SetXY($set_x, $set_y+$mm);
      $pdf->Cell(195,5,"Precios Incluyen IVA",0,0,'L',0);
      $pdf->SetXY($set_x, $set_y+$mm+5);
      $pdf->Cell(195,5,"**** Oferta valida por ".$vigencia." dias ****",0,0,'L',0);

  }
ob_clean();
$pdf->Output($numero_doc.".pdf", "I");
