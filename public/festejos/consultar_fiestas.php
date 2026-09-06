<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Consultar Festas Organizadas </title>
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
  <h2 id="title1">Consultar Fiestas Organizadas</h2>
  <form  method="post">
    <label >Filtro:</label>
    <select id="filtro" size='1' onchange="activar_filtro_consultas()">
      <option value="">Elegir</option>
      <option value="lugar">Por Direccion</option>
      <option value="CI_cliente">Por Cedula del Cliente</option>
      <option value="publico">Por Publico</option>
      <option value="tipo_fiesta">Por tipo de Fiesta</option>
    </select>
    <input type="text" value="" id="filtro_val" placeholder="Indique el Valor a Filtrar" style="display:none;margin-left:5%">
    <br><br>
<?php  
/*Verify no Exist Illegal Access from User*/

    session_start();
    if(count($_SESSION)>0){
	  $usuario = $_SESSION['username'];
      $nivel=$_SESSION['acceso_user'];
      if (!isset($usuario)) {
	      header("location: loggin.php");
		  exit();
     }
	 else{
		   $_SESSION['lastPage_user']="consultar_fiestas.php"; 
	 }
   }   
   else{
	   header("location: loggin.php");
	   exit();
   }

//add the list of Party Requests to the table component

   require "conexion_bd.php";
   echo "<div class='Table_Container'><table id='tabla'><tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>CI cliente</td><td>Sitio</td><td>Estatus</td><td>Fecha</td><td>Hora</td><td>Publico</td><td>Tipo</td></tr>";
   if(validar_conexion()){
      $data=get_data_dict("fiesta",["CI_cliente","lugar","estatus","fecha","hora","publico","tipo_fiesta"],7,-1,-1);
      if(count($data)>0){
         foreach ($data as $d){
           echo"<tr style='width:25%;background:rgb(255,250,239);'><td>".$d["CI_cliente"]."</td><td>".$d["lugar"]."</td><td>".$d["estatus"]."</td><td>".$d["fecha"]."</td><td>".$d["hora"]."</td><td>".$d["publico"]."</td><td>".$d["tipo_fiesta"]."</td></tr>"; 
         }
      } 
   }
   echo "</table></div><br>";
?>

    <input type="button" value="Consultar" onclick="consultar_fiestas()" class="boton_login">
    <input style="margin-left:2%" type="button" value="Generar Reporte" onclick="Pdf_Consults('Party')" class="boton_login">
   
  </form>	
  <form method="post" id="second_form" action="pdf_consultas.php" target="_blank">
     <input name="data_reporte" id="data_reporte" type="hidden" value="">
	 <input name="title_report" id="title_report" type="hidden" value="">
  </form>
  <br>
  <div id="error_report" class="error_msg" style="display:none;">
     <p>No Existen Registros Para generar Reporte</p>
  </div>
  <div id="error_msg" class="error_msg" style="display:none;">
     <p>No Existen Registros Asociados a la Consulta</p>
  </div>
</div>
</center>

</body>

</html>