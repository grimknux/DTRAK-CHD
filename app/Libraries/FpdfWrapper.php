<?php namespace App\Libraries;

use Fpdf\Fpdf;

class FpdfWrapper extends Fpdf {
    // Add any additional methods or overrides here


    public function addFonts(){

        $this->AddFont('Barlow','BL','BarlowBlack.php');
        $this->AddFont('Barlow','BLI','BarlowBlackItalic.php');
        $this->AddFont('Barlow','BXB','BarlowExtraBold.php');
        $this->AddFont('Barlow','BXBI','BarlowExtraBoldItalic.php');
        $this->AddFont('Barlow','B','BarlowBold.php');
        $this->AddFont('Barlow','BI','BarlowBoldItalic.php');
        $this->AddFont('Barlow','SB','BARLOW-SEMIBOLD.php');
        
        $this->AddFont('Lora','B','LoraBold.php');
        $this->AddFont('Lora','BI','LoraBoldItalic.php');
        $this->AddFont('Lora','I','LoraItalic.php');
        $this->AddFont('Lora','','LoraRegular.php');
        
        $this->AddFont('KidsMagazine','','KidsMagazine.php');
        $this->AddFont('SuperComic','','SuperComic.php');
        
    }
}