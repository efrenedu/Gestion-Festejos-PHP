<?php
/*Verify the Phone Number No is Associted to others Clients*/
require "conexion_bd.php";
  $res="";
  if(validar_conexion()){
	  if(count($_POST)>0){
		  $telefono=$_POST['telefono'];
		  $cedula=$_POST['cedula'];
		  if(isset($telefono) && isset($cedula)){
			  $data_trabajs=get_data_dict("trabajador",["CI_trabaj"],1,-1,-1);
              if(count($data_trabajs)>0){
                 $found=false;
				 for($i=0;$i<count($data_trabajs);$i++){
					 if($data_trabajs[$i]["CI_trabaj"]!=$cedula){
						 $telefs=get_data_dict("telefono",["numero_telefono"],1,["CI_trabaj"],[$data_trabajs[$i]["CI_trabaj"]]);
					     if(count($telefs)>0){
							for($j=0;$j<count($telefs);$j++){
								if($telefs[$j]["numero_telefono"]==$telefono){
									$j=count($telefs);
									$i=count($data_trabajs);
									$found=true;
								}
							}
						 }
					 }
				 }
				 if($found==false){
					$res=$res.":OK"; 
				 }
			  }     
              else{
                $res=$res.":OK";
			  }				  
		  }
	  }
  }
  echo $res;



?>