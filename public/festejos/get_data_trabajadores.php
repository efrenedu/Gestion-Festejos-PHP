<?php

require "conexion_bd.php";
$res=":";
if(validar_conexion()){
	if(count($_POST)>0){
		
		 if(isset($_POST['fecha']) && isset($_POST['turno'])){
			 $fecha=$_POST['fecha'];
			 $turno=$_POST['turno'];
			 $data_temp=get_data_dict("trabajador",["CI_trabaj","id_nombre","turno","cargo"],4,["estatus"],["activo"]);
			
			 if(count($data_temp)>0){
				
				 for($i=0;$i<count($data_temp);$i++){
					 
					 $valid_cargo=false;
					 $valid_id=true;
					 $cargo=$data_temp[$i]["cargo"];
					 if($cargo!="Directivo" && $cargo!="Administrativo" && $cargo!="Atencion al Cliente"){
						 $valid_cargo=true;
					 }
					 if($data_temp[$i]["CI_trabaj"]=="000000"){
						 $valid_id=false;
					 }
					 if($data_temp[$i]["turno"]==$turno && $valid_cargo==true && $valid_id==true){
						 $data_name=get_data_dict("nombre",["nombre","segundo_nombre","apellido","segundo_apellido"],4,["id_nombre"],[$data_temp[$i]["id_nombre"]]);
						 if(count($data_name)>0){
						    $nombre="";
				            $apellido="";
				            $nombre=$data_name[0]["nombre"];
				            if($data_name[0]["segundo_nombre"]!=""){
						         $nombre=$nombre." ".$data_name[0]["segundo_nombre"];
				           }
				            $apellido=$data_name[0]["apellido"];
				            if($data_name[0]["segundo_apellido"]!=""){
					              $apellido=$apellido." ".$data_name[0]["segundo_apellido"];
				            }
				            
						    $res=$res.$data_temp[$i]["CI_trabaj"].",".$nombre.",".$apellido.",".$data_temp[$i]["turno"].";";
						 }
					 }
				 }
				 
					 
			 }
		 }
	}
	
}
echo $res;

?>