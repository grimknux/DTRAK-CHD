<?php

namespace App\Controllers;
use App\Models\HomeModel;
use App\Models\ValidationModel;
use App\Models\EmployeeModel;
use App\Libraries\CustomObj;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class Home extends BaseController
{
    public $staffModel;
    public $validationModel;
    public $EmployeeModel;
    public $customobj;


    public $session;

    public function __construct()

    {
        
        $this->staffModel = new HomeModel(); 
        $this->validationModel = new ValidationModel(); 
        $this->EmployeeModel = new EmployeeModel(); 
        $this->customobj = new CustomObj();

        
        $this->session = session();


        helper(['form','html','cookie','array', 'test']);
        
    }


    public function index()
    {

        $client = new \Google_Client();
        $client->setClientId('435241396630-b73qgs0b59heh2so6murgp2sn1cvuebl.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri(base_url('/'));
        $client->addScope('https://www.googleapis.com/auth/drive');

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }

        $data = [
            "page_title" => "PMS Form",
            "page_heading" => "Dashboard",
            "sub_head" => "PMS Form",
            "navactive" => "db",
        ];

        // If access token is not available, initiate OAuth 2.0 authentication
        if (!$this->session->get('access_token')) {
            $data['loginG'] = $client->createAuthUrl();
        }else{
            $data['loginG'] = false;
        }

        if ($this->request->getVar('code')) {
            
            $token = $client->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
            
            if (!isset($token['error'])) {
                $client->setAccessToken($token['access_token']);
                $this->session->set('access_token', $token['access_token']);
                
                header('Location: ' . base_url('/'));
                exit;
            } else {
                return $this->response->setJSON(['status' => 'authtoken', 'message' => $token['error'], 'redirect' => true]);
            }
        }

        return view('homeview', $data);
    }


    public function getemployee()
    {

        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');
        
        if (!empty($csrfToken) && $this->validateCSRFToken($csrfToken)) {
            try {
                
                $staff = $this->staffModel->staff();
                $data = [];
                $cnt = 1;
                foreach ($staff as $row) {
                    $btn = "<a type='button' class='btn btn-success' href='".base_url()."generate-PDF/".$row['ID']."' target='_blank'><i class='fa-solid fa-print'></i></a>
                            <button type='button' class='btn btn-info' onclick='populateEditModal(".$row['ID'].")'><i class='fa-solid fa-pen-to-square'></i></button>
                            <button type='button' class='btn btn-warning' onclick='changePhoto(".$row['ID'].")'><i class='fa-regular fa-image'></i></button>
                            <a type='button' class='btn btn-danger'><i class='fa-solid fa-trash'></i></a>";
                    $chk = "<div class='form-check'>
                    <input class='form-check-input' type='checkbox' name='empselect[]' value='".$row['ID']."' id='flexCheckDefault'></div>";

                    $photo = "";
                    $sign = "";

                    if ($row['ProfilePhoto'] != "") {

                        $filePath = FCPATH . 'public/images/photos/' . $row['ProfilePhoto'];
                        
                        if (file_exists($filePath)) 
                        {
                            $photo = '<img src="'.base_url().'public/images/photos/' . $row['ProfilePhoto'].'" alt="Photo" width="75" height="75">';
                        }else{
                            $photo = "Without Photo";
                        }
                    } else {
                        $photo = "No Record";
                    }
                        
                    if ($row['Signature'] != "" || is_null($row['Signature'])) {
                        
                        if($row['TypeOfEmployment'] == 'PERMANENT'){
                            $sign = "N/A";
                        }else{
                            $filePath = FCPATH . 'public/images/signature/' . $row['Signature'];
                        
                            if (file_exists($filePath)) 
                            {
                                $sign = '<img src="'.base_url().'public/images/signature/' . $row['Signature'].'" alt="Signature" width="85" height="50">';
                            }else{
                                $sign = "Without Signature";
                            }
    
                        }
                       
                    } else {
                        $sign = "No Record";
                    }

                    $data[] = [
                        'chk' => $chk,
                        'cnt' => $row['ID'],
                        'name' => "<strong>".strtoupper($row['FirstName']." ".substr($row['MiddleName'], 0, 1).". ".$row['LastName'])."</strong>",
                        'position' => strtoupper($row['Position']),
                        'division' => strtoupper($row['Division']),
                        'section' => strtoupper($row['AreaOfAssignment']),
                        'emptype' => $row['TypeOfEmployment'],
                        'photo' => $photo ,
                        'signature' => $sign,
                        'btn' => $btn
                    ];

                    $cnt++;
                }

                return $this->response->setJSON($data);
            } catch (\Exception $e) {
                // Log the error using CodeIgniter's logging system
                log_message('error', 'An error occurred in getStaffData(): ' . $e->getMessage());
                return $this->response->setJSON($e->getMessage());
            }
        } else {
            return $this->response->setJSON('CSRF token validation failed');
        }
    }



    public function getdata()
    {
        $id = $this->request->getVar('eid');
        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

        if (!empty($csrfToken) && $this->validateCSRFToken($csrfToken)) {
            try {
                $staffdata = $this->staffModel->getStaffData($id);

                if ($staffdata) {
                    $data = [
                        'id' => $staffdata->ID,
                        'fname' => $staffdata->FirstName,
                        'mname' => $staffdata->MiddleName,
                        'lname' => $staffdata->LastName,
                        'suffix' => $staffdata->Suffix,
                        'sex' => $staffdata->Gender,
                        'position' => $staffdata->Position,
                        'section' => $staffdata->AreaOfAssignment,
                        'division' => $staffdata->Division,
                        'contract_start' => $staffdata->ContractDuration_start,
                        'contract_end' => $staffdata->ContractDuration_end,
                        'address' => $staffdata->Address,
                        'bdate' => $staffdata->Birthdate,
                        'personnotify' => $staffdata->NameOfPersonToNotify,
                        'bloodtype' => $staffdata->Bloodtype,
                        'tin' => $staffdata->TINNumber,
                        'phic' => $staffdata->Philhealth,
                        'sss' => $staffdata->SSS,
                        'pagibig' => $staffdata->PagIbigNumber,
                        'cpnum' => $staffdata->CPNumber,
                        'typeemployment' => $staffdata->TypeOfEmployment,
                        'nname' => $staffdata->NickName,
                        'nameext' => $staffdata->NameExt,
                        'idnum' => $staffdata->Employee_ID,
                        'signature' => $staffdata->Signature,
                        'photo' => $staffdata->ProfilePhoto
                    ];

                    return $this->response->setJSON($data);
                } else {
                    return $this->response->setJSON(['error' => "There's an error in the query"]);
                }
            } catch (\Exception $e) {
                log_message('error', 'An error occurred in getdata(): ' . $e->getMessage());
                return $this->response->setJSON(['error' => $e->getMessage()]);
            }
        } else {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'CSRF token validation failed']);
        }
    }




    public function getsection()
    {
        $div = $this->request->getVar('division');
        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

        if (!empty($csrfToken) && $this->validateCSRFToken($csrfToken)) {
            try {
                $getSection = $this->staffModel->getSection($div);

                if ($getSection) {
                    $data = [];
                    foreach ($getSection as $row) {
                        $data[] = [
                            'value' => $row['sectionID'],
                            'section' => $row['section'],
                        ];
                    }
                    return $this->response->setJSON($data);
                } else {
                    return $this->response->setJSON(['error' => "There's an error in the query"]);
                }
            } catch (\Exception $e) {
                log_message('error', 'An error occurred in getdata(): ' . $e->getMessage());
                return $this->response->setJSON(['error' => $e->getMessage()]);
            }
        } else {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'CSRF token validation failed']);
        }
    }




    public function getdivision()
    {
        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

        if (!empty($csrfToken) && $this->validateCSRFToken($csrfToken)) {
            try {
                $getDivision = $this->staffModel->getDivision();

                if ($getDivision) {
                    $data = [];
                    foreach ($getDivision as $row) {
                        $data[] = [
                            'value' => $row['divcode'],
                            'division' => $row['division']
                        ];
                    }
                    return $this->response->setJSON($data);
                } else {
                    return $this->response->setJSON(['error' => "There's an error in the query"]);
                }
            } catch (\Exception $e) {
                log_message('error', 'An error occurred in getdata(): ' . $e->getMessage());
                return $this->response->setJSON(['error' => $e->getMessage()]);
            }
        } else {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'CSRF token validation failed']);
        }
    }




    public function submitData(){

        $data = [];
        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');
        $validation = \Config\Services::validation();

        

        if ($this->request->getMethod() === 'post' && !empty($csrfToken) && $this->validateCSRFToken($csrfToken)) {
            // Process the form data
            // Retrieve the form input values using $this->request->getPost('input_name')
            // Perform validation, database operations, or any other necessary actions
            $submittedDivision = $this->request->getVar('division');
           
            $divisionValues = $this->validationModel->getDivisionValues();
            $sectionValues = $this->validationModel->getSectionValues($submittedDivision);
            $divVal = [];
            $secVal = [];
            foreach ($divisionValues as $value) {
                $divVal[] = $value['divcode']; // Replace 'your_column' with the actual column name
            }
            foreach ($sectionValues as $value) {
                $secVal[] = $value['sectionID']; // Replace 'your_column' with the actual column name
            }

            $rules = [
                'idnum' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please enter ID Number!',
                    ],
                ],
                'fname' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please enter Firstname!',
                    ],
                ],
                'mname' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please enter Middlename!',
                    ],
                ],
                'lname' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please enter Lastname!',
                    ],
                ],
                'sex' => [
                    'rules' => 'required|in_list[MALE,FEMALE]', 
                    'errors' => [
                        'required' => 'Please select Sex!',
                        'in_list' => 'Please select a valid value!',
                    ],
                ],
                'position' => [
                    'rules' => 'required', 
                    'errors' => [
                        'required' => 'Please enter Position!',
                    ],
                ],
                'division' => [
                    'rules' => 'required|in_list['.implode(',', $divVal).']', 
                    'errors' => [
                        'required' => 'Please select Division!',
                        'in_list' => 'Please select a valid Division',
                    ],
                ],
                'section' => [
                    'rules' =>'required|in_list['.implode(',', $secVal).']',
                    'errors' => [
                        'required' => 'Please select Section!',
                        'in_list' => 'Please select a valid Section',
                    ],
                ],
                'photo' => [
                    'rules' =>'required',
                    'errors' => [
                        'required' => 'Please enter Photo Filename!',
                    ],
                ],
                'signature' => [
                    'rules' =>'required',
                    'errors' => [
                        'required' => 'Please enter Signature Filename!',
                    ],
                ],
            ];


            if($this->validate($rules))
            {
                
                $validatedData = [

                    'ID' => $this->request->getPost('employee_id'),
                    'FirstName' => $this->request->getPost('fname'),
                    'MiddleName' => $this->request->getPost('mname'),
                    'LastName' => $this->request->getPost('lname'),
                    'Suffix' => $this->request->getPost('suffix'),
                    'Gender' => $this->request->getPost('sex'),
                    'Position' => $this->request->getPost('position'),
                    'AreaOfAssignment' => $this->request->getPost('section'),
                    'Division' => $this->request->getPost('division'),
                    'ContractDuration_start' => $this->dateFormat($this->request->getPost('contract_start'),'database'),
                    'ContractDuration_end' => $this->dateFormat($this->request->getPost('contract_end'),'database'),
                    'Address' => $this->request->getPost('address'),
                    'Birthdate' => $this->dateFormat($this->request->getPost('bdate'),'database'),
                    'NameOfPersonToNotify' => $this->request->getPost('personnotify'),
                    'Bloodtype' => $this->request->getPost('bloodtype'),
                    'TINNumber' => $this->request->getPost('tin'),
                    'Philhealth' => $this->request->getPost('phic'),
                    'SSS' => $this->request->getPost('sss'),
                    'PagIbigNumber' => $this->request->getPost('pagibig'),
                    'CPNumber' => $this->request->getPost('cpnum'),
                    'TypeOfEmployment' => $this->request->getPost('typeemployment'),
                    'NickName' => $this->request->getPost('nname'),
                    'NameExt' => $this->request->getPost('nameext'),
                    'Employee_ID' => $this->request->getPost('idnum'),
                    'ProfilePhoto' => $this->request->getPost('photo'),
                    'Signature' => $this->request->getPost('signature'),
                    // ... and so on for other fields
                ];
                
                $result = $this->EmployeeModel->updateEmployee($validatedData);
                
                if ($result['xstatus'] === 'success') {
                    // Handle success response
                    $data['status'] = 'success';
                    $data['message'] = 'Successfully updated employee!';
                } else {
                    // Handle error response
                    $data['status'] = 'error';
                    $data['qstatus'] = 'error';
                    $data['message'] = $result['message'];
                }
                
            }else{

                $data['fname'] = $validation->getError('fname');
                $data['mname'] = $validation->getError('mname');
                $data['lname'] = $validation->getError('lname');
                $data['sex'] = $validation->getError('sex');
                $data['position'] = $validation->getError('position');
                $data['division'] = $validation->getError('division');
                $data['section'] = $validation->getError('section');
                $data['idnum'] = $validation->getError('idnum');
                $data['photo'] = $validation->getError('photo');
                $data['signature'] = $validation->getError('signature');
            }
            
            
           
        } else {
            // Return the error response
            $data['status'] = 'error';
            $data['message'] = 'Invalid request';
        }
    
            // Set the CSRF token for the response
            $data['csrfToken'] = csrf_hash();
        
            // Return the response
            return $this->response->setJSON($data);
    }

    public function imageProcess(){

        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');
        require_once APPPATH."libraries/vendor/autoload.php";
        

        $client = new \Google_Client();
        $client->setClientId('435241396630-b73qgs0b59heh2so6murgp2sn1cvuebl.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri(base_url('process-image'));
        $client->addScope('https://www.googleapis.com/auth/drive');     

        if ($this->session->get('access_token')) {
            

            $tokenInfo = $this->session->get('access_token');
            // Validate the token using Google's token info endpoint
            $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?access_token=".urlencode($tokenInfo);

            $res = @file_get_contents($url);
            
            if ($res === false) {
                // Handle error when file_get_contents fails
                session()->remove('access_token');
                $error = error_get_last();

                $response = ['status' => 'error', 'message' => 'Request failed: Please re-login google', 'redirect' => true];

            }else{

                $token = json_decode($url, true);
                if(isset($token['error_description'])) {
                
                    session()->remove('access_token');
    
                    $response = ['status' => 'error', 'message' => 'Request failed: Please re-login google', 'redirect' => true];
                        
                } else {
    
                    $client->setAccessToken($this->session->get('access_token'));

                    if($this->request->getMethod() == 'post') {

                        $id = $this->request->getPost('empid');

                        $staffdata = $this->staffModel->getStaffData($id);
                        $empid = "";
                        if ($staffdata) {

                            $empid = $staffdata->Employee_ID;
                            
                        } else {
                            $response = ['status' => 'error', 'message' => "There's an error in the query " . $id, 'redirect' => true];
                            return $this->response->setJSON($response);
                        }
                        // Token is invalid
                    
                        if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {
                            $folderId = '1i-sIkjYrKMk1QkAEGqukMxNB9jH6Yqmi';
                            $croppedImageData = $this->request->getPost('croppedImageData');
    
                            // Validate if the data is a valid PNG image
                            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData);
                            $decodedData = base64_decode($imageData);
    
                            $newFileName = 'cropped_image.png';
                            file_put_contents(ROOTPATH . 'public/images/photos/cropped/' . $newFileName, $decodedData);
                            $imagePath = ROOTPATH . 'public/images/photos/cropped/cropped_image.png'; // Replace with the actual image path
    
                            $uploadedImage = file_get_contents($imagePath);
                            if ($uploadedImage !== false) {
                                try {
                                    // Create file metadata
                                    $fileMetadata = new Google_Service_Drive_DriveFile([
                                        'name' => 'cropped.png', // Name of the uploaded image file
                                        'parents' => [$folderId], // Parent folder ID
                                    ]);
                            
                                    // Upload the image to Google Drive
                                    $driveService = new Google_Service_Drive($client);
                                    $file = $driveService->files->create($fileMetadata, [
                                        'data' => $uploadedImage,
                                        'mimeType' => 'image/png', // MIME type of the file
                                        'uploadType' => 'multipart',
                                    ]);
    
                                    //$fileLink = 'https://drive.google.com/file/d/' . $file->id;
    
                                    
                                    //$img = 'https://pngimg.com/d/spongebob_PNG32.png';
                                    $img = 'https://drive.google.com/uc?id=' . $file->id;
    
                                    $apiKey = 'qv3x1B1wpLtfmRvupeKi16pZ';
    
                                    $ch = curl_init();
    
                                    curl_setopt($ch, CURLOPT_URL, 'https://api.remove.bg/v1.0/removebg');
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    curl_setopt($ch, CURLOPT_POST, 1);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, [
                                        'image_url' =>  $img    ,
                                    ]);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                        'X-Api-Key: ' . $apiKey,
                                    ]);
    
                                    // Execute cURL request
                                    $rescurl = curl_exec($ch);
                                    // Check if cURL request was successful
                                    if ($rescurl === false) {
                                        $response = ['status' => 'error', 'message' => 'Failed in Removing background', 'redirect' => true];
                                        // Handle cURL error
                                    } else {
                                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
                                        // Close cURL handle
                                        curl_close($ch);
    
                                        if ($httpCode === 200) {
    
                                            // Save or display the output image
                                            $outputImagePath = ROOTPATH . 'public/images/photos/test/' . $empid . '.png';
    
                                            file_put_contents($outputImagePath, $rescurl);
    
                                            $driveService->files->delete($file->id);

                                            $response = ['status' => 'success', 'message' => 'Successfully Cropped and Removed Background!', 'redirect' => true];
                                            
                                            
                                        } else {
                                            if ($httpCode === 400) {
                                                $response = ['status' => 'error', 'message' => 'Bad Request File', 'redirect' => true];
                                            }else{
                                                $response = ['status' => 'error', 'message' => $httpCode.' 	
                                                Error: Insufficient credits', 'redirect' => true];
                                            }   
                                        }
                                    }

                                } catch (Exception $e) {
                                    // Handle exceptions
                                    // echo 'Error uploading image: ' . $e->getMessage();
                                    $response = ['status' => 'error', 'message' => 'Error uploading image: ' . $e->getMessage(), 'redirect' => true];
                                }
    
                                
                            } else {
                                // echo 'Image read failed.';
                                $response = ['status' => 'error', 'message' => 'Image read failed.', 'redirect' => true];
                            }
    
                        } else {
                            $response = ['status' => 'error', 'message' => 'Invalid CSRF token', 'redirect' => true];
                        }
                        
                    } else {
                        $response = ['status' => 'error', 'message' => 'Invalid Request', 'redirect' => true];
                    }
                }
            }
            
        
            return $this->response->setJSON($response);
        }else{
            $response = ['status' => 'error', 'message' => $this->request->getMethod(), 'redirect' => true];
        }

        
    }

    public function processimage(){

        $data = [];
        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');
        $validation = \Config\Services::validation();
        $custom = $this->customobj;
        $response = array();

        $this->googleClient->setRedirectUri(base_url('process-image'));


        if ($this->request->getMethod() === 'post'){

            if (!empty($csrfToken) && $custom->validateCSRFToken($csrfToken)) {

                $croppedImageData = $this->request->getPost('croppedImageData');

                // Validate if the data is a valid PNG image
                $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $croppedImageData);
                $decodedData = base64_decode($imageData);

                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($decodedData);

                if ($mimeType !== 'image/png') {
                    $response = ['status' => 'error', 'message' => 'Only PNG images are allowed.'];

                    return $this->response->setJSON($response);
                }else{
                    // Convert base64 data to image and save it
                    $newFileName = 'cropped_image.png';
                    file_put_contents(ROOTPATH . 'public/images/photos/cropped/' . $newFileName, $decodedData);

                   

                    // Check if we have an authorization code from the proxy callback
                    if (isset($_GET['code'])) {
                        $authCode = $_GET['code'];
                        $accessToken = $this->googleClient->fetchAccessTokenWithAuthCode($authCode);
                        $this->session->set('access_token', $accessToken);
                        return redirect()->to('process-image'); // Redirect to your upload method
                    }

                    // Authenticate user by redirecting to the proxy authentication endpoint
                    $proxyAuthUrl = base_url('auth-image');
                    return redirect()->to($proxyAuthUrl);

                    // Specify the folder ID where you want to upload the image
                    $folderId = '1i-sIkjYrKMk1QkAEGqukMxNB9jH6Yqmi'; // Replace with the actual folder ID

                    // Specify the image file to upload
                    $imagePath = ROOTPATH . 'public/images/photos/cropped/cropped_image.png'; // Replace with the actual image path

                    // Create file metadata
                    $fileMetadata = new Google_Service_Drive_DriveFile([
                        'name' => 'uploaded_image.png', // Name of the uploaded image file
                        'parents' => [$folderId], // Parent folder ID
                    ]);

                    // Upload the image
                    $content = file_get_contents($imagePath);
                    $file = $this->driveService->files->create($fileMetadata, [
                        'data' => $content,
                        'mimeType' => 'image/png', // MIME type of the file
                        'uploadType' => 'multipart',
                    ]);

                    // Print the file ID of the uploaded image
                    echo 'Uploaded Image ID: ' . $file->id;

                    //$apiKey = 'zyXu7irHFkAYvyTZSTbQr2N7';

                    //$imageURL = base_url() .  'public/images/photos/cropped/cropped_image.png';

                    //$ch = curl_init();
                    //curl_setopt($ch, CURLOPT_URL, 'https://api.remove.bg/v1.0/removebg');
                    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    //curl_setopt($ch, CURLOPT_POST, 1);
                    //curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    //    'image_url' => $imageURL,
                    //]);
                    //curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    //    'X-Api-Key: ' . $apiKey,
                    //]);

                    // Execute cURL request
                    //$rescurl = curl_exec($ch);

                    // Close cURL handle
                    //curl_close($ch);



                    //if ($rescurl !== false && strpos($rescurl, 'data:image/png;base64,') === 0) {
                        // Save or display the output image
                        //$outputImagePath = WRITEPATH . 'public/images/photos/cropped/output-image.png';
                        //$outputImageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $response));
                        //file_put_contents($outputImagePath, $outputImageData);
        
                        $response = ['status' => 'success', 'message' => 'Image cropped successfully.'];
                    //} else {
                        //$response = ['status' => 'error', 'message' => 'Background removal failed.'];
                        
                    //}

                    return $this->response->setJSON($response);
                }

                

            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'CSRF token validation failed']);
            }


            
        }
        

        
    }

    public function authenticate()
    {
        $this->googleClient->setRedirectUri(base_url('process-image')); // Redirect to your upload method

        // Generate the authentication URL
       
        
    }



    private function validateCSRFToken($token)
    {
        return hash_equals(csrf_hash(), $token);
    }



    private function dateFormat($date,$type){
        if($type=='database'){
            $formattedDate = date('Y-m-d', strtotime($date));
        }elseif($type == 'display'){
            $formattedDate = date('F m, d', strtotime($date));
        }
        
        return $formattedDate;
    }


    

}
 