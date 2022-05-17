<?php
error_reporting(E_ERROR | E_PARSE);
require('_core.php');
require('fpdf/fpdf.php');

class PDF extends FPDF
{
    var $a;
    var $b;
    var $c;
    var $d;
    var $e;
    var $f;
    var $w;
    // Cabecera de página\
    public function Header()
    {
      //Encabezado General
      $this->SetFont('Arial', '', 11);
      if($this->PageNo()==1)
      {
        $this->MultiCell(280, 6, $this->a, 0, 'C', 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(280, 4, utf8_decode($this->b), 0, 1, 'C');
        $this->Cell(280, 4, utf8_decode($this->c), 0, 1, 'C');
        $this->Cell(280, 4, utf8_decode($this->d), 0, 1, 'C');
      }
      $this->SetFont('Arial', '', 8);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this-> Cell(40, 10, utf8_decode('Fecha de impresión: '.date('Y-m-d')), 0, 0, 'L');
        $this->Cell(220, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0,0, 'R');
    }
    public function setear($a,$b,$c,$d,$e,$f,$g,$w)
    {
      # code...
      $this->a=$a;
      $this->b=$b;
      $this->c=$c;
      $this->d=$d;
      $this->e=$e;
      $this->f=$f;
      $this->g=$g;
      $this->w=$w;
    }

    public function LineWriteB($array)
    {
      $ygg=0;
      $maxlines=1;
      $array_a_retornar=array();
      $array_max= array();
      foreach ($array as $key => $value) {
        // /Descripcion/
        $nombr=$value[0];
        // /fpdf width/
        $size=$value[1];
        // /fpdf alignt/
        $aling=$value[2];
        $jk=0;
        $w = $size;
        $h  = 0;
        $txt=$nombr;
        $border=0;
        if(!isset($this->CurrentFont))
          $this->Error('No font has been set');
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
          $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
          $nb--;
        $b = 1;

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while($i<$nb)
        {
          // Get next character
          $c = $s[$i];
          if($c=="\n")
          {
            $array_a_retornar[$ygg]["valor"][]=substr($s,$j,$i-$j);
            $array_a_retornar[$ygg]["size"][]=$size;
            $array_a_retornar[$ygg]["aling"][]=$aling;
            $jk++;

            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2)
              $b = $b2;
            continue;
          }
          if($c==' ')
          {
            $sep = $i;
            $ls = $l;
            $ns++;
          }
          $l += $cw[$c];
          if($l>$wmax)
          {
            // Automatic line break
            if($sep==-1)
            {
              if($i==$j)
                $i++;
              $array_a_retornar[$ygg]["valor"][]=substr($s,$j,$i-$j);
              $array_a_retornar[$ygg]["size"][]=$size;
              $array_a_retornar[$ygg]["aling"][]=$aling;
              $jk++;
            }
            else
            {
              $array_a_retornar[$ygg]["valor"][]=substr($s,$j,$sep-$j);
              $array_a_retornar[$ygg]["size"][]=$size;
              $array_a_retornar[$ygg]["aling"][]=$aling;
              $jk++;

              $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2)
              $b = $b2;
          }
          else
            $i++;
        }
        // Last chunk
        if($this->ws>0)
        {
          $this->ws = 0;
        }
        if($border && strpos($border,'B')!==false)
          $b .= 'B';
        $array_a_retornar[$ygg]["valor"][]=substr($s,$j,$i-$j);
        $array_a_retornar[$ygg]["size"][]=$size;
        $array_a_retornar[$ygg]["aling"][]=$aling;
        $jk++;
        $ygg++;
        if ($jk>$maxlines) {
          // code...
          $maxlines=$jk;
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
          $abajo="LR";
          if ($i==0) {
            // code...
            $abajo="TLR";
          }
          if ($j==$total_columnas-1) {
            // code...
            $salto=1;
          }
          if ($i==$total_lineas-1) {
            // code...
            $abajo="BLR";
          }
          if ($i==$total_lineas-1&&$i==0) {
            // code...
            $abajo="1";
          }
          $str = $data[$j]["valor"][$i];
          $this->Cell($data[$j]["size"][$i],4,utf8_decode($str),$abajo,$salto,$data[$j]["aling"][$i]);
        }

      }
    }
}

$id_sucursal = $_SESSION["id_sucursal"];
$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";
$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu((trim($row_emp["descripcion"]))));
//$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
$direccion = utf8_decode(Mayu((trim($row_emp["direccion"]))));
$logo = "img/logo_sys.png";
$title = $nombre_a;
$fech = "";

$id_producto = $_REQUEST["id_producto"];
$fini = $_REQUEST["fini"];
$fin = $_REQUEST["fin"];
$logo = "img/logo_sys.jpg";
$impress = "Impreso: ".date("d/m/Y");

$titulo = "INVENTARIO POR LOTES";

$pdf = new PDF('L', 'mm', 'letter');
$pdf->setear($nombre_a,$direccion,$titulo,$fech,$dats_aux["descripcion"],"","","");
$pdf->SetMargins(10, 10);
$pdf->SetLeftMargin(5);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddPage();

$array_data = array(
  array("Articulo",59,"C"),
  array("Clasificación o categoría",39,"C"),
  array("Costo unitario compra",39,"C"),
  array("Precio unitario venta",39,"C"),
  array("Existencias",25,"C"),
  array("Fecha vencimiento",29,"C"),
  array("Proveedor",39,"C"),
);
$pdf->LineWriteB($array_data);

$total_general = 0;
$sql_lote= _query("SELECT producto.id_producto,producto.descripcion,lote.vencimiento,SUM(lote.cantidad) as cantidad, c.nombre_cat as cat,proveedor.nombre FROM `lote`
  JOIN producto ON producto.id_producto=lote.id_producto
  LEFT JOIN proveedor ON proveedor.id_proveedor=producto.id_proveedor
  LEFT join categoria as c on producto.id_categoria=c.id_categoria
  WHERE lote.cantidad>0 AND lote.id_sucursal=$_SESSION[id_sucursal] group by lote.id_producto,lote.vencimiento ORDER BY lote.id_producto ASC");

while($row = _fetch_array($sql_lote))
{
  $id_producto = $row['id_producto'];
  $descripcion=$row["descripcion"];
  $cat = $row['cat'];
  $existencias = $row['cantidad'];

  $vencimiento = $row['vencimiento'];

  if ($vencimiento=='0000-00-00')
  {
    $vencimiento = '';
  }
  else
  {
    $vencimiento = ED($vencimiento);
  }

  $sql_pres = _query("SELECT pp.*, p.nombre as descripcion_pr FROM presentacion_producto as pp, presentacion as p WHERE pp.presentacion=p.id_presentacion AND pp.id_producto='$id_producto' AND  pp.activo=1  ORDER BY pp.unidad ASC limit 1");
  $npres = _num_rows($sql_pres);
  $exis = 0;

  while ($rowb = _fetch_array($sql_pres))
  {
    $unidad = $rowb["unidad"];
    $costo = $rowb["costo"];
    $precio = $rowb["precio"];

    $sql_rank=_query("SELECT presentacion_producto_precio.precio FROM presentacion_producto_precio WHERE presentacion_producto_precio.id_presentacion=$rowb[id_presentacion] AND presentacion_producto_precio.id_sucursal=$_SESSION[id_sucursal] AND presentacion_producto_precio.precio!=0 ORDER BY presentacion_producto_precio.desde ASC LIMIT 1
      ");

    $xc=0;
    while ($rowr=_fetch_array($sql_rank)) {
      # code...
      if($xc==0)
      {

        $precio=$rowr['precio'];
      }
      $xc++;
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

    $total_costo = round(($costo) * $exis, 4);
    $total_general += $total_costo;

    $array_data = array(
      array($descripcion,59,"L"),
      array($cat,39,"C"),
      array($costo,39,"R"),
      array($precio,39,"R"),
      array($exis,25,"C"),
      array($vencimiento,29,"C"),
      array($row['nombre'],39,"C"),
    );
    $pdf->LineWriteB($array_data);





  }
}

$pdf->Output("reporte_kardex.pdf", "I");
