<?php
require('fpdf186/fpdf.php');

class PDF extends FPDF
{
function Header()
	{
$this->setFont('Arial','',14,'C');
$this->Cell(10,0,'                                                       DAFTAR PERMINTAAN PERBAIKAN PERANGKAT LUNAK');
$this->Ln(6);
		
$this->setFont('Arial','',8,'C');
$this->Cell(10,0,'                                                                                                                                                                                                                                                                                                                             FM-IT-00-25.3-013');
$this->Ln(4);

$this->setFont('Arial','',10,'C');
$this->setFillColor(222,222,222);
$this->setX(10);
$this->CELL(7,11,'No',1,0,'C',1);

$this->CELL(17,11,'Tgl Req',1,0,'C',1);
$this->CELL(17,5,'Tgl','LTR',0,'C',1);
$this->CELL(19,5,'Tgl','LTR',0,'C',1);
$this->CELL(17,11,'Tgl Solved',1,0,'C',1);
$this->CELL(40,11,'User',1,0,'C',1);
$this->CELL(50,11,'Permasalahan',1,0,'C',1);
$this->CELL(17,11,'Penerima',1,0,'C',1);
$this->CELL(22,11,'Keterangan',1,0,'C',1);

$this->CELL(15,11,'Kategori',1,0,'C',1);
$this->CELL(19,6,'Durasi','LTR',0,'C',1);
$this->CELL(19,6,'Durasi','LTR',0,'C',1);
$this->CELL(18,6,'Durasi','LRT',0,'C',1);

$this->setY(25);
$this->setX(34);
$this->CELL(17,6,'Approve','LB',0,'C',1);
$this->CELL(19,6,'Pengerjaan','RLB',0,'C',1);

$this->setY(25);
$this->setX(231);
$this->CELL(19,6,'Pengerjaan','LB',0,'C',1);
$this->CELL(19,6,'Request','LB',0,'C',1);
$this->CELL(18,6,'Tunggu','RLB',0,'C',1);
$this->Ln(6);
	}
	
	//Page Content
	function Content()
	{
	}

//Page footer
	function Footer()
	{
		//atur posisi 1.5 cm dari bawah
		$this->SetY(-15);
		//buat garis horizontal
		//$this->Line(10,$this->GetY(),200,$this->GetY());
		//Arial italic 9
		$this->SetFont('Arial','I',9);
		//nomor halaman
		$this->Cell(0,10,'Halaman '.$this->PageNo().' ',0,0,'R');
	}
var $widths;
var $aligns;

function SetWidths($w)
{
	//Set the array of column widths
	$this->widths=$w;
}

function SetAligns($a)
{
	//Set the array of column alignments
	$this->aligns=$a;
}

function Row($data)
{
	//Calculate the height of the row
	$nb=0;
	for($i=0;$i<count($data);$i++)
		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	$h=5*$nb;
	//Issue a page break first if needed
	$this->CheckPageBreak($h);
	//Draw the cells of the row
	for($i=0;$i<count($data);$i++)
	{
		$w=$this->widths[$i];
		$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		//Save the current position
		$x=$this->GetX();
		$y=$this->GetY();
		//Draw the border
		$this->Rect($x,$y,$w,$h);
		//Print the text
		$this->MultiCell($w,5,$data[$i],0,$a);
		//Put the position to the right of the cell
		$this->SetXY($x+$w,$y);
	}
	//Go to the next line
	$this->Ln($h);
}
function Rowdua($data)
{
	//Calculate the height of the row
	$nb=0;
	for($i=0;$i<count($data);$i++)
		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	$h=5*$nb;
	//Issue a page break first if needed
	$this->CheckPageBreak($h);
	//Draw the cells of the row
	for($i=0;$i<count($data);$i++)
	{
		$w=$this->widths[$i];
		$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		//Save the current position
		$x=$this->GetX();
		$y=$this->GetY();
		//Draw the border
		$this->Rect($x,$y,$w,$h);
		//Print the text
		$this->MultiCell($w,5,$data[$i],0,$a);
		//Put the position to the right of the cell
		$this->SetXY($x+$w,$y);
	}
	//Go to the next line
	$this->Ln($h);
}



function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}
}
?>
