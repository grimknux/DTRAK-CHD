<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ContactModel;

class Contacts extends BaseController
{
    public $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactModel; 
        helper(['form','html','cookie','array', 'test']);
        
    }

    public function index()
    {
        $data = [
            "page_title" => "Add Contact Section",
            "page_heading" => "Add Contact Section",
            "sub_head" => "Contact Form",
        ];
        $session = \Config\Services::session();

        $data['validation'] = null;
        
        $rules = [
            'uname' => [
                'rules' => 'required|min_length[4]|max_length[20]',
                'errors' => [
                    'required' => 'Please enter username!',
                    'min_length' => 'Please enter atleast {param} characters',
                    'max_length' => 'Please enter not more than {param} characters',
                ],
            ],
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Please provide email address',
                    'valid_email' => 'Please enter a valid email address',
                ],  
            ],
            'mobile' => [
                'rules' => 'required|numeric|min_length[10]',
                'errors' => [
                    'required' => 'Please enter mobile number',
                    'numeric' => 'Mobile number: {value} should be numbers only',
                    'min_length' => 'Mobile {value} should contain atleast {param} digits',
                ],
            ],
            'msg' => [
                'rule' => 'required',
                'errors' => [
                    'required' => 'Please enter a message for us',
                ]
            ]
        ];

        if($this->request->getMethod() == 'post')
        {
            if($this->validate($rules))
            {
                $cdata = [
                    'username' => $this->request->getVar('uname', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_STRING),
                    'mobile' => $this->request->getVar('mobile', FILTER_SANITIZE_STRING),
                    'message' => $this->request->getVar('msg', FILTER_SANITIZE_STRING),
                ];
                
                $status = $this->contactModel->saveData($cdata);
                
                if($status)
                {
                   $session->setTempdata('success', 'Thanks, we will get back to you soon', 3); 
                   return redirect()->to(current_url());
                }
                else
                {
                    $session->setTempdata('error', 'Sorry, Try again', 4);
                    return redirect()->to(current_url());
                }

            }
            else
            {
                $data['validation'] = $this->validator;
            }
        }
       
               
        return view("contactview",$data);
    }
}
