<?php

/*Process The Request for Register a Client*/

$msg=":failed";
require "conexion_bd.php";
date_default_timezone_set('America/Caracas'); 
if(count($_POST)>0){
	$cedula=$_POST["cedula"];
	$nombres=$_POST["nombres"];
	$apellidos=$_POST["apellidos"];
	if(isset($cedula) && isset($nombres) &&isset($apellidos) ){
		if(validar_conexion()){
			if(id_exist("cliente","CI_cliente",$cedula)==false){	
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
			   $dat_name["id_nombre"]=strval(generate_id("nombre","id_nombre",true));
			   add_data_dict("nombre",$dat_name);
			   $dat_client=array("CI_cliente"=>$cedula,"id_nombre"=>$dat_name["id_nombre"],"estatus"=>"solvente");
			   add_data_dict("cliente",$dat_client);
			   $msg=":OK";
			}
			else{
				 $msg=":client exist";
			}
		}
		else{
			 $msg=":fail connect";
		}
	}
	else{
		 $msg=":invalid fields";
	}
}
echo $msg;
?>