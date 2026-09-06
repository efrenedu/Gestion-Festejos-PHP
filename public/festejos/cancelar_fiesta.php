<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Cancelar Fiesta </title>
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

<div id="content2">

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
	$_SESSION['lastPage_user']="cancelar_fiesta.php"; 
  	
}
else{
	 header("location: loggin.php");
	 exit();
}

?>
  <h2 id="title1">Cancelar Fiesta</h2>
  <form action="send_cancelar_fiesta.php" method="post" id="formu" >
    <label class="label_login">Cliente:</label>
    <select onchange="get_fiestas()" id="cliente" name="cliente" size="1">
<?php
  /*set the list of Clients Requested a Party */
    require_once __DIR__."/../../private/festejos/db_config.php";
    echo "<option value=''>Elegir</option>";
		
    if(get_conexion()!="OK"){
		exit;
	}
	
    $join_data=array();
    $join_data["nombre"]=array("query_field"=>array("nombre","segundo_nombre","apellido","segundo_apellido"),"share_fields"=>array("field"=>"id_nombre","table_reference"=>"cliente"),"Conditions_join"=>null);
		  
    $data=get_data("cliente",["CI_cliente"],null,$join_data,true);
	if($data["status"]=="Error"){
		 exit;
	}
	$data=$data["message"];
	if(count($data)<=0){
		exit;
	}
    for($i=0;$i<count($data);$i++){			 
		$valor=$data[$i]["CI_cliente"];
		$texto="";
		$nombre=$data[0]["nombre"]." ";
		if($data[0]["segundo_nombre"]!=""){
			$nombre=$nombre.$data[0]["segundo_nombre"]." ";
		}
		$nombre=$nombre.$data[0]["apellido"];
		if($data_nombre[0]["segundo_apellido"]!=""){
			$nombre=$nombre." ".$data[0]["segundo_apellido"];
		}
		$texto=$nombre;
		$cond_data=array("conditions_Names"=>array("CI_cliente"),"conditions_Values"=>array($data[$i]["CI_cliente"]),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 

		$dat_fiest=get_data("fiesta",["CI_cliente","id_fiesta"],$cond_data,null,true);
		if($dat_fiest["status"]=="Error"){
			exit;
		}
		$dat_fiest=$dat_fiest["message"];
		if(count($dat_fiest)>0){
			echo "<option value='".$valor."'>".$texto."</option>";
		}			
	}
	 
    
?>
    </select>
    <br><br>
    <div id="data_client">
    </div>
    <br><br>
    <input type="hidden" id="fiestas_ids" name="fiestas_ids">
    <div id="error_form" style="display:none;background-color:red">
      <p style="color:white;">Por Favor Indique la Fiesta Agregar</p>
    </div>
    <div id="error_form2" style="display:none;background-color:red">
      <p style="color:white;">Por Favor Indique la Fiesta a Remover</p>
    </div>
    <div id="error_form3" style="display:none;background-color:red">
      <p style="color:white;">La Fiesta ya se ha Agregado a la Lista</p>
    </div>
    <div id="error_form4" style="display:none;background-color:red">
      <p style="color:white;">Por Favor Indique el Cliente</p>
    </div>
    <div id="error_form5" style="display:none;background-color:red">
      <p style="color:white;">Debe Agregar Almenos una Fiesta a la Lista</p>
    </div>
    <input type="hidden" name="Monto" id="Monto">
    <input type="button" class="boton_login" onclick="validar_cancelar_fiesta()" value="Continuar">
    <input type="button" class="boton_login"  onclick="reset_fiestasCancel()" value="Limpiar">
  </form>
</div>

<div id="Modal-confirm" class="ModalContainer" style="left:31%;width:40%;height:40%;">
   <div class="ModalX" onclick="closeModal_byName('Modal-confirm')"> X </div>
   <div class="ModalInner">
      <h2>Cancelar Fiestas?</h2>
	  <p> Esta Seguro que Quiere Cancelar las Fiestas Indicadas?</p>
      <input type="button" class="boton_login" onclick="ConfirmModal()" value="Si">
	  <input type="button" class="boton_login" onclick="closeModal_byName('Modal-confirm')" value="No">
   </div>
</div>
</center>
</body>
</html>