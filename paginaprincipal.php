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

require "conexion_bd.php";
session_start();

$usuario = $_SESSION['username'];
$nivel=$_SESSION['acceso_user'];
$foto="...";

if(count($_SESSION)>0){
	if (!isset($usuario)) {
	   header("location: loggin.php");
   }
   else{
     $_SESSION['lastPage_user']="paginaprincipal.php";
     $found=false;
	 if(validar_conexion()){
		$data_user=get_data_dict("usuario",["CI_trabaj",],1,["nombre_usuario"],[$usuario]);
		if(count($data_user)>0){
			$id_trabaj=$data_user[0]["CI_trabaj"];
			$data_trabaj=get_data_dict("trabajador",["id_nombre"],1,["CI_trabaj"],[$id_trabaj]);
			if(count($data_trabaj)>0 && $id_trabaj!="000000"){
			   $id_name=$data_trabaj[0]["id_nombre"];
			   $data_name=get_data_dict("nombre",["nombre","segundo_nombre","apellido","segundo_apellido"],4,["id_nombre"],[ $id_name]);
			   if(count($data_name)>0){
				   $found=true;
				   $name=$data_name[0]["nombre"];
				   if($data_name[0]["segundo_nombre"]!=""){
					   $name=$name." ".$data_name[0]["segundo_nombre"];
				   }
				   $name=$name." ".$data_name[0]["apellido"];
				   if($data_name[0]["segundo_apellido"]!=""){
					   $name=$name." ".$data_name[0]["segundo_apellido"];
				   }
	               echo "<h1 id='msg_welcome'>Bienvenido</h1><h2>".$name."</h2>";
			   }
			}
		}
	 }
	 if($found==false){
         echo "<h1 id='msg_welcome'>Bienvenido </h1>";
	 }
     $dat_foto=get_data("usuario",["foto"],1,["nombre_usuario"],[$usuario]);
     if(count($dat_foto)>0){
	    $foto=$dat_foto[0][0];
     }
     if($foto!="" && $foto!="..."){
	    echo "<div class='box_image'><image id='welcome_img' src='".$foto."' width='150' height='150'/></div>";
     }
     else{
       echo "<div class='box_image'><image id='welcome_img' src='images/user_login.jpg' width='150' height='150'/></div>";
     }
     echo "<br><p class='info_user'>Usuario:".$usuario."</p>";
     echo "<br>";
     echo "<p class='info_user'>Tipo de Usuario:".$nivel."</p>";
     echo "</div>";

  }
}
else{
	header("location: loggin.php");
}

?>




</center>

</body>

</html>

