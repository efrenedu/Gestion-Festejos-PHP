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
require "conexion_bd.php";
session_start();
date_default_timezone_set('America/Caracas');
$usuario_actual="";	 
if(count($_SESSION)>0){
  $usuario = $_SESSION['username'];
  $usuario_actual=$usuario;
  $nivel=$_SESSION['acceso_user'];
  if (!isset($usuario)) {
	  header("location: loggin.php");
	  exit();
  }
  else{
	  if($nivel=="Visitante"){
		  header("location: paginaprincipal.php");
		  exit();
	  }
	  $last_page=$_SESSION['lastPage_user'];
	  $_SESSION['lastPage_user']="send_cancelar_fiesta.php";
	  if($last_page!="cancelar_fiesta.php"){
		 header("location:paginaprincipal.php");
         exit();		 
	  }
  }
}
else{
	 header("location: loggin.php");
	 exit();
}

/*Process the Request to Cancel Party*/
if(count($_POST)>0 ){
	if(isset($_POST["fiestas_ids"]) && isset($_POST["cliente"]) && isset($_POST["Monto"])){
		  if(validar_conexion()){
			  if(id_exist("cliente","CI_cliente", $_POST["cliente"])){
			      $monto=$_POST["Monto"];
				  $cliente_name="";
				  $dat_client=get_data_dict("cliente",["id_nombre"],1,["CI_cliente"],[$_POST["cliente"]]);
                  if(count($dat_client)>0){
                    $dat_name=get_data_dict("nombre",["nombre","segundo_nombre","apellido","segundo_apellido"],4,["id_nombre"],[$dat_client[0]["id_nombre"]]);
                    if(count($dat_name)>0){
						$cliente_name=$dat_name[0]["nombre"];
						if( $dat_name[0]["segundo_nombre"]!=""){
							$cliente_name=$cliente_name." ".$dat_name[0]["segundo_nombre"];
						}
						$cliente_name=$cliente_name." ".$dat_name[0]["apellido"];
						if( $dat_name[0]["segundo_apellido"]!=""){
							$cliente_name=$cliente_name." ".$dat_name[0]["segundo_apellido"];
						}
					}
				  }					  
			      $fiestas_cancel="Fiestas a Cancelar:";
				  $fiestas_list=array();
			      $fecha_actual=strval(date("d-m-Y"));
				  $ids_search=explode(';',$_POST["fiestas_ids"]);
			      $existen=count($ids_search);
				  $index=0;
			      for($i=0;$i<count($ids_search);$i++){
				      $target=$ids_search[$i];
				      if($target!=''){
					      if(id_exist("fiesta","id_fiesta", $target)){
					        $fiesta_dat=get_data_dict("fiesta",["fecha","hora","lugar"],3,["id_fiesta"],[$target]);
                            if(count($fiesta_dat)>0){
								$fiesta_str="-Fiesta ".strval($index+1)." : ".$fiesta_dat[0]["fecha"]." ".$fiesta_dat[0]["hora"]."(".$fiesta_dat[0]["lugar"].")";
								$fiestas_list[]=$fiesta_str;
							    $index++;
							}
							$data_reserv=get_data_dict("producto_reservado",["nombre_producto","formato","cantidad_reservada"],3,["id_fiesta"],[$target]);
                            if(count($data_reserv)>0){
								foreach($data_reserv as $dat){
									if(count($dat)>0){
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
										$prod_dat=get_data_dict("producto",["cantidad_disponible"],1,["nombre_producto"],[$id_prod]);
										if(count($prod_dat)>0){
											$old_cant=intval($prod_dat[0]["cantidad_disponible"]);
											$old_cant=$old_cant+$cant;
											update_data("producto",["cantidad_disponible","estatus"],[strval($old_cant),"disponible"],2,["nombre_producto"],[$id_prod]);
                      
										}
									}
								}
							}
							delete_data("personal_fiesta",["id_fiesta"],[$target]);
			                delete_data("producto_reservado",["id_fiesta"],[$target]);      
							delete_data("fiesta",["id_fiesta"],[$target]);
					      }
						  else{
							 $existen=$existen-1; 
						  }
				      }
			      }
				  $dat_reporte=array("id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)) , "nombre_usuario"=>$usuario_actual , "accion"=>"Cancelar Fiesta" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
				  add_data_dict("reporte_usuario",$dat_reporte);
			      $id_report=$dat_reporte["id_reporte_usr"];
				  $src="reportes/Cancelar_Fiesta".$id_report."_".$fecha_actual.".pdf";
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
                  $pdf->MultiCell(0, 7, utf8_decode('Emisor:'.$_SESSION['username']), 0, 1);
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
				  if($existen==count($ids_search)){
					   	 echo "<image src='images/correcto.png' width='120' height='120'/>";
		                 echo "<div id='correcto_msg'><h2 id='correcto_text'>Modificacion Realizada Satsifactoriamente</h2></div>";
		                 echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
		                 echo "<a id='boton_acceptar' class='boton2' target='_blank' href='".$src."' style='margin-left:3%' >Ver Reporte</a>";	
				   }
				   else{
					   if($existen>0){
					   	  echo "<image src='images/correcto.png' width='120' height='120'/>";
		                  echo "<div id='correcto_msg'><h2 id='correcto_text'>Modificacion Realizada pero Algunos Datos fueron Incorrectos</h2></div>";
		                  echo "<a id='boton_acceptar' class='boton2' href='paginaprincipal.php' >Aceptar</a>";
		                  echo "<a id='boton_acceptar' class='boton2' target='_blank' href='".$src."' style='margin-left:3%' >Ver Reporte</a>";
					   }
					   else{
						  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		                  echo "<div id='error_msg2'><h2 id='error_text'>Datos Incorrectos</h2></div>";
		                  echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";
					   }
				   }
			  }
			  else{
				 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		         echo "<div id='error_msg2'><h2 id='error_text'>Cliente no Registrado</h2></div>";
		         echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";     
			  }
		  }
		  else{
			 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		     echo "<div id='error_msg2'><h2 id='error_text'>Error al Conectar con BD</h2></div>";
		     echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";		    
		  }
	}
	else{
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
		echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";	
	}
}
else{
	 echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";  
}


?>

</div>

</center>

</body>

</html>