<?php  
  
  /*Build a Pdf for a Consult Page ('Productos Alquilados' or 'Fiestas')*/
  require "conexion_bd.php";
  date_default_timezone_set('America/Caracas');
	  
  $res="?";
  if(count($_POST)>0){  
    $consult_type=$_POST["Consult_Type"];
	$table="";
	$fields=array();
	if(isset($consult_type)){
		if($consult_type=="Fiesta"){
		  $table="fiesta";
		  $fields=array("estatus","CI_cliente","fecha","hora","lugar","publico","tipo_fiesta");
		  
	   }
	   else{
		  $table="producto_alquilado";
		  $fields=array("nombre_producto","CI_cliente","formato","cantidad_alquilada","precio_unidad","lugar","fecha_devolucion");
		  
	   }
	}
	$table=$_POST["Table_Search"];
	$fields=$_POST["Fields"];
	
	if($table!="" && count($fields)>0){
       if(validar_conexion()){
         $data_t=get_data_dict($table,$fields,count($fields),-1,-1);
	     if(count($data_t)>0){
            ob_start();
			require('fpdf/fpdf.php');
			ob_end_flush(); 
		 }			 
	   }	   
	}
  }
  echo $res;

?>

