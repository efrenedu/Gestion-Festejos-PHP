<?php

/*Process The Request for Register a Client*/
require_once __DIR__."/../../private/festejos/jwt.php";
require_once __DIR__."/../../private/festejos/db_config.php";
session_start();
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
		 echo ":Acceso Denegado";
	     exit;
     }
     $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="False"){
		echo ":Acceso Denegado";
		exit;
    }
	$acceso=$res["Message"]["Acceso"];
	if($acceso=="Visitante"){
		echo ":Acceso Denegado";
	}
  	
}
else{
	 echo ":Acceso Denegado";
	 exit();
}
if(count($_POST)<=0){
	 echo ":Faltan Datos";
	 exit;
}
$requerids_params=array("cedula","nombres","apellidos");
foreach($requerids_params as $param){
	if(!isset($_POST[$param])){
		 echo ":Data Invalida";
	     exit;
	}
}

$msg=":failed";
date_default_timezone_set('America/Caracas'); 
$cedula=$_POST["cedula"];
$nombres=$_POST["nombres"];
$apellidos=$_POST["apellidos"];
$res_conex=get_conexion();
if($res_conex!="OK"){
	 echo ":{$res_conex}";
	 exit;
}
$exist=id_exist("cliente","CI_cliente",$cedula);
if($exist["status"]=="Error"){
	echo ":{$exist['message']}";
   exit;
}
$exist=$exist["message"];
if($exist!="False"){	
   echo ":Cliente Existente";
   exit;
}
$dat_name=[];
$nombres=ucwords(strtolower($nombres));
$apellidos=ucwords(strtolower($apellidos));
$nomb_f=explode(" ",$nombres);
$apellid_f=explode(" ",$apellidos);
if(count($nomb_f)==2){
	$dat_name["nombre"]=$nomb_f[0];
	$dat_name["segundo_nombre"]=$nomb_f[1];
}else{
	$dat_name["nombre"]=$nombres;
	$dat_name["segundo_nombre"]="";
}
if(count($apellid_f)==2){
	$dat_name["apellido"]=$apellid_f[0];
	$dat_name["segundo_apellido"]=$apellid_f[1];				
}else{
	$dat_name["apellido"]=$apellidos;
	$dat_name["segundo_apellido"]="";
}
$fecha=strval(date("d-m-Y"));
$dat_name["id_nombre"]="NameClient-{$cedula}";
$res_add=add_data("nombre",$dat_name,true);
if($res_add["status"]=="Error"){
	 echo ":{$res_add['message']}";
     exit;
}
$dat_client=array("CI_cliente"=>$cedula,"id_nombre"=>$dat_name["id_nombre"],"estatus"=>"solvente");
$res_add=add_data("cliente",$dat_client,true,true);
if($res_add["status"]=="Error"){
	 echo ":{$res_add['message']}";
     exit;
}
$msg=":OK";
echo $msg;
?>