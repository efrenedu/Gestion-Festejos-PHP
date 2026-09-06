<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Respaldar BD</title>	
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
require_once __DIR__."/../../private/festejos/db_config.php";
require_once __DIR__."/../../private/festejos/jwt.php";
/*Verify No Exist Illegal Access*/
$usuario="";
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
	     echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos del Token del Servidor</h2></div>";
	     echo "<image id='error_img' src='images/incorrecto.png' width='150' height='150'/><br><br>";
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
	$usuario=$res["Message"]["Id_usr"];
	if($permiso!="administrador"){
		header("location: paginaprincipal.php");
        exit();
	}
	$last_page= $_SESSION['lastPage_user'];
    $_SESSION['lastPage_user']="respaldo_restore_bd.php";    
    if($last_page!="respaldar_bd.php"){
		  header("location: paginaprincipal.php"); 
		  exit();
    }
}
else{
	 header("location: loggin.php");
	 exit();
}
if(count($_POST)<=0){
	 echo "<div id='error_msg'><h2 id='error_text'>Error Faltan Datos</h2></div>";
	 echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
     echo "<a class='boton1' href='paginaprincipal.php'>Volver</a>";
	 echo "</div>";
	 exit;
}
if(!isset($_POST["accion"])){
	 echo "<div id='error_msg'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	 echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
     echo "<a class='boton1' href='paginaprincipal.php'>Volver</a>";
	 echo "</div>";
	 exit;
}
/*Process the Request to Save or Restore BD data*/
$acc=$_POST["accion"];
$res_conex=get_conexion();
if($res_conex!="OK"){
	 echo "<div id='error_msg'><h2 id='error_text'>Error {$res_conex}</h2></div>";
	 echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
     echo "<a class='boton1' href='paginaprincipal.php'>Volver</a>";
	 echo "</div>";
	 exit;
}

if($acc=="respaldo"){			
	$ruta_respaldo=__DIR__.DIRECTORY_SEPARATOR."respaldos".DIRECTORY_SEPARATOR;
	$dat_respaldo=respald_bd();
	if($dat_respaldo["status"]=="Error"){
		echo "<div id='error_msg'><h2 id='error_text'>Error {$dat_respaldo['message']}</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='paginaprincipal.php'>Volver</a>";
	    echo "</div>";
	    exit;
	}
	$dat_respaldo=$dat_respaldo["data"];
	
	foreach ($dat_respaldo as $tabl=>$dat_table){
		if(file_exists($ruta_respaldo.$tabl.".csv")){
			unlink($ruta_respaldo.$tabl.".csv");
		}
		$f_tabla=fopen($ruta_respaldo.$tabl.".csv","w+");
		$lines="";
		foreach($dat_table as $row){
           $line_row="";
		   foreach($row as $field_row=>$value_field){
			   $line_row=$line_row.$value_field.";";
		   }
		   $line_row=$line_row."\n";
		   $lines=$lines.$line_row;
		}
       	file_put_contents($ruta_respaldo.$tabl.".csv",$lines);
		fclose($f_tabla);	
	}
	$fecha=strval(date("d-m-Y"));
	$zip=new ZipArchive();
	$zip_server_name="respaldos_zips".DIRECTORY_SEPARATOR."respaldo-".$fecha.".zip";
	$nombre_zip=__DIR__.DIRECTORY_SEPARATOR.$zip_server_name;
	$ruta_zip=__DIR__;
	$files_contains=array();
	if($zip->open($nombre_zip,ZipArchive::CREATE |ZipArchive::OVERWRITE)){
		$archivos=new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.DIRECTORY_SEPARATOR."respaldos"),RecursiveIteratorIterator::LEAVES_ONLY);
			foreach ($archivos as $f){
				if($f->isDir()){
					 continue;
				}
				$ruta_abs=$f->getRealPath();
				$nombre_file=basename($ruta_abs);
				$zip->addFile($ruta_abs,$nombre_file);
				$files_contains[]="respaldos".DIRECTORY_SEPARATOR.$nombre_file;  
			}
			if(count($files_contains)<=0){
				 echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos del Token del Servidor</h2></div>";
	             echo "<div id='error_msg'><h2 id='error_text'>Error Creando Respaldo </h2></div>";
                 echo "<a class='boton1' href='loggin.php'>Volver</a>";
		         exit;
			}
			$res=$zip->close();
			if($res){
				  $id_report=generate_id("reporte_usuario","id_reporte_usr");
				  if($id_report["status"]!="Error"){
					$data_reporte=array("id_reporte_usr"=>$id_report["message"] , "nombre_usuario"=>$usuario , "accion"=>"Respaldar BD" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
					add_data("reporte_usuario",$data_reporte,true,true); 
				  }
				  echo"<div>
						 <h3>El Respaldo esta listo para Descargarse</h3>
						<a href='".$zip_server_name."'>Download</a>
						</div>";
			}
			else{
				echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos del Token del Servidor</h2></div>";
				echo "<div id='error_msg'><h2 id='error_text'>Error Creando Respaldo</h2></div>";
                echo "<a class='boton1' href='loggin.php'>Volver</a>";
			}
			
	}
	else{
		echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos del Token del Servidor</h2></div>";       
		echo "<div id='error_msg'><h2 id='error_text'>Error Accediendo al Servidor</h2></div>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
	}
		   
}
else{
	if(count($_FILES)<=0){
		 echo "<div id='error_msg'><h2 id='error_text'>Faltan Datos</h2></div>";
	     echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
         echo "<a class='boton1' href='paginaprincipal.php'>Volver</a>";
	     echo "</div>";
	     exit;
		
	}
	if(!isset($_FILES["source"])){
		 echo "<div id='error_msg'><h2 id='error_text'>Datos Invalidos</h2></div>";
	     echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
         echo "<a class='boton1' href='paginaprincipal.php'>Volver</a>";
	     echo "</div>";
	     exit;
		
	}
	copy($_FILES["source"]["tmp_name"],$_FILES["source"]["name"]);
    $nombre=$_FILES["source"]["name"];
	$posibles=array(".zip",".rar");
	$valid_format=false;
	foreach($posibles as $posible_target){
		$size=strlen($posible_target);
	    if(substr($nombre,-$size,$size)==$posible_target){
		    $valid_format=true;
		    break;
	    }
	}
	if($valid_format==false){
	   echo "<div id='error_msg'><h2 id='error_text'>El Archivo de Respaldo solo puede ser ZIP o RAR</h2></div>";
	   echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
       echo "<a class='boton1' href='paginaprincipal.php'>Volver</a>";
	   echo "</div>";
	     
  	   if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$nombre)){
		     unlink(__DIR__.DIRECTORY_SEPARATOR.$nombre);
	   }
	   exit;
	}
	
    $dir="respaldos_zips".DIRECTORY_SEPARATOR.$nombre;
    move_uploaded_file($_FILES["source"]["tmp_name"],$dir);
	if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$nombre)){
		unlink(__DIR__.DIRECTORY_SEPARATOR.$nombre);
	}
	
	$zip=new ZipArchive();
	$zip_server_name=$dir;
	$nombre_zip=__DIR__.DIRECTORY_SEPARATOR.$dir;
	$ruta_files=__DIR__.DIRECTORY_SEPARATOR."respaldos";
	$files_contains=array();
	if($zip->open($nombre_zip)){
		$zip->extractTo($ruta_files);
		$zip->close();
		$error=false;
		$archivos=new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.DIRECTORY_SEPARATOR."respaldos"),RecursiveIteratorIterator::LEAVES_ONLY);
		$file_names=array();
		foreach ($archivos as $f){
			if($f->isDir()){
				continue;
			}
			$ruta_abs=$f->getRealPath();
			$nombre_file=basename($ruta_abs);
			$file_names[]=$nombre_file;
		}			  
		foreach ($file_names as $target_name){
			$target_path=$ruta_files.DIRECTORY_SEPARATOR.$target_name."csv";
			if(file_exists($target_path)){
				unlink($target_path);
			}
	    }  
	   if(file_exists($nombre_zip)){
	         unlink($nombre_zip);
	   }
	}			
			 
}
	



?>
</div>

</center>

</body>

</html>