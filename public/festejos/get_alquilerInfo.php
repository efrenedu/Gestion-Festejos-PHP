<?php

  
/*Get the data of rented products by a Client */
require_once __DIR__."/../../private/festejos/db_config.php"; 
require "fechas.php";
$res="";
date_default_timezone_set('America/Caracas');
$res_conex=get_conexion();
if($res_conex!="OK"){
	echo "";
	exit;
}
if(count($_POST)<=0){
	echo "";
	exit;
}
if(!isset($_POST["cedula"])){
	echo "";
	exit;
}
$cedula=$_POST["cedula"];
$cond_data=array("conditions_Names"=>array("CI_cliente"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	   
$data=get_data("producto_alquilado",["nombre_producto","formato","cantidad_alquilada","precio_unidad","fecha_devolucion"],$cond_data,null,true);
if($data["status"]=="Error"){
	echo "";
	exit;
}
$data=$data["message"];
if(count($data)<=0){
	echo "";
	exit;
}
$temp_res="";
$temp_resI="0:";
$dias_retraso=0;
for($i=0;$i<count($data);$i++){
	$valor=$data[$i]["nombre_producto"].",".$data[$i]["formato"].",".$data[$i]["cantidad_alquilada"].",".$data[$i]["precio_unidad"].",".$data[$i]["fecha_devolucion"].";";
	$temp_res=$temp_res.$valor;
	if($i==0){
		$fecha_devol=$data[$i]["fecha_devolucion"];
		$fecha_actual=strval( date("d-m-Y")); 
		$temp=comparar($fecha_actual,$fecha_devol);
        if($temp>0){
			$dias_retraso=strval($temp);
			$temp_resI=$dias_retraso.":";
		}
	}	
}
$res=$temp_resI.$temp_res;
echo $res;

?>