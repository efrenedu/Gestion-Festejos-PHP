<?php 
require "conexion_bd.php";
require "fpdf/fpdf.php";

/*Generate a Pdf from Auditoria Data*/
date_default_timezone_set('America/Caracas'); 
if(validar_conexion()){
	$dat=get_data_dict("reporte_usuario",["id_reporte_usr" , "nombre_usuario" , "accion" ,"fecha","hora"],-1,-1,-1);
	if(count($dat)>0){
		 $espaciadoX=47;
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
		 $rows=array();
		 $fecha=strval(date("d-m-Y"));	
		 for ($i=0 ;$i<count($dat);$i++){
			$user= $dat[$i]["nombre_usuario" ];
			$accion=$dat[$i]["accion" ];
			$fecha=$dat[$i]["fecha" ];
			$hora=$dat[$i]["hora" ];
			$row=array($user,$accion,$fecha,$hora);
			$rows[]=$row;
		 }
		 $num_rows=count($rows);
		 $header=array();
		 $title_celdas=array("Usuario","Accion","Fecha","Hora");
		 $title=" Reporte de Acciones de los Usuarios";
		 $last_row=0;
		 while($last_row<$num_rows){
			if($lineaActual==0){
				//Write Header and Title
				$pdf->Image("images/globos.png",35,0,30,30);
		        $pdf->SetLeftMargin(10);
                $pdf->SetTitle('Reporte: Auditoria');
				$pdf->SetFont('Arial', '', $fontNumber+5);
		        $pdf->MultiCell(0,$espaciadoY, utf8_decode('Agencia de Festejos Ruby C.A.'), 0, 'C');
                $pdf->Ln();
				$pdf->Cell( 0, $espaciadoY, "Reporte de Auditoria", 0, 1, 'C');
		        $pdf->SetFont('Arial', '', $fontNumber);
				$pdf->MultiCell(0,$espaciadoY, utf8_decode('fecha Emision:'.$fecha), 0, 'L');
                $pdf->Ln();
			    //Add number of Lines to Count of Lines
			    $lineaActual=5; 
				
				for($j=0;$j<count($title_celdas);$j++) {
                   $patr=1;
				   $t=$title_celdas[$j];
				   if($j<count($title_celdas)-1){
					   $patr=0;
				   }
				   else{
					  $lineaActual=$lineaActual+1; 
				   }
				   $pdf->Cell( $espaciadoX, $espaciadoY,$t, 1, $patr, 'C');
                }
			   
			}
			$r=$rows[$last_row];
			$texto="";
			for($k=0;$k<count($r);$k++){
				$texto=$r[$k];
				$temp_text="";
				$temp_val=0;
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
				if($k<count($r)-1){
					$patron=0;
				}
				$pdf->Cell( $espaciadoX, $espaciadoY,$texto, 1, $patron, 'C');
			}
            $last_row=$last_row+1;
			$lineaActual=$lineaActual+1;
		    if($lineaActual>=$numLineas){
				$lineaActual=0;
				$pdf->AddPage("P", "A4");
		    }				
		 }
		 $pdf->Output('I','reporteAuditoria'.$fecha.'.pdf');   
	}		
}
?>