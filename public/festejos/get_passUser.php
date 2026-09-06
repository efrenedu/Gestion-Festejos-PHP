<?php

session_start();
$pass="";
require "conexion_bd.php";

/*get the desencripted pass of a User*/
if(count($_SESSION)>0){
  $usuario = $_SESSION['username'];
  $nivel=$_SESSION['acceso_user'];
  if(isset($usuario)) {
	if(validar_conexion()){
       $dat_usr=get_data_dict("usuario",["contrasena"],1,["nombre_usuario"],[$usuario]);
       if(count($dat_usr)>0){
           $pass=$dat_usr[0]["contrasena"];
           $pass=desEncript($pass);
       }	
    }
  }
}
echo ":".$pass;

?>