<?php

$res="";
require_once __DIR__."/../../private/festejos/jwt.php";
require_once __DIR__."/../../private/festejos/db_config.php";
session_start();
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
		 echo "";
	     exit;
     }
     $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="False"){
		echo "";
		exit;
    }
	$acceso=$res["Message"]["Acceso"];
	if($acceso=="Visitante"){
		echo "";
	}
	
  	
}
else{
	 echo "";
	 exit();
}
if(count($_POST)<=0){
	 echo "";
	 exit;
}
if(!isset($_POST["cedula"])){
	 echo "";
	 exit;
}
/*get the data of a Client*/
$cedula=$_POST['cedula'];
if(get_conexion()!="OK"){
	 echo "";
	 exit;
}
$join_data=array();
$join_data["nombre"]=array("query_field"=>array("nombre","segundo_nombre","apellido","segundo_apellido"),"share_fields"=>array("field"=>"id_nombre","table_reference"=>"cliente"),"Conditions_join"=>null);
$cond_data=array("conditions_Names"=>array("CI_cliente"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
	
$data=get_data("cliente",["CI_cliente","estatus"],$cond_data,$join_data,true);
if($data["status"]=="Error"){
	 echo "";
	 exit;	
}
$data=$data["message"];
if(count($data)<=0){
	 echo "";
	 exit;
}
$estatus=$data[0]["estatus"];
$res=":".$cedula.",".$data[0]["nombre"].",".$data[0]["segundo_nombre"].",".$data[0]["apellido"].",".$data[0]["segundo_apellido"].",".$estatus;	 

echo $res;
?>