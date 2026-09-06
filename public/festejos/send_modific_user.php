<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Datos Enviados </title>
	<link rel="stylesheet" type="text/css" href="style_session.css">
	<script type="text/javascript" src="jquery-3.1.1.min.js"></script>
	<script type="text/javascript" src="script_festejos.js"></script>
</head>
<body>
<center>
<br>
<div id="titleBox">
<image id="logo" src="images/globos.png" width="100" height="100"/>
<h1 id="title">Logistica & Festejos Premier</h1>
</div>
<nav id="menu" >
  <ul> 
	 <li><a href="#">Usuarios</a>
	   <ul>
          <li> <a  href="paginaprincipal.php">Inicio</a></li>
          <li><a href="modificar_usuario.php">Modificar Usuario</a></li>
          <li><a class="only_admin" href="asignar_administrador.php">Cambiar Administrador</a></li>
          <li><a href="salir.php">Cerrar Session</a></li>
      </ul>
   </li>
 </ul> 
 <ul>
    <li ><a href="#">Registros</a>
	    <ul>
	       <li> <a id="Menu_Registro_Trabajador" class="only_admin" href="registrar_trabajador.php">Registrar Trabajador</a></li>
           <li> <a id="Menu_registro_producto" class="register_Menu" href="registro_productos.php">Registrar producto</a></li> 
	    </ul>
	</li>
  </ul>
  <ul>
     <li><a href="#">Procesos</a>
	    <ul>
           <li><a id="Menu_alquilar" class="process_Menu" href="alquilar_productos.php">Alquilar producto</a></li>
           <li><a id="Menu_devolver" class="process_Menu" href="devolver_productos.php">Devolver producto</a></li>
           <li><a id="Menu_organizar_fiesta" class="process_Menu" href="organizar_fiestas.php">Organizar fiesta</a></li>
           <li><a id="Menu_cancelar_fiesta" class="process_Menu" href="cancelar_fiesta.php">Cancelar Fiesta</a></li>
	    </ul>
	 </li>
  </ul>
  <ul>
     <li><a href="#">Servicios</a>
	   <ul>
         <li> <a class="only_admin" href="gestion_usuarios.php">Gestionar Usuarios</a></li>
         <li> <a class="only_admin" href="respaldar_bd.php">Respaldar BD</a></li>
         <li> <a href="estadisticas.php">Estadisticas</a></li>
         <li> <a class="only_admin" href="auditoria.php">Auditoria</a></li>
	   </ul>
     </li>
  </ul>
  <ul>
     <li><a href="#">Consultas</a>
	   <ul >
         <li > <a href="consultar_fiestas.php">Fiestas Organizadas</a></li>
         <li > <a href="consultar_alquileres.php">Bienes Alquilados</a></li>
       </ul>
	 </li>
  </ul>
</nav>
<div  id="content2">
<?php  

/*Verify No Exist Illegal Access*/

function generate_pass($num_characters=8){
	
	$new_pass="";
	$specials="!@#$%&*";
	$numbers="23456789";
	$characters="abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ";
	$max_len=strlen($characters)-1;
	for($i=0;$i<$num_characters-2;$i++){
		$new_pass.=$characters[random_int(0,$max_len)];
	}
	$new_pass.=$numbers[random_int(0,strlen($numbers)-1)];
	$new_pass.=$specials[random_int(0,strlen($specials)-1)];
	
	return $new_pass;
}

session_start();
require_once __DIR__."/../../private/festejos/db_config.php";  
require_once __DIR__."/../../private/festejos/jwt.php";
$usuario="";
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
	     echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos del Token del Servidor</h2></div>";
	     echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
         echo "<a class='boton1' href='salir.php'>Volver</a>";
	     echo "</div>";
	     exit;
     }
     $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="False"){
		header("location: salir.php");
		exit;
    }
	$acceso=$res["Message"]["Acceso"];
	$usuario=$res["Message"]["Id_usr"];
	if($acceso!="administrador"){
	     header("location: paginaprincipal.php");
         exit();		 
    }
	$last_page= $_SESSION['lastPage_user'];
	$_SESSION['lastPage_user']="send_modific_user.php";
    if($last_page!="gestion_usuarios.php"){
		header("location: paginaprincipal.php");
        exit();			
	}
  
}
else{
	 header("location: loggin.php");
	 exit();
}
  
//process the request
if(count($_POST)<=0){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	echo "<a class='boton2' href='gestion_usuarios.php' >Aceptar</a>";	  
    exit;
}
if(!isset($_POST['accion']) || !isset($_POST["selected_row"])) {
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error de Datos</h2></div>";
	echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
    exit;
}
$accion = $_POST['accion'];
$user_modif=$_POST['selected_row'];
$res_conex=get_conexion();
date_default_timezone_set('America/Caracas');

if($res_conex!="OK"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Fallo al Conectar</h2></div>";
	echo "<a class='boton2' href='gestion_usuarios.php' >Aceptar</a>"; 
	exit; 
}

$exist_user=id_exist("usuario","nombre_usuario", $user_modif);
if($exist_user["status"]=="Error"){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error: {$exist_user['message']}</h2></div>";
	 echo "<a class='boton2' href='gestion_usuarios.php' >Aceptar</a>";
	 exit;	
}

if($exist_user["message"]!="True"){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Usuario Inexistente </h2></div>";
	 echo "<a class='boton2' href='gestion_usuarios.php' >Aceptar</a>";
	 exit;	
}

$extra_msg="";
if($accion=="permiso"){
	if(!isset($_POST["permiso"])){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Datos Invalidos</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
	$next_permiso=$_POST["permiso"];
	if($next_permiso=="administrador"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>No se Puede Cambiar el Permiso a Usuario Administrador</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
	$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($user_modif),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
	$res_update=update_data("usuario",array("permiso"=>$next_permiso),$cond_data,null);
	if($res_update["status"]=="Error"){
        echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_update['message']}</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}		
}
else if($accion=="desbloquear"){
	$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($user_modif),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
	$join_data=array();
	$join_data["intentos_usuario"]=array("query_field"=>array("num_intentos"=>"0","last_fecha"=>"...","last_hora"=>"..."),"share_fields"=>array("field"=>"id_intento","table_reference"=>"usuario"),"Conditions_join"=>null);				   
	$res_update=update_data("usuario",array("bloqueado"=>"false"),$cond_data,$join_data);
    if($res_update["status"]=="Error"){
        echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_update['message']}</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
}
else if($accion=="reset pass"){
	$next_pass=generate_pass(12);
	$pass_hash=password_hash($next_pass,PASSWORD_BCRYPT);
	$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($user_modif),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
	$res_update=update_data("usuario",array("contrasena"=>$pass_hash),$cond_data);
    if($res_update["status"]=="Error"){
        echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_update['message']}</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
	$extra_msg= "<p>Password Restablecido a {$next_pass} , Por Avisele lo Antes Posible al Usuario para que lo Modifique</p>";
	
}
else if($accion=="borrar user"){
	$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($user_modif),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
	$dat_usr=get_data("usuario",["id_intento"],$cond_data,null,true);
	if($dat_usr["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$dat_usr['message']}</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
	$dat_usr=$dat_usr["message"];
	$res_del=delete_data("reporte_usuario",$cond_data);
	if($res_del["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_del['message']}</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
	$res_del=delete_data("pregunta_secreta",$cond_data);
	if($res_del["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_del['message']}</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
	$res_del=delete_data("usuario",$cond_data);
	if($res_del["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_del['message']}</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
	$cond_data=array("conditions_Names"=>array("id_intento"),"conditions_Values"=>array($dat_usr[0]["id_intento"]),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
	$res_del=delete_data("intentos_usuario",$cond_data);
	if($res_del["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_del['message']}</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	    exit;
	}
}
$id_report=generate_id("reporte_usuario","id_reporte_usr");
if($id_report["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error: {$id_report['message']}</h2></div>";
	echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	exit;
}
$id_report=$id_report["message"];
$dat_report=array("id_reporte_usr"=>$id_report,"nombre_usuario"=>$usuario,"accion"=>"Modificar Usuarios","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
$res_add=add_data("reporte_usuario",$dat_report,true,true);
if($res_add["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_add['message']}</h2></div>";
	echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	exit;
}

echo "<image src='images/correcto.png' width='120' height='120'/>";
if($extra_msg!=""){
	echo $extra_msg;
}
echo "<div id='correcto_msg'><h2 id='correcto_text'>Modificacion Realizada Satsifactoriamente</h2></div>";
echo "<a id='boton_acceptar' class='boton2' href='gestion_usuarios.php' >Aceptar</a>";
			
	  
     



?>

</div>

</center>

</body>

</html>