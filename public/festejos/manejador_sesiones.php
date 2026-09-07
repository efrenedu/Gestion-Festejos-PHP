
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

function verify_registers(){
	  //Remove old Reports of Users
	  $dat_reports_usr=get_data("reporte_usuario",["id_reporte_usr","fecha"],null,null,true);
	  if($dat_reports_usr["status"]=="Error"){
		 return False; 
	  }
	  $dat_reports_usr=$dat_reports_usr["message"];
	  foreach ($dat_reports_usr as $rep){
		  $id_rep=$rep["id_reporte_usr"];
		  $fech_rep=$rep["fecha"];
		  $dif=comparar(strval(date("d-m-Y")),$fech_rep);
		  if($dif>=60){
			  $cond_data=array("conditions_Names"=>array("id_reporte_usr"),"conditions_Values"=>array($id_rep),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
			  delete_data("reporte_usuario",$cond_data,null,true);    
		  }
	 }
	 $dat_reports=get_data("reporte",["id_reporte","src_reporte","fecha"],null,null,true);
	 if($dat_reports["status"]=="Error"){
		 return False; 
	 }
	 $dat_reports=$dat_reports["message"];
	 foreach ($dat_reports as $rep){
		$id_rep=$rep["id_reporte"];
		$fech_rep=$rep["fecha"];
		$src=$rep["src_reporte"];
		$dif=comparar(strval(date("d-m-Y")),$fech_rep);
		if($dif>=30){
			//remove report of 30 days olds
			unlink($src);
			$cond_data=array("conditions_Names"=>array("id_reporte"),"conditions_Values"=>array($id_rep),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	
			delete_data("reporte",$cond_data,null,true);    
		}
	 }
	 
	 //Remove Pendents Finished Party 
	 $fiestas=get_data("fiesta",["id_fiesta","fecha","hora"],null,null,true);
	 if($fiestas["status"]=="Error"){
        return False;
	 }		
     $fiestas=$fiestas["message"];	 
	 foreach($fiestas as $dat){
		$target=$dat["id_fiesta"];
		$dif=comparar($dat["fecha"],strval(date("d-m-Y")));
		if($dif>=0){
			continue;
		}
		$cond_data=array("conditions_Names"=>array("id_fiesta"),"conditions_Values"=>array($target),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
     
	    $data_reserv=get_data("producto_reservado",["nombre_producto","formato","cantidad_reservada"],$cond_data,null,true);
        if($data_reserv["status"]=="Error"){
			continue;
		}
		$data_reserv=$data_reserv["message"];
		foreach($data_reserv as $dat){
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
			$cond_data=array("conditions_Names"=>array("nombre_producto"),"conditions_Values"=>array($id_prod),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
     
			$prod_dat=get_data("producto",["cantidad_disponible"],$cond_data,null,true);
			if($prod_dat["status"]=="Error"){
				continue;
			}
			$prod_dat=$prod_dat["message"];
			if(count($prod_dat)>0){

				$old_cant=intval($prod_dat[0]["cantidad_disponible"]);
				$old_cant=$old_cant+$cant;
				update_data("producto",array("cantidad_disponible"=>strval($old_cant),"estatus"=>"disponible"),$cond_data,null,true);
			}
									
		 }
		 $cond_data=array("conditions_Names"=>array("id_fiesta"),"conditions_Values"=>array($target),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
     
		 delete_data("personal_fiesta",$cond_data,null,true);
		 delete_data("producto_reservado",$cond_data,null,true);
		 delete_data("fiesta",$cond_data,null,true);    
		
	}
			
}
require_once __DIR__."/../../private/festejos/db_config.php";
require_once __DIR__."/../../private/festejos/jwt.php";

require "fechas.php";

if(count($_POST)<2){
	 echo "<div id='error_msg'><h2 id='error_text'>Faltan Datos</h2></div>";
	 echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
     echo "<a class='boton1' href='loggin.php'>Volver</a>";
     echo "</div>";
	 exit;
}
if(!isset($_POST["nombre"]) || !isset($_POST["contrasena"])){
	 echo "<div id='error_msg'><h2 id='error_text'>Faltan Datos</h2></div>";
	 echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
     echo "<a class='boton1' href='loggin.php'>Volver</a>";
     echo "</div>";
	 exit;
}
$status_conex=get_conexion();
if($status_conex!="OK"){
	 echo "<div id='error_msg'><h2 id='error_text'>{$status_conex}</h2></div>";
	 echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
     echo "<a class='boton1' href='loggin.php'>Volver</a>";
     echo "</div>";
	 exit;
}
verify_db();
session_start();
$usuario = $_POST['nombre'];
$clave = $_POST['contrasena'];
$join_data=array();
$join_data["intentos_usuario"]=array("query_field"=>array("num_intentos","last_fecha","last_hora"),"share_fields"=>array("field"=>"id_intento","table_reference"=>"usuario"),"Conditions_join"=>null);				   
$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($usuario),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
$data_res =get_data("usuario",array("nombre_usuario","permiso","CI_trabaj","bloqueado","contrasena"),$cond_data,$join_data,true);
if($data_res["status"]=="Error"){
	  echo "<div id='error_msg'><h2 id='error_text'>Error:{$data_res['message']}</h2></div>";
	  echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
      echo "<a class='boton1' href='loggin.php'>Volver</a>";
	  echo "</div>";
	  exit;
}
$data=$data_res["message"];
date_default_timezone_set('America/Caracas');
if(count($data)<=0){
	  echo "<div id='error_msg'><h2 id='error_text'>Usuario Inexistente</h2></div>";
	  echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
      echo "<a class='boton1' href='loggin.php'>Volver</a>";
	  echo "</div>";
	  exit;
}	
$pass_bd=$data[0]["contrasena"];
$bloq=$data[0]["bloqueado"];
$minutes_dif=0;
$last_loggin="";
$last_fecha=$data[0]["last_fecha"];
$temp_fecha=explode("/",$last_fecha);
$last_hora=$data[0]["last_hora"];
$num_intentos=$data[0]["num_intentos"];
if(count($temp_fecha)>=3){
	$formatted_date=$temp_fecha[0]."-".$temp_fecha[1]."-".$temp_fecha[2];
    $last_loggin=$formatted_date." ".$last_hora;
}
if($last_loggin!=""){
	$date_intento=new DateTime($last_loggin);
	$actual_date=new DateTime();
	$dif_time=$date_intento->diff($actual_date);
	$minutes_dif=($dif_time->days*24*60)+($dif_time->h*60)+$dif_time->i;
}
if($bloq=="true"){
	if($minutes_dif>60){
		$bloq="false";
		$num_intentos=0;
	}
}
if(password_verify($clave,$pass_bd) && $bloq=="false"){
	$path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
    if(!file_exists($path_keySecret)){
	    echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos para Generar Token del Usuario</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
	    echo "</div>";
        exit;
    }
	$fecha_str=strval(date("d/m/Y"));
    $hora_str=strval(date("H:i:s"));
	$data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	$datos_session=["CI_trabaj"=>$data[0]["CI_trabaj"],"Nivel_Acceso"=>$data[0]["permiso"],"Id_User"=>$usuario];
	$dur_token=3600;
	$token_client=generate_tokenLogin($datos_session,$data_secretKey["Token"],$dur_token);
    $join_data=array();
    $join_data["intentos_usuario"]=array("query_field"=>array("num_intentos"=>"0","last_fecha"=>"...","last_hora"=>"..."),"share_fields"=>array("field"=>"id_intento","table_reference"=>"usuario"),"Conditions_join"=>null);
	$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($usuario),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
	$res_update=update_data("usuario",array("bloqueado"=>"false"),$cond_data,$join_data);
	if($res_update["status"]=="Error"){
		echo "<div id='error_msg'><h2 id='error_text'> Error: {$res_update['message']}</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
	    echo "</div>";
        exit;
	}
	$fecha_str=strval(date("d-m-Y"));;
	$id_reporte_usr=generate_id("reporte_usuario","id_reporte_usr");
	if($id_reporte_usr["status"]=="Error"){
		echo "<div id='error_msg'><h2 id='error_text'>Error: {$id_reporte_usr['message']}</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
	    echo "</div>";
        exit;
	}
	$dat_report=array("id_reporte_usr"=>$id_reporte_usr["message"],"nombre_usuario"=>$usuario,"accion"=>"Iniciar Sesion","fecha"=>$fecha_str,"hora"=>$hora_str);
	$res_add=add_data("reporte_usuario",$dat_report,true,true);
	if($res_add["status"]=="Error"){
		echo "<div id='error_msg'><h2 id='error_text'>Error: {$res_add['message']}</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
	    echo "</div>";
        exit;
	}
    $_SESSION['lastPage_user']="loggin.php";
	$_SESSION["Token_User"]=$token_client;	
	verify_registers();
	header ("location: paginaprincipal.php");	
    exit;	
}
else{
	//Loggin Fail
	$fecha_str=strval(date("d/m/Y"));
    $hora_str=strval(date("H:i:s"));
	if($bloq=="true"){
		 echo "<div id='error_msg'><h2 id='error_text'>Usuario Bloqueado</h2></div>";
	     echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
         echo "<a class='boton1' href='loggin.php'>Volver</a>";
	     echo "</div>";
	     exit;
	}
	else{
		if($num_intentos>=0){
			if($minutes_dif>20){
				$num_intentos=0;
			}
			if($num_intentos<3){
		        $num_intentos+=1;
	        }
		}
		if($num_intentos>=3){
			$bloq="true";
		}
	}
	$join_data=array();
    $join_data["intentos_usuario"]=array("query_field"=>array("num_intentos"=>strval($num_intentos),"last_fecha"=>$fecha_str,"last_hora"=>$hora_str),"share_fields"=>array("field"=>"id_intento","table_reference"=>"usuario"),"Conditions_join"=>null);
	$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($usuario),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
	$res_update=update_data("usuario",array("bloqueado"=>$bloq),$cond_data,$join_data,true);
	if($res_update["status"]=="Error"){
		echo "<div id='error_msg'><h2 id='error_text'>Datos Incorrectos, Error: {$res_update['message']}</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
	    echo "</div>";
        exit;
	}
	$fecha_str=strval(date("d-m-Y"));;
	$id_reporte_usr=generate_id("reporte_usuario","id_reporte_usr");
	if($id_reporte_usr["status"]=="Error"){
		echo "<div id='error_msg'><h2 id='error_text'>Datos Incorrectos, Error: {$id_reporte_usr['message']}</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
	    echo "</div>";
        exit;
	}
	$dat_report=array("id_reporte_usr"=>$id_reporte_usr["message"],"nombre_usuario"=>$usuario,"accion"=>"Inicio de Sesion Fallido","fecha"=>$fecha_str,"hora"=>$hora_str);
	$res_add=add_data("reporte_usuario",$dat_report,true,true);
	if($res_add["status"]=="Error"){
		echo "<div id='error_msg'><h2 id='error_text'>Datos Incorrectos, Error: {$res_add['message']}</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
	    echo "</div>";
        exit;
	}
	echo "<div id='error_msg'><h2 id='error_text'>Datos Incorrectos</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
	echo "</div>";
	exit;
}


?>
</center>

</body>

</html>