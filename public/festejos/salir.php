<?php  
/*Logout the User*/
session_start();
session_destroy(); 
header("location: loggin.php");
exit();
?>