<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Modificar Password</title>
	<link rel="stylesheet" type="text/css" href="style_session.css">  
</head>
<body>
<center>
<br>
<div id="titleBox">
<image id="logo" src="images/globos.png" width="100" height="100"/>
<h1 id="title">Logistica & Festejos Premier</h1>
</div>
<div id="content1">
<?php 

/*Verify the User is not Loggin*/
require "conexion_bd.php";
session_start();
if(count($_SESSION)>0){
	$usuario = $_SESSION['username'];
    $nivel=$_SESSION['acceso_user'];
    if (isset($usuario)) {
	   header("location: paginaprincipal.php");
   }
}

/*Modify The Password for a User by request "Forget Password" Option */
if(validar_conexion()){
  date_default_timezone_set('America/Caracas');
  if (count($_POST)>0) {
	  if(!isset($_POST["target"]) || !isset($_POST["pass1"])){
		   
		   echo "<div id='error_msg2'><h2 id='error_text'>Error de Data </h2></div>
		   <a href='loggin.php' class='boton1'>Aceptar</a>
		   ";   
	  }
	  else{
		  $target=$_POST["target"];
		  if(id_exist("usuario","nombre_usuario",$target)==true){
			  $new_pass=$_POST["pass1"];
			  $new_pass=encript($new_pass);
			  update_data("usuario",["contrasena"],[$new_pass],1,["nombre_usuario"],[$target]);
			  $data_intento=get_data_dict("usuario",["id_intento"],1,["nombre_usuario"],[$target]);
			  if(count($data_intento)>0){
				  update_data("intentos_usuario",["num_intentos","last_fecha","last_hora"],["0","...","..."],3,["id_intento"],[$data_intento[0]["id_intento"]]);
			  }
			  $data_reporte=array("id_reporte_usr"=>generate_id("reporte_usuario","id_reporte_usr",true) , "nombre_usuario"=>$target , "accion"=>"Recuperar Contrasena" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
			  add_data_dict("reporte_usuario",$data_reporte);
			  echo"<h2 id='title1'>Contraseña modificada Exitosamente</h2>
                  <br><a href='loggin.php' class='boton1'>Aceptar</a>
				  "; 
		  }
		  else{
			  echo "<div id='error_msg2'><h2 id='error_text'>Usuario Inexistente</h2></div><image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br><a href='loggin.php' class='boton1'>Aceptar</a>";
		  }
	  }
  }
  else{
	    echo "<div id='error_msg2'><h2 id='error_text'>Error de Data </h2></div>
		<br><br><a href='loggin.php' class='boton1'>Aceptar</a>
		";  
  }
}
else{
	  echo "<div id='error_msg2'><h2 id='error_text'>Error de Conexion</h2></div>
	  <a href='loggin.php' class='boton1'>Aceptar</a>
	  ";  
}


?>


</div>

</center>

</body>

</html>