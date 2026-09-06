
<?php  

/*Provide Methods for the Required Operations with data base*/

$host = "localhost";
$user_bd = "root";
$clave_bd = "";
$bd = "festejos";
$conexion=false;
$default_pass_user="Aa12345%";
$tablas_list=array("intentos_usuario","usuario","pregunta_secreta","reporte_usuario","trabajador","cliente","fiesta","producto","reporte","telefono","nombre","producto_alquilado","producto_reservado","personal_fiesta");

//list of fields of each table
$fields_tablas=array(
  array("id_intento","num_intentos","last_fecha","last_hora"),
  array("nombre_usuario" , "contrasena" , "permiso" ,"bloqueado","foto" , "id_intento","CI_trabaj"),
  array("id_pregunta" , "nombre_usuario" , "pregunta" ,"respuesta","numero" ),
  array("id_reporte_usr" , "nombre_usuario" , "accion" ,"fecha","hora" ),
  array("CI_trabaj" ,"id_nombre","cargo","turno","nivel_academico","edad","estatus","fecha_ingreso" ),
  array("CI_cliente" , "id_nombre", "estatus" ),
  array("id_fiesta" , "estatus"  ,"CI_cliente","fecha","hora","lugar","publico","tipo_fiesta","costo"),
  array("nombre_producto" ,"serial", "precio_alquiler" ,"precio","alquilable" ,"cantidad","estatus","cantidad_disponible" ),
  array("id_reporte" , "nombre_usuario"  ,"CI_cliente","tipo","src_reporte","fecha" ),
  array("numero_telefono","CI_trabaj","principal"),
  array("id_nombre","nombre","segundo_nombre","apellido","segundo_apellido"),
  array("id_alquilado","nombre_producto","formato","cantidad_alquilada","precio_unidad","CI_cliente","fecha_devolucion"),
  array("id_reservado","nombre_producto","formato","cantidad_reservada","precio_unidad","id_fiesta"),
  array("id_personal_fiesta","CI_trabaj","rol","id_fiesta"),

);


//field type of each column of a table: 0=> primary key -1=>Normal Field  text=>Foregein key (name of table contain the Primary Key for the FOREIGN_KEY)
$fields_types=array(
  array(0,-1,-1,-1),
  array(0 , -1 , -1 ,-1,-1,"intentos_usuario","trabajador"),
  array(0 , "usuario" , -1 ,-1,-1 ),
  array(0 , "usuario" , -1 ,-1,-1 ),
  array(0 , "nombre",-1,-1,-1,-1,-1,-1),
  array(0 , "nombre",-1 ),
  array(0 , -1  ,"cliente",-1,-1,-1,-1,-1,-1 ),
  array(0 , -1 ,-1,-1,-1,-1,-1,-1 ),
  array(0 , "usuario"  ,"cliente",-1,-1,-1 ),
  array(0,"trabajador",-1),
  array(0,-1,-1,-1,-1),
  array(0,"producto",-1,-1,-1,"cliente",-1),
  array(0,"producto",-1,-1,-1,"fiesta"),
  array(0,"trabajador",-1,"fiesta"),
);

/*Verify and Connect with the Data Base if is Neccesary.
  if the data Base non Exist build the Data Base
*/
function verificar_conexion(){
  global $conexion;
  global $bd;
  global $host;
  global $user_bd;
  global $clave_bd;
  
  $valor=true;
  if($conexion==false){
     $conexion=mysqli_connect($host, $user_bd, $clave_bd);
  }
  if (mysqli_connect_errno()) {
	 $valor=false;
     echo "<h1>Failed to connect to MySQL: " . mysqli_connect_error()."</h1>";
  }
  else{
	  $request="SHOW DATABASES;";
      $data_db=mysqli_query($conexion,$request);
      $existe=false;
      while($extraido_db=mysqli_fetch_array($data_db)){
        if($extraido_db[0]==$bd){
	      $existe=true;
        }
      }	
	  if($existe==false){
		 crear_db();
      }
	  mysqli_free_result($data_db);
	  mysqli_select_db($conexion,$bd); 
  }
  
  if($valor==true){
	return $conexion;
  }
  else{
	  return false;
  }
}

/*Encript the value and return it as a String*/
function encript($val){
	$Chars=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
	"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
	"1","2","3","4","5","6","7","8","9","0","@","%","-","_","?","¿","#","!","¡","=","$","&",);
	$offset=3;
	$next_cad="";
	for($i=0;$i<strlen($val);$i++){
		$c=$val[$i];
		 if(in_array($c,$Chars)){
           $index=array_search($c,$Chars);
		   $index=$index+$offset;
		   if($index>=count($Chars)){
			   $index=$index-count($Chars);  
		   }
		   $c=$Chars[$index];
       }
	   $next_cad=$next_cad.$c;
	}
	return $next_cad;
}

/*Desencript the Value an return it as a String*/
function desEncript($val){
	$Chars=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
	"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
	"1","2","3","4","5","6","7","8","9","0","@","%","-","_","?","¿","#","!","¡","=","$","&",);
	$offset=3;
	$next_cad="";
	for($i=0;$i<strlen($val);$i++){
		$c=$val[$i];
		 if(in_array($c,$Chars)){
           $index=array_search($c,$Chars);
		   $index=$index-$offset;
		   if($index<0){
			   $index=count($Chars)-abs($index);   
		   }
		   $c=$Chars[$index];
       }
		$next_cad=$next_cad.$c;
	}
	return $next_cad;
}

/*Verify if  Exist Connection with Data Base*/
function validar_conexion(){
  global $conexion;
  global $bd;
  global $host;
  global $user_bd;
  global $clave_bd;
  $valor=true;
  if($conexion==false){
     $conexion=mysqli_connect($host, $user_bd, $clave_bd);
	 mysqli_select_db($conexion,$bd); 
	 return true;
  }
  if (mysqli_connect_errno()) {
	 $valor=false;
     echo "<h1>Failed to connect to MySQL: " . mysqli_connect_error()."</h1>";
     return false;
  }
}

/*Build the Data Base*/
function crear_db(){
	
   global $conexion;
   global $bd;
   $request="CREATE DATABASE ".$bd;
   mysqli_query($conexion,$request);
   $order=array(10,4,0,1,9,5,6,7,8,2,3,11,12,13);
   for($i=0;$i<count($order);$i++){
	   Build_table($order[$i]);
   }
   
}

#build the request for the table
function Build_table($index_table){
   global $conexion;
   global $bd;
   global $tablas_list;
   global $fields_tablas;
   global $fields_types;
   if($index_table>=0 && $index_table<count($tablas_list)){
	   $request="CREATE TABLE ".$bd.".".$tablas_list[$index_table]." (";
	   $num_fields=count($fields_tablas[$index_table]);
	   for($i=0;$i<$num_fields;$i++){
		   $request=$request.$fields_tablas[$index_table][$i]." VARCHAR(100) ";
		   if($i==0){
			   $request=$request."PRIMARY KEY ";
		   }
		   if($i<$num_fields-1){
			   $request=$request.", ";
		   }  
	   }
	   for($j=0;$j<$num_fields;$j++){
		   $val_type=$fields_types[$index_table][$j];
		   if(is_string($val_type)){
			   $request=$request." , FOREIGN KEY(".$fields_tablas[$index_table][$j].")";
			   $request=$request." REFERENCES ".$val_type."(".$fields_tablas[$index_table][$j].")";     			   
		   }
	   }
	   $request=$request.");";
	   mysqli_query($conexion,$request);
   }
}

/*Set Default Values of Data Base if non Exist*/
function set_default(){
	
	 global $conexion;
     global $bd;
	 if(id_exist("trabajador","CI_trabaj" ,"000000")==false){
		$id_name="";
		$data_name_temp=get_data("nombre",["id_nombre"],1,["nombre","apellido"],["default","default"]);
	    if(count($data_name_temp)>0){
		   $id_name=$data_name_temp[0][0];
	    }
		else{
		   $dat_nombre=array(generate_id("nombre","id_nombre",false),"default","","default","");
		   add_data("nombre",$dat_nombre,count($dat_nombre));
		   $id_name=$dat_nombre[0];
		}
		$dat_trabaj=array("000000",$id_name,"default","default","default","0","default","00-00-00");
	    add_data("trabajador",$dat_trabaj,count($dat_trabaj));
	 }
	 if(id_exist("usuario","nombre_usuario","admin")==false){
	    
		$dat_intentos=array(generate_id("intentos_usuario","id_intento",false),"0","...","...");
		add_data("intentos_usuario",$dat_intentos,count($dat_intentos));
	    $dat=array("admin",encript("Admin123%"),"administrador","false","...",$dat_intentos[0],"000000");
	    add_data("usuario",$dat,count($dat));
		$pregs=["Color Favorito","Lugar de Nacimiento","Artista Favorito","Musica Favorita","Bebida Favorita","Comida Favorita"];
		$res=["Azul","Caracas","Saito Naoki","Instrumental","Leche","Hamburguesa"];
		for ($i=0;$i<6;$i++){
			$dat_preg=array(generate_id("pregunta_secreta","id_pregunta",true),"admin",$pregs[$i],$res[$i],strval($i+1));
	        add_data("pregunta_secreta",$dat_preg,count($dat_preg));
		}	
	}
}
/*add a Registrer(Row) to the Indicate Table*/
function add_data( $tabla,$dat,$num_values){
	
	global $conexion;
    global $bd;
	$request="INSERT INTO ".$bd.".".$tabla." VALUES (";
	for ($i=0; $i<$num_values;$i++){
		$request=$request."'".$dat[$i]."'";
		if($i<$num_values-1){
			$request=$request." , ";
		}
		else{
		    $request=$request." ); ";	
		}
	}
	mysqli_query($conexion,$request);
}


//add a Registrer to the Indicated Table from  Associative Array 
function add_data_dict($tabla,$dat){
	 global $conexion;
     global $bd;
	 global $tablas_list;
	 global $fields_tablas;
	 $fields=array();
	 for($j=0;$j<count($tablas_list);$j++){
		 if($tabla==$tablas_list[$j]){
			   $fields=$fields_tablas[$j];
		 }
	 }
	 if(count($fields)>0){
		$request="INSERT INTO ".$bd.".".$tabla." VALUES (";
	    $exito=true;
		for ($i=0; $i<count( $fields);$i++){
			$name=$fields[$i];
			$value="default";
			foreach($dat as $key=>$val){
				 if($key==$name){
					 $value=$val;
					 break;
				 }
			}
		    $request=$request."'".$value."'";
		    if($i<count( $fields)-1){
			    $request=$request." , ";
		    }
		    else{
		        $request=$request." ); ";	
		    }
			if($value=="default"){
				 $exito=false;
				 $i=count( $fields);
			 }
	    }
		if($exito==true){
	        mysqli_query($conexion,$request);
		}
	 }
}

/*Return True if the Table is Empty*/
function is_empty($tabla){
	global $conexion;
    global $bd;
	$request="SELECT COUNT(*) FROM ".$tabla.";";
	$data=mysqli_query($conexion,$request);
    if( mysqli_data_seek($data,0)){
        $row=mysqli_fetch_row($data);
	    if($row[0]=="0"){
		    return true;
	    }
	    else{
		   return false; 
	    }
    }
    else{ 
	   return false;
   }
}

/*Reset a Table Values*/
function reset_tabla($tabla_nombre){
	global $conexion;
    global $bd;
	$request="TRUNCATE TABLE ".$bd.".".$tabla_nombre.";";
	mysqli_query($conexion,$request);
}
/*Activate or Desactivate Foregein Keys Check*/
function activate_foraneos($valor){
	global $conexion;
    global $bd;
	$request="";
	if($valor==false){
		$request="SET FOREIGN_KEY_CHECKS = 0;";
	}
	else{
		$request="SET FOREIGN_KEY_CHECKS = 1;";
	}
	mysqli_query($conexion,$request);
}

/*Get the Data of a Table as Array*/
function get_data($tabla,$fields,$num_fields,$keys,$key_values){
	global $conexion;
    global $bd;
	$request="SELECT ";
	if($num_fields==-1){
      $request=$request."* FROM ".$bd.".".$tabla;
	}
	else{
		for ($i=0; $i<$num_fields;$i++){
		   if($i<$num_fields-1){
		      $request=$request.$fields[$i]." , ";
		   }
		   else{
			    $request=$request.$fields[$i]; 
		    }
	   }
	   $request=$request." FROM ".$bd.".".$tabla;
	}
	if($keys!=-1){
		 $request=$request." WHERE ";
		 for ($i=0 ;$i<count($keys);$i++){
			 if($i!=0){
				 $request=$request."AND "; 
		     }
			 $request=$request.$keys[$i]."='".$key_values[$i]."' ";
			 
		 }
	}
	$request=$request.";";
	$data=array();
	$respuesta=mysqli_query($conexion,$request);
	if($respuesta){	
	    while($extraido=mysqli_fetch_array($respuesta,MYSQLI_ASSOC)){
            $temp=array();
            foreach($extraido as $e){
			   $temp[]=$e;
		    }
            $data[]=$temp;
        }	
	    mysqli_free_result($respuesta);
	}
	return $data;
}

/*Get the Data of a Table as Associative Array*/
function get_data_dict($tabla,$fields,$num_fields,$keys,$key_values){
	global $conexion;
    global $bd;
	$request="SELECT ";
	if($num_fields==-1){
      $request=$request."* FROM ".$bd.".".$tabla;
	}
	else{
		for ($i=0; $i<$num_fields;$i++){
		   if($i<$num_fields-1){
		      $request=$request.$fields[$i]." , ";
		   }
		   else{
			    $request=$request.$fields[$i]; 
		   }
	   }
	   $request=$request." FROM ".$bd.".".$tabla;
	}
	if($keys!=-1){
		 $request=$request." WHERE ";
		 for ($i=0 ;$i<count($keys);$i++){
			 if($i!=0){
				 $request=$request."AND "; 
		     }
			 $request=$request.$keys[$i]."='".$key_values[$i]."' ";
			 
		 }
	}
	$request=$request.";";
	$data=array();
	$respuesta=mysqli_query($conexion,$request);
	if($respuesta){	
	    while($extraido=mysqli_fetch_array($respuesta,MYSQLI_ASSOC)){
           $data[]=$extraido;
      }	
	  mysqli_free_result($respuesta);
	}
	return $data;
}

/*Update a Register (Row) of a Table*/
function update_data($tabla,$fields,$values,$num_fields,$keys,$key_values){
	global $conexion;
    global $bd;
	$request="UPDATE ".$bd.".".$tabla." SET ";
	if($num_fields!=-1){
		for ($i=0; $i<$num_fields;$i++){
		   if($i<$num_fields-1){
		      $request=$request.$fields[$i]."='".$values[$i]."' , ";
		   }
		   else{
			    $request=$request.$fields[$i]."='".$values[$i]."'"; 
		    }
	   }
	}
	if($keys!=-1){
		 $request=$request." WHERE ";
		 for ($i=0 ;$i<count($keys);$i++){
			 if($i!=0){
				 $request=$request."AND "; 
		     }
			 $request=$request.$keys[$i]."='".$key_values[$i]."' "; 
		 }
	}
	$request=$request.";";
	mysqli_query($conexion,$request);
}

/*Delete a Register(Row) of a Table*/
function delete_data($tabla,$keys,$key_values){
	global $conexion;
    global $bd;
	$request="DELETE FROM ".$bd.".".$tabla;
	if($keys!=-1){
		 $request=$request." WHERE ";
		 for ($i=0 ;$i<count($keys);$i++){
			 if($i!=0){
				 $request=$request."AND "; 
		     }
			 $request=$request.$keys[$i]."='".$key_values[$i]."' ";
		 }
	}
	$request=$request.";";
	mysqli_query($conexion,$request);
}

//Return True if the Indicated Id Exist in the Table as Primary Key
function id_exist($tabla,$id, $id_value){
	$res=false;
	$data_temp=get_data($tabla,-1,-1,[$id],[$id_value]);
	if(count($data_temp)>0){
		 $res=true;
	}
	return $res;
}

/*Generate a Primary Key for a Table Based in the Number of Registers of it*/
function generate_id($tabla,$id_name,$profundo){
	global $bd;
	global $conexion;
	$q="SELECT COUNT(*) FROM ".$bd.".".$tabla.";";
	$res=mysqli_query($conexion,$q);
	$last_index=0;
	if($res){
		$data_temp=mysqli_fetch_row($res);
		if($data_temp){
			$last_index=$data_temp[0];
		}
	}
	if($profundo==false){
		return $last_index;
	}
	if($last_index>0 && $profundo==true){
		for ($i=0;$i<$last_index;$i++){
			if(id_exist($tabla,$id_name,$i)==false){
				return $i;
			}
		}
		return $last_index;
	}
	else{
		return 0;
	}
}

?>

