<?php

namespace App\Controllers;

class TestMail extends BaseController
{
    public function index()
    {
        
        $to = 'para.dikalag60@gmail.com';
        $subject = 'Account Activation';
        $message = 'Hi User, <br><br>Thanks! Your account has been created Successfully. Please click the link below to activate your account<br>'
        .'<a href="'.base_url().'testmail/verify" target="_blank">Activate Now</a><br><br>Thanks<br>Team';

        $email = \Config\Services::email();

        $email->setTo($to);
        $email->setFrom('chd1.local@gmail.com', 'Account Activation');
       // $email->setBCC('grim.knuxklez26@gmail.com');

        $email->setSubject($subject);
        $email->setMessage($message);
        $filpath = base_url().'public/images/dohlogo.png';
        $email->attach($filpath);

        if($email->send())
        {
            echo "Account Created Successfully! Please activate your account!";
        }
        else
        {
            $data = $email->printDebugger(['headers']);
            print_r($data);
        }

    }

    public function verify                                                                  ()
    {
        echo "Account Verified";
    }
}
 