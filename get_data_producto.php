<?php
   /*get the data of a product */
   require "conexion_bd.php";
   $res="";
   if(count($_POST)>0){
	   $producto=$_POST['producto'];
	   if(isset($producto)){
		  if(validar_conexion()){
	           $data=get_data_dict("producto",[],-1,["nombre_producto"],[$producto]);
			   if(count($data)>0){  
				  $res=":".$data[0]["nombre_producto"].",".$data[0]["serial"].",".$data[0]["precio_alquiler"].",".$data[0]["cantidad"].",".$data[0]["cantidad_disponible"].",".$data[0]["precio"].",".$data[0]["alquilable"];
			   }
           } 
	   }
   }
   echo $res;
?>