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
require_once __DIR__."/../../private/festejos/db_config.php";
require_once __DIR__."/../../private/festejos/jwt.php";
$usuario="";
session_start();
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
	 if($res["Valido"]!="False"){
		header("location: paginaprincipal.php");
		exit;
    }
}

if (count($_POST)<=0) {
	echo "<div id='error_msg'><h2 id='error_text'>Faltan Datos</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
$requerids_params=array("target","pregunta1","pregunta2","respuesta1","respuesta2");
foreach($requerids_params as $param){
	if(!isset($_POST[$param])){
		echo "<div id='error_msg'><h2 id='error_text'>Datos Invalidos</h2></div>";
	    echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
        echo "<a class='boton1' href='loggin.php'>Volver</a>";
        echo "</div>";
	    exit;
	}
}

$res_conex=get_conexion();
if($res_conex!="OK"){
	echo "<div id='error_msg'><h2 id='error_text'>Error {$res_conex}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
$target=$_POST["target"];
$exist_usr= id_exist("usuario","nombre_usuario",$target);
if($exist_usr["status"]=="Error"){
	echo "<div id='error_msg'><h2 id='error_text'>Error {$exist_usr}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
$exist_usr=$exist_usr["message"];
if($exist_usr!="True"){
	echo "<div id='error_msg'><h2 id='error_text'>Usuario Inexistente</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
$valido=false;
$p1=$_POST["pregunta1"];
$r1=strtolower($_POST["respuesta1"]);
$p2=$_POST["pregunta2"];
$r2=strtolower($_POST["respuesta2"]);
$cond_data=array("conditions_Names"=>array("nombre_usuario","pregunta","respuesta"),"conditions_Values"=>array($target,$p1,$r1),"condition_Types"=>array("and","and","and"),"conditions_Verify"=>array("=","=","="));	 
$cond_data2=array("conditions_Names"=>array("nombre_usuario","pregunta","respuesta"),"conditions_Values"=>array($target,$p2,$r2),"condition_Types"=>array("and","and","and"),"conditions_Verify"=>array("=","=","="));	 
$temp_dat= get_data("pregunta_secreta",array("id_pregunta"),$cond_data,null,true);
if($temp_dat["status"]=="Error"){
	echo "<div id='error_msg'><h2 id='error_text'>Error{$temp_dat['message']}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
$temp_dat=$temp_dat["message"];
if(count($temp_dat)<=0){
	echo "<div id='error_msg'><h2 id='error_text'>Respuestas no Coinciden</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
$temp_dat= get_data("pregunta_secreta",array("id_pregunta"),$cond_data2,null,true);
if($temp_dat["status"]=="Error"){
	echo "<div id='error_msg'><h2 id='error_text'>Error{$temp_dat['message']}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
$temp_dat=$temp_dat["message"];
if(count($temp_dat)<=0){
	echo "<div id='error_msg'><h2 id='error_text'>Respuestas no Coinciden</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
echo"<h2 id='title1'>Recuperar Contraseña</h2>
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

?>


</div>

</center>

</body>

</html>