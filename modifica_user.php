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

<div  id="content3">
<?php  

/*Verify No Exist Invalid Access*/

session_start();
require "conexion_bd.php";
$usuario="";
if(count($_SESSION)>0){
  $usuario = $_SESSION['username'];
  $nivel=$_SESSION['acceso_user'];
  if (!isset($usuario)) {
	 header("location: loggin.php");
	 exit();
  }
  else{
	   $last_page=$_SESSION['lastPage_user'];
	   $_SESSION['lastPage_user']="modifica_user.php"; 
	   if($last_page!="modificar_usuario.php"){
		   header("location: paginaprincipal.php");
	       exit();
	   }
  }
  
}
else{
	header("location: loggin.php");
	exit();
}

/*Process a Request of Modification from a User*/

if(count($_POST)>0){
   date_default_timezone_set('America/Caracas');
   if(isset($_POST["modificacion"])){
      if(validar_conexion()){
		$exito=true;
	    $modific=$_POST["modificacion"];
		if($modific=="pass"){
	       if(isset($_POST["pass1"])){
			  $new_pass=$_POST["pass1"];
			  $new_pass=encript($new_pass);
		      update_data("usuario",["contrasena"],[$new_pass],1,["nombre_usuario"],[$usuario]);
           }
		   else{
			   $exito=false;
			   echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>
	           <a href='paginaprincipal.php' class='boton1'>Volver</a>
	           "; 
		   }
		}
		else if($modific=="preguntas"){
			if(isset($_POST["p1"])&& isset($_POST["p2"]) && isset($_POST["p3"]) && isset($_POST["p4"]) && isset($_POST["p5"]) && isset($_POST["p6"])){
				 if(isset($_POST["r1"])&& isset($_POST["r2"]) && isset($_POST["r3"]) && isset($_POST["r4"]) && isset($_POST["r5"]) && isset($_POST["r6"])){
				   $preguntas=array($_POST["p1"],$_POST["p2"],$_POST["p3"],$_POST["p4"],$_POST["p5"],$_POST["p6"]);
				   $respuestas=array($_POST["r1"],$_POST["r2"],$_POST["r3"],$_POST["r4"],$_POST["r5"],$_POST["r6"]);
				   if(isset($_POST["p7"])){
					   if($_POST["p7"]!="" && $_POST["r7"]!=""){
					      $preguntas[]=$_POST["p7"];
					      $respuestas[]=$_POST["r7"];
					   }
				   }
				   if(isset($_POST["p8"])){
					  if($_POST["p8"]!="" && $_POST["r8"]!=""){
					      $preguntas[]=$_POST["p8"];
					      $respuestas[]=$_POST["r8"];
					   }
				   }
				   $temp_dat=get_data_dict("pregunta_secreta",["id_pregunta","numero",],2,["nombre_usuario"],[$usuario]);
				   if(count($temp_dat)>0){
					   $resto=count($preguntas)-count($temp_dat);
					   $last=-1;
					   for($i=0;$i<count($preguntas);$i++){
					      for($k=0;$k<count($temp_dat);$k++){
						   if(intval($temp_dat[$k]["numero"])==($i+1)){
							   update_data("pregunta_secreta",["pregunta","respuesta","numero"],[$preguntas[$i],strtolower($respuestas[$i]),strval($i+1)],3,["id_pregunta"],[$temp_dat[$k]["id_pregunta"]]);
						       if($i>$last){
								   $last=$i;
							   }
							   $k=count($temp_dat);
     						  }
					     }
					   }
					   if($resto>0){
						     $last=$last+1;
						     for($j=$last;$j<count($preguntas);$j++){
								$d_preg=array("id_pregunta"=>generate_id("pregunta_secreta","id_pregunta",true),"nombre_usuario"=>$usuario,"pregunta"=>$preguntas[$j],"respuesta"=>strtolower($respuestas[$j]),"numero"=>strval($j+1));
						        add_data_dict("pregunta_secreta",$d_preg);
					         }
					   }
					   else{
						  if($resto<0){
						     $last=$last+1;
						     for($j=0;$j<count($temp_dat);$j++){
								   if(intval($temp_dat[$j]["numero"])>=($last+1)){
						              delete_data("pregunta_secreta",["nombre_usuario","numero"],[$usuario,$temp_dat[$j]["numero"]]);
					               }
							 } 
						  }
					   }
				   }
				   else{
					  for($i=0;$i<count($preguntas);$i++){
					    $d_preg=array("id_pregunta"=>generate_id("pregunta_secreta","id_pregunta",true),"nombre_usuario"=>$usuario,"pregunta"=>$preguntas[$i],"respuesta"=>strtolower($respuestas[$i]),"numero"=>strval($i+1));
						add_data_dict("pregunta_secreta",$d_preg);
					  }
				    
				   }
				}
				else{
					$exito=false;
				    echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>
	                <a href='paginaprincipal.php' class='boton1'>Volver</a>
	                "; 
				}
			}
			else{
				 $exito=false;
				 echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>
	             <a href='paginaprincipal.php' class='boton1'>Volver</a>
	             "; 
			}
		}
		else if($modific=="perfil"){
     
			if(isset($_FILES["foto"])){
				   copy($_FILES["foto"]["tmp_name"],$_FILES["foto"]["name"]);
                   $nombre=$_FILES["foto"]["name"];
                   $dir="images".DIRECTORY_SEPARATOR.$nombre;
                   move_uploaded_file($_FILES["foto"]["tmp_name"],$dir);
				   update_data("usuario",["foto"],[$nombre],1,["nombre_usuario"],[$usuario]);
			       if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$nombre)){
						unlink(__DIR__.DIRECTORY_SEPARATOR.$nombre);
				   }
			}
			else{

				 $exito=false;
				 echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>
	             <a href='paginaprincipal.php' class='boton1'>Volver</a>
	             "; 
			}
		}
	    if($exito==true){
			 $data_reporte=array("id_reporte_usr"=>generate_id("reporte_usuario","id_reporte_usr",true) , "nombre_usuario"=>$usuario , "accion"=>"Modificar Perfil de Usuario" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
			 add_data_dict("reporte_usuario",$data_reporte);
			 echo"<image src='images/correcto.png' width='120' height='120'/>";
		     echo "<div id='correcto_msg'><h2 id='correcto_text'>Informacion Modificada Satisfactoriamente</h2></div>";
		     echo"<br><a id='boton_acceptar' class='boton2' href='paginaprincipal.php' class='boton1'>Aceptar</a>
				  ";
		}
      }
      else{
		   echo "<div id='error_msg2'><h2 id='error_text'>Error de Conexion</h2></div>
	        <a href='paginaprincipal.php' class='boton1'>Volver</a>
	       ";
      }
   }
   else{
	    echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>
	     <a href='paginaprincipal.php' class='boton1'>Volver</a>
	    ";	  
   }
}
else{
	    echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>
	     <a href='paginaprincipal.php' class='boton1'>Volver</a>
	    ";	
}
?>

</div>

</center>

</body>

</html>