<?php
  /*Verify if the Serial Indicated not exist in all products registers*/
  require_once __DIR__."/../../private/festejos/db_config.php";
    
  $res=":NO";
  if(count($_POST)<=0){
	 echo $res;
     exit;	 
  }
  if(!isset($_POST['serial'])){
	 echo $res;
     exit; 
	  
  }
  $serial=$_POST['serial'];
  $res_conex=get_conexion();
  if($res_conex!="OK"){
	    echo $res;
        exit; 
  }
  $data=get_data("producto",["serial"],null,null,true);
  if($data["status"]=="Error"){
	 echo $res;
     exit; 
  }
  $data=$data["message"];
  if(count($data)<=0){
	  echo ":OK";
      exit; 
  }
  $found=false;
  for($i=0;$i<count($data);$i++){
		$temp_serial= $data[$i]["serial"];
		if($temp_serial==$serial){
			$found=true;
			$i=count($data);
		}
  }
  if($found==false){
	  $res=":OK";  
  }
  echo $res;
?>