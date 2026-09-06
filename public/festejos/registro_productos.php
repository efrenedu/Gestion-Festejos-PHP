<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Registro de productos</title>
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
	if($acceso=="Visitante"){
		header("location:paginaprincipal.php");
	}
	$_SESSION['lastPage_user']="registro_productos.php"; 
  	
}
else{
	 header("location: loggin.php");
	 exit();
}



?>
  <h2 id="title1">Registrar Productos</h2>
  <form id="formu" onreset="reset_regProd()" action="realizar_registro_producto.php" method="post">  
    <label class="label_login" style="margin-left:2%;">Nombre </label>
    <input class="input_login"  type="text" name="producto" id="producto"  placeholder="Ingresar Producto">
    <label class="label_login" style="margin-left:2%;">Productos</label>
    <select onchange="select_producto()" name="lista_productos" id="lista_productos" size='1'>
    <?php 
    //fill the "Select" node with the list of products registers
    require_once __DIR__."/../../private/festejos/db_config.php";
    $first_op="<option value=''>Elegir";
    for($k=0;$k<45;$k++){
  	  $first_op=$first_op."&nbsp;";  
    }
    $first_op=$first_op."</option>";
    echo $first_op;
    $next_html="";
	$res_conex=get_conexion();
	if($res_conex!="OK"){
		exit;
	}
    $listado=get_data("producto",["nombre_producto"],null,null,true);
	if($listado["status"]=="Error"){
		exit;
	}
	$listado=$listado["message"];
	if(count($listado)>0){
		for ($i=0;$i<count($listado);$i++){
			 $valor=$listado[$i]["nombre_producto"];
			 $next_html= $next_html."<option value='".$valor."'>".$valor."</option>";
		}
    }  
    
    if($next_html!=""){
	   echo $next_html;
    } 
    ?>
    </select>
    <br><br><br>
    <label class="label_login">Serial</label>
    <input class="input_login" id="serial" type="text" name="serial"  placeholder="Ingresar Serial"><br><br>
    <label class="label_login">Precio de Alquiler (%) </label>
    <input class="input_login" step="0.01" max="1.0" min="0.0"  type="number" name="precio_alquiler" id="precio_alquiler"  placeholder="Ingresar precio" style="margin-right:12%;"><br><br>
    <label class="label_login">Precio del Producto ($) </label>
    <input class="input_login" min="0.0"  type="number" name="precio" id="precio"  placeholder="Ingresar Precio" style="margin-right:12%;"><br><br>
    <label class="label_login">Cantidad</label>
    <input class="input_login"  min="0" type="number" name="cantidad" id="cantidad" placeholder="Ingresar Cantidad"><br><br>
    <label id="label_disp" class="label_login" style="display:none;">Productos Reservados</label>
    <input class="input_login"  type="number" name="cantidad_disp"  id="cantidad_disp" style="display:none;margin-right:15%;" readonly><br><br>
    <label id="label_disp" class="label_login">Alquilable:</label>
    <input type="radio" id="alquilable_op1" name="alquilable" value="true" checked>Si
    <input type="radio" id="alquilable_op2" name="alquilable" value="false" >No
    <br><br>
    <br>
    <input type="hidden" name="accion_form" id="accion_form">
    <input type="button" id="boton1" onclick="validar_producto()" class="boton_login" name="Registrar" value="Registrar">
    <input type="button" id="boton2" onclick="validar_producto()" class="boton_login" name="Actualizar" value="Actualizar" style="display:none;">
    <input type="reset" class="boton_login" name="Limpiar" value="Limpiar">
  </form>
  <div id="error_form" style="display:none;background-color:red">
    <p style="color:white;">Nombre del Producto no Valido</p>
  </div>
  <div id="error_form2" style="display:none;background-color:red">
    <p style="color:white;">Precio Invalido</p>
  </div>
  <div id="error_form3" style="display:none;background-color:red">
    <p style="color:white;">Cantidad no Valida</p>
  </div>
  <div id="error_form4" style="display:none;background-color:red">
    <p style="color:white;">Serial no Valido</p>
  </div>
  <div id="error_form5" style="display:none;background-color:red">
     <p style="color:white;">Producto Ya Registrado</p>
  </div>
  <div id="error_form6" style="display:none;background-color:red">
    <p style="color:white;">La Cantidad del Producto No Puede ser Menor a la Cantidad de Productos Alquilados/Reservados</p>
  </div>
  <div id="error_form7" style="display:none;background-color:red">
    <p style="color:white;">El Serial ya ha Sido Registrado</p>
  </div>
  <div id="error_form8" style="display:none;background-color:red">
    <p style="color:white;">Porcentaje de Precio de Alquiler Invalido</p>
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