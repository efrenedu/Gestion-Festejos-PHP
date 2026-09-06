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
  require "conexion_bd.php";
  session_start();
  $usuario="";
  if(count($_SESSION)>0){
    $usuario = $_SESSION['username'];
    $nivel=$_SESSION['acceso_user'];
    if (!isset($usuario)){
	   header("location: loggin.php"); 
       exit();	   
    }
	else{
		$last_page= $_SESSION['lastPage_user'];
		$_SESSION['lastPage_user']="send_modific_user.php";
        if($last_page!="gestion_usuarios.php"){
		    header("location: paginaprincipal.php");
            exit();			
	   }
	   if($nivel!="administrador"){
	       header("location: paginaprincipal.php"); 
		   exit();
       }
	} 
  }
  else{  
	 header("location: loggin.php");
     exit();	 
  }

  //process the request
  if(count($_POST)>0 ){  
     if(!isset($_POST['accion']) && !isset($_POST["selected_row"])) {
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error de Datos</h2></div>";
		echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
     }
     else{
	    $accion = $_POST['accion'];
	    $user_modif=$_POST['selected_row'];
        if(validar_conexion()){
             if(id_exist("usuario","nombre_usuario", $user_modif)){
					 if($accion=="permiso"){
						 if(!isset($_POST["permiso"])){
							 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		                     echo "<div id='error_msg2'><h2 id='error_text'>Error de Data</h2></div>";
		                     echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
						 }
						 else{
						     update_data("usuario",["permiso"],[$_POST["permiso"]],1,["nombre_usuario"],[$user_modif]);
						 } 
					 }
					 else if($accion=="desbloquear"){
						update_data("usuario",["bloqueado"],["false"],1,["nombre_usuario"],[$user_modif]);
                        $id_intento=get_data_dict("usuario",["id_intento"],1,["nombre_usuario"],[$user_modif])[0]["id_intento"];
						update_data("intentos_usuario",["num_intentos","last_fecha","last_hora"],["0","...","..."],3,["id_intento"],[$id_intento]);
					 }
					 else if($accion=="reset pass"){
						update_data("usuario",["contrasena"],[$default_pass_user],1,["nombre_usuario"],[$user_modif]);
					 }
					 else if($accion=="borrar user"){
						  $dat_usr=get_data_dict("usuario",["id_intento"],1,["nombre_usuario"],[$user_modif]);
						  delete_data("pregunta_secreta",["nombre_usuario"],[$user_modif]);  
						  delete_data("usuario",["nombre_usuario"],[$user_modif]);
					      delete_data("intentos_usuario",["id_intento"],[$dat_usr[0]["id_intento"]]);
					 }
					 add_data("reporte_usuario",[generate_id("reporte_usuario","id_reporte_usr",true),$usuario,"Modificar Usuarios",strval(date("d-m-Y")),strval(date("H:i:s"))],5);
					 echo "<image src='images/correcto.png' width='120' height='120'/>";
		             echo "<div id='correcto_msg'><h2 id='correcto_text'>Modificacion Realizada Satsifactoriamente</h2></div>";
		             echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
			 }
			 else{
				  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		          echo "<div id='error_msg2'><h2 id='error_text'>Error Usuario no Existente</h2></div>";
		          echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";
			 }
	   }
	   else{
		  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		  echo "<div id='error_msg2'><h2 id='error_text'>Fallo al Conectar</h2></div>";
		  echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>"; 
	   }
     }
  }
  else{
	 	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
		echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
  }

?>

</div>

</center>

</body>

</html>