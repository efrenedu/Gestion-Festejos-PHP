<?php

  
  require "conexion_bd.php";
  $res="";
  if(validar_conexion()){
	  if(count($_POST)>0){
		  $cedula=$_POST['cedula'];
		  if(isset($cedula)){
			  $data_t=get_data_dict("trabajador",[],-1,["CI_trabaj"],[$cedula]);
			  if(count($data_t)>0){
				  $data_name=get_data_dict("nombre",["nombre","segundo_nombre","apellido","segundo_apellido"],4,["id_nombre"],[$data_t[0]["id_nombre"]]);
			      if(count($data_name)>0){
					
					  $nombres=$data_name[0]["nombre"];
					  if($data_name[0]["segundo_nombre"]!=""){
						  $nombres=$nombres." ".$data_name[0]["segundo_nombre"];
						  
					  }
					  $apellidos=$data_name[0]["apellido"];
					  if($data_name[0]["segundo_apellido"]!=""){
						  $apellidos=$apellidos." ".$data_name[0]["segundo_apellido"];
						  
					  }
					  
					  $res=":". $data_t[0]["CI_trabaj"].",".$nombres.",".$apellidos.",". $data_t[0]["cargo"].",". $data_t[0]["turno"];
					  $res=$res.",".$data_t[0]["nivel_academico"].",".$data_t[0]["edad"].",".$data_t[0]["estatus"];
					  $data_telefs=get_data_dict("telefono",["numero_telefono"],1,["CI_trabaj"],[ $cedula]);
			          if(count($data_telefs)>0){
						   $res=$res.";";
						  for($i=0;$i<count($data_telefs);$i++){
							  $telef=$data_telefs[$i]["numero_telefono"];
							  $res=$res.$telef.",";
						  }
					  }
					  

					 
				  }
			  }
		  }
	  }
  }
  echo $res;
?>