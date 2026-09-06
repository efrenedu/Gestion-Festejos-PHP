<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Registro de trabajadores</title>
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
	$acceso=$res["Message"]["Acceso"];
	if($acceso!="administrador"){
		header("location:paginaprincipal.php");
	}
	$_SESSION['lastPage_user']="registrar_trabajador.php"; 
  	
}
else{
	 header("location: loggin.php");
	 exit();
}


?>

  <h2 id="title1">Registrar Trabajador</h2>
  <form onreset="reset_telefonos()"  id="formu" action="send_registro_trabajador.php" method="post">
     <label  class="label_login"> Cedula</label>
     <label style="margin-left:25%;" class="label_login">Trabajadores:   </label>
     <br>
     <input id="cedula" type="text" class="input_login" style="margin-right:5%;" name="cedula"  placeholder="Ingresar Cedula">
     <select onchange="select_trabajador()" id='trabajadores' name='trabajadores' size='1' >

        <?php 
		  /*set the List of "Trabajadores" On the Select Node*/
          require_once __DIR__."/../../private/festejos/db_config.php";
          $first_op="<option value=''>Elegir";
          for($k=0;$k<45;$k++){
	          $first_op=$first_op."&nbsp;";  
          }
          $first_op=$first_op."</option>";
          echo $first_op;
          $next_html="";
          if(get_conexion()=="OK"){
			
			$join_data=array();
            $join_data["nombre"]=array("query_field"=>array("nombre","segundo_nombre","apellido","segundo_apellido"),"share_fields"=>array("field"=>"id_nombre","table_reference"=>"trabajador"),"Conditions_join"=>null); 
	        $cond_data=array("conditions_Names"=>array("CI_trabaj"),"conditions_Values"=>array("000000"),"condition_Types"=>array("and"),"conditions_Verify"=>array("!="));	 	  
			
			$listado=get_data("trabajador",["CI_trabaj"],$cond_data,$join_data,true);
            if($listado["status"]=="Error"){
				exit;
			}
			$listado=$listado["message"];
			$cond_data=array("conditions_Names"=>array("permiso","CI_trabaj"),"conditions_Values"=>array("administrador","000000"),"condition_Types"=>array("and","and"),"conditions_Verify"=>array("=","!="));	 	  
			$dat_admin=get_data("usuario",["CI_trabaj"],$cond_data,null,true);
			if($dat_admin["status"]=="Error"){
				exit;
			}
			$dat_admin=$dat_admin["message"];
			$id_admin="000000";
			if(count($dat_admin)>0){
				$id_admin=$dat_admin[0]["CI_trabaj"];
			}
			for ($i=0;$i<count($listado);$i++){
			    $cedula=$listado[$i]["CI_trabaj"];
			    $id_name=$listado[$i]["id_nombre"];
			    $is_admin="false";
				if($cedula==$id_admin){
					$is_admin="true";
				}
				$nombre=$listado[$i]["nombre"];
				if($listado[$i]["segundo_nombre"]!=""){
					$nombre=$nombre." ".$listado[$i]["segundo_nombre"];
				}
				$nombre=$nombre." ".$listado[$i]["apellido"];
				if($listado[$i]["segundo_apellido"]!=""){
					$nombre=$nombre." ".$listado[$i]["segundo_apellido"];
				}
				$next_html= $next_html."<option value='".$cedula.";".$is_admin."'>".$nombre."</option>";   
		    }
	        
          }
          if($next_html!=""){
	         echo $next_html;
          }
          		  
       ?>
			
     </select>
     <br><br><br>
	
     <label class="label_login"> Nombres</label>
     <input id="nombre" type="text" class="input_login" name="nombre" style="margin-right:30%;" placeholder="Ingresar Nombre"><br><br>
     <label  class="label_login"> Apellidos</label>
     <input id="apellido" type="text" class="input_login" name="apellido"  style="margin-right:30%;" placeholder="Ingresar Apellido"><br><br>
     <label class="label_login"> Edad</label>
     <input id="edad" type="text" class="input_login" name="edad"  placeholder="Ingresar Edad" style="margin-right:25%;">
     <label class="label_login" id="estatus_label" style="display:none;margin-left:2%;"> Estatus:</label>
     <select style="display:none" id="estatus" name="estatus" size='1' >
       <option value="activo" selected >Activo</option>
       <option value="reposo" >De Reposo</option>
       <option value="vaciones">De Vacaciones</option>
     </select>
     <br><br>
     <label class="label_login"> Telefono:   </label>
     <input id="telefono_field" type="text" class="input_login" placeholder="Ingresar Telefono">
     <input id="telefono_button" onclick="agregar_telefono()" class="boton_login" type="button" value="Agregar" style="margin-left:6%;">
     <input id="telefono_button_remove" onclick="remove_telefono()" class="boton_login" type="button" value="Borrar">
     <input id="telefonos_list" type="hidden" value="" name="telefonos_list">
     <br><br>
     <label class="label_login"> Telefonos:   </label><br>
     <select id="telefonos" name="telefonos" size='4' >
       <option value=''>Ningun Telefono Agregado</option>";
     </select>
     <br><br>
     <label  class="label_login"> Nivel Academico</label>
     <select  id="nivel_academico" name="nivel_academico" size='1'>
       <option value='' selected>Elegir</option>
       <option value='Bachiller' >Bachiller</option>
       <option value='Tsu'>TSU</option>
	   <option value='Ingeniero'>Ingeniero</option>
	    <option value='Licensiado'>Licensiado</option>
     </select>
     <br><br>
     <label class="label_login"> Cargo   </label>
     <select id='carg' name='cargo' size='1'>
       <option value='' selected>Elegir</option>
       <option value='Directivo'>Directivo</option>     
	   <option value='Administrativo'>Administrativo</option>
       <option value='Atencion al Cliente'>Atencion al Cliente</option>
	   <option value='Artista'>Artista</option>
	   <option value='Obrero'>Obrero</option>
     </select>
     <br><br>
     <label class="label_login"> Turno   </label>
     <select id='turno' name='turno' size='1'>
       <option value='' selected>Elegir</option>
       <option value='diurno'>Diurno</option>
       <option value='nocturno'>Nocturno</option>
     </select>
     <input id="accionRegistro" name="accionRegistro" type="hidden" value="">
     <br>
     <br>
     <br>
     <input id="boton1" type="button" onclick="validar_trabajadores()" class="boton_login" name="Procesar1" value="Registrar" >
     <input id="boton2" style="display:none"; type="button" onclick="validar_trabajadores()" class="boton_login" name="Procesar2" value="Actualizar" >
     <input type="reset" class="boton_login" name="Limpiar" value="Limpiar">
  </form>
  <div id="error_form" style="display:none;background-color:red">
     <p style="color:white;">Por Favor Seleccione Un Cargo Valido</p>
  </div>
<div id="error_form2" style="display:none;background-color:red">
   <p style="color:white;">cedula no valida</p>
</div>
<div id="error_form3" style="display:none;background-color:red">
   <p style="color:white;">nombre invalido</p>
</div>
<div id="error_form4" style="display:none;background-color:red">
   <p style="color:white;">apellido invalido</p>
</div>
<div id="error_form5" class="error_msg" style="display:none;">
   <p>Edad no Valida</p>
</div>
<div id="error_form6" class="error_msg" style="display:none;">
   <p>Por Favor Agregue Almenos un Numero de Telefono</p>
</div>
<div id="error_form7" class="error_msg" style="display:none;">
   <p>Por favor Indique el Nivel Academico</p>
</div>
<div id="error_form10" class="error_msg" style="display:none;">
   <p>Por favor Indique El Turno del Trabajador</p>
</div>
<div id="error_form11" class="error_msg" style="display:none;">
   <p>Cedula Del trabajador ya Registrada</p>
</div>

<div id="ModalMessage" class="ModalContainer">
  <div class="ModalX" onclick="closeModal_byName('ModalMessage')"> X </div>
   <div class="ModalInner">
      <h2>Telefono Ya Registrado </h2>
      <p>El Telefono ya ha sido Registrado por otro Trabajador</p>
	  <br><br>
	  <input type="button" value="Aceptar" class="boton_login" onclick="closeModal_byName('ModalMessage')" id="AceptarModal" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	  <br><br><br><br>
   </div>
</div>
<div id="ModalMessage2" class="ModalContainer">
  <div class="ModalX" onclick="closeModal_byName('ModalMessage2')"> X </div>
   <div class="ModalInner">
      <h2>Telefono Ya Existe </h2>
      <p>El Telefono ya se ha Agregado</p>
	  <br><br>
	  <input type="button" value="Aceptar" class="boton_login" onclick="closeModal_byName('ModalMessage2')" id="AceptarModal" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	  <br><br><br><br>
   </div>
</div>
<div id="ModalMessage3" class="ModalContainer">
  <div class="ModalX" onclick="closeModal_byName('ModalMessage3')"> X </div>
   <div class="ModalInner">
      <h2>Telefono no Valido </h2>
      <p>Por favor Indique el Telefono a Borrar</p>
	  <br><br>
	  <input type="button" value="Aceptar" class="boton_login" onclick="closeModal_byName('ModalMessage3')" id="AceptarModal" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	  <br><br><br><br>
   </div>
</div>
<div id="ModalMessage4" class="ModalContainer">
  <div class="ModalX" onclick="closeModal_byName('ModalMessage4')"> X </div>
   <div class="ModalInner">
      <h2>Telefono no Valido </h2>
      <p>Por Escriba un Telefono Valido</p>
	  <br><br>
	  <input type="button" value="Aceptar" class="boton_login" onclick="closeModal_byName('ModalMessage4')" id="AceptarModal" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	  <br><br><br><br>
   </div>
</div>
<div id="Modal-confirm" class="ModalContainer">
  <div class="ModalX" onclick="closeModal_byName('Modal-confirm')"> X </div>
   <div class="ModalInner">
      <h2>Confirmar Registro/Actualizacion</h2>
      <p>Esta Seguro que Desea Realizar los Cambios?</p>
	  <br><br>
	  <input type="button" value="Si" class="boton_login" onclick="ConfirmModal()" id="AceptarModal" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	  <input type="button" value="No" class="boton_login" onclick="closeModal_byName('Modal-confirm')" id="AceptarModal" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	  <br><br><br><br>
   </div>
</div>
</div>

</center>

</body>

</html>
