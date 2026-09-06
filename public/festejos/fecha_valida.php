<?php

require "fechas.php";
$res=":NO";
//verify if the required date is valid
if(count($_POST)>0){
	$fecha1=$_POST["fecha"];
	if(isset($fecha1)){
	    $fecha_actual=strval(date("d-m-Y"));
	    if($fecha1!=$fecha_actual){
		    if(comparar($fecha1,$fecha_actual)>0){
			    $res=":OK";
		    }
	   }
	}
}
echo $res;	

?>