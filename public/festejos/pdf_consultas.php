<?php 
require "conexion_bd.php";
require "fpdf/fpdf.php";

/*Generate a Pdf from Auditoria Data*/
date_default_timezone_set('America/Caracas'); 

if(count($_POST)>0){
	$report_data=$_POST['data_reporte'];
	$title_report=$_POST['title_report'];
	if(isset($report_data) && isset($title_report)){
		$rows=explode("|",$report_data);
        $espaciadoX=30;
		$espaciadoY=10;
		$fontSize=0.1828;
		$fontNumber=10;
		$heigthLetters=$fontSize*$fontNumber;	 
		$lineHeigth= $heigthLetters+ $espaciadoY;
		$pdf=new FPDF();
		$pdf->SetFont('Arial', '', $fontNumber);
	    $pdf->AddPage("P", "A4");
		$w=$pdf->GetPageWidth();
		$h=$pdf->GetPageHeight();
		$numLineas=round($h/$lineHeigth);
		$lineaActual=0;
		$fecha=strval(date("d-m-Y"));	
        $num_rows=count($rows);	
        $header=array();;
		$last_row=0;
        while($last_row<$num_rows){
			if($lineaActual==0){
				//Write Header and Title
				$pdf->Image("images/globos.png",35,0,30,30);
		        $pdf->SetLeftMargin(10);
                $pdf->SetTitle('Reporte: Consultas');
				$pdf->SetFont('Arial', '', $fontNumber+5);
		        $pdf->MultiCell(0,$espaciadoY, utf8_decode('Agencia de Festejos Ruby C.A.'), 0, 'C');
                $pdf->Ln();
				$pdf->Cell( 0, $espaciadoY, $title_report, 0, 1, 'C');
		        $pdf->SetFont('Arial', '', $fontNumber);
				$pdf->MultiCell(0,$espaciadoY, utf8_decode('fecha Emision:'.$fecha), 0, 'L');
                $pdf->Ln();
			    //Add number of Lines to Count of Lines
			    $lineaActual=5; 
			}
			
			$columns=explode(";",$rows[$last_row]);
			for($i=0;$i<count($columns);$i++){
				if($columns[$i]!=""){
					$texto=$columns[$i];
					$temp_val=0;
					$temp_text="";
					for($j=0;$j<strlen($texto);$j++){
					   if($temp_val<$espaciadoX){
						   $temp_text=$temp_text.$texto[$j];
					   }
					   else{
					  	   $j=strlen($texto);
					   }
					   $temp_val=$temp_val+$heigthLetters;
                      					   
				    }
					$texto=$temp_text;
				    $patron=1;
				    if($i<count($columns)-1){
					    $patron=0;
				    }
				    $pdf->Cell( $espaciadoX, $espaciadoY,$texto, 1, $patron, 'C');
		
				}
				else{
					 $pdf->Cell( $espaciadoX, $espaciadoY,"", 0, 1, 'C');
				     $i=count($columns);
				}
			}
	
            $last_row=$last_row+1;
			$lineaActual=$lineaActual+1;
		    if($lineaActual>=$numLineas){
				$lineaActual=0;
				$pdf->AddPage("P", "A4");
		    }				
		}
		$pdf->Output('I','reporteConsulta'.$fecha.'.pdf');		
	}
}
?>