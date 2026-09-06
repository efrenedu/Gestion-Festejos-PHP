<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 

	<title>bienvenido</title>
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

/*verify no exist invalid access
  if valid set data of user
*/

require_once __DIR__."/../../private/festejos/db_config.php";
require_once __DIR__."/../../private/festejos/jwt.php";

session_start();


if(count($_SESSION)>0){
	 if(get_conexion()!="OK"){
		 echo "<div id='error_msg'><h2 id='error_text'>Error Conectando con el Servidor</h2></div>";
	     echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
         echo "<a class='boton1' href='loggin.php'>Volver</a>";
	     echo "</div>";
	     exit;
	 }
	 $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
	 if(!file_exists($path_keySecret)){
	     echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos del Token del Servidor</h2></div>";
	     echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
         echo "<a class='boton1' href='loggin.php'>Volver</a>";
	     echo "</div>";
	     exit;
     }
	 $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="False"){
		header("location: loggin.php");
		exit;
	 }
	 $dat_user=$res["Message"];
	 $user=$dat_user["Id_usr"];
	 $permiso_user=$dat_user["Acceso"];
     $worker=$dat_user["CI_trabaj"];
     $_SESSION['lastPage_user']="paginaprincipal.php";
	 $cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($user),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
     $dat_usr_bd=get_data("usuario",["foto"],$cond_data,null,true);
     if($dat_usr_bd["status"]=="Error"){
		 echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos del Usuario</h2></div>";
	     echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
         echo "<a class='boton1' href='loggin.php'>Volver</a>";
	     echo "</div>";
	     exit;
	 }
	 $dat_usr_bd=$dat_usr_bd["message"];
	 if(count($dat_usr_bd)>0){
	    $foto=$dat_usr_bd[0]["foto"];
		echo "<h1 id='msg_welcome'>Bienvenido</h1>";
	
     }
     if($foto!="" && $foto!="..."){
	    echo "<div class='box_image'><image id='welcome_img' src='images/".$foto."' width='150' height='150'/></div>";
     }
     else{
       echo "<div class='box_image'><image id='welcome_img' src='images/user_login.jpg' width='150' height='150'/></div>";
     }
     echo "<br><p class='info_user'>Usuario:".$user."</p>";
     echo "<br>";
     echo "<p class='info_user'>Tipo de Usuario:".$permiso_user."</p>";
     echo "</div>";
   
}
else{
	header("location: loggin.php");
}

?>




</center>

</body>

</html>

