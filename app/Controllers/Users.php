<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\CustomLibrary;

class Users extends BaseController
{
    public $user;
    public $tl;
    public $validation;
    
    public function __construct()
    {
        
        $this->user = new UserModel; 
        $this->tl = new CustomLibrary; 
        helper(['form','html','cookie','array', 'test']);
        $this->validation = \Config\Services::validation();
        
    }

    public function index()
    {
        
        $db = \Config\Database::connect("default");
        $db1 = \Config\Database::connect("seconddb");
        $query = $db->query("SELECT u_firstname, u_email, u_status from users LIMIT 0,10");
        $result = $query->getResult();
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        //return view('userlist', $data);

        //==================================
        $query1 = $db1->query("SELECT p_title, p_description, p_price from projects LIMIT 0,10");
        $result1 = $query1->getResult();
        echo "<pre>";
        print_r($result1);
        echo "</pre>";
    }

    public function userlist()
    {

        $data['subject'] = $this->user->getData();

        /*echo "<pre>";
        print_r($data['subject']);
        echo "</pre>";*/

        return view("dataview", $data);
    }

    public function displayuser()
    {

        $data = [
            "page_title" => "User Section",
            "page_heading" => "User Section",
            "sub_head" => "List of Users",
        ];

        
        $data['users'] = $this->user->getUserList();
        $data['customlib'] = $this->tl->getData();

        return view("userview", $data);
    }

    public function userform()
    {
        

        $data = [
            "page_title" => "Add User Section",
            "page_heading" => "Add User Section",
            "sub_head" => "User Form",
            'class' => 'form-horizontal',
        ];
        
               
        return view("userformview",$data);
    }

    public function submitform()
    {

        /*$rules = [
            'username' => 'required',
            'email' => 'required|valid_email',
            'mobile' => 'required|numeric'
        ];*/
        if($this->request->getMethod() == 'post')
        {

        $rules = [
            'username' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please enter username!'
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
            'test.0' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please enter test!'
                ],
            ],
        ];

        
            
            if($this->validate($rules))
            {
                //Ready to save
                //echo "Form is successful";




                $data['success'] = true;
                
                $data['message'] = "Successfully Added!";

            }   
            else
            {   
                
                
                $data['error'] = true;
                

                $data['username'] = $this->validation->getError('username');
                $data['email'] = $this->validation->getError('email');
                $data['mobile'] = $this->validation->getError('mobile');
                $data['test'] = $this->validation->getError('test.0');

                
                //$data['test'] = "<pre>".$validation->getError('test.*')."</pre>";

                //$data['validation'] = $this->validation->getErrors();

            }

            echo json_encode($data);
        }
        else
        {
            return redirect()->route('userform');
        }

    }


}
 