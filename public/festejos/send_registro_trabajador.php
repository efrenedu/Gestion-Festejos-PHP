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
  require "conexion_bd.php";
  session_start();
  if(count($_SESSION)>0){
    $usuario = $_SESSION['username'];
    $nivel=$_SESSION['acceso_user'];
    if (!isset($usuario)){
	   header("location: loggin.php"); 
       exit();	   
    }
	else{
		$last_page=$_SESSION['lastPage_user'];
        $_SESSION['lastPage_user']="send_registro_trabajador.php"; 
        if($last_page!="registrar_trabajador.php"){
		   header("location: paginaprincipal.php"); 
		   exit();
		}
	}
	if($nivel!="administrador"){
	   header("location: paginaprincipal.php"); 
	   exit();
    } 
  }
  else{  
	 header("location: loggin.php");
     exit();	 
  }
  
  /*Process the Request for Register the Working*/
  if(count($_POST)>0 ){
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
    if ((isset($ci ) && isset($turno) &&isset($nomb) && isset($apellid) && isset($cargo) && isset($edad) && isset( $nivel_academico) && isset($telefonos) && isset($accion) && isset($estatus))==false) {
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>error datos invalidos</h2></div>";
		echo "<a class='boton2' href='registrar_trabajador.php' >aceptar</a>"; 
   }
   else{
       if(validar_conexion()){
		  if($accion!="Registrar" || (id_exist("trabajador","CI_trabaj", $ci)==false &&  $accion=="Registrar")==true){
			$dat_name=[];
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
			$dat_name["id_nombre"]=strval(generate_id("nombre","id_nombre",true));
			$data_f=["CI_trabaj"=>$ci ,"id_nombre"=> $dat_name["id_nombre"],"cargo"=>$cargo ,"turno"=>$turno ,"nivel_academico"=>$nivel_academico ,"edad"=>$edad ,"estatus"=>$estatus,"fecha_ingreso"=>$fecha];
			if($accion=="Registrar"){
			   add_data_dict("nombre",$dat_name);
			   add_data_dict("trabajador",$data_f);
			   $data_reporte=["id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)),"nombre_usuario"=>$usuario,"accion"=>"Registrar Trabajador","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
			   add_data_dict("reporte_usuario",$data_reporte);
			}
			else{
				$id_name="";
				$old_name=get_data_dict("trabajador",["id_nombre"],1,["CI_trabaj"],[$ci]);
				if(count($old_name)>0){
					$id_name=$old_name[0]["id_nombre"];
					update_data("nombre",["nombre","segundo_nombre","apellido","segundo_apellido"],[$dat_name["nombre"],$dat_name["segundo_nombre"],$dat_name["apellido"],$dat_name["segundo_apellido"]],4,["id_nombre"],[$id_name]);
					update_data("trabajador",["estatus","cargo","turno","nivel_academico","edad"],[$estatus,$cargo ,$turno,$nivel_academico,$edad],5,["CI_trabaj"],[$ci]);
				    delete_data("telefono",["CI_trabaj"],[$ci]);
				}
				$data_reporte=["id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)),"nombre_usuario"=>$usuario,"accion"=>"Actualizar Trabajador","fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s"))];
			    add_data_dict("reporte_usuario",$data_reporte);
			}
			$rows_telefs=explode(";",$telefonos);
			for($i=0;$i<count($rows_telefs);$i++){
				$columns=explode(",",$rows_telefs[$i]);
				if(count($columns)==2){
				  $data_telefs=["numero_telefono"=>$columns[0],"CI_trabaj"=>$ci,"principal"=>$columns[1]];
				  add_data_dict("telefono",$data_telefs);
				}
			}
		    echo "<image src='images/correcto.png' width='120' height='120'/>";
			if($accion=="Registrar"){
		        echo "<div id='correcto_msg'><h2 id='correcto_text'>Trabajador Registrado Satisfactoriamente</h2></div>";
			}
			else{
				  echo "<div id='correcto_msg'><h2 id='correcto_text'>Trabajador Actualizado Satisfactoriamente</h2></div>";
	
			}
 		    echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
		  }
		  else{
			  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		      echo "<div id='error_msg2'><h2 id='error_text'>Cedula Ya Registrada</h2></div>";
		      echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
		  }
	   }
	   else{
		  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		  echo "<div id='error_msg2'><h2 id='error_text'>Fallo al Conectar</h2></div>";
		  echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>";
	   }
     }
  }
  else{
	 	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
		echo "<a class='boton2' href='registrar_trabajador.php' >Aceptar</a>"; 
  }

?>

</div>

</center>

</body>

</html>