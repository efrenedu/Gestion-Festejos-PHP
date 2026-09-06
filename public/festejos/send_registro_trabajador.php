<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Recuperar contraseña </title>
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
  require_once __DIR__."/../../private/festejos/db_config.php";
  require_once __DIR__."/../../private/festejos/jwt.php";
  session_start();
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
	$nivel=$res["Message"]["Acceso"];
	if($nivel!="administrador"){
	   header("location: paginaprincipal.php"); 
	   exit();
    } 
	$_SESSION['lastPage_user']="registrar_trabajador.php"; 
  	
  }
  else{
	 header("location: loggin.php");
	 exit();
  }
  
  
  /*Process the Request for Register the Working*/
  if(count($_POST)<=0){
	  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	  echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	  echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>"; 
      exit;
  }
  $list_params=array("cedula","nombre","apellido","cargo","edad","telefonos_list","nivel_academico","turno","accionRegistro","estatus");
  foreach($list_params as $param){
	  if(!isset($_POST[$param])){
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	     echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>"; 
         exit; 
	  }
  }
  
  $ci = $_POST['cedula'];
  $nomb=$_POST['nombre'];
  $apellid=$_POST['apellido'];
  $cargo=$_POST['cargo'];
  $edad=$_POST['edad'];
  $telefonos=$_POST['telefonos_list'];
  $nivel_academico=$_POST['nivel_academico'];
  $turno=$_POST['turno'];
  $accion=$_POST['accionRegistro'];
  $estatus=$_POST['estatus'];
  date_default_timezone_set('America/Caracas');
  $res_conex=get_conexion();
  if($res_conex!="OK"){
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error: {$res_conex}</h2></div>";
	    echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
	    exit;
  }
   $exist_id=id_exist("trabajador","CI_trabaj", $ci);
   if($exist_id["status"]=="Error"){
	  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	  echo "<div id='error_msg2'><h2 id='error_text'>Fallo al Verificar Trabajador</h2></div>";
	  echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
	  exit; 
   }
   if($accion=="Registrar" && $exist_id["message"]=="True"){
	   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	   echo "<div id='error_msg2'><h2 id='error_text'>El trabajador ya se ha Registrado</h2></div>";
	   echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
	   exit; 
   }
   else if($accion!="Registrar" && $exist_id["message"]=="False"){
	   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	   echo "<div id='error_msg2'><h2 id='error_text'>Trabajador Inexistente</h2></div>";
	   echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
	   exit;  
   }
  	$dat_name=array("id_nombre"=>"","nombre"=>"","segundo_nombre"=>"","apellido"=>"","segundo_apellido"=>"");
	$nomb=ucwords(strtolower($nomb));
	$apellid=ucwords(strtolower($apellid));
	$nomb_f=explode(" ",$nomb);
	$apellid_f=explode(" ",$apellid);
	if(count($nomb_f)==2){
		$dat_name["nombre"]=$nomb_f[0];
		$dat_name["segundo_nombre"]=$nomb_f[1];	
	}else{
		$dat_name["nombre"]=$nomb;
		$dat_name["segundo_nombre"]="";
	}
	if(count($apellid_f)==2){
		$dat_name["apellido"]=$apellid_f[0];
		$dat_name["segundo_apellido"]=$apellid_f[1];
	}else{
		$dat_name["apellido"]=$apellid;
		$dat_name["segundo_apellido"]="";
	}
	$fecha=strval(date("d-m-Y"));
	$id_name=generate_id("nombre","id_nombre");
		if($id_name["status"]=="Error"){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	        echo "<div id='error_msg2'><h2 id='error_text'>Error: {$id_name['message']}</h2></div>";
	        echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
	        exit; 
	}
	$dat_name["id_nombre"]=$id_name["message"];
	$data_f=["CI_trabaj"=>$ci ,"id_nombre"=> $dat_name["id_nombre"],"cargo"=>$cargo ,"turno"=>$turno ,"nivel_academico"=>$nivel_academico ,"edad"=>$edad ,"estatus"=>$estatus,"fecha_ingreso"=>$fecha];
	if($accion=="Registrar"){
		
		add_data("nombre",$dat_name,true);
		add_data("trabajador",$data_f,true);
		$rows_telefs=explode(";",$telefonos);
		for($i=0;$i<count($rows_telefs);$i++){
			$columns=explode(",",$rows_telefs[$i]);
			if(count($columns)==2){
				  $data_telefs=["numero_telefono"=>$columns[0],"CI_trabaj"=>$ci,"principal"=>$columns[1]];
				  add_data("telefono",$data_telefs,true);
			}
		}
		$id_report=generate_id("reporte_usuario","id_reporte_usr");
		if($id_report["status"]=="Error"){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	        echo "<div id='error_msg2'><h2 id='error_text'>Error: {$id_report['message']}</h2></div>";
	        echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
	        exit; 
		}
		$data_reporte=["id_reporte_usr"=>$id_name["message"],"nombre_usuario"=>$usuario,"accion"=>"Registrar Trabajador","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
		add_data("reporte_usuario",$data_reporte,true,true);
		echo "<image src='images/correcto.png' width='120' height='120'/>";
		echo "<div id='correcto_msg'><h2 id='correcto_text'>Trabajador Registrado Satisfactoriamente</h2></div>";
	    echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
	}
	else{
		$id_name="";
		$join_data["nombre"]=array("query_field"=>array("nombre"=>$dat_name["nombre"],"segundo_nombre"=>$dat_name["segundo_nombre"],"apellido"=>$dat_name["apellido"],"segundo_apellido"=>$dat_name["segundo_apellido"]),"share_fields"=>array("field"=>"id_nombre","table_reference"=>"trabajador"),"Conditions_join"=>null);         
		$cond_data=array("conditions_Names"=>array("CI_trabaj"),"conditions_Values"=>array($ci),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	  	
  		update_data("trabajador",["estatus"=>$estatus,"cargo"=>$cargo,"turno"=>$turno,"nivel_academico"=>$nivel_academico,"edad"=>$edad],$cond_data,$join_data);
		delete_data("telefono",$cond_data);
		$rows_telefs=explode(";",$telefonos);
		for($i=0;$i<count($rows_telefs);$i++){
			$columns=explode(",",$rows_telefs[$i]);
			if(count($columns)==2){
				  $data_telefs=["numero_telefono"=>$columns[0],"CI_trabaj"=>$ci,"principal"=>$columns[1]];
				  add_data("telefono",$data_telefs,true);
			}
		}		
		
		$id_report=generate_id("reporte_usuario","id_reporte_usr");
		if($id_report["status"]=="Error"){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	        echo "<div id='error_msg2'><h2 id='error_text'>Error:{$id_report['message']}</h2></div>";
	        echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
	        exit; 
		}
		$data_reporte=["id_reporte_usr"=>$id_report["message"],"nombre_usuario"=>$usuario,"accion"=>"Actualizar Trabajador","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
		add_data("reporte_usuario",$data_reporte,true,true);
		echo "<image src='images/correcto.png' width='120' height='120'/>";
		echo "<div id='correcto_msg'><h2 id='correcto_text'>Trabajador Actualizado Satisfactoriamente</h2></div>";
 		echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
	}

?>

</div>

</center>

</body>

</html>