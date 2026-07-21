<?php
  #return the user Access Level
  session_start();
  if(count($_SESSION)>0){
	  echo $_SESSION['acceso_user'];
  }
  else{
	 echo "."; 
  }

?>