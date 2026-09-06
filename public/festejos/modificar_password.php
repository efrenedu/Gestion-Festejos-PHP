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
require_once __DIR__."/../../private/festejos/db_config.php";
require_once __DIR__."/../../private/festejos/jwt.php";
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
if($res_conex!="OK"){
	
	echo "<div id='error_msg'><h2 id='error_text'>Error {$res_conex}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}

/*Modify The Password for a User by request "Forget Password" Option */
date_default_timezone_set('America/Caracas');
if (count($_POST)<=0) {
	echo "<div id='error_msg'><h2 id='error_text'>Faltan Datos</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
}
if(!isset($_POST["target"]) || !isset($_POST["pass1"]) ){
	echo "<div id='error_msg'><h2 id='error_text'>Datos Invalidos</h2></div>";
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
$new_pass=$_POST["pass1"];
$new_pass=password_hash($new_pass,PASSWORD_BCRYPT);
$cond_data=array("conditions_Names"=>array("nombre_usuario"),"conditions_Values"=>array($target),"condition_Types"=>array("and"),"conditions_Verify"=>array("="));	 
$join_data=array();
$join_data["intentos_usuario"]=array("query_field"=>array("num_intentos"=>"0","last_fecha"=>"...","last_hora"=>"..."),"share_fields"=>array("field"=>"id_intento","table_reference"=>"usuario"),"Conditions_join"=>null);
$res_update=update_data("usuario",array("contrasena"=>$new_pass),$cond_data,$join_data);
if($res_update["status"]=="Error"){
	echo "<div id='error_msg'><h2 id='error_text'>Error {$res_update['message']}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
	
}
$id_report=generate_id("reporte_usuario","id_reporte_usr");
if($id_report["status"]=="Error"){
	echo "<image src='images/incorrecto.png' width='120' height='120'/>";
	echo "<div id='error_msg2'><h2 id='error_text'>Error {$id_report['message']} </h2></div>";
	echo "<a class='boton2' href='paginaprincipal.php' >Aceptar</a>";  
    exit;
	
}
$id_report=$id_report["message"];
$data_reporte=array("id_reporte_usr"=>$id_report , "nombre_usuario"=>$target , "accion"=>"Recuperar Contrasena" ,"fecha"=>strval(date("d-m-Y")),"hora"=>strval(date("H:i:s")));
$res_add=add_data("reporte_usuario",$data_reporte,true,true);
if($res_add["status"]=="Error"){
	echo "<div id='error_msg'><h2 id='error_text'>Error {$res_add['message']}</h2></div>";
	echo "<image id='error_img' src='images/user_error.png' width='150' height='150'/><br><br>";
    echo "<a class='boton1' href='loggin.php'>Volver</a>";
    echo "</div>";
	exit;
	
}
echo"<h2 id='title1'>Contraseña modificada Exitosamente</h2>
     <br><a href='loggin.php' class='boton1'>Aceptar</a> "; 
		  
	  
 



?>


</div>

</center>

</body>

</html>