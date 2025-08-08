<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CustomObj;
use App\Models\LoginModel;
use App\Models\UserModel;

class Login extends BaseController
{
    public $session;
    public $loginModel;
    public $usermodel;
    public $validation;
    public $customobj;
    public function __construct()
    {
        $this->loginModel = new LoginModel(); 
        $this->customobj = new CustomObj();
        $this->usermodel = new UserModel();
        $this->session = session();
        $this->validation = \Config\Services::validation();
        helper(['form','html','cookie','array', 'test']);
        
    }

    public function index()
    {
        $custom = $this->customobj;
        if(session()->has('logged_user')){
            return redirect()->to(base_url('doctoreceive/receive'));
        }

        
        $data = [];
        
        $data['username'] = null;
        $data['password'] = null;
        $data['error'] = null;

        if($this->request->getMethod() == 'post')
        {   
            $csrfToken = $this->request->getVar('csrf_token');

            if (!empty($csrfToken) && $custom->validateCSRFToken($csrfToken)) {
                $rules = [
                    'username' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Please provide username',
                        ],
                    ],
                    'password' => [
                        'rules' => 'required|min_length[4]|max_length[30]',
                        'errors' => [
                            'required' => 'Please enter Password!',
                            'min_length' => 'Please enter atleast {param} characters',
                            'max_length' => 'Please enter not more than {param} characters',
                        ],
                    ],
                ];
    
    
                if($this->validate($rules))
                {
                    $username = $this->request->getVar('username');
                    $password = $this->request->getVar('password');
                    

                    $userdata = $this->loginModel->verifyUser($username);
                    
                    if($userdata)
                    {

                        if(empty($userdata['password'])){

                            $new_password = password_hash($password, PASSWORD_DEFAULT);
                            if(!$this->add_new_password($username,$new_password)){
                                $this->session->setTempdata('error', 'An error occured while logging-in', 3); 
                                return redirect()->to(current_url());
                            }

                                if($custom->verifyPassword($password, trim($userdata['userpass']))){
                                
                                $loginInfo = [

                                    'uniid' => $userdata['empcode'],
                                    'agent' => $this->getUserAgentInfo(),
                                    'ip' => $this->request->getIPAddress()

                                ];

                                $lastid = $this->loginModel->saveLoginInfo($loginInfo);

                                if($lastid){
                                    $this->session->set('logged_info',$lastid);
                                }

                                $this->session->set('logged_user',$userdata['empcode']);
                                $this->session->set('user_level',$userdata['userlevel']);
                                $this->session->set('admin_menu',$userdata['admin_menu']);
                                
                                return redirect()->to(base_url('doctoreceive/receive'));
                            
                            }else{
                                $this->session->setTempdata('error', 'Sorry, wrong password for that Username', 3); 
                                return redirect()->to(current_url());
                            }

                        }else{

                            if(password_verify($password, trim($userdata['password']))){
                                
                                $loginInfo = [

                                    'uniid' => $userdata['empcode'],
                                    'agent' => $this->getUserAgentInfo(),
                                    'ip' => $this->request->getIPAddress()

                                ];

                                $lastid = $this->loginModel->saveLoginInfo($loginInfo);

                                if($lastid){
                                    $this->session->set('logged_info',$lastid);
                                }

                                $this->session->set('logged_user',$userdata['empcode']);
                                $this->session->set('user_level',$userdata['userlevel']);
                                $this->session->set('admin_menu',$userdata['admin_menu']);
                                
                                return redirect()->to(base_url('doctoreceive/receive'));
                            
                            }else{
                                $this->session->setTempdata('error', 'Sorry, wrong password for that Username', 3); 
                                return redirect()->to(current_url());
                            }

                        }

                    }
                    else
                    {
                        $this->session->setTempdata('error', 'Sorry, Username does not exist', 3); 
                            return redirect()->to(current_url());
                    }
                    

                }   
                else
                {   
                    
                    $data['username'] = $this->validation->getError('username');
                    $data['password'] = $this->validation->getError('password');

                    $data['error'] = true;

                }

            }else{
                

                $this->session->setTempdata('error', 'CSRF token verification failed.', 3); 
                return redirect()->to(current_url());
            }
            
        }

        return view('loginpage', $data);

        
    }

    private function add_new_password($username, $new_password){

        $data = [
            'password' => $new_password,
        ];

        if($this->usermodel->update_action_officer_by_empcode($username,$data)['success']){
            return true;
        }

        return false;
        
    }


    public function getUserAgentInfo()
    {
        $agent = $this->request->getUserAgent();
        if($agent->isBrowser())
        {
            $currentAgent = $agent->getBrowser();
        }
        elseif($agent->isRobot())
        {
            $currentAgent = $this->agent->isRobot();
        }
        elseif($agent->isMobile())
        {
            $currentAgent = $agent->isMobile();   
        }
        else
        {
            $currentAgent = "Unidentified User Agent";
        }

        return $currentAgent;
        
    }

    public function logout()
    {
        if(session()->has('logged_info')){
            $lastid = session()->get('logged_info');
            $this->loginModel->updateLogoutTime($lastid);
        }

        session()->remove('logged_user');
        session()->remove('logged_info');
        session()->remove('user_level');
        session()->remove('admin_menu');
        session()->destroy(); 
        return redirect()->to(base_url());
    }

}
