<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Inicio de sesion</title>
	<link rel="stylesheet" type="text/css" href="style_session.css">
    <script type="text/javascript" src="jquery-3.1.1.min.js"></script>
	<script type="text/javascript" src="script_festejos.js"></script>
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

/*Veify the User si not Loggin*/
require "conexion_bd.php";
session_start();
if(count($_SESSION)>0){
	$usuario = $_SESSION['username'];
    $nivel=$_SESSION['acceso_user'];
    if (isset($usuario)) {
	   header("location: paginaprincipal.php");
       exit();
   }
}

/*Valid the Responses to "Preguntas Secretas" of User to Modify Password from Way "Forget Password" 
  if is valid Write the Html Form to Modify Password*/
  
if(validar_conexion()){
   if(count($_POST)>0) {
	  if(!isset($_POST["target"]) || !isset($_POST["respuesta1"]) || !isset($_POST["respuesta2"]) || !isset($_POST["pregunta1"]) || !isset($_POST["pregunta2"])){
		   echo "<div id='error_msg2'><h2 id='error_text'>Error de Data </h2></div>
		   <a href='loggin.php' class='boton1'>Volver</a>
		   ";   
	  }
	  else{
		  $target=$_POST["target"];
		  if(id_exist("usuario","nombre_usuario",$target)==true){
			  $valido=false;
			  $p1=$_POST["pregunta1"];
			  $r1=strtolower($_POST["respuesta1"]);
			  $p2=$_POST["pregunta2"];
			  $r2=strtolower($_POST["respuesta2"]);
			  if(count(get_data_dict("pregunta_secreta",["id_pregunta"],1,["pregunta","respuesta"],[$p1,$r1]))>0){
			     if(count(get_data_dict("pregunta_secreta",["id_pregunta"],1,["pregunta","respuesta"],[$p2,$r2]))>0){
			        $valido=true;
				 }
			  }
			  if($valido==false){
			      echo "<div id='error_msg2'><h2 id='error_text'>Respuestas Incorrectas</h2></div>
				  <br><br><a href='loggin.php' class='boton1'>Volver</a>
				  ";
			  }
			  else{ echo"<h2 id='title1'>Recuperar Contraseña</h2>
                   <br>			  
				   <form action='modificar_password.php' method='post' onsubmit='verificar_pass(event)' >
				     <label for='pass' class='label_login'> Password</label>
                     <input type='password' id='pass1' class='input_login' name='pass1' required placeholder='Password' style='margin-left:10%'><br><br>
				     <label for='pass2' class='label_login'> Repita el Password</label>
                     <input type='password' id='pass2' class='input_login' name='pass2' required='' placeholder='Repita el Password'><br><br>
                     <input type='hidden' name='target' value='".$target."'>
				     <div id='error_msg' style='display:none'>
				        <p style=style='color:white;'>por favor repita la contraseña correctamente</p>
                     </div>
				     <div id='error_msg2' style='display:none'><p style='color:white;'>La contraseña debe contener almenos una letra mayuscula, una letra minuscula, un numero, un caracter especial y tener una lngitud de 8 caracteres</p></div>
				     <br>
                     <input type='submit' class='boton_login' name='Finalizar' value='Finalizar' style='margin-right:3%;padding:1%'>
                     <input type='reset' class='boton_login' name='Limpiar' value='Limpiar' style='margin-left:4%;margin-bottom:1%'>
                     <br>
				     <input type='button' class='boton_login' name='boton' value='volver' style='margin-left:23%' onclick='go_loggin()'>
				   </form>";
			  }
		  }
		  else{
			  echo "<div id='error_msg2'><h2 id='error_text'>Usuario Inexistente</h2></div><image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br><a href='loggin.php' class='boton1'>Volver</a>";
		  }
	  }
  }
  else{
	    echo "<div id='error_msg2'><h2 id='error_text'>Error de Data </h2></div>
		<br><br><a href='loggin.php' class='boton1'>Volver</a>
		"; 
  }
}
else{
	  echo "<div id='error_msg2'><h2 id='error_text'>Error de Conexion</h2></div>
	  <a href='loggin.php' class='boton1'>Volver</a>
	  ";  
}

?>


</div>

</center>

</body>

</html>