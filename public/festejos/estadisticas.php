<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Gestion de Usuarios</title>
	<link rel="stylesheet" type="text/css" href="style_session.css">
	<script type="text/javascript" src="jquery-3.1.1.min.js"></script>
	<script src="charts/chart.umd.js"></script>
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

//Verify No Exist Ilegal Access from User
require_once __DIR__."/../../private/festejos/jwt.php";
session_start();
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
	$_SESSION['lastPage_user']="estadisticas.php"; 
  	
}
else{
	 header("location: loggin.php");
	 exit();
}

?>
  <h2 id="title1">Estadisticas</h2>
  <form id="formu" method="post">
    <label >Informacion a Consultar</label>
    <select onclick="show_opcionStat()" style="margin-left:5%;" id="accion1" name="acciones">
      <option value="" selected>Elegir</option>
      <option value="producto">Productos</option>
      <option value="trabajador">Trabajadores</option>
      <option value="fiesta">Fiestas</option>
      <option value="usuario">Usuarios</option>
    </select>
    <br><br>
    <label id="label_op2" style="display:none;" >Filtrar Por</label>
    <select style="margin-left:5%; display:none;"id="accion2" name="acciones2">
      <option value="" selected>Elegir</option>
      <option value="opcion1">opcion1</option>
      <option value="opcion2">opcion2</option>
      <option value="opcion3">opcion3</option>
      <option value="opcion4">opcion4</option>
    </select>
    <br>
    <br>
    <input type="button" class="boton_login" value="ver esatidistica" onclick="show_estadisticas()">
  </form>
  <div id="error_msg" style="display:none;">
    <p style="color:white;">Por Favor Elige la Informacion a Consultar</p>
  </div>
  <div id="error_msg2" style="display:none;">
    <p style="color:white;">Por Favor Indique el Filtro Aplicar</p>
  </div>
  <div id="error_msg3" class="error_msg" style="display:none;">
    <p>No se Encontro Ninguna Informacion</p>
  </div>
  <canvas id="stats" width=300 height=300></canvas>
</center>

</body>

</html>

