
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>datos enviados </title>
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

/*Verify No Exist Illegal Access*/

/*Verify No Exist Illegal Access*/
require_once __DIR__."/../../private/festejos/jwt.php";
require_once __DIR__."/../../private/festejos/db_config.php";
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
	$acceso=$res["Message"]["Acceso"];
	$usuario=$res["Message"]["Id_usr"];
	if($acceso=="Visitante"){
		header("location:paginaprincipal.php");
	}
	$lastPage_user= $_SESSION['lastPage_user'];
	$_SESSION['lastPage_user']="realizar_alquiler.php";
	if($lastPage_user!="alquilar_productos.php"){
		   header("location: paginaprincipal.php"); 
		   exit();
	}
}
else{
	 header("location: loggin.php");
	 exit();
}
 
if(count($_POST)<=0){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Faltan Datos</h2></div>";
	echo "<a class='boton2' href='alquilar_productos.php' >aceptar</a>"; 
    exit;
}


/*Process the Request of Rent Products*/
$requerids_params=array("data_productos","fecha","cliente_params","metodo_pago","costo","impuestos","total");
foreach($requerids_params as $param){
	if(!isset($_POST[$param])){
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Datos Invalidos</h2></div>";
         echo "<a class='boton2' href='alquilar_productos.php' >aceptar</a>"; 
         exit;
	}
}

date_default_timezone_set('America/Caracas');
$productos = $_POST['data_productos'];
$fecha_alquiler =strval( date("d-m-Y")); 
$fecha_devolucion = $_POST['fecha'];
$cliente = $_POST['cliente_params'];
$metodo_pago=$_POST['metodo_pago'];
$subtotal=$_POST['costo'];
$impuestos=$_POST['impuestos'];
$total=$_POST['total'];
$res_conex=get_conexion();
if($res_conex!="OK"){
     echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_conex}</h2></div>";
     echo "<a class='boton2' href='alquilar_productos.php' >aceptar</a>"; 
     exit;	
}
$cliente=explode(";",$cliente);
$cedula=$cliente[0];
$nombres=$cliente[1];
$apellidos=$cliente[2];
$cond_data=array("conditions_Names"=>array("CI_cliente"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
$old_d_cliente=get_data("cliente",["estatus"],$cond_data,null,true);
if($old_d_cliente["status"]=="Error"){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error {$old_d_cliente['message']}}</h2></div>";
     echo "<a class='boton2' href='alquilar_productos.php' >aceptar</a>"; 
     exit;
}
$old_d_cliente=$old_d_cliente["message"];
if(count($old_d_cliente)<=0){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error Cliente Inexistente</h2></div>";
     echo "<a class='boton2' href='alquilar_productos.php' >aceptar</a>"; 
     exit;
}
if($old_d_cliente[0]["estatus"]=="deuda"){
	echo "<image src='incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>El Cliente ya ha Realizado un Alquiler </h2></div>";
	echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
    exit;
}
$productos=explode(";",$productos);
$productos_list=array();
$count_prods=0;
for($i=0;$i<count($productos);$i++){
	 if($productos[$i]==""){
	       continue;
	 }
     $temp_prod=explode(":",$productos[$i]);
	 if(count($temp_prod)<=0){
		 continue;
	 }
	 
	 $name_prod=$temp_prod[0];
	 $info_prod=explode(",",$temp_prod[1]);
	 if(count($info_prod)<=0){
		continue; 
	 }
	 
	 $formato=$info_prod[0];
	 $cant=$info_prod[1];
	 $precio_ud=$info_prod[2];
	 $cond_data=array("conditions_Names"=>array("nombre_producto"),"conditions_Values"=>array($name_prod),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
	 $old_data=get_data("producto",["cantidad_disponible","estatus"],$cond_data,null,true);
	 if($old_data["status"]=="Error"){
		echo "<image src='incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error {$old_data['message']} </h2></div>";
	    echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
        exit; 
	 }
	 $old_data=$old_data["message"];
	 if(count($old_data)<=0){
		 echo "<image src='incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Error Producto Inexistente </h2></div>";
	     echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
         exit; 
	  }
	
	  $next_id=generate_id("producto_alquilado","id_alquilado");
	  if($next_id["status"]=="Error"){
		 echo "<image src='incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Error{$next_id['message']}</h2></div>";
	     echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
         exit; 
		 
	  }
	  $next_id=$next_id["message"];
	  $cant_uds=intval($cant);
	  if($formato=="Pack(P)"){
         $cant_uds=$cant_uds*10;
	  }
	  else if($formato=="Pack(M)"){
		 $cant_uds=$cant_uds*25;  
	  }
      else if($formato=="Pack(G)"){
          $cant_uds=$cant_uds*50;
	 }
	 $next_cant=intval($old_data[0]["cantidad_disponible"])-$cant_uds;
	 $estatus=$old_data[0]["estatus"];
	 if($next_cant<=0){
		$next_cant=0;
		$estatus="agotado";
	 }
	 $res_update=update_data("producto",array("cantidad_disponible"=>strval( $next_cant),"estatus"=>$estatus),$cond_data,null,true);
	 if($res_update["status"]=="Error"){
         echo "<image src='incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Error{$res_update['message']}</h2></div>";
	     echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
         exit;
	 }		 
			              
	 
     $data_prod=array("id_alquilado"=>$next_id,"nombre_producto"=>$name_prod,"formato"=>$formato,"cantidad_alquilada"=>$cant,"precio_unidad"=>$precio_ud,"CI_cliente"=>$cedula,"fecha_devolucion"=>$fecha_devolucion);	
     $res_add=add_data("producto_alquilado",$data_prod,true);
	 if($res_add["status"]=="Error"){
	      echo "<image src='incorrecto.png' width='120' height='120'/>";
	      echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
	      echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
           exit;
	
     }
	 $count_prods+=1;
	 $productos_list[ $name_prod]=array($formato,$cant,$precio_ud);			       
}
if($count_prods<=0){
	echo "<image src='incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error Productos Alquilar No Recibidos </h2></div>";
	echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
    exit;
}

$cond_data=array("conditions_Names"=>array("CI_cliente"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	  
$res_update=update_data("cliente",array("estatus"=>"deuda"),$cond_data,null);
if($res_update["status"]=="Error"){
	echo "<image src='incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
	echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
    exit;
}
$id_report=generate_id("reporte","id_reporte");
if($id_report["status"]=="Error"){
    echo "<image src='incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report['message']}</h2></div>";
	echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
    exit;

}	
$id_report=$id_report["message"];

$src="reportes/Alquiler".strval($id_report)."_".$fecha_alquiler.".pdf";
$data_report=array("id_reporte"=>$id_report,"nombre_usuario"=>$_SESSION['username'],"CI_cliente"=>$cedula,"tipo"=>"alquiler","src_reporte"=>$src,"fecha"=>$fecha_alquiler);
$res_add=add_data("reporte",$data_report,true);
if($res_add["status"]=="Error"){
	echo "<image src='incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
	echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
    exit;
	
}
$h_actual=strval(date("H:i:s"));
$id_report_usr=generate_id("reporte_usuario","id_reporte_usr");
if($id_report_usr["status"]=="Error"){
	echo "<image src='incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report_usr['message']}</h2></div>";
	echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
    exit;
	
}
$id_report_usr=$id_report_usr["message"];
$data_reporteUser=array("id_reporte_usr"=>$id_report_usr,"nombre_usuario"=>$usuario,"accion"=>"alquilar productos","fecha"=>$fecha_alquiler,"hora"=>$h_actual);		
$res_add=add_data("reporte_usuario",$data_reporteUser,true,true);
if($res_add["status"]=="Error"){
	echo "<image src='incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
	echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
    exit;
	
}
$prods_alquilar="productos alquiar: ";
foreach ($productos_list as $key => $value) {
    $msg=$key.": ";
	$formato=$value[0];
	$cant=$value[1];
	$precio=$value[2];
	if($formato=="Unidad" || $formato=="unidad"){
		 $msg=$msg.$cant." Unidades , ";
	}
	else{
		$msg=$msg.$formato." x".$cant." , ";
	}
	 $prods_alquilar=$prods_alquilar.$msg;
}
				
//Write the Report
ob_start();
require('fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->Image("images/globos.png",35,0,30,30);
$pdf->Image("images/globos.png",150,55,40,60);
$pdf->SetLeftMargin(20);
$pdf->SetTitle('Reporte: Alquiler de Productos-'.$id_report);
$pdf->SetFont('Arial', 'B', 14);  
$pdf->MultiCell(0,10, utf8_decode('Agencia de Festejos Ruby C.A.'), 0, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(0,10, utf8_decode('Solicitud de Alquiler '), 0, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', '', 13);
$pdf->MultiCell(0, 7, utf8_decode('Emisor:'.$_SESSION['username']), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode($prods_alquilar), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Fecha Devolucion:'.$fecha_devolucion ), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Fecha de Alquiler:'.$fecha_alquiler), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Metodo de Pago:'.$metodo_pago), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Subtotal:'.$subtotal."$"), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Impuestos:'.$impuestos."$"), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Total a Pagar:'.$total."$"), 0, 1);
$pdf->Ln();  
$pdf->Output('F',$src,true);
ob_end_flush(); 
echo "<image src='images/correcto.png' width='120' height='120'/>";
echo "<div id='correcto_msg'><h2 id='correcto_text'>Producto Alquilado Satisfactoriamente</h2></div>";
echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
echo "<a id='boton_acceptar' class='boton2' target='_blank' href='".$src."' style='margin-left:3%' >Ver Reporte</a>";  
			

?>
  
</div>

</center>

</body>

</html>