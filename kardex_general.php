<?php
error_reporting(E_ERROR | E_PARSE);

require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');

$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nit = $row_emp['nit'];
$nrc = $row_emp['nrc'];
$razonsocial = $row_emp['razonsocial'];
$descripcion = $row_emp['descripcion'];
$giro = $row_emp['giro'];
$whatsapp=$row_emp["whatsapp"];
$email=$row_emp["email"];
$depa = $row_emp["id_departamento"];
$muni = $row_emp["id_municipio"];
$telefono1 = $row_emp["telefono1"];
$telefono2 = $row_emp["telefono2"];
$logo = $row_emp["logo"];
$fecha_r = ($_REQUEST["fecha"]);
$turno_r = $_REQUEST["turno"];
$nombre_a = utf8_decode(Mayu((trim($row_emp["descripcion"]))));
//$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
$direccion = utf8_decode(Mayu((trim($row_emp["direccion"]))));
$sql2 = _query("SELECT dep.* FROM departamento as dep WHERE dep.id_departamento='$depa'");
$row2 = _fetch_array($sql2);
$departamento = $row2["nombre_departamento"];

$sql3 = _query("SELECT mun.* FROM municipio as mun WHERE dep.id_municipio='$muni'");
$row3 = _fetch_array($sql3);
$municipio = $row3["nombre_municipio"];

    $id_producto = $_REQUEST["id_producto"];
    $fini = ($_REQUEST["fini"]);
    $fin = ($_REQUEST["fin"]);
    $logo = "img/logo_sys.png";
    $impress = "Impreso: ".date("d/m/Y");
    $title = $descripcion;
    $titulo = "KARDEX GENERAL DE PRODUCTOS";
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


class PDF extends FPDF
{
    var $a;
    var $b;
    var $c;
    var $d;
    var $e;
    var $f;
    // Cabecera de página\
    public function Header()
    {

      //$this->Image($this->a,9,4,50,18);
      //$pdf->Image($logob,160,4,50,15);

      //Encabezado General
      if ($this->PageNo()!=1) {
        // code...
        $set_y =$this-> GetY();
        $set_x =$this->GetX();

        $this->SetFont('latin','',8);
        $this->SetXY($set_x, $set_y);
        $this->Cell(18,10,"FECHA",1,1,'C',0);
        $this->SetXY($set_x+18, $set_y);
        $this->Cell(18,10,"TIPO DOC",1,1,'C',0);
        $this->SetXY($set_x+36, $set_y);
        $this->Cell(18,10,"NUM. DOC",1,1,'C',0);
        $this->SetXY($set_x+110, $set_y);
        $this->Cell(54,5,"ENTRADA",1,1,'C',0);
        $this->SetXY($set_x+110, $set_y+5);
        $this->Cell(18,5,"CANTIDAD",1,1,'C',0);
        $this->SetXY($set_x+128, $set_y+5);
        $this->Cell(18,5,"COSTO",1,1,'C',0);
        $this->SetXY($set_x+146, $set_y+5);
        $this->Cell(18,5,"SUBTOTAL",1,1,'C',0);
        $this->SetXY($set_x+164, $set_y);
        $this->Cell(54,5,"SALIDA",1,1,'C',0);
        $this->SetXY($set_x+164, $set_y+5);
        $this->Cell(18,5,"CANTIDAD",1,1,'C',0);
        $this->SetXY($set_x+182, $set_y+5);
        $this->Cell(18,5,"COSTO",1,1,'C',0);
        $this->SetXY($set_x+200, $set_y+5);
        $this->Cell(18,5,"SUBTOTAL",1,1,'C',0);
        $this->SetXY($set_x+218, $set_y);
        $this->Cell(54,5,"SALDO",1,1,'C',0);
        $this->SetXY($set_x+218, $set_y+5);
        $this->Cell(18,5,"CANTIDAD",1,1,'C',0);
        $this->SetXY($set_x+236, $set_y+5);
        $this->Cell(18,5,"COSTO",1,1,'C',0);
        $this->SetXY($set_x+254, $set_y+5);
        $this->Cell(18,5,"SUBTOTAL",1,1,'C',0);
        $this->SetXY($set_x+54, $set_y);
        $this->Cell(56,10,"PROVEEDOR",1,1,'C',0);
      }


    }

    public function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // latin italic 8
        $this->SetFont('latin', '', 8);
        // Número de página requiere $pdf->AliasNbPages();
        //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
        $this-> Cell(40, 10, utf8_decode('Impreso: '.date('d/m/Y')), 0, 0, 'L');
        $this->Cell(235, 10, utf8_decode('Pag. ').$this->PageNo().' de {nb}', 0, 0, 'R');
    }
    public function setear($logo,$nombre_lab,$direccion,$departamento,$telefono1,$telefono2,$whatsapp,$email)
    {
      # code...
       # code...
      $this->a=$logo;
      $this->b=$nombre_lab;
      $this->c=$direccion;
      $this->d=$departamento;
      $this->e=$telefono1;
      $this->f=$telefono2;
      $this->g=$whatsapp;
      $this->h=$email;
    }
    public function cabecera($logo,$nombre_lab,$direccion,$departamento,$telefono1,$telefono2,$whatsapp,$email,$titulo,$fech)
    {
      $set_x = 35;
      $set_y = 5;
      $this->Image($logo,8,4,35,25);
      //Encabezado General
      $this->SetFont('latin','',16);
      $this->SetXY($set_x, $set_y);
      $this->Cell(220,5,utf8_decode($nombre_lab),0,1,'C');
      $this->SetXY($set_x, $set_y+11);
      $this->SetFont('latin','',8);
      $this->Cell(220,5,utf8_decode(ucwords(("Depto. ".utf8_decode($departamento)))),0,1,'C');
      $this->SetXY($set_x+68, $set_y+5);
      $this->MultiCell(85,3.5,str_replace(" Y ", " y ",ucwords(utf8_decode(($direccion))))."",0,'C',0);
      $this->SetXY($set_x, $set_y+14);
      $this->Cell(220,5,Mayu("PBX: ".$telefono1." / ".$telefono2),0,1,'C');
      $plus = 0;
      $this->SetXY($set_x, $set_y+17-$plus);
      $this->Cell(220,5,utf8_decode(ucwords("WhatsApp: ").$whatsapp),0,1,'C');
      $this->SetXY($set_x, $set_y+20-$plus);
      $this->Cell(220,5,utf8_decode("E-mail: ".$email),0,1,'C');

      $this->Cell(140,6,utf8_decode($titulo),0,1,'L');
      $this->Cell(140,6,$fech,0,1,'L');

    }
    public function otr()
    {

            $set_y =$this-> GetY();
            $set_x =$this->GetX();

            $this->SetFont('latin','',8);
            $this->SetXY($set_x, $set_y);
            $this->Cell(18,10,"FECHA",1,1,'C',0);
            $this->SetXY($set_x+18, $set_y);
            $this->Cell(18,10,"TIPO DOC",1,1,'C',0);
            $this->SetXY($set_x+36, $set_y);
            $this->Cell(18,10,"NUM. DOC",1,1,'C',0);
            $this->SetXY($set_x+110, $set_y);
            $this->Cell(54,5,"ENTRADA",1,1,'C',0);
            $this->SetXY($set_x+110, $set_y+5);
            $this->Cell(18,5,"CANTIDAD",1,1,'C',0);
            $this->SetXY($set_x+128, $set_y+5);
            $this->Cell(18,5,"COSTO",1,1,'C',0);
            $this->SetXY($set_x+146, $set_y+5);
            $this->Cell(18,5,"SUBTOTAL",1,1,'C',0);
            $this->SetXY($set_x+164, $set_y);
            $this->Cell(54,5,"SALIDA",1,1,'C',0);
            $this->SetXY($set_x+164, $set_y+5);
            $this->Cell(18,5,"CANTIDAD",1,1,'C',0);
            $this->SetXY($set_x+182, $set_y+5);
            $this->Cell(18,5,"COSTO",1,1,'C',0);
            $this->SetXY($set_x+200, $set_y+5);
            $this->Cell(18,5,"SUBTOTAL",1,1,'C',0);
            $this->SetXY($set_x+218, $set_y);
            $this->Cell(54,5,"SALDO",1,1,'C',0);
            $this->SetXY($set_x+218, $set_y+5);
            $this->Cell(18,5,"CANTIDAD",1,1,'C',0);
            $this->SetXY($set_x+236, $set_y+5);
            $this->Cell(18,5,"COSTO",1,1,'C',0);
            $this->SetXY($set_x+254, $set_y+5);
            $this->Cell(18,5,"SUBTOTAL",1,1,'C',0);
            $this->SetXY($set_x+54, $set_y);
            $this->Cell(56,10,"PROVEEDOR",1,1,'C',0);
    }
}


$pdf=new PDF('L','mm', 'Letter');
//$pdf->setear($logo,$title,$telefonos,$nit,$nrc,$titulo,$fech);
$pdf->SetMargins(4, 6);
$pdf->SetLeftMargin(4);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddFont("latin","","latin.php");


    $pdf->AddPage();
     $pdf->cabecera($logo,$nombre_a,$direccion,$departamento,$telefono1,$telefono2,$whatsapp,$email,$titulo,$fech);
    $pdf->otr();
    $pdf->SetFont('latin','',10);

    $sqla=_query("SELECT producto.id_producto FROM producto WHERE id_producto IN (SELECT movimiento_producto_detalle.id_producto FROM movimiento_producto join movimiento_producto_detalle on movimiento_producto_detalle.id_movimiento=movimiento_producto.id_movimiento where movimiento_producto_detalle.fecha BETWEEN '$fini' AND '$fin' OR movimiento_producto_detalle.fecha BETWEEN '$fini' AND '$fin')");
    //$pdf->SetTextColor(0,0,0);
    echo _error();
    while ($ra=_fetch_array($sqla)) {
      // code...
    $id_producto=$ra['id_producto'];


    $sql = "SELECT * FROM movimiento_producto_detalle as md, movimiento_producto as m
            WHERE md.id_movimiento=m.id_movimiento
            AND m.id_sucursal='$id_sucursal'
            AND md.id_producto='$id_producto'
            AND m.tipo!='ASIGNACION'
            AND m.tipo!='TRANSFERENCIA'
            AND CAST(m.fecha AS DATE) BETWEEN '$fini' AND '$fin' ORDER BY md.id_movimiento ASC";


    $sql_aux = _query("SELECT descripcion FROM producto  WHERE id_producto='$id_producto'");
    $dats_aux = _fetch_array($sql_aux);
    if ($pdf-> GetY()>188) {
      $pdf->AddPage();
    }

    //$pdf->SetFillColor(195, 195, 195);
    //$pdf->SetTextColor(255,255,255);
    $page = 0;
    $j=0;
    $mm = 0;
    $i = 0;


    $result = _query($sql);
    if(_num_rows($result)>0)
    {

      $pdf->Cell(100,5,utf8_decode($dats_aux["descripcion"]),0,1,'L',0);

      $set_y =$pdf-> GetY();
      $set_x =$pdf->GetX();
      $set_y =$pdf-> GetY();
      $set_x =$pdf->GetX();
      $pdf->SetXY($set_x,$set_y);
        $entrada = 0;
        $salida = 0;
        $init = 1;
        while($row = _fetch_array($result))
        {
            $fechadoc = ED($row["fecha"]);
            if($row["tipo"] == "ENTRADA" || $row["proceso"] =="TRR")
            {
              $csal = -1;
              $centr = $row["cantidad"];
              $entrada += $centr;
            }
            else if($row["tipo"] == "SALIDA" || $row["proceso"] =="TRE")
            {
              $centr = -1;
              $csal = $row["cantidad"];
              $salida += $csal;
            }

            if($row["tipo"] == "AJUSTE" && $row['id_presentacion']!=0)
            {
              $csal = -1;
              $centr = $row["cantidad"];
              $entrada += $centr;
            }
            else if($row["tipo"] == "AJUSTE")
            {
              $centr = -1;
              $csal = $row["cantidad"];
              $salida += $csal;
            }
            $id_presentacion = $row["id_presentacion"];
            $sql_pres = _query("SELECT unidad FROM presentacion_producto WHERE id_presentacion ='$id_presentacion'");

            $uniades=1;
            $cost=0;
            $id_compra=0;
            $id_factura=0;
            $id_cliente=0;
            while ($dats_pres = _fetch_array($sql_pres)) {
              // code...
              $uniades = $dats_pres["unidad"];
              $cost = $dats_pres["costo"];
              $id_compra = $row["id_compra"];
              $id_factura = $row["id_factura"];
              $id_cliente = "";
            }


            if($id_factura > 0)
            {
              $sql_comp = _query("SELECT tipo_documento, num_fact_impresa, id_cliente FROM factura WHERE id_factura='$id_factura'");
              $dats_comp = _fetch_array($sql_comp);
              $alias_tipodoc = $dats_comp["tipo_documento"];
              if($alias_tipodoc == "COF")
              {
                $alias_tipodoc = "FAC";
              }
              else
              {
                $alias_tipodoc = $alias_tipodoc;
              }
              $numero_doc = $dats_comp["num_fact_impresa"];
              $id_cliente = $dats_comp["id_cliente"];
            }
            if($id_compra > 0)
            {
              $sql_comp = _query("SELECT alias_tipodoc, numero_doc FROM compra WHERE id_compra='$id_compra'");
              $dats_comp = _fetch_array($sql_comp);
              $alias_tipodoc = $dats_comp["alias_tipodoc"];
              $numero_doc = $dats_comp["numero_doc"];
            }
            if($id_compra == 0 && $id_factura == 0)
            {
              $alias_tipodoc = $row['tipo'];
              $numero_doc = $row['correlativo'];
            }
            //$ultcosto = $row["costo"];//$uniades;
            $ultcosto = $row["costo"]/$uniades;
            $stock_actual = $row["stock_actual"];
            $stock_anterior = $row["stock_anterior"];
            $id_proveedor = $row["id_proveedor"];

            if($init)
      			{
              if($stock_anterior > 0)
              {
                $pdf->Cell(162,5,"INVENTARIO INICIAL",0,0,'C',0);
                $pdf->Cell(56,5,"",0,0,'C',0);
                $pdf->Cell(18,5,$stock_anterior,0,0,'C',0);
                $pdf->Cell(18,5,number_format($ultcosto,2,".",","),0,0,'C',0);
                $pdf->Cell(18,5,number_format(($stock_anterior * $ultcosto), 2),0,1,'C',0);

              }
              $init=0;
      			}

            $pdf->Cell(18,5,$fechadoc,0,0,'C',0);
            $pdf->Cell(18,5,$alias_tipodoc,0,0,'C',0);
            $pdf->Cell(18,5,$numero_doc,0,0,'C',0);

            if($id_proveedor>0)
            {
                $sql2 = _query("SELECT p.nombre, pa.nombre as pais FROM proveedor as p LEFT JOIN paises as pa ON(p.nacionalidad=pa.id) WHERE p.id_proveedor='".$id_proveedor."'");
                $datos2 = _fetch_array($sql2);
                $nombr = utf8_decode(trim($datos2["nombre"]));
                $nombr = $nombr." / ".utf8_decode($datos2["pais"])."";
                $ygg=1;
                if(ceil(strlen($nombr))/2 > 14)
                {
                    $nom = divtextlin($nombr, 27);
                    foreach ($nom as $nnon)
                    {
                      if ($ygg==1) {
                        $pdf->Cell(56,5,$nnon,0,0,'L',0);
                      }
                      $ygg++;
                    }

                }
                else
                {
                    $pdf->Cell(56,5,$nombr,0,0,'L',0);
                }
            }
            else {
              if($id_cliente>0)
              {
                  $sql2 = _query("SELECT nombre FROM cliente WHERE id_cliente='".$id_cliente."'");
                  $datos2 = _fetch_array($sql2);
                  $nombr = utf8_decode(trim($datos2["nombre"]));
                  $nombr = $nombr." / SV";
                  $ygg=1;
                  if(ceil(strlen($nombr))/2 > 14)
                  {
                      $nom = divtextlin($nombr, 27);

                      foreach ($nom as $nnon)
                      {
                        if ($ygg==1) {
                          $pdf->Cell(56,5,$nnon,0,0,'L',0);
                        }
                        $ygg++;
                      }

                  }
                  else
                  {
                      $pdf->Cell(56,5,$nombr,0,0,'L',0);
                  }
              }
              else
              {
                  $pdf->Cell(56,5,$row["concepto"],0,0,'L',0);
              }

            }



            if($centr >= 0)
            {
                $pdf->Cell(18,5,$centr,0,0,'C',0);
                $pdf->Cell(18,5,number_format($ultcosto,2,".",","),0,0,'C',0);
                $pdf->Cell(18,5,number_format(($centr * $ultcosto), 2),0,0,'C',0);
            }
            else
            {
                $pdf->Cell(18,5,"",0,0,'C',0);
                $pdf->Cell(18,5,"",0,0,'C',0);
                $pdf->Cell(18,5,"",0,0,'C',0);
            }
            if($csal >= 0)
            {
                $pdf->Cell(18,5,$csal,0,0,'C',0);
                $pdf->Cell(18,5,number_format($ultcosto,2,".",","),0,0,'C',0);
                $pdf->Cell(18,5,number_format(($csal * $ultcosto), 2),0,0,'C',0);
            }
            else
            {
                $pdf->Cell(18,5,"",0,0,'C',0);
                $pdf->Cell(18,5,"",0,0,'C',0);
                $pdf->Cell(18,5,"",0,0,'C',0);
            }
            $pdf->Cell(18,5,$stock_actual,0,0,'C',0);
            $pdf->Cell(18,5,number_format($ultcosto,2,".",","),0,0,'C',0);
            $pdf->Cell(18,5,number_format(($stock_actual * $ultcosto), 2),0,1,'C',0);
            $j++;
            $i++;

            if($id_proveedor>0)
            {
                $sql2 = _query("SELECT p.nombre, pa.nombre as pais FROM proveedor as p LEFT JOIN pais as pa ON(p.pais=pa.id) WHERE p.id_proveedor='".$id_proveedor."'");
                $datos2 = _fetch_array($sql2);
                $nombr = utf8_decode(trim($datos2["nombre"]));
                $nombr = $nombr." / ".utf8_decode($datos2["pais"])."";
                $ygg=1;
                if(ceil(strlen($nombr))/2 > 14)
                {
                    $nom = divtextlin($nombr, 27);
                    foreach ($nom as $nnon)
                    {
                      if ($ygg>1) {
                        $pdf->Cell(18,5,"",0,0,'C',0);
                        $pdf->Cell(18,5,"",0,0,'C',0);
                        $pdf->Cell(18,5,"",0,0,'C',0);
                        $pdf->Cell(56,5,$nnon,0,1,'L',0);
                      }
                      $ygg++;
                    }

                }
            }
            if($id_cliente>0)
            {
                $sql2 = _query("SELECT nombre FROM cliente WHERE id_cliente='".$id_cliente."'");
                $datos2 = _fetch_array($sql2);
                $nombr = utf8_decode(trim($datos2["nombre"]));
                $nombr = $nombr." / SV";
                $ygg=1;
                if(ceil(strlen($nombr))/2 > 14)
                {
                    $nom = divtextlin($nombr, 27);

                    foreach ($nom as $nnon)
                    {
                      if ($ygg>1) {
                        $pdf->Cell(18,5,"",0,0,'C',0);
                        $pdf->Cell(18,5,"",0,0,'C',0);
                        $pdf->Cell(18,5,"",0,0,'C',0);
                        $pdf->Cell(56,5,$nnon,0,1,'L',0);
                      }
                      $ygg++;
                    }

                }
            }

        }
        $pdf->Cell(74,6,"","T",0,'C',0);
        $pdf->Cell(36,6,"TOTAL ENTRADA","T",0,'C',0);
        $pdf->Cell(18,6,$entrada,"T",0,'C',0);
        $pdf->Cell(36,6,"TOTAL SALIDA","T",0,'C',0);
        $pdf->Cell(18,6,$salida,"T",0,'C',0);
        $pdf->Cell(146,6,"","T",1,'C',0);




    }
  }
ob_clean();
$pdf->Output("kardex.pdf","I");
