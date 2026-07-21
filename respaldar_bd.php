<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 	
	<title>Respaldar Base de Datos</title>
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
if(count($_SESSION)>0){
  $usuario = $_SESSION['username'];
  $nivel=$_SESSION['acceso_user'];
  if (!isset($usuario)) {
	 header("location: loggin.php");
  }
  else{
	   if($nivel!="administrador"){
	      header("location: paginaprincipal.php"); 
          exit();		  
       }
	   $_SESSION['lastPage_user']="respaldar_bd.php";  
  }
  
}
else{
	header("location: loggin.php");
}
?>
  <h2 id="title1">Respaldar Base de Datos</h2>
  <form id="formu"  action="respaldo_restore.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="accion" id="accion" value="respaldo" >
    <label for="opcion"> Accion a Realizar</label>
    <input onchange="select_accion_respaldo()" type="radio" name="opcion" value="respaldar">Respaldar
    <input onchange="select_accion_respaldo()" type="radio" name="opcion" value="restaurar">Restaurar
    <br>
    <br>
    <div id="file_respaldo" style="display:none;">
        <input type="file" name="source" id="source" >
    </div>
    <div id="error_msg" style="display:none;">
       <p style="color:white;">Por favor Seleccione un Archivo Valido</p>
    </div>
    <div id="error_msg2" style="display:none;">
       <p style="color:white;">El Archivo debe Ser en Formato ZIP</p>
    </div>
    <div id="error_msgPass" style="display:none;">
       <p style="color:white;">Password de Confirmacion Invalido</p>
    </div>
    <br>
    <br>
    <input class="boton_login" type="button" value="Procesar" onclick="validar_respaldo()">
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
        <p>Por Favor Confirme su Password como Admin</p>
        <input type="password" value="" id="passRequired">
	    <br><br>
	    <input type="button" value="Aceptar" class="boton_login" onclick="ConfirmModalPass()" id="AceptarAdmin" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	    <input type="button" value="Cancelar" class="boton_login" onclick="closeModalPass()" id="CancelarAdmin" style="padding-left:10px;padding-right:10px;margin-left:10px;">
        <br><br><br><br>
      </div>
    </div>
  </form>
</div>

</center>

</body>

</html>

