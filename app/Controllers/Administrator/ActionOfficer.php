<?php

namespace App\Controllers\Administrator;
use App\Controllers\BaseController;

use App\Libraries\CustomObj;
use App\Models\OfficeModel;
use App\Models\UserLevelModel;
use App\Models\UserModel;
use App\Models\AdminMenuModel;
use App\Models\AuditTrailModel;


class ActionOfficer extends BaseController
{

    public $validation;
    public $customobj;
    public $OfficeModel;
    public $userlevelmodel;
    public $usermodel;
    public $adminmenumodel;
    public $AuditTrailModel;

    public $session;

    public function __construct()

    {

        $this->validation = \Config\Services::validation();
        $this->customobj = new CustomObj();
        $this->OfficeModel = new OfficeModel();
        $this->userlevelmodel = new UserLevelModel();
        $this->usermodel = new UserModel();
        $this->adminmenumodel = new AdminMenuModel();
        $this->audittrailmodel = new AuditTrailModel();
        
        $this->session = session();
        helper(['form','html','cookie','array', 'test', 'url', 'api']);
    }


    public function index()
    {

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(session()->get('user_level') == '-1'){
            $admin = true;
            $admin_menu = explode(',', session()->get('admin_menu'));
        }else{
            $admin = false;
            $admin_menu = [];
            return redirect()->to(base_url('/'));
        }

        if(!in_array('1', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        $pmis_emp = $this->get_users();
        $offices = $this->OfficeModel->getOffice();
        $user_levels = $this->userlevelmodel->getUserLevels();
        $admin_menus = $this->adminmenumodel->get_admin_menus();
        
        
        $navi_bread = "<li>Reference Table</li>
        <li>Document Type</li>";
        $data = [
            //'header' => 'Released and Processed (Tagged as "Done") Documents',
            'header' => '<i class="fa fa-gears"></i> Reference Table',
            'navactive' => 'reference',
            'navsubactive' => 'ref_action_officer',
            'bread' => $navi_bread,
            'offices' => $offices,
            'user_levels' => $user_levels,
            'admin_menus' => $admin_menus,
            'pmis_emp' => $pmis_emp['data'],
            'pmis_success' => $pmis_emp['success'],
            'pmis_message' => $pmis_emp['message'],

            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('administrator/reference-action-officer', $data);
    }

    public function view_action_officer_table(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url());
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

            if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                try {
                    $data = [];
                    $get_users = $this->usermodel->get_users();
                    $cnt = "";

                    if($get_users){
                        
                        foreach ($get_users as $ao) {
                            $cnt ++;

                            if($ao['status'] == 'A'){
                                $btn = '<div class="btn-group">
                                        <a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right text-left">
                                            <li class="dropdown-header">
                                                <i class="fa fa-user pull-right"></i> <strong>Action</strong>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="edit_action_officer list-group-item-action list-group-item-info" title="Edit!">
                                                    <i class="fa fa-pencil-square-o pull-right"></i>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                 <a href="javascript:void(0)" class="inactive_action_officer list-group-item-action list-group-item-warning" title="Tag as Inactive!">
                                                    <i class="fa fa-minus-circle pull-right"></i>
                                                    Deactivate
                                                </a>
                                            </li>
                                            <li>
                                                 <a href="javascript:void(0)" class="delete_action_officer list-group-item-action list-group-item-danger" title="Delete!">
                                                    <i class="fa fa-trash pull-right"></i>
                                                    Delete
                                                 </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="javascript:void(0)" class="reset_password_action_officer list-group-item-action list-group-item-default" title="Reset Password!">
                                                    <i class="fa fa-history pull-right"></i>
                                                    Reset Password
                                                 </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    
                                    ';
                            }else{
                                if (!empty($ao['deleted_at'])) {
                                    $btn = 'Deleted!';
                                }else{
                                    $btn = 'Inactive <br> <a href="javascript:void(0)" class="reactivate_action_officer" title="Reactivate Ation Officer!">Reactivate?</a>';
                                }
                                
                            }
                            

                            $middlename = ($ao['middlename'] == 'N/A' || empty($ao['middlename'])) ? '' : $ao['middlename'][0] . '.' ;
                            $data[] = [
                                'cnt' => $cnt,
                                'empcode' => $ao['empcode'],
                                'empid' => $ao['empid'],
                                'name' => $ao['firstname'] . ' ' . $middlename . ' ' . $ao['lastname'],
                                'offices' => $ao['offices'],
                                'office_rep' => ($ao['office_rep'] == 'Y') ? 'Yes' : 'No',
                                'userlevel' => $ao['userleveldesc'],
                                'btn' => $btn,
                            ];

                        }

                    }else{

                        $data ['error'] =  'No data found';

                    }           

                } catch (\Exception $e) {

                    log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                    return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                    
                }

                return $this->response->setJSON($data);

            } else {
                
                log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

            }
        }else {

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }

    }

    public function add_action_officer(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    $admin_menu = explode(',', session()->get('admin_menu'));

                    if(session()->get('user_level') == "-1" && in_array('1', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $rules = [
                                'username' => [
                                    'rules' => 'required|usernameUnique',
                                    'errors' => [
                                        'required' => 'Please enter Username.',
                                        'usernameUnique' => 'Username already exists!',
                                    ],
                                ],
                                'firstname' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please enter First Name.',
                                    ],
                                ],
                                'middlename' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please enter Middle Name.',
                                    ],
                                ],
                                'lastname' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please enter Last Name.',
                                    ],
                                ],
                                'office' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Select atleast one (1) office.',
                                    ],
                                ],
                                'office_rep' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Office Representative',
                                    ],
                                ],
                                'user_level' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select User Level',
                                    ],
                                ],

                            ];

                            if($this->request->getPost('user_level') == '-1'){
                                $rules['admin_menu'] = [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Menu Assignment',
                                    ],
                                ];
                            }
                            
                            if($this->validate($rules))
                            {
                                $empid = $this->customobj->generateEmpId();
                                $password = password_hash('12345', PASSWORD_DEFAULT);
                                $offices = $this->request->getPost('office');
                                $admin_menu = ($admin_menu_post = $this->request->getPost('admin_menu')) && is_array($admin_menu_post) && count($admin_menu_post) > 0 ? implode(',', $admin_menu_post) : null;


                                $data = [
                                    'empcode' => $this->request->getPost('username'),
                                    'empid' => $empid,
                                    'lastname' => $this->request->getPost('lastname'),
                                    'firstname' => $this->request->getPost('firstname'),
                                    'middlename' => $this->request->getPost('middlename'),
                                    'password' => $password,
                                    'userlevel' => $this->request->getPost('user_level'),
                                    'office_rep' => $this->request->getPost('office_rep'),
                                    'offices' => $this->request->getPost('office'),
                                    'admin_menu' => $admin_menu,
                                    'status' => 'A',
                                    'modby' => session()->get('logged_user')
                                ];

                                $insert = $this->usermodel->insert_action_officer($data);

                                if($insert['success']){
                                    $data = [
                                        'success' => true,
                                        'message' => 'Successfully added Action Officer'
                                    ];
                                }else{
                                     $data = [
                                        'success' => false,
                                        'message' => "Error: ".$insert['message']
                                    ];
                                }
                        
                            } else {   
                                
                                $data = [
                                    'success' => false,
                                    'formnotvalid' => true,
                                    'data' => [
                                        'username' => $this->validation->getError('username'),
                                        'firstname' => $this->validation->getError('firstname'),
                                        'middlename' => $this->validation->getError('middlename'),
                                        'lastname' => $this->validation->getError('lastname'),
                                        'office' => $this->validation->getError('office'),
                                        'office_rep' => $this->validation->getError('office_rep'),
                                        'user_level' => $this->validation->getError('user_level'),
                                        'admin_menu' => $this->validation->getError('admin_menu'),
                                    ],
                                ];
                                
                            }

                        } catch (\Exception $e) {

                            log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                            
                        }

                        return $this->response->setJSON($data);

                    }else{
                        log_message('error', 'Access Denied. You are not allowed to access this page.');
                        return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid Access']));
                    }

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else{

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
    }

    public function update_action_officer(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    $admin_menu = explode(',', session()->get('admin_menu'));

                    if(session()->get('user_level') == "-1" && in_array('1', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $rules = [
                                'firstname' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please enter First Names.',
                                    ],
                                ],
                                'middlename' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please enter Middle Name.',
                                    ],
                                ],
                                'lastname' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please enter Last Name.',
                                    ],
                                ],
                                'office' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Select atleast one (1) office.',
                                    ],
                                ],
                                'office_rep' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Office Representative',
                                    ],
                                ],
                                'user_level' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select User Level',
                                    ],
                                ],

                            ];

                            if($this->request->getPost('user_level') == '-1'){
                                $rules['admin_menu'] = [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Menu Assignment',
                                    ],
                                ];
                            }
                            
                            if($this->validate($rules))
                            {

                                $empid = $this->request->getPost('empid');
                                $get_user = $this->usermodel->get_user($empid);
                                $admin_menu = ($admin_menu_post = $this->request->getPost('admin_menu')) && is_array($admin_menu_post) && count($admin_menu_post) > 0 ? implode(',', $admin_menu_post) : null;

                                $data = [
                                    'empcode' => $get_user['empcode'],
                                    'lastname' => $this->request->getPost('lastname'),
                                    'firstname' => $this->request->getPost('firstname'),
                                    'middlename' => $this->request->getPost('middlename'),
                                    'userlevel' => $this->request->getPost('user_level'),
                                    'office_rep' => $this->request->getPost('office_rep'),
                                    'offices' => $this->request->getPost('office'),
                                    'admin_menu' => $admin_menu,
                                    'modby' => session()->get('logged_user')
                                ];

                                $update = $this->usermodel->update_action_officer($empid,$data);

                                if($update['success']){
                                    $data = [
                                        'success' => true,
                                        'message' => 'Successfully updated Action Officer'
                                    ];
                                }else{
                                     $data = [
                                        'success' => false,
                                        'message' => "Error: ".$update['message']
                                    ];
                                }
                        
                            } else {   
                                
                                $data = [
                                    'success' => false,
                                    'formnotvalid' => true,
                                    'data' => [
                                        'firstname' => $this->validation->getError('firstname'),
                                        'middlename' => $this->validation->getError('middlename'),
                                        'lastname' => $this->validation->getError('lastname'),
                                        'office' => $this->validation->getError('office'),
                                        'office_rep' => $this->validation->getError('office_rep'),
                                        'user_level' => $this->validation->getError('user_level'),
                                        'admin_menu' => $this->validation->getError('admin_menu'),
                                    ],
                                ];
                                
                            }

                        } catch (\Exception $e) {

                            log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                            
                        }

                        return $this->response->setJSON($data);

                    }else{
                        log_message('error', 'Access Denied. You are not allowed to access this page.');
                        return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid Access']));
                    }

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else{

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
    }

    public function delete_action_officer(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    $admin_menu = explode(',', session()->get('admin_menu'));

                    if(session()->get('user_level') == "-1" && in_array('1', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $empid = $this->request->getPost('empid');
                            $get_user = $this->usermodel->get_user($empid);

                            if($get_user){

                                $data = [
                                    'empcode' => $get_user['empcode'],
                                    'status' => 'I',
                                    'modby' => session()->get('logged_user')
                                ];

                                $delete = $this->usermodel->delete_action_officer($empid,$data);

                                if($delete['success']){

                                    $data = [
                                        'success' => true,
                                        'message' => $delete['message']
                                    ];
                                }else{
                                    throw new \Exception("Delete Error: " . $delete['message']);
                                }
                                 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error deleting Action Officer. ',
                                ];
                            }


                        } catch (\Exception $e) {

                            log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                            
                        }

                        return $this->response->setJSON($data);

                    }else{
                        log_message('error', 'Access Denied. You are not allowed to access this page.');
                        return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid Access']));
                    }

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else{

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
    }

    public function inactive_action_officer(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    $admin_menu = explode(',', session()->get('admin_menu'));

                    if(session()->get('user_level') == "-1" && in_array('1', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $empid = $this->request->getPost('empid');
                            $get_user = $this->usermodel->get_user($empid);

                            if($get_user){

                                $data = [
                                  'status' => 'I',
                                  'modby' => session()->get('logged_user')
                                ];

                                $inactive = $this->usermodel->update_action_officer($empid,$data);

                                if($inactive['success']){

                                    $data = [
                                        'success' => true,
                                        'message' => 'Action Officer Deactivated.'
                                    ];
                                }else{
                                    throw new \Exception("Delete Error: " . $inactive['message']);
                                }
                                 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error deactivating Action Officer. ',
                                ];
                            }


                        } catch (\Exception $e) {

                            log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                            
                        }

                        return $this->response->setJSON($data);

                    }else{
                        log_message('error', 'Access Denied. You are not allowed to access this page.');
                        return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid Access']));
                    }

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else{

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
    }

    public function reactivate_action_officer(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    $admin_menu = explode(',', session()->get('admin_menu'));

                    if(session()->get('user_level') == "-1" && in_array('1', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $empid = $this->request->getPost('empid');
                            $get_user = $this->usermodel->get_user($empid,$status = "I");

                            if($get_user){

                                $data = [
                                  'status' => 'A',
                                  'modby' => session()->get('logged_user')
                                ];

                                $inactive = $this->usermodel->update_action_officer($empid,$data);

                                if($inactive['success']){

                                    $data = [
                                        'success' => true,
                                        'message' => 'Action Officer Reactivated.'
                                    ];
                                }else{
                                    throw new \Exception("Reactivate Error: " . $inactive['message']);
                                }
                                 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error reactivating Action Officer. ',
                                ];
                            }


                        } catch (\Exception $e) {

                            log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                            
                        }

                        return $this->response->setJSON($data);

                    }else{
                        log_message('error', 'Access Denied. You are not allowed to access this page.');
                        return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid Access']));
                    }

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else{

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
    }

    public function reset_password_action_officer(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    $admin_menu = explode(',', session()->get('admin_menu'));

                    if(session()->get('user_level') == "-1" && in_array('1', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $empid = $this->request->getPost('empid');
                            $get_user = $this->usermodel->get_user($empid,$status = "A");

                            if($get_user){
                                $password = password_hash('12345', PASSWORD_DEFAULT);
                                $data = [
                                  'password' => $password,
                                  'modby' => session()->get('logged_user')
                                ];

                                $inactive = $this->usermodel->update_action_officer($empid,$data);

                                if($inactive['success']){

                                    $data = [
                                        'success' => true,
                                        'message' => 'Successfully Reset Password!.'
                                    ];
                                }else{
                                    throw new \Exception("Reactivate Error: " . $inactive['message']);
                                }
                                 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error resetting password for Action Officer. ',
                                ];
                            }


                        } catch (\Exception $e) {

                            log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                            
                        }

                        return $this->response->setJSON($data);

                    }else{
                        log_message('error', 'Access Denied. You are not allowed to access this page.');
                        return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid Access']));
                    }

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else{

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
    }


    public function get_action_officer(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    $admin_menu = explode(',', session()->get('admin_menu'));

                    if(session()->get('user_level') == "-1" && in_array('1', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $empid = $this->request->getPost('empid');
                            $get_user = $this->usermodel->get_user($empid);

                            if($get_user){

                                $offices = explode(',', $get_user['offices']);
                                $admin_menu = explode(',', $get_user['admin_menu']);
                                $data = [
                                    'success' => true,
                                    'data' => [
                                        'empcode' => $get_user['empcode'],
                                        'lastname' => $get_user['lastname'],
                                        'firstname' => $get_user['firstname'],
                                        'middlename' => $get_user['middlename'],
                                        'userlevel' => $get_user['userlevel'],
                                        'office_rep' => $get_user['office_rep'],
                                        'admin_menu' => $admin_menu,
                                        'offices' => $offices,
                                    ],
                                    
                                ]; 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error fetching Action Officer Details.'
                                ];
                            }


                        } catch (\Exception $e) {

                            log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                            
                        }

                        return $this->response->setJSON($data);

                    }else{
                        log_message('error', 'Access Denied. You are not allowed to access this page.');
                        return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid Access']));
                    }

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else{

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
    }


    public function get_users(){

        try {
            $system = 'pmis';
            $api = getApiConfig($system);

            $apiKey = $api['key'];
            $apiUrl = $api['url'];

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ApiKey ' . $apiKey,
                    'Accept: application/json'
                ],
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $errorMsg = curl_error($curl);
                curl_close($curl);

                // 🔴 Handle unreachable API
                throw new \Exception ("API request failed: Error fetching PMIS users. $errorMsg");
            }

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $data = json_decode($response, true);

            if ($httpCode !== 200) {
                // 🔴 Failed authorization or error
                //echo "Error ({$data['code']}): " . $data['message'];
                throw new \Exception("Error ({$data['code']}): " . $data['message']);
            }

            // ✅ Success
            $data = [
                'success' => true,
                'data' => $data['data'],
                'message' => $data['data'],
            ];

            return $data;
            
        } catch (\Exception  $e) {
            $data = [
                'success' => false,
                'data' => [],
                'message' => $e->getMessage(),
            ];
             return $data;
        }

    }


}