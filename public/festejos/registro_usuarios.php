<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 	
	<title>Registro de Usuarios</title>
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
session_start();
if(count($_SESSION)>0){
  $usuario = $_SESSION['username'];
  $nivel=$_SESSION['acceso_user'];
  if (!isset($usuario)) {
	header("location: loggin.php");
	exit();
  }
  else{
	  if($nivel!="administrador"){
	     header("location: paginaprincipal.php"); 
		 exit();
      }
      $_SESSION['lastPage_user']="registro_usuarios.php";
  }
}
else{
	header("location: loggin.php");
	exit();
}
?>

  <h2 id="title1" >Registrar Usuario</h2>
  <form  id="formu" action="send_registro_user.php" method="post">
    <label for="user_name" class="label_login"> usuario</label>
    <input id="user_name" type="text" class="input_login" name="user_name"  placeholder="Ingresar Nombre Usuario"><br><br>
    <label for="pass1" class="label_login">Contraseña</label>
    <input id="pass1" type="password" class="input_login" name="pass1"  placeholder="Ingresar Contraseña"><br><br>
    <label for="pass2" class="label_login">Repita la Contraseña</label>
    <input id="pass2" type="password" class="input_login" name="pass2"  placeholder="Repita la Contraseña"><br><br>
    <label for="permiso" class="label_login">Nivel de Usuario</label>
    <select id="permiso" name="permiso">
      <option value='' selected>Elegir</option>
      <option value='Visitante'>Vistante</option>
      <option value="Secretaria" >Secretaria</option>
    </select>
	<br><br>
	<label for="trabajador" class="label_login">Trabajador</label>
	<select id="trabajador" name="trabajador">
	   <option value='' selected>Elegir</option>
	   <?php
	      require "conexion_bd.php";
		  if(validar_conexion()){
	        $listado=get_data_dict("trabajador",["CI_trabaj","id_nombre"],2,-1,-1);
	        if(count($listado>0)){
				for($i=0;$i<count($listado);$i++){
					$id_name=$listado[$i]["id_nombre"];
					$cedula=$listado[$i]["CI_trabaj"];
					$have_user=false;
					$user_dat=get_data_dict("usuario",["nombre_usuario"],1,["CI_trabaj"],[$cedula]);
	                if(count($user_dat)>0){
						$have_user=true;
					}
					if($cedula!="000000" && $have_user==false){
					   $data_name=get_data_dict("nombre",["nombre","segundo_nombre","apellido","segundo_apellido"],4,["id_nombre"],[$id_name]);
					   $name_trabaj="";
					   if(count($data_name)>0){
						  $name_trabaj=$data_name[0]["nombre"];
				          if($data_name[0]["segundo_nombre"]!=""){
					          $name_trabaj=$name_trabaj." ".$data_name[0]["segundo_nombre"];
				          }
				          $name_trabaj=$name_trabaj." ".$data_name[0]["apellido"];
				          if($data_name[0]["segundo_apellido"]!=""){
					          $name_trabaj=$name_trabaj." ".$data_name[0]["segundo_apellido"];
				          }
					   }
					   echo "<option value='".$cedula."'>".$name_trabaj."</option>";
				    }
				}
		    }
		  }
	   ?>
	</select>
    <h3>preguntas Secretas</h3>
    <p>Campos con * son Obligatorios</p><br>
    <label for="p1" class='label_login'> Pregunta 1* </label>
    <select id="p1" name="p1" >
      <option value='' selected>Elegir</option>
      <option value='Artista Favorito'>Artista Favorito</option>
      <option value='Bebida Favorita'>Bebida Favorita</option>
      <option value='Color Favorito'>Color Favorito</option>
      <option value='Comida Favorita'>Comida Favorita</option>
      <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
      <option value='Lugar Favorito'>Lugar Favorito</option>
      <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
      <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
      <option value='Musica Favorita'>Musica Favorita</option>
      <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
      <option value='Pelicula Favorita'>Pelicula Favorita</option>
      <option value='Personaje Favorito'>Personaje Favorito</option>            
    </select>
    <input type="text" id="r1" name="r1" class='input_login'  placeholder="Respuesta">
    <br>
    <label for="p2" class='label_login'> Pregunta 2* </label>
    <select id="p2" name="p2" >
       <option value='' selected>Elegir</option>
       <option value='Artista Favorito'>Artista Favorito</option>
       <option value='Bebida Favorita'>Bebida Favorita</option>
       <option value='Color Favorito'>Color Favorito</option>
       <option value='Comida Favorita'>Comida Favorita</option>
       <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
       <option value='Lugar Favorito'>Lugar Favorito</option>
       <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
       <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
       <option value='Musica Favorita'>Musica Favorita</option>
       <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
       <option value='Pelicula Favorita'>Pelicula Favorita</option>
       <option value='Personaje Favorito'>Personaje Favorito</option>    
    </select>
    <input type="text" id="r2" name="r2" class='input_login'  placeholder="Respuesta">
    <br>

    <label for="p3" class='label_login'> Pregunta 3* </label>
    <select id="p3" name="p3" >
       <option value='' selected>Elegir</option>
       <option value='Artista Favorito'>Artista Favorito</option>
       <option value='Bebida Favorita'>Bebida Favorita</option>
       <option value='Color Favorito'>Color Favorito</option>
       <option value='Comida Favorita'>Comida Favorita</option>
       <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
       <option value='Lugar Favorito'>Lugar Favorito</option>
       <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
       <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
       <option value='Musica Favorita'>Musica Favorita</option>
       <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
       <option value='Pelicula Favorita'>Pelicula Favorita</option>
       <option value='Personaje Favorito'>Personaje Favorito</option>    
    </select>
    <input type="text" id="r3" name="r3" class='input_login'  placeholder="Respuesta">
    <br>
    <label for="p4" class='label_login'> Pregunta 4* </label>
    <select id="p4" name="p4" >
       <option value='' selected>Elegir</option>
       <option value='Artista Favorito'>Artista Favorito</option>
       <option value='Bebida Favorita'>Bebida Favorita</option>
       <option value='Color Favorito'>Color Favorito</option>
       <option value='Comida Favorita'>Comida Favorita</option>
       <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
       <option value='Lugar Favorito'>Lugar Favorito</option>
       <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
       <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
       <option value='Musica Favorita'>Musica Favorita</option>
       <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
       <option value='Pelicula Favorita'>Pelicula Favorita</option>
       <option value='Personaje Favorito'>Personaje Favorito</option>  
    </select>
    <input type="text" id="r4" name="r4" class='input_login'  placeholder="Respuesta">
    <br>
    <label for="p5" class='label_login'> Pregunta 5* </label>
    <select id="p5" name="p5" >
       <option value='' selected>Elegir</option>
       <option value='Artista Favorito'>Artista Favorito</option>
       <option value='Bebida Favorita'>Bebida Favorita</option>
       <option value='Color Favorito'>Color Favorito</option>
       <option value='Comida Favorita'>Comida Favorita</option>
       <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
       <option value='Lugar Favorito'>Lugar Favorito</option>
       <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
       <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
       <option value='Musica Favorita'>Musica Favorita</option>
       <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
       <option value='Pelicula Favorita'>Pelicula Favorita</option>
       <option value='Personaje Favorito'>Personaje Favorito</option>   
    </select>
    <input type="text" id="r5" name="r5" class='input_login' placeholder="Respuesta">
    <br>

    <label for="p6" class='label_login'> Pregunta 6* </label>
    <select id="p6" name="p6" >
       <option value='' selected>Elegir</option>
       <option value='Artista Favorito'>Artista Favorito</option>
       <option value='Bebida Favorita'>Bebida Favorita</option>
       <option value='Color Favorito'>Color Favorito</option>
       <option value='Comida Favorita'>Comida Favorita</option>
       <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
       <option value='Lugar Favorito'>Lugar Favorito</option>
       <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
       <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
       <option value='Musica Favorita'>Musica Favorita</option>
       <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
       <option value='Pelicula Favorita'>Pelicula Favorita</option>
       <option value='Personaje Favorito'>Personaje Favorito</option>    
    </select>	
    <input type="text" id="r6" name="r6" class='input_login'  placeholder="Respuesta">
    <br>
    <label for="p7" class='label_login'> Pregunta 7 </label>
    <select id="p7" name="p7" >
       <option value='' selected>Elegir</option>
       <option value='Artista Favorito'>Artista Favorito</option>
       <option value='Bebida Favorita'>Bebida Favorita</option>
       <option value='Color Favorito'>Color Favorito</option>
       <option value='Comida Favorita'>Comida Favorita</option>
       <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
       <option value='Lugar Favorito'>Lugar Favorito</option>
       <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
       <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
       <option value='Musica Favorita'>Musica Favorita</option>
       <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
       <option value='Pelicula Favorita'>Pelicula Favorita</option>
       <option value='Personaje Favorito'>Personaje Favorito</option>   
    </select>
    <input type="text" id="r7" name="r7" class='input_login' placeholder="Respuesta">
    <br>

    <label for="p8" class='label_login'> Pregunta 8 </label>
    <select id="p8" name="p8" >
       <option value='' selected>Elegir</option>
       <option value='Artista Favorito'>Artista Favorito</option>
       <option value='Bebida Favorita'>Bebida Favorita</option>
       <option value='Color Favorito'>Color Favorito</option>
       <option value='Comida Favorita'>Comida Favorita</option>
       <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
       <option value='Lugar Favorito'>Lugar Favorito</option>
       <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
       <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
       <option value='Musica Favorita'>Musica Favorita</option>
       <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
       <option value='Pelicula Favorita'>Pelicula Favorita</option>
       <option value='Personaje Favorito'>Personaje Favorito</option> 
    </select>	
    <input type="text" id="r8" name="r8" class='input_login' placeholder="Respuesta">
    <br>
    <br>
    <input type="button" class="boton_login" name="Procesar" value="Registrar" onclick="validar_usuarios()" style="padding:1%";>
    <input type="reset" class="boton_login" name="Limpiar" value="Limpiar" style="margin-left:6%";>
    <br>
    <input type="button" class="boton_login" value="Volver" onclick="go_gestion()" style="margin-left:20%">
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
        <p>Por Favor confirme su Password como Admin</p>
        <input type="password" value="" id="passRequired">
	    <br><br>
	    <input type="button" value="Aceptar" class="boton_login" onclick="ConfirmModalPass()" id="AceptarAdmin" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	    <input type="button" value="Cancelar" class="boton_login" onclick="closeModalPass()" id="CancelarAdmin" style="padding-left:10px;padding-right:10px;margin-left:10px;">
        <br><br><br><br>
      </div>
    </div>
  </form>
  <div id="error_msg" style="display:none;background-color:red">
    <p style="color:white;">por favor Repita la contraseña Correctamente</p>
  </div>
  <div id="error_msgPass" style="display:none;">
    <p style="color:white;">Password de Confirmacion Invalido</p>
  </div>
  <div id="error_msg2" style="display:none;background-color:red">
    <p style="color:white;">la contraseña debe tener al menos una letra minuscula, una mayusucula , un numero , un caracter especial y un tamaño de 8 caracteres</p>
  </div>
  <div id="error_msg3" style="display:none;background-color:red">
    <p style="color:white;">Por favor escriba un nombre de Usuario de almenos cuatro caracteres</p>
  </div>
  <div id="error_msg4" style="display:none;background-color:red">
    <p style="color:white;">Por Favor Indique Almenos Seis Preguntas Secretas</p>
  </div>
  <div id="error_msg5" style="display:none;background-color:red">
    <p style="color:white;">Las Preguntas no Pueden Repetirse</p>
  </div>
  <div id="error_msg6" style="display:none;background-color:red">
    <p style="color:white;">Por Favor Indique las Respuestas</p>
  </div>
  <div id="error_msg7" style="display:none;background-color:red">
     <p style="color:white;">Por Favor Indique el Nivel de Usuario</p>
  </div>
  <div id="error_msg8" style="display:none;background-color:red">
     <p style="color:white;">Por Favor Indique el Trabajador Asociado al Usuario</p>
  </div>
</div>

</center>

</body>

</html>