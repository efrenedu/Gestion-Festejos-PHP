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
require_once __DIR__."/../../private/festejos/db_config.php";  
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
	$last_page=$_SESSION['lastPage_user'];
    $_SESSION['lastPage_user']="modifica_user.php"; 
	if($last_page!="modificar_usuario.php"){
	    header("location: paginaprincipal.php");
	   exit();
	}
  
}
else{
	 header("location: loggin.php");
	 exit();
}
  
  
/*Process a Request of Modification from a User*/
if(count($_POST)<=0){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error Faltan Datos</h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
    exit;		
}
date_default_timezone_set('America/Caracas');
if(!isset($_POST["modificacion"])){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
    exit;	
}
$res_conex=get_conexion();
if($res_conex!="OK"){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_conex}</h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
    exit;	
}
$exito=true;
$modific=$_POST["modificacion"];
if($modific=="pass"){
	 if(!isset($_POST["pass1"])){
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	     echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
         exit; 
	 }
	 $new_pass=$_POST["pass1"];
	 $new_pass=password_hash($new_pass,PASSWORD_BCRYPT);
	 $cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($usuario),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		

     $res_update=update_data("usuario",array("contrasena"=>$new_pass),$cond_data,null);
     if($res_update["status"]=="Error"){
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
	     echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
         exit; 
		 
	 }
	
}
else if($modific=="preguntas"){
   $requerids=array("p1","p2","p3","p4","p5","p6","r1","r2","r3","r4","r5","r6");
   foreach($requerids as $param){
        if(!isset($_POST[$param])){
           echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	       echo "<div id='error_msg2'><h2 id='error_text'>Error Faltan Datos</h2></div>";
	       echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
           exit;    
       }  
   }
  $preguntas=array($_POST["p1"],$_POST["p2"],$_POST["p3"],$_POST["p4"],$_POST["p5"],$_POST["p6"]);
  $respuestas=array($_POST["r1"],$_POST["r2"],$_POST["r3"],$_POST["r4"],$_POST["r5"],$_POST["r6"]);
  if(isset($_POST["p7"]) && isset($_POST["r7"])){
		if($_POST["p7"]!="" && $_POST["r7"]!=""){
			$preguntas[]=$_POST["p7"];
			$respuestas[]=$_POST["r7"];
		}
  }
  if(isset($_POST["p8"]) && isset($_POST["r8"])){
		if($_POST["p8"]!="" && $_POST["r8"]!=""){
			$preguntas[]=$_POST["p8"];
			$respuestas[]=$_POST["r8"];
		}
  }
  $cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($usuario),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		

  $temp_dat=get_data("pregunta_secreta",["id_pregunta","numero",],$cond_data,null,true);
  if($temp_dat["status"]=="Error"){
        echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error {$temp_dat['message']}</h2></div>";
	    echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
        exit; 
  }
  $temp_dat=$temp_dat["message"];		   
  if(count($temp_dat)<=0){
       for($i=0;$i<count($preguntas);$i++){
            $id_preg=generate_id("pregunta_secreta","id_pregunta");
			if($id_preg["status"]=="Error"){
				echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	            echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_preg['message']}</h2></div>";
	            echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
                exit; 
			}
			$id_preg=$id_preg["message"];
			$d_preg=array("id_pregunta"=>$id_preg,"nombre_usuario"=>$usuario,"pregunta"=>$preguntas[$i],"respuesta"=>strtolower($respuestas[$i]),"numero"=>strval($i+1));
			$res_add=add_data("pregunta_secreta",$d_preg,true);
			if($res_add["status"]=="Error"){
				echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	            echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
	            echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
                exit;
			}
	  }
     
  }
  else{
       $resto=count($preguntas)-count($temp_dat);
	   $last=-1;
	   for($i=0;$i<count($preguntas);$i++){
			for($k=0;$k<count($temp_dat);$k++){
				if(intval($temp_dat[$k]["numero"])==($i+1)){
					$cond_data=array("conditions_Names"=>array("id_pregunta"),"conditions_Values"=>array($temp_dat[$k]["id_pregunta"]),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		

					$res_update=update_data("pregunta_secreta",array("pregunta"=>$preguntas[$i],"respuesta"=>strtolower($respuestas[$i]),"numero"=>strval($i+1)),$cond_data);
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
			   $id_preg=generate_id("pregunta_secreta","id_pregunta");
			   if($id_preg["status"]=="Error"){
				    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	                echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_preg['message']}</h2></div>";
	                echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
                    exit; 
			   }
			   $id_preg=$id_preg["message"];
			   $d_preg=array("id_pregunta"=>$id_preg,"nombre_usuario"=>$usuario,"pregunta"=>$preguntas[$j],"respuesta"=>strtolower($respuestas[$j]),"numero"=>strval($j+1));
			   $res_add=add_data("pregunta_secreta",$d_preg,true);
			   if($res_add["status"]=="Error"){
				   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	               echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
	               echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
                   exit;
			   }
			}
	  }
	  else{
			if($resto<0){
				$last=$last+1;
				for($j=0;$j<count($temp_dat);$j++){
					if(intval($temp_dat[$j]["numero"])>=($last+1)){
						$cond_data=array("conditions_Names"=>array("nombre_usuario","numero"),"conditions_Values"=>array($usuario,$temp_dat[$j]["numero"]),"condition_Types"=>array("and","and"),"conditions_Verify"=>array("=","="));	 	  		
                        delete_data("pregunta_secreta",$cond_data,null);
					}
				} 
			}
	  }

  }
					   		
}
else if($modific=="perfil"){
	 if(!isset($_FILES["foto"])){
		  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	      echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	      echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
          exit; 
	 }
	 copy($_FILES["foto"]["tmp_name"],$_FILES["foto"]["name"]);
     $nombre=$_FILES["foto"]["name"];
	 $posibles=array(".png",".jpg",".jpeg");
     $valid_file=false;
     $format="";
     foreach($posibles as $target){
	    $size=strlen($target);
	    if(substr($nombre,-$size,$size)==$target){
		    $valid_file=true;
		    $format=$target;
		    break;
	    } 
     }
     if($valid_file==false){
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error Solo se Puede Subir un Archiv de Imagen PNG, JPG</h2></div>";
	    echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
	    if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$nombre)){
	         unlink(__DIR__.DIRECTORY_SEPARATOR.$nombre);
        }
	    exit;
     }
     $new_name="{$usuario}-Image{$format}";
     $dir="images".DIRECTORY_SEPARATOR.$new_name;
     move_uploaded_file($_FILES["foto"]["tmp_name"],$dir);
	 if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$nombre)){
		unlink(__DIR__.DIRECTORY_SEPARATOR.$nombre);
	 }
	 $cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($usuario),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  		
	 $res_update=update_data("usuario",array("foto"=>$new_name),$cond_data);
	 if($res_update["status"]=="Error"){
		  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	      echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
	      echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	  
          exit;  
	 }
	  
	
}

$id_report=generate_id("reporte_usuario","id_reporte_usr");
if($id_report["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error: {$id_report['message']}</h2></div>";
	echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	exit;
}
$id_report=$id_report["message"];
$data_reporte=array("id_reporte_usr"=>$id_report, "nombre_usuario"=>$usuario , "accion"=>"Modificar Perfil de Usuario" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
$res_add=add_data("reporte_usuario",$data_reporte,true);
if($res_add["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_add['message']}</h2></div>";
	echo "<a class='boton2' href='gestion_usuarios.php' >aceptar</a>";
	exit;
}
echo"<image src='images/correcto.png' width='120' height='120'/>";
echo "<div id='correcto_msg'><h2 id='correcto_text'>Informacion Modificada Satisfactoriamente</h2></div>";
echo"<br><a id='boton_acceptar' class='boton2' href='paginaprincipal.php' class='boton1'>Aceptar</a> ";

     

?>

</div>

</center>

</body>

</html>