<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LoginModel;

class Login extends BaseController
{
    public $loginModel;
    public $session;
    public function __construct()
    {
        $db = db_connect();
        $this->loginModel = new LoginModel($db); 
        $this->session = session();
        helper(['form','html','cookie','array', 'test']);
        
    }

    public function index()
    {

        if(session()->has('logged_user') || session()->has('google_user')){
            return redirect()->to(base_url('home-page'));
        }

        
        $data = [
            "page_title" => "Login",
            "page_heading" => "Welcome to DOH POMIS",
            "sub_head" => "Please Login",
        ];
        $data['validation'] = null;
        //#######################-----SITE LOGIN------################################

        
        if($this->request->getMethod() == 'post')
        {
            

            $rules = [
                'email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Please provide email address',
                        'valid_email' => 'Please enter a valid email address',
                    ],
                ],
                'password' => [
                    'rules' => 'required|min_length[4]|max_length[20]',
                    'errors' => [
                        'required' => 'Please enter Password!',
                        'min_length' => 'Please enter atleast {param} characters',
                        'max_length' => 'Please enter not more than {param} characters',
                    ],
                ],
            ];

            if($this->validate($rules))
            {
                $email = $this->request->getVar('email');
                $password = $this->request->getVar('password');

                $userdata = $this->loginModel->verifyEmail($email);
                if($userdata)
                {
                    if(password_verify($password, $userdata['user_password'])){

                        if($userdata['user_status'] == 'A')
                        {
                            $loginInfo = [
                                'uniid' => $userdata['uniid'],
                                'agent' => $this->getUserAgentInfo(),
                                'ip' => $this->request->getIPAddress(),
                                'login_time' => date('Y-m-d H:i:s'),

                            ];

                            $lastid = $this->loginModel->saveLoginInfo($loginInfo);

                            if($lastid){
                                $this->session->set('logged_info',$lastid);
                            }

                            $this->session->set('logged_user',$userdata['uniid']);
                            $this->session->set('logged_type',$userdata['user_type']);
                            
                            return redirect()->to(base_url('home-page'));
                        }
                        else
                        {
                            $this->session->setTempdata('error', 'Please activate your account. Contact system administrator.', 3); 
                            return redirect()->to(current_url());
                        }
                    }
                    else
                    {
                        $this->session->setTempdata('error', 'Sorry, wrong password for that email', 3); 
                        return redirect()->to(current_url());
                    }

                }
                else
                {
                    $this->session->setTempdata('error', 'Sorry, email does not exist', 3); 
                        return redirect()->to(current_url());
                }

            }   
            else
            {   
                
                $data['validation'] = $this->validator;
                

            }
        }
        //#######################-----END SITE LOGIN------################################

        //#######################-----GOOGLE LOGIN------################################
        require_once APPPATH."libraries/vendor/autoload.php";

        $google_client = new \Google_Client();
        $google_client->setClientId('323535648684-lh435kc6t15ufngist51o4gs9dl1faon.apps.googleusercontent.com');
        $google_client->setClientSecret('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');
        $google_client->setRedirectUri(base_url('login-page'));
        $google_client->addScope('email');
        $google_client->addScope('profile');

        if($this->request->getVar('code'))
        {
            $token = $google_client->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
            if(!isset($token['error']))
            {
                $google_client->setAccessToken($token['access_token']);
                $this->session->set('access_token', $token['access_token']);

                $google_service = new \Google_Service_Oauth2($google_client);
                
                $gdata = $google_service->userinfo->get();

                if($this->loginModel->google_user_exists($gdata['id']))
                {
                    //update
                    $userdata = [
                        'first_name' => $gdata['given_name'],
                        'last_name' => $gdata['family_name'],
                        'email' => $gdata['email'],
                        'profile_pic' => $gdata['picture'],
                    ];

                    $this->loginModel->updateGoogleUser($userdata, $gdata['id']);

                    $this->session->set('google_user',$userdata);
                    return redirect()->to(base_url('home-page'));
                }
                else
                {
                    //insert
                    $userdata = [
                        'oauth_id' => $gdata['id'],
                        'first_name' => $gdata['given_name'],
                        'last_name' => $gdata['family_name'],
                        'email' => $gdata['email'],
                        'profile_pic' => $gdata['picture'],
                    ];

                    $this->loginModel->createGoogleUser($userdata);

                    $this->session->set('google_user',$userdata);
                    return redirect()->to(base_url('home-page'));

                }


            }
        }

        
        //#######################-----END GOOGLE LOGIN------################################

        if(!$this->session->get('access_token'))
        {
            $data['loginButton'] = $google_client->createAuthUrl();
        }


        return view('loginpage', $data);
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

    

}
