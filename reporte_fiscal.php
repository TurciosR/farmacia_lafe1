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

$min = $_REQUEST["l"];
$fini = ($_REQUEST["fini"]);
$fin = ($_REQUEST["ffin"]);
$fini1 = $_REQUEST["fini"];
$fin1 = $_REQUEST["ffin"];
$logo = "img/logo_sys.png";
$impress = "Impreso: ".date("d/m/Y");
$title = $nombre_a;
$titulo = "REPORTE FISCAL";
if($fini!="" && $fin!="")
{
    list($a,$m,$d) = explode("-", $fini);
    list($a1,$m1,$d1) = explode("-", $fin);
    if($a ==$a1)
    {
        if($m==$m1)
        {
            $fech="DEL $d AL $d1 DE ".meses($m)." DE $a";
        }
        else
        {
            $fech="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
        }
    }
    else
    {
        $fech="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
    }
}


$existenas = "";
if($min>0)
{
    $existenas = "CANTIDAD: $min";
}

$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->Image($logo,8,4,30,25);
$set_x = 5;
$set_y = 6;

    //Encabezado General
    //Encabezado General
$pdf->SetFont('Arial','',16);
$pdf->SetXY($set_x, $set_y);
$pdf->MultiCell(220,6,$title,0,'C',0);
$pdf->SetXY($set_x, $set_y+11);
$pdf->SetFont('Arial','',8);
$pdf->Cell(220,5,utf8_decode(ucwords(Minu("Depto. ".utf8_decode($departamento)))),0,1,'C');
$pdf->SetXY($set_x+68, $set_y+5);
$pdf->MultiCell(85,3.5,str_replace(" Y ", " y ",ucwords(utf8_decode(Minu($direccion))))."",0,'C',0);
$pdf->SetXY($set_x, $set_y+14);
$pdf->Cell(220,5,Mayu("PBX: ".$telefono1." / ".$telefono2),0,1,'C');
$plus = 0;
$pdf->SetXY($set_x, $set_y+17-$plus);
$pdf->Cell(220,5,utf8_decode(ucwords("WhatsApp: ").$whatsapp),0,1,'C');
$pdf->SetXY($set_x, $set_y+20-$plus);
$pdf->Cell(220,5,utf8_decode("E-mail: ".$email),0,1,'C');
$pdf->SetXY($set_x, $set_y+25);
$pdf->Cell(220,6,utf8_decode($titulo),0,1,'C');
$pdf->SetXY($set_x, $set_y+28);
$pdf->Cell(220,6,$fech,0,1,'C');

    $set_y = 40;
    $set_x = 5;
    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Arial','',8);
    $pdf->SetXY($set_x, $set_y);
    $pdf->Cell(19,10,utf8_decode("FECHA"),0,1,'C',0);
    $pdf->SetXY($set_x+19, $set_y);
    $pdf->Cell(19,10,utf8_decode("SUCURSAL"),0,1,'C',0);
    $pdf->SetXY($set_x+38, $set_y);
    $pdf->Cell(49,5,utf8_decode("TIQUETE"),0,1,'C',0);
    $pdf->SetXY($set_x+87, $set_y);
    $pdf->Cell(49,5,"FACTURA",0,1,'C',0);
    $pdf->SetXY($set_x+136, $set_y);
    $pdf->Cell(49,5,"CREDITO FISCAL",0,1,'C',0);
    $pdf->SetXY($set_x+185, $set_y);
    $pdf->MultiCell(19,5,"TOTAL GENERAL",0,'C',0);
    /////////////////////////////////////////////////////////
    /////////////////
    $pdf->SetXY($set_x+38, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("INICIO"),0,1,'C',0);
    $pdf->SetXY($set_x+54.3, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("FIN"),0,1,'C',0);
    $pdf->SetXY($set_x+70.6, $set_y+5);
    $pdf->Cell(16.4,5,"TOTAL",0,1,'C',0);
    //////////////////
    $pdf->SetXY($set_x+87, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("INICIO"),0,1,'C',0);
    $pdf->SetXY($set_x+103.3, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("FIN"),0,1,'C',0);
    $pdf->SetXY($set_x+119.6, $set_y+5);
    $pdf->Cell(16.4,5,"TOTAL",0,1,'C',0);
    //////////////////
    $pdf->SetXY($set_x+136, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("INICIO"),0,1,'C',0);
    $pdf->SetXY($set_x+152.3, $set_y+5);
    $pdf->Cell(16.3,5,utf8_decode("FIN"),0,1,'C',0);
    $pdf->SetXY($set_x+168.6, $set_y+5);
    $pdf->Cell(16.4,5,"TOTAL",0,1,'C',0);
    $pdf->Line($set_x,$set_y+10,$set_x+205,$set_y+10);
    //$pdf->SetTextColor(0,0,0);
    $set_y = 50;
    $page = 0;
    $j=0;
    $mm = 0;
    $i = 1;
    $fk = $fini1;
    $fs = 1;
    $f1 = 0;
    while(strtotime($fk) <= strtotime($fin1))
    {
        if($page==0)
            $salto = 43;
        else
            $salto = 51;
        if($j==$salto)
        {
            $page++;
            $pdf->AddPage();
            //$pdf->SetFont('Latin','',10);
            //$pdf->Image($logo,9,4,50,18);
            //$pdf->Image($logo1,245,8,24.5,24.5);
            $set_y = 6;
            $f1=0;
            $set_x = 5;
            $mm=0;
            //Encabezado General
            $j=0;
            //$pdf->SetFont('Latin','',8);
        }
        $fk = ($fk);
        $sql_efectivo = _query("SELECT * FROM factura WHERE fecha = '$fk' AND id_sucursal = '$id_sucursal'");
        $cuenta = _num_rows($sql_efectivo);
        $sql_min_max=_query("SELECT MIN(numero_doc) as minimo, MAX(numero_doc) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%TIK%' AND id_sucursal = 1 AND anulada = 0 UNION ALL SELECT MIN(numero_doc) as minimo, MAX(numero_doc) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%COF%' AND id_sucursal = 1 AND anulada = 0 UNION ALL SELECT MIN(numero_doc) as minimo, MAX(numero_doc) as maximo FROM factura WHERE fecha = '$fk' AND numero_doc LIKE '%CCF%' AND id_sucursal = 1 AND anulada = 0");
        $cuenta_min_max = _num_rows($sql_min_max);
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $total_tike_e = 0;
        $total_factura_e = 0;
        $total_credito_fiscal_e = 0;
        $total_reserva_e = 0;
        $total_dev_e = 0;
        $total_tike_g = 0;
        $total_factura_g = 0;
        $total_credito_fiscal_g = 0;
        $total_reserva_g = 0;
        $total_dev_g = 0;
        $total_tike = 0;
        $total_factura = 0;
        $total_credito_fiscal = 0;
        $tike_min = 0;
        $tike_max = 0;
        $factura_min = 0;
        $factura_max = 0;
        $credito_fiscal_min = 0;
        $credito_fiscal_max = 0;
        $dev_min = 0;
        $dev_max = 0;
        $res_min = 0;
        $res_max = 0;
        $t_tike = 0;
        $t_factuta = 0;
        $t_credito = 0;
        $t_dev = 0;
        $t_res = 0;
        $t_recerva = 0;
        $total_contado = 0;
        $total_tarjeta = 0;
        $lista_dev = "";
        if($cuenta > 0)
        {
            while ($row_corte = _fetch_array($sql_efectivo))
            {
                $id_factura = $row_corte["id_factura"];
                $anulada = $row_corte["anulada"];
                $subtotal = $row_corte["subtotal"];
                $suma = $row_corte["suma"];
                $iva = $row_corte["iva"];
                $total = $row_corte["total"];
                $numero_doc = $row_corte["numero_doc"];
                $ax = explode("_", $numero_doc);
                $numero_co = $ax[0];
                $alias_tipodoc = $ax[1];
                $tipo_pago = $row_corte["tipopago"];
                $total_iva = $row_corte["total_iva"];
                $total = $row_corte["total"];

                if($alias_tipodoc == 'TIK')
                {
                    $total_tike += $total;
                }
                else if($alias_tipodoc == 'COF')
                {
                    $total_factura += $total;
                }
                else if($alias_tipodoc == 'CCF')
                {
                    $total_credito_fiscal += $total;
                }




            }
        }

        if($cuenta_min_max)
        {
            $i = 1;
            while ($row_min_max = _fetch_array($sql_min_max))
            {
                if($i == 1)
                {
                    $tike_min = $row_min_max["minimo"];
                    $tike_max = $row_min_max["maximo"];
                    list($minimo_num,$ads) = explode("_", $tike_min);
                    list($maximo_num,$ads) = explode("_", $tike_max);
                    if($tike_min > 0)
                    {
                        $tike_min = $minimo_num;
                    }
                    else
                    {
                        $tike_min = 0;
                    }

                    if($tike_max > 0)
                    {
                        $tike_max = $maximo_num;
                    }
                    else
                    {
                        $tike_max = 0;
                    }
                }
                if($i == 2)
                {
                    $factura_min = $row_min_max["minimo"];
                    $factura_max = $row_min_max["maximo"];
                    list($minimo_num,$ads) = explode("_", $factura_min);
                    list($maximo_num,$ads) = explode("_", $factura_max);
                    if($factura_min != "")
                    {
                        $factura_min = $minimo_num;
                    }
                    else
                    {
                        $factura_min = 0;
                    }

                    if($factura_max != "")
                    {
                        $factura_max = $maximo_num;
                    }
                    else
                    {
                        $factura_max = 0;
                    }
                }
                if($i == 3)
                {
                    $credito_fiscal_min = $row_min_max["minimo"];
                    $credito_fiscal_max = $row_min_max["maximo"];
                    $mi = explode("_", $credito_fiscal_min);
                    $minimo_num = $mi[0];
                    $max = explode("_", $credito_fiscal_max);
                    $maximo_num = $max[0];
                    if($credito_fiscal_min != "")
                    {
                        $credito_fiscal_min = $minimo_num;
                    }
                    else
                    {
                        $credito_fiscal_min = 0;
                    }

                    if($credito_fiscal_max != "")
                    {
                        $credito_fiscal_max = $maximo_num;
                    }
                    else
                    {
                        $credito_fiscal_max = 0;
                    }
                }
                $i += 1;
            }
        }
        /*$total_tike = $total_tike_e + $total_tike_g;
        $total_factura = $total_factura_e + $total_factura_g;
        $total_credito_fiscal = $total_credito_fiscal_e + $total_credito_fiscal_g;*/
        $total_general = $total_tike + $total_factura + $total_credito_fiscal;
        $fk = ED($fk);
        $pdf->SetXY($set_x, $set_y+$f1);
        $pdf->Cell(19,5,utf8_decode($fk),0,1,'C',0);
        $pdf->SetXY($set_x+19, $set_y+$f1);
        $pdf->Cell(19,5,utf8_decode($id_sucursal),0,1,'C',0);
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetXY($set_x+38, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode(intval($tike_min)),0,1,'C',0);
        $pdf->SetXY($set_x+54.3, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode(intval($tike_max)),0,1,'C',0);
        $pdf->SetXY($set_x+70.6, $set_y+$f1);
        $pdf->Cell(16.4,5,number_format($total_tike, 2),0,1,'R',0);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetXY($set_x+87, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode(intval($factura_min)),0,1,'C',0);
        $pdf->SetXY($set_x+103.3, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode(intval($factura_max)),0,1,'C',0);
        $pdf->SetXY($set_x+119.6, $set_y+$f1);
        $pdf->Cell(16.4,5,number_format($total_factura, 2),0,1,'R',0);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetXY($set_x+136, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode(intval($credito_fiscal_min)),0,1,'C',0);
        $pdf->SetXY($set_x+152.3, $set_y+$f1);
        $pdf->Cell(16.3,5,utf8_decode(intval($credito_fiscal_max)),0,1,'C',0);
        $pdf->SetXY($set_x+168.6, $set_y+$f1);
        $pdf->Cell(16.4,5,number_format($total_credito_fiscal,2),0,1,'R',0);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $pdf->SetXY($set_x+185, $set_y+$f1);
        $pdf->Cell(19,5,number_format($total_general, 2),0,1,'R',0);
        /////////////////////////////////
        $fk = sumar_dias($fk,1);

        $fk = MD($fk);
        $f1+=5;
        $fs += 1;
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

    ob_clean();
    $pdf->Output("reporte_fiscal.pdf","I");
