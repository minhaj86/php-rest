<?php
require('fpdf.php');
require('../lib/DBL.php');

class PDF extends FPDF
{
// Page header
function Header()
{
	// Logo
	$this->Image('QLogIcon.png',10,6,15);
	// Arial bold 15
	$this->SetFont('Arial','B',15);
	// Move to the right
	$this->Cell(80);
	// Title
	$this->Cell(50,10,'Expense Report',1,0,'C');
	// Line break
	$this->Ln(20);
}

// Page footer
function Footer()
{
	// Position at 1.5 cm from bottom
	$this->SetY(-15);
	// Arial italic 8
	$this->SetFont('Arial','I',8);
	// Page number
	$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	// Position at 1.5 cm from bottom
	$this->SetY(-15);
	$this->SetX(-70);
	// Arial italic 8
	$this->SetTextColor(0,128,0);
	$this->SetFont('','B',10);
	// Page number
	$this->Cell(0,10,'Powered By: QLog.mobi',0,0,'C');
}
// Colored table
function FancyTable($header, $data)
{
	$this->SetX(25);
	// Colors, line width and bold font
	$this->SetFillColor(0,0,0);
	$this->SetTextColor(255);
	$this->SetDrawColor(0,128,0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B',10);
	// Header
	$w = array(40, 35, 40, 45);
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
	$this->Ln();
	// Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('','',6);
	// Data
	$fill = false;
	$rowCount=1;
	foreach($data as $row)
	{
		if($rowCount%38==0){
			$this->SetX(25);
			$this->Cell(array_sum($w),0,'','T');
			$this->AddPage();
			$this->SetX(25);
			// Colors, line width and bold font
			$this->SetFillColor(0,0,0);
			$this->SetTextColor(255);
			$this->SetDrawColor(0,128,0);
			$this->SetLineWidth(.3);
			$this->SetFont('','B',10);
			// Header
			$w = array(40, 35, 40, 45);
			for($i=0;$i<count($header);$i++)
				$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
			$this->Ln();
			// Color and font restoration
			$this->SetFillColor(224,235,255);
			$this->SetTextColor(0);
			$this->SetFont('','',6);
		} else{
			$this->SetX(25);
			$this->Cell($w[0],6,$row[0].$rowCount,'LR',0,'L',$fill);
			$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
			$this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
			$this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
			$this->Ln();
			$fill = !$fill;
		}
		$rowCount++;
	}
	// Closing line
	$this->SetX(25);
	$this->Cell(array_sum($w),0,'','T');
}
// Load data
function LoadData($username)
{
	$dbh = new DBL();
	$dbh->setOpType("select");
	$query = "select * from vehicle_details where user='admin' ;";
	$dbh->run($query);
		
	$data = array();
	$i=0;
	for loop
	// fetch row, 
	// for each column
	$data[$i][]=single column;
	
	return $data;
}

}
// this starts execution
/*		if($dbh->getRowCount()<1){
			return false;
		}
		*/
// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
//$pdf->AddPage();
//$pdf->SetFont('Times','',12);
// Column headings
$header = array('Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)');
// Data loading
$data = $pdf->LoadData('countries.txt');
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output();
?>

