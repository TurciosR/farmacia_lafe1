<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');

# code...
class PDF extends FPDF
{
  var $a;
  var $b;
  var $c;
  var $d;
  var $e;
  var $f;
  var $g;
  // Cabecera de página\
  public function Header()
  {
    if ($this->PageNo()==1) {
      // code...
      //Encabezado General
      $this->SetFont('Latin','',12);

      $this->MultiCell(272,6,$this->a,0,'C',0);
      $this->SetFont('Latin','',10);
      $this->Cell(272,6,$this->b,0,1,'C');
      $this->Cell(272,6,"NIT: ".$this->c.", NRC: ".$this->d,0,1,'C');
      $this->Cell(272,6,utf8_decode($this->e),0,1,'C');
      $this->Cell(272,6,$this->f,0,1,'C');
      $this->Cell(100,5,"PRODUCTO: ".utf8_decode($this->g),0,1,'L',0);
    }

    $this->SetFont('Latin','',8);
    $this->Cell(18,10,"FECHA",1,0,'C',0);
    $this->Cell(18,10,"TIPO DOC",1,0,'C',0);
    $this->Cell(18,10,"NUM. DOC",1,0,'C',0);
    $xs=$this->GetX();
    $ys=$this->GetY();

    $this->Cell(11,5,"PRECIO","TLR",1,'C',0);
    $this->SetX($xs);
    $this->Cell(11,5,"MARCA","BLR",0,'C',0);

    $xs=$this->GetX();
    $this->SetY($ys);
    $this->SetX($xs);

    $this->Cell(54,5,"ENTRADA",1,1,'C',0);
    $this->SetX($xs);
    $this->Cell(18,5,"CANTIDAD",1,0,'C',0);
    $this->Cell(18,5,"COSTO",1,0,'C',0);
    $this->Cell(18,5,"SUBTOTAL",1,0,'C',0);

    $xs=$this->GetX();
    $this->SetY($ys);
    $this->SetX($xs);

    $this->Cell(54,5,"SALIDA",1,1,'C',0);
    $this->SetX($xs);
    $this->Cell(18,5,"CANTIDAD",1,0,'C',0);
    $this->Cell(18,5,"COSTO",1,0,'C',0);
    $this->Cell(18,5,"SUBTOTAL",1,0,'C',0);

    $xs=$this->GetX();
    $this->SetY($ys);
    $this->SetX($xs);

    $this->Cell(54,5,"SALDO",1,1,'C',0);
    $this->SetX($xs);
    $this->Cell(18,5,"CANTIDAD",1,0,'C',0);
    $this->Cell(18,5,"COSTO",1,0,'C',0);
    $this->Cell(18,5,"SUBTOTAL",1,0,'C',0);

    $xs=$this->GetX();
    $this->SetY($ys);
    $this->SetX($xs);

    $this->Cell(45,10,"PROVEEDOR",1,1,'C',0);

  }

  public function Footer()
  {
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial', 'I', 8);
    // Número de página requiere $pdf->AliasNbPages();
    //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
    $this-> Cell(100, 10, utf8_decode('Fecha de impresión: '.date('Y-m-d')), 0, 0, 'L');
    $this->Cell(156, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
  }
  public function setear($a,$b,$c,$d,$e,$f,$g)
  {
    # code...
    $this->a=$a;
    $this->b=$b;
    $this->c=$c;
    $this->d=$d;
    $this->e=$e;
    $this->f=$f;
    $this->g=$g;
  }
  function array_procesor($array)
  {
    $ygg=0;
    $maxlines=1;
    $array_a_retornar=array();
    foreach ($array as $key => $value) {
      /*Descripcion*/
      $nombr=$value[0];
      /*character*/
      $longitud=$value[1];
      /*fpdf width*/
      $size=$value[2];
      /*fpdf alignt*/
      $aling=$value[3];
      if(strlen($nombr) > $longitud)
      {
        $i=0;
        $nom = divtextlin($nombr, $longitud);
        foreach ($nom as $nnon)
        {
          $array_a_retornar[$ygg]["valor"][]=$nnon;
          $array_a_retornar[$ygg]["size"][]=$size;
          $array_a_retornar[$ygg]["aling"][]=$aling;
          $i++;
        }
        $ygg++;
        if ($i>$maxlines) {
          // code...
          $maxlines=$i;
        }
      }
      else {
        // code...
        $array_a_retornar[$ygg]['valor'][]=$nombr;
        $array_a_retornar[$ygg]['size'][]=$size;
        $array_a_retornar[$ygg]["aling"][]=$aling;
        $ygg++;

      }
    }

    $ygg=0;
    foreach($array_a_retornar as $keys)
    {
      for ($i=count($keys["valor"]); $i <$maxlines ; $i++) {
        // code...
        $array_a_retornar[$ygg]["valor"][]="";
        $array_a_retornar[$ygg]["size"][]=$array_a_retornar[$ygg]["size"][0];
        $array_a_retornar[$ygg]["aling"][]=$array_a_retornar[$ygg]["aling"][0];
      }
      $ygg++;
    }

    $data=$array_a_retornar;
    $total_lineas=count($data[0]["valor"]);
    $total_columnas=count($data);

    for ($i=0; $i < $total_lineas; $i++) {
      // code...
      for ($j=0; $j < $total_columnas; $j++) {
        // code...
        $salto=0;
        $abajo=0;
        if ($i==0) {
          // code...
          $abajo="";
        }
        if ($j==$total_columnas-1) {
          // code...
          $salto=1;
        }
        if ($i==$total_lineas-1) {
          // code...
          $abajo="";
        }
        $this->Cell($data[$j]["size"][$i],5,utf8_decode($data[$j]["valor"][$i]),$abajo,$salto,$data[$j]["aling"][$i]);
      }

    }
    //return $array_a_retornar;

  }

}


$pdf=new PDF('L','mm', 'Letter');
$pdf->SetMargins(10, 10);
$pdf->SetLeftMargin(4);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddFont("latin","","latin.php");
$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";

$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$tel1 = $row_emp['telefono'];
$nit = $row_emp['nit'];
$nrc = $row_emp['nrc'];
$razonsocial = $row_emp['razonsocial'];
$descripcion = utf8_decode($row_emp['descripcion']);
$giro = $row_emp['giro'];
$telefonos="TEL. ".$tel1;

$id_producto = $_REQUEST["id_producto"];
$fini = $_REQUEST["fini"];
$fin = $_REQUEST["fin"];
$logo = "img/logo_sys.jpg";
$impress = "Impreso: ".date("d/m/Y");
$title = $descripcion;
$titulo = "KARDEX DE PRODUCTO";
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

$sql = "SELECT * FROM movimiento_producto_detalle as md, movimiento_producto as m
WHERE md.id_movimiento=m.id_movimiento
AND m.id_sucursal='$id_sucursal'
AND md.id_producto='$id_producto'
AND m.tipo!='ASIGNACION'
AND m.tipo!='TRANSFERENCIA'
AND CAST(m.fecha AS DATE) BETWEEN '$fini' AND '$fin' ORDER BY md.id_detalle ASC";

$sql_aux = _query("SELECT descripcion FROM producto  WHERE id_producto='$id_producto'");
$dats_aux = _fetch_array($sql_aux);

$pdf->setear($title,$telefonos,$nit,$nrc,$titulo,$fech,$dats_aux['descripcion']);
$pdf->AddPage();
$result = _query($sql);
if(_num_rows($result)>0)
{
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

    $uniades=1;
    $id_presentacion = $row["id_presentacion"];
    $sql_pres = _query("SELECT unidad,costo,precio_marca FROM presentacion_producto WHERE id_presentacion ='$id_presentacion'");
    $precio_marca =0;
    if (_num_rows($sql_pres)>0) {
      // code...
      $dats_pres = _fetch_array($sql_pres);

      $precio_marca = number_format($dats_pres["precio_marca"],2);
      $uniades = $dats_pres["unidad"];
      $cost = $dats_pres["costo"];
    }

    $id_compra = $row["id_compra"];
    $id_factura = $row["id_factura"];
    if($id_factura > 0)
    {
      $sql_comp = _query("SELECT tipo_documento, num_fact_impresa FROM factura WHERE id_factura='$id_factura'");
      $dats_comp = _fetch_array($sql_comp);
      $alias_tipodoc = $dats_comp["tipo_documento"];
      $numero_doc = $dats_comp["num_fact_impresa"];
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

    $ultcosto = $row["costo"]/$uniades;
    $stock_actual = $row["stock_actual"];
    $stock_anterior = $row["stock_anterior"];
    $id_proveedor = $row["id_proveedor"];

    $nombr="";
    if($id_proveedor>0)
    {
      $sql2 = _query("SELECT p.nombre, pa.nombre as pais FROM proveedor as p LEFT JOIN paises as pa ON(p.nacionalidad=pa.id) WHERE p.id_proveedor='".$id_proveedor."'");
      $datos2 = _fetch_array($sql2);
      $nombr = utf8_decode($datos2["nombre"]);
      $nombr = $nombr." (".utf8_decode($datos2["pais"]).")";
    }

    if($init==1)
    {
      if($stock_anterior > 0)
      {
        $pdf->Cell(173,5,"INVENTARIO INICIAL",0,0,'C',0);
        $pdf->Cell(18,5,$stock_anterior,0,0,'R',0);
        $pdf->Cell(18,5,number_format($ultcosto,2,".",","),0,0,'R',0);
        $pdf->Cell(18,5,number_format(($stock_anterior * $ultcosto), 2),0,0,'R',0);
        $pdf->Cell(45,5,"",0,1,'C',0);
        $mm+=5;
      }
      $init=0;
    }

    $arraydat = array(
      'canteent' => 0,
      'cantecos' => 0,
      'cantesub' => 0,
      'cantsent' => 0,
      'cantscos' => 0,
      'cantssub' => 0,
    );

    if($centr >= 0)
    {
      $arraydat['canteent']=$centr;
      $arraydat['cantecos']=number_format($ultcosto,2,".",",");
      $arraydat['cantesub']=number_format(($centr * $ultcosto), 2);
    }
    else {
      $arraydat['canteent']="";
      $arraydat['cantecos']="";
      $arraydat['cantesub']="";
    }

    if($csal >= 0)
    {
      $arraydat['cantsent']=$csal;
      $arraydat['cantscos']=number_format($ultcosto,2,".",",");
      $arraydat['cantssub']=number_format(($csal * $ultcosto), 2);

    }
    else {
      $arraydat['cantsent']="";
      $arraydat['cantscos']="";
      $arraydat['cantssub']="";
    }

    $array_data = array(
      array($fechadoc,150,18,"L"),
      array($alias_tipodoc,150,18,"L"),
      array($numero_doc,150,18,"R"),
      array($precio_marca,150,11,"R"),
      array($arraydat['canteent'],150,18,"R"),
      array($arraydat['cantecos'],150,18,"R"),
      array($arraydat['cantesub'],150,18,"R"),
      array($arraydat['cantsent'],150,18,"R"),
      array($arraydat['cantscos'],150,18,"R"),
      array($arraydat['cantssub'],150,18,"R"),
      array($stock_actual,150,18,"R"),
      array(number_format($ultcosto,2,".",","),150,18,"R"),
      array(number_format(($stock_actual * $ultcosto), 2),150,18,"R"),
      array($nombr,30,45,"L"),

    );
    $data=$pdf->array_procesor($array_data);


  }
}
ob_clean();
$pdf->Output("kardex.pdf","I");
