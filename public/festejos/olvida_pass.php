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

/*Verif the User is Not Loggin*/

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
/*Show the Form for Respond to "Pregunta Secretas" of User to Modify Password  from Way "Forget Password"  */
if(validar_conexion()){
  if (count($_POST)>0) {
	  if(!isset($_POST["target"])){
		   echo "<div id='error_msg2'><h2 id='error_text'>Error de Data</h2></div>
		  <br><br><a href='loggin.php' class='boton1'>Volver</a>";
      }
	  else{
		 $target=strtolower($_POST["target"]);	 
		 if(id_exist("usuario","nombre_usuario",$target)==true){
			  $dat_u=get_data_dict("usuario",["nombre_usuario"],1,["bloqueado","nombre_usuario"],["false",$target]);
			  if(count($dat_u)>0){ 
			      $dat=get_data_dict("pregunta_secreta",["pregunta","respuesta","numero"],3,["nombre_usuario"],[$target]);
	              if(count($dat)>0){
				      $preguntas=array();
				      $respuestas=array();
				      for ($i=0;$i<count($dat);$i++){
				          $preguntas[]=$dat[$i]["pregunta"];
				          $respuestas[]=$dat[$i]["respuesta"];
				      }
				      $index1=rand(0,count($preguntas)-1);
				      $index2=$index1;
				      while($index2==$index1){
				             $index2=rand(0,count($preguntas)-1);
				      }
				      $p1=$preguntas[$index1];
				      $p2=$preguntas[$index2];
			          echo"<h2 id='title1'>Recuperar Contraseña</h2>
                      <br>
				      <form action='new_pass.php' method='post'>
				        <label  class='label_login'> Pregunta Secreta 1: ".$p1."</label>
                        <input type='hidden' class='input_login' name='pregunta1'  value='".$p1."'><br><br>
				        <label for='respuesta1' class='label_login'> Respuesta</label>
                        <input type='text' class='input_login' name='respuesta1' required='' placeholder='Respuesta'><br><br>
                        <hr style='color:blue;width:70%;height:1px;background-color:blue;'>
                        <label  class='label_login'> Pregunta Secreta 2: ".$p2."</label>
                        <input type='hidden' class='input_login' name='pregunta2' required='' value='".$p2."'><br><br>
                        <label for='respuesta2' class='label_login'> Respuesta</label>
                        <input type='text' class='input_login' name='respuesta2' required='' placeholder='Respuesta'><br><br>
                        <br>
                        <input type='submit' class='boton_login' name='Continuar' value='Continuar' style='margin-right:3%;'>
                        <input type='reset' class='boton_login' name='Limpiar' value='Limpiar' style='margin-left:4%;margin-bottom:2%;'><br>
                        <input type='button' class='boton_login' name='volver' value='Volver'  style='margin-left:25%' onclick='go_loggin()'>
				        <input type='hidden' name='target' value='".$target."'>
				      </form>";
			       }
				   else{
					  echo "<div id='error_msg2'><h2 id='error_text'>Error, El Usuario no tiene Preguntas Secretas</h2></div><br><br><a href='loggin.php' class='boton1'>Volver</a>";
				   }
			  }
			  else{
				 echo "<div id='error_msg2'><h2 id='error_text'>No se puede Recuperar la Contraseña de un Usuario Bloqueado</h2></div><br><br><a href='loggin.php' class='boton1'>Volver</a>";
			  }	 
		}
		 else{
			echo "<div id='error_msg2'><h2 id='error_text'>Usuario Inexistente</h2></div><image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br><a href='loggin.php' class='boton1'>Volver</a>";
		 }
      }
  }
  else{
	   echo "<div id='error_msg2'><h2 id='error_text'>Error de Data</h2></div><br><br><a href='loggin.php' class='boton1'>Volver</a>";
  }
}
else{
	 echo "<div id='error_msg2'><h2 id='error_text'>Fallo al conectar</h2></div><br><br><a href='loggin.php' class='boton1'>Volver</a>";
}

?>


</div>

</center>

</body>

</html>