<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CustomObj;
use App\Models\RegisterModel;
use App\Models\UserType;

class Register extends BaseController
{
    public $registerModel;
    public $userType;
    public $session;
    public $email;
    public $customobj;
    public function __construct()
    {
        $db = db_connect();
        $this->registerModel = new RegisterModel($db); 
        $this->customobj = new CustomObj();
        $this->userType = new UserType(); 
        helper(['form','html','cookie','array', 'test', 'date']);
        $this->session = \Config\Services::session();
        $this->email = \Config\Services::email();
        $this->validation = \Config\Services::validation();

        
    }

    public function index()
    {
        
        $custom = $this->customobj;

        if(session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }
            
        $data = [
            "page_title" => "Register",
            "page_heading" => "Create an Account",
            "sub_head" => "Register",
        ];
        $data['validation'] = null;
        $data['firstname'] = null;
        $data['lastname'] = null;
        $data['email'] = null;
        $data['password'] = null;
        $data['confirmPassword'] = null;
        $data['error'] = null;

        
        if($this->request->getMethod() == 'post')
        {
            
            $csrfToken = $this->request->getVar('csrf_token');
            if (!empty($csrfToken) && $custom->validateCSRFToken($csrfToken)) {
                $rules = [
                    'firstname' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Please enter a username!',
                        ],
                    ],
                    'lastname' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Please enter a username!',
                        ],
                    ],
                    'email' => [
                        'rules' => 'required|valid_email|is_unique[administrator.Email]',
                        'errors' => [
                            'required' => 'Please provide email address',
                            'valid_email' => 'Please enter a valid email address',
                            'is_unique' => 'Email already exists!',
                        ],
                    ],
                    'password' => [
                        'rules' => 'required|min_length[4]|max_length[20]',
                        'errors' => [
                            'required' => 'Please enter a Password!',
                            'min_length' => 'Please enter atleast {param} characters',
                            'max_length' => 'Please enter not more than {param} characters',
                        ],
                    ],
                    'confirmPassword' => [
                        'rules' => 'required|matches[password]',
                        'errors' => [
                            'required' => 'Please Confirm Password',
                            'matches' => 'Password do not match!',
                        ],
                    ],
                ];

                if($this->validate($rules))
                {
                    $uniid = md5(str_shuffle('abcdefghijklmnopqrstuvwxyz'.time()));
                    $userdata = [
                        'Firstname' => $this->request->getVar('firstname', FILTER_SANITIZE_STRING),
                        'Lastname' => $this->request->getVar('lastname', FILTER_SANITIZE_STRING),
                        'Email' => $this->request->getVar('email'),
                        'Password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                        'uniid' => $uniid,
                        'Status' => "Inactive",
                    ];
                    
                    if($this->registerModel->createUser($userdata))
                    {
                        $to = $this->request->getVar('email');
                        $subject = 'Account Activation Link';
                        $message = 'Hi <b>'.$this->request->getVar('firstname', FILTER_SANITIZE_STRING).' '.$this->request->getVar('firstname', FILTER_SANITIZE_STRING).'</b>, <br><br>Thanks! Your account has been created Successfully. Please click the link below to activate your account<br>'
                        .'<a href="'.base_url().'activate-user/'.$uniid.'" target="_blank">Activate Now</a><br><br>Thanks<br>Team';
                
                        
                
                        $this->email->setTo($to);
                        $this->email->setFrom('chd1.local@gmail.com', 'Account Activation');
                        $this->email->setSubject($subject);
                        $this->email->setMessage($message);

                        if($this->email->send())
                        {
                            $this->session->setTempdata('success', 'Account created successfully! Please activate your account. Contact your administrator.', 3); 
                            return redirect()->to(current_url());
                        }
                        else
                        {
                            $this->session->setTempdata('error', 'Account created successfully! Sorry, unable to send activation link.', 3); 
                            return redirect()->to(current_url());
                        }

                        
                    }
                    else
                    {
                        $this->session->setTempdata('error', 'Sorry, unable to create account. Please try again.', 3); 
                        return redirect()->to(current_url());
                    }

                }   
                else
                {   
                    
                    //$data['validation'] = $this->validator;
                    
                    $data['firstname'] = $this->validation->getError('firstname');
                    $data['lastname'] = $this->validation->getError('lastname');
                    $data['email'] = $this->validation->getError('email');
                    $data['password'] = $this->validation->getError('password');
                    $data['confirmPassword'] = $this->validation->getError('confirmPassword');
                    $data['error'] = true;
                    

                }
            }else{
                $this->session->setTempdata('error', 'CSRF token verification failed.', 3); 
                return redirect()->to(current_url());
            }
        }

        return view('registerpage', $data);
    }

    public function activate($uniid=null)
    {

             
        
        $data = [
            "page_title" => "Activate",
            //"page_heading" => "Create an Account",
            "sub_head" => "Activate Account",
        ];
        $data['validation'] = null;

        if(!empty($uniid))
        {
            $userdata = $this->registerModel->verifyUniid($uniid);

            if($userdata)
            {
                if($this->verifyExpiryTime($userdata->activation_date))
                {
                    if($userdata->Status == 'Inactive')
                    {
                        $status = $this->registerModel->updateStatus($uniid);
                        if($status == true)
                        {
                            $data['success'] = 'Congratulations! Account activated Successfully! Please activate it within 1 hour';
                        }
                    }   
                    else
                    {
                        $data['already'] = 'Your account is already activated!';
                    }
                }
                else
                {
                    $data['error'] = 'Sorry! Activation link expired!';
                }
            }
            else{
                $data['error'] = 'Sorry! We are unable to find your account.';
            }
        }
        else
        {

            $data['error'] = 'Sorry unable to process your request!';
        }

        return view("activate_view", $data);
    }

    public function verifyExpiryTime($regTime){
        $currTime = now();
        $activatetime = strtotime($regTime);
        $diffTime = (int)$currTime - (int)$activatetime;

        if($diffTime < 3600){
            return true;
        }
        else
        {
            return false;
        }
    }
}
