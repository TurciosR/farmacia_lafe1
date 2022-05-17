<?php
//----------------------------------------------------//
/* -----------------CONF PARA MENSAJES --------------*/
	$hostnameb = "api-sms.dyndns.org";
    $usernameb = "sms";
    $passwordb = "sms123";
    $dbnameb = "sms";

	$conexionb = mysqli_connect("$hostnameb","$usernameb","$passwordb","$dbnameb");
	if (mysqli_connect_errno()){
		echo "Error en conexión MySQL: " . mysqli_connect_error();
	}
setlocale(LC_TIME, "es_SV.UTF-8");
date_default_timezone_set("America/El_Salvador");

function _queryb($sql_string){
	global $conexionb;
  // Cambiar el set character a utf8
    mysqli_set_charset($conexionb, "utf8");
	$queryb=mysqli_query($conexionb,$sql_string);
	return $queryb;
}
// Begin functions queries
function _fetch_arrayb($sql_string){
	global $conexionb;
	$fetchedb = mysqli_fetch_array($sql_string,MYSQLI_ASSOC);
	return $fetchedb;
}

// funciones insertar
function _insertb($table_name, $form_data){
    // retrieve the keys of the array (column titles)
	$form_data2=array();
	$variable='';
	// retrieve the keys of the array (column titles)
	$fields = array_keys ( $form_data );
	// join as string fields and variables to insert
	$fieldss = implode ( ',', $fields );
	//$variables = implode ( "','", $form_data ); U+0027
	foreach($form_data as $variable){
		$var1=preg_match('/\x{27}/u', $variable);
		$var2=preg_match('/\x{22}/u', $variable);
		if($var1==true || $var2==true){
		 $variable = addslashes($variable);
		}
		array_push($form_data2,$variable);
    }
    $variables = implode ( "','",$form_data2 );

    // build the query
    $sql = "INSERT INTO " . $table_name . "(" . $fieldss . ")";
    $sql .= "VALUES('" . $variables . "')";
    // run and return the query result resource
    return _queryb($sql);
}

function db_closeb(){
	global $conexionb;
	mysqli_close($conexionb);
}
// the where clause is left optional incase the user wants to delete every row!
function _deleteb($table_name, $where_clause='')
{
    // check for optional where clause
    $whereSQL = '';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add keyword
            $whereSQL = " WHERE ".$where_clause;
        } else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // build the query
    $sql = "DELETE FROM ".$table_name.$whereSQL;
	return _queryb($sql);
}
// again where clause is left optional
function _updateb($table_name, $form_data, $where_clause='')
{
    // check for optional where clause
    $whereSQL = '';
    $form_data2=array();
	$variable='';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add key word
            $whereSQL = " WHERE ".$where_clause;
        } else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE ".$table_name." SET ";

    // loop and build the column /
    $sets = array();
    //begin modified
	foreach($form_data as $index=>$variable){
		$var1=preg_match('/\x{27}/u', $variable);
		$var2=preg_match('/\x{22}/u', $variable);
		if($var1==true || $var2==true){
		 $variable = addslashes($variable);
		}
		$form_data2[$index] = $variable;
    }
    foreach ( $form_data2 as $column => $value ) {
		$sets [] = $column . " = '" . $value . "'";
	}
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;
    // run and return the query result
    return _queryb($sql);
}
function _errorb(){
  global $conexionb;
    return mysqli_error($conexionb);
}
function nombre_diab($fecha)
{
    return utf8_decode(strftime("%A %d %B de %Y",strtotime($fecha)));
}
function quitar_tilde($cadena)
{
    $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹","'","`");
    $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E"," "," ");
    $texto = str_replace($no_permitidas, $permitidas ,$cadena);
    return $texto;
}
function horab($hora)
{
  $hora_pre = date_create($hora);
  $hora_pos = date_format($hora_pre, 'g:i A');
  return $hora_pos;
}
//----------------------------------------------------//
/* -----------------CONF PARA MENSAJES --------------*/
?>
