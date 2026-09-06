<?php
/*Return the data request from the data base as a String */

require "conexion_bd.php";
$res="";
if(count($_POST)>0){
	if(isset($_POST["tabla"]) && isset($_POST["fields"]) && isset($_POST["num_fields"]) && isset($_POST["filtro"]) && isset($_POST["filtro_val"]) ){
          $tabla=$_POST["tabla"];
		  $fields=$_POST["fields"];
		  $num_fields=$_POST["num_fields"];
		  $filtro=$_POST["filtro"];
		  $filtro_val=$_POST["filtro_val"];
		  if(validar_conexion()){
			  $data=get_data_dict($tabla,$fields,$num_fields,$filtro,$filtro_val);
			  if(count($data)>0){
				 $res="?"; 
				 for($i=0;$i<count($data);$i++){
					 $valor="";
					 for($j=0;$j<$num_fields;$j++){
						 $valor=$valor.$data[$i][$fields[$j]];
						 if($j<$num_fields-1){
						     $valor= $valor.";"; 
						 }
					 }
					$res=$res.$valor."|";
				 }
			  }
		  }
	}			
}
echo $res;

?>