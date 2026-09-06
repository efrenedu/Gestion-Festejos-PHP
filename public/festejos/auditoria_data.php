<?php 
require_once __DIR__."/../../private/festejos/db_config.php";
require "fechas.php";

/*write a table with the data required to show in 'Auditoria' Page*/

	
echo"<div class='Table_Container'><table>";
echo"<tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>Usuario</td><td>Accion Realizada</td><td>Fecha</td><td>Hora</td></tr>";

//Verify if exist connection with server
if(get_conexion()!="OK"){
	echo"</table></div>";
    echo "<br><br>";
    exit;
}
$key=-1;
$key_val=-1;
$is_fecha=false;
$f1="";
$f2="";
if(count($_POST)<=0){
	echo"</table></div>";
    echo "<br><br>";
    exit;
}
if(!isset($_POST["filtro"]) || !isset($_POST["filtro_val"])){
	echo"</table></div>";
    echo "<br><br>";
    exit;	
}
//filter to Apply 
$filtro=$_POST["filtro"];
$filtro_val=$_POST["filtro_val"];
if($filtro!="" && $filtro_val!=""){
	$filtro= strtolower($filtro);
	if($filtro=="usuario"){
		$filtro="nombre_usuario";
	}
	else if($filtro == "fecha"){
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
		$key=$filtro;
        $key_val=$filtro_val;
	}
}

$cond_data=array("conditions_Names"=>array($key),"conditions_Values"=>array($key_val),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  
if($key==-1 || $key_val==-1){
	$cond_data=null;
}
$data=get_data("reporte_usuario",["nombre_usuario","accion","fecha","hora"],$cond_data,null,true);
if($data["status"]=="Error"){
	echo"</table></div>";
    echo "<br><br>";
	exit;
}
$contador=0;
$data=$data["message"];
if(count($data)<=0){
	echo"</table></div>";
    echo "<br><br>";
	exit;
}
$fechas_get=array();
$date_f1=new DateTime($f1);
$date_f2=new DateTime($f2);
foreach($data as $dat){
	$correcto=true;
	if($is_fecha==true){
		$correcto=false;
		$fecha_temp=$dat["fecha"];
		if($fecha_temp==""){
			continue;
		}
		if(count(explode("-",$fecha_temp))<3){
			continue;
		}
		
		$valid=false;
		if( comparar($fecha_temp,$f1)>0 && comparar($fecha_temp,$f2)<0){
			$valid=true;
		}
        else if($fecha_temp==$f1 || $fecha_temp==$f2){
			$valid=true;
		}	
        if($valid==false){
            continue;
		}			
	}
	if( $contador<30){
		echo "<tr style='width:25%;background:rgb(231,238,255);'>";
		echo "<td>".$dat["nombre_usuario"]."</td>";
		echo "<td>".$dat["accion"]."</td>";
		echo "<td>".$dat["fecha"]."</td>";
		echo "<td>".$dat["hora"]."</td>";
		echo "</tr>";
		$contador=$contador+1;
	}
}
	

echo"</table></div>";
echo "<br><br>";

?>
