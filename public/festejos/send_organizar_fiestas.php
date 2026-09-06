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

  /*Verify No Exist Illegal Access*/
  require "conexion_bd.php";
  session_start();
  if(count($_SESSION)>0){
    $usuario= $_SESSION['username'];
	$nivel=$_SESSION['acceso_user'];;
    if (!isset($usuario)){
	   header("location: loggin.php");  
	   exit();
    }
	else{
	   if($nivel=="Visitante"){
		   header("location: paginaprincipal.php");
		   exit();
	   }
	   $last_page=$_SESSION['lastPage_user'];
	   $_SESSION['lastPage_user']="send_organizar_fiestas.php"; 
       if($last_page!="organizar_fiestas.php"){
		    header("location: paginaprincipal.php"); 
            exit();			
	   }
	}
  }
  else{
	 header("location: loggin.php");  
     exit();	 
  }
  
  //Process the Request to Organziate a Party
  if(count($_POST)>0 ){ 
     if((isset($_POST['fecha'] ) && isset($_POST['hora_fiesta']) && isset($_POST['ubicacion'])&& isset($_POST['cliente_params']) && isset($_POST['tipo_fiesta']) && isset($_POST['publico']) && isset($_POST['costo']) &&  isset($_POST['impuestos']) &&  isset($_POST['total']) && isset($_POST['metodo_pago']) && isset($_POST['data_productos']) && isset($_POST['data_consumibles']) &&isset($_POST['data_personal']))==false ) {
		echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>error datos invalidos</h2></div>";
		echo "<a class='boton2' href='organizar_fiestas.php' >aceptar</a>"; 
     }
     else{
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
        if(validar_conexion()){
		   $party_found=false;
		   $data_fiestas=get_data_dict("fiesta",["CI_cliente","lugar","publico","tipo_fiesta","fecha","hora"],6,-1,-1);
		   if(count($data_fiestas)>0){
			  for($i=0;$i<count($data_fiestas);$i++){
				  $lugar_f=$data_fiestas[$i]["lugar"];
				  $cl_f=$data_fiestas[$i]["CI_cliente"];
				  $publ_f=$data_fiestas[$i]["publico"];
				  $tipo_f=$data_fiestas[$i]["tipo_fiesta"];
				  $fecha_f=$data_fiestas[$i]["fecha"]; 
				  $hora_f=$data_fiestas[$i]["hora"];
				  if($lugar_f==$lugar && $cl_f==$cedula && $publ_f==$publico && $tipo_f==$tipo && $fecha_f==$fecha && $hora_f==$hora){
					 $party_found=true; 
					 $i=count($data_fiestas);
				  }
			  }
		   }
		   if($party_found==false){
              $id_fiesta=strval(generate_id("fiesta","id_fiesta",true));  
			  $data_fiesta=array("id_fiesta"=>$id_fiesta,"estatus"=>"Pendiente","CI_cliente"=>$cedula,"fecha"=>$fecha,"hora"=>$hora,"lugar"=>$lugar,"publico"=>$publico,"tipo_fiesta"=>$tipo,"costo"=>$monto_fiest);      
			  add_data_dict("fiesta",$data_fiesta);
			  $prod_list="Productos Alquilados: ";
			  $consumibles_list="Consumibles Comprados: ";
			  $personal_list="Personal de la Fiesta: ";
			  $reduccion_monto=0;
			  if($productos!=""){
			    $productos=explode("|",$productos);
				foreach ($productos as $value1) {
				    if($value1!=""){
					   $raw_pr=explode(";",$value1);
					   $nombre_pr=$raw_pr[0];
					   $info_pr=$raw_pr[1];
					   $info_pr=explode(",",$info_pr);
					   $format_pr=$info_pr[0];
					   $cant_pr=$info_pr[1];
					   $price_pr=$info_pr[2];
					   $id_prod=strval(generate_id("producto_reservado","id_reservado",true));
					   $data_prod=array("id_reservado"=>$id_prod,"nombre_producto"=>$nombre_pr,"formato"=>$format_pr,"cantidad_reservada"=>$cant_pr,"precio_unidad"=>$price_pr,"id_fiesta"=>$id_fiesta);
					   add_data_dict("producto_reservado",$data_prod);
					   $old_data=get_data_dict("producto",["cantidad_disponible"],1,["nombre_producto"],[$nombre_pr]);
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
						   update_data("producto",["cantidad_disponible","estatus"],[strval($old_cant),$next_estatus],2,["nombre_producto"],[$nombre_pr]);  
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
			  }
			  if($consumibles!=""){
				$consumibles=explode("|",$consumibles); 
				foreach($consumibles as $value2) {
				    if($value2!=""){
					   $raw_c=explode(";",$value2);
					   $nombre_c=$raw_c[0];
					   $info_c=$raw_c[1];
					   $info_c=explode(",",$info_c);
					   $format_c=$info_c[0];
					   $cant_c=$info_c[1];
					   $price_c=$info_c[2];
					   $old_data=get_data_dict("producto",["cantidad"],1,["nombre_producto"],[$nombre_c]);
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
						   update_data("producto",["cantidad_disponible","cantidad","estatus"],[strval($old_cant),strval($old_cant),$next_status],3,["nombre_producto"],[$nombre_c]);
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
			  }
			  if($reduccion_monto>0){
				 $reduccion_monto=$reduccion_monto+($reduccion_monto*0.16);
			     $monto_fiest=$monto_fiest-$reduccion_monto;
			  }
			  update_data("fiesta",["costo"],[strval($monto_fiest)],1,["id_fiesta"],[$id_fiesta]);
		      if($personal!=""){	
			     $personal=explode("|",$personal);
		         foreach ($personal as $value3) {
			  	    if($value3!=""){
					   $info_p=explode(",",$value3);
					   $ci=$info_p[0];
					   $nams=$info_p[1];
					   $apells=$info_p[2];
					   $rol=$info_p[3];
					   $id_p=strval(generate_id("personal_fiesta","id_personal_fiesta",true)); 
					   $data_personalF=array("id_personal_fiesta"=>$id_p,"CI_trabaj"=>$ci,"rol"=>$rol,"id_fiesta"=>$data_fiesta["id_fiesta"]);
					   add_data_dict("personal_fiesta",$data_personalF);
				       $personal_list=$personal_list.$nams." ".$apells."(".$rol."),";
				    }
                 }
			  }
              $id_report=generate_id("reporte","id_reporte",true);
			  $src="reportes/Fiesta".$id_report."_".$fecha_actual.".pdf";
			  $data_report=array("id_reporte"=>$id_report,"nombre_usuario"=>$_SESSION['username'],"CI_cliente"=>$cedula,"tipo"=>"Organizar Fiesta","src_reporte"=>$src,"fecha"=>$fecha_actual);
		      add_data_dict("reporte",$data_report);
			  $h_actual=strval(date("H:i:s"));
			  $data_reporteUser=array("id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)),"nombre_usuario"=>$_SESSION['username'],"accion"=>"Organizar Fiesta","fecha"=>$fecha_actual,"hora"=>$h_actual);		
              add_data_dict("reporte_usuario",$data_reporteUser);
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
              $pdf->MultiCell(0, 7, utf8_decode('Emisor: '.$_SESSION['username']), 0, 1);
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
		   }
		   else{
			   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		       echo "<div id='error_msg2'><h2 id='error_text'>La Fiesta ya ha Sido Organizada</h2></div>";
		       echo "<a class='boton2' href='organizar_fiestas.php' >Aceptar</a>";
		   } 
	    }
	    else{
		   echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		   echo "<div id='error_msg2'><h2 id='error_text'>Fallo al Conectar</h2></div>";
		   echo "<a class='boton2' href='organizar_fiestas.php' >Aceptar</a>";
	    }
     }
  }
  else{
	 	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
		echo "<div id='error_msg2'><h2 id='error_text'>Error Datos Invalidos</h2></div>";
		echo "<a class='boton2' href='organizar_fiestas.php' >Aceptar</a>";  
  }
?>

</div>

</center>

</body>

</html>