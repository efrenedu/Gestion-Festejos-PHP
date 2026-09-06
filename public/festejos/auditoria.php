<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Auditoria</title>
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
	$dat=$res["Message"];
	$permiso=$dat["Acceso"];
	if($permiso!="administrador"){
		header("location: paginaprincipal.php");
        exit();
	}
	$_SESSION['lastPage_user']="auditoria.php"; 
  	
}
else{
	 header("location: loggin.php");
	 exit();
}
?>
  <h2 id="title1">Auditoria</h2>
  <form method="post">
    <label for="accion" >Filtrar Por:</label>
    <select onchange="modificar_filtro()" id="acc" name="accion">
      <option value="" selected>Elegir</option>
      <option value="usuario">Usuario</option>
      <option value="accion"> Accion</option>
      <option value="fecha">Rango de Fechas</option>
    </select>
    <br><br>
   <div id="filtro2" style="display:none;">
     <label for="filtro_date" style="margin-left:2%";> fecha de inicio</label>
     <input type="date" id="filtro_date" name="filtro_date" placeholder="escriba un filtro">
     <label for="filtro_date" style="margin-left:2%";> fecha Fin</label>
     <input type="date" id="filtro_date2" name="filtro_date2" placeholder="escriba un filtro">
   </div>
   <div id="filtro3" style="display:none;">
     <label for="filtro_acc" style="margin-left:2%";> valor del filtro</label>
     <select id="filtro_acc" name="filtro_acc" >
        <option value="" selected>Elegir</option>
        <option value="Iniciar Sesion">Iniciar Sesion</option>
        <option value="Modificar Perfil de Usuario">Modificar Perfil de Usuario</option>
        <option value="Recuperar Contrasena">Recuperar Contrasena</option>
        <option value="Registrar Usuario">Registrar Usuario</option>
        <option value="Cambiar Administrador">Cambiar Administrador</option>
 
	 </select>
   </div>
<?php  
   
   /*write nodes html of  table and filters*/
   
   require_once __DIR__."/../../private/festejos/db_config.php";
   echo "<div id='filtro1' style='display:none;' >
   <label for='filtro_name' style='margin-left:2%';> valor del filtro</label>
   <select id='filtro_name' name='filtro_name'>
     <option value='' selected>Elegir</option>
   ";
   $conectado=get_conexion();
   if($conectado=="OK"){
	 $conectado=true;
	 $data_users=get_data("usuario",["nombre_usuario"],null,null,true);
	 if($data_users["status"]!="Error"){
		$data_users=$data_users["message"];
		for ($i=0;$i<count($data_users);$i++){
		   echo"<option value='".$data_users[$i]["nombre_usuario"]."'>".$data_users[$i]["nombre_usuario"]."</option>";  
	    } 
	 }
	 
   }
   echo"</select></div><br>";
   echo"<input type='button' class='boton_login' name='consultar' value='Consultar' style='margin-right:5%;' onclick='auditoria()'><input type='reset' class='boton_login' name='Limpiar' value='Limpiar'>";
   echo"<br><br><input type='button' value='Generar PDF' onclick='reporte_audidoria()' class='boton_login' id='Reporte' style='padding-left:10px;padding-right:10px;margin-right:10px;'>";
   echo "<br><br><div id='tabla_auditoria'>";
   echo"<div class='Table_Container'><table>";
   echo"<tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>Usuario</td><td>Accion Realizada</td><td>Fecha</td><td>Hora</td></tr>";

   if($conectado=="OK"){
	 $data=get_data("reporte_usuario",["nombre_usuario","accion","fecha","hora"],null,null,true);
     if($data["status"]!="Error"){
		$data=$data["message"]; 
		$contador=0;
		foreach($data as $dat){
		    if($contador<30){
			   echo "<tr style='width:25%;background:rgb(231,238,255);'>";
			   echo "<td>".$dat["nombre_usuario"]."</td>";
			   echo "<td>".$dat["accion"]."</td>";
			   echo "<td>".$dat["fecha"]."</td>";
			   echo "<td>".$dat["hora"]."</td>";
			   echo "</tr>";
			   $contador=$contador+1;
			}
		}
	 }
	 
   }
   echo"</table></div>";
   
   echo "<br><br>";
   echo"</div>";
?>
  </form>
  <div id="error_form" style="display:none;background-color:red;">
    <p style="color:white;">por favor seleccione una accion valida</p>
  </div>
  <div id="error_form2" style="display:none;background-color:red;">
    <p style="color:white;">la contraseña debe al menos una letra minuscula, una mayusucula , un numero , un caracter especial y un tamaño de 8 caracteres</p>
  </div>
</div>

</center>

</body>

</html>