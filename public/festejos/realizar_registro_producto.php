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
require "conexion_bd.php";
session_start();

if(count($_SESSION)>0){
   $usuario = $_SESSION['username'];
   $nivel=$_SESSION['acceso_user'];  
   if (!isset($usuario)) {
	  header("location: loggin.php");
	  exit();
   }
   else{
	 if($nivel=="Visitante"){
		 header("location: paginaprincipal.php");
		 exit();
	 }
	 $last_page=$_SESSION['lastPage_user']; 
	 $_SESSION['lastPage_user']="realizar_registro_productos.php"; 
     if($last_page!="registro_productos.php"){
		  header("location: paginaprincipal.php");
		  exit();
	 }
   }
}
else{
	header("location: loggin.php");
	exit();
}

/*Process the Request to Register The Product*/
date_default_timezone_set('America/Caracas');
if(count($_POST)>0){
  $producto = $_POST['producto'];
  $porcentaje_alquiler=$_POST['precio_alquiler'];
  $precio = $_POST['precio'];
  $cantidad = $_POST['cantidad'];
  $accion=$_POST['accion_form'];
  $serial=$_POST['serial'];
  $alquilable=$_POST['alquilable'];
  if((isset($producto) && isset($precio) && isset($cantidad) && isset($serial) && isset( $accion) && isset( $porcentaje_alquiler) && isset( $alquilable))==false){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>error datos incorrectos</h2></div>";
	echo "<a class='boton2' href='registro_productos.php' >aceptar</a>";	
  }
  else{
    if(validar_conexion()){
		$producto=ucwords(strtolower($producto));
		if((id_exist("producto","nombre_producto",$producto)==False &&  $accion=="Registro")==true || (id_exist("producto","nombre_producto",$producto)==true &&  $accion=="Update")==true ){
		     $disponible="disponible";
			 if(intval($cantidad)<=0){
				 $disponible="Sin Existencias";
			 }
			 if($accion=="Registro"){
				 $cant_disp=$cantidad;
				 $dat=["nombre_producto"=>$producto,"serial"=>$serial, "precio_alquiler"=>$porcentaje_alquiler ,"precio"=>$precio, "alquilable"=>$alquilable   ,"cantidad"=> $cantidad ,"estatus"=>$disponible, "cantidad_disponible" => $cant_disp];
				 add_data_dict("producto",$dat);
				 $data_reporte=["id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)),"nombre_usuario"=>$usuario,"accion"=>"Registrar Producto","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
			     add_data_dict("reporte_usuario",$data_reporte);
			 }
			 else{
				$old_prod=get_data_dict("producto",["cantidad","cantidad_disponible"],2,["nombre_producto"],[$producto]);
				if(count($old_prod)>0){
				   $disp_cant=intval($old_prod[0]["cantidad_disponible"]);
				   $old_cant=intval($old_prod[0]["cantidad"]);
				   $dif=$old_cant-$disp_cant;
				   $max_cant=intval($cantidad);
				   if($max_cant-$dif<=0 && $disponible=="disponible"){
					   $disponible="agotado";
				   }
				   $next_disp=strval($max_cant-$dif);
				   update_data("producto",["precio_alquiler","cantidad","estatus","cantidad_disponible","precio","alquilable"],[$porcentaje_alquiler,$cantidad,$disponible, $next_disp,$precio,$alquilable],6,["nombre_producto"],[$producto]);
				   $data_reporte=["id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)),"nombre_usuario"=>$usuario,"accion"=>"Actuaizar Producto","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
			       add_data_dict("reporte_usuario",$data_reporte);
				}
			 }
              echo "<image src='images/correcto.png' width='120' height='120'/>";
		      echo "<div id='correcto_msg'><h2 id='correcto_text'>Producto Registrado Satisfactoriamente</h2></div>";
		      echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
        }
		else{
			 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	         echo "<div id='error_msg2'><h2 id='error_text'>Producto ya Registrado</h2></div>";
	         echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";
		}
	  }
	  else{
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error de Conexion</h2></div>";
	    echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";	 
	  }
    }
  }
else{
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	echo "<a class='boton2' href='registro_productos.php' >Aceptar</a>";		  
}

?>

</div>

</center>

</body>

</html>