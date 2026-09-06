
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Bienvenido</title>
	<link rel="stylesheet" type="text/css" href="style_session.css">
</head>
<body>
<center>
<br>
<div id="titleBox">
<image id="logo" src="images/globos.png" width="100" height="100"/>
<h1 id="title">Logistica & Festejos Premier</h1>
</div>
<div id="content1">

<?php  

/*Manage the Access from Users*/

require "conexion_bd.php";
require "fechas.php";
verificar_conexion();
set_default();
session_start();
$usuario = strtolower($_POST['nombre']);
$clave = $_POST['contrasena'];

if(isset($usuario) && isset($clave)){

  $clave=encript($clave);
  $data =get_data_dict("usuario",["nombre_usuario","permiso","bloqueado","id_intento"],4,["nombre_usuario","contrasena"],[$usuario,$clave]);
  $data2=get_data_dict("usuario",["nombre_usuario","id_intento","bloqueado"],3,["nombre_usuario"],[$usuario]);
  date_default_timezone_set('America/Caracas');
  if (count($data)>0) {
	   $bloq=$data[0]["bloqueado"];
	   if($bloq=="true"){
		 //User Blocked
		 $data_intentos=get_data_dict("intentos_usuario",["last_fecha","last_hora"],2,["id_intento"],[$data[0]["id_intento"]]);
      	 if(count($data_intentos)>0){
            if($data_intentos[0]["last_fecha"]!="..."){	
				$f_actual=strval(date("d-m-Y"));
				$h_actual=strval(date("H:i:s"));
		        $t_usr=strtotime($data_intentos[0]["last_fecha"]." ".$data_intentos[0]["last_hora"]."+1 hour");
		        $bloqueo_d=strval(date("d-m-Y H:i:s",$t_usr));
		        $bloqueo_d=explode(" ",$bloqueo_d);
				if(count($bloqueo_d)==2){
				    $date_bloqueo=$bloqueo_d[0];
				    $time_bloqueo=$bloqueo_d[1];
		            if( $date_bloqueo==$f_actual || (comparar($date_bloqueo,$f_actual)<0)){
						 if($h_actual==$time_bloqueo || (calcular_tiempo($time_bloqueo,$h_actual)<0)){
							update_data("intentos_usuario",["num_intentos","last_fecha","last_hora"],["0","...","..."],3,["id_intento"],[$data[0]["id_intento"]]);
	                        update_data("usuario",["bloqueado"],["false"],1,["nombre_usuario"],[$usuario]);
						    $bloq="false";
						 }
				   }
				}
			}
		 }			 
	   }
	   if($bloq=="false"){
		    //Valid Access
		    update_data("intentos_usuario",["num_intentos","last_fecha","last_hora"],["0","...","..."],3,["id_intento"],[$data[0]["id_intento"]]);
			$data_reporte=array("id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)) , "nombre_usuario"=>$data[0]["nombre_usuario"] , "accion"=>"Iniciar Sesion" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
			add_data_dict("reporte_usuario",$data_reporte);
			$_SESSION['username'] = $data[0]["nombre_usuario"];
            $_SESSION['acceso_user'] =$data[0]["permiso"];

            //Remove old Reports of Users
			$dat_reports_usr=get_data_dict("reporte_usuario",["id_reporte_usr","fecha"],2,-1,-1);
			if(count($dat_reports_usr)>0){
				foreach ($dat_reports_usr as $rep){
					$id_rep=$rep["id_reporte_usr"];
					$fech_rep=$rep["fecha"];
					$dif=comparar(strval(date("d-m-Y")),$fech_rep);
					if($dif>=60){
						//remove report of 60 days olds
						delete_data("reporte_usuario",["id_reporte_usr"],[$id_rep]);    
					}
				}
			}
            //Remove old Reports of Process
			$dat_reports=get_data_dict("reporte",["id_reporte","src_reporte","fecha"],3,-1,-1);
			if(count($dat_reports)>0){
				foreach ($dat_reports as $rep){
					$id_rep=$rep["id_reporte"];
					$fech_rep=$rep["fecha"];
					$src=$rep["src_reporte"];
					$dif=comparar(strval(date("d-m-Y")),$fech_rep);
					if($dif>=30){
						//remove report of 30 days olds
						unlink($src);
						delete_data("reporte",["id_reporte"],[$id_rep]);    
					}
				}
			}
			//Remove Pendents Finished Party 
			$fiestas=get_data_dict("fiesta",["id_fiesta","fecha","hora"],3,-1,-1);
			if(count($fiestas)>0){
				 foreach($fiestas as $dat){
					 $target=$dat["id_fiesta"];
					 $dif=comparar($dat["fecha"],strval(date("d-m-Y")));
					 if($dif<0){
						    $data_reserv=get_data_dict("producto_reservado",["nombre_producto","formato","cantidad_reservada"],3,["id_fiesta"],[$target]);
                            if(count($data_reserv)>0){
								foreach($data_reserv as $dat){
									if(count($dat)>0){
										$id_prod=$dat["nombre_producto"];
										$format=$dat["formato"];
										$cant=intval($dat["cantidad_reservada"]);
										if($format=="Pack(P)"){
											$cant=$cant*10;
										}
										else if($format=="Pack(M)"){
											$cant=$cant*25;
										}
										else if($format=="Pack(G)"){
											$cant=$cant*50;
										}
										$prod_dat=get_data_dict("producto",["cantidad_disponible"],1,["nombre_producto"],[$id_prod]);
										if(count($prod_dat)>0){
											$old_cant=intval($prod_dat[0]["cantidad_disponible"]);
											$old_cant=$old_cant+$cant;
											update_data("producto",["cantidad_disponible","estatus"],[strval($old_cant),"disponible"],2,["nombre_producto"],[$id_prod]);
										}
									}
								}
							}
							delete_data("personal_fiesta",["id_fiesta"],[$target]);
			                delete_data("producto_reservado",["id_fiesta"],[$target]);
							delete_data("fiesta",["id_fiesta"],[$target]);    
					 }
				 }
			}
			header ("location: paginaprincipal.php");
	   }
	   else{
		   //Invalid Access: User Blocked
		   echo "<div id='error_msg'><h2 id='error_text'>El Usuario Esta Bloqueado</h2></div>";
	       echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
           echo "<a class='boton1' href='loggin.php'>Volver</a>";
	       echo "</div>";
	    }
  }
  else if(count($data2)>0){
	  //Fail Password
	  $id_intento=$data2[0]["id_intento"];
	  $bloq=$data2[0]["bloqueado"];
	  $data_intentos=get_data_dict("intentos_usuario",["num_intentos","last_fecha","last_hora"],3,["id_intento"],[$id_intento]);
	  if(count($data_intentos)>0){
		 $fecha_actual=date("d-m-Y H:i:s");
	     $fecha=strval(date("d-m-Y"));
		 $hora=strval(date("H:i:s"));
		 
		 if($bloq=="false"){  
			 
			  if($data_intentos[0]["last_fecha"]!="..."){
			      $t_usr=strtotime($data_intentos[0]["last_fecha"]." ".$data_intentos[0]["last_hora"]."+20 minute");
		          $date_end_intentos=strval(date("d-m-Y H:i:s",$t_usr));
		          $date_end_intentos=explode(" ", $date_end_intentos);
				  if(count($date_end_intentos)==2){
					  $date_intento=$date_end_intentos[0];
					  $time_intento=$date_end_intentos[1];
					   if( $date_intento==$fecha || (comparar($date_intento,$fecha)<0)){
					     
						 if($hora==$time_intento || (calcular_tiempo($time_intento,$hora)<0)){
 					        
							
				            update_data("intentos_usuario",["num_intentos","last_fecha","last_hora"],["0","...","..."],3,["id_intento"],[$id_intento]);
		                    $data_intentos=get_data_dict("intentos_usuario",["num_intentos","last_fecha","last_hora"],3,["id_intento"],[$id_intento]);
         
						 }
				     }
				  }
		       }
			  $num_intentos=intval($data_intentos[0]["num_intentos"]);
			  $bloqueado=false;
		      if($num_intentos+1<3){
				$num_intentos=$num_intentos+1;  
			  }
			  else{
				 $num_intentos=3;  
				 $bloqueado=true;
				 update_data("usuario",["bloqueado"],["true"],1,["nombre_usuario"],[$usuario]);
			  }
			  update_data("intentos_usuario",["num_intentos","last_fecha","last_hora"],[strval($num_intentos),$fecha,$hora],3,["id_intento"],[$id_intento]);
		      if($bloqueado==false){
			      echo "<div id='error_msg'><h2 id='error_text'>Datos Incorrectos</h2></div>";
	              if($num_intentos<2){
			          echo "<div id='error_msg2'><h2 id='error_text'>Tiene ".$num_intentos." Intento fallido de Inicio de Sesion</h2></div>";
			      }
			      else{
				      echo "<div id='error_msg2'><h2 id='error_text'>Tiene ".$num_intentos." Intentos fallidos de Inicio de Sesion, le recomendamos esperar 20 min antes de volver a iniciar Sesion para evitar bloqueo de su cuenta</h2></div>";
			      }
			      echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
                  echo "<a class='boton1' href='loggin.php'>Volver</a>";
	              echo "</div>";
			  }
			  else{
				  echo "<div id='error_msg'><h2 id='error_text'>Usuario Bloqueado</h2></div>";
	             
				  echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
                  echo "<a class='boton1' href='loggin.php'>Volver</a>";
	 
	              echo "</div>";  
			  }
	      }
           else{
			    $t_usr=strtotime($data_intentos[0]["last_fecha"]." ".$data_intentos[0]["last_hora"]."+1 hour");
			    $bloqueo_d=strval(date("d-m-Y H:i:s",$t_usr));
		        $bloqueo_d=explode(" ",$bloqueo_d);
				if(count($bloqueo_d)==2){
				    $date_bloqueo=$bloqueo_d[0];
				    $time_bloqueo=$bloqueo_d[1];
			        if( $date_bloqueo==$fecha || (comparar($date_bloqueo,$fecha)<0)){
						 if($hora==$time_bloqueo || (calcular_tiempo($time_bloqueo,$hora)<0)){
							update_data("intentos_usuario",["num_intentos","last_fecha","last_hora"],["1",$fecha,$hora],3,["id_intento"],[ $id_intento]);
	                        update_data("usuario",["bloqueado"],["false"],1,["nombre_usuario"],[$usuario]);
						    $bloq="false";
							echo "<div id='error_msg'><h2 id='error_text'>Datos Incorrectos</h2></div>";
	                        echo "<div id='error_msg2'><h2 id='error_text'>Tiene 1 Intento fallido de Inicio de Sesion</h2></div>";
			                echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
                            echo "<a class='boton1' href='loggin.php'>Volver</a>";
	                        echo "</div>";
						 }
				   }
				}
				
				if($bloq!="false"){
	       
		           echo "<div id='error_msg'><h2 id='error_text'>El Usuario Esta Bloqueado</h2></div>";
	               echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
                   echo "<a class='boton1' href='loggin.php'>Volver</a>";
	               echo "</div>";
				}
		   }
		  
	  }
	   else{
		   echo "<div id='error_msg'><h2 id='error_text'>Error de Data</h2></div>";
	       echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
           echo "<a class='boton1' href='loggin.php'>Volver</a>";
	       echo "</div>";
	    }
  }
  else {
	  //User Inexistent
	  echo "<div id='error_msg'><h2 id='error_text'>Datos Incorrectos</h2></div>";
	  echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
      echo "<a class='boton1' href='loggin.php'>Volver</a>";
	  echo "</div>";
  }
}
else{
	 //Data Invalid
	 echo "<div id='error_msg'><h2 id='error_text'>Error de Data</h2></div>";
	 echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
     echo "<a class='boton1' href='loggin.php'>Volver</a>";
     echo "</div>";
}
?>
</center>

</body>

</html>