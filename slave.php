<?php
header("Access-Control-Allow-Origin: *");
require_once("_conexion.php");

if (isset($_POST['hash']))
{
	$hash = $_POST['hash'];
	$sql_ver = _query("SELECT * FROM access_conf WHERE hash='$hash'");
	if (_num_rows($sql_ver)>0)
	{
		function search_changes()
		{
			$sql_suc = _query("SELECT * FROM access_conf WHERE id_conf='1'");
			$dats_suc = _fetch_array($sql_suc);
			$id_sucur = $dats_suc["id_sucursal"];

			$table = $_POST["table"];
			$actu  = array('movimiento_producto_detalle','movimiento_producto_pendiente','movimiento_stock_ubicacion','detalle_compra','factura_detalle','traslado_detalle');
			if(in_array($table,$actu))
			{
				_query("UPDATE movimiento_producto_detalle JOIN producto ON movimiento_producto_detalle.id_producto=producto.id_producto SET movimiento_producto_detalle.id_server_prod=producto.id_server
				WHERE movimiento_producto_detalle.id_server_prod=0");
				_query("UPDATE movimiento_producto_detalle JOIN presentacion_producto ON movimiento_producto_detalle.id_presentacion=presentacion_producto.id_presentacion SET movimiento_producto_detalle.id_server_presen=presentacion_producto.id_server
				WHERE  movimiento_producto_detalle.id_server_presen=0");
				_query("UPDATE movimiento_producto_pendiente JOIN producto ON movimiento_producto_pendiente.id_producto=producto.id_producto SET movimiento_producto_pendiente.id_server_prod=producto.id_server
				WHERE movimiento_producto_pendiente.id_server_prod=0");
				_query("UPDATE movimiento_producto_pendiente JOIN presentacion_producto ON movimiento_producto_pendiente.id_presentacion=presentacion_producto.id_presentacion SET movimiento_producto_pendiente.id_server_presen=presentacion_producto.id_server
				WHERE  movimiento_producto_pendiente.id_server_presen=0");
				_query("UPDATE movimiento_stock_ubicacion JOIN producto ON movimiento_stock_ubicacion.id_producto=producto.id_producto SET movimiento_stock_ubicacion.id_server_prod=producto.id_server
				WHERE movimiento_stock_ubicacion.id_server_prod=0");
				_query("UPDATE movimiento_stock_ubicacion JOIN presentacion_producto ON movimiento_stock_ubicacion.id_presentacion=presentacion_producto.id_presentacion SET movimiento_stock_ubicacion.id_server_presen=presentacion_producto.id_server
				WHERE  movimiento_stock_ubicacion.id_server_presen=0");
				_query("UPDATE detalle_compra JOIN producto ON detalle_compra.id_producto=producto.id_producto SET detalle_compra.id_server_prod=producto.id_server
				WHERE detalle_compra.id_server_prod=0");
				_query("UPDATE detalle_compra JOIN presentacion_producto ON detalle_compra.id_presentacion=presentacion_producto.id_presentacion SET detalle_compra.id_server_presen=presentacion_producto.id_server
				WHERE  detalle_compra.id_server_presen=0");
				_query("UPDATE factura_detalle JOIN producto ON factura_detalle.id_prod_serv=producto.id_producto SET factura_detalle.id_server_prod=producto.id_server
				WHERE factura_detalle.id_server_prod=0");
				_query("UPDATE factura_detalle JOIN presentacion_producto ON factura_detalle.id_presentacion=presentacion_producto.id_presentacion SET factura_detalle.id_server_presen=presentacion_producto.id_server
				WHERE  factura_detalle.id_server_presen=0");
				_query("UPDATE traslado_detalle JOIN producto ON traslado_detalle.id_producto=producto.id_producto SET traslado_detalle.id_server_prod=producto.id_server
				WHERE traslado_detalle.id_server_prod=0");
				_query("UPDATE traslado_detalle JOIN presentacion_producto ON traslado_detalle.id_presentacion=presentacion_producto.id_presentacion SET traslado_detalle.id_server_presen=presentacion_producto.id_server
				WHERE  traslado_detalle.id_server_presen=0");
			}
			$exculdes = array('presentacion', 'proveedor', 'categoria', 'producto', 'presentacion_producto', 'presentacion_producto_precio', 'traslado', 'traslado_detalle','traslado_detalle_recibido', 'log_cambio_local', 'log_cambio_detalle_local','traslado_g','traslado_detalle_g');

			$sql_sync = _query("SHOW COLUMNS FROM $table WHERE Field = 'id_server'");
			if(_num_rows($sql_sync)>0)
			{
				if(!in_array($table,$exculdes))
				{
					$pk = "";
					$sql_pk = _query("DESCRIBE $table");
					while($row = _fetch_array($sql_pk))
					{
						if($row["Key"] =="PRI")
							$pk = $row['Field'];
					}
					$data = array();
					$sql_data = _query("SELECT * FROM $table WHERE id_server = '0' LIMIT 250");
					$count = _num_rows($sql_data);
					while($row = _fetch_array($sql_data))
					{
						unset($row["id_server"]);
						if (array_key_exists("id_sucursal",$row)) {
							// code...
							if ($table!="sucursal") {
								// code...
								$row['id_sucursal']=$id_sucur;
							}


						}
						$data[$row[$pk]] = $row;
					}
					$array = array('insert' => $data);
					$response = array(
						'response' => "OK",
						'data' => $array,
						'count' => $count,
						'pk' => $pk,
						'process' => "insert"
					);
				}
				else
				{
					$response = array('response' => 'manual_sync');
				}
			}
			else
			{
					$response = array('response' => 'no_sync');
			}
			echo json_encode($response);
		}
		function search_producto()
		{
			$id = $_POST["id"];
			$process = $_POST["action"];
			if($process == "insert")
			{
				$sql = _query("SELECT * FROM producto WHERE id_producto = '$id'");
				$data = _fetch_array($sql);
				unset($data["id_server"]);
				$id_producto = $data["id_producto"];

				$sql_suc = _query("SELECT * FROM access_conf WHERE id_conf='1'");
				$dats_suc = _fetch_array($sql_suc);
				$id_sucur = $dats_suc["id_sucursal"];

				$id_sucursal = $id_sucur;//$data["id_sucursal"];
				$sql1 = _query("SELECT * FROM presentacion_producto WHERE id_producto = '$id_producto' AND id_sucursal='$id_sucursal'");
				$sql2 = _query("SELECT * FROM presentacion_producto_precio WHERE id_producto = '$id_producto' AND id_sucursal='$id_sucursal'");
				$data1 = array();
				while($row = _fetch_array($sql1))
				{
					unset($row["id_server"]);
					$data1[] = $row;
				}
				$data2 = array();
				while($row = _fetch_array($sql2))
				{
					unset($row["id_server"]);
					$data2[] = $row;
				}
				$response = array(
					'producto' => $data,
					'presentacion_producto' => $data1,
					'presentacion_producto_precio' => $data2,
				);
				echo json_encode($response);
			}
			else if ($process == "update")
			{
				$sql = _query("SELECT * FROM producto WHERE id_producto = '$id'");
				$data = _fetch_array($sql);
				$id_server = $data["id_server"];
				unset($data["id_server"]);
				$array = array(
					'producto' => $data,
				);
				$response = array(
					'id_server' => $id_server,
					'data' => $array
				);
				echo json_encode($response);
			}
		}
		function search_presentacion_producto()
		{
				$id = $_POST["id"];

				$sql = _query("SELECT * FROM presentacion_producto WHERE id_presentacion = '$id'");
				$data = _fetch_array($sql);
				$id_server = $data["id_server"];
				$id_producto = $data["id_producto"];
				$sql_prod = _query("SELECT id_server FROM producto WHERE id_producto='$id_producto'");
				$data_prod = _fetch_array($sql_prod);
				$id_server_prod = $data_prod["id_server"];

				unset($data["id_server"]);
				$prep = array(0 => $data);
				$array = array(
					'presentacion_producto' => $prep,
				);

				$response = array(
					'id_server' => $id_server,
					'id_server_prod' => $id_server_prod,
					'data' => $array
				);

				echo json_encode($response);
		}
		function search_presentacion_producto_precio()
		{
				$id = $_POST["id"];
				$sql = _query("SELECT * FROM presentacion_producto_precio WHERE id_prepd = '$id'");
				$data = _fetch_array($sql);
				$id_server = $data["id_server"];
				$id_presentacion = $data["id_presentacion"];

				$sql_pre = _query("SELECT id_server FROM presentacion_producto WHERE id_presentacion='$id_presentacion'");
				$data_pre = _fetch_array($sql_pre);
				$id_server_pre = $data_pre["id_server"];
				unset($data["id_server"]);
				$prep = array(0 => $data);
				$array = array(
					'presentacion_producto_precio' => $prep,
				);

				$response = array(
					'id_server' => $id_server,
					'id_server_pre' => $id_server_pre,
					'data' => $array
				);

				echo json_encode($response);
		}
		function insert_producto()
		{
			$sql_suc = _query("SELECT * FROM access_conf WHERE id_conf='1'");
			$dats_suc = _fetch_array($sql_suc);
			$id_sucur = $dats_suc["id_sucursal"];
			$data =  json_decode($_POST["data"], true);
			$prods = $data["producto"];
			$prods_pre = $data["presentacion_producto"];
			$prods_pre_pre = $data["presentacion_producto_precio"];
			$prods_pre_pre1 = array();
			$form_data = array();
			$table = "producto";
			$table1 = "presentacion_producto";
			$table2 = "presentacion_producto_precio";
			$table3 = "stock";
			$where = "";
			$nprod = count($prods);
			$response = array();
			$flag_p = 1;
			$flag_pp = 1;
			$flag_ppp = 1;

			$present= array();
			$newpresent= array();
			_begin();

			$where = "id_server = '".$prods["id_server"]."'";
			foreach ($prods as $campo => $valor)
			{
				$form_data[$campo] = $valor;
			}
			unset($form_data["id_producto"]);
			$sql_val = _query("SELECT id_producto FROM producto WHERE ".$where);
			if(_num_rows($sql_val)>0)
			{
				$datos = _fetch_array($sql_val);
				$id_producto = $datos["id_producto"];
				$insert = _update($table, $form_data,$where);
			}
			else
			{
				$insert = _insert($table, $form_data);
				$id_producto = _insert_id();
			}
			$form_datast = array(
				'id_producto' => $id_producto,
				'id_sucursal' => $id_sucur,
				'stock' => 0,
				'stock_local' => 0
			);
			_insert($table3, $form_datast);
			if(!$insert)
			$flag_p = 0;

			$jo=0;
			$jojo=0;

			foreach ($prods_pre as $pos => $mini_array)
			{
				$id_presentacions = 0;
				$form_data1 = array();
				$where1 = "id_server = '".$mini_array["id_server"]."'";
				foreach ($mini_array as $campo => $valor)
				{
					if($campo == "id_producto")
					{
						$form_data1["id_producto"] = $id_producto;
					}
					else if ($campo == 'id_sucursal')
					{
						$form_data1["id_sucursal"] = $id_sucur;
					}
					else if ($campo == 'id_presentacion')
					{
						$id_presentacions = $valor;
						$present[$jo]=$id_presentacions;
						$jo++;
					}
					else
					{
						$form_data1[$campo] = $valor;
					}
				}
				unset($form_data1["id_presentacion"]);
				$sql_val1 = _query("SELECT id_presentacion FROM presentacion_producto WHERE ".$where1);
				if(_num_rows($sql_val1)>0)
				{
					$datos = _fetch_array($sql_val1);
					$id_presentacion = $datos["id_presentacion"];
					$insert1 = _update($table1, $form_data1, $where1);
				}
				else
				{
					$insert1 = _insert($table1, $form_data1);
					$id_presentacion = _insert_id();
				}

				$newpresent[$jojo]=$id_presentacion;
				$jojo++;

				//echo _error();
				if(!$insert1)
				$flag_pp = 0;
			}

			$k = 0;
			$prods_pre_pre1[$k] = array();
			foreach ($present as $key => $valk) {
				$k = 0;
				// code...
				foreach ($prods_pre_pre as $posi => $mini_array2)
				{

					foreach ($mini_array2 as $campo2 => $valor2)
					{
						if($campo2 == "id_presentacion")
						{

							if($valor2 == $valk)
							{
								$prods_pre_pre1[$k]["id_presentacion"] = $newpresent[$key];
							}
						}
						else if($campo2 == "id_producto")
						{
							$prods_pre_pre1[$k]["id_producto"] = $id_producto;
						}
						else if ($campo2 == 'id_sucursal')
						{
							$prods_pre_pre1[$k]["id_sucursal"] = $id_sucur;
						}
						else
						{
							$prods_pre_pre1[$k][$campo2] = $valor2;
						}
					}
					$k++;
				}
			}




			foreach ($prods_pre_pre1 as $pos => $mini_array)
			{
				$form_data2 = array();
				$where2 = "id_server = '".$mini_array["id_server"]."'";
				foreach ($mini_array as $campo => $valor)
				{
					$form_data2[$campo] = $valor;
				}
				unset($form_data2["id_prepd"]);
				$sql_val2 = _query("SELECT id_server FROM presentacion_producto_precio WHERE ".$where2);
				if(_num_rows($sql_val2)>0)
				{
					$insert2 = _update($table2, $form_data2, $where2);
				}
				else
				{
					$insert2 = _insert($table2, $form_data2);
				}
				if(!$insert2)
				$flag_ppp = 0;
			}
			if($flag_p && $flag_pp && $flag_ppp)
			{
				_commit();
				echo "all changes commited";
			}
			else
			{
				_rollback();
				echo "sync error";
			}
		}
		function update_producto()
		{
			$data =  json_decode($_POST["data"], true);

			$prods = $data["producto"];
			$id_server = $_POST["id_server"];

			$form_data = array();

			$table = "producto";
			$where  = "id_server='".$id_server."'";
			foreach ($prods as $campo => $valor)
			{
				$form_data[$campo] = $valor;
			}
			unset($form_data["id_producto"]);
			unset($form_data["id_sucursal"]);
			$update = _update($table, $form_data,$where);
			if($update)
			{
				echo "all changes commited";
			}
			else
			{
				echo "sync error";
			}
		}
		function insert_presentacion_producto()
		{
			$sql_suc = _query("SELECT * FROM access_conf WHERE id_conf='1'");
			$dats_suc = _fetch_array($sql_suc);
			$id_sucur = $dats_suc["id_sucursal"];

			$data =  json_decode($_POST["data"], true);
			$prods_pre = $data["presentacion_producto"];
			$table1 = "presentacion_producto";
			$response = array();

			$id_server = $_POST["id_server"];
			$id_server_prod = $_POST["id_server_prod"];
			$sql_prod = _query("SELECT id_producto FROM producto WHERE id_server='$id_server_prod'");
			$datos_prod = _fetch_array($sql_prod);
			$id_producto = $datos_prod["id_producto"];
			$where1= "id_server='".$prods_pre["id_server"]."'";
			foreach ($prods_pre as $campo => $valor)
			{
				if($campo == "id_producto")
				{
					$form_data1["id_producto"] = $id_producto;
				}
				else if($campo == "id_sucursal")
				{
					$form_data1["id_sucursal"] = $id_sucur;
				}
				else
				{
					$form_data1[$campo] = $valor;
				}
			}
			unset($form_data1["id_presentacion"]);

			$sql_val1 = _query("SELECT id_server FROM presentacion_producto WHERE ".$where1);
			if(_num_rows($sql_val1)>0)
			{
				$insert1 = _update($table1, $form_data1, $where1);
			}
			else
			{
				$insert1 = _insert($table1, $form_data1);
			}
			if($insert1)
			{
				echo "all changes commited";
			}
			else
			{
				echo "sync error";
			}
		}
		function update_presentacion_producto()
		{
			$data =  json_decode($_POST["data"], true);
			$prods_pre = $data["presentacion_producto"];
			$table1 = "presentacion_producto";
			$id_server = $_POST["id_server"];

			$form_data1 = array();
			$where1 = "id_server = '".$id_server."'";
			foreach ($prods_pre as $campo => $valor)
			{
				$form_data1[$campo] = $valor;
			}
			unset($form_data1["id_presentacion"]);
			unset($form_data1["id_sucursal"]);
			unset($form_data1["id_producto"]);

			$update = _update($table1, $form_data1, $where1);
			if($update)
			{
				echo "all changes commited";
			}
			else
			{
				echo "sync error"._error();
			}
		}
		function insert_presentacion_producto_precio()
		{
			$sql_suc = _query("SELECT * FROM access_conf WHERE id_conf='1'");
			$dats_suc = _fetch_array($sql_suc);
			$id_sucur = $dats_suc["id_sucursal"];

			$data =  json_decode($_POST["data"], true);
			$prods_pre = $data["presentacion_producto_precio"];
			$table1 = "presentacion_producto_precio";
			$response = array();

			$form_data1 = array();

			$id_server = $_POST["id_server"];
			$id_server_pre = $_POST["id_server_pre"];
			$sql_pre = _query("SELECT id_presentacion, id_producto FROM presentacion_producto WHERE id_server='$id_server_pre'");
			$datos_pre = _fetch_array($sql_pre);
			$id_presentacionl = $datos_pre["id_presentacion"];
			$id_productol = $datos_pre["id_producto"];

			$where1= "id_server='".$prods_pre["id_server"]."'";

			foreach ($prods_pre as $campo => $valor)
			{
				if($campo == "id_producto")
				{
					$form_data1["id_producto"] = $id_productol;
				}
				else if($campo == "id_sucursal")
				{
					$form_data1["id_sucursal"] = $id_sucur;
				}
				else if($campo == "id_presentacion")
				{
					$form_data1["id_presentacion"] = $id_presentacionl;
				}
				else
				{
					$form_data1[$campo] = $valor;
				}

			}
			unset($form_data1["id_prepd"]);
			$sql_val1 = _query("SELECT id_server FROM presentacion_producto_precio WHERE ".$where1);
			if(_num_rows($sql_val1)>0)
			{
				$insert1 = _update($table1, $form_data1, $where1);
			}
			else
			{
				$insert1 = _insert($table1, $form_data1);
			}
			if($insert1)
			{
				echo "all changes commited";
			}
			else
			{
				echo "sync error";
			}
		}
		function update_presentacion_producto_precio()
		{
			$data =  json_decode($_POST["data"], true);
			$prods_pre = $data["presentacion_producto_precio"];
			$table1 = "presentacion_producto_precio";
			$id_server = $_POST["id_server"];

			$form_data1 = array();
			$where1 = "id_server = '".$id_server."'";
			foreach ($prods_pre as $campo => $valor)
			{
				$form_data1[$campo] = $valor;
			}
			unset($form_data1["id_prepd"]);
			unset($form_data1["id_sucursal"]);
			unset($form_data1["id_producto"]);
			unset($form_data1["id_presentacion"]);
			$update = _update($table1, $form_data1, $where1);
			if($update)
			{
				echo "all changes commited";
			}
			else
			{
				echo "sync error"._error();
			}
		}
		function insert_traslado_detalle_recibido()
		{
			$data =  json_decode($_REQUEST["data"], true);
			$prods_pre = $data["traslado_detalle_recibido"];
			$table1 = "traslado_detalle_recibido";
			$response = array();

			$id_server = $_POST["id_server"];

			$where1= "id_server='".$prods_pre["id_server"]."'";
			foreach ($prods_pre as $campo => $valor)
			{
				if ($campo=="id_server_prod") {
					$sql_a=_fetch_array(_query("SELECT producto.id_producto FROM producto where id_server=$valor"));
					$form_data1[$campo] = $valor;
					$form_data1["id_producto"] = $sql_a['id_producto'];
				}
				else
				{
					if ($campo=="id_server_presen") {
						// code...

						$sql_a=_fetch_array(_query("SELECT presentacion_producto.id_presentacion FROM presentacion_producto where id_server=$valor"));
						$form_data1[$campo] = $valor;
						$form_data1["id_presentacion"] = $sql_a['id_presentacion'];
					}
					else {
						if ($campo=="id_traslado_server") {
							// code...

							$sql_a=_fetch_array(_query("SELECT traslado.id_traslado FROM traslado where id_server=$valor"));
							$form_data1[$campo] = $valor;
							$form_data1["id_traslado"] = $sql_a['id_traslado'];
						}
						// code...
						$form_data1[$campo] = $valor;
					}
				}
			}
			$sql_val1 = _query("SELECT id_server FROM traslado_detalle_recibido WHERE ".$where1);
			if(_num_rows($sql_val1)>0)
			{
				$insert1 = _update($table1, $form_data1, $where1);
			}
			else
			{
				$insert1 = _insert($table1, $form_data1);
			}
			if($insert1)
			{
				echo "all changes commited";
			}
			else
			{
				echo "sync error";
			}
		}
		function insert_traslado()
		{
			$data =  json_decode($_REQUEST["data"], true);
			$prods = $data["traslado"];
			$prods_pre = $data["traslado_detalle"];

			$form_data = array();
			$table = "traslado";
			$table1 = "traslado_detalle";

			$where = "";
			$nprod = count($prods);
			$i=0;
			$response = array();
			foreach ($prods as $campo => $valor)
			{
				$form_data[$campo] = $valor;
			}
			$insert = _insert($table, $form_data);
			$id_local = _insert_id();

			$j = 0;
			foreach ($prods_pre as $pos => $mini_array)
			{
				$form_data1 = array();

				$nprod1 = count($mini_array);

				foreach ($mini_array as $campo => $valor)
				{


					if ($campo=="id_server_prod") {
						$sql_a=_fetch_array(_query("SELECT producto.id_producto FROM producto where id_server=$valor"));
						$form_data1[$campo] = $valor;
						$form_data1["id_producto"] = $sql_a['id_producto'];
					}
					else
					{
						if ($campo=="id_server_presen") {
							// code...

							$sql_a=_fetch_array(_query("SELECT presentacion_producto.id_presentacion FROM presentacion_producto where id_server=$valor"));
							$form_data1[$campo] = $valor;
							$form_data1["id_presentacion"] = $sql_a['id_presentacion'];
						}
						else {
							// code...
							$form_data1[$campo] = $valor;
						}
					}
				}
				$form_data1["id_traslado"]=$id_local;

				$insert1 = _insert($table1, $form_data1);
			}

			if($insert1)
			{
				echo "all changes commited";
			}
			else
			{
				echo "sync error";
			}
		}
		function update_traslado()
		{
			$data =  json_decode($_REQUEST["data"], true);

			$prods = $data["traslado"];
			$id_server = $_POST["id_server"];

			$form_data = array();

			$table = "traslado";
			$where  = "id_server='".$id_server."'";
			foreach ($prods as $campo => $valor)
			{
				$form_data[$campo] = $valor;
			}
			unset($form_data["id_traslado"]);
			$update = _update($table, $form_data,$where);
			if($update)
			{
				echo "all changes commited";
			}
			else
			{
				echo "sync error";
			}
		}
		function search_traslado_detalle_recibido()
		{
				$id = $_POST["id"];

				$sql = _query("SELECT * FROM traslado_detalle_recibido WHERE id_detalle_traslado_recibido  = '$id'");
				$data = _fetch_array($sql);
				$id_server = $data["id_server"];
				$id_sucursal_envia = $data["id_sucursal_origen"];
				$id_sucursal_recive = $data["id_sucursal_destino"];
				unset($data["id_server"]);
				$prep = array(0 => $data);
				$array = array(
					'traslado_detalle_recibido' => $prep,
				);

				$response = array(
					'id_server' => $id_server,
					'id_sucursal_envia' => $id_sucursal_envia,
					'id_sucursal_recive' => $id_sucursal_recive,
					'data' => $array
				);

				echo json_encode($response);
		}
		function search_traslado()
		{
			$q1=_query("UPDATE traslado_detalle JOIN producto ON traslado_detalle.id_producto=producto.id_producto SET traslado_detalle.id_server_prod=producto.id_server WHERE traslado_detalle.id_server_prod=0;");
			$q2=_query("UPDATE traslado_detalle JOIN presentacion_producto ON traslado_detalle.id_presentacion=presentacion_producto.id_presentacion SET traslado_detalle.id_server_presen=presentacion_producto.id_server WHERE  traslado_detalle.id_server_presen=0;");

			$id = $_REQUEST["id"];
			$process = $_REQUEST["action"];
			if($process == "insert")
			{
				$id_verf = $_REQUEST['id_verf'];
			$sql = _query("SELECT * FROM traslado WHERE id_traslado = '$id' ");
			$data = _fetch_array($sql);
			unset($data["id_server"]);

			$sql1 = _query("SELECT * FROM traslado_detalle WHERE id_traslado='$id' ");

			$sql2 = _query("SELECT * FROM log_cambio_local WHERE id_log_cambio='$id_verf'")  ;
			$data2 = _fetch_array($sql2);
			unset($data2["id_server"]);

			$data1 = array();
			while($row = _fetch_array($sql1))
			{
				unset($row["id_server"]);
				$data1[] = $row;
			}

			$response = array(
				'traslado' => $data,
				'traslado_detalle' => $data1,
				'cambio' => $data2,
			);
			echo json_encode($response);
		}
		else
		{
			if ($process == "update")
			{
				$sql = _query("SELECT * FROM traslado WHERE id_traslado = '$id'");
				$data = _fetch_array($sql);
				$id_server = $data["id_server"];
				$id_sucursal_envia = $data["id_sucursal_origen"];
				$id_sucursal_recive = $data["id_sucursal_destino"];
				unset($data["id_server"]);
				$array = array(
					'traslado' => $data,
				);
				$response = array(
					'id_server' => $id_server,
					'id_sucursal_envia' => $id_sucursal_envia,
					'id_sucursal_recive' => $id_sucursal_recive,
					'data' => $array
				);

				echo json_encode($response);
			}
		}

		}
		if (! isset($_POST ['process']))
		{
		}
		else
		{
			if (isset($_POST ['process']))
			{
				switch ($_POST ['process'])
				{
					case 'insert':
					if (isset($_POST ['table']))
					{
						switch ($_POST ['table'])
						{
							case 'productos':
							insert_producto();
							break;
							case 'presentacion_producto':
							insert_presentacion_producto();
							break;
							case 'presentacion_producto_precio':
							insert_presentacion_producto_precio();
							break;
							case 'traslado':
							insert_traslado();
							break;
							case 'traslado_detalle_recibido':
							insert_traslado_detalle_recibido();
							break;
						}
					}
					break;
					case 'update':
					if (isset($_POST ['table']))
					{
						switch ($_POST ['table'])
						{
							case 'productos':
							update_producto();
							break;
							case 'presentacion_producto':
							update_presentacion_producto();
							break;
							case 'presentacion_producto_precio':
							update_presentacion_producto_precio();
							break;
							case 'traslado':
							update_traslado();
							break;
						}
					}
					break;
					case 'search':
					if (isset($_POST ['table']))
					{
						switch ($_POST ['table'])
						{
							case 'productos':
							search_producto();
							break;
							case 'presentacion_producto':
							search_presentacion_producto();
							break;
							case 'presentacion_producto_precio':
							search_presentacion_producto_precio();
							break;
							case 'traslado':
							search_traslado();
							break;
							case 'traslado_detalle_recibido':
							search_traslado_detalle_recibido();
							break;
						}
					}
					break;
					case 'constab':
					search_changes();
					break;
				}
			}
		}
	}
}
