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
	if($acceso=="Visitante"){
		header("location:paginaprincipal.php");
	}
	$usuario_actual=$res["Message"]["Id_usr"];
	$last_page=$_SESSION['lastPage_user'];
	$_SESSION['lastPage_user']="realizar_devolucion.php"; 
	if($last_page!="devolver_productos.php"){
		header("location:paginaprincipal.php");
		exit();
	}
}
else{
	 header("location: loggin.php");
	 exit();
}
require_once __DIR__."/../../private/festejos/db_config.php";

if(count($_POST)<=0){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Faltan Datos</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}
$requerids_params=array("cliente","dedudccciones","deuda_retraso","deuda_damage","deuda_total","metodo_pago");
foreach($requerids_params as $param){
	if(!isset($_POST[$param])){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Datos Invalidos</h2></div>";
        echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	    exit;
	}
}
date_default_timezone_set('America/Caracas');
$cedula=$_POST['cliente'];
$deuda_retraso=$_POST['deuda_retraso'];
$deuda_damage=$_POST['deuda_damage'];
$deuda_total=$_POST['deuda_total'];
$metodo_pago=$_POST['metodo_pago'];
$prods_devolver="Productos: ";
$res_conex=get_conexion();
if($res_conex!="OK"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_conex}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}

$cond_data=array("conditions_Names"=>array("CI_cliente"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
    
$old_d_cliente=get_data("cliente",["estatus"],$cond_data,null,true);  
if($old_d_cliente["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$old_d_cliente['message']}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}
$old_d_cliente=$old_d_cliente["message"];
if(count($old_d_cliente)<=0){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error Cliente Inexistente </h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}
if($old_d_cliente[0]["estatus"]!="deuda"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>El Cliente no tiene Ningun Producto Pendiente por Devolver</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
	
}
$deducciones=$_POST['dedudccciones'];
$deducciones=explode(';',$deducciones);
$data_deducc=array();
for($j=0;$j<count($deducciones);$j++){
	if($deducciones[$j]!=""){
		 $next_deducc=explode(":",$deducciones[$j]);
		 $data_deducc[$next_deducc[0]]=$next_deducc[1];
	}
}

//update the data base 

$alquilados=get_data("producto_alquilado",["nombre_producto","formato","cantidad_alquilada"],$cond_data,null,true);
if($alquilados["status"]=="Error"){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$alquilados['message']}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}			
$alquilados=$alquilados["message"];
if(count($alquilados)<=0){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error el Cliente no Tiene ningun Producto Alquilado</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}

if(count($data_deducc)>0){
	for($i=0;$i<count($alquilados);$i++){
		$nombre=$alquilados[$i]["nombre_producto"];
		$cant_alquilada=intval($alquilados[$i]["cantidad_alquilada"]);
		$number_deduc=intval($data_deducc[$nombre]);
		$prods_devolver=$prods_devolver.$nombre." ";
		$formato=$alquilados[$i]["formato"];
		if($formato=="Pack(P)"){
			$prods_devolver=$prods_devolver.$formato." x".$cant_alquilada;
			$cant_alquilada=$cant_alquilada*10;
		}
		else if($formato=="Pack(M)"){
			$cant_alquilada=$cant_alquilada*25;
		}
		else if($formato=="Pack(G)"){
			$cant_alquilada=$cant_alquilada*50;
		}
		else{
			$prods_devolver=$prods_devolver.$cant_alquilada." Unidades";
		}
		$cant_alquilada=$cant_alquilada-$number_deduc;
		if($cant_alquilada<0){
	         $cant_alquilada=0;
		}
		$cond_data=array("conditions_Names"=>array("nombre_producto"),"conditions_Values"=>array($nombre),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
			
		$data_prod=get_data("producto",["cantidad_disponible","cantidad"],$cond_data,null,true);
		if($data_prod["status"]=="Error"){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	        echo "<div id='error_msg2'><h2 id='error_text'>Error {$data_prod['message']}</h2></div>";
            echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	        exit;
		}
		$data_prod=$data_prod["message"];
		if(count($data_prod)>0){
			$cant_disp=intval($data_prod[0]["cantidad_disponible"]);
			$cant_disp=$cant_disp+$cant_alquilada;
			$cant_total=intval($data_prod[0]["cantidad"]);
			$cant_total=$cant_total-$number_deduc;
			if($cant_total<0){
				$cant_total=0; 
			}
			$res_update=update_data("producto",array("cantidad_disponible"=>strval($cant_disp),"cantidad"=>strval($cant_total)),$cond_data,null,true); 
		    if($res_update["status"]=="Error"){
				echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	            echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
                echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	            exit;
			}
		}
		$prods_devolver=$prods_devolver." , ";
	}
}
$cond_data=array("conditions_Names"=>array("CI_cliente"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
$res_del=delete_data("producto_alquilado",$cond_data);
if($res_del["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_del['message']}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}

$res_update= update_data("cliente",array("estatus"=>"solvente"),$cond_data);
if($res_update["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}				
$fecha=strval( date("d-m-Y"));
$id_report=generate_id("reporte","id_reporte");
if($id_report["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report['message']}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}
$id_report=$id_report["message"];
$src="reportes/Devolver_Alquiler".strval($id_report)."_".$fecha.".pdf";
$data_report=array("id_reporte"=>$id_report,"nombre_usuario"=>$usuario_actual,"CI_cliente"=>$cedula,"tipo"=>"devolver alquiler","src_reporte"=>$src,"fecha"=>$fecha);
$res_add=add_data("reporte",$data_report,true);
if($res_add["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}
$h_actual=strval(date("H:i:s"));

$id_report_usr=generate_id("reporte_usuario","id_reporte_usr");
if($id_report_usr["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report_usr['message']}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}
$id_report_usr=$id_report_usr["message"];
$data_reporteUser=array("id_reporte_usr"=>$id_report_usr,"nombre_usuario"=>$usuario_actual,"accion"=>"devolver alquiler","fecha"=>$fecha,"hora"=>$h_actual);		
$res_add=add_data("reporte_usuario",$data_reporteUser,true,true);
if($res_add["status"]=="Error"){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
    echo "<a class='boton2' href='devolver_productos.php' >Aceptar</a>";
	exit;
}	

ob_start();
require('fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->Image("images/globos.png",35,0,30,30);
$pdf->Image("images/globos.png",150,55,40,60);
$pdf->SetLeftMargin(20);
$pdf->SetTitle('Reporte: Devolver Alquiler-'.$id_report);
$pdf->SetFont('Arial', 'B', 14);  
$pdf->MultiCell(0,10, utf8_decode('Agencia de Festejos Ruby C.A.'), 0, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(0,10, utf8_decode('Devolver Alquiler'), 0, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', '', 13);
$pdf->MultiCell(0, 7, utf8_decode('Emisor:'.$_SESSION['username']), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode( $prods_devolver), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Fecha:'.$fecha ), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Metodo de Pago:'.$metodo_pago), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Deuda por Retraso:'.$deuda_retraso."$"), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Deuda por Daños o Perdidas:'.$deuda_damage."$"), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Deuda Total:'.$deuda_total."$"), 0, 1);
$pdf->Ln();  
$pdf->Output('F',$src,true);
ob_end_flush(); 

echo "<image src='images/correcto.png' width='120' height='120'/>";
echo "<div id='correcto_msg'><h2 id='correcto_text'>Devolucion de Alquiler Realizada Satisfactoriamente</h2></div>";
echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
echo "<a id='boton_acceptar' class='boton2' target='_blank' href='".$src."' style='margin-left:3%' >Ver Reporte</a>";
			  

?>


</div>

</center>

</body>

</html>