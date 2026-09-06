<?php
   /*get the data of a product */
   require_once __DIR__."/../../private/festejos/db_config.php";
   $res="";
   if(count($_POST)<=0){
	   echo "Error:Faltan Datos";
	   exit;
   }
   if(!isset($_POST["producto"])){
	   echo "Error:Datos Invalidos"; 
	   exit;
   }
   $producto=$_POST['producto'];
   $res_conex=get_conexion();
   if($res_conex!="OK"){
	   echo "Error:{$res_conex}";
       exit;	   
   } 
   $cond_data=array("conditions_Names"=>array("nombre_producto"),"conditions_Values"=>array($producto),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
   $data=get_data("producto",["nombre_producto","serial","precio_alquiler","cantidad","cantidad_disponible","precio","alquilable"],$cond_data,null,true);
   if($data["status"]=="Error"){
	   echo "Error:{$data['message']}";
	   exit;
   }
   $data=$data["message"];
   if(count($data)>0){  
	   $res=":".$data[0]["nombre_producto"].",".$data[0]["serial"].",".$data[0]["precio_alquiler"].",".$data[0]["cantidad"].",".$data[0]["cantidad_disponible"].",".$data[0]["precio"].",".$data[0]["alquilable"];
   }   
   
   echo $res;
?>