<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RegisterModel;
use App\Models\UserType;

class Register extends BaseController
{
    public $registerModel;
    public $userType;
    public $session;
    public $email;
    public function __construct()
    {
        $db = db_connect();
        $this->registerModel = new RegisterModel($db); 
        $this->userType = new UserType(); 
        helper(['form','html','cookie','array', 'test', 'date']);
        $this->session = \Config\Services::session();
        $this->email = \Config\Services::email();

        
    }

    public function index()
    {
        if(session()->has('logged_user')){
            return redirect()->to(base_url('home-page'));
        }
            
        $data = [
            "page_title" => "Register",
            "page_heading" => "Create an Account",
            "sub_head" => "Register",
        ];
        $data['validation'] = null;

        if($this->userType->getUserType())
        {
            $data['usertype'] = $this->userType->getUserType();
            //print_r($data['usertype']);
            $type_code = array_column($data['usertype'], 'type_code');
            //echo $type_code;
            //echo implode(', ',$type_code);

        }
        

        

        if($this->request->getMethod() == 'post')
        {
            

            $rules = [
                'username' => [
                    'rules' => 'required|min_length[4]|max_length[20]|is_unique[users.username]',
                    'errors' => [
                        'required' => 'Please enter a username!',
                        'min_length' => 'Please enter atleast {param} characters',
                        'max_length' => 'Please enter not more than {param} characters',
                        'is_unique' => 'Username already exists!',
                    ],
                ],
                'email' => [
                    'rules' => 'required|valid_email|is_unique[users.user_email]',
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
                'confirm_password' => [
                    'rules' => 'required|matches[password]',
                    'errors' => [
                        'required' => 'Please enter Confirm Password',
                        'matches' => 'Password do not match!',
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
                'utype' => [
                    'rules' => 'required|in_list['.implode(', ',$type_code).']',
                    'errors' => [
                        'required' => 'Please select User type',
                        'in_list' => 'Value does not exists in our database',
                    ],
                ],
            ];

            if($this->validate($rules))
            {
                $uniid = md5(str_shuffle('abcdefghijklmnopqrstuvwxyz'.time()));
                $userdata = [
                    'username' => $this->request->getVar('username', FILTER_SANITIZE_STRING),
                    'user_email' => $this->request->getVar('email'),
                    'user_password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                    'user_type' => $this->request->getVar('utype', FILTER_SANITIZE_STRING),
                    'mobile_num' => $this->request->getVar('mobile'),
                    'uniid' => $uniid,
                    'activation_date' => date('Y-m-d H:i:s'),
                ];
                
                if($this->registerModel->createUser($userdata))
                {
                    $to = $this->request->getVar('email');
                    $subject = 'Account Activation Link for POMIS';
                    $message = 'Hi <b>'.$this->request->getVar('uname', FILTER_SANITIZE_STRING).'</b>, <br><br>Thanks! Your account has been created Successfully. Please click the link below to activate your account<br>'
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
                
                $data['validation'] = $this->validator;
                

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
                    if($userdata->user_status == 'I')
                    {
                        $status = $this->registerModel->updateStatus($uniid);
                        if($status == true)
                        {
                            $data['success'] = 'Congratulations! Account activated Successfully!';
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
