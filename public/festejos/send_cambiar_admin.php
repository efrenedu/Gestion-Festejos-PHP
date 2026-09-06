<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 	
	<title>Cambiar Administrador</title>
	<link rel="stylesheet" type="text/css" href="style_session.css">
	<script type="text/javascript" src="jquery-3.1.1.min.js"></script>
	<script type="text/javascript" src="script_festejos.js"></script>
</head>
<body onload="iniciar_menu()">
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
require_once __DIR__."/../../private/festejos/jwt.php";
require_once __DIR__."/../../private/festejos/db_config.php";
 
session_start();
$usuario_actual="";
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
	     echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos del Token del Servidor</h2></div>";
	     echo "<image id='error_img' src='images/incorrecto.png' width='150' height='150'/><br><br>";
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
	$dat=$res["Message"];
	$permiso=$dat["Acceso"];
	$usuario_actual=$res["Message"]["Id_usr"];
	if($permiso!="administrador"){
		header("location: paginaprincipal.php");
        exit();
	}
	$last_page=$_SESSION['lastPage_user'];
	$_SESSION['lastPage_user']="send_cambiar_admin.php"; 
	if($last_page!="asignar_administrador.php"){
		  header("location: paginaprincipal.php"); 
          exit();	 
	}
  	
}
else{
	 header("location: loggin.php");
	 exit();
}
date_default_timezone_set('America/Caracas');
if(count($_POST)<=0 ){
	  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	  echo "<div id='error_msg2'><h2 id='error_text'>Faltan Datos</h2></div>";
	  echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";   
      exit;
}
if(!isset($_POST["trabajador"])){
	  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	  echo "<div id='error_msg2'><h2 id='error_text'> Datos Invalidos</h2></div>";
	  echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";   
      exit;
}
$res_conex=get_conexion();
if($res_conex!="OK"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_conex} </h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";  
    exit;
}

$id_trabaj=$_POST["trabajador"];
$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($usuario_actual),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 		  
$res_update=update_data("usuario",array("CI_trabaj"=>$id_trabaj),$cond_data);
if($res_update["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']} </h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";  
    exit;
	
}
$id_report=generate_id("reporte_usuario","id_reporte_usr");
if($id_report["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report['message']} </h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";  
    exit;
	
}

$data_reporte=["id_reporte_usr"=>$id_report["message"],"nombre_usuario"=>$usuario_actual,"accion"=>"Cambiar Administrador","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
add_data("reporte_usuario",$data_reporte,true,true);
echo "<image src='images/correcto.png' width='120' height='120'/>";
echo "<div id='correcto_msg'><h2 id='correcto_text'>Modificacion Realizada Satsifactoriamente</h2></div>";
echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
	   
   
   
	
	


?>

</div>

</center>

</body>

</html>

