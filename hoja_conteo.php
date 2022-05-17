<?php
require('_core.php');
require('fpdf/fpdf.php');
require("code128/code128.php");
ini_set('memory_limit', '-1');
$params="";
$cu="";

if (isset($_POST['params'])) {
  # code...
  if ($_POST['cu']) {
    # code...
    $params=$_POST['params'];
    $cu=$_POST['cu'];

    if (isset($_POST['categoria']))
    {
      $params="";
      $cu=0;
      $categoria=$_POST['categoria'];
      $origen=$_POST['destino'];

      $id_categoria=$categoria;
      $id_ubicacion=$origen;



      $cabebera=utf8_decode("HOJA DE CONTEO");

      class PDF extends PDF_Code128
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

          // Logo
          $this->Image('img/finanzas.jpg', 10, 10, 33);
          $this->AddFont('latin','','latin.php');
          $this->SetFont('latin', '', 10);
          // Movernos a la derecha
          // Título
          $this->SetX(43);
          $this->Cell(130, 4, 'HOJA DE CONTEO ', 0, 1, 'C');
          $this->SetX(43);
          $this->Cell(130, 4, '', 0, 1, 'C');
          $this->SetX(43);
          $this->Cell(130, 4, '', 0, 1, 'C');
          $this->SetX(43);
          $this->Cell(130, 4, 'FECHA: '.utf8_decode("____/__/__"), 0, 1, 'C');
          // Salto de línea
          $this->Ln(5);
          $set_y=$this->GetY();
          $set_x=$this->GetX();
          $this->SetXY($set_x, $set_y);
          $this->AddFont('latin','','latin.php');
          $this->SetFont('latin', '', 7);
          $this->Cell(91, 5, 'PRODUCTO', 1, 0, 'L');
          $this->Cell(40, 5, utf8_decode('PRESENTACIÓN'), 1, 0, 'L');
          $this->Cell(20, 5, 'UNIDADES', 1, 0, 'L');
          $this->Cell(20, 5, 'VIRTUAL', 1, 0, 'L');
          $this->Cell(20, 5, 'REAL', 1, 1, 'L');
          $this->Ln(1);
        }

        public function Footer()
        {
          // Posición: a 1,5 cm del final
          $this->SetY(-15);
          // Arial italic 8
          $this->SetFont('Arial', 'I', 8);
          // Número de página requiere $pdf->AliasNbPages();
          //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
          $this-> Cell(40, 10, utf8_decode('Fecha de impresión: '.date('Y-m-d')), 0, 0, 'L');
          $this->Cell(156, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
        }
        public function setear($a,$b,$c,$d,$e,$f)
        {
          # code...
          $this->a=$a;
          $this->b=$b;
          $this->c=$c;
          $this->d=$d;
          $this->e=$e;
          $this->f=$f;
        }
      }

      $pdf = new PDF('P', 'mm', 'letter');

      $pdf->setear(0  ,0,0,0,0,0);
      $pdf->SetMargins(10, 10);
      $pdf->SetLeftMargin(10);
      $pdf->AliasNbPages();
      $pdf->SetAutoPageBreak(true, 15);
      $pdf->AliasNbPages();

      $sql_ubidispo=_query("SELECT estante.descripcion,posicion.posicion, stock_ubicacion.id_estante,stock_ubicacion.id_posicion, SUM(stock_ubicacion.cantidad) as exu FROM stock_ubicacion LEFT JOIN estante ON estante.id_estante=stock_ubicacion.id_estante LEFT JOIN posicion ON posicion.id_posicion=stock_ubicacion.id_posicion WHERE stock_ubicacion.id_ubicacion=$id_ubicacion GROUP BY stock_ubicacion.id_estante,stock_ubicacion.id_posicion HAVING exu>0 ");

      while($rpos=_fetch_array($sql_ubidispo))
      {
        $estante=$rpos['id_estante'];
        $posicion=$rpos['id_posicion'];

        $des_estante=$rpos['descripcion'];
        $des_posicion=$rpos['posicion'];


        if ($categoria=="") {
          # code...
          $sql_ids=_query("SELECT DISTINCT stock_ubicacion.id_producto,producto.descripcion  FROM stock_ubicacion JOIN producto  ON  producto.id_producto=stock_ubicacion.id_producto WHERE stock_ubicacion.id_ubicacion=$id_ubicacion AND stock_ubicacion.id_estante=$estante AND stock_ubicacion.id_posicion=$posicion");

        }
        else {
          # code...
          $sql_ids=_query("SELECT DISTINCT stock_ubicacion.id_producto,producto.descripcion  FROM stock_ubicacion JOIN producto  ON  producto.id_producto=stock_ubicacion.id_producto WHERE producto.id_categoria=$id_categoria AND stock_ubicacion.id_ubicacion=$id_ubicacion AND stock_ubicacion.id_estante=$estante AND stock_ubicacion.id_posicion=$posicion");

        }


        $i=_num_rows($sql_ids);

        $params="";
        $cu=0;
        while ($rowa=_fetch_array($sql_ids))
        {


          $id_producto =   $rowa['id_producto'];;
          $id_sucursal=$_SESSION['id_sucursal'];

          $sql_existencia = _query("SELECT sum(cantidad) as existencia FROM stock_ubicacion WHERE id_producto='$id_producto' AND stock_ubicacion.id_ubicacion='$origen' AND stock_ubicacion.id_estante=$estante AND stock_ubicacion.id_posicion=$posicion");
          $dt_existencia = _fetch_array($sql_existencia);
          $existencia = $dt_existencia["existencia"];

          $sql_p=_query("SELECT presentacion.nombre, prp.descripcion,prp.id_presentacion,prp.unidad,prp.costo,prp.precio FROM presentacion_producto AS prp JOIN presentacion ON presentacion.id_presentacion=prp.presentacion WHERE prp.id_producto=$id_producto AND prp.activo=1 AND prp.id_sucursal=$id_sucursal ORDER BY prp.unidad DESC");

          while ($row=_fetch_array($sql_p))
          {
            $costop=$row['costo'];
            $unidadp=$row['unidad'];
            $preciop=$row['precio'];
            $descripcionp=$row['descripcion'];

            $a=intdiv($existencia,$row['unidad']);
            $array_e[$i]=$a;
            $existencia=$existencia-($a*$row['unidad']);
            $params.=$id_producto."|".$costop."|".$preciop."|"."0"."|".$unidadp."|".$a."|".$row['id_presentacion']."#";

            $cu++;

          }
        }

        if ($cu>0) {
          // code...
          $lista=explode('#',$params);


          $pdf->AddPage();
          $pdf->Cell(26, 5, $des_estante." ".$des_posicion, 0, 1, 'C');
          for ($i=0;$i<$cu ;$i++)
          {
            list($id_producto,$precio_compra,$precio_venta,$cantidad,$unidades,$existencia,$id_presentacion)=explode('|',$lista[$i]);
            $sql=_fetch_array(_query("SELECT producto.descripcion,presentacion.nombre FROM producto JOIN presentacion_producto ON presentacion_producto.id_producto=producto.id_producto JOIN presentacion ON presentacion.id_presentacion=presentacion_producto.presentacion WHERE presentacion_producto.id_producto=$id_producto AND presentacion_producto.id_presentacion=$id_presentacion"));
            $a=$pdf->GetX();
            $pdf->Cell(26, 18, "", 0, 0, 'C');
            $pdf->Code128($a,$pdf-> GetY(), $id_producto, 26, 6);
            $pdf->Cell(65, 6, substr(utf8_decode($sql['descripcion']),0,38), 0, 0, 'L');
            $pdf->Cell(40, 6, utf8_decode($sql['nombre']), "B", 0, 'L');
            $pdf->Cell(20, 6, $unidades, "B", 0, 'R');
            $pdf->Cell(20, 6, $existencia, "B", 0, 'R');
            $pdf->Cell(20, 6, "", "B", 1, 'L');
            $pdf->Ln(2);
          }
        }


      }



    }

    $pdf->Output("hoja de conteo.pdf", "I");

  }
  else {
    # code...
    echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
  }
}
else {
  # code...
  echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
}
