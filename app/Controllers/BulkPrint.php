<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HomeModel;

class BulkPrint extends BaseController
{

    public $staffModel;
    public function __construct()

    {
        $this->staffModel = new HomeModel(); 
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
            "navactive" => "bp",
        ];

        $getDivision = $this->staffModel->getDivision();

        $data['division'] = $getDivision;
           
        $getStaff = $this->staffModel->staff();

        $data['staff'] = $getStaff;

        return view('bulkprint', $data);
    }



    
}