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

/*Verify No Eixst Illegal Access*/
require_once __DIR__."/../../private/festejos/db_config.php";
require_once __DIR__."/../../private/festejos/jwt.php";
session_start();
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
	if($acceso=="Visitante"){
		header("location:paginaprincipal.php");
	}
	 $last_page=$_SESSION['lastPage_user']; 
	 $_SESSION['lastPage_user']="realizar_registro_productos.php"; 
     if($last_page!="registro_productos.php"){
		  header("location: paginaprincipal.php");
		  exit();
	 } 
}
else{
	 header("location: loggin.php");
	 exit();
}

/*Process the Request to Register The Product*/
date_default_timezone_set('America/Caracas');
if(count($_POST)<=0){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Faltan Datos</h2></div>";
	echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	
    exit;
}
$list_params=array("producto","precio_alquiler","precio","cantidad","accion_form","serial","alquilable");
foreach ($list_params as $param){
	if(!isset($_POST[$param])){
	   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	   echo "<div id='error_msg2'><h2 id='error_text'>Datos Incorrectos</h2></div>";
	   echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	
       exit;
	}
}
$producto = $_POST['producto'];
$porcentaje_alquiler=$_POST['precio_alquiler'];
$precio = $_POST['precio'];
$cantidad = $_POST['cantidad'];
$accion=$_POST['accion_form'];
$serial=$_POST['serial'];
$alquilable=$_POST['alquilable'];
$res_conex=get_conexion();
if($res_conex!="OK"){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_conex}</h2></div>";
	 echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	 
	 exit;
}
$producto=ucwords(strtolower($producto));
$exist_prod=id_exist("producto","nombre_producto",$producto);
if($exist_prod["status"]=="Error"){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error {$exist_prod['message']}</h2></div>";
	 echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	 
	 exit;
}
$exist_prod=$exist_prod["message"];
if(($exist_prod=="False" &&  $accion=="Registro")==false && ($exist_prod=="True" &&  $accion=="Update")==false ){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 if($accion=="Registro"){
		echo "<div id='error_msg2'><h2 id='error_text'>Producto ya Registrado</h2></div>";  
	 }
	 else{
		 echo "<div id='error_msg2'><h2 id='error_text'>Producto Inexistente</h2></div>"; 
	 }
	 echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";
     exit;
}
$disponible="disponible";
if(intval($cantidad)<=0){
	$disponible="Sin Existencias";
}
 if($accion=="Registro"){
		$cant_disp=$cantidad;
		$dat=["nombre_producto"=>$producto,"serial"=>$serial, "precio_alquiler"=>$porcentaje_alquiler ,"precio"=>$precio, "alquilable"=>$alquilable   ,"cantidad"=> $cantidad ,"estatus"=>$disponible, "cantidad_disponible" => $cant_disp];
		add_data("producto",$dat,true);
		$id_val=generate_id("reporte_usuario","id_reporte_usr");
		if($id_val["status"]=="Error"){
			 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	         echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_val['message']}</h2></div>";
	         echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	 
	         exit;
		}
		$id_val=$id_val["message"];
		$data_reporte=["id_reporte_usr"=>$id_val,"nombre_usuario"=>$usuario,"accion"=>"Registrar Producto","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
		add_data("reporte_usuario",$data_reporte,true,true);
}
else{
	$cond_data=array("conditions_Names"=>array("nombre_producto"),"conditions_Values"=>array($producto),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
	$old_prod=get_data("producto",["cantidad","cantidad_disponible"],$cond_data,null,true);
	if($old_prod["status"]=="Error"){
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error {$old_prod['message']}</h2></div>";
	    echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	 
	    exit;	
	}
	$old_prod=$old_prod["message"];
	if(count($old_prod)<=0){
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error No Existen Registros del Producto</h2></div>";
	    echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	 
	    exit;	
	}
	$disp_cant=intval($old_prod[0]["cantidad_disponible"]);
	$old_cant=intval($old_prod[0]["cantidad"]);
	$dif=$old_cant-$disp_cant;
	$max_cant=intval($cantidad);
	if($max_cant-$dif<=0 && $disponible=="disponible"){
		$disponible="agotado";
	}
	$next_disp=strval($max_cant-$dif);
	$res_update=update_data("producto",array("precio_alquiler"=>$porcentaje_alquiler,"cantidad"=>$cantidad,"estatus"=>$disponible,"cantidad_disponible"=>$next_disp,"precio"=>$precio,"alquilable"=>$alquilable),$cond_data,null);
	if($res_update["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
	    echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	 
	    exit;
	}
	$id_report=generate_id("reporte_usuario","id_reporte_usr");
	if($id_report["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report['message']}</h2></div>";
	    echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	 
	    exit;
	}
	$id_report=$id_report["message"];
	$data_reporte=["id_reporte_usr"=>$id_report,"nombre_usuario"=>$usuario,"accion"=>"Actuaizar Producto","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
	add_data("reporte_usuario",$data_reporte,true,true);
   
}
echo "<image src='images/correcto.png' width='120' height='120'/>";
echo "<div id='correcto_msg'><h2 id='correcto_text'>Producto Registrado Satisfactoriamente</h2></div>";
echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";


?>

</div>

</center>

</body>

</html>