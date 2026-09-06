<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 	
	<title>Datos Enviados </title>
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
          <li> <a href="paginaprincipal.php">Inicio</a></li>
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

/*Verify No Exist Illegarl Access*/
require_once __DIR__."/../../private/festejos/jwt.php";
require_once __DIR__."/../../private/festejos/db_config.php";
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
	if($acceso=="Visitante"){
		header("location:paginaprincipal.php");
	}
	 $last_page=$_SESSION['lastPage_user'];
	 $_SESSION['lastPage_user']="send_organizar_fiestas.php"; 
     if($last_page!="organizar_fiestas.php"){
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
	echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
}
$requerids_params=array("fecha","hora_fiesta","ubicacion","cliente_params","tipo_fiesta","publico","costo","impuestos","total","metodo_pago","data_productos","data_consumibles","data_personal");
foreach($requerids_params as $param){
	if(!isset($_POST[$param])){
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Datos Invalidos</h2></div>";
         echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
         exit;
	}
}
  
  //Process the Request to Organziate a Party
date_default_timezone_set('America/Caracas');
$fecha_actual=strval(date("d-m-Y"));
$fecha = $_POST['fecha'];
$hora=$_POST['hora_fiesta'];
$lugar=$_POST['ubicacion'];
$productos=$_POST['data_productos'];
$consumibles=$_POST['data_consumibles'];
$personal=$_POST['data_personal'];
$cliente=$_POST['cliente_params'];
$tipo=$_POST['tipo_fiesta'];
$publico=$_POST['publico'];
$metodo=$_POST['metodo_pago'];
$costo=$_POST['costo'];
$impuestos=$_POST['impuestos'];
$total=$_POST['total'];
$monto_fiest=floatval($total);
$cliente=explode(";",$cliente);
$cedula=$cliente[0];
$full_name_cliente=$cliente[1]." ".$cliente[2];
$res_conex=get_conexion();
if($res_conex!="OK"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_conex}</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
}

$party_found=false;
$cond_data=array("conditions_Names"=>array("lugar","CI_cliente","fecha","hora"),"conditions_Values"=>array($lugar,$cedula,$fecha,$hora),"condition_Types"=>array("and","and","and","and"),"conditions_Verify"=>array("=","=","=","="));	 
		   
$data_fiestas=get_data("fiesta",["CI_cliente"],$cond_data);
if($data_fiestas["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$data_fiestas['message']}</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
}
$data_fiestas=$data_fiestas["message"];

if(count($data_fiestas)>0){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Ya Existe una Fiesta Registrada para el Cliente en lugar , fecha y hora Indiciada</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
}
$id_fiesta=generate_id("fiesta","id_fiesta"); 
if($id_fiesta["status"]=="Error"){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_fiesta['message']}</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
}	
$id_fiesta=$id_fiesta["message"];
$data_fiesta=array("id_fiesta"=>$id_fiesta,"estatus"=>"Pendiente","CI_cliente"=>$cedula,"fecha"=>$fecha,"hora"=>$hora,"lugar"=>$lugar,"publico"=>$publico,"tipo_fiesta"=>$tipo,"costo"=>$monto_fiest);      
$res_add=add_data("fiesta",$data_fiesta,true,true);
if($res_add["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
}

$prod_list="Productos Alquilados: ";
$consumibles_list="Consumibles Comprados: ";
$personal_list="Personal de la Fiesta: ";
$reduccion_monto=0;
if($productos!=""){
	$productos=explode("|",$productos);
	foreach ($productos as $value1) {
		if($value1==""){
			continue;
		}
		$raw_pr=explode(";",$value1);
		$nombre_pr=$raw_pr[0];
		$info_pr=$raw_pr[1];
		$info_pr=explode(",",$info_pr);
		$format_pr=$info_pr[0];
		$cant_pr=$info_pr[1];
		$price_pr=$info_pr[2];
		$id_prod=generate_id("producto_reservado","id_reservado");
		if($id_prod["status"]=="Error"){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	        echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_prod['message']}</h2></div>";
            echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
            exit;
		}
		$id_prod=$id_prod["message"];
		$data_prod=array("id_reservado"=>$id_prod,"nombre_producto"=>$nombre_pr,"formato"=>$format_pr,"cantidad_reservada"=>$cant_pr,"precio_unidad"=>$price_pr,"id_fiesta"=>$id_fiesta);
		$res_add=add_data("producto_reservado",$data_prod,true);
		if($res_add["status"]=="Error"){
		   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	       echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
           echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
           exit;	
		}
		$cond_data=array("conditions_Names"=>array("nombre_producto"),"conditions_Values"=>array($nombre_pr),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
		$old_data=get_data("producto",["cantidad_disponible"],$cond_data,null,true);
		if($old_data["status"]=="Error"){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
            echo "<div id='error_msg2'><h2 id='error_text'>Error {$old_data['message']}</h2></div>";
            echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
            exit;	
		}
		$old_data=$old_data["message"];
		if(count($old_data)>0){
			 $old_cant=intval($old_data[0]["cantidad_disponible"]);
			 $real_cant=intval($cant_pr);
			 if($format_pr=="Pack(P)"){
				$real_cant=$real_cant*10;
			 }
			 else if($format_pr=="Pack(M)"){
				$real_cant=$real_cant*25;
			 }
             else if($format_pr=="Pack(G)"){
				 $real_cant=$real_cant*50;
			}
			$old_cant=$old_cant-($real_cant);
			$next_estatus="disponible";
			if($old_cant<=0){
				$old_cant=0;
				$next_estatus="agotado";
			}
		   
          $res_update= update_data("producto",["cantidad_disponible"=>strval($old_cant),"estatus"=>$next_estatus],$cond_data,null,true);  
		  if($res_update["status"]=="Error"){
			  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
              echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
              echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
              exit;
		  }
		}
		$prod_list=$prod_list.$nombre_pr;
		if($format_pr=="unidad" || $format_pr=="Unidad"){
			$prod_list=$prod_list." ".$cant_pr." Unidades ,";
		}
		else{
			 $prod_list=$prod_list." ".$format_pr." x".$cant_pr.",";
		}
		
	}
}
if($consumibles!=""){
	$consumibles=explode("|",$consumibles); 
	foreach($consumibles as $value2) {
		if($value2==""){continue;}
		$raw_c=explode(";",$value2);
		$nombre_c=$raw_c[0];
		$info_c=$raw_c[1];
		$info_c=explode(",",$info_c);
		$format_c=$info_c[0];
		$cant_c=$info_c[1];
		$price_c=$info_c[2];
		$cond_data=array("conditions_Names"=>array("nombre_producto"),"conditions_Values"=>array($nombre_c),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
		
		$old_data=get_data("producto",["cantidad"],$cond_data,null,true);
		if($old_data["status"]=="Error"){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
            echo "<div id='error_msg2'><h2 id='error_text'>Error {$old_data['message']}</h2></div>";
            echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
            exit;	
		}
		$old_data=$old_data["message"];
		if(count($old_data)>0){
			$old_cant=intval($old_data[0]["cantidad"]);
			$real_cant=intval($cant_c);
			if($format_c=="Pack(P)"){
				$real_cant=$real_cant*10;
			}
			else if($format_c=="Pack(M)"){
				$real_cant=$real_cant*25;
			}
			else if($format_c=="Pack(G)"){
				$real_cant=$real_cant*50;
			}
			$old_cant=$old_cant-($real_cant);
			$next_status="disponible";
			$reduccion_monto=$reduccion_monto+($real_cant*$price_c);
			if($old_cant<=0){
				$old_cant=0;
				$next_status="agotado";
			}
			$res_update=update_data("producto",array("cantidad_disponible"=>strval($old_cant),"cantidad"=>strval($old_cant),"estatus"=>$next_status),$cond_data,null,true);
		    if($res_update["status"]=="Error"){
				echo "<image src='images/incorrecto.png' width='120' height='120'/>";
                echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
                echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
                exit;
			}
		}
		$consumibles_list=$consumibles_list.$nombre_c;
		if($format_c=="unidad" || $format_c=="Unidad"){
			 $consumibles_list=$consumibles_list." ".$cant_c." Unidades ,";
		}
		else{
			 $consumibles_list=$consumibles_list." ".$format_c." x".$cant_c.",";
		}          
    }   
}
if($reduccion_monto>0){
	$reduccion_monto=$reduccion_monto+($reduccion_monto*0.16);
	$monto_fiest=$monto_fiest-$reduccion_monto;
}
$cond_data=array("conditions_Names"=>array("id_fiesta"),"conditions_Values"=>array($id_fiesta),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	
update_data("fiesta",array("costo"=>strval($monto_fiest)),$cond_data,null,true);
if($personal!=""){	
	$personal=explode("|",$personal);
	foreach ($personal as $value3) {
		if($value3==""){continue;}
		$info_p=explode(",",$value3);
		$ci=$info_p[0];
		$nams=$info_p[1];
		$apells=$info_p[2];
		$rol=$info_p[3];
		$id_p="Party_worker-{$id_fiesta}-{$ci}";
		$data_personalF=array("id_personal_fiesta"=>$id_p,"CI_trabaj"=>$ci,"rol"=>$rol,"id_fiesta"=>$data_fiesta["id_fiesta"]);
		$res_add=add_data("personal_fiesta",$data_personalF,true);
		if($res_add["status"]=="Error"){
			echo "<image src='images/incorrecto.png' width='120' height='120'/>";
            echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
            echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
            exit;
		}
		$personal_list=$personal_list.$nams." ".$apells."(".$rol."),";
				    
     }
}
$id_report=generate_id("reporte","id_reporte");
if($id_report["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
    echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report['message']}</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
}
$id_report=$id_report["message"];
$src="reportes/Fiesta".$id_report."_".$fecha_actual.".pdf";
$data_report=array("id_reporte"=>$id_report,"nombre_usuario"=>$usuario_actual,"CI_cliente"=>$cedula,"tipo"=>"Organizar Fiesta","src_reporte"=>$src,"fecha"=>$fecha_actual);
$res_add=add_data("reporte",$data_report,true);
if($res_add["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
    echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
	
}
$id_report_usr=generate_id("reporte_usuario","id_reporte_usr");
if($id_report_usr["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
    echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report_usr['message']}</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;
}

$id_report_usr=$id_report_usr["message"];
$h_actual=strval(date("H:i:s"));
$data_reporteUser=array("id_reporte_usr"=>$id_report_usr,"nombre_usuario"=>$usuario_actual,"accion"=>"Organizar Fiesta","fecha"=>$fecha_actual,"hora"=>$h_actual);		
$res_add=add_data("reporte_usuario",$data_reporteUser,true,true);
if($res_add["status"]=="Error"){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
    echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
    echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
    exit;

}	
//write the report
ob_start();
require('fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->Image("images/globos.png",35,0,30,30);
$pdf->Image("images/globos.png",150,55,40,60);
$pdf->SetLeftMargin(20);
$pdf->SetTitle('reporte:Fiesta'.$id_report."_".$fecha_actual);
$pdf->SetFont('Arial', 'B', 14);  
$pdf->MultiCell(0,10, utf8_decode('Agencia de Festejos Ruby C.A.'), 0, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(0,10, utf8_decode('Solicitud de Fiesta'), 0, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', '', 13);
$pdf->MultiCell(0, 7, utf8_decode('Emisor: '.$usuario_actual), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Nombre del Cliente: '.$full_name_cliente), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Cedula del Cliente: '.$cedula), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Fecha de la Fiesta: '.$fecha), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Nombre del Sitio de la Fiesta: '.$lugar), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Tipo de Fiesta: '.$tipo), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Dirigida a Publico: '.$publico), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode( $prod_list), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode( $consumibles_list), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode( $personal_list), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Metodo de Pago: '.$metodo), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Costo: '.$costo."$"), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Impuestos: '.$impuestos."$"), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Total a Pagar: '.$total."$"), 0, 1);
$pdf->Ln();
$pdf->Output('F',$src);
ob_end_flush(); 
echo "<image src='images/correcto.png' width='120' height='120'/>";
echo "<div id='correcto_msg'><h2 id='correcto_text'>Fiesta Organizada Satsifactoriamente</h2></div>";
echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
echo "<a id='boton_acceptar' class='boton2' target='_blank' href='".$src."' style='margin-left:3%' >Ver Reporte</a>";
		   

	   
     
  
 
?>

</div>

</center>

</body>

</html>