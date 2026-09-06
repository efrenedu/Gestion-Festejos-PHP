<?php
/*Verify the Phone Number No is Associted to others Clients*/
   require_once __DIR__."/../../private/festejos/db_config.php";
   $res="";
   $res_conex=get_conexion();
   if($res_conex!="OK"){
	   echo "Error:{$res_conex}";
	   exit;
   }
   if(count($_POST)<=0){
	  echo "Error: Datos Invalidos";
	  exit; 
   }
   if(!isset($_POST["cedula"]) || !isset($_POST["telefono"])){
  	  echo "Error: Datos Invalidos";
	  exit;  
   }
   $telefono=$_POST['telefono'];
   $cedula=$_POST['cedula'];
   $join_data=array();
   $join_data["telefono"]=array("query_field"=>array("numero_telefono"),"share_fields"=>array("field"=>"CI_trabaj","table_reference"=>"trabajador"),"Conditions_join"=>null); 
   $cond_data=array("conditions_Names"=>array("CI_trabaj"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("!="));	 	  
			        
   $data_trabajs=get_data("trabajador",["CI_trabaj"],$cond_data,$join_data,true);
   if($data_trabajs["status"]=="Error"){
	  echo "Error:{$data_trabajs["message"]}";
	  exit;  
   }
   if(count($data_trabajs)<=0){
	   echo ":OK";
       exit;	   
   }
   $data_trabajs=$data_trabajs["message"];
   $found=false;
   for($i=0;$i<count($data_trabajs);$i++){
		if($data_trabajs[$i]["numero_telefono"]==$telefono){
			$found=true;
			$i=count($data_trabajs);
		}				 
   }
   if($found==false){
		$res=":OK"; 
   }
   echo $res;
?>