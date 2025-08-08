<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\FpdfWrapper;
use App\Models\EmployeeModel;
use App\Models\PDFModel;

class PdfController extends BaseController
{

    public $EmployeeModel;
    public $fpdf;
    public $PDFModel;

    public function __construct()

    {
        $this->fpdf = new FpdfWrapper();
        $this->EmployeeModel = new EmployeeModel(); 
        $this->PDFModel = new PDFModel(); 
        helper(['form','html','cookie','array', 'test']);
        
    }


    public function generate_pdf($id)
    {
        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }


        $pdf = $this->fpdf;
        $employee = $this->EmployeeModel->getEmployee($id);

        if ($employee) {
            // Access employee values
            $employeeID = $employee['Employee_ID'];
            $firstName = $employee['FirstName'];
            $lastName = $employee['LastName'];
            $signature = $employee['Signature'];
            $profilePhoto = $employee['ProfilePhoto'];

            if(is_null($employee['Bloodtype']) || $employee['Bloodtype'] == ""){
                $blood = "-";
            }else{
                $blood = $employee['Bloodtype'];
            }
            
            if($employee['Division']=='RLED'){
                $section = ' ';
            }else{
                $section = $employee['AreaOfAssignment'];
            }

            if ($profilePhoto != "") {

                $filePath = FCPATH . 'public/images/photos/' . $profilePhoto;
                
                if (file_exists($filePath)) 
                {
					$imageSrc = ROOTPATH.'public/images/photos/' . $profilePhoto;
				}else{

                    
					$imageSrc =ROOTPATH.'public/images/profile.jpg';
				}
            } else {
                $imageSrc = ROOTPATH.'public/images/profile.jpg';
            }
				
            if ($signature != "") {
                $filePath = FCPATH . 'public/images/signature/' . $signature;
                
                if (file_exists($filePath)) 
                {
					$signaturePhoto = ROOTPATH.'public/images/signature/' . $signature;
				}else{
					$signaturePhoto = ROOTPATH.'public/images/nosign.jpg';
				}
            } else {
                $signaturePhoto = ROOTPATH.'public/images/nosign.png';
            }
				
            
            $pdf->addFonts();
            
            $pdf->SetTitle('Employee Details - '.$firstName.' '.$lastName); // Set the PDF title

            // Add your PDF generation logic here
            // For example:
            //$pdf->AddPage();
            //$pdf->SetFont('Lora', 'B', 16);
            //$pdf->Cell(40, 10, 'Hello, World! - '.$firstName);

            // Output the PDF
            if($employee['TypeOfEmployment'] == 'CONTRACTUAL'){
                #tin number
                $tin = $employee['TINNumber'];
                $tin_number = substr($tin, 0, 3);
                $tin_number .= "-".substr($tin,3, 3);
                $tin_number .= "-".substr($tin,6, 3);
                if(strlen($tin) > 9){
                    $tin_number .= "-".substr($tin,9, 50);
                }
                
                #philhealth number
                $phic = $employee['Philhealth'];
                $phic_number = substr($phic, 0, 2);
                $phic_number .= "-".substr($phic,2, 9);
                $phic_number .= "-".substr($phic,11, 1);
                
                #SSS number
                $sss = $employee['SSS'];
                $sss_number = substr($sss, 0, 2);
                $sss_number .= "-".substr($sss,2, 7);
                $sss_number .= "-".substr($sss,9, 1);
                
                $pdf->AddPage();
                    
                // Line break
                $pdf->Ln(10);
                    
                // Position at 1.5 cm from bottom
                
                $pdf->Image(ROOTPATH.'public/images/bg_new2.png',22.6,26,82.3);
                $pdf->Image(ROOTPATH.'public/images/bg_back.png',104.5,26,82.3);
                
                
                $pdf->SetFont('Barlow','BXB', 14);
                $pdf->SetY(77);	
                $pdf->SetX(78.5);
                $pdf->SetFillColor(15 , 143, 70);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(100, 100, 100);
                $pdf->Cell(20,7,$employee['Division'],1,1,'C',1);
                //$pdf->Line(77, 83, 100, 83);
                
                $sect_len = strlen($section);
                if($sect_len > 8){
                    $pdf->SetFont('Barlow','BXB', 12);
                    $pdf->SetY(84);
                    $pdf->SetX(78.5);
                    $pdf->SetTextColor(100, 100, 100);
                    $pdf->SetFillColor(255, 207, 53);
                    $pdf->SetDrawColor(100, 100, 100);
                    $pdf->CellFitScaleColor(20,5,$section,1,1,'',1);
                }else{
                    $pdf->SetFont('Barlow','BXB', 12);
                    $pdf->SetY(84);
                    $pdf->SetX(78.5);
                    $pdf->SetTextColor(100, 100, 100);
                    $pdf->SetFillColor(255, 207, 53);
                    $pdf->SetDrawColor(100, 100, 100);
                    $pdf->Cell(20,5,$section,1,1,'C',1);
                    
                }
                
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetFillColor(0, 0, 0);
                
                $pdf->SetFont('Barlow','BL', 25);
                $pdf->SetY(90);
                $pdf->SetX(44);
                $pdf->SetTextColor(0 , 0, 0);
                $pdf->CellFitScale(57,10,strtoupper(utf8_decode($employee['FirstName'])),0,1,'',1);				
                
                $pdf->SetFont('Barlow','BXB', 20);
                $pdf->SetY(98);
                $pdf->SetX(44);
                $pdf->SetTextColor(80, 80, 80);
                $pdf->CellFitScale(55,10,strtoupper(substr($employee['MiddleName'], 0, 1)).". ".strtoupper(utf8_decode($employee['LastName']))." ".strtoupper($employee['Suffix']),0,1,'',1);
                
                $pdf->SetFont('Lora','B', 10);
                $pdf->SetY(107);
                $pdf->SetX(44);
                $pdf->SetTextColor(0 , 0, 0);
                $pdf->CellFitScale(50,4,strtoupper($employee['Position']),0,1,'',1);
                $pdf->Line(45.3, 111, 93, 111);
                
                $pdf->SetFont('Barlow','BXB', 10);
                $pdf->SetY(112);
                $pdf->SetX(44);
                $pdf->SetTextColor(0 , 0, 0);
                $pdf->Cell(50,4,"ID No.: ".$employee['Employee_ID'],0,"","C","");
                
                $pdf->Image($signaturePhoto,70,120,25,14);
                $pdf->Image($imageSrc,37.5,53.5,36, 36);
                
                $pdf->SetFont('Barlow','BXB', 5);
                $pdf->SetTextColor(15 , 143, 70);
                $pdf->TextWithDirection(25,138,"Effectivity: ",'U');
                
                $pdf->SetFont('Barlow','BXB', 5);
                $pdf->SetTextColor(15 , 143, 70);
                $pdf->TextWithDirection(27.5,138,date('F d, Y', strtotime($employee['ContractDuration_start']))." - ".date('F d, Y', strtotime($employee['ContractDuration_end'])),'U');
                
                #-------------------------------------------BACK PAGE-------------------------------------------
                
                
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetY(35);
                $pdf->SetX(108);
                $pdf->Cell(76,20,"",1,'L',"");
                
                $pdf->SetFont('Barlow','BXB', 9);
                $pdf->SetY(35);
                $pdf->SetX(108);
                $pdf->MultiCell(76,4,"ADDRESS:",1,'L',"");
                
                $pdf->SetFont('Barlow','SB', 10);
                $pdf->SetY(40);
                $pdf->SetX(108);
                $pdf->MultiCell(76,5,strtoupper(utf8_decode($employee['Address'])),0,'C',"");
                
                #border - DETAILS
                $pdf->SetY(57);
                $pdf->SetX(108);
                $pdf->Cell(76,33,"",1,"","","");
                
                #text inside border box
                $pdf->SetFont('Barlow','BXB', 8);
                $pdf->SetY(58);
                $pdf->SetX(112);
                $pdf->Cell(18,5,"BIRTHDATE",0,"","C","");
                
                $pdf->SetFont('Barlow','SB', 8);
                $pdf->SetY(63);
                $pdf->SetX(112);
                $pdf->Cell(18,5,date("M. d, Y", strtotime($employee['Birthdate'])),"B","","C","");
                
                $pdf->SetFont('Barlow','BXB', 8);
                $pdf->SetY(58);
                $pdf->SetX(133);
                $pdf->Cell(22,5,"BLOOD TYPE",0,"","C","");
                
                $pdf->SetFont('Barlow','SB', 8);
                $pdf->SetY(63);
                $pdf->SetX(139);
                $pdf->Cell(10,5,$blood,"B","","C","");
                
                $pdf->SetFont('Barlow','BXB', 8);
                $pdf->SetY(58);
                $pdf->SetX(160);
                $pdf->Cell(18,5,"TIN NO.",0,"","C","");
                
                $pdf->SetFont('Barlow','SB', 8);
                $pdf->SetY(63);
                $pdf->SetX(159);
                $pdf->Cell(20,5,$tin_number,"B","","C","");
                
                $pdf->SetFont('Barlow','BXB', 8);
                $pdf->SetY(75);
                $pdf->SetX(120);
                $pdf->Cell(18,5,"PHILHEALTH NO.",0,"","C","");
                
                $pdf->SetFont('Barlow','SB', 8);
                $pdf->SetY(80);
                $pdf->SetX(118);
                $pdf->Cell(22,5,$phic_number,"B","","C","");
                
                $pdf->SetFont('Barlow','BXB', 8);
                $pdf->SetY(75);
                $pdf->SetX(152);
                $pdf->Cell(18,5,"SSS NO.",0,"","C","");
                
                $pdf->SetFont('Barlow','SB', 8);
                $pdf->SetY(80);
                $pdf->SetX(150);
                $pdf->Cell(22,5,$sss_number,"B","","C","");
                
                #border - EMERGENCY
                $pdf->SetY(92);
                $pdf->SetX(108);
                $pdf->Cell(76,15,"",1,"","","");
                
                $pdf->SetFont('Barlow','B', 8);
                $pdf->SetY(92);
                $pdf->SetX(108);
                $pdf->Cell(53.5,5,"Person to notify in case of emergency:",1,"","L","");
                
                $pdf->SetFont('Barlow','SB', 8);
                $pdf->SetY(98);
                $pdf->SetX(111);
                $pdf->Cell(70,5,"Name: ".strtoupper(utf8_decode($employee['NameOfPersonToNotify'])),0,"","L","");
                
                $pdf->SetFont('Barlow','SB', 8);
                $pdf->SetY(102);
                $pdf->SetX(111);
                $pdf->Cell(70,5,"Contact No.: ".$employee['CPNumber'],0,"","L","");
                
                $pdf->SetFont('Barlow','B', 8);
                $pdf->SetY(108);
                $pdf->SetX(108);
                $pdf->MultiCell(76,4,"This is to certify the person whose picture and signature appear hereon is an employee of Ilocos CHD, SFLU",0,'C',"");
                
                //$pdf->Line(108, 128, 184, 128);
                
                $pdf->Rect(108, 128, 76, .5, "F");
                
                $pdf->SetFont('Barlow','BXB', 8);
                $pdf->SetY(128);
                $pdf->SetX(108);
                $pdf->Cell(76,5,"PAULA PAZ M. SYDIONGCO, MD, MPH, MBA, CESO IV",0,"","C","");
                
                $pdf->SetFont('Barlow','B', 7);
                $pdf->SetY(131);
                $pdf->SetX(108);
                $pdf->Cell(76,5,"DIRECTOR IV",0,"","C","");	
                
                $pdf->Output();
                
            }elseif($employee['TypeOfEmployment'] == 'PERMANENT'){
		
                    $pdf->AddPage();
                    
                    // Line break
                    $pdf->Ln(10);
                    $x = 5;
                    // Position at 1.5 cm from bottom
                    //$pdf->Image(base_url().'public/images/a4.jpg',0,0,210);
                    $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',8+$x,8.5,95.2);
                    $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',103.1+$x,8.5,95.2);
                    
    
                    $pdf->SetFont('Barlow','BXB', 14);
                    $pdf->SetY(133.8);//128.8
                    $pdf->SetX(9.3+$x);//22.8
                    $pdf->SetTextColor(255 , 255, 255);
                    $pdf->Cell(17,5,$employee['Division'],0,1,'C',0);
                    
                    $pdf->SetFont('Barlow','BXB', 14);
                    $pdf->SetY(134);//129
                    $pdf->SetX(9.5+$x);//23
                    $pdf->SetTextColor(15 , 143, 70);
                    $pdf->Cell(17,5,$employee['Division'],0,1,'C',0);
                    
                    $sect_len = strlen($section);
                    if($sect_len > 6){
                        // /$section
                        $pdf->SetFont('Barlow','BXB', 13);
                        $pdf->SetY(137.8);	
                        $pdf->SetX(9.3+$x);//22.8
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->CellFitScale(17,5,$section,0,1,'',0);
                        
                        $pdf->SetY(138);//133	
                        $pdf->SetX(9.5+$x);//23
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->CellFitScale(17,5,$section,0,1,'',0);
                        
                    }else{
                        
                        $pdf->SetFont('Barlow','BXB', 13);
                        $pdf->SetY(137.8);	
                        $pdf->SetX(9.3+$x);//22.8
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Cell(17,5,$section,0,1,'C',0);
                        
                        $pdf->SetY(138);//133	
                        $pdf->SetX(9.5+$x);//23
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(17,5,$section,0,1,'C',0);
                        
                    }
                    
                    $pdf->SetDrawColor(0, 0, 0);
                    $pdf->SetFillColor(0, 0, 0);
                    
                    if(strlen($employee['NickName']) > 3){
                        $pdf->SetFont('KidsMagazine','', 47);
                        $pdf->SetY(111.5);
                        $pdf->SetX(41.5+$x);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->CellFitScale(50,25,strtoupper($employee['NickName']),0,1,'',0);

                        $pdf->SetFont('KidsMagazine','', 47);
                        $pdf->SetY(112);
                        $pdf->SetX(42+$x);
                        $pdf->SetTextColor(40, 92, 67);
                        $pdf->CellFitScale(50,25,strtoupper($employee['NickName']),0,1,'',0);
                        
                    }else{
                        
                        $pdf->SetFont('KidsMagazine','', 47);
                        $pdf->SetY(111.5);
                        $pdf->SetX(41.5+$x);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(50,25,strtoupper($employee['NickName']),0,1,'C',0);
                        
                        $pdf->SetFont('KidsMagazine','', 47);
                        $pdf->SetY(112);
                        $pdf->SetX(42+$x);
                        $pdf->SetTextColor(40, 92, 67);
                        $pdf->Cell(50,25,strtoupper($employee['NickName']),0,1,'C',0);

                        
                    }		
                    
                    $pdf->SetTextColor(0 , 0, 0);
                    
                    $name = strtoupper(utf8_decode($employee['FirstName']." ".substr($employee['MiddleName'], 0, 1).". ".$employee['LastName']))." ".strtoupper($employee['Suffix']);
                    if(strlen($name) > 25){
                        
                        $pdf->SetFont('Barlow','B', 9);
                        $pdf->SetY(132);//121
                        $pdf->SetX(53+$x);//56
                        $pdf->SetTextColor(0 , 0, 0);
                        $pdf->CellFitScale(44,4,$name,0,1,'',0);
                        $pdf->Line(50.7+$x, 136, 99+$x, 136);
                        
                    }else{
                        
                        $pdf->SetFont('Barlow','B', 9);
                        $pdf->SetY(132);//121
                        $pdf->SetX(53+$x);//56
                        $pdf->SetTextColor(0 , 0, 0);
                        $pdf->Cell(44,4,$name,0,1,'C',0);
                        $pdf->Line(50.7+$x, 136, 99+$x, 136);
                    }
                    
                    if(strlen($employee['Position']) > 26){
                        $pdf->SetFont('Barlow','SB', 9);
                        $pdf->SetY(136);//125
                        $pdf->SetX(53+$x);//56
                        $pdf->SetTextColor(100 , 100, 100);
                        $pdf->CellFitScale(44,4,strtoupper($employee['Position']),0,1,'',0);
                    }else{
                        $pdf->SetFont('Barlow','SB', 9);
                        $pdf->SetY(136);//125
                        $pdf->SetX(53+$x);//56
                        $pdf->SetTextColor(100 , 100, 100);
                        $pdf->Cell(44,4,strtoupper($employee['Position']),0,1,'C',0);
                    }
                    
                    $pdf->SetFont('Barlow','B', 9);
                    $pdf->SetY(141);//131
                    $pdf->SetX(53+$x);//56
                    $pdf->SetTextColor(0 , 0, 0);
                    $pdf->Cell(44,4,"ID No.: ".$employee['Employee_ID'],0,"","C","");
                    //$pdf->Line(62, 135, 94, 135);
                    
                    $pdf->Image($imageSrc,25.3+$x, 45.7, 50.8, 50.8);
                    //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
                    
                    
                    #-------------------------------------------BACK PAGE-------------------------------------------
                    
                    $x = 100.1;
                    
                    $pdf->SetFont('Barlow','BXB', 14);
                    $pdf->SetY(133.8);//128.8
                    $pdf->SetX(9.3+$x);//22.8
                    $pdf->SetTextColor(255 , 255, 255);
                    $pdf->Cell(17,5,$employee['Division'],0,1,'C',0);
                    
                    $pdf->SetFont('Barlow','BXB', 14);
                    $pdf->SetY(134);//129
                    $pdf->SetX(9.5+$x);//23
                    $pdf->SetTextColor(15 , 143, 70);
                    $pdf->Cell(17,5,$employee['Division'],0,1,'C',0);
                    
                    $sect_len = strlen($section);
                    if($sect_len > 6){
                        // /$section
                        $pdf->SetFont('Barlow','BXB', 13);
                        $pdf->SetY(137.8);	
                        $pdf->SetX(9.3+$x);//22.8
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->CellFitScale(17,5,$section,0,1,'',0);
                        
                        $pdf->SetY(138);//133	
                        $pdf->SetX(9.5+$x);//23
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->CellFitScale(17,5,$section,0,1,'',0);
                        
                    }else{
                        
                        $pdf->SetFont('Barlow','BXB', 13);
                        $pdf->SetY(137.8);	
                        $pdf->SetX(9.3+$x);//22.8
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Cell(17,5,$section,0,1,'C',0);
                        
                        $pdf->SetY(138);//133	
                        $pdf->SetX(9.5+$x);//23
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(17,5,$section,0,1,'C',0);
                        
                    }
                    
                    $pdf->SetDrawColor(0, 0, 0);
                    $pdf->SetFillColor(0, 0, 0);
                    
                    if(strlen($employee['NickName']) > 3){
                        $pdf->SetFont('KidsMagazine','', 47);
                        $pdf->SetY(111.5);
                        $pdf->SetX(41.5+$x);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->CellFitScale(50,25,strtoupper($employee['NickName']),0,1,'',0);

                        $pdf->SetFont('KidsMagazine','', 47);
                        $pdf->SetY(112);
                        $pdf->SetX(42+$x);
                        $pdf->SetTextColor(40, 92, 67);
                        $pdf->CellFitScale(50,25,strtoupper($employee['NickName']),0,1,'',0);
                        
                    }else{
                        
                        $pdf->SetFont('KidsMagazine','', 47);
                        $pdf->SetY(111.5);
                        $pdf->SetX(41.5+$x);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(50,25,strtoupper($employee['NickName']),0,1,'C',0);
                        
                        $pdf->SetFont('KidsMagazine','', 47);
                        $pdf->SetY(112);
                        $pdf->SetX(42+$x);
                        $pdf->SetTextColor(40, 92, 67);
                        $pdf->Cell(50,25,strtoupper($employee['NickName']),0,1,'C',0);

                        
                    }		
                    
                    $pdf->SetTextColor(0 , 0, 0);
                    
                    $name = strtoupper(utf8_decode($employee['FirstName']." ".substr($employee['MiddleName'], 0, 1).". ".$employee['LastName']))." ".strtoupper($employee['Suffix']);
                    if(strlen($name) > 25){
                        
                        $pdf->SetFont('Barlow','B', 9);
                        $pdf->SetY(132);//121
                        $pdf->SetX(53+$x);//56
                        $pdf->SetTextColor(0 , 0, 0);
                        $pdf->CellFitScale(44,4,$name,0,1,'',0);
                        $pdf->Line(50.7+$x, 136, 99+$x, 136);
                        
                    }else{
                        
                        $pdf->SetFont('Barlow','B', 9);
                        $pdf->SetY(132);//121
                        $pdf->SetX(53+$x);//56
                        $pdf->SetTextColor(0 , 0, 0);
                        $pdf->Cell(44,4,$name,0,1,'C',0);
                        $pdf->Line(50.7+$x, 136, 99+$x, 136);
                    }
                    
                    if(strlen($employee['Position']) > 26){
                        $pdf->SetFont('Barlow','SB', 9);
                        $pdf->SetY(136);//125
                        $pdf->SetX(53+$x);//56
                        $pdf->SetTextColor(100 , 100, 100);
                        $pdf->CellFitScale(44,4,strtoupper($employee['Position']),0,1,'',0);
                    }else{
                        $pdf->SetFont('Barlow','SB', 9);
                        $pdf->SetY(136);//125
                        $pdf->SetX(53+$x);//56
                        $pdf->SetTextColor(100 , 100, 100);
                        $pdf->Cell(44,4,strtoupper($employee['Position']),0,1,'C',0);
                    }
                    
                    $pdf->SetFont('Barlow','B', 9);
                    $pdf->SetY(141);//131
                    $pdf->SetX(53+$x);//56
                    $pdf->SetTextColor(0 , 0, 0);
                    $pdf->Cell(44,4,"ID No.: ".$employee['Employee_ID'],0,"","C","");
                    //$pdf->Line(62, 135, 94, 135);
                    
                    $pdf->Image($imageSrc,25.3+$x, 45.7, 50.8, 50.8);
                    //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
    
    
    
    
    
                
                $pdf->Output();
            
            } else {
                 // Handle case when employee is not found
                $pdf->addFonts();
            
                $pdf->SetTitle('Employee Not Found'); // Set the PDF title
                // Add your PDF generation logic here
                // For example:
                $pdf->AddPage();
                $pdf->SetFont('Lora', 'B', 16);
                $pdf->Cell(40, 10, 'Employee not Found!');

                // Output the PDF
                $pdf->Output(); // Capture the PDF content as a string
            }

        } else {
            // Handle case when employee is not found
            $pdf->addFonts();
        
            $pdf->SetTitle('Employee Not Found'); // Set the PDF title
            // Add your PDF generation logic here
            // For example:
            $pdf->AddPage();
            $pdf->SetFont('Lora', 'B', 16);
            $pdf->Cell(40, 10, 'Employee not Found!');

            // Output the PDF
            $pdfContent = $pdf->Output(); // Capture the PDF content as a string

            return $this->response->setJSON(['success' => true, 'pdfContent' => $pdfContent]);
        }


        //echo $pdfContent;
        exit();
    }



    #BULK PER SECTION -----------------------------------------------------------------------------------

    public function checkbulk(){

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }

        $data = [];
        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');
        $validation = \Config\Services::validation();


        if ($this->request->getMethod() === 'post' && !empty($csrfToken) && $this->validateCSRFToken($csrfToken)) {
            
            $rules = [
                'employeetype' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please select Employee Type!',
                    ],
                ],
            ];

            
            if ($this->validate($rules)) {
                $validatedData = [];
                // Validation success
                // Generate and set the CSRF token for the response
                $validatedData = [
                    'emptype' => $this->request->getVar('employeetype'),
                    'csrfToken' => csrf_hash(), // Regenerate the CSRF token here
                ];

                if($this->request->getVar('division') != ""){
                    $validatedData['div'] = $this->request->getVar('division');
                }
                
                if($this->request->getVar('section') != ""){
                    $validatedData['sect'] = $this->request->getVar('section');
                }

                $encoded_data = base64_encode(serialize($validatedData));

                $redirect_url = base_url('generate-bulk/') . $encoded_data;

                $data = [
                    'status' => 'success',
                    'redirect_url' => $redirect_url,
                    'message' => "Success",
                ];
                // Return the redirect URL as JSON response
                return $this->response->setJSON($data);
                
            } else {
                // Validation failed
                $data['status'] = 'error';
                $data['errors'] = $validation->getErrors();

                // Return the validation errors as JSON response
                return $this->response->setJSON($data);
            }

        } else {
            // Return the error response
            $data['error'] = true;
            $data['message'] = 'Invalid request';
        }


            // Set the CSRF token for the response
            $data['csrfToken'] = csrf_hash();
            return $this->response->setJSON($data);

    }

    public function bulkprint_pdf($data){

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }

        $info = [];

        try {

            $decoded  = base64_decode($data);
            $decoded_data = unserialize($decoded);

            if (!empty($decoded_data['csrfToken']) && $this->validateCSRFToken($decoded_data['csrfToken'])) {

                // Do something with the data
                $emptype = $decoded_data['emptype'];
                
                if(isset($decoded_data['div'])){
                    $division = $decoded_data['div'];
                }else{
                    $division = "";
                }

                if(isset($decoded_data['sect'])){
                    $section = $decoded_data['sect'];
                }else{
                    $section = "";
                }


                $staff = $this->PDFModel->getEmployee($emptype, $division, $section);
                if($staff){

                
                $pdf = $this->fpdf;
                // Set the maximum Y position before starting a new page
                $maxY = 250; // Change this value as needed
                $pdf->SetAutoPageBreak(0,0);

                $pdf->addFonts();
                
                $pdf->SetTitle('CHD Ilocos ID Bulk Print'); // Set the PDF titled
                // Add your PDF generation logic here
                // For example:
                $pdf->AliasNbPages();
                $ctr = 1;
                
                foreach ($staff as $row) {
                    
                    // Access employee values
                    $employeeID = $row['Employee_ID'];
                    $firstName = $row['FirstName'];
                    $lastName = $row['LastName'];
                    $signature = $row['Signature'];
                    $profilePhoto = $row['ProfilePhoto'];

                    if(is_null($row['Bloodtype']) || $row['Bloodtype'] == ""){
                        $blood = "-";
                    }else{
                        $blood = $row['Bloodtype'];
                    }
                    
                    if($row['Division']=='RLED'){
                        $section = ' ';
                    }else{
                        $section = $row['AreaOfAssignment'];
                    }

                    if ($profilePhoto != "") {

                        $filePath = FCPATH . 'public/images/photos/' . $profilePhoto;
                        
                        if (file_exists($filePath)) 
                        {
                            $imageSrc = ROOTPATH.'public/images/photos/' . $profilePhoto;
                        }else{
                            
                            $imageSrc = ROOTPATH.'public/images/profile.jpg';
                            
                           
                        }
                    } else {
                        $imageSrc = ROOTPATH.'public/images/profile.jpg';
                    }
                        
                    if ($signature != "") {
                        $filePath = FCPATH . 'public/images/signature/' . $signature;
                        
                        if (file_exists($filePath)) 
                        {
                            $signaturePhoto = ROOTPATH.'public/images/signature/' . $signature;
                        }else{
                            $signaturePhoto = ROOTPATH.'public/images/nosign.jpg';
                        }
                    } else {
                        $signaturePhoto = ROOTPATH.'public/images/nosign.png';
                    }
                        
                    
                    $pdf->addFonts();
                    
                    $pdf->SetTitle('CHD Ilocos ID Bulk Print'); // Set the PDF titled
                    $employeeID = $row['Employee_ID'];
                    $firstName = $row['FirstName'];
                    $lastName = $row['LastName'];
                    $signature = $row['Signature'];
                    $profilePhoto = $row['ProfilePhoto'];

                    if(is_null($row['Bloodtype']) || $row['Bloodtype'] == ""){
                        $blood = "-";
                    }else{
                        $blood = $row['Bloodtype'];
                    }
                    
                    if($row['Division']=='RLED'){
                        $section = ' ';
                    }else{
                        $section = $row['AreaOfAssignment'];
                    }

                    if ($profilePhoto != "") {

                        $filePath = FCPATH . 'public/images/photos/' . $profilePhoto;
                        
                        if (file_exists($filePath)) 
                        {
                            $imageSrc = ROOTPATH.'public/images/photos/' . $profilePhoto;
                        }else{
                            $imageSrc = ROOTPATH.'public/images/profile.jpg';
                        }
                    } else {
                        $imageSrc = ROOTPATH.'public/images/profile.jpg';
                    }
                        
                    if ($signature != "") {
                        $filePath = FCPATH . 'public/images/signature/' . $signature;
                        
                        if (file_exists($filePath)) 
                        {
                            $signaturePhoto = ROOTPATH.'public/images/signature/' . $signature;
                        }else{
                            $signaturePhoto = ROOTPATH.'public/images/nosign.jpg';
                        }
                    } else {
                        $signaturePhoto = ROOTPATH.'public/images/nosign.png';
                    }
                        
                    
                    $pdf->addFonts();
                    
                    $pdf->SetTitle('CHD Ilocos ID Bulk Print'); // Set the PDF titled


                    
                    if($row['TypeOfEmployment'] == 'PERMANENT'){
                        if($ctr % 2 == 0){
                            
                            $y = 139.5;
                            $x = 5;
                            // Position at 1.5 cm from bottom
                            //$pdf->Image(base_url().'public/images/a4.jpg',0,0,210);
                            $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',8+$x,8.55+$y,95.2);
                            $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',103.1+$x,8.55+$y,95.2);
                            
            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(133.8+$y);//128.8
                            $pdf->SetX(9.3+$x);//22.8
                            $pdf->SetTextColor(255 , 255, 255);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(134+$y);//129
                            $pdf->SetX(9.5+$x);//23
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 6){
                                // /$section
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8+$y);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                                $pdf->SetY(138+$y);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8+$y);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                                $pdf->SetY(138+$y);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            if(strlen($row['NickName']) > 3){
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5+$y);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
        
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112+$y);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5+$y);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112+$y);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
        
                                
                            }		
                            
                            $pdf->SetTextColor(0 , 0, 0);
                            
                            $name = strtoupper(utf8_decode($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName']))." ".strtoupper($row['Suffix']);
                            if(strlen($name) > 25){
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132+$y);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->CellFitScale(44,4,$name,0,1,'',0);
                                $pdf->Line(50.7+$x, 136+$y, 99+$x, 136+$y);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132+$y);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->Cell(44,4,$name,0,1,'C',0);
                                $pdf->Line(50.7+$x, 136+$y, 99+$x, 136+$y);
                            }
                            
                            if(strlen($row['Position']) > 26){
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136+$y);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->CellFitScale(44,4,strtoupper($row['Position']),0,1,'',0);
                            }else{
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136+$y);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->Cell(44,4,strtoupper($row['Position']),0,1,'C',0);
                            }
                            
                            $pdf->SetFont('Barlow','B', 9);
                            $pdf->SetY(141+$y);//131
                            $pdf->SetX(53+$x);//56
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(44,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            //$pdf->Line(62, 135, 94, 135);
                            
                            $pdf->Image($imageSrc,25.3+$x, 45.7+$y, 50.8, 50.8);
                            //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
                            
                            
                            #-------------------------------------------BACK PAGE-------------------------------------------
                            
                            $x = 100.1;
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(133.8+$y);//128.8
                            $pdf->SetX(9.3+$x);//22.8
                            $pdf->SetTextColor(255 , 255, 255);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(134+$y);//129
                            $pdf->SetX(9.5+$x);//23
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 6){
                                // /$section
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8+$y);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                                $pdf->SetY(138+$y);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8+$y);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                                $pdf->SetY(138+$y);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            if(strlen($row['NickName']) > 3){
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5+$y);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
        
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112+$y);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5+$y);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112+$y);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
        
                                
                            }		
                            
                            $pdf->SetTextColor(0 , 0, 0);
                            
                            $name = strtoupper(utf8_decode($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName']))." ".strtoupper($row['Suffix']);
                            if(strlen($name) > 25){
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132+$y);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->CellFitScale(44,4,$name,0,1,'',0);
                                $pdf->Line(50.7+$x, 136+$y, 99+$x, 136+$y);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132+$y);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->Cell(44,4,$name,0,1,'C',0);
                                $pdf->Line(50.7+$x, 136+$y, 99+$x, 136+$y);
                            }
                            
                            if(strlen($row['Position']) > 26){
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136+$y);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->CellFitScale(44,4,strtoupper($row['Position']),0,1,'',0);
                            }else{
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136+$y);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->Cell(44,4,strtoupper($row['Position']),0,1,'C',0);
                            }
                            
                            $pdf->SetFont('Barlow','B', 9);
                            $pdf->SetY(141+$y);//131
                            $pdf->SetX(53+$x);//56
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(44,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            //$pdf->Line(62, 135, 94, 135);
                            
                            $pdf->Image($imageSrc,25.3+$x, 45.7+$y, 50.8, 50.8);
                            //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");


                            #bottom
                        }else{
                            $pdf->AddPage();

                            $x = 5;
                           // Position at 1.5 cm from bottom
                            //$pdf->Image(base_url().'public/images/a4.jpg',0,0,210);
                            $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',8+$x,8.5,95.2);
                            $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',103.1+$x,8.5,95.2);
                            
            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(133.8);//128.8
                            $pdf->SetX(9.3+$x);//22.8
                            $pdf->SetTextColor(255 , 255, 255);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(134);//129
                            $pdf->SetX(9.5+$x);//23
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 6){
                                // /$section
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                                $pdf->SetY(138);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                                $pdf->SetY(138);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            if(strlen($row['NickName']) > 3){
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
        
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
        
                                
                            }		
                            
                            $pdf->SetTextColor(0 , 0, 0);
                            
                            $name = strtoupper(utf8_decode($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName']))." ".strtoupper($row['Suffix']);
                            if(strlen($name) > 25){
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->CellFitScale(44,4,$name,0,1,'',0);
                                $pdf->Line(50.7+$x, 136, 99+$x, 136);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->Cell(44,4,$name,0,1,'C',0);
                                $pdf->Line(50.7+$x, 136, 99+$x, 136);
                            }
                            
                            if(strlen($row['Position']) > 26){
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->CellFitScale(44,4,strtoupper($row['Position']),0,1,'',0);
                            }else{
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->Cell(44,4,strtoupper($row['Position']),0,1,'C',0);
                            }
                            
                            $pdf->SetFont('Barlow','B', 9);
                            $pdf->SetY(141);//131
                            $pdf->SetX(53+$x);//56
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(44,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            //$pdf->Line(62, 135, 94, 135);
                            
                            $pdf->Image($imageSrc,25.3+$x, 45.7, 50.8, 50.8);
                            //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
                            
                            
                            #-------------------------------------------BACK PAGE-------------------------------------------
                            
                            $x = 100.1;
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(133.8);//128.8
                            $pdf->SetX(9.3+$x);//22.8
                            $pdf->SetTextColor(255 , 255, 255);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(134);//129
                            $pdf->SetX(9.5+$x);//23
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 6){
                                // /$section
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                                $pdf->SetY(138);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                                $pdf->SetY(138);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            if(strlen($row['NickName']) > 3){
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
        
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
        
                                
                            }		
                            
                            $pdf->SetTextColor(0 , 0, 0);
                            
                            $name = strtoupper(utf8_decode($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName']))." ".strtoupper($row['Suffix']);
                            if(strlen($name) > 25){
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->CellFitScale(44,4,$name,0,1,'',0);
                                $pdf->Line(50.7+$x, 136, 99+$x, 136);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->Cell(44,4,$name,0,1,'C',0);
                                $pdf->Line(50.7+$x, 136, 99+$x, 136);
                            }
                            
                            if(strlen($row['Position']) > 26){
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->CellFitScale(44,4,strtoupper($row['Position']),0,1,'',0);
                            }else{
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->Cell(44,4,strtoupper($row['Position']),0,1,'C',0);
                            }
                            
                            $pdf->SetFont('Barlow','B', 9);
                            $pdf->SetY(141);//131
                            $pdf->SetX(53+$x);//56
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(44,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            //$pdf->Line(62, 135, 94, 135);
                            
                            $pdf->Image($imageSrc,25.3+$x, 45.7, 50.8, 50.8);
                            //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
            
                        }

                        //$pdf->Image(base_url().'public/images/a42.png',0,0,210);
                    }elseif($row['TypeOfEmployment'] == 'CONTRACTUAL'){
                        #tin number

                        $tin = $row['TINNumber'];
                        $tin_number = substr($tin, 0, 3);
                        $tin_number .= "-".substr($tin,3, 3);
                        $tin_number .= "-".substr($tin,6, 3);
                        if(strlen($tin) > 9){
                            $tin_number .= "-".substr($tin,9, 50);
                        }
                        
                        #philhealth number
                        $phic = $row['Philhealth'];
                        $phic_number = substr($phic, 0, 2);
                        $phic_number .= "-".substr($phic,2, 9);
                        $phic_number .= "-".substr($phic,11, 1);
                        
                        #SSS number
                        $sss = $row['SSS'];
                        $sss_number = substr($sss, 0, 2);
                        $sss_number .= "-".substr($sss,2, 7);
                        $sss_number .= "-".substr($sss,9, 1);



                        if($ctr % 2 == 0){
                            $y=130;
                            $pdf->Image(ROOTPATH.'public/images/bg_new2.png',22.6,26+$y,82.3);
                            $pdf->Image(ROOTPATH.'public/images/bg_back.png',104.9,26+$y,82.3);
                            
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(77+$y);	
                            $pdf->SetX(78.5);
                            $pdf->SetFillColor(15 , 143, 70);
                            $pdf->SetTextColor(255, 255, 255);
                            $pdf->SetDrawColor(100, 100, 100);
                            $pdf->Cell(20,7,$row['Division'],1,1,'C',1);
                            //$pdf->Line(77, 83, 100, 83);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 8){
                                $pdf->SetFont('Barlow','BXB', 12);
                                $pdf->SetY(84+$y);
                                $pdf->SetX(78.5);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->SetFillColor(255, 207, 53);
                                $pdf->SetDrawColor(100, 100, 100);
                                $pdf->CellFitScaleColor(20,5,$section,1,1,'',1);
                            }else{
                                $pdf->SetFont('Barlow','BXB', 12);
                                $pdf->SetY(84+$y);
                                $pdf->SetX(78.5);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->SetFillColor(255, 207, 53);
                                $pdf->SetDrawColor(100, 100, 100);
                                $pdf->Cell(20,5,$section,1,1,'C',1);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            $pdf->SetFont('Barlow','BL', 25);
                            $pdf->SetY(90+$y);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->CellFitScale(57,10,strtoupper(utf8_decode($row['FirstName'])),0,1,'',1);				
                            
                            $pdf->SetFont('Barlow','BXB', 20);
                            $pdf->SetY(98+$y);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(80, 80, 80);
                            $pdf->CellFitScale(55,10,strtoupper(substr($row['MiddleName'], 0, 1)).". ".strtoupper(utf8_decode($row['LastName']))." ".strtoupper($row['Suffix']),0,1,'',1);
                            
                            $pdf->SetFont('Lora','B', 10);
                            $pdf->SetY(107+$y);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->CellFitScale(50,4,strtoupper($row['Position']),0,1,'',1);
                            $pdf->Line(45.3, 111+$y, 93, 111+$y);
                            
                            $pdf->SetFont('Barlow','BXB', 10);
                            $pdf->SetY(112+$y);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(50,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            
                            $pdf->Image($signaturePhoto,70,120+$y,25,14);
                            $pdf->Image($imageSrc,37.5,53.5+$y,36, 36);
                            
                            $pdf->SetFont('Barlow','BXB', 5);
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->TextWithDirection(25,138+$y,"Effectivity: ",'U');
                            
                            $pdf->SetFont('Barlow','BXB', 5);
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->TextWithDirection(27.5,138+$y,date('F d, Y', strtotime($row['ContractDuration_start']))." - ".date('F d, Y', strtotime($row['ContractDuration_end'])),'U');
                            
                            #-------------------------------------------BACK PAGE-------------------------------------------
                            
                            
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetY(35+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,20,"",1,'L',"");
                            
                            $pdf->SetFont('Barlow','BXB', 9);
                            $pdf->SetY(35+$y);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,4,"ADDRESS:",1,'L',"");
                            
                            $pdf->SetFont('Barlow','SB', 10);
                            $pdf->SetY(40+$y);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,5,strtoupper(utf8_decode($row['Address'])),0,'C',"");
                            
                            #border - DETAILS
                            $pdf->SetY(57+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,33,"",1,"","","");
                            
                            #text inside border box
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58+$y);
                            $pdf->SetX(112);
                            $pdf->Cell(18,5,"BIRTHDATE",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63+$y);
                            $pdf->SetX(112);
                            $pdf->Cell(18,5,date("M. d, Y", strtotime($row['Birthdate'])),"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58+$y);
                            $pdf->SetX(133);
                            $pdf->Cell(22,5,"BLOOD TYPE",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63+$y);
                            $pdf->SetX(139);
                            $pdf->Cell(10,5,$blood,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58+$y);
                            $pdf->SetX(160);
                            $pdf->Cell(18,5,"TIN NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63+$y);
                            $pdf->SetX(159);
                            $pdf->Cell(20,5,$tin_number,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(75+$y);
                            $pdf->SetX(120);
                            $pdf->Cell(18,5,"PHILHEALTH NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(80+$y);
                            $pdf->SetX(118);
                            $pdf->Cell(22,5,$phic_number,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(75+$y);
                            $pdf->SetX(152);
                            $pdf->Cell(18,5,"SSS NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(80+$y);
                            $pdf->SetX(150);
                            $pdf->Cell(22,5,$sss_number,"B","","C","");
                            
                            #border - EMERGENCY
                            $pdf->SetY(92+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,15,"",1,"","","");
                            
                            $pdf->SetFont('Barlow','B', 8);
                            $pdf->SetY(92+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(53.5,5,"Person to notify in case of emergency:",1,"","L","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(98+$y);
                            $pdf->SetX(111);
                            $pdf->Cell(70,5,"Name: ".strtoupper(utf8_decode($row['NameOfPersonToNotify'])),0,"","L","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(102+$y);
                            $pdf->SetX(111);
                            $pdf->Cell(70,5,"Contact No.: ".$row['CPNumber'],0,"","L","");
                            
                            $pdf->SetFont('Barlow','B', 8);
                            $pdf->SetY(108+$y);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,4,"This is to certify the person whose picture and signature appear hereon is an employee of Ilocos CHD, SFLU",0,'C',"");
                            
                            //$pdf->Line(108, 128, 184, 128);
                            
                            $pdf->Rect(108, 128+$y, 76, .5, "F");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(128+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,5,"PAULA PAZ M. SYDIONGCO, MD, MPH, MBA, CESO IV",0,"","C","");
                            
                            $pdf->SetFont('Barlow','B', 7);
                            $pdf->SetY(131+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,5,"DIRECTOR IV",0,"","C","");	 
                        

                        }else{
                            $pdf->AddPage();

                            $pdf->Image(ROOTPATH.'public/images/bg_new2.png',22.6,26,82.3);
                            $pdf->Image(ROOTPATH.'public/images/bg_back.png',104.9,26,82.3);
                            
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(77);	
                            $pdf->SetX(78.5);
                            $pdf->SetFillColor(15 , 143, 70);
                            $pdf->SetTextColor(255, 255, 255);
                            $pdf->SetDrawColor(100, 100, 100);
                            $pdf->Cell(20,7,$row['Division'],1,1,'C',1);
                            //$pdf->Line(77, 83, 100, 83);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 8){
                                $pdf->SetFont('Barlow','BXB', 12);
                                $pdf->SetY(84);
                                $pdf->SetX(78.5);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->SetFillColor(255, 207, 53);
                                $pdf->SetDrawColor(100, 100, 100);
                                $pdf->CellFitScaleColor(20,5,$section,1,1,'',1);
                            }else{
                                $pdf->SetFont('Barlow','BXB', 12);
                                $pdf->SetY(84);
                                $pdf->SetX(78.5);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->SetFillColor(255, 207, 53);
                                $pdf->SetDrawColor(100, 100, 100);
                                $pdf->Cell(20,5,$section,1,1,'C',1);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            $pdf->SetFont('Barlow','BL', 25);
                            $pdf->SetY(90);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->CellFitScale(57,10,strtoupper(utf8_decode($row['FirstName'])),0,1,'',1);				
                            
                            $pdf->SetFont('Barlow','BXB', 20);
                            $pdf->SetY(98);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(80, 80, 80);
                            $pdf->CellFitScale(55,10,strtoupper(substr($row['MiddleName'], 0, 1)).". ".strtoupper(utf8_decode($row['LastName']))." ".strtoupper($row['Suffix']),0,1,'',1);
                            
                            $pdf->SetFont('Lora','B', 10);
                            $pdf->SetY(107);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->CellFitScale(50,4,strtoupper($row['Position']),0,1,'',1);
                            $pdf->Line(45.3, 111, 93, 111);
                            
                            $pdf->SetFont('Barlow','BXB', 10);
                            $pdf->SetY(112);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(50,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            
                            $pdf->Image($signaturePhoto,70,120,25,14);
                            $pdf->Image($imageSrc,37.5,53.5,36, 36);
                            
                            $pdf->SetFont('Barlow','BXB', 5);
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->TextWithDirection(25,138,"Effectivity: ",'U');
                            
                            $pdf->SetFont('Barlow','BXB', 5);
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->TextWithDirection(27.5,138,date('F d, Y', strtotime($row['ContractDuration_start']))." - ".date('F d, Y', strtotime($row['ContractDuration_end'])),'U');
                            
                            #-------------------------------------------BACK PAGE-------------------------------------------
                            
                            
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetY(35);
                            $pdf->SetX(108);
                            $pdf->Cell(76,20,"",1,'L',"");
                            
                            $pdf->SetFont('Barlow','BXB', 9);
                            $pdf->SetY(35);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,4,"ADDRESS:",1,'L',"");
                            
                            $pdf->SetFont('Barlow','SB', 10);
                            $pdf->SetY(40);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,5,strtoupper(utf8_decode($row['Address'])),0,'C',"");
                            
                            #border - DETAILS
                            $pdf->SetY(57);
                            $pdf->SetX(108);
                            $pdf->Cell(76,33,"",1,"","","");
                            
                            #text inside border box
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58);
                            $pdf->SetX(112);
                            $pdf->Cell(18,5,"BIRTHDATE",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63);
                            $pdf->SetX(112);
                            $pdf->Cell(18,5,date("M. d, Y", strtotime($row['Birthdate'])),"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58);
                            $pdf->SetX(133);
                            $pdf->Cell(22,5,"BLOOD TYPE",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63);
                            $pdf->SetX(139);
                            $pdf->Cell(10,5,$blood,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58);
                            $pdf->SetX(160);
                            $pdf->Cell(18,5,"TIN NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63);
                            $pdf->SetX(159);
                            $pdf->Cell(20,5,$tin_number,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(75);
                            $pdf->SetX(120);
                            $pdf->Cell(18,5,"PHILHEALTH NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(80);
                            $pdf->SetX(118);
                            $pdf->Cell(22,5,$phic_number,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(75);
                            $pdf->SetX(152);
                            $pdf->Cell(18,5,"SSS NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(80);
                            $pdf->SetX(150);
                            $pdf->Cell(22,5,$sss_number,"B","","C","");
                            
                            #border - EMERGENCY
                            $pdf->SetY(92);
                            $pdf->SetX(108);
                            $pdf->Cell(76,15,"",1,"","","");
                            
                            $pdf->SetFont('Barlow','B', 8);
                            $pdf->SetY(92);
                            $pdf->SetX(108);
                            $pdf->Cell(53.5,5,"Person to notify in case of emergency:",1,"","L","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(98);
                            $pdf->SetX(111);
                            $pdf->Cell(70,5,"Name: ".strtoupper(utf8_decode($row['NameOfPersonToNotify'])),0,"","L","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(102);
                            $pdf->SetX(111);
                            $pdf->Cell(70,5,"Contact No.: ".$row['CPNumber'],0,"","L","");
                            
                            $pdf->SetFont('Barlow','B', 8);
                            $pdf->SetY(108);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,4,"This is to certify the person whose picture and signature appear hereon is an employee of Ilocos CHD, SFLU",0,'C',"");
                            
                            //$pdf->Line(108, 128, 184, 128);
                            
                            $pdf->Rect(108, 128, 76, .5, "F");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(128);
                            $pdf->SetX(108);
                            $pdf->Cell(76,5,"PAULA PAZ M. SYDIONGCO, MD, MPH, MBA, CESO IV",0,"","C","");
                            
                            $pdf->SetFont('Barlow','B', 7);
                            $pdf->SetY(131);
                            $pdf->SetX(108);
                            $pdf->Cell(76,5,"DIRECTOR IV",0,"","C","");	

                        }
                        

                    }
                    
                    
                    $ctr++;
                    
                }
                
                //$pdf->AddPage();
                //$pdf->SetFont('Lora', 'B', 16);
                //$pdf->Cell(40, 10, $emptype.$division.$section);
                //$pdf->Ln(10);

                // Output the PDF as a response to the client
                $pdf->Output();


                exit();

                }else{
                    echo "<h2>Error, please contact System Administrator.</h2>";
                }

            }else{
                // CSRF token is empty or invalid, capture and log the error
                $errorMessage = 'CSRF token validation failed or token is empty.';
                log_message('error', $errorMessage);

                // You can handle the error according to your application's requirements
                // For example, you may want to display an error message to the user.
                // In this case, you can pass the error message to the error view:
                $info['error_message'] = $errorMessage;

                return view('error_view', $info); // Load the error view
            }

        } catch (\Exception $e) {
            // Catch any exception thrown during execution
            // and display the error in the error view

            $info['error_message'] = $e->getMessage(); // Get the error message
            log_message('error', $e->getMessage());

            return view('pdf-error', $info); // Load the error view
        }



    }







    #BULK PER EMPLOYEE -----------------------------------------------------------------------------------

    public function checkbulkemp(){

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }

        $data = [];
        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');
        $validation = \Config\Services::validation();


        if ($this->request->getMethod() === 'post' && !empty($csrfToken) && $this->validateCSRFToken($csrfToken)) {
            
            $rules = [
                'employee' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please select Employee!',
                    ],
                ],
            ];

            
            if ($this->validate($rules)) {
                $validatedData = [];
                // Validation success
                // Generate and set the CSRF token for the response
                $validatedData = [
                    'emp' => $this->request->getVar('employee'),
                    'csrfToken' => csrf_hash(), // Regenerate the CSRF token here
                ];


                $encoded_data = base64_encode(serialize($validatedData));

                $redirect_url = base_url('generate-bulk-emp/') . $encoded_data;

                $data = [
                    'status' => 'success',
                    'redirect_url' => $redirect_url,
                    'message' => "Success",
                ];
                // Return the redirect URL as JSON response
                return $this->response->setJSON($data);
                
            } else {
                // Validation failed
                $data['status'] = 'error';
                $data['errors'] = $validation->getErrors();

                // Return the validation errors as JSON response
                return $this->response->setJSON($data);
            }

        } else {
            // Return the error response
            $data['error'] = true;
            $data['message'] = 'Invalid request';
        }


            // Set the CSRF token for the response
            $data['csrfToken'] = csrf_hash();
            return $this->response->setJSON($data);

    }


    public function bulkprint_pdf_emp($data){

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }


        $info = [];

        try {

            $decoded  = base64_decode($data);
            $decoded_data = unserialize($decoded);

            if (!empty($decoded_data['csrfToken']) && $this->validateCSRFToken($decoded_data['csrfToken'])) {

                // Do something with the data
                $empArray = $decoded_data['emp'];
                
                //$empArray = implode(',', $emp);

                $staff = $this->PDFModel->getEmployeeBulk($empArray);



                if($staff){



                
                $pdf = $this->fpdf;
                // Set the maximum Y position before starting a new page
                $maxY = 250; // Change this value as needed
                $pdf->SetAutoPageBreak(0,0);

                $pdf->addFonts();
                
                $pdf->SetTitle('CHD Ilocos ID Bulk Print'); // Set the PDF titled
                // Add your PDF generation logic here
                // For example:
                $pdf->AliasNbPages();
                $ctr = 1;
                
                foreach ($staff as $row) {
                    
                    // Access employee values
                    $employeeID = $row['Employee_ID'];
                    $firstName = $row['FirstName'];
                    $lastName = $row['LastName'];
                    $signature = $row['Signature'];
                    $profilePhoto = $row['ProfilePhoto'];

                    if(is_null($row['Bloodtype']) || $row['Bloodtype'] == ""){
                        $blood = "-";
                    }else{
                        $blood = $row['Bloodtype'];
                    }
                    
                    if($row['Division']=='RLED'){
                        $section = ' ';
                    }else{
                        $section = $row['AreaOfAssignment'];
                    }

                    if ($profilePhoto != "") {

                        $filePath = FCPATH . 'public/images/photos/' . $profilePhoto;
                        
                        if (file_exists($filePath)) 
                        {
                            $imageSrc = ROOTPATH.'public/images/photos/' . $profilePhoto;
                        }else{
                            $imageSrc = ROOTPATH.'public/images/profile.jpg';
                        }
                    } else {
                        $imageSrc = ROOTPATH.'public/images/profile.jpg';
                    }
                        
                    if ($signature != "") {
                        $filePath = FCPATH . 'public/images/signature/' . $signature;
                        
                        if (file_exists($filePath)) 
                        {
                            $signaturePhoto = ROOTPATH.'public/images/signature/' . $signature;
                        }else{
                            $signaturePhoto = ROOTPATH.'public/images/nosign.jpg';
                        }
                    } else {
                        $signaturePhoto = ROOTPATH.'public/images/nosign.png';
                    }
                        
                    
                    $pdf->addFonts();
                    
                    $pdf->SetTitle('CHD Ilocos ID Bulk Print'); // Set the PDF titled
                    $employeeID = $row['Employee_ID'];
                    $firstName = $row['FirstName'];
                    $lastName = $row['LastName'];
                    $signature = $row['Signature'];
                    $profilePhoto = $row['ProfilePhoto'];

                    if(is_null($row['Bloodtype']) || $row['Bloodtype'] == ""){
                        $blood = "-";
                    }else{
                        $blood = $row['Bloodtype'];
                    }
                    
                    if($row['Division']=='RLED'){
                        $section = ' ';
                    }else{
                        $section = $row['AreaOfAssignment'];
                    }

                    if ($profilePhoto != "") {

                        $filePath = FCPATH . 'public/images/photos/' . $profilePhoto;
                        
                        if (file_exists($filePath)) 
                        {
                            $imageSrc = ROOTPATH.'public/images/photos/' . $profilePhoto;
                        }else{
                            $imageSrc = ROOTPATH.'public/images/profile.jpg';
                        }
                    } else {
                        $imageSrc = ROOTPATH.'public/images/profile.jpg';
                    }
                        
                    if ($signature != "") {
                        $filePath = FCPATH . 'public/images/signature/' . $signature;
                        
                        if (file_exists($filePath)) 
                        {
                            $signaturePhoto = ROOTPATH.'public/images/signature/' . $signature;
                        }else{
                            $signaturePhoto = ROOTPATH.'public/images/nosign.jpg';
                        }
                    } else {
                        $signaturePhoto = ROOTPATH.'public/images/nosign.png';
                    }
                        
                    
                    $pdf->addFonts();
                    
                    $pdf->SetTitle('CHD Ilocos ID Bulk Print'); // Set the PDF titled


                    
                    if($row['TypeOfEmployment'] == 'PERMANENT'){
                        if($ctr % 2 == 0){
                            
                            $y = 139.5;
                            $x = 5;
                            // Position at 1.5 cm from bottom
                            //$pdf->Image(base_url().'public/images/a4.jpg',0,0,210);
                            $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',8+$x,8.55+$y,95.2);
                            $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',103.1+$x,8.55+$y,95.2);
                            
            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(133.8+$y);//128.8
                            $pdf->SetX(9.3+$x);//22.8
                            $pdf->SetTextColor(255 , 255, 255);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(134+$y);//129
                            $pdf->SetX(9.5+$x);//23
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 6){
                                // /$section
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8+$y);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                                $pdf->SetY(138+$y);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8+$y);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                                $pdf->SetY(138+$y);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            if(strlen($row['NickName']) > 3){
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5+$y);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
        
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112+$y);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5+$y);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112+$y);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
        
                                
                            }		
                            
                            $pdf->SetTextColor(0 , 0, 0);
                            
                            $name = strtoupper(utf8_decode($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName']))." ".strtoupper($row['Suffix']);
                            if(strlen($name) > 25){
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132+$y);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->CellFitScale(44,4,$name,0,1,'',0);
                                $pdf->Line(50.7+$x, 136+$y, 99+$x, 136+$y);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132+$y);//121
                                $pdf->SetX(53);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->Cell(44,4,$name,0,1,'C',0);
                                $pdf->Line(50.7+$x, 136+$y, 99+$x, 136+$y);
                            }
                            
                            if(strlen($row['Position']) > 26){
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136+$y);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->CellFitScale(44,4,strtoupper($row['Position']),0,1,'',0);
                            }else{
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136+$y);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->Cell(44,4,strtoupper($row['Position']),0,1,'C',0);
                            }
                            
                            $pdf->SetFont('Barlow','B', 9);
                            $pdf->SetY(141+$y);//131
                            $pdf->SetX(53+$x);//56
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(44,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            //$pdf->Line(62, 135, 94, 135);
                            
                            $pdf->Image($imageSrc,25.3+$x, 45.7+$y, 50.8, 50.8);
                            //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
                            
                            
                            #-------------------------------------------BACK PAGE-------------------------------------------
                            
                            $x = 100.1;
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(133.8+$y);//128.8
                            $pdf->SetX(9.3+$x);//22.8
                            $pdf->SetTextColor(255 , 255, 255);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(134+$y);//129
                            $pdf->SetX(9.5+$x);//23
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 6){
                                // /$section
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8+$y);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                                $pdf->SetY(138+$y);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8+$y);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                                $pdf->SetY(138+$y);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            if(strlen($row['NickName']) > 3){
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5+$y);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
        
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112+$y);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5+$y);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112+$y);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
        
                                
                            }		
                            
                            $pdf->SetTextColor(0 , 0, 0);
                            
                            $name = strtoupper(utf8_decode($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName']))." ".strtoupper($row['Suffix']);
                            if(strlen($name) > 25){
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132+$y);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->CellFitScale(44,4,$name,0,1,'',0);
                                $pdf->Line(50.7+$x, 136+$y, 99+$x, 136+$y);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132+$y);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->Cell(44,4,$name,0,1,'C',0);
                                $pdf->Line(50.7+$x, 136+$y, 99+$x, 136+$y);
                            }
                            
                            if(strlen($row['Position']) > 26){
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136+$y);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->CellFitScale(44,4,strtoupper($row['Position']),0,1,'',0);
                            }else{
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136+$y);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->Cell(44,4,strtoupper($row['Position']),0,1,'C',0);
                            }
                            
                            $pdf->SetFont('Barlow','B', 9);
                            $pdf->SetY(141+$y);//131
                            $pdf->SetX(53+$x);//56
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(44,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            //$pdf->Line(62, 135, 94, 135);
                            
                            $pdf->Image($imageSrc,25.3+$x, 45.7+$y, 50.8, 50.8);
                            //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
                        }else{
                            $pdf->AddPage();

                            $x = 5;
                           // Position at 1.5 cm from bottom
                            //$pdf->Image(base_url().'public/images/a4.jpg',0,0,210);
                            $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',8+$x,8.5,95.2);
                            $pdf->Image(ROOTPATH.'public/images/bg_regular_big_final.jpg',103.1+$x,8.5,95.2);
                            
            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(133.8);//128.8
                            $pdf->SetX(9.3+$x);//22.8
                            $pdf->SetTextColor(255 , 255, 255);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(134);//129
                            $pdf->SetX(9.5+$x);//23
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 6){
                                // /$section
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                                $pdf->SetY(138);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                                $pdf->SetY(138);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            if(strlen($row['NickName']) > 3){
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
        
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
        
                                
                            }		
                            
                            $pdf->SetTextColor(0 , 0, 0);
                            
                            $name = strtoupper(utf8_decode($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName']))." ".strtoupper($row['Suffix']);
                            if(strlen($name) > 25){
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->CellFitScale(44,4,$name,0,1,'',0);
                                $pdf->Line(50.7+$x, 136, 99+$x, 136);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->Cell(44,4,$name,0,1,'C',0);
                                $pdf->Line(50.7+$x, 136, 99+$x, 136);
                            }
                            
                            if(strlen($row['Position']) > 26){
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->CellFitScale(44,4,strtoupper($row['Position']),0,1,'',0);
                            }else{
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->Cell(44,4,strtoupper($row['Position']),0,1,'C',0);
                            }
                            
                            $pdf->SetFont('Barlow','B', 9);
                            $pdf->SetY(141);//131
                            $pdf->SetX(53+$x);//56
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(44,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            //$pdf->Line(62, 135, 94, 135);
                            
                            $pdf->Image($imageSrc,25.3+$x, 45.7, 50.8, 50.8);
                            //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
                            
                            
                            #-------------------------------------------BACK PAGE-------------------------------------------
                            
                            $x = 100.1;
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(133.8);//128.8
                            $pdf->SetX(9.3+$x);//22.8
                            $pdf->SetTextColor(255 , 255, 255);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(134);//129
                            $pdf->SetX(9.5+$x);//23
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->Cell(17,5,$row['Division'],0,1,'C',0);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 6){
                                // /$section
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                                $pdf->SetY(138);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(17,5,$section,0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','BXB', 13);
                                $pdf->SetY(137.8);	
                                $pdf->SetX(9.3+$x);//22.8
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                                $pdf->SetY(138);//133	
                                $pdf->SetX(9.5+$x);//23
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(17,5,$section,0,1,'C',0);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            if(strlen($row['NickName']) > 3){
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
        
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->CellFitScale(50,25,strtoupper($row['NickName']),0,1,'',0);
                                
                            }else{
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(111.5);
                                $pdf->SetX(41.5+$x);
                                $pdf->SetTextColor(0, 0, 0);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
                                
                                $pdf->SetFont('KidsMagazine','', 47);
                                $pdf->SetY(112);
                                $pdf->SetX(42+$x);
                                $pdf->SetTextColor(40, 92, 67);
                                $pdf->Cell(50,25,strtoupper($row['NickName']),0,1,'C',0);
        
                                
                            }		
                            
                            $pdf->SetTextColor(0 , 0, 0);
                            
                            $name = strtoupper(utf8_decode($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName']))." ".strtoupper($row['Suffix']);
                            if(strlen($name) > 25){
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->CellFitScale(44,4,$name,0,1,'',0);
                                $pdf->Line(50.7+$x, 136, 99+$x, 136);
                                
                            }else{
                                
                                $pdf->SetFont('Barlow','B', 9);
                                $pdf->SetY(132);//121
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(0 , 0, 0);
                                $pdf->Cell(44,4,$name,0,1,'C',0);
                                $pdf->Line(50.7+$x, 136, 99+$x, 136);
                            }
                            
                            if(strlen($row['Position']) > 26){
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->CellFitScale(44,4,strtoupper($row['Position']),0,1,'',0);
                            }else{
                                $pdf->SetFont('Barlow','SB', 9);
                                $pdf->SetY(136);//125
                                $pdf->SetX(53+$x);//56
                                $pdf->SetTextColor(100 , 100, 100);
                                $pdf->Cell(44,4,strtoupper($row['Position']),0,1,'C',0);
                            }
                            
                            $pdf->SetFont('Barlow','B', 9);
                            $pdf->SetY(141);//131
                            $pdf->SetX(53+$x);//56
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(44,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            //$pdf->Line(62, 135, 94, 135);
                            
                            $pdf->Image($imageSrc,25.3+$x, 45.7, 50.8, 50.8);
                            //$pdf->Rect(42.5, 53.5, 30.8, 30.8, "F");
            
            
                        }
                        //$pdf->Image(base_url().'public/images/a42.png',0,0,210);
                    }elseif($row['TypeOfEmployment'] == 'CONTRACTUAL'){
                        #tin number

                        $tin = $row['TINNumber'];
                        $tin_number = substr($tin, 0, 3);
                        $tin_number .= "-".substr($tin,3, 3);
                        $tin_number .= "-".substr($tin,6, 3);
                        if(strlen($tin) > 9){
                            $tin_number .= "-".substr($tin,9, 50);
                        }
                        
                        #philhealth number
                        $phic = $row['Philhealth'];
                        $phic_number = substr($phic, 0, 2);
                        $phic_number .= "-".substr($phic,2, 9);
                        $phic_number .= "-".substr($phic,11, 1);
                        
                        #SSS number
                        $sss = $row['SSS'];
                        $sss_number = substr($sss, 0, 2);
                        $sss_number .= "-".substr($sss,2, 7);
                        $sss_number .= "-".substr($sss,9, 1);



                        if($ctr % 2 == 0){
                            $y=130;
                            $pdf->Image(ROOTPATH.'public/images/bg_new2.png',22.6,26+$y,82.3);
                            $pdf->Image(ROOTPATH.'public/images/bg_back.png',104.9,26+$y,82.3);
                            
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(77+$y);	
                            $pdf->SetX(78.5);
                            $pdf->SetFillColor(15 , 143, 70);
                            $pdf->SetTextColor(255, 255, 255);
                            $pdf->SetDrawColor(100, 100, 100);
                            $pdf->Cell(20,7,$row['Division'],1,1,'C',1);
                            //$pdf->Line(77, 83, 100, 83);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 8){
                                $pdf->SetFont('Barlow','BXB', 12);
                                $pdf->SetY(84+$y);
                                $pdf->SetX(78.5);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->SetFillColor(255, 207, 53);
                                $pdf->SetDrawColor(100, 100, 100);
                                $pdf->CellFitScaleColor(20,5,$section,1,1,'',1);
                            }else{
                                $pdf->SetFont('Barlow','BXB', 12);
                                $pdf->SetY(84+$y);
                                $pdf->SetX(78.5);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->SetFillColor(255, 207, 53);
                                $pdf->SetDrawColor(100, 100, 100);
                                $pdf->Cell(20,5,$section,1,1,'C',1);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            $pdf->SetFont('Barlow','BL', 25);
                            $pdf->SetY(90+$y);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->CellFitScale(57,10,strtoupper(utf8_decode($row['FirstName'])),0,1,'',1);				
                            
                            $pdf->SetFont('Barlow','BXB', 20);
                            $pdf->SetY(98+$y);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(80, 80, 80);
                            $pdf->CellFitScale(55,10,strtoupper(substr($row['MiddleName'], 0, 1)).". ".strtoupper(utf8_decode($row['LastName']))." ".strtoupper($row['Suffix']),0,1,'',1);
                            
                            $pdf->SetFont('Lora','B', 10);
                            $pdf->SetY(107+$y);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->CellFitScale(50,4,strtoupper($row['Position']),0,1,'',1);
                            $pdf->Line(45.3, 111+$y, 93, 111+$y);
                            
                            $pdf->SetFont('Barlow','BXB', 10);
                            $pdf->SetY(112+$y);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(50,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            
                            $pdf->Image($signaturePhoto,70,120+$y,25,14);
                            $pdf->Image($imageSrc,37.5,53.5+$y,36, 36);
                            
                            $pdf->SetFont('Barlow','BXB', 5);
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->TextWithDirection(25,138+$y,"Effectivity: ",'U');
                            
                            $pdf->SetFont('Barlow','BXB', 5);
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->TextWithDirection(27.5,138+$y,date('F d, Y', strtotime($row['ContractDuration_start']))." - ".date('F d, Y', strtotime($row['ContractDuration_end'])),'U');
                            
                            #-------------------------------------------BACK PAGE-------------------------------------------
                            
                            
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetY(35+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,20,"",1,'L',"");
                            
                            $pdf->SetFont('Barlow','BXB', 9);
                            $pdf->SetY(35+$y);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,4,"ADDRESS:",1,'L',"");
                            
                            $pdf->SetFont('Barlow','SB', 10);
                            $pdf->SetY(40+$y);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,5,strtoupper(utf8_decode($row['Address'])),0,'C',"");
                            
                            #border - DETAILS
                            $pdf->SetY(57+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,33,"",1,"","","");
                            
                            #text inside border box
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58+$y);
                            $pdf->SetX(112);
                            $pdf->Cell(18,5,"BIRTHDATE",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63+$y);
                            $pdf->SetX(112);
                            $pdf->Cell(18,5,date("M. d, Y", strtotime($row['Birthdate'])),"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58+$y);
                            $pdf->SetX(133);
                            $pdf->Cell(22,5,"BLOOD TYPE",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63+$y);
                            $pdf->SetX(139);
                            $pdf->Cell(10,5,$blood,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58+$y);
                            $pdf->SetX(160);
                            $pdf->Cell(18,5,"TIN NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63+$y);
                            $pdf->SetX(159);
                            $pdf->Cell(20,5,$tin_number,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(75+$y);
                            $pdf->SetX(120);
                            $pdf->Cell(18,5,"PHILHEALTH NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(80+$y);
                            $pdf->SetX(118);
                            $pdf->Cell(22,5,$phic_number,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(75+$y);
                            $pdf->SetX(152);
                            $pdf->Cell(18,5,"SSS NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(80+$y);
                            $pdf->SetX(150);
                            $pdf->Cell(22,5,$sss_number,"B","","C","");
                            
                            #border - EMERGENCY
                            $pdf->SetY(92+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,15,"",1,"","","");
                            
                            $pdf->SetFont('Barlow','B', 8);
                            $pdf->SetY(92+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(53.5,5,"Person to notify in case of emergency:",1,"","L","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(98+$y);
                            $pdf->SetX(111);
                            $pdf->Cell(70,5,"Name: ".strtoupper(utf8_decode($row['NameOfPersonToNotify'])),0,"","L","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(102+$y);
                            $pdf->SetX(111);
                            $pdf->Cell(70,5,"Contact No.: ".$row['CPNumber'],0,"","L","");
                            
                            $pdf->SetFont('Barlow','B', 8);
                            $pdf->SetY(108+$y);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,4,"This is to certify the person whose picture and signature appear hereon is an employee of Ilocos CHD, SFLU",0,'C',"");
                            
                            //$pdf->Line(108, 128, 184, 128);
                            
                            $pdf->Rect(108, 128+$y, 76, .5, "F");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(128+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,5,"PAULA PAZ M. SYDIONGCO, MD, MPH, MBA, CESO IV",0,"","C","");
                            
                            $pdf->SetFont('Barlow','B', 7);
                            $pdf->SetY(131+$y);
                            $pdf->SetX(108);
                            $pdf->Cell(76,5,"DIRECTOR IV",0,"","C","");	 
                        

                        }else{
                            $pdf->AddPage();

                            $pdf->Image(ROOTPATH.'public/images/bg_new2.png',22.6,26,82.3);
                            $pdf->Image(ROOTPATH.'public/images/bg_back.png',104.9,26,82.3);
                            
                            
                            $pdf->SetFont('Barlow','BXB', 14);
                            $pdf->SetY(77);	
                            $pdf->SetX(78.5);
                            $pdf->SetFillColor(15 , 143, 70);
                            $pdf->SetTextColor(255, 255, 255);
                            $pdf->SetDrawColor(100, 100, 100);
                            $pdf->Cell(20,7,$row['Division'],1,1,'C',1);
                            //$pdf->Line(77, 83, 100, 83);
                            
                            $sect_len = strlen($section);
                            if($sect_len > 8){
                                $pdf->SetFont('Barlow','BXB', 12);
                                $pdf->SetY(84);
                                $pdf->SetX(78.5);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->SetFillColor(255, 207, 53);
                                $pdf->SetDrawColor(100, 100, 100);
                                $pdf->CellFitScaleColor(20,5,$section,1,1,'',1);
                            }else{
                                $pdf->SetFont('Barlow','BXB', 12);
                                $pdf->SetY(84);
                                $pdf->SetX(78.5);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->SetFillColor(255, 207, 53);
                                $pdf->SetDrawColor(100, 100, 100);
                                $pdf->Cell(20,5,$section,1,1,'C',1);
                                
                            }
                            
                            $pdf->SetDrawColor(0, 0, 0);
                            $pdf->SetFillColor(0, 0, 0);
                            
                            $pdf->SetFont('Barlow','BL', 25);
                            $pdf->SetY(90);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->CellFitScale(57,10,strtoupper(utf8_decode($row['FirstName'])),0,1,'',1);				
                            
                            $pdf->SetFont('Barlow','BXB', 20);
                            $pdf->SetY(98);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(80, 80, 80);
                            $pdf->CellFitScale(55,10,strtoupper(substr($row['MiddleName'], 0, 1)).". ".strtoupper(utf8_decode($row['LastName']))." ".strtoupper($row['Suffix']),0,1,'',1);
                            
                            $pdf->SetFont('Lora','B', 10);
                            $pdf->SetY(107);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->CellFitScale(50,4,strtoupper($row['Position']),0,1,'',1);
                            $pdf->Line(45.3, 111, 93, 111);
                            
                            $pdf->SetFont('Barlow','BXB', 10);
                            $pdf->SetY(112);
                            $pdf->SetX(44);
                            $pdf->SetTextColor(0 , 0, 0);
                            $pdf->Cell(50,4,"ID No.: ".$row['Employee_ID'],0,"","C","");
                            
                            $pdf->Image($signaturePhoto,70,120,25,14);
                            $pdf->Image($imageSrc,37.5,53.5,36, 36);
                            
                            $pdf->SetFont('Barlow','BXB', 5);
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->TextWithDirection(25,138,"Effectivity: ",'U');
                            
                            $pdf->SetFont('Barlow','BXB', 5);
                            $pdf->SetTextColor(15 , 143, 70);
                            $pdf->TextWithDirection(27.5,138,date('F d, Y', strtotime($row['ContractDuration_start']))." - ".date('F d, Y', strtotime($row['ContractDuration_end'])),'U');
                            
                            #-------------------------------------------BACK PAGE-------------------------------------------
                            
                            
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetY(35);
                            $pdf->SetX(108);
                            $pdf->Cell(76,20,"",1,'L',"");
                            
                            $pdf->SetFont('Barlow','BXB', 9);
                            $pdf->SetY(35);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,4,"ADDRESS:",1,'L',"");
                            
                            $pdf->SetFont('Barlow','SB', 10);
                            $pdf->SetY(40);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,5,strtoupper(utf8_decode($row['Address'])),0,'C',"");
                            
                            #border - DETAILS
                            $pdf->SetY(57);
                            $pdf->SetX(108);
                            $pdf->Cell(76,33,"",1,"","","");
                            
                            #text inside border box
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58);
                            $pdf->SetX(112);
                            $pdf->Cell(18,5,"BIRTHDATE",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63);
                            $pdf->SetX(112);
                            $pdf->Cell(18,5,date("M. d, Y", strtotime($row['Birthdate'])),"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58);
                            $pdf->SetX(133);
                            $pdf->Cell(22,5,"BLOOD TYPE",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63);
                            $pdf->SetX(139);
                            $pdf->Cell(10,5,$blood,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(58);
                            $pdf->SetX(160);
                            $pdf->Cell(18,5,"TIN NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(63);
                            $pdf->SetX(159);
                            $pdf->Cell(20,5,$tin_number,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(75);
                            $pdf->SetX(120);
                            $pdf->Cell(18,5,"PHILHEALTH NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(80);
                            $pdf->SetX(118);
                            $pdf->Cell(22,5,$phic_number,"B","","C","");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(75);
                            $pdf->SetX(152);
                            $pdf->Cell(18,5,"SSS NO.",0,"","C","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(80);
                            $pdf->SetX(150);
                            $pdf->Cell(22,5,$sss_number,"B","","C","");
                            
                            #border - EMERGENCY
                            $pdf->SetY(92);
                            $pdf->SetX(108);
                            $pdf->Cell(76,15,"",1,"","","");
                            
                            $pdf->SetFont('Barlow','B', 8);
                            $pdf->SetY(92);
                            $pdf->SetX(108);
                            $pdf->Cell(53.5,5,"Person to notify in case of emergency:",1,"","L","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(98);
                            $pdf->SetX(111);
                            $pdf->Cell(70,5,"Name: ".strtoupper(utf8_decode($row['NameOfPersonToNotify'])),0,"","L","");
                            
                            $pdf->SetFont('Barlow','SB', 8);
                            $pdf->SetY(102);
                            $pdf->SetX(111);
                            $pdf->Cell(70,5,"Contact No.: ".$row['CPNumber'],0,"","L","");
                            
                            $pdf->SetFont('Barlow','B', 8);
                            $pdf->SetY(108);
                            $pdf->SetX(108);
                            $pdf->MultiCell(76,4,"This is to certify the person whose picture and signature appear hereon is an employee of Ilocos CHD, SFLU",0,'C',"");
                            
                            //$pdf->Line(108, 128, 184, 128);
                            
                            $pdf->Rect(108, 128, 76, .5, "F");
                            
                            $pdf->SetFont('Barlow','BXB', 8);
                            $pdf->SetY(128);
                            $pdf->SetX(108);
                            $pdf->Cell(76,5,"PAULA PAZ M. SYDIONGCO, MD, MPH, MBA, CESO IV",0,"","C","");
                            
                            $pdf->SetFont('Barlow','B', 7);
                            $pdf->SetY(131);
                            $pdf->SetX(108);
                            $pdf->Cell(76,5,"DIRECTOR IV",0,"","C","");	

                        }
                        

                    }
                    
                    
                    $ctr++;
                    
                }
                
                //$pdf->AddPage();
                //$pdf->SetFont('Lora', 'B', 16);
                //$pdf->Cell(40, 10, $emptype.$division.$section);
                //$pdf->Ln(10);

                // Output the PDF as a response to the client
                $pdf->Output();


                exit();

                }else{
                    echo "<h2>Error, please contact System Administrator.</h2>";
                }

            }else{
                // CSRF token is empty or invalid, capture and log the error
                $errorMessage = 'CSRF token validation failed or token is empty.';
                log_message('error', $errorMessage);

                // You can handle the error according to your application's requirements
                // For example, you may want to display an error message to the user.
                // In this case, you can pass the error message to the error view:
                $info['error_message'] = $errorMessage;

                return view('error_view', $info); // Load the error view
            }

        } catch (\Exception $e) {
            // Catch any exception thrown during execution
            // and display the error in the error view

            $info['error_message'] = $e->getMessage(); // Get the error message
            log_message('error', $e->getMessage());

            return view('pdf-error', $info); // Load the error view
        }
    }


    private function validateCSRFToken($token)
    {
        return hash_equals(csrf_hash(), $token);
    }

}


