<?php

/*Write "Preguntas Secretas" Associated to a User*/
require_once __DIR__."/../../private/festejos/jwt.php";
require_once __DIR__."/../../private/festejos/db_config.php";  
function On_NoData(){
	return "<option value='' selected>Elegir</option>
               <option value='Artista Favorito'>Artista Favorito</option>
               <option value='Bebida Favorita'>Bebida Favorita</option>
               <option value='Color Favorito'>Color Favorito</option>
               <option value='Comida Favorita'>Comida Favorita</option>
               <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
               <option value='Lugar Favorito'>Lugar Favorito</option>
               <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
               <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
               <option value='Musica Favorita'>Musica Favorita</option>
               <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
               <option value='Pelicula Favorita'>Pelicula Favorita</option>
               <option value='Personaje Favorito'>Personaje Favorito</option>
                ";
}
function On_Error(){
	for ($i=0;$i<8;$i++){
	    $opcional="";
	    if($i<6){$opcional="* ";}
	    echo"<label for='p".strval($i+1)."' class='label_login'> Pregunta ".strval($i+1)." ".$opcional."</label>";
        echo"<select id='p".strval($i+1)."' name='p".strval($i+1)."' >";
	    echo"<option value='' selected>Elegir</option>
               <option value='Artista Favorito'>Artista Favorito</option>
               <option value='Bebida Favorita'>Bebida Favorita</option>
               <option value='Color Favorito'>Color Favorito</option>
               <option value='Comida Favorita'>Comida Favorita</option>
               <option value='Lugar de Nacimiento'>Lugar de Nacimiento</option>
               <option value='Lugar Favorito'>Lugar Favorito</option>
               <option value='Marca de Ropa Favorita'>Marca de Ropa Favorita</option>
               <option value='Mejor Amigo de Infancia'>Mejor Amigo de Infancia</option>
               <option value='Musica Favorita'>Musica Favorita</option>
               <option value='Nombre de Primer Amor'>Nombre de Primer Amor</option>
               <option value='Pelicula Favorita'>Pelicula Favorita</option>
               <option value='Personaje Favorito'>Personaje Favorito</option>
                ";
	    echo"</select>"; 
	    echo"<input id='r".strval($i+1)."' type='text' class='input_login' name='r".strval($i+1)."'  placeholder='respuesta'><br><br>";
	}
}
function set_code($with_session){
    if($with_session==true){
	    session_start();
    }
    $usuario="";
    if(count($_SESSION)>0){
       $path_keySecret=__DIR__."/../../private/festejos/secretToken.json";
       if(!file_exists($path_keySecret)){
	       echo On_Error();
		   exit;
       }    
       $data_secretKey=json_decode(file_get_contents($path_keySecret),true);
	   $token=$_SESSION["Token_User"];
	   $res=validar_token($token,$data_secretKey["Token"]);
	   if($res["Valido"]=="False"){
	    	echo On_Error();
			exit;
       }
	   $usuario=$res["Message"]["Id_usr"];

    }
    else{
        echo On_Error();
		exit;
    }
  $res_conex=get_conexion();
  if($res_conex!="OK" || $usuario==""){
	  echo On_Error;
	  exit;
  }
  $preguntas=array("Artista Favorito","Bebida Favorita","Color Favorito","Comida Favorita","Lugar de Nacimiento","Lugar Favorito","Marca de Ropa Favorita","Mejor Amigo de Infancia","Musica Favorita","Nombre de Primer Amor","Pelicula Favorita","Personaje Favorito");
  for ($i=0;$i<8;$i++){
	  $opcional="";
	  $respuesta="";
	  $cond_data=array("conditions_Names"=>array("nombre_usuario","numero"),"conditions_Values"=>array($usuario,strval($i+1)),"condition_Types"=>array("and","and"),"conditions_Verify"=>array("=","="));	 	  		

	  $preg_data=get_data("pregunta_secreta",array("pregunta","respuesta"),$cond_data,null,true);
	  if($i<6){$opcional="* ";}
	  echo"<label for='p".strval($i+1)."' class='label_login'> Pregunta ".strval($i+1)." ".$opcional."</label>";
      echo"<select id='p".strval($i+1)."' name='p".strval($i+1)."' >";     
	  if($preg_data["status"]=="Error"){
		  echo On_NoData();
		  echo"</select>";
	      echo"<input id='r".strval($i+1)."' type='text' class='input_login' name='r".strval($i+1)."'  placeholder='respuesta'><br><br>";
	      continue; 
	  }
	  $preg_data=$preg_data["message"];
	  if(count($preg_data)<=0){
		  echo On_NoData();
		  echo"</select>";
	      echo"<input id='r".strval($i+1)."' type='text' class='input_login' name='r".strval($i+1)."'  placeholder='respuesta'><br><br>";
	      continue;
	  }
	  $found=false;
	  $index=-1;
	  for ($j=0;$j<count($preguntas);$j++){
		  if($preguntas[$j]==$preg_data[0]["pregunta"]){
				$found=true;
				$index=$j;
				$respuesta=$preg_data[0]["respuesta"];
		  }
	  }
      if($found==false){
		  echo On_NoData();
		  echo"</select>";
	      echo"<input id='r".strval($i+1)."' type='text' class='input_login' name='r".strval($i+1)."'  placeholder='respuesta'><br><br>";
	      continue;
		       
	  }
	  echo"<option value=''>Elegir</option>";
	  for ($j=0;$j<count($preguntas);$j++){
		   $selected="";
		  if($j==$index){$selected="selected";}
		  echo"<option value='".$preguntas[$j]."' ".$selected.">".$preguntas[$j]."</option>";
	  }
	  echo"</select>";
	  if($respuesta==""){
		  echo"<input id='r".strval($i+1)."' type='text' class='input_login' name='r".strval($i+1)."'  placeholder='respuesta'><br><br>";
	  }
	  else{
		  echo"<input id='r".strval($i+1)."' type='text' class='input_login' name='r".strval($i+1)."'  placeholder='respuesta' value='".$respuesta."'><br><br>";
	 
	  }  
  }
  
}

set_code(true);
?>

