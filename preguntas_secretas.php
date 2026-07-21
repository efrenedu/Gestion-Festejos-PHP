<?php

/*Write "Preguntas Secretas" Associated to a User*/

require "conexion_bd.php";
function set_code($with_session){

  $usuario="";
  if($with_session==true){
	session_start();
  }
  if(count($_SESSION)>0){
    $usuario = $_SESSION['username'];
  }
  if(validar_conexion() && $usuario!=""){
    $preguntas=array("Artista Favorito","Bebida Favorita","Color Favorito","Comida Favorita","Lugar de Nacimiento","Lugar Favorito","Marca de Ropa Favorita","Mejor Amigo de Infancia","Musica Favorita","Nombre de Primer Amor","Pelicula Favorita","Personaje Favorito");
    for ($i=0;$i<8;$i++){
	  $opcional="";
	  $respuesta="";
	  $preg_data=get_data("pregunta_secreta",["pregunta","respuesta"],2,["nombre_usuario","numero"],[$usuario,strval($i+1)]);
	  if($i<6){$opcional="* ";}
	  echo"<label for='p".strval($i+1)."' class='label_login'> Pregunta ".strval($i+1)." ".$opcional."</label>";
      echo"<select id='p".strval($i+1)."' name='p".strval($i+1)."' >";     
	  if(count($preg_data)>0){
		  $found=false;
		  $index=-1;
		  for ($j=0;$j<count($preguntas);$j++){
			  if($preguntas[$j]==$preg_data[0][0]){
				$found=true;
				$index=$j;
				$respuesta=$preg_data[0][1];
			 }
	      }
		  if($found==false){
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
		  }
		  else{
			  echo"<option value=''>Elegir</option>";
			  for ($j=0;$j<count($preguntas);$j++){
				  $selected="";
				  if($j==$index){$selected="selected";}
			      echo"<option value='".$preguntas[$j]."' ".$selected.">".$preguntas[$j]."</option>";
	          }
		  }
	  }
	  else{
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
  else{
      for ($i=0;$i<8;$i++){
	     $opcional="";
	    if($i<5){$opcional="* ";}
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
}

set_code(true);
?>

