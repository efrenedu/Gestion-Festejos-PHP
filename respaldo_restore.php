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
require "conexion_bd.php";
session_start();
$usuario="";
if(count($_SESSION)>0){
  $usuario=$_SESSION['username'];
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
	  $last_page= $_SESSION['lastPage_user'];
      $_SESSION['lastPage_user']="respaldo_restore_bd.php";    
      if($last_page!="respaldar_bd.php"){
		  header("location: paginaprincipal.php"); 
		  exit();
	  }
  }
}
else{
    header("location: loggin.php"); 
	exit();
}

/*Process the Request to Save or Restore BD data*/
if(count($_POST)>0){
	if(isset($_POST["accion"])){
	     if($_POST["accion"]=="respaldo"){			
			if(validar_conexion()){				
				$ruta_respaldo=__DIR__.DIRECTORY_SEPARATOR."respaldos".DIRECTORY_SEPARATOR;
			    for ($j=0;$j<count($tablas_list);$j++){
				    $tabl=$tablas_list[$j];
					if(file_exists($ruta_respaldo.$tabl.".csv")){
						unlink($ruta_respaldo.$tabl.".csv");
					}
				    $f_tabla=fopen($ruta_respaldo.$tabl.".csv","w+");
					$data_tabla=get_data($tabl,$fields_tablas[$j],count($fields_tablas[$j]),-1,-1);
					$str_temp="";
					if(count($data_tabla)>0){
					     foreach ($data_tabla as $dat_t){
						     for ($i=0;$i<count($dat_t);$i++){
							     $str_temp=$str_temp.$dat_t[$i];
							     if($i<count($dat_t)-1){
							  	    $str_temp=$str_temp.";";
							    }
						    }
							$str_temp=$str_temp.";\n";
					    }
					    file_put_contents($ruta_respaldo.$tabl.".csv",$str_temp);
					    fclose($f_tabla);
					}
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
					 if(count($files_contains)>0){
				        $res=$zip->close();
				         if($res){
							$data_reporte=array("id_reporte_usr"=>generate_id("reporte_usuario","id_reporte_usr",true) , "nombre_usuario"=>$usuario , "accion"=>"Respaldar BD" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
			                add_data_dict("reporte_usuario",$data_reporte);
	                         echo"<div>
							       <h3>El Respaldo esta listo para Descargarse</h3>
								   <a href='".$zip_server_name."'>Download</a>
							      </div>";
				         }
				         else{
						    echo "<div id='error_msg'><h2 id='error_text'>Error Creando Respaldo</h2></div>";
                            echo "<a class='boton1' href='loggin.php'>Volver</a>";
				        }
					 }
					 else{
						  echo "<div id='error_msg'><h2 id='error_text'>Error Creando Respaldo </h2></div>";
                          echo "<a class='boton1' href='loggin.php'>Volver</a>";
					 } 
			    }
			    else{
				   echo "<div id='error_msg'><h2 id='error_text'>Error Accediendo al Servidor</h2></div>";
                   echo "<a class='boton1' href='loggin.php'>Volver</a>";
			    }
		    }
			else{
				 echo "<div id='error_msg'><h2 id='error_text'>Error de Conexion</h2></div>";
                 echo "<a class='boton1' href='loggin.php'>Volver</a>";
	             echo "</div>";
			}
		 }
		 else{
			 if(count($_FILES)>0){
				 if(validar_conexion()){
				   copy($_FILES["source"]["tmp_name"],$_FILES["source"]["name"]);
                   $nombre=$_FILES["source"]["name"];
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
						activate_foraneos(false);
						for ($j=0;$j<count($tablas_list);$j++){
				              $tabl=$tablas_list[$j];
							  $f_name=$ruta_files.DIRECTORY_SEPARATOR.$tabl.".csv";
				              if(file_exists($f_name)==false){
								  $error=true;
							  }
							  else{
								  $f=fopen($f_name,"r+");
								  $permiso_open=true;
								  clearstatcache();
								  if(filesize($f_name)==0){$permiso_open=false;}
								  $rows=array();
								  $temp_cad="";
								  if($permiso_open==true){
								     $temp_data=fread($f,filesize($f_name));
									 for ($i=0;$i<strlen($temp_data);$i++){
									     $last_char="";
										 if($temp_cad!=""){
											 $last_char=$temp_cad[strlen($temp_cad)-1];
										 }
									     if($temp_data[$i]=="\n" && $last_char==";"){
											 $temp_cad=substr($temp_cad, 0, -1);
										     $rows[]=$temp_cad;
										     $temp_cad="";
									     }
								        else{
										     $temp_cad=$temp_cad.$temp_data[$i];
									    }
								     }
								  }
								  reset_tabla($tabl);
								  if(count($rows)>0){
								      foreach($rows as $tabla_row){
									      $df_tabla=explode(";",$tabla_row);
									      add_data($tabl,$df_tabla,count($df_tabla));
								      }
								  }
								  fclose($f);
								  unlink("respaldos".DIRECTORY_SEPARATOR.$tabl.".csv");
							  }
				        }
						activate_foraneos(true);
						$data_reporte=array("id_reporte_usr"=>generate_id("reporte_usuario","id_reporte_usr",true) , "nombre_usuario"=>$usuario , "accion"=>"Restaurar BD" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
			            add_data_dict("reporte_usuario",$data_reporte);
						if($error==false){
					        echo"<h2 id='title1'>Restauracion de Base de Datos Realizada Exitosamente</h2>
                            <br><a href='paginaprincipal.php' class='boton1'>Aceptar</a>
				             ";
						}
						else{
							 echo "<div id='error_msg'><h2 id='error_text'>Error , Restauracion Incompleta Faltaron Algunos Archivos del Respaldo</h2></div>";
                             echo "<a class='boton1' href='loggin.php'>Volver</a>";
	                         echo "</div>"; 
						}
				   }
				   else{
					  echo "<div id='error_msg'><h2 id='error_text'>Error Leyendo Respaldo</h2></div>";
                      echo "<a class='boton1' href='loggin.php'>Volver</a>";
	                  echo "</div>"; 
				   } 
				   if(file_exists($nombre_zip)){
						unlink($nombre_zip);
				   }
				 }
				 else{
					 echo "<div id='error_msg'><h2 id='error_text'>Error de Conexion</h2></div>";	                
                     echo "<a class='boton1' href='loggin.php'>Volver</a>";
	                 echo "</div>"; 
				 }
			 }
			 else{  
			     echo "<div id='error_msg'><h2 id='error_text'>Error de Data</h2></div>";
                 echo "<a class='boton1' href='loggin.php'>Volver</a>";
	             echo "</div>";
			 }
		 }
	}
	else{
	   echo "<div id='error_msg'><h2 id='error_text'>Error de Data2</h2></div>";
       echo "<a class='boton1' href='loggin.php'>Volver</a>";
	   echo "</div>";
	}
}
else{
	
	 echo "<div id='error_msg'><h2 id='error_text'>Error de Data</h2></div>";
     echo "<a class='boton1' href='loggin.php'>Volver</a>";
	 echo "</div>";
}

?>
</div>

</center>

</body>

</html>