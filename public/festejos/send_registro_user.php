<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Registro de Usuario </title>
	<link rel="stylesheet" type="text/css" href="style_session.css">
	<script type="text/javascript" src="jquery-3.1.1.min.js"></script>
	<script type="text/javascript" src="script_festejos.js"></script>
</head>
<body>
<center>
<br>
<div id="titleBoc">
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
  $usuario_actual="";
  if(count($_SESSION)>0){
    $usuario = $_SESSION['username'];
	$usuario_actual=$usuario;
    $nivel=$_SESSION['acceso_user'];
    if (!isset($usuario)){
	   header("location: loggin.php"); 
       exit();	   
    }
	else{
		$last_page=$_SESSION['lastPage_user'];
        $_SESSION['lastPage_user']="send_registro_user.php";
		if($last_page!="registro_usuarios.php"){
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
  /*Process the request to register the user*/
  if(count($_POST)>0){
    if( !isset($_POST['user_name']) || !isset($_POST['pass1']) || !isset($_POST['permiso']) || !isset($_POST["trabajador"])) {
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>error datos invalidos</h2></div>";
		echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
   }
   else{
	   date_default_timezone_set('America/Caracas');
	   $usuario=strtolower($_POST['user_name']);
       $pass=$_POST['pass1'];
	   $permiso=$_POST['permiso'];
	   $trabajador=$_POST['trabajador'];
	   if(isset($_POST["p1"]) && isset($_POST["p2"]) && isset($_POST["p3"]) && isset($_POST["p4"]) && isset($_POST["p5"]) && isset($_POST["p6"])){
         if(isset($_POST["r1"]) &&	isset($_POST["r2"])	&& isset($_POST["r3"]) && isset($_POST["r4"]) && isset($_POST["r5"]) && isset($_POST["r6"])){
           if(validar_conexion()){
		       if(id_exist("usuario","nombre_usuario",$usuario)==false){
		           $preguntas=array($_POST["p1"],$_POST["p2"],$_POST["p3"],$_POST["p4"],$_POST["p5"],$_POST["p6"]);
			       $respuestas=array($_POST["r1"],$_POST["r2"],$_POST["r3"],$_POST["r4"],$_POST["r5"],$_POST["r6"]);
				   if(isset($_POST["p7"])){
					   if($_POST["p7"]!="" && $_POST["r7"]!=""){
					     $preguntas[]=$_POST["p7"];
					     $respuestas[]=$_POST["r7"];
					   }
				   }
				   if(isset($_POST["p8"])){
					   if($_POST["p8"]!="" && $_POST["r8"]!=""){
					     $preguntas[]=$_POST["p8"];
					     $respuestas[]=$_POST["r8"];
					   }
				   }
				   $dat_intentos=array("id_intento"=>generate_id("intentos_usuario","id_intento",false),"num_intentos"=>"0","last_fecha"=>"...","last_hora"=>"...");
	               add_data_dict("intentos_usuario",$dat_intentos);
		           $pass=encript($pass);
		           $dat=array("nombre_usuario"=>$usuario ,"contrasena"=>$pass ,"permiso"=>$permiso ,"bloqueado"=>"false","foto"=>"..." , "id_intento"=>$dat_intentos["id_intento"],"CI_trabaj"=>$trabajador);
				   add_data_dict("usuario",$dat);
		           for($i=0;$i<count($preguntas);$i++){
					   $d_preg=array("id_pregunta"=>generate_id("pregunta_secreta","id_pregunta",true) ,"nombre_usuario"=>$dat["nombre_usuario"] , "pregunta"=>$preguntas[$i] ,"respuesta"=>strtolower($respuestas[$i]),"numero"=>strval($i+1));
				       add_data_dict("pregunta_secreta",$d_preg);
				   }
				   $dat_reporte=array("id_reporte_usr"=>generate_id("reporte_usuario","id_reporte_usr",true) , "nombre_usuario"=>$usuario_actual , "accion"=>"Registrar Usuario" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
				   add_data_dict("reporte_usuario",$dat_reporte);
		           echo "<image src='images/correcto.png' width='120' height='120'/>";
		           echo "<div id='correcto_msg'><h2 id='correcto_text'>Usuario Registrado Satisfactoriamente</h2></div>";
		           echo "<a id='boton_acceptar' class='boton2' href='gestion_usuarios.php' >Aceptar</a>";
			   }
			   else{
			        echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		            echo "<div id='error_msg2'><h2 id='error_text'>Nombre de Usuario ya Existente</h2></div>";
		            echo "<a class='boton2' href='registro_usuarios.php' >Aceptar</a>";
		        }
		   }
		   else{
				echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		        echo "<div id='error_msg2'><h2 id='error_text'>Fallo al Conectar</h2></div>";
		        echo "<a class='boton2' href='registro_usuarios.php' >Aceptar</a>";
		   }
		 }
		 else{
		     echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		     echo "<div id='error_msg2'><h2 id='error_text'>Error de Data</h2></div>";
		     echo "<a class='boton2' href='registro_usuarios.php' >Aceptar</a>";
		 }
	   }
	   else{
		  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		  echo "<div id='error_msg2'><h2 id='error_text'>Error de Data</h2></div>";
		  echo "<a class='boton2' href='registro_usuarios.php' >Aceptar</a>"; 
	   }
   }
  }
  else{
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	 echo "<a class='boton2' href='registro_usuarios.php' >Aceptar</a>";	  
  }

?>

</div>

</center>

</body>

</html>