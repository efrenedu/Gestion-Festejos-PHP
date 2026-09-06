<?php

require_once __DIR__."/../../private/festejos/jwt.php";
require_once __DIR__."/../../private/festejos/db_config.php";
        
session_start();
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
	     echo "Error:Token File Not Found";
	     exit;
     }
     $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="False"){
		echo "Error:Token Invalido";
		exit;
    }
	$acceso=$res["Message"]["Acceso"];
	if($acceso=="Visitante"){
		echo "Error:Acceso Denegado";
		exit;
	}
	
}
else{
	 echo "Error:Acceso Denegado";
	 exit();
}
$res=":";
$res_conex=get_conexion();
if($res_conex!="OK"){
	echo "Error:{$res_conex}";
	exit;
}
if(count($_POST)<=0){
	echo "Error:Faltan Datis";
	exit;
}
if(!isset($_POST['fecha']) || !isset($_POST['turno'])){
	echo "Error:Datos Invalidos";
	exit;
}
$fecha=$_POST['fecha'];
$turno=$_POST['turno'];
$cond_data=array("conditions_Names"=>array("estatus","CI_trabaj","cargo","cargo","cargo","turno"),"conditions_Values"=>array("activo","000000","Directivo","Administrativo","Atencion al Cliente",$turno),"condition_Types"=>array("and","and","and","and","and","and"),"conditions_Verify"=>array("=","!=","!=","!=","!=","="));	 
$join_data=array();
$join_data["nombre"]=array("query_field"=>array("nombre","segundo_nombre","apellido","segundo_apellido"),"share_fields"=>array("field"=>"id_nombre","table_reference"=>"trabajador"),"Conditions_join"=>null);				   
   
$data_temp=get_data("trabajador",["CI_trabaj","id_nombre","turno","cargo"],$cond_data,$join_data,true);
if($data_temp["status"]=="Error"){
    echo "Error:{$data_temp['message']}";
	exit;
}		
$data_temp=$data_temp["message"];
if(count($data_temp)<=0){
    echo ":";
	exit;

}	
for($i=0;$i<count($data_temp);$i++){
	 $nombre="";
     $apellido="";
	 $nombre=$data_temp[0]["nombre"];
	 if($data_temp[0]["segundo_nombre"]!=""){
		$nombre=$nombre." ".$data_temp[0]["segundo_nombre"];
	 }
	 $apellido=$data_temp[0]["apellido"];
	 if($data_temp[0]["segundo_apellido"]!=""){
		  $apellido=$apellido." ".$data_temp[0]["segundo_apellido"];
	 }
	  $res=$res.$data_temp[$i]["CI_trabaj"].",".$nombre.",".$apellido.",".$data_temp[$i]["turno"].";";				 
}

echo $res;

?>