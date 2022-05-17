<?php
    /** Error reporting */
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    if (PHP_SAPI == 'cli')
	   die('Error Inesperado');
    /** Include PHPExcel */
    require_once dirname(__FILE__) . '/php_excel/Classes/PHPExcel.php';
    include('_core.php');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Open Solutions Systems")
    						->setLastModifiedBy("Open Solutions Systems")
    						->setTitle("Office 2007 XLSX")
    						->setSubject("Office 2007 XLSX")
    						->setDescription("Documento compatible con Office 2007 XLSX")
    						->setKeywords("office 2007 openxml php")
    						->setCategory("Reportes");

    $id_sucursal = $_SESSION["id_sucursal"];
    $sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal='$id_sucursal'";

    $resultado_emp=_query($sql_empresa);
    $row_emp=_fetch_array($resultado_emp);
    $nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
    //$direccion = Mayu(utf8_decode($row_emp["direccion_empresa"]));
    $direccion = (((($row_emp["direccion"]))));
    $tel1 = $row_emp['telefono1'];
    $nrc = $row_emp['nrc'];
    $nit = $row_emp['nit'];
    $telefonos="TEL. ".$tel1;


    $logo = "img/logo_sys.png";
    $impress = "Impreso: ".date("d/m/Y");
    $title = $nombre_a;
    $titulo = "INVENTARIO POR LOTES";

    //Titulos
    $title0="INVENTARIO POR LOTES";


    //style border
    $BStyle = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
            ),
            'allborders' => array(
               'style' => PHPExcel_Style_Border::BORDER_THIN
            )
        )
    );
    //Center table
    $titulo = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
  		'font'  => array(
  			'bold'  => true,
  			'color' => array('rgb' => '00000'),
  			'size'  => 10,
  			'name'  => 'Arial'
        )
    );
	$negrita_centrado = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 10,
            'name'  => 'Arial'
        )
    );
    $centrado = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
		'font'  => array(
			'bold'  => false,
			'color' => array('rgb' => '000000'),
			'size'  => 10,
			'name'  => 'Arial'
        )
    );

    $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
    $objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
    $objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
    $objPHPExcel->getActiveSheet()->mergeCells('A4:G4');
    $objPHPExcel->getActiveSheet()->mergeCells('A5:G5');
    $objPHPExcel->getActiveSheet()->mergeCells('A6:G6');
    $objPHPExcel->getActiveSheet()->mergeCells('A7:G7');

    //altura de algunas filas
    for($j=2;$j<8;$j++)
    {
        $objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(15);
    }
    //Ancho de algunas filas
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    //  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
    //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);

    $nin = 9;
    //Esrilo de fuentes
    $objPHPExcel->getActiveSheet()->getStyle("A1:L7")->applyFromArray($titulo);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $nombre_a);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $direccion);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', $telefonos);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', $title0);
    //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', $fech);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', "NRC: ".$nrc."  NIT: ".$nit);


    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A8', "Articulo");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B8', "Clasificación o categoría");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', "Costo unitario compra");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D8', "Precio unitario venta");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E8', "Existencias");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F8', "Fecha vencimiento");
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G8', "Proveedor");

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

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$nin, $descripcion);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$nin, $cat);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$nin, $costo);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$nin, $precio);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$nin, $exis);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$nin, $vencimiento);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$nin, $row['nombre']);
        $nin++;
      }
    }

    /*$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$nin, "TOTALES");

    $objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$nin,  round($deuda_total,4));
    */
    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('inventario_lote_xls');



    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $archivo_salida="inventario_lote_xls".date("dmY").".xls";
    // Redirect output to a client’s web browser (Excel7)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$archivo_salida.'"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 07:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
?>
