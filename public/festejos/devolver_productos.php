<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Devolver Productos</title>
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
/*Verify no Exist Illegal Access from User*/
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
	$acceso=$res["Message"]["Acceso"];
	if($acceso=="Visitante"){
		header("location:paginaprincipal.php");
	}
	 $_SESSION['lastPage_user']="devolver_productos.php"; 
}
else{
	 header("location: loggin.php");
	 exit();
}



?>

  <h2 id='title1'>Devolver Productos o Bienes</h2>
  <form onreset="reset_deuda()" id="formu" action='realizar_devolucion.php' method='post'>
    <label class="label_login">Cliente:</label>
    <select onchange="search_prodClient()" id="cliente" name="cliente" size="1">
<?php
    /*set the list of clients with rented products*/
    require_once __DIR__."/../../private/festejos/db_config.php";
    echo "<option value=''>Elegir</option>";	
    
	if(get_conexion()!="OK"){exit;}
	$cond_data=array("conditions_Names"=>array("estatus"),"conditions_Values"=>array("deuda"),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
    $join_data=array();
	$join_data["nombre"]=array("query_field"=>array("nombre","apellido","segundo_nombre","segundo_apellido"),"share_fields"=>array("field"=>"id_nombre","table_reference"=>"cliente"),"Conditions_join"=>null);
	
	$data=get_data("cliente",["CI_cliente"],$cond_data,$join_data,true);
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
		if($data[0]["segundo_apellido"]!=""){
			$nombre=$nombre." ".$data[0]["segundo_apellido"];
		}
		$texto=$nombre; 
		echo "<option value='".$valor."'>".$texto."</option>"; 
	}
	 
  
?>
    </select>
    <br><br>
    <div id="prod_list" style="display:none">
      <h3> Informacion de la Devolucion</h3>
      <label class="label_login" id="label_fecha">Fecha Devolucion:</label>
      <label class="label_login" style="margin-left:10%" id="label_retraso">Dias de Retraso:0 dias</label><br><br><br>
      <label class="label_login">Productos a Devolver</label>
      <label class="label_login" style="margin-left:15%">Unidades Dañadas o Faltantes</label>
      <br><br>
      <div id="box1">
      </div>
    </div>
    <input type="hidden" id="count_prods" name="count_prods" value="">
    <br>
    <div id="deuda_infoBox" style="display:none">
      <h3>Informacion de la Deuda</h3>
      <div id="deuda_box" style="display:inline-block">
        <label class="label_login" >Deudas</label><br>
        <label class="label_login" id="deuda_retraso_label">Por Retraso: 0$</label><br>
        <label class="label_login" id="deuda_damage_label">Por Daños a los Productos: 0$</label><br>
        <label class="label_login" id="deuda_total_label">Total: 0$</label><br>
        <input type="hidden" id="deuda_retraso" name="deuda_retraso">
	    <input type="hidden" id="deuda_damage" name="deuda_damage">
	    <input type="hidden" id="deuda_total" name="deuda_total"> 
      </div>
      <div id="metodo_pago_box" style="display:inline-block;margin-left:10%">
        <label class="label_login">Metodo de Pago</label><br>
        <input type="radio" value="Efectivo" name="metodo_pago" checked>Efectivo<br>
        <input type="radio" value="Tarjeta de Credito" name="metodo_pago">Tarjeta de Credito<br>
        <input type="radio" value="Transferencia" name="metodo_pago">Transferencia<br>
      </div>
    </div>
    <div id="Modal-confirm" class="ModalContainer" style="left:31%;width:40%;height:40%;">
      <div class="ModalX" onclick="closeModal_byName('Modal-confirm')"> X </div> 
      <div class="ModalInner">
        <h2>Devolver Productos?</h2>
	    <p> Esta Seguro que Quiere Devolver los Productos?</p>
        <input type="button" class="boton_login" onclick="ConfirmModal()" value="Si">
	    <input type="button" class="boton_login" onclick="closeModal_byName('Modal-confirm')" value="No">  
      </div> 
    </div>
    <br><br>
    <input type="hidden" value="" name="dedudccciones" id="dedudccciones">
    <input type="reset" value="Limpiar" class="boton_login">
    <input type="button" value="Procesar" class="boton_login" onclick="validar_devolucion()">
  </form>
  <div id="error_form" style="display:none;background-color:red">
    <p style="color:white;">por favor Indique el Cliente</p>
  </div>
</div>

</center>

</body>

</html>