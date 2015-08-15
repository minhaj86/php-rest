<?php
require('fpdf.php');
require('../lib/DBL.php');

class PDF extends FPDF
{
	var $Name = 'User';
	
	function __construct($Name) {
		parent::__construct();
		$this->Name = $Name;
	}	
	
	// Page header
	function Header()
	{
		$dbh = new DBL();
		$dbh->setOpType("select");
		$query = "SELECT * FROM user_details WHERE user='".$this->Name."' ;";
		$dbh->run($query);
		if($dbh->getRowCount()<1){
		} else {
			$j=0;
			foreach ($dbh->getColumns() as $col => $val){
				$this->UserInfo[$col]=$val;
			}
		}
		// Logo
		$this->Image('QLogIcon.png',100,16,15);
		// Arial bold 15
		$this->Ln(23);
		// Move to the right
		// Title
		$this->Cell(70);
		$this->Cell(50,10,'Expense Report',1,0,'C');
		$this->Ln(10);
		$this->SetFont('Arial','',8);
		$this->Cell(20);
		$this->Cell(90,10,'Name: '.$this->UserInfo['first_name'].' '.$this->UserInfo['last_name'].'   Mobile Number: '.$this->UserInfo['mobile_number'].'   Email: '.$this->UserInfo['email_address'],0,0,'L');
		
		// Line break
		$this->Ln(10);
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
		$this->SetFont('','B',7);
		// Header
		$w = array(17, 17, 17, 17,17,17,17,17,17,17,17);
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
				$this->SetFont('','B',7);
				// Header
				$w = array(17, 17, 17, 17,17,17,17,17,17,17,17);
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
				$this->Cell($w[3],6,$row[3],'LR',0,'R',$fill);
				$this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
				$this->Cell($w[5],6,number_format($row[5]),'LR',0,'L',$fill);
				$this->Cell($w[6],6,$row[6],'LR',0,'L',$fill);
				$this->Cell($w[7],6,number_format($row[7]),'LR',0,'L',$fill);
				$this->Cell($w[8],6,$row[8],'LR',0,'L',$fill);
				$this->Cell($w[9],6,$row[9],'LR',0,'L',$fill);
				$this->Ln();
				$fill = !$fill;
			}
			$rowCount++;
		}
		// Closing line
		$this->SetX(25);
		$this->Cell(array_sum($w)-17,0,'','T');
	}
	// Load data
	function LoadData($file)
	{
		// Read file lines
		$lines = file($file);
		$data = array();
		foreach($lines as $line)
			$data[] = explode(';',trim($line));
		return $data;
	}

}

$dbh = new DBL();
$dbh->setOpType("select");
$query = "SELECT v.registration_number,l.date_begin,l.start_odometer_reading,l.start_location,l.date_finish,l.end_odometer_reading,l.end_location,l.kilometre_travelled,l.travel_type,l.purpose_of_the_journey FROM user_details u,vehicle_details v,vehicle_logs l where u.user=v.user and v.registration_number=l.registration_number and u.user='".$_GET['user']."' ;";
$dbh->run($query);
if($dbh->getRowCount()<1){
} else {
	$i=0;
	while (1){
		$j=0;
		foreach ($dbh->getColumns() as $col => $val){
			$data[$i][$j]=$val;
			$j++;
		}
		$i++;
		if($dbh->fetchNext()==1)
			continue;
		else 
			break;
	}
}
//		print_r($data);
// Instanciation of inherited class
$pdf = new PDF($_GET['user']);
$pdf->AliasNbPages();
//$pdf->AddPage();
//$pdf->SetFont('Times','',12);
// Column headings
$header = array('Reg. No', 'Date Begin', 'Start Reading', 'Start Location','Date Finish','End Reading','End Location','KM Traveled','Travel Type','Purpose');
// Data loading
//$data = $pdf->LoadData('countries.txt');
$pdf->SetTitle('Detailed Log - '.$_GET['user']);
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output();

?>

