<?php

namespace App\Http\Controllers;


use Codedge\Fpdf\Fpdf\Fpdf;
use Codedge\Fpdf\Fpdf\Exception;

class SPDF extends FPDF
{

    protected $title;
    protected $subTitle;
    protected $subTitle2;


    public function __construct($stitle="", $subTitle="",  $subTitle2="")
    {
        parent::__construct();
        $this->title = $stitle;
        $this->subTitle = $subTitle;
        $this->subTitle2 = $subTitle2;
    }

    // Page header
    function Header()
    {
        // Logo - carefull for path at hosting
        $img = '../public/img/favicon.png';
        $this->Image($img,10,9,17,17);
        
        // Line break
        // $this->Ln(10);
        $this->SetFont('Arial','',10);
        $this->Cell(210,18, $this->title ,0,0,'C');

        $this->Ln(3.5);
        $this->SetFont('Arial','',12);
        $this->Cell(210,20, $this->subTitle ,0,0,'C');

        $this->Ln(1);
        $this->SetFont('Arial','',14);
        $this->Cell(210,30, $this->subTitle2 ,0,0,'C');

        $add1 = 'Jalan AUP No. 3 Pasar Minggu - Jakarta Selatan 12520';
        $add2 = 'TELP/FAX (021) 780665 - 7817611 | EMAIL: ditsayurobat@pertanian.go.id | WEBSITE http://ditsayur.hortikultura.pertanian.go.id';

        $this->Ln(8);
        $this->SetFont('Arial','',8);
        $this->Cell(210,30, $add1 ,0,0,'C');
        $this->Ln(4);
        $this->SetFont('Arial','',8);
        $this->Cell(210,30, $add2 ,0,0,'C');

        $this->SetLineWidth(0.1);
        $this->Line(10, 35, 200, 35);
        
        $this->SetLineWidth(0.8);
        $this->Line(10, 36, 200, 36);


    }   

    
}