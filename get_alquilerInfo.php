<?php

  
/*Get the data of rented products by a Client */
require "conexion_bd.php";
require "fechas.php";
$res="";
date_default_timezone_set('America/Caracas');
if(validar_conexion()){
	if(count($_POST)>0){
		if(isset($_POST["cedula"])){
            $cedula=$_POST["cedula"];
			$data=get_data_dict("producto_alquilado",["nombre_producto","formato","cantidad_alquilada","precio_unidad","fecha_devolucion"],5,["CI_cliente"],[$cedula]);
			if(count($data)>0){
				$temp_res="";
				$temp_resI="0:";
				$dias_retraso=0;
				for($i=0;$i<count($data);$i++){
					$valor=$data[$i]["nombre_producto"].",".$data[$i]["formato"].",".$data[$i]["cantidad_alquilada"].",".$data[$i]["precio_unidad"].",".$data[$i]["fecha_devolucion"].";";
					$temp_res=$temp_res.$valor;
					if($i==0){
						$fecha_devol=$data[$i]["fecha_devolucion"];
						$fecha_actual=strval( date("d-m-Y")); 
						$temp=comparar($fecha_actual,$fecha_devol);
                        if($temp>0){
							$dias_retraso=strval($temp);
							$temp_resI=$dias_retraso.":";
						}
					}	
				}
				$res=$temp_resI.$temp_res;
			}
		}
	}
}
echo $res;

?>