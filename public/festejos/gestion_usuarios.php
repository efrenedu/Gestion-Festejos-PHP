<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Gestion de Usuario</title>
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
<div  id="content3">
<?php  
/*Verify no Exist Illegal Access from User*/

session_start();
require_once __DIR__."/../../private/festejos/db_config.php";  
require_once __DIR__."/../../private/festejos/jwt.php";
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
	$usuario_actual=$res["Message"]["Id_usr"];
	if($acceso!="administrador"){
	     header("location: paginaprincipal.php");
         exit();		 
    }
	$_SESSION['lastPage_user']="gestion_usuarios.php";
  
}
else{
	 header("location: loggin.php");
	 exit();
}

//show the list of users except the User Admin
echo"<h2 id='title1'>Gestion de Usuarios</h2>";
echo"<div class='Table_Container'><table onmouseenter='enter_table()' onmouseleave='exit_table()' ><caption id='titulo_tabla'>Lista de Usuarios</caption>";
echo"<tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>Usuario</td><td>Permiso</td><td>Bloqueado</td></tr>";
$res_conex=get_conexion();
if($res_conex!="OK"){
	echo"</table></div>";
    echo "<br><br>";
    exit;
}

$cond_data=array("conditions_Names"=>array("permiso"),"conditions_Values"=>array("administrador"),"condition_Types"=>array("and"),"conditions_Verify"=>array("!="));	 	  		
$data=get_data("usuario",["nombre_usuario","permiso","bloqueado"],$cond_data,null,true);
if($data["status"]=="Error"){
	echo"</table></div>";
    echo "<br><br>";
    exit;
}
$data=$data["message"];
if(count($data)<=0){
	for($i=0;$i<10;$i++ ){
		echo "<tr id='row".strval($i)."' style='width:25%;background:rgb(231,238,255);'>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "</tr>";
    }
	echo"</table></div>";
    echo "<br><br>";
    exit;
}

$index=0;
foreach($data as $dat){
	$bloq=$dat["bloqueado"];
	if($bloq=="false"){
		$bloq="No";
    }
	else{
		$bloq="Si";
	}
	echo "<tr id='row".strval($index)."' onclick='select_row(this)' style='width:25%;background:rgb(255,250,239);'>";
	echo "<td>".$dat["nombre_usuario"]."</td>";
    echo "<td>".$dat["permiso"]."</td>";
	echo "<td>".$bloq."</td>";
	echo "</tr>";
	$index+=1;
}	
		
		
	

echo"</table></div>";
echo "<br><br>";
?>
  <form id="formu" action="send_modific_user.php" method="post">
    <input type="hidden" value="" name="accion" id="accion">
    <input type="hidden" value="" name="selected_row" id="selected_row">
    <div id="opciones">
      <input type="button" class="boton_login" value="Nuevo Usuario" onclick="activar_gestion_user('nuevo')">
      <input type="button" class="boton_login" value="Modificar Usuario" onclick="activar_gestion_user('editar')">
    </div>
    <br>
    <div id="modific_user" style="display:none">
      <label id="user_id" class="label_login">Usuario</label>
      <br><br>
      <div id="opciones2">
        <input type="button" class="boton_login" value="Desbloquear" name="boton1" onclick="showModalPass('desbloquear')">
        <input type="button" class="boton_login" value="Reset Password" name="boton2" onclick="showModalPass('reset pass')">
        <input type="button" class="boton_login" value="Otorgar Permiso" name="boton3" onclick="activar_gestion_user('permiso display')" >
        <br>
        <input type="button" class="boton_login" value="Borrar Usuario" name="boton3" onclick="showModalPass('borrar user')" style="margin-top:1%;" >
        <br><br>
        <input type="button" class="boton_login" value="Volver" onclick="activar_gestion_user('volver')" style="margin-left:42%;padding-left:2%;padding-right:2%">
      </div>
      <div id="opciones3" style="display:none">
        <label for="permiso" class="label_login">Permiso</label>
        <select id="permiso" name="permiso">
          <option value="" selected>elegir</option>
          <option value="Visitante" >Visitante</option>
          <option value="Secretaria" >Secretaria</option>
        </select>
        <br><br>
        <input type="button" class="boton_login" value="Modificar Permiso" name="boton3" onclick="activar_gestion_user('permiso')" >
        <input type="button" class="boton_login" value="Cancelar" onclick="activar_gestion_user('cancelar')">
      </div>
      <div id="Modal-confirm" class="ModalContainer">
        <div id="closeX_confirm" class="ModalX" onclick="closeModal()"> X </div>
        <div id="InnerConfirm" class="ModalInner">
          <h2>Confirmar </h2>
          <p>Estas Seguro que Deseas Realizar el Cambio?</p>
	      <br><br>
	      <input type="button" value="Si" class="boton_login" onclick="ConfirmModal()" id="AceptarModal" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	      <input type="button" value="No" class="boton_login" onclick="closeModal()" id="CancelarModal" style="padding-left:10px;padding-right:10px;margin-left:10px;">
          <br><br><br><br>
        </div>
      </div>
      <div id="modalPass" class="ModalContainer">
        <div id="closeX" class="ModalX" onclick="closeModalPass()"> X </div>
        <div id="modal_innerPass" class="ModalInner">
         <h2>Confirmar Password </h2>
         <p>Por Favor confirme su Password como Admin</p>
         <input type="password" value="" id="passRequired">
	     <br><br>
	     <input type="button" value="Aceptar" class="boton_login" onclick="ConfirmModalPass()" id="AceptarAdmin" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	     <input type="button" value="Cancelar" class="boton_login" onclick="closeModalPass()" id="CancelarAdmin" style="padding-left:10px;padding-right:10px;margin-left:10px;">
         <br><br><br><br>
        </div>
      </div>
      <br>
    </div>
  </form>
  <div id="error_msg" style="display:none;">
    <p style="color:white;">Por Favor Indique un Usuario Valido</p>
  </div>
  <div id="error_msgPass" style="display:none;">
    <p style="color:white;">Password de Confirmacion Invalido</p>
  </div>
  <div id="error_msg2" style="display:none;">
     <p style="color:white;">Por Favor Indique un Permiso de Usuario Valido</p>
  </div>
</div>
</center>
</body>
</html>