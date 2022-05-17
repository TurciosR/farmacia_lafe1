<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');


$pdf=new fPDF('P','mm', 'Letter');
$pdf->SetMargins(10,5);
$pdf->SetTopMargin(2);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true,1);
$pdf->AddFont("latin","","latin.php");
$id_sucursalr = $_SESSION["id_sucursal"];
$id_pedido = $_REQUEST["id_pedido"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursalr'";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
$tel1 = $row_emp['telefono1'];
$n_sucursal = $row_emp['n_sucursal'];
$tel2 = $row_emp['telefono2'];
$direccion = $row_emp['direccion'];
$telefonos="TEL. ".$tel1." y ".$tel2;

    $id_sucursal = $_REQUEST["id_sucursal"];
    $min = $_REQUEST["min"];
    $max = $_REQUEST["max"];
    $logo = "img/logo_sys.png";
    $impress = "Impreso: ".date("d/m/Y");
    $titulo = "REPORTE DE PEDIDO";
    $fech =date("d")." DE ".utf8_decode(Mayu(utf8_decode(meses(date("m")))))." DEL ".date("Y");
    $pdf->AddPage();
    $pdf->SetFont('Latin','',10);
    $pdf->Image($logo,9,4,50,18);
    //$pdf->Image($logob,160,4,50,15);
    $set_x = 0;
    $set_y = 6;

    //Encabezado General
    $pdf->SetFont('Latin','',12);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(220,6,$nombre_a,0,1,'C');
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y+5);
    $pdf->Cell(220,6,"SUCURSAL ".$n_sucursal.": ".$direccion,0,1,'C');
    $pdf->SetXY($set_x, $set_y+10);
    $pdf->Cell(220,6,utf8_decode($telefonos),0,1,'C');
    $pdf->SetXY($set_x, $set_y+15);
    $pdf->Cell(220,6,$titulo,0,1,'C');
    $pdf->SetXY($set_x, $set_y+25);
    $pdf->Cell(220,6,$fech,0,1,'C');
//Finaliza el encabezado
//Comienza los datos general del pedido
$total_p=_fetch_array(_query("SELECT cliente.nombre, pedido.fecha, pedido.fecha_entrega, pedido.lugar_entrega, pedido.numero, pedido.total
  FROM pedido, cliente
  WHERE pedido.id_cliente=cliente.id_cliente AND pedido.id_pedido='$id_pedido' AND pedido.id_sucursal='$id_sucursalr'
  "));
  $total=$total_p['total'];
  $cliente=$total_p['nombre'];
  $fecha=$total_p['fecha'];
  $fecha_e=$total_p['fecha_entrega'];
  $lugar=$total_p['lugar_entrega'];
  $numero_p=$total_p['numero'];
  $set_y = 18;
  $set_x = 20;
  $pdf->SetXY($set_x+63, $set_y+9);
  $pdf->Cell(50,5,utf8_decode("PEDIDO: ").$numero_p."",0,1,'C',0);
  $pdf->SetFont('Latin','',10);
  $pdf->SetXY($set_x-5.5, $set_y+20);
  $pdf->Cell(10,5,'Cliente:',0,1,'C',0);
  $pdf->SetXY($set_x+12, $set_y+20);
  $pdf->Cell(35,5,utf8_decode($cliente."."),0,1,'C',0);
  $pdf->SetXY($set_x, $set_y+25);
  $pdf->Cell(10,5,utf8_decode('Fecha creación:'),0,1,'C',0);
  $pdf->SetXY($set_x+21, $set_y+25);
  $pdf->Cell(20,5,utf8_decode($fecha."."),0,1,'C',0);
  $pdf->SetXY($set_x-0.5, $set_y+30);
  $pdf->Cell(10,5,utf8_decode('Fecha entrega:'),0,1,'C',0);
  $pdf->SetXY($set_x+20.5, $set_y+30);
  $pdf->Cell(20,5,utf8_decode($fecha_e."."),0,1,'C',0);
  $pdf->SetXY($set_x-0.5, $set_y+35);
  $pdf->Cell(10,5,utf8_decode('Lugar entrega:'),0,1,'C',0);
  $pdf->SetXY($set_x+16, $set_y+35);
  $pdf->Cell(20,5,utf8_decode($lugar."."),0,1,'C',0);

    $set_y = 59;
    $set_x = 13;
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Latin','',10);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(10,5,utf8_decode("Id"),1,1,'C',0);
    $pdf->SetXY($set_x+10, $set_y);
    $pdf->Cell(55,5,"Producto",1,1,'L',0);
    $pdf->SetXY($set_x+65, $set_y);
    $pdf->Cell(23,5,utf8_decode("Presentación"),1,1,'C',0);
    $pdf->SetXY($set_x+88, $set_y);
    $pdf->Cell(24,5,utf8_decode("Descripción"),1,1,'C',0);
    $pdf->SetXY($set_x+112, $set_y);
    $pdf->Cell(20,5,"Precio V.",1,1,'C',0);
    $pdf->SetXY($set_x+132, $set_y);
    $pdf->Cell(19,5,"Cantidad P.",1,1,'C',0);
    $pdf->SetXY($set_x+151, $set_y);
    $pdf->Cell(19,5,"Cantidad E.",1,1,'C',0);
    $pdf->SetXY($set_x+170, $set_y);
    $pdf->Cell(16,5,"Sub T.",1,1,'C',0);
    //$pdf->SetTextColor(0,0,0);
    $set_y = 40;
    $page = 0;
    $j=0;
    $mm = 0;
    $i = 1;
    $sql1 =_query("SELECT producto.id_producto, producto.descripcion AS producto, presentacion.nombre,presentacion_producto.id_presentacion ,presentacion_producto.descripcion, presentacion_producto.unidad ,pedido_detalle.id_pedido_detalle,pedido_detalle.precio_venta, pedido_detalle.cantidad, pedido_detalle.cantidad_enviar ,pedido_detalle.subtotal, stock.stock
      FROM pedido_detalle
      JOIN producto ON (pedido_detalle.id_producto=producto.id_producto)
      JOIN presentacion_producto ON (pedido_detalle.id_presentacion=presentacion_producto.id_presentacion)
      JOIN presentacion ON (presentacion_producto.presentacion=presentacion.id_presentacion)
      JOIN stock ON (pedido_detalle.id_producto=stock.id_producto)
      WHERE pedido_detalle.id_pedido='$id_pedido'");
    if(_num_rows($sql1)>0)
    {   $m=5;
      $cantP=0;
      $total=0;
        while($row = _fetch_array($sql1))
        {
            if($page==0)
                $salto = 45;
            else
                $salto = 46;
            if($j==$salto)
            {
                $page++;
                $pdf->AddPage();
                $pdf->SetFont('Latin','',10);
                $pdf->Image($logo,9,4,50,18);
                //$pdf->Image($logo1,245,8,24.5,24.5);
                $set_x = 0;
                $set_y = 6;
                $mm=5;
                //Encabezado General
                $pdf->SetFont('Latin','',12);
                $pdf->SetXY($set_x, $set_y);
                $pdf->Cell(220,6,$nombre_a,0,1,'C');
                $pdf->SetFont('Latin','',10);
                $pdf->SetXY($set_x, $set_y+5);
                $pdf->Cell(220,6,$telefonos,0,1,'C');
                $pdf->SetXY($set_x, $set_y+10);
                $pdf->Cell(220,6,utf8_decode($titulo),0,1,'C');
                $pdf->SetXY($set_x, $set_y+15);
                $pdf->Cell(220,6,$direccion,0,1,'C');
                $pdf->SetXY($set_x, $set_y+20);
                $pdf->Cell(220,6,$fech,0,1,'C');
                $set_x = 5;
                $set_y = 35;
                $j=0;
                $pdf->SetFont('Latin','',8);
            }
            $set_y = 59;
            $set_x = 13;
            $id_producto = $row["id_producto"];
            $producto = utf8_decode($row["producto"]);
            $presentacion = utf8_decode(ucwords(strtolower($row["nombre"])));
            $descripcion = $row["descripcion"];
            $precio = $row["precio_venta"];
            $cantidad = $row["cantidad"];
            $cantidad_e = $row["cantidad_enviar"];
            $subtotal = $row["subtotal"];
            $pdf->SetFont('Latin','',10);
            $pdf->SetXY($set_x, $set_y+$m);
            $pdf->Cell(10,5,$id_producto,1,1,'C',0);
            $pdf->SetXY($set_x+10, $set_y+$m);
            $pdf->Cell(55,5,utf8_decode($producto),1,1,'L',0);
            $pdf->SetXY($set_x+65, $set_y+$m);
            $pdf->Cell(23,5,utf8_decode($presentacion),1,1,'C',0);
            $pdf->SetXY($set_x+88, $set_y+$m);
            $pdf->Cell(24,5,utf8_decode($descripcion),1,1,'C',0);
            $pdf->SetXY($set_x+112, $set_y+$m);
            $pdf->Cell(20,5,$precio,1,1,'C',0);
            $pdf->SetXY($set_x+132, $set_y+$m);
            $pdf->Cell(19,5,$cantidad,1,1,'C',0);
            $pdf->SetXY($set_x+151, $set_y+$m);
            $pdf->Cell(19,5,$cantidad_e,1,1,'C',0);
            $pdf->SetXY($set_x+170, $set_y+$m);
            $pdf->Cell(16,5,"$".number_format($subtotal,2,".",","),1,1,'C',0);
            $mm += 5;
            $m+=5;
            $i++;
            $j++;
            $cantP+=$cantidad_e;
            $total+=$subtotal;
            if($j==1)
            {
                //Fecha de impresion y numero de pagina
                $pdf->SetXY(4, 270);
                $pdf->Cell(10, 0.4,$impress, 0, 0, 'L');
                $pdf->SetXY(193, 270);
                $pdf->Cell(20, 0.4, 'Pag. '.$pdf->PageNo().' de {nb}', 0, 0, 'R');
            }
        }
        $pdf->SetFont('Latin','',10);
        $pdf->SetXY($set_x, $set_y+$m);
        $pdf->Cell(151,5,"Total",1,1,'C',0);
        $pdf->SetXY($set_x+151, $set_y+$m);
        $pdf->Cell(19,5,$cantP,1,1,'C',0);
        $pdf->SetXY($set_x+170, $set_y+$m);
        $pdf->Cell(16,5,"$".number_format($total,2,".",","),1,1,'C',0);
    }
ob_clean();
$pdf->Output("reporte_pedido.pdf","I");
