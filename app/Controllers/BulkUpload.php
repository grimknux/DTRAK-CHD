<?php

namespace App\Controllers;
use App\Models\EmployeeModel;

class BulkUpload extends BaseController
{
    public $EmployeeModel;
    public function __construct()

    {
        
        $this->EmployeeModel = new EmployeeModel(); 
        helper(['form','html','cookie','array', 'test']);
        
    }


    public function index()
    {

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }

        $data = [
            "page_title" => "PMS Form",
            "page_heading" => "Dashboard",
            "sub_head" => "PMS Form",
            "navactive" => "bu",
        ];

        

        return view('bulkupload', $data);
    }


    public function uploadCSV()
    {
        $data = [];
        $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');
        $validation = \Config\Services::validation();

        if ($this->request->getMethod() === 'post' && !empty($csrfToken) && $this->validateCSRFToken($csrfToken)) {

           

            //if ($csvFile && $csvFile->isValid() && $csvFile->getExtension() === 'csv') {

                $validationRules = [
                    'empCSV' => [
                        'rules' => 'uploaded[empCSV]|mime_in[empCSV,text/csv,text/plain,text/tsv]',
                        'errors' => [
                            'uploaded' => 'Please select a CSV file to upload.',
                            'mime_in' => 'The uploaded file must be a valid CSV file.',
                        ],
                    ],
                ];


                if ($this->validate($validationRules)) {

                    $csvFile = $this->request->getFile('empCSV');
                    $result = $this->EmployeeModel->insertDataFromCSV($csvFile);
                    
                    if ($result['xstatus'] === 'success') {
                        $data['status'] = 'success';
                        //return view('csv_upload_form', $data);
                        //data['status'] = 'success';
                        $data['message'] = $result['message'];

                    } else {
                        $data['status'] = 'error';
                        $data['message'] = $result['message'];
                    }

                }else{
                    $data['status'] = 'error';
                    $data['message'] = $validation->getError('empCSV');
                }   

        } else {
            // Return the error response
            $data['status'] = 'error';
            $data['message'] = 'Invalid request.';

            
        }
        return $this->response->setJSON($data);
        
    }

    private function validateCSRFToken($token)
    {
        return hash_equals(csrf_hash(), $token);
    }

}
 