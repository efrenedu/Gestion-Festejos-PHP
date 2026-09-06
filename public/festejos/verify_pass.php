<?php

require_once __DIR__."/../../private/festejos/db_config.php";  
require_once __DIR__."/../../private/festejos/jwt.php";
if(count($_POST)<=0){
	echo "Error:Faltan Datos";
	exit;
}
if(!isset($_POST["password_required"])){
	  echo "Error:Datos Invalidos";
	  exit;
}
session_start();
$usuario_actual="";
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
	     echo "Error:Internal Error,Token del Servidor no Enocntrado";
		 exit;
     }
     $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="False"){
		echo "Error:{$res['Message']}";
		exit;
    }
	$usuario_actual=$res["Message"]["Id_usr"];
	
      
}
else{
	 echo "Error:Acceso Denegado , Usuario No Valido";
	 exit;
}

$res_conex=get_conexion();
if($res_conex!="OK"){
	echo "Error:{$res_conex}";
	exit;
}
$pass=$_POST["password_required"];
$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($usuario_actual),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
				
$dat_usr=get_data("usuario",["contrasena"],$cond_data,null,true);
if($dat_usr["status"]=="Error"){
   echo "Error:{$dat_usr['message']}";
   exit;	
}
$dat_usr=$dat_usr["message"];

if(count($dat_usr)<=0){
   echo "Error:Usuario Inexistente";
   exit;
}	
if(password_verify($pass,$dat_usr[0]["contrasena"])){
	echo ":OK";
	exit;
}
echo "False";


?>