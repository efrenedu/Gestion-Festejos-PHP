<?php
  /*Verify if the Serial Indicated not exist in all products registers*/
  require "conexion_bd.php";
  $res=":NO";
  if(count($_POST)>0){
	   $serial=$_POST['serial'];
	   if(isset($serial)){
		   if(validar_conexion()){
			 $data=get_data_dict("producto",["serial"],1,-1,-1);
		     if(count($data)>0){
				 $found=false;
                 for($i=0;$i<count($data);$i++){
			   	    $s= $data[$i]["serial"];
				    if($s==$serial){
					    $found=true;
					    $i=count($data);
				    }
			     }
				 if($found==false){
					 $res=":OK"; 
				 }
		      }
			  else{
				  $res=":OK"; 
			  }	  
		  }		  
	   }
  }
  echo $res;
?>