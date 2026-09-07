<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Cancelar Fiesta</title>
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
	 $_SESSION['lastPage_user']="send_cancelar_fiesta.php";
	 if($last_page!="cancelar_fiesta.php"){
		 header("location:paginaprincipal.php");
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
	echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
    exit;
}
date_default_timezone_set('America/Caracas');
$requerids_params=array("fiestas_ids","cliente","Monto");
foreach($requerids_params as $param){
	if(!isset($_POST[$param])){
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Datos Invalidos</h2></div>";
         echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
         exit;
	}
}
  
$res_conex=get_conexion();
if($res_conex!="OK"){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_conex}</h2></div>";
     echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
     exit; 
}

$monto=$_POST["Monto"];
$cliente_name="";
$cedula=$_POST["cliente"];
$cond_data=array("conditions_Names"=>array("CI_cliente"),"conditions_Values"=>array($cedula),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
$join_data=array();
$join_data["nombre"]=array("query_field"=>array("nombre","apellido","segundo_nombre","segundo_apellido"),"share_fields"=>array("field"=>"id_nombre","table_reference"=>"cliente"),"Conditions_join"=>null);
	
$dat_client=get_data("cliente",["id_nombre"],$cond_data,$join_data,true);
if($dat_client["status"]=="Error"){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error {$dat_client['message']}</h2></div>";
     echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
     exit; 
	
}
$dat_client=$dat_client["message"];
if(count($dat_client)<=0){
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	 echo "<div id='error_msg2'><h2 id='error_text'>Error Cliente Inexistente</h2></div>";
     echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
     exit;
	
}
$cliente_name=$dat_client[0]["nombre"];
if( $dat_client[0]["segundo_nombre"]!=""){
	$cliente_name=$cliente_name." ".$dat_client[0]["segundo_nombre"];
}
$cliente_name=$cliente_name." ".$dat_client[0]["apellido"];
if( $dat_client[0]["segundo_apellido"]!=""){
	$cliente_name=$cliente_name." ".$dat_client[0]["segundo_apellido"];
}
$fiestas_cancel="Fiestas a Cancelar:";
$fiestas_list=array();
$fecha_actual=strval(date("d-m-Y"));
$ids_search=explode(';',$_POST["fiestas_ids"]);
$index=0;
$remove_party=array();
for($i=0;$i<count($ids_search);$i++){
	$target=$ids_search[$i];
	if($target==''){continue;}
	
	$cond_data=array("conditions_Names"=>array("id_fiesta"),"conditions_Values"=>array($target),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	
	$fiesta_dat=get_data("fiesta",["fecha","hora","lugar"],$cond_data,null,true);
    if($fiesta_dat["status"]=="Error"){
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Error {$fiesta_dat['message']}</h2></div>";
         echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
         exit;
	}
	$fiesta_dat=$fiesta_dat["message"];
	if(count($fiesta_dat)<=0){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error Existen Fiestas Inexistentes</h2></div>";
        echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
        exit;
	}
	$fiesta_str="-Fiesta ".strval($index+1)." : ".$fiesta_dat[0]["fecha"]." ".$fiesta_dat[0]["hora"]."(".$fiesta_dat[0]["lugar"].")";
	$fiestas_list[]=$fiesta_str;
	$index++;
	$remove_party[]=$target;

	$data_reserv=get_data("producto_reservado",["nombre_producto","formato","cantidad_reservada"],$cond_data,null,true);
    if($data_reserv["status"]=="Error"){
		 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	     echo "<div id='error_msg2'><h2 id='error_text'>Error {$data_reserv['message']}</h2></div>";
         echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
         exit;
	}
	$data_reserv=$data_reserv["message"];
	if(count($data_reserv)>0){
		foreach($data_reserv as $dat){
			if(count($dat)<=0){
				continue;
			}
			$id_prod=$dat["nombre_producto"];
			$format=$dat["formato"];
			$cant=intval($dat["cantidad_reservada"]);
			if($format=="Pack(P)"){
				$cant=$cant*10;
			}
			else if($format=="Pack(M)"){
				$cant=$cant*25;
			}
			else if($format=="Pack(G)"){
				$cant=$cant*50;
			}
			$cond_data=array("conditions_Names"=>array("nombre_producto"),"conditions_Values"=>array($id_prod),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	
			$prod_dat=get_data("producto",["cantidad_disponible"],$cond_data,null,true);
			if($prod_dat["status"]=="Error"){
				 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	             echo "<div id='error_msg2'><h2 id='error_text'>Error {$prod_dat['message']}</h2></div>";
                 echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
                 exit;
			}
			$prod_dat=$prod_dat["message"];
			if(count($prod_dat)<=0){
				continue;
			}
			$old_cant=intval($prod_dat[0]["cantidad_disponible"]);
			$old_cant=$old_cant+$cant;
			$res_update=update_data("producto",array("cantidad_disponible"=>strval($old_cant),"estatus"=>"disponible"),$cond_data,null,true); 
            if($res_update["status"]=="Error"){
				 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	             echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
                 echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
                 exit;
			}
		}
	}
}
foreach($remove_party as $id_remove){
	$cond_data=array("conditions_Names"=>array("id_fiesta"),"conditions_Values"=>array($id_remove),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 	
			
	$res_del=delete_data("personal_fiesta",$cond_data);
	if($res_del["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_del['message']}</h2></div>";
        echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
        exit;
	}
	$res_del=delete_data("producto_reservado",$cond_data);
    if($res_del["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_del['message']}</h2></div>";
        echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
        exit;
	}	
	$res_del=delete_data("fiesta",$cond_data);
	if($res_del["status"]=="Error"){
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_del['message']}</h2></div>";
        echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
        exit;
	}
}
$id_report=generate_id("reporte","id_reporte");
if($id_report["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
    echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report['message']}</h2></div>";
    echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
    exit;
}
$id_report=$id_report["message"];
$src="reportes/Cancelar_Fiesta".$id_report."_".$fecha_actual.".pdf";
$data_report=array("id_reporte"=>$id_report,"nombre_usuario"=>$usuario_actual,"CI_cliente"=>$cedula,"tipo"=>"Organizar Fiesta","src_reporte"=>$src,"fecha"=>$fecha_actual);
$res_add=add_data("reporte",$data_report,true);
if($res_add["status"]=="Error"){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
    echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
    exit;	
}

$id_report_usr=generate_id("reporte_usuario","id_reporte_usr");
if($id_report_usr["status"]=="Error"){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report_usr['message']}</h2></div>";
    echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
    exit;		
}
$id_report_usr=$id_report_usr["message"];
$dat_reporte=array("id_reporte_usr"=>$id_report_usr , "nombre_usuario"=>$usuario_actual , "accion"=>"Cancelar Fiesta" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
$res_add=add_data("reporte_usuario",$dat_reporte,true,true);
if($res_add["status"]=="Error"){
    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
    echo "<a class='boton2' href='cancelar_fiesta.php' >aceptar</a>"; 
    exit;	
}
			 
//write the report
ob_start();
require('fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->Image("images/globos.png",35,0,30,30);
$pdf->Image("images/globos.png",135,49,40,60);
$pdf->SetLeftMargin(20);
$pdf->SetTitle('Reporte: Cancelar Fiesta-'.$id_report);
$pdf->SetFont('Arial', 'B', 14);  
$pdf->MultiCell(0,10, utf8_decode('Agencia de Festejos Ruby C.A.'), 0, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(0,10, utf8_decode('Solicitud para Cancelar Fiesta'), 0, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', '', 13);
$pdf->MultiCell(0, 7, utf8_decode('Fecha Emision:'.$fecha_actual), 0, 'L');
$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Emisor:'.$usuario_actual), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode('Cliente:'.$cliente_name), 0, 1);
$pdf->Ln();
$pdf->MultiCell(0, 7, utf8_decode($fiestas_cancel), 0, 1);
$pdf->Ln();
foreach($fiestas_list as $temp_party){
     $pdf->MultiCell(0, 7, utf8_decode($temp_party), 0, 1);
      $pdf->Ln();
}                    
$pdf->MultiCell(0, 7, utf8_decode('Total a Devolver:'.$monto."$"), 0, 1);
$pdf->Output('F',$src,true);
ob_end_flush(); 
		   				   
echo "<image src='images/correcto.png' width='120' height='120'/>";
echo "<div id='correcto_msg'><h2 id='correcto_text'>Modificacion Realizada Satsifactoriamente</h2></div>";
echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
echo "<a id='boton_acceptar' class='boton2' target='_blank' href='".$src."' style='margin-left:3%' >Ver Reporte</a>";	



			  
		
	
	


?>

</div>

</center>

</body>

</html>