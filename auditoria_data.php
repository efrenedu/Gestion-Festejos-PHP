<?php 
require "conexion_bd.php";
require "fechas.php";

/*write a table with the data required to show in 'Auditoria' Page*/

	
echo"<div class='Table_Container'><table>";
echo"<tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>Usuario</td><td>Accion Realizada</td><td>Fecha</td><td>Hora</td></tr>";

//Verify if exist connection with server
if(validar_conexion()){
	$key=-1;
	$key_val=-1;
	$is_fecha=false;
	$f1="";
	$f2="";
	if(count($_POST)>0){
	    if(isset($_POST["filtro"]) && isset($_POST["filtro_val"])){
	         //filter to Apply 
			 $filtro=$_POST["filtro"];
			 $filtro_val=$_POST["filtro_val"];
			 if($filtro!="" && $filtro_val!=""){
				$filtro= strtolower($filtro);
				if($filtro=="usuario"){
					$filtro="nombre_usuario";
				}
				else if($filtro === "fecha"){
					$is_fecha=true;
					$fechas= explode (";", $filtro_val);
					if(count($fechas)==2){  
				       $f1=strtotime($fechas[0]);
					   $f1=strval(date("d-m-Y",$f1));
					   $f2=strtotime($fechas[1]);
					   $f2=strval(date("d-m-Y",$f2));
					}
				    $filtro= "";
					$filtro_val="";
				}
				if($is_fecha==false){
				   $key=[$filtro];
                   $key_val=[$filtro_val];
				}
			 }
		}
	}		
	$data=get_data_dict("reporte_usuario",["nombre_usuario","accion","fecha","hora"],4,$key,$key_val);
	$contador=0;
	
	//write the data
	if(count($data)>0){
		foreach($data as $dat){
			$correcto=true;
			if($is_fecha==true){
				$correcto=false;
				$fecha_temp=$dat["fecha"];
				if($fecha_temp!=""){
				   if($fecha_temp==$f1 || $fecha_temp==$f2){
					   $correcto=true;
				   }
				   else if( comparar($fecha_temp,$f1)>0 && comparar($fecha_temp,$f2)<0 ){
					   $correcto=true;
				   }
				}
				else{
					 $correcto=false;
				}	
			}
			if($correcto==true && $contador<30){
			   echo "<tr style='width:25%;background:rgb(231,238,255);'>";
			   echo "<td>".$dat["nombre_usuario"]."</td>";
			   echo "<td>".$dat["accion"]."</td>";
			   echo "<td>".$dat["fecha"]."</td>";
			   echo "<td>".$dat["hora"]."</td>";
			   echo "</tr>";
			   $contador=$contador+1;
			}
		}
	}
}
echo"</table></div>";
echo "<br><br>";

?>
