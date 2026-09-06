<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,height=device-height ,initial-scale=1"/> 
	<title>Inicio de Sesion</title>
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
/*verify if the user have session open*/
require_once __DIR__."/../../private/festejos/jwt.php";
session_start();

if(count($_SESSION)>0){
	 $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
	 if(!file_exists($path_keySecret)){
	     exit;
     }
	 $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="True"){
		$_SESSION['lastPage_user']="loggin.php";  
		header("location: paginaprincipal.php");  
	 }
  
}
?>
  <h2 id="title1">Iniciar Sesion</h2><br>
  <form action="manejador_sesiones.php" method="post">
    <label for="nombre" class="label_login" style="margin-right:2%">Usuario</label>
    <input type="text" id="user" class="input_login" name="nombre" required="" placeholder="Ingresar Usuario"><br><br>
    <label for="contrasena" class="label_login"> Contraseña</label>
    <input type="password" class="input_login" name="contrasena" required placeholder="Ingresar Contraseña"><br>
    <div>
      <div style='margin-left:40%'>
         <p><a id="olvida_pass" href="#" onclick="send_target()">Olvide la Contraseña</a></p>
      </div>
    </div>
    <br>
    <input type="submit" class="boton_login" name="Procesar" value="Iniciar Sesion">
    <input type="reset" class="boton_login" name="Limpiar" value="Limpiar">
  </form>
  <form id="second_form" action="olvida_pass.php" method="post">
     <input type="hidden" id="target" name="target" value="">
  </form>
</div>
</center>

</body>

</html>