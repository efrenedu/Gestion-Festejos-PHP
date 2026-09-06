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
session_start();
$usuario_actual="";
if(count($_SESSION)>0){
  $usuario = $_SESSION['username'];
  $nivel=$_SESSION['acceso_user'];
  if (!isset($usuario)) {
	 header("location: loggin.php");
	 exit();
  }
  else{
	   $usuario_actual=$usuario;
	   $last_page=$_SESSION['lastPage_user'];
	   $_SESSION['lastPage_user']="send_cambiar_admin.php";
	   if($nivel!="administrador"){
	      header("location: paginaprincipal.php"); 
          exit();		  
       }
	   if($last_page!="asignar_administrador.php"){
		  header("location: paginaprincipal.php"); 
          exit();	 
	   } 
  }
}
else{
	header("location: loggin.php");
	exit();
}

/*process the request*/
require "conexion_bd.php";
date_default_timezone_set('America/Caracas');

if(count($_POST)>0 ){
   if(isset($_POST["trabajador"])){
	   if(validar_conexion()){
		    $id_trabaj=$_POST["trabajador"];
		    update_data("usuario",["CI_Trabaj"],[$id_trabaj],1,["nombre_usuario"],[$usuario_actual]);
            $data_reporte=["id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)),"nombre_usuario"=>$usuario_actual,"accion"=>"Cambiar Administrador","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
			add_data_dict("reporte_usuario",$data_reporte);
		    echo "<image src='correcto.png' width='120' height='120'/>";
		    echo "<div id='correcto_msg'><h2 id='correcto_text'>Modificacion Realizada Satsifactoriamente</h2></div>";
		    echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
	   }
	   else{
		  	 echo "<image src='incorrecto.png' width='120' height='120'/>";
		     echo "<div id='error_msg2'><h2 id='error_text'>Error al Conectar con BD</h2></div>";
		     echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>"; 
	   }
   }
   else{
	  echo "<image src='incorrecto.png' width='120' height='120'/>";
	  echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	  echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";   
   }
	
}
else{
	echo "<image src='incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";  
}
?>

</div>

</center>

</body>

</html>

