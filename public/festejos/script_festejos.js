var myPieChart=null;   //for build Charts



/*User Confirm Password in Dialog Window of Password Input*/
function ConfirmModalPass(){
	$("#modalPass").css("display","none");
	var pass_indicado=$("#passRequired").val();
	var data_send={"password_required":pass_indicado};
	if(pass_indicado!=""){
	   var url="verify_pass.php";
	   $.ajax({
          method: "POST",
          url: url,
          data:data_send,
          success: function(data) {
			  if(data=="False"){
				  $("#error_msgPass").css("display","block");
			      return;
			  }
			  var dat_split=data.split(":");
			  if(dat_split[1]!="OK"){
				  
				  $("#error_msgPass").css("display","block");
			      return;
			  }
			 ShowModal();
             $("#error_msg2").css("display","none"); 
		     $("#error_msg").css("display","none");
             $("#error_msgPass").css("display","none");
           }
         });
	}	
}



/*Close Dialog Window for Password Input*/
function closeModalPass(){
	$("#modalPass").css("display","none");
	$("#error_msgPass").css("display","none");
	$("#accion").val("");	
}

/*Show Dialog Windows for password Input*/
function showModalPass(accion){
	$("#error_msg2").css("display","none"); 
	$("#error_msg").css("display","none");
	$("#passRequired").val("");
	$("#modalPass").css("display","flex");
	$("#error_msgPass").css("display","none");
	if(accion!="nuevo" && accion!="BD" && accion!="edit info" && accion!="Change_Admin" ){
		$("#accion").val(accion);
	}    
}

/*show Dialog Windows for Conform Action*/
function ShowModal(){ 
    $("#Modal-confirm").css("display","flex");
}

/*Close Dialog Windows for Confirm Action*/
function closeModal(){
	$("#Modal-confirm").css("display","none");
	$("#accion").val("");
	
}

/*Show Dialog Windows by Name*/
function showModal_byName(name){
	$("#"+name).css("display","flex");
}

/*Close Dialog windows by Name*/
function closeModal_byName(name){
	$("#"+name).css("display","none");
	if(name=="Modal-confirm"){
	   select_trabajador();
	}
}

/*Trigger the Action of Form Node on Succes Confirm the Dialog Windows*/
function ConfirmModal(){	
	 $("#formu").submit();
}

/*Verify if the Indicated String is a Number(Integer or Float)*/
function is_number(cad){
	var res=true;
	for (var i=0;i<cad.length;i++){
	   var code=cad.charCodeAt(i);
       if((code>=48 && code<=57)==false ){
		  if(cad[i]!="." && cad[i]!=","){
			  res=false;
		      i=cad.length;
		  }
		  else{
			  if((i>0 && i<cad.length-1)==false){
				   res=false;
		           i=cad.length;
			    }
			  
		  }
	   }
	}
	if(cad.length==0){
		res=false;
	}
	return res;
}

/*Verify if the Indicated String Simple is a Text*/
function is_text(cad,white_spaces){
	var res=true;
	for (var i=0;i<cad.length;i++){
	   var code=cad.charCodeAt(i);
       var minuscula=false;
	   var mayuscula=false;
	   var w_space=false;
	   if(cad[i]==" "){
		   w_space=true;
	   }
       if((code>=97 && code<=122)==true ){
		  minuscula=true;
	   }
	   else if((code>=65 && code<=90)==true ){
		  mayuscula=true;
	   }
	   if(minuscula==false && mayuscula==false){
		    if((w_space==true &&white_spaces==true)==false){
			    res=false;
			    i=cad.length;
			}	
	   }
	}
	if(cad.length==0){
		res=false;
	}
	return res;
}

/*Select a Row of Table the User for the User Gestion Panel*/
function select_row(e){
	var in_modific=$("#modific_user").css('display');
	if(in_modific=="none"){
		$(e).css("background","rgb(251,244,202)");
		var def_color="rgb(255,250,239)";
		var id_nodo=$(e).attr('id');
		var valor=$(e).find('td:first-child').text();;
		$("#selected_row").val(valor);
		for(var i=0;i<10;i++){
			var search_val="row"+i.toString();
			if(search_val!=id_nodo){
				search_val="#"+search_val;
				$(search_val).css("background",def_color);
			
				}
			}
	    }		
}

/*Change the Cursor Icon to 'Hand' When the Mouse Enter in the Table with the Users List of User Gestion Panel*/
function enter_table(){
	var in_modific=$("#modific_user").css('display');
	if(in_modific=="none"){
		document.body.style.cursor = 'pointer';
	}
}
/*Change the Cursor Icon to 'Default' When the Mouse Exit from the Table with the Users List of User Gestion Panel*/
function exit_table(){
	document.body.style.cursor = 'default';
}

/*Validate the fields Required for Worker Register
  if is valid Process the Register
*/
function validar_trabajadores(){
	
	closeModal_byName("ModalMessage");
	closeModal_byName("ModalMessage2");
	closeModal_byName("ModalMessage3");
	closeModal_byName("ModalMessage4");
	var valido=0;
	var data_name=[];
	var data_telefs=[];
	$("#telefonos_list").val("");
	var valor=$("#cedula").val();
	var update=false;
	var t_selected=$("#trabajadores").find("option:selected").val();
	if(t_selected!=""){
		$("#accionRegistro").val("Update");
		update=true;
	}
	else{
	   $("#accionRegistro").val("Registrar");
	}
	if(update==false){
	    var cedulas_registradas=document.getElementById("trabajadores").options;
	    if(cedulas_registradas.length>0){
		    for (var k=0;k<cedulas_registradas.length;k++){
			      if(cedulas_registradas[k].value==valor){
				       valido=-9;
				       k=cedulas_registradas.length;
			      }
		    }   
	    }
	}
	if(valido==0){
        if(valor.length<7){
		    valido=-2;
	    }
	    else{
		     if(is_number(valor)==false){
		         valido=-2;
		    }
	   }
	}
	if(valido==0){
		valor=$("#nombre").val();
		if(valor.indexOf(" ")!=-1){
			valor=valor.split(" ");
			if(valor.length>2){valido=-3;}
			else if(valor[0].length<3 || valor[1].length<3){
				valido=-3;
			}
			else if(is_text(valor[0],false)==false || is_text(valor[1],false)==false){
                valido=-3;
			}
            else{
               data_name["nombre"]=valor[0];
			   data_name["segundo_nombre"]=valor[1];
			}				
		}
		else{
			if(valor.length<3){
		        valido=-3;
			}
			else if(is_text(valor,false)==false){
			 valido=-3;
		   }
		   else{
			   data_name["nombre"]=valor;
			   data_name["segundo_nombre"]="";
		   }
	    }
		if(valido==0){
			valor=$("#apellido").val();
		    if(valor.indexOf(" ")!=-1){
			    valor=valor.split(" ");
				if(valor.length>2){valido=-4;}
			    else if(valor[0].length<3 || valor[1].length<3){
				    valido=-4;
			    }
			    else if(is_text(valor[0],false)==false || is_text(valor[1],false)==false){
                    valido=-4;
			    }
                else{
                   data_name["apellido"]=valor[0];
			       data_name["segundo_apellido"]=valor[1];
			    }				
		   }
		    else{
			    if(valor.length<3){
		            valido=-4;
			    }
			    else if(is_text(valor,false)==false){
			       valido=-4;
		        }
		        else{
			       data_name["apellido"]=valor;
			       data_name["segundo_apellido"]="";
		       }
	       }	
		}
	}
	if(valido==0){
		valor=$("#edad").val();
		if(is_number(valor)==false){
			  valido=-5;
		}
		if(valido==0){
			var ops=document.getElementById("telefonos").options;
		    if(ops.length>0){
				if(ops[0].value!=""){
					for(var i=0;i<ops.length;i++){
						 var principal="false";
						 if(i==0){principal="true";}
						 var telef=[];
						 telef["numero_telefono"]=ops[i].value;
						 telef["principal"]=principal;
						 data_telefs.push(telef); 
					}
				}
				else{
					valido=-6;
				}
			}
			else{
				valido=-6;
			}
			
		}
	}
	if(valido==0){
		 valor=$("#nivel_academico").find("option:selected").val();
         if(valor==""){
		        valido=-7;
	     }
		if(valido==0){
			 valor=$("#carg").find("option:selected").val();
            if(valor==""){
		        valido=-1;
	        }
		   
		}
		if(valido==0){
			valor=$("#turno").find("option:selected").val();
            if(valor==""){
		        valido=-8;
	        }
		}
	}
    if(valido==0){
		    var telefonos_val="";
			for(var j=0;j<data_telefs.length;j++){
				var numero=data_telefs[j]["numero_telefono"];
				var principal=data_telefs[j]["principal"];
				telefonos_val=telefonos_val+(numero+","+principal)+";";
			}
		    $("#telefonos_list").val(telefonos_val);
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form8").css("display","none");
			$("#error_form9").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");
			$("#carg").prop("disabled",false);
			//valid fields wait for confirm from User
			ShowModal();
	}
	else{
		//Show Error to User
		if(valido==-1){
		    $("#error_form").css("display","block");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");
		}
		else if(valido==-2){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","block");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");
		}
		else if(valido==-3){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","block");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");
		}
		else if(valido==-4){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","block");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");
		}
		else if(valido==-5){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","block");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");
		}
		else if(valido==-6){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","block");
			$("#error_form7").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");	
		}
		else if(valido==-7){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","block");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");
		}
		else if(valido==-8){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form10").css("display","block");
			$("#error_form11").css("display","none");
		}
		else if(valido==-9){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","block");
		}
	}
}


/*Validate if The Client Fields For Register Client are Valids
  and return the valid value as Integer
*/
function validar_clientes(){	
	var valido=0;
	var valor=$("#client_cedula").val();
    if(valor.length<7){
		valido=-1;
	}
	else if(is_number(valor)==false){
		valido=-1;
	}
	else{
		valor=$("#client_names").val();	
		if(valor.indexOf(" ")!=-1){
			valor=valor.split(" ");
			if(valor.length>2){valido=-2;}
			else if(valor[0].length<3 || valor[1].length<3){
				valido=-2;
			}
			else if(is_text(valor[0],false)==false || is_text(valor[1],false)==false){
                valido=-2;
			}
		}
		else{
			if(valor.length<3){
		        valido=-2;
			}
			else if(is_text(valor,false)==false){
			 valido=-2;
		   }
	    }
		if(valido==0){
			valor=$("#client_apellidos").val();
		    if(valor.indexOf(" ")!=-1){
			    valor=valor.split(" ");
			    if(valor.length>2){valido=-3;}
			    else if(valor[0].length<3 || valor[1].length<3){
				     valido=-3;
			    }
			    else if(is_text(valor[0],false)==false || is_text(valor[1],false)==false){
                    valido=-3;
			   }			
		    }
		    else{
			    if(valor.length<3){
		           valido=-3;
			    }
			    else if(is_text(valor,false)==false){
			      valido=-3;
		        }
	        }  
		}
	}
    return valido;	
}

/*Validate the Field for Register Product Page
  if is Valid Process the Register
  */
function validar_producto(){
	var valido=0;
	var update=false;
	var t_selected=$("#lista_productos").find("option:selected").val();
	if(t_selected!=""){
		$("#accion_form").val("Update");
		update=true;
	}
	else{
	   $("#accion_form").val("Registro");
	}
	var valor=$("#producto").val();
	if(valor.length<3){valido=-1;}
	else if(is_text(valor,true)==false){
		valido=-1;
	}
	if(valido==0 && update==false){
		var listado=document.getElementById("lista_productos").options;
		if(listado.length>0){
			for(var i=0;i<listado.length;i++){
				if(valor==listado[i].value){
					valido=-5;
					i=listado.length;
				}
			}
		}
	}
	if(valido==0){		
	   valor=$("#serial").val();
	   if(valor.length<3){
		   valido=-4;
	   }
	   for(var i=0;i<valor.length;i++){
		   if(valor[i]==" "){
			   valido=-4;
			   i=valor.length;
		   }
	   }
	   if(valido==0){
		  valor=$("#precio_alquiler").val();
		  var alquilabl=document.getElementById("alquilable_op1")
		  var is_alquilable=false;
		  if(alquilabl.checked==true){
			  is_alquilable=true;
		  }
	      if(is_number(valor)==false){
		     valido=-6;
	      }
	      else {
			 var valor_num=parseFloat(valor)
			 if(valor_num<0.0){
				 valido=-6;
			 }
			 else if(valor_num>1.0){
				 valido=-6;
			 }
			 else if(valor_num==0.0){
				 if(is_alquilable){
					 valido=-6;
				 }
			 }
	      }
		  
	   }
	   if(valido==0){
		  valor=$("#precio").val();
	      if(is_number(valor)==false){
		     valido=-2;
	      }
	      else if(parseFloat(valor)<=0){
		     valido=-2;
	     }
	   }
	   if(valido==0){
		   valor=$("#cantidad").val();
	      if(is_number(valor)==false){
		     valido=-3;
	      }
	      else if(parseInt(valor)<=0){
		     valido=-3;
	      }
	   }
	}		
	if(valido==0){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form8").css("display","none");
			if(update==true){
				var cant1=parseInt($("#cantidad_disp").val());
			    var cant2=parseInt($("#cantidad").val());
			    if(cant2<cant1){
				  $("#error_form6").css("display","block"); 	
				}
				else{
					showModal_byName("Modal-confirm");
				}
			}
			else{
				$("#error_form7").css("display","block");

				var datos={"serial": $("#serial").val()};
				 $.ajax({
                   data: datos,
                   method: "POST",
                   url: "serial_valido.php",
                   success: function(data){
					   if(data.includes(":")){
						   data=data.split(":");
						   if(data.length==2){
							  res=data[1];
                              if(res=="OK"){
								 $("#error_form7").css("display","none"); 
								 
                                 //Validation Succes Wait for Confirmation from User								 
                                 showModal_byName("Modal-confirm");
							  }                 							  
						   }
					   }
				   }
				 });
				
			}
	}
	else{	
	    //show erro to User
		if(valido==-1){
		    $("#error_form").css("display","block");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form8").css("display","none");
		}
		else if(valido==-2){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","block");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form8").css("display","none");
		}
		else if(valido==-3){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","block");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form8").css("display","none");
		}
		else if(valido==-4){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","block");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form8").css("display","none");
		}
		else if(valido==-5){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","block");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form8").css("display","none");
		}
		else if(valido==-6){
			$("#error_form").css("display","none");
			$("#error_form2").css("display","none");
	        $("#error_form3").css("display","none");
			$("#error_form4").css("display","none");
			$("#error_form5").css("display","none");
			$("#error_form6").css("display","none");
			$("#error_form7").css("display","none");
			$("#error_form8").css("display","block");
		}
	}	
}

/*Validate the Fields Devolution of Rented Products Page 
 if the result is Correct Process the Request
 */
function validar_devolucion(){
	var id_client=$("#cliente").find("option:selected").val();
	if(id_client==""){
		//show error
		$("#error_form").css("display","block");   
	}
	else{
		$("#error_form").css("display","none");
		var prods_deducciones="";
		var numero=parseInt($("#count_prods").val());
		for(var i=0;i<numero;i++){
			var nodo=$("#prod"+(i+1).toString());
			var nodo_name=$("#prod"+(i+1).toString()+"_name");
			prods_deducciones=prods_deducciones+nodo_name.val()+":"+nodo.val().toString()+";";
		}		
		$("#dedudccciones").val(prods_deducciones);
		
		//validation Succes wait for Confirmation from User
		showModal_byName("Modal-confirm");
	}
}

/*Validate the Fields for Organizate Party Process Page
  if the Result is Ok , Process the Request
*/
function validar_fiesta(){
	var valor="";
	var valido=0;
	valor=$("#fecha_fiesta").val();
	if(valor==""){
		valido=-1;
	}
	else{
		var v_temp=valor.split("-");
		valor=v_temp[2]+"-"+v_temp[1]+"-"+v_temp[0];
		$("#fecha").val(valor);
	}
	if(valido==0){
		valor=$("#hora_fiesta").val();
		if(valor==""){
			valido=-2;
		}
		if(valido==0){
			valor=$("#ubicacion").val();
			if(valor===""){
				valido=-3;
			}
			else{
				if(valor.length<4){
					valido=-3;
				}
				
			}	
		}
		if(valido==0){
			valor=$("#publico").val();
			if(valor==""){
				valido=-4;
			}
			else{
				valor=$("#tipo_fiesta").val();
				if(valor==""){
					valido=-5;
				}
			}
		}
	}
	if(valido==0){		
		var listado=document.getElementById("lista_productos").options;
		var next_val="";
		if(listado.length>0){
			if(listado[0].value!=""){
				for(var i=0;i<listado.length;i++){
					var v=listado[i].value;
					if(v!=""){
						next_val=next_val+v+"|";
					}
				}
			}
		}
		$("#data_productos").val(next_val);
		listado=document.getElementById("lista_consumibles").options;
		next_val="";
		if(listado.length>0){
			if(listado[0].value!=""){
				for(var i=0;i<listado.length;i++){
					var v=listado[i].value;
					if(v!=""){
					    next_val=next_val+v+"|";
				    }
				} 
			}
		}
		$("#data_consumibles").val(next_val);
	    listado=document.getElementById("lista_personal").options;
		next_val="";
		if(listado.length>0){
			if(listado[0].value!=""){
			    for(var i=0;i<listado.length;i++){
				    var v=listado[i].value;
				    if(v!=""){
					    next_val=next_val+v+"|";
				   }
			   }
			      
	        }
        }
        $("#data_personal").val(next_val);  			
		valor=$("#cliente_params").val();
		if(valor==""){
			valido=-6;
	    }			
	}	
	if(valido==0){
		$("#error_Form1").css("display","none");
		$("#error_Form2").css("display","none");
		$("#error_Form3").css("display","none");
		$("#error_Form4").css("display","none");
		$("#error_Form5").css("display","none");
		$("#error_Form6").css("display","none");
		$("#error_Form7").css("display","none");			
		valor=$("#fecha").val();
		var datos={"fecha":valor};				 
        $.ajax({
            data: datos,
            method: "POST",
            url: "fecha_valida.php",
            success: function(data){				 
				data=data.split(":");
				var info=data[1];
				if(info=="OK"){
					//validation Succes Wait for Confirmation from User
					showModal_byName("modalConfirm");
				}
				else{
					$("#error_Form2").css("display","block"); 
				}
			}
		});	
	}
	else{
		//Show Error to User
		if(valido==-1){
			$("#error_Form1").css("display","block");
		    $("#error_Form2").css("display","none");
		    $("#error_Form3").css("display","none");
		    $("#error_Form4").css("display","none");
		    $("#error_Form5").css("display","none");
		    $("#error_Form6").css("display","none");
		    $("#error_Form7").css("display","none");
		}
		else if(valido==-2){
			$("#error_Form1").css("display","none");
		    $("#error_Form2").css("display","none");
		    $("#error_Form3").css("display","block");
		    $("#error_Form4").css("display","none");
		    $("#error_Form5").css("display","none");
		    $("#error_Form6").css("display","none");
		    $("#error_Form7").css("display","none");
		}
		else if(valido==-3){
			$("#error_Form1").css("display","none");
		    $("#error_Form2").css("display","none");
		    $("#error_Form3").css("display","none");
		    $("#error_Form4").css("display","block");
		    $("#error_Form5").css("display","none");
		    $("#error_Form6").css("display","none");
		    $("#error_Form7").css("display","none");
		}
		else if(valido==-4){
			$("#error_Form1").css("display","none");
		    $("#error_Form2").css("display","none");
		    $("#error_Form3").css("display","none");
		    $("#error_Form4").css("display","none");
		    $("#error_Form5").css("display","block");
		    $("#error_Form6").css("display","none");
		    $("#error_Form7").css("display","none");
		}
		else if(valido==-5){
			$("#error_Form1").css("display","none");
		    $("#error_Form2").css("display","none");
		    $("#error_Form3").css("display","none");
		    $("#error_Form4").css("display","none");
		    $("#error_Form5").css("display","none");
		    $("#error_Form6").css("display","block");
		    $("#error_Form7").css("display","none");
		}
		else if(valido==-6){
			$("#error_Form1").css("display","none");
		    $("#error_Form2").css("display","none");
		    $("#error_Form3").css("display","none");
		    $("#error_Form4").css("display","none");
		    $("#error_Form5").css("display","none");
		    $("#error_Form6").css("display","none");
		    $("#error_Form7").css("display","block");
		}
	}
} 

/*Validate  the Worker to Assign as New Admin User
*/
function validar_cambio_Admin(){
	$("#error_msg").css("display","none");
	var valido=true;
	var new_worker=$("#trabajador").find(":selected").val();
	if(new_worker==""){
		valido=false;
	}
	if(valido){
	    showModalPass("Change_Admin");
	}
	else{
		$("#error_msg").css("display","block");
	}
}

/*Validate Fields of Rented Product Process Page
 if the Resutl is Ok Process the Request
*/
function validar_alquiler(){
	$("#error_Form1").css("display","none");
	$("#error_Form2").css("display","none");
	$("#error_Form3").css("display","none");
	$("#error_Form4").css("display","none");		
	 var valido=0;
	 var ops=document.getElementById("lista_productos").options;
	 if(ops.length>0){
		  if(ops[0].value!=""){
             var productos="";
			 for(var i=0;i<ops.length;i++){
                var temp=ops[i].value; 
				temp=temp.split(";");
				var name_prod=temp[0];
				var info_prod=temp[1];
				info_prod=info_prod.split(",");
			    var formato=info_prod[0];
				var cant=info_prod[1];
				var precio_ud=info_prod[2];
				productos=productos+name_prod+":"+formato+","+cant+","+precio_ud+";";
			 }
			 $("#data_productos").val(productos);			 
		  }
          else{
            valido=-1;
		  }			  		  
	 }
	 else{
		 valido=-1;
	 }	 
	 if(valido==0){		
		valor=$("#cliente_params").val();
		if(valor==""){
				valido=-2;
		}			
		if(valido==0){			
			 valor=$("#fecha_devolucion").val();		
		    if(valor==""){			
			    valido=-3;
		    }
		    else{			
			    valor=valor.split("-");
			    valor=valor[2]+"-"+valor[1]+"-"+valor[0];            
			    $("#fecha").val(valor);	
				var datos={"fecha":valor};				 
                $.ajax({
                    data: datos,
                    method: "POST",
                    url: "fecha_valida.php",
                    success: function(data){					 
					   data=data.split(":");
					   var info=data[1];
					   if(info=="OK"){
						  //Validation Success Wait for User Confirmation
						  showModal_byName("modalConfirm");
					   }
					   else{
						  $("#error_Form3").css("display","block"); 
					   }
				   }
				 });			  				 
		    }			
		}
	}	 	 	
	if(valido!=0){
		//show error to User
	    if(valido==-1){  	
	  	   $("#error_Form1").css("display","block");
	    }
	    else if(valido==-2){
		   $("#error_Form2").css("display","block");
	    }
	    else if(valido==-3){
		   $("#error_Form4").css("display","block");
	    }
	}	
}
/*Validate Fields of 'Respaldo' of Data Base Page
  if is Ok Process the Request
*/
function validar_respaldo(){
	var accion=$('input[name="opcion"]:checked').val();
	var valido=true;
	if(accion=="restaurar"){
		$("#accion").val("restaurar");
		var f=$("#source").val();
		$("#error_msg").css("display","none");
		$("#error_msg2").css("display","none");
		if(f===""){
			$("#error_msg").css("display","block");			
			valido=false;
		}
		else if(f.endsWith(".zip")==false){
			$("#error_msg2").css("display","block");
			valido=false;
		}
	}
	else if(accion=="respaldar"){
		$("#accion").val("respaldo");
	}
   if(valido){
	   //Validation Success Request Admin Confirmation
	   showModalPass("BD");
   }
}

/*Validate the Secret Questions of User for User Register or User Account Modification Page
  and return the result as boolean value
*/
function validar_pregs(){
		   
	var res=true;
	var p1=$("#p1").find(":selected").val();
    var p2=$("#p2").find(":selected").val();
	var p3=$("#p3").find(":selected").val();
	var p4=$("#p4").find(":selected").val();
	var p5=$("#p5").find(":selected").val();
	var p6=$("#p6").find(":selected").val();
	var p7=$("#p7").find(":selected").val();
	var p8=$("#p8").find(":selected").val();
	if(p1!="" && p2!="" && p3!="" && p4!="" && p5!="" && p6!=""){
        var r1=$("#r1").val();
		var r2=$("#r2").val();
		var r3=$("#r3").val();
		var r4=$("#r4").val();
		var r5=$("#r5").val();
		var r6=$("#r6").val();
		var r7=$("#r7").val();
		var r8=$("#r8").val();		  
		if(r1!="" && r2!="" && r3!="" && r4!="" && r5!="" && r6!="" && (p7=="" || (p7!="" && r7!="")) && (p8=="" || (p8!="" &&r8!=""))){  
			var preguntas=[p1,p2,p3,p4,p5,p6];
			if(p7!="" ){
				preguntas.push(p7);       
			}
		    if(p8!=""){
				preguntas.Push(p8);	  
			} 
			var repetidas=false;
			for(var i=0;i<preguntas.length;i++){
                for(var j=0;j<preguntas.length;j++){
					if(i!=j){
                        if(preguntas[i]==preguntas[j]){
						    repetidas=true;
							i=preguntas.length;
							j=preguntas.length;
						}
					}
				} 
		    }
			if(repetidas==true){
			    $("#error_msg4").css("display","none");
	            $("#error_msg5").css("display","block");
	            $("#error_msg6").css("display","none");
			    res=false;
			}  
		}
		else{
			$("#error_msg4").css("display","none");
	        $("#error_msg5").css("display","none");
	        $("#error_msg6").css("display","block");	
			 res=false;
		}
    }
	else{
		$("#error_msg4").css("display","block");
	    $("#error_msg5").css("display","none");
	    $("#error_msg6").css("display","none");
	    res=false;		
	} 		
	return res;
}

/*Validate the Password Fields for User Register or User Account Modification Page
  and return the result as boolean value
*/
function verificar_pass(){
	var pass1= $("#pass1").val();
	var pass2= $("#pass2").val();
	var res=true;
	if(pass1!=pass2){
		$("#error_msg").css("display","block");
		$("#error_msg2").css("display","none");
	    res= false;
	}
	else{
		$("#error_msg").css("display","none");
		$("#error_msg2").css("display","none");
		var correctos=[false,false,false,false,false,true]
		var continuar=true;
		if(pass1.length>=8){correctos[0]=true;}	
		for (var i=0;i<pass1.length;i++){
			var c=pass1.charAt(i);
			var code=pass1.charCodeAt(i);  
			if(code>=48 && code<=57){
				correctos[1]=true;   
			}
			else if(code==33 || code==95 || code==63 || code==64 || (code>=35 && code<=38)){
				correctos[4]=true;
						
			}
			else if(c=='' || c==' '){
				correctos[5]=false;
			}
			else if(c==c.toUpperCase()){
				correctos[2]=true;		
			}
			else if(c==c.toLowerCase()){
				 correctos[3]=true;		   
			}   
		}
		for (var j=0;j<correctos.length;j++){
			if(correctos[j]==false){continuar=false;}
		}
		if(continuar==false){
            $("#error_msg2").css("display","block");			 
            res= false;
		}	   		 
	}
	return res;
}

/*Validate Fields of User Register Page
  if is Valid Process the Register
*/
function validar_usuarios(){		
	$("#error_msg").css("display","none");
	$("#error_msg2").css("display","none");
	$("#error_msg3").css("display","none");
	$("#error_msg4").css("display","none");
	$("#error_msg5").css("display","none");
	$("#error_msg6").css("display","none");
	$("#error_msg7").css("display","none");		
    $("#error_msg8").css("display","none");		
    
	var valido=true;
	if($("#user_name").val().length>3){
		if($("#permiso").find(":selected").val()!=""){
			if(verificar_pass()){
				var trabajador=$("#trabajador").find(":selected").val();
				if(trabajador==""){
				  valido=false;
				  $("#error_msg8").css("display","block");	
				}
				if(valido){
			        if(validar_pregs()==false){valido=false;}
				}
			}
			else{ 
				valido=false; 
			}
		}
		else{
			$("#error_msg7").css("display","block");			   
			valido=false;
		}
	}
	else{
		$("#error_msg3").css("display","block");			
		valido=false;
	}		
	if(valido==true){
		//validation Success Wait for Admin Confirmation
		showModalPass("nuevo");
	}	
}

/*Validate Fields of Cancel Party Process Page
  is the Result is Ok , Process the Request
*/
function validar_cancelar_fiesta(){
	
	var valido=0;	
	var cliente=$("#cliente").find(":selected").val();
	if(cliente==""){
		valido=-1;
	}
	if(valido==0){

		var ops=document.getElementById("fiestas_cancel").options;	
		if(ops.length>0){
            if(ops[0].value==''){
		        valido=-2;
	        }
	    }
        else{
            valido=-2;
        }		
	}
	if(valido==0){
		$("#error_form").css("display","none");
		$("#error_form2").css("display","none");
	    $("#error_form3").css("display","none");
        $("#error_form4").css("display","none");
	    $("#error_form5").css("display","none");	    
		var ids_list="";
		var ops=document.getElementById("fiestas_cancel").options;
	    for(var i=0;i<ops.length;i++){
			if(ops[i].value!=""){
			    var temp=ops[i].value.split(";");
			    ids_list=ids_list+temp[0];
			    if(i<ops.length-1){
                   ids_list=ids_list+";";
			    }
			}
		}
		$("#fiestas_ids").val(ids_list);
		//Validation Succes Wait for User Confirmation
		showModal_byName("Modal-confirm");
	}
	else{
		//show the Error to The User
	    if(valido==-1){
		   $("#error_form").css("display","none");
		   $("#error_form2").css("display","none");
	       $("#error_form3").css("display","none");
           $("#error_form4").css("display","block");
	       $("#error_form5").css("display","none");	    
	    }
        else if(valido==-2){
           $("#error_form").css("display","none");
		   $("#error_form2").css("display","none");
	       $("#error_form3").css("display","none");
           $("#error_form4").css("display","none");
	       $("#error_form5").css("display","block");
	    
	    }		   		
	}
}

/*Validate field of User Account Modification Page
 if the Result is Ok Process the Request
*/
function validar_modific_user(){	
	var valid=true;
	var modific_t=$("#tipo_modificacion").val();	
	if(modific_t=="pass"){
		if(verificar_pass()==false){valid=false;};
	}
	else if(modific_t=="preguntas"){
		if(validar_pregs()==false){valid=false;};		
	}
	else if(modific_t=="perfil"){
		var foto=$("#foto").val();		
		if((foto.endsWith(".jpg") || foto.endsWith(".png") || foto.endsWith(".jpeg"))==false){		
			valid=false;
			$("#error_msg3").css("display","block");
		}
		else{
			$("#error_msg3").css("display","none");	
		}
	}
	if(valid==true){
		//Validation Succes Wait for Confirmation of User
		$("#error_msg").css("display","none");	
		$("#error_msg3").css("display","none");
		$("#error_msg4").css("display","none");
		$("#error_msg5").css("display","none");
		$("#error_msg6").css("display","none");
		showModalPass("edit info");		
	}
}

/*Set the Data of Worker On the Required html Nodes*/
function set_trabajador(raw){
	
	if(raw!=""){
		var temp_raw=raw.split(";");
		if(temp_raw.length==2){
			var trabaj_dat=temp_raw[0];
			var temp_dat_telef=temp_raw[1].split("|");
			var telefs="";
			var is_admin=false;
			var no_telef=false;
			if(temp_dat_telef.length==2){
				telefs=temp_dat_telef[0];
				if(temp_dat_telef[1]=="true"){
					is_admin=true;
				}
			}
			if(telefs==""){
				telefs="Ningun Telefono Agregado,";
				no_telef=true;
			}
			trabaj_dat=trabaj_dat.split(",");
			telefs=telefs.split(",");
			if(trabaj_dat.length==8){
				$("#cedula").val(trabaj_dat[0]);
				$("#nombre").val(trabaj_dat[1]);
				$("#apellido").val(trabaj_dat[2]);
				$("#edad").val(trabaj_dat[6]);
				$("#carg").val(trabaj_dat[3]);
				$("#nivel_academico").val(trabaj_dat[5]);
				$("#turno").val(trabaj_dat[4]);
				$("#estatus").val(trabaj_dat[7]);
				if(is_admin){
					$("#carg").prop("disabled",true);
				}
			}
			if(telefs.length>0){
				var next_html=""
				for(var i=0;i<telefs.length;i++){
					if(telefs[i]!=""){
						var value_telef=telefs[i];
						if(no_telef==true){
							value_telef="";
						}
					    next_html=next_html+"<option value='"+value_telef+"'>"+telefs[i]+"</option>";
					}
				}
				$("#telefonos").html(next_html);
			}
		}
		
	}
}

/*Set the Data of Product On the Required Html Nodes*/
function set_producto(data){
	
	data=data.split(",");
	$("#producto").val(data[0]);
	$("#serial").val(data[1]);
	$("#precio_alquiler").val(data[2]);
	$("#cantidad").val(data[3]);
	$("#precio").val(data[5]);
	$("input[name='alquilable'][value='"+data[6]+"']").prop("checked",true);
	var dif=parseInt(data[3])-parseInt(data[4]);
	$("#cantidad_disp").val(dif.toString());	
}

/*Show the Windows of Worker Selection for Organizate Party Page*/
function show_trabajador(name){
    var fecha=$("#fecha_fiesta").val();
	var hora=$("#hora_fiesta").val();
	if(fecha!="" && hora!=""){
	   var f=fecha.split("-");
	   fecha=f[2]+"-"+f[1]+"-"+f[0];
	   hora=hora.split(":");
	   var turno="";
	   var hora_indicada=parseInt(hora[0]);
	   if(hora_indicada>=18 || hora_indicada<=6){
		   turno="nocturno";
	   }
	   else{
		   turno="diurno";
	   } 
	   var datos={"fecha":fecha,"turno":turno};
	    $.ajax({
            data: datos,
            method: "POST",
            url: "get_data_trabajadores.php",
            success: function(data){	
   			
				if(data.includes(":")){
					data=data.split(":");
					var raw=data[1];
					var next_html="<option value='' selected >Elegir Trabajador</option>";
					
				    if(raw!="" && data[0]!="Error"){
						raw=raw.split(";");
						for(var i=0;i<raw.length;i++){
							if(raw[i]!=""){
								var data_t=raw[i].split(",");
								var ci=data_t[0];
								var nombre=data_t[1];
								var apellido=data_t[2];
								var turno=data_t[3];
								var full_name=nombre+" "+apellido;
								var valor_nodo=ci+","+nombre+","+apellido+","+turno;
								next_html=next_html+"<option value='"+valor_nodo+"' >"+full_name+"</option>";
							}
						}	
				    }
					 $("#trabajadores_list").html(next_html);
						 
			    }
		    }
	   });
	   showModal_byName(name);
	   $("#rol").val("");
	   $("#boton_send").prop("disabled",true);
	   $("#boton_reset").prop("disabled",true);
	   $("#send_trabajadorBtn").prop("disabled",true);
	   $("#error_Form1").css("display","none");
	   $("#error_Form2").css("display","none");
	   $("#error_Form3").css("display","none");
	   $("#error_Form4").css("display","none");
	   $("#error_Form5").css("display","none");
	   $("#error_Form6").css("display","none");
	   $("#error_Form7").css("display","none");
	   $("#error_Form8").css("display","none");
	   $("#error_Form9").css("display","none");
	   $("#error_Form10").css("display","none");
	   $("#info_trabajador").css("display","none");
	   $("#error_trabaj1").css("display","none");   
	}
	else{
         //show error Message Dialog: Require Indicated Time and Date 	
		 showModal_byName("modalError");
	}		
}

/*Show the windows of Products Selection for Organizate Party or Rented Product Page*/
function show_productos(name,alquilables){	
	if(alquilables==true){
	    $("#datos_compra").css("display","none");
	    $("#prodList").val("");
	    $("#formato").val("Unidad");
	    $("#cantidad").val("");
	    $("#error_producto").css("display","none");
	    $("#error_producto2").css("display","none");
        $("#error_producto3").css("display","none");
	    $("#error_Form1").css("display","none");
	    $("#error_Form2").css("display","none");
	    $("#error_Form3").css("display","none");
	    $("#error_Form4").css("display","none");
		var path = window.location.pathname;
        var page = path.split("/").pop();
		if(page=="organizar_fiestas.php"){
			$("#error_Form5").css("display","none");
	        $("#error_Form6").css("display","none");
	        $("#error_Form7").css("display","none");
            $("#error_Form8").css("display","none");
			$("#error_Form9").css("display","none");
            $("#error_Form10").css("display","none");
		}
	    showModal_byName(name);
	    $("#send_productosBtn").prop("disabled",true);
	}
	else{
		$("#datos_compra2").css("display","none");
	    $("#prodList2").val("");
	    $("#formato2").val("Unidad");
	    $("#cantidad2").val("");
	    $("#error_producto4").css("display","none");
	    $("#error_producto5").css("display","none");
        $("#error_producto6").css("display","none");
		$("#error_Form1").css("display","none");
		$("#error_Form2").css("display","none");
		$("#error_Form3").css("display","none");
		$("#error_Form4").css("display","none");
		$("#error_Form5").css("display","none");
		$("#error_Form6").css("display","none");
		$("#error_Form7").css("display","none");
		$("#error_Form8").css("display","none");
		$("#error_Form9").css("display","none");
		$("#error_Form10").css("display","none");
	    showModal_byName(name);
	    $("#send_productosBtn2").prop("disabled",true);
	}
	$("#boton_send").prop("disabled",true);
	$("#boton_reset").prop("disabled",true);			
}


/*Show the Windows for Select Client for a Process Page (Organizate Party, Rent Products,...)*/
function showClient(name){
	
	$("#p1").css("display","block");
	$("#p2").css("display","none");
	showModal_byName(name);
	$("#client_search").val("");	
	$("#boton_send").prop("disabled",true);
	$("#boton_reset").prop("disabled",true);
	$("#send_ClientBtn").prop("disabled",true);
	$("#cliente_info").css("display","none");
    $("#error_client1").css("display","none");
	$("#error_client2").css("display","none");
	$("#error_client7").css("display","none"); 
	$("#error_Form1").css("display","none");
	$("#error_Form2").css("display","none");
	$("#error_Form3").css("display","none");
	$("#error_Form4").css("display","none");
	var path = window.location.pathname;
    var page = path.split("/").pop();
    if(page=="organizar_fiestas.php"){
		$("#error_Form5").css("display","none");
	    $("#error_Form6").css("display","none");
	    $("#error_Form7").css("display","none");
        $("#error_Form8").css("display","none");
	    $("#error_Form9").css("display","none");
        $("#error_Form10").css("display","none");
	}	
}

/*show the data of Selected party  from 'fiestas_cancel' node of cancel party page*/
function show_party_select(){
	var val=$("#fiestas_cancel").find("option:selected").val();
	var old_node_val=$("#data_f").html();
	if(val!='' && old_node_val==''){
		var next_html="";
		var info=val.split(";");
		if(info.length>0){
			var labels=["","Lugar: ","Fecha: ","Publico: ","Tipo de Fiesta: ","Costo : "];
			for(var i=1;i<info.length;i++){
				var texto=labels[i]+info[i];
				var devol_info="";
				if(i==5){
					texto=texto+"$";
					var temp_devol=parseFloat(info[i])*0.65;
					if(temp_devol>0){
						temp_devol=parseFloat(temp_devol.toFixed(2));
					}
					devol_info="Devolucion (65%) : "+temp_devol.toString()+"$";
				}
				next_html=next_html+"<label>"+texto+"</label><br>";
			    if(devol_info!=""){
					next_html=next_html+"<label>"+devol_info+"</label><br>";
				}
			}
		}
		$("#data_f").html(next_html);
	}

	
}

/*Show the Data of Selected Party from 'fiesta_list' node for Cancel Party Page*/
function mostrar_infoFiesta(){
	var val=$("#fiesta_list").find("option:selected").val();
	$("#error_form").css("display","none");
	$("#error_form2").css("display","none");
	$("#error_form3").css("display","none");
	$("#error_form4").css("display","none");
	$("#error_form5").css("display","none");	
	if(val!=''){
		var next_html="";
		var info=val.split(";");
		if(info.length>0){
			var labels=["","Lugar: ","Fecha: ","Publico: ","Tipo de Fiesta: ","Costo : "];
			for(var i=1;i<info.length;i++){
				var texto=labels[i]+info[i];
				var devol_info="";
				if(i==5){
					texto=texto+"$";
					var temp_devol=parseFloat(info[i])*0.65;
					if(temp_devol>0){
						temp_devol=parseFloat(temp_devol.toFixed(2));
					}
					devol_info="Devolucion (65%) : "+temp_devol.toString()+"$";
				}
				next_html=next_html+"<label>"+texto+"</label><br>";
			    if(devol_info!=""){
					next_html=next_html+"<label>"+devol_info+"</label><br>";
				}
			}
		}
		$("#data_f").html(next_html);
	}
	else{
		$("#data_f").html("");
	}
}

/*Show a Chart with the stadistics of System*/
function show_estadisticas(){	
	$("#error_msg3").css("display","none");	
	 if(myPieChart!=null){
        myPieChart.destroy();
    }
	var opcion1=$("#accion1").find(":selected").val();
	var opcion2=$("#accion2").find(":selected").val();
	var opcion2_text=$("#accion2").find(":selected").text();
	var error=false;
	if(opcion1=="" || opcion2==""){
		error=true;
	}	
	if(error==false){		
		$("#error_msg").css("display","none");
		$("#error_msg2").css("display","none");
		get_dataStadistica(opcion1,opcion2,opcion2_text);		
    }
	else{
		if(opcion1==""){
			$("#error_msg").css("display","block");
			$("#error_msg2").css("display","none");
		}
		else if(opcion2==""){
			$("#error_msg2").css("display","block");
			$("#error_msg").css("display","none");
		}
	}	
}

/*Show the List of Posibles Options for each Stadistic of System*/
function show_opcionStat(){
	var opcion=$("#accion1").find(":selected").val();
	if(opcion!=""){	
		$("#label_op2").css("display","inline-block");
		$("#accion2").css("display","inline-block");
		var next_html="<option value='' selected>Elegir</option>";
		if(opcion=="producto"){
			next_html=next_html+"<option value='producto_alquilado:nombre_producto,cantidad_alquilada,formato'>Alquilados</option>";
			next_html=next_html+"<option value='producto_reservado:nombre_producto,cantidad_reservada,formato'>Reservados para Fiestas</option>";
		}
		else if(opcion=="fiesta"){
			next_html=next_html+"<option value='fiesta:publico'>Publico</option>";
            next_html=next_html+"<option value='fiesta:tipo_fiesta'>Tipo de Fiesta</option>";
		}
		else if(opcion=="usuario"){		
			next_html=next_html+"<option value='usuario:bloqueado'>Bloqueados</option>";
            next_html=next_html+"<option value='usuario:permiso'>Nivel de Usuario</option>";
		}
		else if(opcion=="trabajador"){
			next_html=next_html+"<option value='trabajador:cargo'>Cargos</option>";
			next_html=next_html+"<option value='trabajador:estatus'>Estatus</option>";	
		}
		$("#accion2").html(next_html);		
	}
	else{
		$("#accion2").css("display","none");
		$("#label_op2").css("display","none");			
	}
}

//Show the Chart with Indicated Values
function show_grafica(labels,cantidades){	
	const colores_posibles=['rgb(255, 99, 132)','rgb(54, 162, 235)',
	'rgb(255, 205, 86)','rgb(244,194,134)','rgb(225,134,244)','rgb(172,231,210)',
	'rgb(105,211,210)'	];				  
	var colores=[];	
	var valores=[];
	for(var i=0;i<labels.length;i++){
		if(i<colores_posibles.length){
			colores.push(colores_posibles[i]);
		}
		else{
		    colores.push('rgb(0, 0, 0)');
		}					 
	    valores.push(cantidades[labels[i]]);
	}		
	const data = {
        labels:labels,
        datasets: [{             
            data: valores,
            backgroundColor: colores,
            hoverOffset: 16
        }]	
    };	
	myPieChart=new Chart(document.getElementById("stats"), {
        type: "pie",
        data: data,
		options: {
            title: {
                display: true,
                text: "World Wide Wine Production"
            }
			,responsive: false,
        }
	});
}

/*The User Select a Worker from the List for Register a Worker Page*/
function select_trabajador(){
	if(document.title!="Registro de trabajadores"){
		return;
	}
	var valor=$("#trabajadores").find("option:selected").val();
	$("#cedula").prop("readonly", false);
	$("#carg").prop("disabled", false);
	$("#boton1").css("display", "inline-block");
	$("#boton2").css("display", "none");
    $("#estatus_label").css("display","none");
	$("#edad").css("margin-right","25%");
	$("#estatus").css("display","none");
	if(valor!=""){
		 valor=valor.split(";");
	     var cedul=valor[0];
	     var is_admin=valor[1];
		 var datos={"cedula":cedul};
		 $.ajax({
           data: datos,
           method: "POST",
           url: "get_data_trabajador.php",
           success: function(data){
			   if(data.includes(":")){
				   $("#estatus_label").css("display","inline-block");
		           $("#estatus").css("display","inline-block");
				   $("#edad").css("margin-right","0%");
				   var temp_dat=data.split(":")
				   if(temp_dat[0]=="Error"){
					  
					   alert(temp_dat[1]);
				   }
				   else{
					 var data_f=temp_dat[1];
				     data_f=data_f+"|"+is_admin;
				     $("#cedula").prop("readonly", true);
				     $("#boton1").css("display", "none");
	                 $("#boton2").css("display", "inline-block");
				     set_trabajador(data_f); 
				   }
				   
			   }
			   else{
				   
				    set_trabajador("");
			   }  
		   },
		   
		 });
	}
	else{
		//reset the form when select null worker
		$("#formu").get(0).reset();
	}
}

/*Select a Worker On Windows for Select Worker of Organizate Party Page*/ 
function seleccionar_trabajador(){
    $("#error_trabaj1").css("display","none");
	var trabajador=$("#trabajadores_list").find("option:selected").val();
	if(trabajador!=""){
		var data=trabajador.split(",");
		var nombre=data[1];
		var apellido=data[2];
		var turno=data[3];
		var cedula=data[0];
		$("#label_nombreT").text("Nombre:"+nombre);
		$("#nombre_T").val(nombre);
		$("#label_apellidoT").text("Apellido:"+apellido);
		$("#apellido_T").val(apellido);
		$("#label_cedulaT").text("CI:"+cedula);
		$("#cedula_T").val(cedula);
		$("#label_turnoT").text("Turno:"+turno);
		$("#turno_T").val(turno);
		$("#info_trabajador").css("display","block");
		$("#send_trabajadorBtn").prop("disabled",false);
	}
	else{
		$("#info_trabajador").css("display","none");
		$("#send_trabajadorBtn").prop("disabled",true);
		
	}
	
}

/*Select the Action to Process On 'Respaldo' of Bd Page*/
function select_accion_respaldo(){
	var accion=$('input[name="opcion"]:checked').val();
	$("#error_msg").css("display","none");
	$("#error_msg2").css("display","none");	
	if(accion=="restaurar"){
		$("#file_respaldo").css("display","block");
		$("#source").val("");
	}
	else if(accion=="respaldar"){
		$("#file_respaldo").css("display","none");
	}
}

/*Select a Product On the Windows for Select Products of Organizate Party or Rented Product Page*/
function seleccionar_producto(alquilables){
    if(alquilables==true){
	   var data=$("#prodList").find("option:selected").val();
	   if(data!=""){	
		   data=data.split(";");
		   var info_prod=data[1];
		   info_prod=info_prod.split(",");
		   var cant=info_prod[0];		
		   var precio=parseFloat(info_prod[1])*parseFloat(info_prod[2]);
		   if(precio>0){
		       precio=parseFloat(precio.toFixed(2));
		   }
		   if(cant>0){
			   $("#send_productosBtn").prop("disabled",false);	  
			   $("#error_producto").css("display","none");
			   $("#error_producto2").css("display","none");
			   $("#error_producto3").css("display","none");
			   $("#datos_compra").css("display","block");
			   $("#precio_ud").text("Precio: "+precio.toString()+"$");
			   $("#cant_disp").text("Cantidad Disponible: "+cant+" uds");			
		   }
		   else{
			   $("#send_productosBtn").prop("disabled",true);	
			   $("#datos_compra").css("display","none");
	           $("#error_producto2").css("display","none");
			   $("#error_producto3").css("display","none");
	           $("#formato").val("Unidad");
	           $("#cantidad").val("");
			   $("#error_producto").css("display","block");
		   }
	   }
	   else{
		   $("#send_productosBtn").prop("disabled",true);
	       $("#error_producto2").css("display","none");
		   $("#error_producto3").css("display","none");
		   $("#datos_compra").css("display","none");
	       $("#prodList").val("");
	       $("#formato").val("Unidad");
	       $("#cantidad").val("");
		   $("#error_producto").css("display","none");
	   }
	}
	else{
		var data=$("#prodList2").find("option:selected").val();
	    if(data!=""){		
		   data=data.split(";");
		   var info_prod=data[1];
		   info_prod=info_prod.split(",");
		   var cant=info_prod[0];		
		   var precio=parseFloat(info_prod[2]);
		   if(precio>0){
			 precio=parseFloat(precio.toFixed(2));  
		   }
		   if(cant>0){
			   $("#send_productosBtn2").prop("disabled",false);	  
			   $("#error_producto4").css("display","none");
			   $("#error_producto5").css("display","none");
			   $("#error_producto6").css("display","none");
			   $("#datos_compra2").css("display","block");
			   $("#precio_ud2").text("Precio: "+precio.toString()+"$");
			   $("#cant_disp2").text("Cantidad Disponible: "+cant+" uds");			
		   }
		   else{
			   $("#send_productosBtn2").prop("disabled",true);	
			   $("#datos_compra2").css("display","none");
	           $("#error_producto5").css("display","none");
			   $("#error_producto6").css("display","none");
	           $("#formato2").val("Unidad");
	           $("#cantidad2").val("");
			   $("#error_producto4").css("display","block");
		   }
	   }
	   else{
		   $("#send_productosBtn2").prop("disabled",true);
	       $("#error_producto5").css("display","none");
		   $("#error_producto6").css("display","none");
		   $("#datos_compra2").css("display","none");
	       $("#prodList2").val("");
	       $("#formato2").val("Unidad");
	       $("#cantidad2").val("");
		   $("#error_producto4").css("display","none");
	   }		
	}
}

/*Select a Product from the List for the Register Product Page*/
function select_producto(){
	
	var id_prod=$("#lista_productos").find("option:selected").val();
	if(id_prod!=""){		
		 var datos={"producto":id_prod};
		 $.ajax({
            data: datos,
            method: "POST",
            url: "get_data_producto.php",
            success: function(data){			
			   if(data.includes(":")){				   
				   data=data.split(":");
				   if(data.length==2){
					   var raw_data=data[1];
					   if(data[0]=="Error"){
						   alert(data[1]);
					   }
					   else{
						   if(raw_data!=""){
						       $("#serial").prop("readonly", true);
						       $("#producto").prop("readonly", true);
		                       $("#boton1").css("display", "none");
		                       $("#boton2").css("display", "inline-block"); 
						       $("#cantidad_disp").css("display","inline-block");
	                     	   $("#label_disp").css("display","inline-block");		
							   set_producto(raw_data);
					       }
					   }
					   
				   }				  
			   }			   
		   }
		 });
	}
	else{
		$("#producto").prop("readonly", false);
		$("#serial").prop("readonly", false);
		$("#boton1").css("display", "inline-block");
		$("#boton2").css("display", "none");
		$("#cantidad_disp").css("display","none");
		$("#label_disp").css("display","none");		
		$("#formu")[0].reset();
	}
}

/*Remove a Phone Number from the List of Phono Numbers on Worker Register Page*/
function remove_telefono(){
	closeModal_byName("ModalMessage2");
	closeModal_byName("ModalMessage");
	closeModal_byName("ModalMessage3");
	closeModal_byName("ModalMessage4");
	var index=document.getElementById("telefonos").selectedIndex;
	var ops=document.getElementById("telefonos").options;
		
	if(ops.length>0 && index!=-1){
		if(ops[0].value==""){
			$("#error_form").css("display","none");
		    $("#error_form2").css("display","none");
		    $("#error_form3").css("display","none");
		    $("#error_form4").css("display","none");
		    $("#error_form5").css("display","none");
		    $("#error_form6").css("display","none");
		    $("#error_form7").css("display","none");
			$("#error_form10").css("display","none");
			$("#error_form11").css("display","none");
		}
		else{
			var next_html="";
			for(var i=0;i<ops.length;i++){
				if(i!=index){
					var valor=ops[i].value;
					next_html=next_html+"<option value='"+valor+"'>"+valor+"</option>"
				}
			}	
			if(next_html==""){
			   next_html="<option value=''>Ningun Telefono Agregado</option>"
			}
			$("#telefonos").html(next_html);
		}		
	}
	else{
		$("#error_form").css("display","none");
		$("#error_form2").css("display","none");
		$("#error_form3").css("display","none");
		$("#error_form4").css("display","none");
		$("#error_form5").css("display","none");
		$("#error_form6").css("display","none");
		$("#error_form7").css("display","none");
		$("#error_form10").css("display","none");
		$("#error_form11").css("display","none");
		showModal_byName("ModalMessage3");
	}
}

/*Remove a Worker from the List of Assignated Worker of Organizate Party Page*/
function borrar_trabajador(){
	var index=document.getElementById("lista_personal").selectedIndex;
	if(index!=-1){		
		var ops=document.getElementById("lista_personal").options;
		var next_html="";
		var count_nodes=0;
		for(var i=0;i<ops.length;i++){
			if(i!=index){
				count_nodes=count_nodes+1;
				next_html=next_html+ops[i].value;
			}
		}
		if(count_nodes<=0){
			next_html=" <option value=''>Ningun Trabajador Agregado</option>";
		}
		$("#lista_personal").html(next_html);
	}	
}

/*Remove a Party from the List of Parties to Cancel*/
function remover_fiesta(){
	var index=document.getElementById("fiestas_cancel").selectedIndex;
	var ops=document.getElementById("fiestas_cancel").options;
	if(ops.length>0 && index!=-1){
		$("#error_form").css("display","none");
	    $("#error_form2").css("display","none");
	    $("#error_form3").css("display","none");
		$("#error_form4").css("display","none");
	    $("#error_form5").css("display","none");
		var old_val=$("#fiestas_cancel").find("option:selected").val();
	    var old_text=$("#fiestas_cancel").find("option:selected").text();
	    var old_temp=old_text.split(" ");
		var old_index=parseInt(old_temp[1]);
		if(ops[0].value!=""){
			var next_html="";
			for(var i=0;i<ops.length;i++){
				if(i!=index){
					var valor=ops[i].value;
					var texto=ops[i].text;
					next_html=next_html+"<option value='"+valor+"'>"+texto+"</option>"
				}
			}
			if(next_html==""){
				next_html="<option value=''>Agregar Fiestas a Cancelar</option>"
			}
			$("#fiestas_cancel").html(next_html);
			calcular_devolucion();
			var ops2=document.getElementById("fiesta_list").options;
			var next_html2="";
			for(var j=0;j<ops2.length;j++){
				if(j!=old_index){
					next_html2=next_html2+"<option value='"+ops2[j].value+"'>"+ops2[j].text+"</option>";
				}
				else{
					if(ops2[j].value!=''){
						next_html2=next_html2+"<option value='"+old_val+"'>"+old_text+"</option>";
						next_html2=next_html2+"<option value='"+ops2[j].value+"'>"+ops2[j].text+"</option>";
				    }
					else{
						next_html2=next_html2+"<option value='"+ops2[j].value+"'>"+ops2[j].text+"</option>";
				        next_html2=next_html2+"<option value='"+old_val+"'>"+old_text+"</option>";
						 
					}
				}
			}
			$("#fiesta_list").html(next_html2);
	        $("#fiesta_list").val('');
            $("#data_f").html("");
		}
	}
	else{
		$("#error_form").css("display","none");
	    $("#error_form2").css("display","block");
	    $("#error_form3").css("display","none");
		$("#error_form4").css("display","none");
	    $("#error_form5").css("display","none");
	}
}

/*Remove a Product from The List of Products of Organizate Pary or Rent Products Page*/
function borrar_producto(alquilable){	
	var name_node="lista_productos";
	if(alquilable==false){
		name_node="lista_consumibles";
	}	
	var index=document.getElementById(name_node).selectedIndex;
	if(index!=-1){		
		var next_html="";
		var listado=document.getElementById(name_node).options;
		var found=false;
		if(listado.length>0){
			var data_p=listado[index].value;						
			data_p=data_p.split(";");
			var info_p=data_p[1];
			info_p=info_p.split(",");
			var formato=info_p[0];
			var cant=parseInt(info_p[1]);
			var costo_ud=0;
			if(alquilable){costo_ud=parseFloat(info_p[3]);}
			else{
				costo_ud=parseFloat(info_p[2]);
			}
            if(costo_ud>0){
				costo_ud=parseFloat(costo_ud.toFixed(2));
			}			
			for(var i=0;i<listado.length;i++){				
				if(i!=index){					
					next_html=next_html+"<option value='"+listado[i].value+"'>"+listado[i].text+"</option>";
					found=true;
				}
			}
			if(found==false){
			   next_html="<option value=''>Ningun Producto Agregado</option>";					
            }		   		   
		    if(formato=="Pack(P)"){
				 cant=cant*10;	 			  
			}
			else if(formato=="Pack(M)"){
				cant=cant*25;					
			}
		    else if(formato=="Pack(G)"){
				cant=cant*50;		
			}
		    var costo_inicial=parseFloat($("#costo").val());
            if(costo_inicial>0){
			   costo_inicial=parseFloat(costo_inicial.toFixed(2));
		    }
		    var temp_costo=cant*costo_ud;
		    var next_costo=costo_inicial-(temp_costo);
		    var iva=0.16;
		    var next_impuestos=next_costo*iva;
		    if(next_costo>0){
		      next_costo=parseFloat(next_costo.toFixed(2));
		    }
		    if(next_impuestos>0){
		       next_impuestos=parseFloat(next_impuestos.toFixed(2));
		    }
		    var next_total=next_costo+next_impuestos;
		    $("#"+name_node).html(next_html);
		    $("#costo").val(next_costo.toString());
		    $("#impuestos").val(next_impuestos.toString());
		    $("#total").val(next_total.toString());							
		    $("#label_costo").text("SubTotal: "+next_costo.toString()+"$");
		    $("#label_impuestos").text("Impuestos: "+next_impuestos.toString()+"$");
		    $("#label_total").text("Total: "+next_total.toString()+"$");							
		}								
	}			
}
/*Reset the List of Phone Numbers of Register worker Page*/
function reset_telefonos(){
	
	closeModal_byName("ModalMessage2");
	closeModal_byName("ModalMessage");
	closeModal_byName("ModalMessage3");
	closeModal_byName("ModalMessage4");
	$("#telefonos").html("<option value=''>Ningun Telefono Agregado</option>");
    $("#boton1").css("display", "inline-block");
	$("#boton2").css("display", "none");
	$("#error_form").css("display","none");
	$("#error_form2").css("display","none");
	$("#error_form3").css("display","none");
	$("#error_form4").css("display","none");
	$("#error_form5").css("display","none");
	$("#error_form6").css("display","none");
	$("#error_form7").css("display","none");
	$("#error_form10").css("display","none");
	$("#error_form11").css("display","none");
	$("#cedula").prop("readonly", false);
}

/*Reset the Rented Product Process Page Data*/
function reset_alquiler(){
	$("#lista_productos").html("<option value=''>Ningun Producto Agregado</option>");
	$("#costo").val("0");
	$("#impuestos").val("0");
	$("#total").val("0");
	$("#label_costo").text("Subtotal:0$");
	$("#label_impuestos").text("Impuestos:0$");
	$("#label_total").text("Total:0$");
	$("#cliente_labelBox").html("<p>Cliente</p><p>No Identificado</p>");
	$("#cliente_params").val("");
	$("#fecha").val("");
	$("#ferror_Form1").css("display","none");
	$("#ferror_Form2").css("display","none");
	$("#ferror_Form3").css("display","none");
	$("#ferror_Form4").css("display","none");
}

/*Reset the Resgister Product Page data*/
function reset_regProd(){
	$("#producto").prop("readonly", false);
	$("#serial").prop("readonly", false);
	$("#cantidad_disp").css("display", "none");
	$("#label_disp").css("display", "none");
}

/*Reset the Debt data from Return Rented Product Process Page*/
function reset_deuda(){
	$("#prod_list").css("display","none");	
	$("#deuda_infoBox").css("display","none");
    $("#deuda_damage_label").text("Por Daños a los Productos: 0$");
	$("#deuda_retraso_label").text("Por Retraso: 0$");
	$("#deuda_total_label").text("Total: 0$");							
	$("#deuda_retraso").val("0");
	$("#deuda_total").val("0");
	$("#deuda_damage").val("0");
}

/*Reset Organizate Party Page*/
function reset_fiesta(){
	$("#lista_productos").html("<option value=''>Ningun Producto Agregado</option>");
	$("#lista_consumibles").html("<option value=''>Ningun Producto Agregado</option>");
    $("#lista_personal").html("<option value=''>Ningun Trabajador Agregado</option>");
	$("#costo").val("20");
	$("#impuestos").val("3.2");
	$("#total").val("23.2");
	$("#label_costo").text("Subtotal:20$");
	$("#label_impuestos").text("Impuestos:3.2$");
	$("#label_total").text("Total:23.2$");
	$("#cliente_labelBox").html("<p>Cliente</p><p>No Identificado</p>");
	$("#cliente_params").val("");
	$("#fecha").val("");
	$("#ferror_Form1").css("display","none");
	$("#ferror_Form2").css("display","none");
	$("#ferror_Form3").css("display","none");
	$("#ferror_Form4").css("display","none");
	$("#ferror_Form5").css("display","none");
	$("#ferror_Form6").css("display","none");
	$("#ferror_Form7").css("display","none");
	$("#ferror_Form8").css("display","none");
	$("#ferror_Form9").css("display","none");
	$("#ferror_Form10").css("display","none");	
}

/*Reset Cancel Party Page*/
function reset_fiestasCancel(){
	$("#fiestas_ids").val("");
	$("#cliente").val('');
	$("#data_client").html("");
	$("#error_form").css("display","none");
	$("#error_form2").css("display","none");
	$("#error_form3").css("display","none");
	$("#error_form4").css("display","none");
	$("#error_form5").css("display","none");	
}

/*Add a Phone Number to the Required List of Worker Regsiter Page*/
function agregar_telefono(){
	closeModal_byName("ModalMessage");
	closeModal_byName("ModalMessage2");
	closeModal_byName("ModalMessage3");
	closeModal_byName("ModalMessage4");
	var valor=$("#telefono_field").val();
	var valido=true;
	if(valor.length==12 || valor.length==11){
		if(valor.length==12){
			for(var i=0;i<valor.length;i++){
				 var code=valor.charCodeAt(i);
				 if(i!=4){
					if( (code>=48 && code<=57)==false ){valido=false;i=valor.length; }
				 }
				 else{
					if(valor[i]!="-"){
                           valido=false;
						   i=valor.length;
					}						
				 }
			}
			if(valido==true){
				var temp=valor.split("-");
				valor=temp[0]+temp[1];
			}
		}
		else{
			if(is_number(valor)==false){
				 valido=false;
			}
		}
	}
	else{
		valido=false;
	}
    if(valido==true ){
		var ops=document.getElementById("telefonos").options;
		if(ops.length>0){
			var first=false;
			if(ops[0].value==""){
			   first=true;
			}
			else{
				for(var j=0;j<ops.length;j++){
					if(ops[j].value==valor){
						valido=false;
						j=ops.length;
					}
				}
			}
			if(valido==true){	
				var cedula=$("#cedula").val();
				var datos={"telefono":valor,"cedula":cedula};
				$.ajax({
                      data: datos,
                      method: "POST",
                      url: "validar_telefono.php",
                      success: function(data){
						 if(data.includes(":")){
							 
							 var respuesta=data.split(":");
							 var continuar=false;
							 if(respuesta.length==2){
								if(respuesta[1]=="OK"){
									continuar=true;
								}
							 }
							 if(continuar==true){
								$("#error_form12").css("display","none");
							    if(first==true){
									$("#telefonos").html("<option value='"+valor+"'>"+valor+"</option>");		   
								}
								else{
								     var last_html=$("#telefonos").html();
		                             last_html=last_html+"<option value='"+valor+"'>"+valor+"</option>"
								     $("#telefonos").html(last_html);
							    }
							 }
							 else{
								alert(respuesta[1]);
								showModal_byName("ModalMessage");
							 }
						 }
						 else{
							showModal_byName("ModalMessage");
						 }
					   }
					 });
			}
            else{
                 showModal_byName("ModalMessage2");
			}
			$("#telefono_field").val("");
		}
	}
	else{
		$("#error_form").css("display","none");
		$("#error_form2").css("display","none");
		$("#error_form3").css("display","none");
		$("#error_form4").css("display","none");
		$("#error_form5").css("display","none");
		$("#error_form6").css("display","none");
		$("#error_form7").css("display","none");
		$("#error_form10").css("display","none");
		$("#error_form11").css("display","none");
		showModal_byName("ModalMessage4");
	}
}

/*Add a Party to the List of Parties to Cancel*/
function add_fiesta(){
	var val=$("#fiesta_list").find("option:selected").val();
	var texto=$("#fiesta_list").find("option:selected").text();
    var index=document.getElementById("fiesta_list").selectedIndex;		
	if(val!=''){
	    $("#error_form").css("display","none");
	    $("#error_form2").css("display","none");
	    $("#error_form3").css("display","none");
	    $("#error_form4").css("display","none");
	    $("#error_form5").css("display","none");		
	    var valido=true;		
		var ops=document.getElementById("fiestas_cancel").options;
		if(ops.length>0){
			var first=false;
			if(ops[0].value==""){
			   first=true;
			}
			else{
				for(var j=0;j<ops.length;j++){
					if(ops[j].value==valor_next){
						valido=false;
						j=ops.length;
					}
				}
			}
			if(valido){				
				if(first){
					$("#fiestas_cancel").html("<option value='"+val+"'>"+texto+"</option>");
				}
				else{
					var next_html=$("#fiestas_cancel").html();
					next_html=next_html+"<option value='"+val+"'>"+texto+"</option>"
					$("#fiestas_cancel").html(next_html)					
				}
				calcular_devolucion();				
				var next_html2="";
				var ops2=document.getElementById("fiesta_list").options;
				for(var j=0;j<ops2.length;j++){
					if(j!=index){
						next_html2=next_html2+"<option value='"+ops2[j].value+"'>"+ops2[j].text+"</option>";
					}
				}
				$("#fiesta_list").html(next_html2);		
			}
			else{
				 $("#error_form3").css("display","block");		
			}
	   	}
	}
	else{	
		$("#error_form").css("display","block");
	    $("#error_form2").css("display","none");
        $("#error_form3").css("display","none");
	    $("#error_form4").css("display","none");
	    $("#error_form5").css("display","none");	
	}
	$("#fiesta_list").val('');	
	$("#data_f").html("");
}

/*Close the Windows for Select the Workers for the Party of Origanziate Party Page*/
function close_trabajador(name,failed){
	var rol=$("#rol").val();	
	if(failed==false){
	    if(rol==""){
		    $("#error_trabaj1").css("display","block");
	    }
	    else{			
			var nombre=$("#nombre_T").val();
			var apellido=$("#apellido_T").val();
			var cedula=$("#cedula_T").val();
			var turno=$("#turno_T").val();			
		    var next_html=$("#lista_personal").html();
		    var ops=document.getElementById("lista_personal").options;
	        if(ops.length>0){
			   var next_val=cedula+","+nombre+","+apellido+","+rol;
			   var next_t=nombre+" "+apellido+":"+rol;
			   if(ops[0].value==""){
				   next_html="<option value='"+next_val+"'>"+next_t+"</option>";
			   }
			   else{
				  next_html=next_html+ "<option value='"+next_val+"'>"+next_t+"</option>";
				   
			   }
		    }	   
		    $("#lista_personal").html(next_html);		 
		    closeModal_byName(name);
	        $("#boton_send").prop("disabled",false);
	        $("#boton_reset").prop("disabled",false);
	        $("#error_trabaj1").css("display","none");
	    }
	}
	else{
		closeModal_byName(name);
	    $("#boton_send").prop("disabled",false);
	    $("#boton_reset").prop("disabled",false);
	    $("#error_trabaj1").css("display","none");		
	}	
}

/*Close the Windows for Indicate the Client*/
function closeClient(name,failed){	
	if(failed==false){		
		var valor_id=$("#inf1").text();
		var valor_name=$("#inf2").text();
		var valor_apellidos=$("#inf3").text();
		valor_id=valor_id.split(":");
		valor_name=valor_name.split(":");
		valor_apellidos=valor_apellidos.split(":");
		var cliente_data=valor_id[1]+";"+valor_name[1]+";"+valor_apellidos[1];		
		var html_cl="<p>Cedula: "+valor_id[1]+"</p><p>"+"Nombres: "+valor_name[1]+"</p><p>"+"Apellidos: "+valor_apellidos[1]+"</p>";
		$("#cliente_params").val(cliente_data);
		$("#cliente_labelBox").html(html_cl);					
	}
	closeModal_byName(name);
	$("#error_client1").css("display","none");
	$("#error_client2").css("display","none");
	$("#error_client7").css("display","none"); 
	$("#boton_send").prop("disabled",false);
	$("#boton_reset").prop("disabled",false);	
}

/*Close the Windows for Indicate the Products 'Consumibles' for the Party on the Page Organziate Party*/
function close_productos_consumibles(name,failed){	
	var error=false;
	if(failed==false){		
		var prod=$("#prodList2").find("option:selected").val();
		if(prod!=""){
			prod=prod.split(";");
			var prod_name=prod[0];
			var prod_info=prod[1];
			prod_info=prod_info.split(",");
			var precioTotal=parseFloat(prod_info[2]);			
			if(precioTotal>0){
			   precioTotal=parseFloat(precioTotal.toFixed(2));
			}
			var cant_disp=parseInt(prod_info[0]);
			var cant_compra=$("#cantidad2").val();
			if(cant_compra!="" ){
				cant_compra=parseInt(cant_compra);
			}
			else{
				cant_compra=0;
			}
			if(cant_disp>0){
			    if(cant_compra>0){
			         var formato_compra=$("#formato2").find("option:selected").val();		
			         var cant_inicial=cant_compra;
					 var f_text="uds";
			         if(formato_compra=="Pack(P)"){
				          cant_compra=cant_compra*10;
						  f_text=formato_compra+" X ";					  
			         }
			         else if(formato_compra=="Pack(M)"){
				            cant_compra=cant_compra*25;
							 f_text=formato_compra+" X ";
			         }
			         else if(formato_compra=="Pack(G)"){
						  cant_compra=cant_compra*50;
						  f_text=formato_compra+" X ";
			         }
					 if((cant_disp-cant_compra)>=0){
			            var costo_compra=precioTotal*cant_compra;
					    var listado=document.getElementById("lista_consumibles").options;
						if(listado.length>0){
							var next_html="";						
							var prod_t=prod_name+": ";
							if(f_text=="uds"){
								prod_t=prod_t+cant_inicial.toString()+" "+f_text;
							}
							else{
								prod_t=prod_t+f_text+cant_inicial.toString();
							}
							var prod_val=prod_name+";"+formato_compra+","+cant_inicial+","+precioTotal.toString();							
							if(listado[0].value!=""){
								var repetido=false;								
								for(var i=0;i<listado.length;i++){
									var data_node=listado[i].value;
									data_node=data_node.split(";");
									var name_node=data_node[0];					
									if(name_node==prod_name){
										var info_node=data_node[1];
									    info_node=info_node.split(",");
										var cant_node=info_node[1];
										var form_node=info_node[0];
										if(form_node==formato_compra){
											repetido=true;
											cant_node=parseInt(cant_node);
											cant_node=cant_node+cant_inicial;
											prod_val=prod_name+";"+formato_compra+","+cant_node.toString()+","+precioTotal;
											if(f_text=="uds"){
								                 prod_t=prod_name+": "+cant_node.toString()+" "+f_text;
							                }
							                else{
								                 prod_t=prod_name+": "+f_text+cant_node.toString();
							                }
											next_html=next_html+"<option value='"+prod_val+"'>"+prod_t+"</option>";
										}
										else{
											next_html=next_html+"<option value='"+listado[i].value+"'>"+listado[i].text+"</option>";
										}
									}
									else{
										next_html=next_html+"<option value='"+listado[i].value+"'>"+listado[i].text+"</option>";
									}
								}
								if(repetido==false){
									next_html=next_html+"<option value='"+prod_val+"'>"+prod_t+"</option>";
								}
							}
							else{
								next_html=next_html+"<option value='"+prod_val+"'>"+prod_t+"</option>";
							}
							$("#lista_consumibles").html(next_html);
						    var costo=parseFloat($("#costo").val());
							costo=costo+costo_compra;
							var iva=0.16;
							var impuestos=costo*iva;
							if(costo>0){
							    costo=parseFloat(costo.toFixed(2));
							}
							if(impuestos>0){
							    impuestos=parseFloat(impuestos.toFixed(2));
							}
							var total=costo+impuestos;
							$("#costo").val(costo.toString());
							$("#impuestos").val(impuestos.toString());
							$("#total").val(total.toString());
							$("#label_costo").text("SubTotal: "+costo.toString()+"$");
							$("#label_impuestos").text("Impuestos: "+impuestos.toString()+"$");
							$("#label_total").text("Total: "+total.toString()+"$");
						}
					 }
					 else{
					    error=true;
					   $("#error_producto5").css("display","none");
				       $("#error_producto6").css("display","block");
					 }
			    }
				else{
					 error=true;
					$("#error_producto5").css("display","block");
				    $("#error_producto6").css("display","none");
				}
			}
		}
	}
	if(error==false){		
	  closeModal_byName(name);
	  $("#boton_send").prop("disabled",false);
	  $("#boton_reset").prop("disabled",false);
	}
}

/*Close the Windows to Indicate the Products to Rent or Sent to a Party*/
function close_productos(name,failed){
	var error=false;
	if(failed==false){		
		var prod=$("#prodList").find("option:selected").val();
		if(prod!=""){
			prod=prod.split(";");
			var prod_name=prod[0];
			var prod_info=prod[1];
			prod_info=prod_info.split(",");
			var precioTotal=parseFloat(prod_info[2]);
			var precio_ud=parseFloat(prod_info[1])*parseFloat(prod_info[2]);
	        if(precioTotal>0){
			   precioTotal=parseFloat(precioTotal.toFixed(2));
			}
			if(precio_ud>0){
			  precio_ud=parseFloat(precio_ud.toFixed(2));
			}
			var cant_disp=parseInt(prod_info[0]);			
			var cant_compra=$("#cantidad").val();
			if(cant_compra!="" ){
				cant_compra=parseInt(cant_compra);
			}
			else{
				cant_compra=0;
			}
			if(cant_disp>0){
			    if(cant_compra>0){
			         var formato_compra=$("#formato").find("option:selected").val();			
			         var cant_inicial=cant_compra;
					 var f_text="uds";
			         if(formato_compra=="Pack(P)"){
				          cant_compra=cant_compra*10;
						  f_text=formato_compra+" X ";	  
			         }
			         else if(formato_compra=="Pack(M)"){
				            cant_compra=cant_compra*25;
							 f_text=formato_compra+" X ";
			         }
			         else if(formato_compra=="Pack(G)"){
						  cant_compra=cant_compra*50;
						  f_text=formato_compra+" X ";
			         }
					 if((cant_disp-cant_compra)>=0){ 
			            var costo_compra=precio_ud*cant_compra;
						var listado=document.getElementById("lista_productos").options;
						if(listado.length>0){
							var next_html="";
							var prod_t=prod_name+": ";
							if(f_text=="uds"){
								prod_t=prod_t+cant_inicial.toString()+" "+f_text;
							}
							else{
								prod_t=prod_t+f_text+cant_inicial.toString();
							}
							var prod_val=prod_name+";"+formato_compra+","+cant_inicial+","+precioTotal.toString()+","+precio_ud.toString();
							if(listado[0].value!=""){
								var repetido=false;
								for(var i=0;i<listado.length;i++){
									var data_node=listado[i].value;
									data_node=data_node.split(";");
									var name_node=data_node[0];
									if(name_node==prod_name){
										var info_node=data_node[1];
									    info_node=info_node.split(",");
										var cant_node=info_node[1];
										var form_node=info_node[0];
										if(form_node==formato_compra){
											repetido=true;
											cant_node=parseInt(cant_node);
											cant_node=cant_node+cant_inicial;
											prod_val=prod_name+";"+formato_compra+","+cant_node.toString()+","+precioTotal.toString()+","+precio_ud.toString();
											if(f_text=="uds"){
								                 prod_t=prod_name+": "+cant_node.toString()+" "+f_text;
							                }
							                else{
								                 prod_t=prod_name+": "+f_text+cant_node.toString();
							                }
											next_html=next_html+"<option value='"+prod_val+"'>"+prod_t+"</option>";
										}
										else{
											next_html=next_html+"<option value='"+listado[i].value+"'>"+listado[i].text+"</option>";
										}
									}
									else{
										next_html=next_html+"<option value='"+listado[i].value+"'>"+listado[i].text+"</option>";
									}
								}
								if(repetido==false){
									
									next_html=next_html+"<option value='"+prod_val+"'>"+prod_t+"</option>";
								}
							}
							else{
								next_html=next_html+"<option value='"+prod_val+"'>"+prod_t+"</option>";
							}
							$("#lista_productos").html(next_html);
						    var costo=parseFloat($("#costo").val());
							costo=costo+costo_compra;
							var iva=0.16;
							var impuestos=costo*iva;
							if(costo>0){
							   costo=parseFloat(costo.toFixed(2));
							}
							if(impuestos>0){
							    impuestos=parseFloat(impuestos.toFixed(2));
							}
							var total=costo+impuestos;
						    if(total>0){
								total=total.toFixed(2);
							}
							$("#costo").val(costo.toString());
							$("#impuestos").val(impuestos.toString());
							$("#total").val(total.toString());
							$("#label_costo").text("SubTotal: "+costo.toString()+"$");
							$("#label_impuestos").text("Impuestos: "+impuestos.toString()+"$");
							$("#label_total").text("Total: "+total.toString()+"$");
						}
					 }
					 else{
					    error=true;
					    $("#error_producto3").css("display","block");
				        $("#error_producto2").css("display","none");
					 }
			    }
				else{
					error=true;
					$("#error_producto2").css("display","block");
				    $("#error_producto3").css("display","none");
				}
			}
		}
	}
	if(error==false){
	    closeModal_byName(name);
	    $("#boton_send").prop("disabled",false);
	    $("#boton_reset").prop("disabled",false);
	}
}

/*Send Request to Register a Client*/
function RegistrarClient(volver,send){
	$("#error_client3").css("display","none");
	$("#error_client4").css("display","none");
	$("#error_client5").css("display","none");
	$("#error_client6").css("display","none");
	if(volver==false){
	   if(send==false){
		  $("#client_names").val("");
		  $("#client_cedula").val("");
		  $("#client_apellidos").val("");
	      $("#p1").css("display","none");
	      $("#p2").css("display","block");
	   }
	   else{
		   var valido=validar_clientes();  
		   if(valido==0){
			    var val_names=$("#client_names").val();
				var val_apellids=$("#client_apellidos").val();
				var temp_names=val_names.split(' ');
				var temp_apellids=val_apellids.split(' ');
				val_names="";
				val_apellids="";
				for (var i=0;i<temp_names.length;i++){
					val_names=val_names+(temp_names[i].charAt(0).toUpperCase()+temp_names[i].slice(1).toLowerCase())+" ";
				}
				for (var j=0;j<temp_apellids.length;j++){
					val_apellids=val_apellids+(temp_apellids[j].charAt(0).toUpperCase()+temp_apellids[j].slice(1).toLowerCase())+" ";
				}
			    var datos={"cedula":$("#client_cedula").val(),"nombres":$("#client_names").val(),"apellidos":$("#client_apellidos").val()};
				$.ajax({
                data: datos,
                method: "POST",
                url: "send_data_cliente.php",
                success: function(data){
				   data=data.split(":");
				   var res=data[1];
			       if(res=="OK"){
					   var cliente_data=$("#client_cedula").val()+";"+val_names+";"+ val_apellids;
					   var html_cl="<p>Cedula: "+$("#client_cedula").val()+"</p><p>"+"Nombres: "+val_names+"</p><p>"+"Apellidos: "+val_apellids+"</p>";
					   $("#cliente_params").val(cliente_data);
					   $("#cliente_labelBox").html(html_cl);
					   closeClient("modalCliente",true);
				   }
				   else{
					  $("#error_client6").css("display","block"); 
				   }
		        }
			  });
		   }				
		   else if(valido==-1){
			   $("#error_client3").css("display","block");
		   }
		   else if(valido==-2){
			      $("#error_client4").css("display","block");
		   }
		   else if(valido==-3){
			      $("#error_client5").css("display","block");
		   } 
	   }
	}
	else{
		 $("#cliente_info").css("display","none");
		 $("#send_ClientBtn").prop("disabled",true);
		 $("#client_search").val("");	  
		 $("#error_client1").css("display","none");
	     $("#error_client2").css("display","none");
		 $("#error_client7").css("display","none"); 
		 $("#p1").css("display","block");
	     $("#p2").css("display","none");
	}
}

/*Verify if Exist the Client and is 'solvente' */
function SearchClient(){
	var id=$("#client_search").val();
	$("#error_client1").css("display","none");
	$("#error_client2").css("display","none");
	$("#error_client7").css("display","none"); 
	var id_error=false;
	if(id==""){
		id_error=true;	
	}
	else if(id.length<7){
		id_error=true;
	}
	else if(is_number(id)==false){
		id_error=true;
	}
	if(id_error==false){
		var datos={"cedula":id};
		 $.ajax({
            data: datos,
            method: "POST",
            url: "get_data_cliente.php",
            success: function(data){
			   if(data.includes(":")){
				   data=data.split(":");
				   var datos_client=data[1];
				   datos_client=datos_client.split(",");
				   var new_data="";
				   var estatus=datos_client[5];
				   if(estatus=="solvente"){
				      new_data="<p id='inf1'>Cedula:"+datos_client[0]+"</p>";
				      new_data=new_data+"<p id='inf2'>Nombres:"+datos_client[1]+" "+datos_client[2]+"</p>";
				      new_data=new_data+"<p id='inf3'>Apellidos:"+datos_client[3]+" "+datos_client[4]+"</p>";
				      $("#cliente_info").css("display","block");
				      $("#data_client").html(new_data);
				      $("#send_ClientBtn").prop("disabled",false);
				   }
				   else{
					    $("#cliente_info").css("display","none");
		                $("#send_ClientBtn").prop("disabled",true);
				        $("#error_client7").css("display","block"); 
				   }
			   }
			   else{
				  $("#cliente_info").css("display","none");
		          $("#send_ClientBtn").prop("disabled",true);
				  $("#error_client2").css("display","block"); 
			   }
		   }
		 });
	}
	else{
		$("#cliente_info").css("display","none");
		$("#send_ClientBtn").prop("disabled",true);
		$("#error_client1").css("display","block");
	}
}

/*Search the Rented Products by the Client and calculate the money to pay for it */
function search_prodClient(){
	var id_client=$("#cliente").find("option:selected").val();
	var datos={"cedula":id_client};
    $("#error_form").css("display","none");
	closeModal_byName("Modal-confirm");	
	if(id_client!=""){
	    $.ajax({
             data: datos,
             method: "POST",
             url: "get_alquilerInfo.php",
             success: function(data){
				 if(data.includes(":")){
					 data=data.split(":");
					 var retraso_days=0;
					 if(data[0]!=""){ 
					      retraso_days=parseInt(data[0]);
					 }
					 var prodInfo=data[1];
					 if(prodInfo!=""){
						 prodInfo=prodInfo.split(";");
						 var next_html="";
						 var num_prods=0;
					     for(var i=0;i<prodInfo.length;i++){
							   if(prodInfo[i]!=""){
								  num_prods=num_prods+1;
						          var data_producto=prodInfo[i].split(",");
							      var name_prod=data_producto[0];
							      var formato=data_producto[1];
							      var cant=data_producto[2];
							      var precio=data_producto[3];
								  if(i==0){
									  $("#label_fecha").text("Fecha Devolucion: "+data_producto[4]);
						              $("#label_retraso").text("Dias de Retraso: "+retraso_days.toString()+" Dias");
								  }
							      var texto="";
								  var cant_real=parseInt(cant);
							      if(formato=="unidad" || formato=="Unidad"){
								      texto="x"+cant+" ("+cant+" Unidades)";
							      }
							      else{
										if(formato=="Pack(P)"){
											cant_real=cant_real*10;
										}
										else if(formato=="Pack(M)"){
											cant_real=cant_real*25;
										}
										else if(formato=="Pack(G)"){
											cant_real=cant_real*50;
										}
								        texto=texto+" "+formato+" x"+cant+"("+cant_real.toString()+" Unidades)";
							      }
							      next_html= next_html+"<label style='font-size:15px' class='label_login'>"+(i+1).toString()+"-"+name_prod+":"+texto+"</label>";
								  next_html= next_html+"<input style='margin-left:10%' type='number' id='prod"+(i+1).toString()+"' name='prod"+(i+1).toString()+"' value='0' max='"+cant_real+"' ><br>";
								  next_html= next_html+"<input type='hidden' id='prodPrice"+(i+1).toString()+"' name='prodPrice"+(i+1).toString()+"' value='"+precio+"' >";
							      next_html= next_html+"<input type='hidden' id='prod"+(i+1).toString()+"_name' name='prod"+(i+1).toString()+"_name' value='"+name_prod+"'><br>"; 
							}
						 }
						 $("#prod_list").css("display","block");
						 $("#box1").html(next_html);
						 $("#count_prods").val(num_prods.toString());
						 $("input[type=number]").bind('keyup input', calcular_deuda);
						 if(retraso_days>0){
							var deuda_base=10;
							var deuda_retraso=parseInt(retraso_days/30);
							deuda_retraso=deuda_retraso*deuda_base;
							var deuda=deuda_base+deuda_retraso;
							$("#deuda_infoBox").css("display","block");
							$("#deuda_retraso_label").text("Por Retraso: "+deuda.toString()+"$");
							$("#deuda_total_label").text("Total: "+deuda.toString()+"$");
						    $("#deuda_damage_label").text("Por Daños a los Productos: 0$");
		                    $("#deuda_retraso").val(deuda.toString());
							$("#deuda_total").val(deuda.toString());
						     $("#deuda_damage").val("0");
						 }
						 else{
							$("#deuda_infoBox").css("display","none");
                            $("#deuda_damage_label").text("Por Daños a los Productos: 0$");
							$("#deuda_retraso_label").text("Por Retraso: 0$");
							$("#deuda_total_label").text("Total: 0$");							
						    $("#deuda_retraso").val("0");
							$("#deuda_total").val("0");
						    $("#deuda_damage").val("0");
						 }
					 }
				 }
				 else{
					 $("#prod_list").css("display","none"); 
						$("#deuda_infoBox").css("display","none");
                            $("#deuda_damage_label").text("Por Daños a los Productos: 0$");
							$("#deuda_retraso_label").text("Por Retraso: 0$");
							$("#deuda_total_label").text("Total: 0$");							
						    $("#deuda_retraso").val("0");
							$("#deuda_total").val("0");
						    $("#deuda_damage").val("0");
				 }
			 }
	    });
	}
	else{
		    $("#prod_list").css("display","none");
		    $("#deuda_infoBox").css("display","none");
            $("#deuda_damage_label").text("Por Daños a los Productos: 0$");
			$("#deuda_retraso_label").text("Por Retraso: 0$");
			$("#deuda_total_label").text("Total: 0$");							
			$("#deuda_retraso").val("0");
			$("#deuda_total").val("0");
			$("#deuda_damage").val("0");			
	}					  
}

//get and show the data required for the chart of stadistics
function get_dataStadistica(param1,param2,param2_s){		
	var tabla="";
	var cant_fields=0;
	var lista_params=[];
	var lista_conds=[-1,-1];
	var next_param2="";		
	var raw_data=param2;
	raw_data=raw_data.split(":");	
	if(raw_data.length>0){
	    next_param2=raw_data[1];
	   tabla=raw_data[0];
	}	
	if(next_param2.includes(";")){		
	   var temp_list=next_param2.split(";");
	   var temp_params=temp_list[0];
	   var temp_cond=temp_list[1];
	   var temp_cond2=[];	 	   
	   if(temp_params.includes(",")){
		   lista_params=temp_params.split(",");
	   }
	   else{
		     lista_params=[temp_params];
	   }
	   		  	  		
	}
	else{		
		if(next_param2.includes(",")){			
			lista_params=next_param2.split(",");
		}
		else{			
			lista_params=[next_param2];
		}				
	}	
	cant_fields=lista_params.length;	
	var datos={"table":tabla,"fields":lista_params,"num_fields":cant_fields,"op1":param1,"op2":param2_s};		
	$.ajax({
        data: datos,
        method: "POST",
        url: "get_stats.php",
        success: function(dat) {
		   if(dat.includes(":")){
			    var raw=dat.split(":")[1];
				var raw_dat=raw.split("|");
				var ops_raw=raw_dat[1];
				var data=raw_dat[0];
				var ops=ops_raw.split(",");
				var op1=ops[0];
				var op2=ops[1];
				var next_data= convert_toStats(data,op1,op2);
				var labels=next_data[0];
				var cants=next_data[1];
				if(labels.length>0 && Object.keys(cants).length>0>0){
					show_grafica(labels,cants);
				}
				else{
					  $("#error_msg3").css("display","block");
				}
		   }
		   else{
			   $("#error_msg3").css("display","block");
		   }
         }
       });	
}

/*get the Data of Parties  Associeted to a Client for the Cancel Party Page */
function get_fiestas(){	
	var cliente=$("#cliente").find(":selected").val();
	if(cliente!=""){
		 var datos_search=["id_fiesta","lugar","fecha","publico","tipo_fiesta","costo"];
	     var table="fiesta";
	     var datos={"tabla":table,"fields":datos_search,"num_fields":datos_search.length,"filtro":"CI_cliente","filtro_val":cliente};	    
		 $.ajax({
           data: datos,
           method: "POST",
           url: "realizar_consulta.php",
           success: function(dat) {		   
			   if(dat.includes("?")){
				  dat=dat.split("?");
				   if(dat[1]!=""){
					  var info=dat[1].split("|");
					  var next_html=" <label class='label_login'>Lista de Fiestas : </label><select onchange='mostrar_infoFiesta()' id='fiesta_list' name='fiesta_list' size='1'><option value=''>Elegir</option>";
					  for(var i=0;i<info.length;i++){
						  if(info[i]!=""){
							var datos_temp=info[i].split(";");
							if(datos_temp.length>0){
							    next_html=next_html+"<option value='"+datos_temp[0]+";"+datos_temp[1]+";"+datos_temp[2]+";"+datos_temp[3]+";"+datos_temp[4]+";"+datos_temp[5]+"'>Fiesta "+i.toString()+" : "+datos_temp[2]+"</option>";
							}
						}
					}
					$("#error_form").css("display","none");
	                $("#error_form2").css("display","none");
	                $("#error_form3").css("display","none");
	                $("#error_form4").css("display","none");
	                $("#error_form5").css("display","none");
					next_html=next_html+"</select><input type='button' style='margin-left:1%;' class='boton_login' onclick='add_fiesta()' value='Agregar' ><br><br>";
					next_html=next_html+"<h3>Datos de La Fiesta</h3><div id='data_f'></div><br>";
					next_html=next_html+"<h3>Fiestas a Cancelar</h3><br>";
				    next_html=next_html+"<div  style='display:inline-block;margin-right:3%;'><select onchange='show_party_select()' id='fiestas_cancel' size='4'><option value=''>Agregar Fiestas a Cancelar</option></select></div><div style='display:inline-block;' ><input type='button' style='margin-left:1%' class='boton_login' onclick='remover_fiesta()' value='Remover' ></div><br><br>";
					next_html=next_html+"<h4 id='monto_label'>Monto a Devolver: 0$</h4>";
					$("#data_client").html(next_html);
				}
			}
		  }
	   });
	}
	else{
			$("#data_client").html("");
	}
}

/*Consult the Data of 'Productos Alquilados and show it in a Table Node*/
function consultar_productosAlquilados(){
    $("#error_report").css("display","none");	
	$("#error_msg").css("display","none");	
	var filtro=$("#filtro").find("option:selected").val();
	var filtro_val=$("#filtro_val").val();
	var filtros_aplicar=[-1,-1];
	if(filtro!="" && filtro_val!=""){
		filtros_aplicar=[filtro,filtro_val];
	}
	var datos_search=["nombre_producto","formato","cantidad_alquilada","CI_cliente","fecha_devolucion"];
	var table="producto_alquilado";
	var datos={"tabla":table,"fields":datos_search,"num_fields":datos_search.length,"filtro":filtros_aplicar[0],"filtro_val":filtros_aplicar[1]};
	$.ajax({
        data: datos,
        method: "POST",
        url: "realizar_consulta.php",
        success: function(dat) {
			if(dat.includes("?")){
				dat=dat.split("?");
				if(dat[1]!=""){
					var info=dat[1].split("|");
					var next_html="<tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>Producto</td><td>Formato</td><td>Cantidad</td><td>CI Cliente</td><td>Fecha Devolucion</td></tr>";
					for(var i=0;i<info.length;i++){
						if(info[i]!=""){
							var fields=info[i].split(";");
							next_html=next_html+"<tr style='width:25%;background:rgb(255,250,239);'><td>"+fields[0]+"</td><td>"+fields[1]+"</td><td>"+fields[2]+"</td><td>"+fields[3]+"</td><td>"+fields[4]+"</td></tr>"; 
						}
					}
					$("#tabla").html(next_html);
				}
			}
			else{
				var next_html="<tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>Producto</td><td>Formato</td><td>Cantidad</td><td>CI Cliente</td><td>Fecha Devolucion</td></tr>";	
				$("#tabla").html(next_html);
				$("#error_msg").css("display","block");
			}
		}
	});
}

/*Consult the Data of Organizated Parties and Show it in a Table Node*/
function consultar_fiestas(){
    $("#error_report").css("display","none");	
	$("#error_msg").css("display","none");	
	var filtro=$("#filtro").find("option:selected").val();
	var filtro_val=$("#filtro_val").val();
	var filtros_aplicar=[-1,-1];
	if(filtro!="" && filtro_val!=""){
		filtros_aplicar=[filtro,filtro_val];
	}
	var datos_search=["CI_cliente","lugar","estatus","fecha","hora","publico","tipo_fiesta"];
	var table="fiesta";
	var datos={"tabla":table,"fields":datos_search,"filtro":filtros_aplicar[0],"filtro_val":filtros_aplicar[1]};
	$.ajax({
        data: datos,
        method: "POST",
        url: "realizar_consulta.php",
        success: function(dat) {
			if(dat.includes("?")){
				dat=dat.split("?");
				if(dat[1]!=""){
					var info=dat[1].split("|");
					var next_html="<tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>CI cliente</td><td>Sitio</td><td>Estatus</td><td>Fecha</td><td>Hora</td><td>Publico</td><td>Tipo</td></tr>";
					for(var i=0;i<info.length;i++){
						if(info[i]!=""){
							var fields=info[i].split(";");
							next_html=next_html+"<tr style='width:25%;background:rgb(255,250,239);'><td>"+fields[0]+"</td><td>"+fields[1]+"</td><td>"+fields[2]+"</td><td>"+fields[3]+"</td><td>"+fields[4]+"</td><td>"+fields[5]+"</td><td>"+fields[6]+"</td></tr>"; 
						}
					}
					$("#tabla").html(next_html);
				}
			}
			else{
				var next_html="<tr style='padding:3px;text-align:center;background-color:rgb(49,75,141);color:white'><td>CI cliente</td><td>Lugar</td><td>Estatus</td><td>Fecha</td><td>Hora</td><td>Publico</td><td>Tipo</td></tr>";
				$("#tabla").html(next_html);
				$("#error_msg").css("display","block");
			}
		}
	});	
}

/*Activate or Desactivate The Nodes Associated to the Filters for a Consult Page*/
function activar_filtro_consultas(){	
	var activar=false;
	var valor=$("#filtro").find("option:selected").val();
	if(valor!=""){
		activar=true;
	}
	if(activar==true){
		$("#filtro_val").css("display","inline-block");
	}
	else{
		$("#filtro_val").css("display","none");
		$("#filtro_val").val("");
	}
}

/*Activate or Desactivate the Nodes of User Gestion Page*/
function activar_gestion_user(arg){
	if(arg=="editar"){
	  var user=$("#selected_row").val(); 
	  if(user!=""){
		   var valor_usr="Usuario: "+user;
		   $("#user_id").text(valor_usr);
	       $("#modific_user").css("display","block");
           $("#opciones").css("display","none");
	       $("#permiso_user").css("display","none");
	       $("#error_msgPass").css("display","none");
	       $("#error_msg").css("display","none");		  
	  }
	  else{
		    $("#error_msg2").css("display","none"); 
		    $("#error_msg").css("display","block");
		    $("#error_msgPass").css("display","none"); 
	  }
	}
	else if(arg=="volver"){
	    $("#modific_user").css("display","none");
	    $("#permiso_user").css("display","none");
        $("#opciones").css("display","block");
	    $("#error_msg2").css("display","none"); 
		$("#error_msg").css("display","none");
		$("#error_msgPass").css("display","none");
	}	
	else if(arg=="permiso display"){
	     $("#opciones3").css("display","block");
		 $("#opciones2").css("display","none");
		 $("#error_msg2").css("display","none"); 
		 $("#error_msg").css("display","none");
		 $("#error_msgPass").css("display","none"); 
	}
	else if(arg=="permiso"){
		var valid=true;
		var valor_permiso=$("#permiso").find(":selected").val();
		if(valor_permiso==""){
			valid=false;
			$("#error_msg2").css("display","block"); 
		}
		if(valid){
			$("#error_msg2").css("display","none"); 
			showModalPass(arg);
		}
	}
	else if(arg=="cancelar"){
		$("#opciones3").css("display","none");
		$("#opciones2").css("display","block");
		 $("#error_msg2").css("display","none"); 
		 $("#error_msg").css("display","none");
		 $("#error_msgPass").css("display","none");  
	}
	else if(arg=="nuevo"){
		$("#error_msgPass").css("display","none");
		window.location.href = "registro_usuarios.php"
	}
}

/*Modidy the Nodes Filters to show in the Auditoria Page*/
function modificar_filtro(){
	var filtro=$("#acc").find(":selected").val();
	if(filtro!=""){
		if(filtro!="fecha"){
			if(filtro.endsWith("usuario")){
			     $("#filtro1").css("display","block");
                 $("#filtro2").css("display","none");
			     $("#filtro3").css("display","none");
			}
			else{
				 $("#filtro1").css("display","none");
                 $("#filtro2").css("display","none");
			     $("#filtro3").css("display","block");
			}
		}
		else{
			   $("#filtro1").css("display","none");
			   $("#filtro2").css("display","block");
			   $("#filtro3").css("display","none");
		}	
	}
	else{
			$("#filtro1").css("display","none");
            $("#filtro2").css("display","none");
			$("#filtro3").css("display","none");			
	}
}

/*Modify the data to Show in User Account Modification Page */
function modificar_opciones_user(arg){
	
	$("#error_msg").css("display","none");
	$("#error_msg2").css("display","none");
	$("#error_msg3").css("display","none");
	$("#error_msg4").css("display","none");
	$("#error_msg5").css("display","none");
	$("#error_msg6").css("display","none");
	$("#error_msgPass").css("display","none");   
	if(arg=="pass"){
	   $("#tipo_modificacion").val("pass");
	   $("#pass_div").css("display","block");
	   $("#preguntas_div").css("display","none");
	   $("#perfil_div").css("display","none");  
	}
	else if(arg=="preguntas"){ 
	   $("#tipo_modificacion").val("preguntas");
	   $("#pass_div").css("display","none");
	   $("#preguntas_div").css("display","block");
	   $("#perfil_div").css("display","none");
	   var data = new FormData();  
	   $.ajax({
          data: data,
          type: "GET",
          url: "preguntas_secretas.php",
          cache: false,
          contentType: false,
          processData: false,
		  beforeSend:function(url) {
          },
          success: function(dat) {
             $("#content_pregs").html(dat);
          }
       });
	}
	else if(arg=="perfil"){
	   $("#foto").val("")
	   $("#tipo_modificacion").val("perfil");	
	   $("#pass_div").css("display","none");
	   $("#preguntas_div").css("display","none");
	   $("#perfil_div").css("display","block"); 
	}
}

//Calculate the Debt to Pay by the Client(damage+retraso) in Return Rented Product Page*/
function calcular_deuda(e){
	$("#deuda_infoBox").css("display","block");          
	var num=$("#count_prods").val();
	var deuda_damage=0;
	for(var i=0;i<num;i++){
		var nodo=$("#prod"+(i+1).toString());
		var nodo_price=$("#prodPrice"+(i+1).toString());
        var maximo=parseInt(nodo.prop("max"));
		if(nodo.val()<0 || nodo.val()==""){
			nodo.val(0);
		}
		else if(nodo.val()>maximo){
			nodo.val(maximo);
		}
		deuda_damage=deuda_damage+(parseFloat(nodo_price.val()))*nodo.val();	
	}
	$("#deuda_damage").val(deuda_damage.toString());
	$("#deuda_damage_label").text("Por Daños a los Productos: "+deuda_damage+"$");
	var retraso=$("#deuda_retraso").val();
	var next_total=parseInt(retraso)+deuda_damage
	$("#deuda_total").val(next_total.toString());
	$("#deuda_total_label").text("Total: "+next_total.toString()+"$");	
}

/*Calculate the  Money to Pay by the Client in Rent Product or Organizate Party Page*/
function ajustar_costos(){	
    alert("costos");
	var costo=0.0;
	var iva=0.16;
	var impuestos=0.0;
	var total=0.0;	
	var valor_lugar=$("#lugar").find("option:selected").text();
	var valor_publico=$("#publico").find("option:selected").text();
    if(valor_lugar!="elejir"){
		if(valor_lugar=="salon de fiesta"){costo=500;}
		else if(valor_lugar=="salon de baile"){costo=400;}
		else if(valor_lugar=="piscina"){costo=350;}
		else if(valor_lugar=="aire libre"){costo=170;}
		else if(valor_lugar=="a domicilio"){costo=100;}
	}
	if(valor_publico!="elejir"){
		if(valor_publico=="infantil"){costo+=30;}
		else if(valor_publico=="adolecentes"){costo+=50;}
		else if(valor_publico=="adultos"){costo+=100;}
		else if(valor_publico=="adultos mayores"){costo+=80;}
	}	
	costo=parseFloat(costo).toFixed(2);
	impuestos=(costo*iva);
	impuestos=parseFloat(impuestos).toFixed(2);
	total=costo+impuestos;
	total=parseFloat(total).toFixed(2);	
	if(total>0.0){
	  $("#precio").val(costo.toString());
	  $("#impuestos").val(impuestos.toString());
	  $("#total").val(total.toString());
	  $("#label_precio").text("precio:"+costo.toString()+"$");
	  $("#label_impuestos").text("impuestos:"+impuestos.toString()+"$");
	  $("#label_total").text("total:"+total.toString()+"$");
	}
	else{
      $("#precio").val("0");
	  $("#impuestos").val("0");
	  $("#total").val("0");
	  $("#label_precio").text("precio:...");
	  $("#label_impuestos").text("impuestos:...");
	  $("#label_total").text("total:...");
	
	}
}

/*Calculate the Money to Return to The Client for Cancel a Party*/
function calcular_devolucion(){	
	var ops=document.getElementById("fiestas_cancel").options;
	var temp=0;
	for(var i=0;i<ops.length;i++){
		if(ops[i].value!=''){
          var val=ops[i].value.split(";");
		   var temp_costo=parseFloat(val[5]);
           temp=temp+(temp_costo*0.65);
		}
	}	
	if(temp>0){
		temp=parseFloat(temp.toFixed(2));
	}
	else{
		temp=0;
	}	
    var devol=temp.toString();
    $("#Monto").val(devol);
    $("#monto_label").text("Monto a Devolver: "+devol+"$");	  
}

/*Convert the data to the Format Required for Show Stadistics in a Chart*/
function convert_toStats(data,op1,op2){
	
	var rows=data.split(";");	
	var labels=[];
	var cants=[];
	var res=[[],[]];
	if(op1=="usuario"){
		if(op2=="Bloqueados"){
			labels=["Bloqueados","No Bloqueados"];
			var cant1=0;
			var cant2=0;			
			for(var i=0;i<rows.length;i++){
				if(rows[i]=="True" || rows[i]=="true"){
					cant1=cant1+1;
				}
				else{
					cant2=cant2+1;
				}
			}			
			cants["Bloqueados"]=cant1;
			cants["No Bloqueados"]=cant2;			
		}
		else{			
			for(var i=0;i<rows.length;i++){
				var valor=rows[i];
				if(labels.length==0){
					labels[0]=valor;
					cants[valor]=1;
				}
				else {					
					if(Object.keys(cants).includes(valor)==false){
							cants[valor]=1;
							labels.push(valor);							
					}
					else{
							cants[valor]=cants[valor]+1;
					}					
				}
			}
			
		}
	}
	else if(op1=="producto"){		
		for(var i=0;i<rows.length;i++){			
			var cols=rows[i].split(",");
			var name=cols[0];
			var cant=parseInt(cols[1]);
			if(op2!="Disponibles" && op2!="disponibles"){
			   var formato=cols[2];			
			   if(formato=="Pack(P)"){
				    cant=cant*10;
			   }
			   else if(formato=="Pack(M)"){
				   cant=cant*25;
			   }
			   else if(formato=="Pack(G)"){
				   cant=cant*50;
			   }
			}			
			if(labels.length==0){
				if(cant>0){
				   labels[0]=name;
				   cants[name]=cant;
				}
			}
			else {					
				if(Object.keys(cants).includes(name)==false){
					if(cant>0){
						cants[name]=cant;
						labels.push(name);
					}							
				}
				else{					        
					var old_val=cants[name];
					cants[name]=old_val+cant;
				}		
			}		
		}
	}
	else if(op1=="fiesta"){
		    labels=[];
			for(var i=0;i<rows.length;i++){
				var valor=rows[i];
				if(labels.length==0){
					labels[0]=valor;
					cants[valor]=1;
				}
				else{
					if(Object.keys(cants).includes(valor)==false){
						cants[valor]=1;
						labels.push(valor);
				    }
					else{
						cants[valor]=cants[valor]+1;
					}
				}
			}	
	}
	else if(op1=="trabajador"){
		for(var i=0;i<rows.length;i++){
			var valor=rows[i];
			if(labels.length==0){
				labels[0]=valor;
				cants[valor]=1;
			}
			else {					
				if(Object.keys(cants).includes(valor)==false){
					cants[valor]=1;
					labels.push(valor);
				}
				else{
					cants[valor]=cants[valor]+1;
				}
					
			}
		}
	}
	if(labels.length>0 && Object.keys(cants).length>0){
		res=[labels,cants];
	}
	return res;
}

/*Trigger a Second Form Node and set in a Node 'Targer' the Value of Node 'User'*/
function send_target(){	
	var formu=$("#second_form");
	var usr=$("#user");
	var tar=$("#target").val(usr.val());
    formu.submit();	
}

/*Go to the User Gestion Page*/
function go_gestion(){
	window.location.href = "gestion_usuarios.php"
}
/*Go to Loggin Page*/
function go_loggin(){	
	window.location.href = "loggin.php"
}

/*Set the Colors of Menu Option Based in type of User Loggin*/
function iniciar_menu(){
    

	$.ajax({
        type: "POST",
        url: "get_user_access.php",
        cache: false,
        contentType: false,
        processData: false,
		beforeSend:function(url) {    
        },
        success: function(dat) {
	
			 if(dat=="administrador"){ 
				$(".only_admin").css("color","white");
			    $(".process_Menu").css("color","white");
			    $("#Menu_registro_producto").css("color","white");
			 }
			 else{ 
			     $(".only_admin").css("color","gray");
			     if(dat=="Visitante"){
					$(".process_Menu").css("color","gray");
			        $("#Menu_registro_producto").css("color","gray");
				 }
				 else{
					$(".process_Menu").css("color","white");
			        $("#Menu_registro_producto").css("color","white");
				 }
				
			 }
         }
    });
}

/*Request and Set the Data of Auditoria in Required Nodes*/
function auditoria(){	
	var filtro=$("#acc").find(":selected").val();
	var filtro_val="";
	if(filtro!=""){
		if(filtro!="fecha"){
			if(filtro.endsWith("usuario")){
			      filtro_val=$("#filtro_name").find(":selected").val();
            }
			else{
	           filtro_val=$("#filtro_acc").find(":selected").val();
			}
		}
		else{
			var f1=$("#filtro_date").val();
			var f2=$("#filtro_date2").val();
			if(f1!="" && f2!=""){
				filtro_val=f1+";"+f2;
			}
			else{
				filtro="";
				filtro_val="";
			}
		}	
	}
	var data = new FormData();
    data.append("filtro",filtro);
    data.append("filtro_val",filtro_val); 
	$.ajax({
        data: data,
        type: "POST",
        url: "auditoria_data.php",
        cache: false,
        contentType: false,
        processData: false,
		beforeSend:function(url) {
        },
        success: function(dat) {
            $("#tabla_auditoria").html(dat);	
        }
    });	
}
/*Open the Page Required to Write a Pdf with Auditoria Data*/
function reporte_audidoria(){
	window.open("pdf_auditoria.php");
}


/*Send to the Page to Write a Report for a Consult*/
function Pdf_Consults(args){
    $("#error_report").css("display","none");
	$("#error_msg").css("display","none");
	var table="";
	var filter=-1;
	var filter_val=-1;
	var fields=[];
	var field_count=0;
	var valid=false;
	var initial_rows="";
	var title="";
	if(args=="Party"){
		 valid=true;
		 table="fiesta";
		 fields=["CI_cliente","estatus","fecha","tipo_fiesta","publico","lugar"];
	     field_count=fields.length;
		 initial_rows="Cliente;Estatus;Fecha;Tipo de Fiesta;Publico;Sitio;";
	     title="Fiestas Organizadas"
	}
	else if(args=="Rent_Products"){
		valid=true;
		table="producto_alquilado";
        fields=["nombre_producto","formato","cantidad_alquilada","fecha_devolucion","CI_cliente"];
	    field_count=fields.length;
		initial_rows="Producto;Formato;Cantidad Alquil.;Fecha Devol.;Cliente;";
	    title="Productos Alquilados"
	}
	if(valid){
		var datos={"tabla":table,"fields":fields,"filtro":filter,"filtro_val":filter_val};	    
		$.ajax({
           data: datos,
           method: "POST",
           url: "realizar_consulta.php",
           success: function(dat) {			   
			   if(dat.includes("?")){
				  dat=dat.split("?");
				   if(dat[1]!=""){
					  var rows=initial_rows+"|"+dat[1];
					  $("#data_reporte").val(rows);
					  $("#title_report").val(title);
					  var formu=$("#second_form");
					  formu.submit();
					  
				   }
			   }
			   else{
				  $("#error_report").css("display","block");  
			   }
		   }
	    });
	}
}


























