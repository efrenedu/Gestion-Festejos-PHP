<?php
  require_once __DIR__."/../../private/festejos/db_config.php";
  $msg="";
  if(count($_POST)<=0){
	  echo "Datos Insuficientes";
	  exit;
  }
  if(!isset($_POST['table']) || !isset($_POST['fields']) || !isset($_POST['num_fields']) || !isset($_POST['op1']) || !isset($_POST['op2'])){
	  echo "Datos Invalidos";
	  exit;
  }
  if(get_conexion()!="OK"){
	  echo "Error Conectando con el Servidor";
	  exit;
  }
  $table=$_POST['table'];
  $fields=$_POST['fields'];
  $num_fields=$_POST['num_fields'];
  $opcion1=$_POST['op1'];
  $opcion2=$_POST['op2'];
 
  $cond_data=null;	  
  if($table=="trabajador"){
	  $cond_data=array("conditions_Names"=>array("CI_trabaj"),"conditions_Values"=>array("000000"),"condition_Types"=>array("and"),"conditions_Verify"=>array("!="));	 	  
  }
 	
 $data=get_data($table,$fields,$cond_data);
 if($data["status"]=="Error"){
	 echo $data["message"];
	 exit;
 }
 $data=$data["message"];
 if(count($data)<=0){
	echo "No Registros Encontrados";
	exit;
 }
 $msg=":";
 for($i=0;$i<count($data);$i++){
	  $row="";
	  for($j=0;$j<count($data[$i]);$j++){
				if($j<count($data[$i])-1){
					$row=$row.$data[$i][$j].",";
				}
				else{
					$row=$row.$data[$i][$j];
				}
			
	  }
	  if($row!=""){
		  $msg=$msg.$row;
	  }
	  if($i<count($data)-1){
		  $msg=$msg.";"; 
	  }
  }
  $msg=$msg."|".$opcion1.",".$opcion2;  
  echo $msg;
?>