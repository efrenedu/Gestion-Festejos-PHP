<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1.0"/> 
	<title>Modificar Informacion del Usuario </title>
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
<div id="options_container">
<div class="opciones_user" onclick="modificar_opciones_user('pass')">Cambiar Contraseña</div>
<div class="opciones_user" onclick="modificar_opciones_user('preguntas')">Cambiar Preguntas Secretas</div>
<div class="opciones_user" onclick="modificar_opciones_user('perfil')">Modificar Perfil</div>
</div>

<?php  

/*Verify No Exist Access Illegal */
session_start();
require_once __DIR__."/../../private/festejos/jwt.php";
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
	$usuario=$res["Message"]["Id_usr"];
	$_SESSION['lastPage_user']="modificar_usuario.php";
  
}
else{
	 header("location: loggin.php");
	 exit();
}

?>

  <form id="formu"  action="modifica_user.php" method="post"  enctype="multipart/form-data">
    <input id="tipo_modificacion" type="hidden" name="modificacion" value="pass">
    <div id="pass_div">
       <h2 id="title1">Modificar contraseña</h2>
       <label for="pass1" class="label_login"> Nueva contraseña</label>
       <input id="pass1" type="password" class="input_login" name="pass1"  placeholder="Nueva Contraseña"><br><br>
       <label for="pass2" class="label_login"> Repita contraseña</label>
       <input id="pass2" type="password" class="input_login" name="pass2"  placeholder="Repita Contraseña"><br><br>
       <br>
    </div >
    <div id="preguntas_div" style="display:none">
       <h2 id="title1">Modificar Preguntas Secretas</h2>
       <h4>Campos con * son Obligatorios</h4>
       <div id="content_pregs">  
         <?php 
		     //request write the data of "preguntas secretaS" of User without init sesssion
             require "preguntas_secretas.php";
             set_code(false);
          ?>
         <br>
         <br>
       </div>
    </div>
    <div id="perfil_div" style="display:none">
      <h2 id="title1">Modificar Perfil</h2>
      <label for="foto: " class="label_login"> Foto de Perfil</label>
      <input type="file" id="foto" name="foto">
      <br><br>
      </div>
      <input type="button" class="boton_login" name="Procesar" value="Procesar" onclick="validar_modific_user()">
      <input type="reset" class="boton_login" name="Limpiar" value="Limpiar">
      <div id="Modal-confirm" class="ModalContainer">
        <div id="closeX_confirm"  class="ModalX" onclick="closeModal()"> X </div>
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
           <p>Por Favor confirme su Password de Usuario</p>
           <input type="password" value="" id="passRequired">
	       <br><br>
	       <input type="button" value="Aceptar" class="boton_login" onclick="ConfirmModalPass()" id="AceptarAdmin" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	       <input type="button" value="Cancelar" class="boton_login" onclick="closeModalPass()" id="CancelarAdmin" style="padding-left:10px;padding-right:10px;margin-left:10px;">
           <br><br><br><br>
         </div>
     </div>
  </form>
  <div id="error_msg2" style="display:none;">
    <p style="color:white;">La contraseña debe contener almenos una letra mayuscula, una letra minuscula, un numero, un caracter especial y tener una lngitud de 8 caracteres</p>
  </div>
  <div id="error_msgPass" style="display:none;">
    <p style="color:white;">Password de Confirmacion Invalido</p>
  </div>
  <div id="error_msg" style="display:none;">
    <p style="color:white;">por favor repita la contraseña correctmente</p>
  </div>
  <div id="error_msg3" style="display:none;background-color:red">
    <p style="color:white;">Por favor seleccione una imagen en formato : JPG,JPEG o PNG</p>
  </div>
  <div id="error_msg4" style="display:none;background-color:red">
    <p style="color:white;">Por favor indique almenos seis pregunta secretas</p>
  </div>
  <div id="error_msg5" style="display:none;background-color:red">
    <p style="color:white;">Las preguntas Secretas no Pueden Repetirse</p>
  </div>
  <div id="error_msg6" style="display:none;background-color:red">
     <p style="color:white;">Por favor indique Respuestas Validas</p>
  </div>
</div>
</center>

</body>

</html>