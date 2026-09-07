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
$res_conex=get_conexion();
if (count($_POST)<=0) {
	echo "<div id='error_msg'><h2 id='error_text'>Faltan Datos</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
if(!isset($_POST["target"])){
	echo "<div id='error_msg'><h2 id='error_text'>Datos Invalidos</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}

if($res_conex!="OK"){
	echo "<div id='error_msg'><h2 id='error_text'>Error {$res_conex}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}

/*Show the Form for Respond to "Pregunta Secretas" of User to Modify Password  from Way "Forget Password"  */
$target=strtolower($_POST["target"]);	
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
$cond_data=array("conditions_Names"=>array("nombre_usuario","bloqueado"),"conditions_Values"=>array($target,"false"),"condition_Types"=>array("and","and"),"conditions_Verify"=>array("=","="));	 
    
$join_data=array();
$join_data["pregunta_secreta"]=array("query_field"=>array("numero","pregunta","respuesta"),"share_fields"=>array("field"=>"nombre_usuario","table_reference"=>"usuario"),"Conditions_join"=>null);
$dat_u=get_data("usuario",["nombre_usuario"],$cond_data,$join_data,true);
if($dat_u["status"]=="Error"){
	echo "<div id='error_msg'><h2 id='error_text'>Error{$dat_u['message']}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
	
}  
$dat_u=$dat_u["message"];
if(count($dat_u)<=0){
	echo "<div id='error_msg'><h2 id='error_text'>Usuario Inexistente ,Bloqueado o sin Preguntas Secretas</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
	
}  
$preguntas=array();
$respuestas=array();
$user_request=$dat_u[0]["nombre_usuario"];
for ($i=0;$i<count($dat_u);$i++){
	$preguntas[]=$dat_u[$i]["pregunta"];
	$respuestas[]=$dat_u[$i]["respuesta"];
}
$index1=rand(0,count($preguntas)-1);
$index2=$index1;
while($index2==$index1){
	 $index2=rand(0,count($preguntas)-1);
}
$p1=$preguntas[$index1];
$p2=$preguntas[$index2];
$path_keySecret_Recover=__DIR__."/../../private/festejos/secretToken_recoverPass.json";
if(!file_exists($path_keySecret_Recover)){
    echo "<div id='error_msg'><h2 id='error_text'>Error Obteniendo Datos de Token Para Recuperar Contraseñas del Servidor</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
$data_secretKey=json_decode(file_get_contents($path_keySecret_Recover),true);
$datos_session=["CI_trabaj"=>"None","Nivel_Acceso"=>"Undefined","Id_User"=>$user_request];
$dur_token=300;
$token_client=generate_tokenLogin($datos_session,$data_secretKey["Token"],$dur_token);
$_SESSION["Token_User"]=$token_client;	

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
			       

?>


</div>

</center>

</body>

</html>