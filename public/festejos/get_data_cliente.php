<?php

$res="";
require "conexion_bd.php";

/*get the data of a Client*/
if(count($_POST)>0){
	$cedula=$_POST['cedula'];
	if(isset($cedula)){
		if(validar_conexion()){
		   $data=get_data_dict("cliente",["CI_cliente","id_nombre","estatus"],3,["CI_cliente"],[$cedula]);
		   if(count($data)>0){
			   $data_nombre=get_data_dict("nombre",["nombre","segundo_nombre","apellido","segundo_apellido"],4,["id_nombre"],[ $data[0]["id_nombre"]]);
		       $estatus=$data[0]["estatus"];
			   if(count($data_nombre)>0){
				     $res=":".$cedula.",".$data_nombre[0]["nombre"].",".$data_nombre[0]["segundo_nombre"].",".$data_nombre[0]["apellido"].",".$data_nombre[0]["segundo_apellido"].",".$estatus;	 
			   }
		   }
	    }
	}
}
echo $res;
?>