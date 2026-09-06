<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Alquilar Productos</title>
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
	$dat=$res["Message"];
	$permiso=$dat["Acceso"];
	if($permiso=="Visitante"){
		header("location: paginaprincipal.php");
        exit();
	}
	$_SESSION['lastPage_user']="alquilar_productos.php"; 
  	
}
else{
	 header("location: loggin.php");
	 exit();
}


?>

  <h2 id='title1'>Alquilar Productos o Bienes</h2>
  <form id="formu" onreset="reset_alquiler()" action='realizar_alquiler.php' method='post'>
    <label class="label_login" >Productos Alquilar:</label>
    <br><br>
    <select id="lista_productos" name="lista_productos" size='4'>
      <option value=''>Ningun Producto Agregado</option>
    </select>
    <input type="hidden" id="data_productos" name="data_productos">
    <div style="margin-left:5%;display:inline-block">
      <input  id="boton1" class="boton_login" type="button" value="Agregar" onclick="show_productos('modalProducto',true)"><br><br>
      <input  id="boton2" class="boton_login" type="button" value="Borrar" onclick="borrar_producto(true)">
    </div>
    <br><br>
    <label class="label_login">Fecha de Devolución:</label>
    <input type="date" name="fecha_devolucion" id="fecha_devolucion"><br><br>
    <input type="hidden" name="fecha" id="fecha" value="">
    <label class="label_login">Cliente:</label>
    <input type="hidden" id="cliente_params" name="cliente_params" value="">
    <input style="margin-left:2%;" class="boton_login" type="button" value="Identificar" onclick="showClient('modalCliente')" >
    <label style="margin-left:8%;" class="label_login">Metodo de Pago:</label>
    <br><br>
    <div id="cliente_labelBox" style="display:inline-block">
      <p>Cliente</p>
      <p>No Identificado</p>
    </div>
    <div style="display:inline-block; margin-left:15%;">
      <input type="radio" value="Efectivo" name="metodo_pago" checked>Efectivo
      <br>
      <input type="radio" value="Tarjeta de Credito" name="metodo_pago">Tarjeta de Credito
      <br>
      <input type="radio" value="Transferencia" name="metodo_pago">Transferencia
    </div>
    <br><br><br>
    <div>
      <input id="costo" type="hidden" name="costo" value="0">
      <input id="impuestos" type="hidden" name="impuestos" value="0">
      <input id="total" type="hidden" name="total" value="0">
      <label id="label_costo" style="font-weight:bold;padding-right:5%;margin-left:10%;">Subtotal:0$</label>
      <label id="label_impuestos" style="font-weight:bold;padding-right:5%;">Impuestos:0$</label>
      <label id="label_total" style="font-weight:bold;padding-right:5%;">Total:0$</label>
      <br><br>
    </div>
    <div id="error_Form1" style="background-color:red;color:white;display:none">
	   <p>Por Favor Agregue los Productos Alquilar</p>
    </div>
    <div id="error_Form2" style="background-color:red;color:white;display:none">
	   <p>Por Favor Indique el Cliente</p>
    </div>
    <div id="error_Form3" style="background-color:red;color:white;display:none">
	   <p>Por Favor Indique una Fecha Posterior al Dia de Hoy</p>
    </div>
    <div id="error_Form4" style="background-color:red;color:white;display:none">
	   <p>Por Favor Indique una Fecha Valida</p>
    </div>
    <input type="button" id="boton_send" class="boton_login" name="Procesar" value="Procesar" onclick="validar_alquiler()">
    <input type="reset" id="boton_reset" class="boton_login" name="Limpiar" value="Limpiar">
    <div id="modalProducto" class="ModalContainer" style="left:21%;width:60%;height:60%;">
       <div class="ModalX" onclick="close_productos('modalProducto',true)"> X </div>
       <div class="ModalInner">
         <h2>Agregar Producto</h2>
	     <label>Producto</label>
	     <select size='1' id="prodList" onchange="seleccionar_producto(true)">
	        <option value='' >Elegir Producto</option>
	        <?php
	       //set the list of products with state "alquilable" on ComboBox(Select) component
           require_once __DIR__."/../../private/festejos/db_config.php";

	       if(get_conexion()=="OK"){
			  $cond_data=array("conditions_Names"=>array("alquilable"),"conditions_Values"=>array("true"),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
     
		      $dat=get_data("producto",["nombre_producto","cantidad_disponible","precio_alquiler","precio"],$cond_data,null,true);
		      if($dat["status"]=="Error"){
				  exit;
			  }
			  $dat=$dat["message"];
			  if(count($dat)>0){
			      for($i=0;$i<count($dat);$i++){
				      echo "<option value='".$dat[$i]["nombre_producto"].";".$dat[$i]["cantidad_disponible"].",".$dat[$i]["precio_alquiler"].",".$dat[$i]["precio"]."'>".$dat[$i]["nombre_producto"]."</option>";
			     }
		      }
	       }
	       ?>
	     </select>
	     <br><br>
	    <div id="datos_compra" style="display:none">
	       <label> Informacion del Producto</label><br><br>
	       <label id="precio_ud" style="margin-right:10%;">Precio Ud: 0$</label>
	       <label id="cant_disp">Cantidad Diponible: 000</label>
	       <br><br><br>
	       <label > formato de Compra </label>
	       <select size='1' id="formato">
	         <option value='Unidad' >Unidad</option>
	         <option value='Pack(P)' >Paquete Pequeño</option>
	         <option value='Pack(M)' >Paquete Mediano</option>
	         <option value='Pack(G)' >Paquete Grande</option>
	       </select> <br><br>
	       <label> Cantidad a Comprar</label>
	       <input type="number" value="" id="cantidad">
	       <br><br>
	    </div>
	    <div id="error_producto" style="background-color:red;color:white;">
	       <p>No quedan Unidades del Producto Disponibles para Alquilar </p>
	    </div>
	    <div id="error_producto2" style="background-color:red;color:white;">
	       <p>Por Favor Indique la Cantidad a Comprar </p>
	    </div>
	    <div id="error_producto3" style="background-color:red;color:white;">
	       <p>No Quedan Suficientes Existencias del Producto, Por Favor Indique una Cantidad Menor </p>
	    </div>
	    <input type="button" id="send_productosBtn" value="Aceptar" class="boton_login" onclick=" close_productos('modalProducto',false)" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	    <input type="button" value="Cancelar" class="boton_login" onclick="close_productos('modalProducto',true)" style="padding-left:10px;padding-right:10px;margin-left:10px;">
        <br><br><br><br>
      </div>
    </div>
   <div id="modalCliente" class="ModalContainer" style="left:21%;width:60%;height:60%;">
     <div class="ModalX" onclick="closeClient('modalCliente',true)"> X </div> 
      <div class="ModalInner">
		<div id="p1">
		   <h2>Identificar Cliente</h2>
		   <label> Cedula:</label>
		   <input type="text" id="client_search" value="">
	       <input type="button" id="search_ClientBtn" value="Buscar" class="boton_login" onclick="SearchClient()" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	       <br><br>
		   <input type="button" id="reg_clientBtn" value="Registrar" class="boton_login" onclick="RegistrarClient(false,false)" style="padding-left:10px;padding-right:10px;margin-left:50%;">
           <br><br><br>
		   <div id="error_client1" style="display:none;background-color:red">
               <p style="color:white;">por favor Indique la Cedula del Cliente</p>
           </div>
		   <div id="error_client2" style="display:none;background-color:red">
               <p style="color:white;">El cliente no ha sido Registrado</p>
           </div>
		   <div id="error_client7" style="display:none;background-color:red">
               <p style="color:white;">El Cliente No ha Devuelto los Productos que ha Alquilado</p>
           </div>
		   <div id="cliente_info" >
		      <h2> Informacion del Cliente</h2>
			  <div id="data_client">Informacion</div>
			  <br><br>
		   </div>
	       <input type="button" id="send_ClientBtn" value="Aceptar" class="boton_login" onclick=" closeClient('modalCliente',false)" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	       <input type="button" value="Cancelar" class="boton_login" onclick="closeClient('modalCliente',true)" style="padding-left:10px;padding-right:10px;margin-left:10px;">
        </div>
		<div id="p2">
		   <h2>Registrar Cliente</h2>
		   <label> Cedula:</label>
		   <input type="text" id="client_cedula" value="" placeholder="Cedula del Cliente">
	       <br><br>
		   <label> Nombres:</label>
		   <input type="text" id="client_names" value="" placeholder="Nombres del Cliente">
	       <br><br>
		   <label> Apellidos:</label>
		   <input type="text" id="client_apellidos" value="" placeholder="Apellidos del Cliente">
	       <br><br>
		   <div id="error_client3" style="display:none;background-color:red">
               <p style="color:white;">Por favor Escriba una Cedula Valida</p>
           </div>
		   <div id="error_client4" style="display:none;background-color:red">
               <p style="color:white;">Nombres del Cliente Invalidos</p>
           </div>
		   <div id="error_client5" style="display:none;background-color:red">
               <p style="color:white;">Apellidos del Cliente Invalidos</p>
           </div>
		   <div id="error_client6" style="display:none;background-color:red">
               <p style="color:white;">La Cedula ya ha Sido Registrada</p>
           </div>
		   <br><br>
		   <input type="button" id="send_ClientReg" value="Registrar" class="boton_login" onclick=" RegistrarClient(false,true)" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	       <input type="button" id="volver_ClientReg" value="Volver" class="boton_login" onclick="RegistrarClient(true,false)" style="padding-left:10px;padding-right:10px;margin-left:10px;">
		</div>
      </div>
   </div>
   <div id="modalConfirm" class="ModalContainer" style="left:31%;width:40%;height:40%;">
     <div class="ModalX" onclick="closeModal_byName('modalConfirm')"> X </div>
     <div class="ModalInner">
       <h2>Alquilar Productos?</h2>
	   <p> Esta Seguro que Quiere Alquilar los Productos?</p>
       <input type="button" class="boton_login" onclick="ConfirmModal()" value="Si">
	   <input type="button" class="boton_login" onclick="closeModal_byName('modalConfirm')" value="No">
     </div>
   </div>
  </form>
</div>
<div id="error_form" style="display:none;background-color:red">
  <p style="color:white;">Por Favor Elige un Producto Valido</p>
</div>
<div id="error_form2" style="display:none;background-color:red">
  <p style="color:white;">Por Favor Elige un Cliente Valido</p>
</div>
<div id="error_form3" style="display:none;background-color:red">
  <p style="color:white;">Por Favor Escriba una Cantidad Valida</p>
</div>
</center>

</body>

</html>