<?php

require_once '/var/www/html/vendor/tecnickcom/tcpdf/tcpdf.php';
require_once '/var/www/html/send_email/vendor/autoload.php';

class CustomTCPDF extends TCPDF
{
    private $isFirstPage = true;
    private $headerInfo;

    // Constructor with header information
    public function __construct($headerInfo)
    {
        parent::__construct();
        $this->headerInfo = $headerInfo;
    }

    // Page header
    public function Header()
    {
        // Display header only on the first page
        if ($this->isFirstPage) {

            // Logo
            $this->Image('/var/www/html/static/images/logo-diginext.png', 10, 6, 30);
            $this->SetFont('dejavusans', 'I', 16);
            $this->SetTextColor(0, 0, 255); // Sky blue color

            // Move to the right
            $this->Cell(80);
            // Title
            $this->Cell(30, 34, 'Báo Cáo Cuộc Gọi Hệ Thống VOS', 0, 0, 'C');
            $this->Ln();

            // Display additional header information
            $this->SetFont('dejavusans', 'I', 12);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 0, $this->headerInfo, 0, 0, 'C');

            // Set flag to false after the header is displayed
            $this->isFirstPage = false;
        }
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('dejavusans', 'I', 8);
        // Page number
        $this->Cell(25);

        $this->Cell(0, 10, 'Trang ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}