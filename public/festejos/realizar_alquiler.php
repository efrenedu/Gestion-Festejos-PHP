
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
session_start();
$usuario = "";
$nivel="";
if(count($_SESSION)>0){
	$usuario = $_SESSION['username'];
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
	   $lastPage_user= $_SESSION['lastPage_user'];
	   $_SESSION['lastPage_user']="realizar_alquiler.php";
	   if($lastPage_user!="alquilar_productos.php"){
		   header("location: paginaprincipal.php"); 
		   exit();
	   }
   }
}
else {
	header("location: loggin.php");
	exit();
}

require "conexion_bd.php";

/*Process the Request of Rent Products*/

if (count($_POST)>0 ) {
	date_default_timezone_set('America/Caracas');
	if(isset($_POST['data_productos']) &&  isset($_POST['fecha']) && isset($_POST['cliente_params']) && isset( $_POST['metodo_pago'] ) && isset($_POST['costo']) && isset($_POST['impuestos']) &&  isset($_POST['total'])){
	  $productos = $_POST['data_productos'];
      $fecha_alquiler =strval( date("d-m-Y")); 
      $fecha_devolucion = $_POST['fecha'];
      $cliente = $_POST['cliente_params'];
      $metodo_pago=$_POST['metodo_pago'];
	  $subtotal=$_POST['costo'];
	  $impuestos=$_POST['impuestos'];
	  $total=$_POST['total'];
	  if(validar_conexion()){
		$cliente=explode(";",$cliente);
		$cedula=$cliente[0];
		$nombres=$cliente[1];
		$apellidos=$cliente[2];
		$old_d_cliente=get_data_dict("cliente",["estatus"],1,["CI_cliente"],[$cedula]);
		if(count($old_d_cliente)>0){
			if($old_d_cliente[0]["estatus"]=="deuda"){
				 echo "<image src='incorrecto.png' width='120' height='120'/>";
	             echo "<div id='error_msg2'><h2 id='error_text'>El Cliente ya ha Realizado un Alquiler</h2></div>";
	             echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
			}
			else{
		        $productos=explode(";",$productos);
		        $productos_list=array();
		        for($i=0;$i<count($productos);$i++){
			        if($productos[$i]!=""){
                       $temp_prod=explode(":",$productos[$i]);
			           if(count($temp_prod)>0){
			              $name_prod=$temp_prod[0];
			              $info_prod=explode(",",$temp_prod[1]);
			              if(count($info_prod)>0){
			                 $formato=$info_prod[0];
			                 $cant=$info_prod[1];
			                 $precio_ud=$info_prod[2];
					         $next_id=strval(generate_id("producto_alquilado","id_alquilado",true));
                             $data_prod=array("id_alquilado"=>$next_id,"nombre_producto"=>$name_prod,"formato"=>$formato,"cantidad_alquilada"=>$cant,"precio_unidad"=>$precio_ud,"CI_cliente"=>$cedula,"fecha_devolucion"=>$fecha_devolucion);	
                             add_data_dict("producto_alquilado",$data_prod);
					         $productos_list[ $name_prod]=array($formato,$cant,$precio_ud);
					         $old_data=get_data_dict("producto",["cantidad_disponible","estatus"],2,["nombre_producto"],[$name_prod]);
					         if(count($old_data)>0){
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
						        update_data("producto",["cantidad_disponible","estatus"],[strval( $next_cant),$estatus],2,["nombre_producto"],[$name_prod]);
					         }					   
			              }
			           }
			       }
		        }
		        update_data("cliente",["estatus"],["deuda"],1,["CI_cliente"],[$cedula]);
		        $id_report=strval(generate_id("reporte","id_reporte",true)); 
		        $src="reportes/Alquiler".strval($id_report)."_".$fecha_alquiler.".pdf";
	            $data_report=array("id_reporte"=>$id_report,"nombre_usuario"=>$_SESSION['username'],"CI_cliente"=>$cedula,"tipo"=>"alquiler","src_reporte"=>$src,"fecha"=>$fecha_alquiler);
		        add_data_dict("reporte",$data_report);
		        $h_actual=strval(date("H:i:s"));
                $data_reporteUser=array("id_reporte_usr"=>strval(generate_id("reporte_usuario","id_reporte_usr",true)),"nombre_usuario"=>$_SESSION['username'],"accion"=>"alquilar productos","fecha"=>$fecha_alquiler,"hora"=>$h_actual);		
                add_data_dict("reporte_usuario",$data_reporteUser);
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
			}
		}
        else{
           echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	       echo "<div id='error_msg2'><h2 id='error_text'>Cliente Inexistente</h2></div>";
	       echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
		}			
      }
	  else{
		  echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	      echo "<div id='error_msg2'><h2 id='error_text'>Fallo de Conexion</h2></div>";
	      echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
	  }
	}
	else{
	    echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Datos Incorrectos</h2></div>";
	    echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";	
	}
  }
  else{
	  	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	    echo "<div id='error_msg2'><h2 id='error_text'>Datos Incorrectos</h2></div>";
	    echo "<a class='boton2' href='alquilar_productos.php' >Aceptar</a>";
  }
?>
  
</div>

</center>

</body>

</html>