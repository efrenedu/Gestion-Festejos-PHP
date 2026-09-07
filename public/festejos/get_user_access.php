<?php
  #return the user Access Level
  
require_once __DIR__."/../../private/festejos/jwt.php";
session_start();
if(count($_SESSION)>0){
     $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
     if(!file_exists($path_keySecret)){
	     echo ".";
	     exit;
     }
     $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	 $token=$_SESSION["Token_User"];
	 $res=validar_token($token,$data_secretKey["Token"]);
	 if($res["Valido"]=="False"){
		echo ".";
		exit;
    }
	$permiso=$res["Message"]["Acceso"];
	echo $permiso;
	

}
else{
	 echo ".";
	
}

  

?>