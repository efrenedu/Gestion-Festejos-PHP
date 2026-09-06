<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Registro de Usuario </title>
	<link rel="stylesheet" type="text/css" href="style_session.css">
	<script type="text/javascript" src="jquery-3.1.1.min.js"></script>
	<script type="text/javascript" src="script_festejos.js"></script>
</head>
<body>
<center>
<br>
<div id="titleBoc">
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

  /*Verify No Eixst Illegal Access*/
require_once __DIR__."/../../private/festejos/db_config.php";  
require_once __DIR__."/../../private/festejos/jwt.php";
session_start();
$usuario_actual="";
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
		header("location:paginaprincipal.php");
	}
	$last_page=$_SESSION['lastPage_user'];
    $_SESSION['lastPage_user']="send_registro_user.php";
	if($last_page!="registro_usuarios.php"){
		 header("location: paginaprincipal.php"); 
		 exit();
	}
      
}
else{
	 header("location: loggin.php");
	 exit();
}

  
  /*Process the request to register the user*/
  if(count($_POST)<=0){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Faltan Datos</h2></div>";
	 echo "<a class='boton2' href='registro_usuarios.php' >Aceptar</a>";	  
     exit;
  }
  if( !isset($_POST['user_name']) || !isset($_POST['pass1']) || !isset($_POST['permiso']) || !isset($_POST["trabajador"])) {
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Datos Invalidos</h2></div>";
		echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
        exit;
  }
   date_default_timezone_set('America/Caracas');
   $usuario=strtolower($_POST['user_name']);
   $pass=$_POST['pass1'];
   $permiso=$_POST['permiso'];
   if($permiso=="administrador"){
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Permiso del Usuario a Registrar Invalido</h2></div>";
		echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
        exit; 
   }
   $trabajador=$_POST['trabajador'];
   $params_required=array("p1","p2","p3","p4","p5","p6","r1","r2","r3","r4","r5","r6");
   foreach ($params_required as $param){
         if(!isset($_POST[$param])){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		    echo "<div id='error_msg2'><h2 id='error_text'>Faltan Datos</h2></div>";
		    echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
            exit; 
		 }
   }	   
   $res_conex=get_conexion();
   if($res_conex!="OK"){
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>{$res_conex}</h2></div>";
		echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
        exit; 
   }
   $exist=id_exist("usuario","nombre_usuario",$usuario);
   if($exist["status"]=="Error"){
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>{$exist['message']}</h2></div>";
		echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
        exit; 
   }
   $exist=$exist["message"];
   if($exist!="False"){
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Usuario ya Registrado</h2></div>";
		echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
        exit;
   }
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
	 $id_intento=generate_id("intentos_usuario","id_intento");
	 if($id_intento["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$id_intento['message']}</h2></div>";
		echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
        exit;
	 }
	 $id_intento=$id_intento["message"];
	 $dat_intentos=array("id_intento"=>$id_intento,"num_intentos"=>"0","last_fecha"=>"...","last_hora"=>"...");
	 add_data("intentos_usuario",$dat_intentos,true);
	 $pass=password_hash($pass,PASSWORD_BCRYPT);
	 $dat=array("nombre_usuario"=>$usuario ,"contrasena"=>$pass ,"permiso"=>$permiso ,"bloqueado"=>"false","foto"=>"..." , "id_intento"=>$dat_intentos["id_intento"],"CI_trabaj"=>$trabajador);
	 add_data("usuario",$dat,true);
	 for($i=0;$i<count($preguntas);$i++){
		$id_preg=generate_id("pregunta_secreta","id_pregunta");
	    if($id_preg["status"]=="Error"){
		   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		   echo "<div id='error_msg2'><h2 id='error_text'>Error: {$id_preg['message']}</h2></div>";
		   echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
           exit;
	    }
	    $id_preg=$id_preg["message"];
		$d_preg=array("id_pregunta"=>$id_preg ,"nombre_usuario"=>$dat["nombre_usuario"] , "pregunta"=>$preguntas[$i] ,"respuesta"=>strtolower($respuestas[$i]),"numero"=>strval($i+1));
		add_data("pregunta_secreta",$d_preg,true);
	}
	$id_report=generate_id("reporte_usuario","id_reporte_usr");
    if($id_report["status"]=="Error"){
		   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		   echo "<div id='error_msg2'><h2 id='error_text'>Error: {$id_report['message']}</h2></div>";
		   echo "<a class='boton2' href='registro_usuarios.php' >aceptar</a>";
           exit;
	}
	$id_report=$id_report["message"];
	$dat_reporte=array("id_reporte_usr"=>$id_report , "nombre_usuario"=>$usuario_actual , "accion"=>"Registrar Usuario" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
	add_data("reporte_usuario",$dat_reporte,true,true);
	echo "<image src='images/correcto.png' width='120' height='120'/>";
	echo "<div id='correcto_msg'><h2 id='correcto_text'>Usuario Registrado Satisfactoriamente</h2></div>";
	echo "<a id='boton_acceptar' class='boton2' href='gestion_usuarios.php' >Aceptar</a>";
			   

?>

</div>

</center>

</body>

</html>