<?php

/*return the diference of days of first date(f1) and second date(f2) */
function comparar($f1,$f2){

  $valoresPrimera = explode ("-", $f1); 
  $valoresSegunda = explode ("-", $f2);
  $diaPrimera=$valoresPrimera[0];  
  $mesPrimera=$valoresPrimera[1];  
  $anyoPrimera=$valoresPrimera[2]; 
  $diaSegunda=$valoresSegunda[0];  
  $mesSegunda=$valoresSegunda[1];  
  $anyoSegunda=$valoresSegunda[2];
  $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);  
  $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);     

  if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){
      // first date invalid
      return 0;
  }elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){
      //second date invalid
      return 0;
  }else{
    return  $diasPrimeraJuliano - $diasSegundaJuliano;
  } 
}

/*calculate the diference of time between the two times*/	
function calcular_tiempo($t1,$t2){
	$valoresPrimera = explode (":", $t1); 
    $valoresSegunda = explode (":", $t2);

	if(count($valoresPrimera)==3 && count($valoresSegunda)==3){
	   $horprimera=intval($valoresPrimera[0]);
	   $minprimera=intval($valoresPrimera[1]);
	   $secprimera=intval($valoresPrimera[2]);
	   $horsegunda=intval($valoresSegunda[0]);
	   $minsegunda=intval($valoresSegunda[1]);
	   $secsegunda=intval($valoresSegunda[2]);
	   $totalMins_primera=($horprimera*60)+$minprimera;
	   $totalsecs_primera=( $totalMins_primera*60)+$secprimera;
	   $totalMins_segunda=($horsegunda*60)+$minsegunda;
	   $totalsecs_segunda=( $totalMins_segunda*60)+$secsegunda;
	   $dif_secs=$totalsecs_primera-$totalsecs_segunda;
	   return $dif_secs;  
	}
	else{
		return false;
	}
}
?>