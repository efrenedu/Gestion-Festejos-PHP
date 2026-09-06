<?php

  
  require_once __DIR__."/../../private/festejos/db_config.php";
  $res="";
  if(get_conexion()!="OK"){
	 echo "Error: Error de Conexion"; 
	 exit;	
  }
  if(count($_POST)<=0){
	  echo "Error:Datos Invalidos";
	  exit;	
  }
  if(!isset($_POST["cedula"])){
	 echo "Error:Datos Invalidos"; 
	 exit;	
  }
  $cedula=$_POST['cedula'];
  $join_data=array();
  $join_data["nombre"]=array("query_field"=>array("nombre","segundo_nombre","apellido","segundo_apellido"),"share_fields"=>array("field"=>"id_nombre","table_reference"=>"trabajador"),"Conditions_join"=>null);         
  $cond_data=array("conditions_Names"=>array("CI_trabaj"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  	
  $data_t=get_data("trabajador",["CI_trabaj","cargo","turno","nivel_academico","edad","estatus"],$cond_data,$join_data,true);
  if($data_t["status"]=="Error"){
	 echo "Error:{$data_t['message']}";
     exit;	 
  }
  $data_t=$data_t["message"];
  if(count($data_t)<=0){
	 echo "Error:No Existe el Trabajador";
	 exit;
  }
   $nombres=$data_t[0]["nombre"];
   if($data_t[0]["segundo_nombre"]!=""){
	  $nombres=$nombres." ".$data_t[0]["segundo_nombre"];
						  
   }
   $apellidos=$data_t[0]["apellido"];
   if($data_t[0]["segundo_apellido"]!=""){
		$apellidos=$apellidos." ".$data_t[0]["segundo_apellido"];
						  
   }
   $res=":".$data_t[0]["CI_trabaj"].",".$nombres.",".$apellidos.",". $data_t[0]["cargo"].",". $data_t[0]["turno"];
   $res=$res.",".$data_t[0]["nivel_academico"].",".$data_t[0]["edad"].",".$data_t[0]["estatus"].";";
   $data_telefs=get_data("telefono",array("numero_telefono"),$cond_data,null,true);
   
   if($data_telefs["status"]=="Error" ){
	   echo "Error:{$data_telefs['message']}";
	   exit;
   }
   $data_telefs=$data_telefs["message"];
   if(count($data_telefs)>0){
	   for($i=0;$i<count($data_telefs);$i++){
			$telef=$data_telefs[$i]["numero_telefono"];
			$res=$res.$telef.",";
	   }
   }
   echo $res;
?>