<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Organizar Fiestas</title>
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

/*Verify No Exist Illegarl Access*/
session_start();
if(count($_SESSION)>0){
  $usuario = $_SESSION['username'];
  $nivel=$_SESSION['acceso_user'];
  if (!isset($usuario)) {
	  header("location: loggin.php");
  }
  else{
	 if($nivel=="Visitante"){
		 header("location: paginaprincipal.php");
		 exit();
	 }
	 $_SESSION['lastPage_user']="organizar_fiestas.php"; 
  }
}
else{
	 header("location: loggin.php");
}

?>

  <h2 id='title1'>Organizar Fiesta</h2>
  <form id="formu" onreset="reset_fiesta()" action='send_organizar_fiestas.php' method='post'>
    <br>
    <label class="label_login">Fecha de la Fiesta:</label>
    <input type="date" name="fecha_fiesta" id="fecha_fiesta"><br><br>
    <input type="hidden" name="fecha" id="fecha" value="">
    <label class="label_login"> hora de la Fiesta:</label>
    <input type="time" name="hora_fiesta" id="hora_fiesta"><br><br>
    <label class="label_login">Nombre del Sitio de la Fiesta</label>
    <input type="text" id="ubicacion" name="ubicacion">
    <br><br>
    <label class="label_login" >Publico de la Fiesta</label>
    <select id="publico" name="publico" size='1'>
      <option value="" selected>Elegir</option>
      <option value="Infantil">Infantil</option>
      <option value="Adolecente">Adolecente</option>
      <option value="Adulto">Adulto</option>
      <option value="Tercera Edad">Tercer Edad</option>
    </select>
    <br><br>
    <label class="label_login" >Tipo de Fiesta</label>
    <select id="tipo_fiesta" name="tipo_fiesta" size='1'>
      <option value="" selected>Elegir</option>
      <option value="cumpleanos">Cumpleaños</option>
      <option value="celebracion">Celebracion</option>
      <option value="baile">Baile</option>
      <option value="baile">Boda</option>
      <option value="baile">Reunion</option>
    </select>
    <br><br>
    <div id="box_alquilables" style="display:inline-block">
      <label class="label_login" style="margin-right:12%" >Productos Alquilar:</label><br><br>
      <select id="lista_productos" name="lista_productos" size='4'>
        <option value=''>Ningun Producto Agregado</option>
      </select>
      <input type="hidden" id="data_productos" name="data_productos">
      <div style="display:inline-block">
        <input  id="boton1" class="boton_login" type="button" value="Agregar" onclick="show_productos('modalProducto',true)"><br><br>
        <input  id="boton2" class="boton_login" type="button" value="Borrar" onclick="borrar_producto(true)">
      </div>
    </div>
    <div id="box_consumibles" style="display:inline-block;margin-left:2%">
      <label class="label_login" style="margin-right:12%" >Productos Consumibles:</label><br><br>
      <select id="lista_consumibles" name="lista_consumibles" size='4'>
        <option value=''>Ningun Producto Agregado</option>
      </select>
      <input type="hidden" id="data_consumibles" name="data_consumibles">
      <div style="display:inline-block">
        <input  id="boton3" class="boton_login" type="button" value="Agregar" onclick="show_productos('modalProducto2',false)"><br><br>
        <input  id="boton4" class="boton_login" type="button" value="Borrar" onclick="borrar_producto(false)">
      </div>
    </div>
    <br><br>
    <div id="box_personal">
      <label class="label_login" style="margin-right:12%" >Personal de la Fiesta:</label><br><br>
      <select id="lista_personal" name="lista_personal" size='4'>
        <option value=''>Ningun Trabajador Agregado</option>
      </select>
      <input type="hidden" id="data_personal" name="data_personal">
      <div style="display:inline-block">
        <input  id="boton5" class="boton_login" type="button" value="Agregar" onclick="show_trabajador('modalTrabajadores')"><br><br>
        <input  id="boton6" class="boton_login" type="button" value="Borrar" onclick="borrar_trabajador()">
      </div>
    </div>
    <br><br>
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
      <input id="costo" type="hidden" name="costo" value="20">
      <input id="impuestos" type="hidden" name="impuestos" value="3.2">
      <input id="total" type="hidden" name="total" value="23.2">
      <label id="label_costo" style="font-weight:bold;padding-right:5%;margin-left:10%;">Subtotal:20$</label>
      <label id="label_impuestos" style="font-weight:bold;padding-right:5%;">Impuestos:3.2$</label>
      <label id="label_total" style="font-weight:bold;padding-right:5%;">Total:23.2$</label>
      <br><br>
    </div>
    <div id="error_Form1" style="background-color:red;color:white;display:none">
	  <p>Por Favor Indique una Fecha Valida</p>
    </div>
    <div id="error_Form2" style="background-color:red;color:white;display:none">
	 <p>Por Favor Indique una fecha Posterior al dia de hoy</p>
    </div>
    <div id="error_Form3" style="background-color:red;color:white;display:none">
	  <p>Por Favor Indique una Hora Valida</p>
    </div>
    <div id="error_Form4" style="background-color:red;color:white;display:none">
	  <p>Por Favor  Indique el Lugar de la Fiesta</p>
    </div>
    <div id="error_Form5" style="background-color:red;color:white;display:none">
	  <p>Por Favor Indique el Publico al que Esta Dirigido la Fiesta</p>
    </div>
    <div id="error_Form6" style="background-color:red;color:white;display:none">
	  <p>Por Favor Indique el Tipo de Fiesta que se Organizara</p>
    </div>
    <div id="error_Form7" style="background-color:red;color:white;display:none">
	  <p>Por Favor Indique el Cliente</p>
    </div>
    <input type="button" id="boton_send" class="boton_login" name="Procesar" value="Procesar" onclick="validar_fiesta()">
    <input type="reset" id="boton_reset" class="boton_login" name="Limpiar" value="Limpiar">

   <div id="modalProducto" class="ModalContainer" style="left:21%;width:60%;height:60%;">
     <div class="ModalX" onclick="close_productos('modalProducto',true)"> X </div>
     <div class="ModalInner">
       <h2>Agregar Producto</h2>
	   <label>Producto</label>
	   <select size='1' id="prodList" onchange="seleccionar_producto(true)">
	     <option value='' >Elegir Producto</option>
	     <?php
	    //add to the select html node the list of products "alquilables"
	    require_once "conexion_bd.php";
		$conexion_OK=false;
	     if(validar_conexion()){
		   $conexion_OK=true;
		   $dat=get_data_dict("producto",["nombre_producto","cantidad_disponible","precio_alquiler","precio"],4,["alquilable"],["true"]);
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
	      <label > Formato de Compra </label>
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
	     <p>No Quedan Unidades del Producto Disponibles para Alquilar </p>
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
   <div id="modalProducto2" class="ModalContainer" style="left:21%;width:60%;height:60%;">
    <div class="ModalX" onclick="close_productos_consumibles('modalProducto2',true)"> X </div>
    <div class="ModalInner">
       <h2>Agregar Producto</h2>
       <label>Producto</label>
	   <select size='1' id="prodList2" onchange="seleccionar_producto(false)">
	     <option value='' >Elegir Producto</option>
	     <?php
	    //Add to the Select Html Node the List of Products No "Alquilables"
	    if($conexion_OK==true){
		   $dat=get_data_dict("producto",["nombre_producto","cantidad_disponible","precio_alquiler","precio"],4,["alquilable"],["false"]);
		   if(count($dat)>0){
			   for($i=0;$i<count($dat);$i++){
				   echo "<option value='".$dat[$i]["nombre_producto"].";".$dat[$i]["cantidad_disponible"].",".$dat[$i]["precio_alquiler"].",".$dat[$i]["precio"]."'>".$dat[$i]["nombre_producto"]."</option>";
			   }
		   }
	   }
	  ?>
	  </select>
	  <br><br>
	  <div id="datos_compra2" style="display:none">
	    <label> Informacion del Producto</label><br><br>
	    <label id="precio_ud2" style="margin-right:10%;">Precio Ud: 0$</label>
	    <label id="cant_disp2">Cantidad Diponible: 000</label>
	    <br><br><br>
	    <label > Formato de Compra </label>
	    <select size='1' id="formato2">
	      <option value='Unidad' >Unidad</option>
	      <option value='Pack(P)' >Paquete Pequeño</option>
	      <option value='Pack(M)' >Paquete Mediano</option>
	      <option value='Pack(G)' >Paquete Grande</option>
	    </select> <br><br>
	    <label> Cantidad a Comprar</label>
	    <input type="number" value="" id="cantidad2">
	    <br><br>
	  </div>
	  <div id="error_producto4" style="background-color:red;color:white;">
	     <p>No Quedan Unidades del Producto Disponibles </p>
	  </div>
	  <div id="error_producto5" style="background-color:red;color:white;">
	     <p>Por Favor Indique la Cantidad a Comprar </p>
	  </div>
	  <div id="error_producto6" style="background-color:red;color:white;">
	     <p>No Quedan Suficientes Existencias del Producto, Por Favor Indique una Cantidad Menor </p>
	  </div>
	  <input type="button" id="send_productosBtn2" value="Aceptar" class="boton_login" onclick=" close_productos_consumibles('modalProducto2',false)" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	  <input type="button" value="Cancelar" class="boton_login" onclick="close_productos_consumibles('modalProducto2',true)" style="padding-left:10px;padding-right:10px;margin-left:10px;">
      <br><br><br><br>
    </div>
  </div>
  <div id="modalTrabajadores" class="ModalContainer" style="left:21%;width:60%;height:60%;">
    <div class="ModalX" onclick="close_trabajador('modalTrabajadores',true)"> X </div>
    <div class="ModalInner">
      <h2>Agregar Personal a la Fiesta</h2>
	  <label>Trabajador</label>
	  <select size='1' id="trabajadores_list" onchange="seleccionar_trabajador()">
	     <option value='' >Elegir Trabajador</option>
	  </select>
	  <br><br>
	  <div id="info_trabajador">
	    <h3>Informacion del Trabajador</h3>
		<label id="label_cedulaT">Cedula:</label><br>
		<label id="label_nombreT">Nombre:</label><br>
		<label id="label_apellidoT">Apellido:</label><br>
		<label id="label_turnoT">Turno:</label><br>
		<input type="hidden" id="nombre_T" value="">
		<input type="hidden" id="apellido_T" value="">
		<input type="hidden" id="cedula_T" value="">
		<input type="hidden" id="turno_T" value="">
		<br>
		<label>Rol en la Fiesta:</label>
		<select id="rol" size='1'>
		   <option value=''>Elegir</option>
		   <option value='Musico'>Musico</option>
		   <option value='Ayudante'>Ayudante</option>
		   <option value='Payaso'>Payaso</option>
		</select>
		<br>
		<br>
		<br>
	  </div>
	  <div id="error_trabaj1" style="background-color:red;color:white;">
	     <p>Por Favor Indique el Rol del Trabajador en la Fiesta </p>
	  </div>
      <input type="button" id="send_trabajadorBtn" value="Aceptar" class="boton_login" onclick=" close_trabajador('modalTrabajadores',false)" style="padding-left:10px;padding-right:10px;margin-right:10px;">
	  <input type="button" value="Cancelar" class="boton_login" onclick="close_trabajador('modalTrabajadores',true)" style="padding-left:10px;padding-right:10px;margin-left:10px;">
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
            <p style="color:white;">El cliente no ha devuelto los productos que ha Alquilado</p>
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
      <h2>Organizar la Fiesta?</h2>
	  <p> Esta Seguro que Quiere Organizar la Fiesta?</p>
      <input type="button" class="boton_login" onclick="ConfirmModal()" value="Si">
	  <input type="button" class="boton_login" onclick="closeModal_byName('modalConfirm')" value="No">
    </div> 
  </div>
  <div id="modalError" class="ModalContainer" style="left:31%;width:40%;height:40%;">
   <div class="ModalX" onclick="closeModal_byName('modalError')"> X </div> 
   <div class="ModalInner">
      <h2>Fecha y hora de Fiesta no Indicada</h2>
      <p> Por Favor Indique la Fecha y hora de la Fiesta antes de Indicar el Personal a Participar</p>
      <input type="button" class="boton_login" onclick="closeModal_byName('modalError')" value="Aceptar">
    </div> 
  </div>
  </form>
  <div id="error_form" style="display:none;background-color:red">
    <p style="color:white;">Por Favor Elige un Producto valido</p>
  </div>
  <div id="error_form2" style="display:none;background-color:red">
    <p style="color:white;">Por Favor Elige un Cliente Valido</p>
  </div>
  <div id="error_form3" style="display:none;background-color:red">
    <p style="color:white;">Por Favor Escriba una Cantidad Valida</p>
  </div>
</div>


</center>

</body>

</html>