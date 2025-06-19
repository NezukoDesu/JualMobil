<?php
require_once('../tcpdf/tcpdf.php');

class PDFGenerator extends TCPDF {
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'logo.jpg';
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 30);
        }
        
        // Title
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 15, 'LAPORAN PEMESANAN MOBIL', 0, false, 'C', 0);
        $this->Ln(20);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C');
    }

    public function ColoredTable($header, $data, $widths) {
        // Header colors
        $this->SetFillColor(38, 84, 124); // Darker blue
        $this->SetTextColor(255);
        $this->SetDrawColor(38, 84, 124);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B', 10);

        // Header
        foreach($header as $i => $col) {
            $this->Cell($widths[$i], 8, $col, 1, 0, 'C', 1);
        }
        $this->Ln();

        // Data rows
        $this->SetFillColor(242, 247, 252); // Light blue
        $this->SetTextColor(0);
        $this->SetFont('', '', 10);
        $fill = false;

        foreach($data as $row) {
            foreach($row as $i => $col) {
                $align = ($i == 0) ? 'C' : 'L'; // Center align for number column
                $this->Cell($widths[$i], 7, $col, 'LR', 0, $align, $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }

        // Closing line
        $this->Cell(array_sum($widths), 0, '', 'T');
    }
}
