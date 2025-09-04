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
                            $add_password = $this->update_user($username,$new_password);
                            if(!$add_password['success']){
                                $this->session->setTempdata('error', 'An error occured while logging-in: '.$add_password['message'], 3); 
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
                                $this->session->set('user_fullname',strtoupper($userdata['firstname'] . ' ' . $userdata['lastname']));
                                $this->session->set('user_fname',strtoupper($userdata['firstname']));
                                
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
                                $this->session->set('user_fullname',strtoupper($userdata['firstname'] . ' ' . $userdata['lastname']));
                                $this->session->set('user_fname',strtoupper($userdata['firstname']));
                                
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

    private function update_user($username, $new_password){

        $data = [
            'password' => $new_password,
        ];

        $update_user = $this->usermodel->update_action_officer_by_empcode($username,$data);
        
        if($update_user['success']){
            return [
                'success' => true,
                'message' => 'Password updated successfully'
            ];
        }

        return [
            'success' => false,
            'message' => $update_user['message']
        ];
        
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


    public function change_password()
    {
        if (!session()->has('logged_user')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        if (!$this->request->isAJAX()) {
            log_message('error', 'Invalid Ajax Request on change_password()');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid Ajax Request']);
        }

        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method Not Allowed']);
        }

        $csrfToken = $this->request->getPost('csrf_token');
        if (empty($csrfToken) || !$this->customobj->validateCSRFToken($csrfToken)) {
            log_message('error', 'Invalid CSRF token on change_password(), IP: ' . $this->request->getIPAddress());
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid CSRF token']);
        }

        try {
            $logged_user = session()->get('logged_user');


            $rules = [
                'prof_old_password' => [
                    'rules' => 'required|confirmOldPassword',
                    'errors' => [
                        'required' => 'Please enter Current Password.',
                        'confirmOldPassword' => 'Password is incorrect.',
                    ],
                ],

                'prof_new_password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please enter New Password.',
                    ],
                ],
                
                'prof_new_password_confirm' => [
                    'rules' => 'required|matches[prof_new_password]',
                    'errors' => [
                        'required' => 'Please enter Confirm Password.',
                        'matches' => 'Password does not match.',
                    ],
                ],

            ];

            if($this->validate($rules))
            {
                
                $oldPassword = $this->request->getPost('prof_old_password');
                $newPassword = $this->request->getPost('prof_new_password');

                $new_password = password_hash($newPassword, PASSWORD_DEFAULT);
                $change_password = $this->update_user($logged_user, $new_password);

                if($change_password['success']){
                    
                    $data = [
                        'success' => true,
                        'message' => 'Password updated successfully. You will be logged out in a few seconds.'
                    ];
                }else{
                    $data = [
                        'success' => false,
                        'formnotvalid' => false,
                        'message' => 'An error occured while updating password: '.$change_password['message']
                    ];
                }

            } else {   
                                
                $data = [
                    'success' => false,
                    'formnotvalid' => true,
                    'data' => [
                        'prof_old_password' => $this->validation->getError('prof_old_password'),
                        'prof_new_password' => $this->validation->getError('prof_new_password'),
                        'prof_new_password_confirm' => $this->validation->getError('prof_new_password_confirm'),
                    ],
                ];
                
            }

            return $this->response->setJSON($data);

        } catch (\Exception $e) {
            log_message('error', 'Error in change_password(): ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error occurred']);
        }
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
        return redirect()->to(base_url('/'));
    }

}
