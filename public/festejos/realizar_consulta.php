<?php
/*Return the data request from the data base as a String */

require_once __DIR__."/../../private/festejos/jwt.php";
require_once __DIR__."/../../private/festejos/db_config.php";
    
session_start();
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
	     echo "Error: Token Data of Server not Found";
	     exit;
     }
     $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="False"){
		echo "Error:Acceso Denegado";
		exit;
    }

}
else{
	 echo "Error:Acceso Denegado";
	 exit;
}

$res="";
if(count($_POST)<=0){
	 echo "Error:Faltan Datos";
	 exit;
}
$requerids_params=array("tabla","fields","filtro","filtro_val");
foreach($requerids_params as $param){
	if(!isset($_POST[$param])){
		echo "Error:Datos Invalidos";
	    exit;
	}
}
$tabla=$_POST["tabla"];
$fields=$_POST["fields"];
$filtro=$_POST["filtro"];
$filtro_val=$_POST["filtro_val"];
$res_conex=get_conexion();
if($res_conex!="OK"){
	echo "Error:{$res_conex}";
	exit;
}
ECHO $filtro.";".$filtro_val;
$cond_data=array("conditions_Names"=>array($filtro),"conditions_Values"=>array($filtro_val),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
if($filtro==-1 || $filtro_val==-1){
	$cond_data=null;
}
$data=get_data($tabla,$fields,$cond_data,null,true);
if($data["status"]=="Error"){
    echo "Error:{$data['message']}";
	exit;
}	
$data=$data["message"];
if(count($data)<=0){
	echo "";
	exit;
}
$res="?"; 
for($i=0;$i<count($data);$i++){
	$valor="";
	$limit=count($fields);
	$count=0;
	foreach($fields as $target_field){
		$valor=$valor.$data[$i][$target_field];
		if($count<$limit-1){
			$valor= $valor.";"; 
		}
		$count+=1;
	}
	$res=$res.$valor."|";
}
			  
		  
			

echo $res;

?>