<?php

namespace App\Controllers\Administrator;
use App\Controllers\BaseController;

use App\Libraries\CustomObj;
use App\Models\OfficeModel;
use App\Models\UserLevelModel;
use App\Models\UserModel;
use App\Models\ActionModel;
use App\Models\ActionTakenModel;
use App\Models\AdminMenuModel;
use App\Models\AuditTrailModel;


class ActionTaken extends BaseController
{

    public $validation;
    public $customobj;
    public $OfficeModel;
    public $userlevelmodel;
    public $usermodel;
    public $actionmodel;
    public $actiontakenmodel;
    public $adminmenumodel;
    public $audittrailmodel;

    public $session;

    public function __construct()

    {

        $this->validation = \Config\Services::validation();
        $this->customobj = new CustomObj();
        $this->OfficeModel = new OfficeModel();
        $this->userlevelmodel = new UserLevelModel();
        $this->usermodel = new UserModel();
        $this->actionmodel = new ActionModel();
        $this->actiontakenmodel = new ActionTakenModel();
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

        if(!in_array('7', $admin_menu)){
            return redirect()->to(base_url('/'));
        }
        

        $navi_bread = "<li>Reference Table</li>
        <li>Action Required</li>";
        $data = [
            //'header' => 'Released and Processed (Tagged as "Done") Documents',
            'header' => '<i class="fa fa-gears"></i> Reference Table',
            'navactive' => 'reference',
            'navsubactive' => 'ref_action_taken',
            'bread' => $navi_bread,

            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('administrator/reference-action-taken', $data);
    }

    public function view_action_taken_table(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url());
        }

        if(session()->get('user_level') == '-1'){
            $admin = true;
            $admin_menu = explode(',', session()->get('admin_menu'));
        }else{
            $admin = false;
            $admin_menu = [];
            return redirect()->to(base_url('/'));
        }

        if(!in_array('7', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

            if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                try {
                    $data = [];
                    $get_action_taken = $this->actiontakenmodel->get_action_taken_all();
                    $cnt = "";

                    if($get_action_taken){
                        
                        foreach ($get_action_taken as $actiontaken) {
                            $cnt ++;

                            if($actiontaken['act_tstatus'] == 'Active'){
                                $btn = '<div class="btn-group">
                                        <a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right text-left">
                                            <li class="dropdown-header">
                                                <i class="fa fa-user pull-right"></i> <strong>Action</strong>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="edit_action_taken list-group-item-action list-group-item-info" title="Edit!">
                                                    <i class="fa fa-pencil-square-o pull-right"></i>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                 <a href="javascript:void(0)" class="inactive_action_taken list-group-item-action list-group-item-warning" title="Tag as Inactive!">
                                                    <i class="fa fa-minus-circle pull-right"></i>
                                                    Deactivate
                                                </a>
                                            </li>
                                            <li>
                                                 <a href="javascript:void(0)" class="delete_action_taken list-group-item-action list-group-item-danger" title="Delete!">
                                                    <i class="fa fa-trash pull-right"></i>
                                                    Delete
                                                 </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    ';
                            }else{
                                if (!empty($actiontaken['deleted_at'])) {
                                    $btn = 'Deleted!';
                                }else{
                                    $btn = 'Inactive <br> <a href="javascript:void(0)" class="reactivate_action_taken" title="Reactivate Document Type!">Reactivate?</a>';
                                }
                                
                            }
                            
                            $data[] = [
                                'cnt' => $cnt,
                                'action_code' => $actiontaken['action_code'],
                                'action_desc' => $actiontaken['action_desc'],
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

    public function add_action_taken(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('7', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('7', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $rules = [
                                'action_taken' => [
                                    'rules' => 'required|actiontakenUnique',
                                    'errors' => [
                                        'required' => 'Please select Action to be Taken.',
                                        'actiontakenUnique' => 'Action Taken already exists!',
                                    ],
                                ],

                            ];
                            
                            if($this->validate($rules))
                            {
                                $action_code = $this->actiontakenmodel->generate_action_taken_code();

                                $data = [
                                    'action_code' => $action_code,
                                    'action_desc' => $this->request->getPost('action_taken'),
                                ];

                                $insert = $this->actiontakenmodel->insert_action_taken($data);

                                if($insert['success']){
                                    $data = [
                                        'success' => true,
                                        'message' => 'Successfully added Action Taken'
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
                                        'action_taken' => $this->validation->getError('action_taken'),
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

    public function update_action_required(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('7', $admin_menu)){
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
                            $reqaction_code = $this->request->getPost('reqaction_code');
                            
                            $rules = [
                                'action_required' => [
                                    'rules' => 'required|actionrequireUnique['.$reqaction_code.']',
                                    'errors' => [
                                        'required' => 'Please enter Action Required.',
                                        'actionrequireUnique' => 'Action Required already exists!',
                                    ],
                                ],
                                'action_taken' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Action to be Taken.',
                                    ],
                                ],

                            ];
                            
                            if($this->validate($rules))
                            {

                                $data = [
                                    'reqaction_desc' => $this->request->getPost('action_required'),
                                    'reqaction_done' => $this->request->getPost('action_taken'),
                                ];

                                $update = $this->actionmodel->update_action_required($reqaction_code,$data);

                                if($update['success']){
                                    $data = [
                                        'success' => true,
                                        'message' => 'Successfully updated Action Required'
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
                                        'action_required' => $this->validation->getError('action_required'),
                                        'action_taken' => $this->validation->getError('action_taken'),
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

    public function delete_action_required(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('7', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('7', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $reqaction_code = $this->request->getPost('reqaction_code');
                            $get_action_required = $this->actionmodel->getActionByRequire($reqaction_code);

                            if($get_action_required){

                                $data = [
                                    'act_rstatus' => 'Inactive',
                                ];

                                $delete = $this->actionmodel->delete_action_required($reqaction_code,$data);

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
                                    'message' => 'Error deleting Action Required. ',
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

    public function inactive_action_required(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('7', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('7', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $reqaction_code = $this->request->getPost('reqaction_code');
                            $get_action_required = $this->actionmodel->getActionByRequire($reqaction_code);

                            if($get_action_required){

                                $data = [
                                    'act_rstatus' => 'Inactive',
                                ];

                                $inactive = $this->actionmodel->update_action_required($reqaction_code,$data);

                                if($inactive['success']){

                                    $data = [
                                        'success' => true,
                                        'message' => $inactive['message']
                                    ];
                                }else{
                                    throw new \Exception("Inactive Error: " . $inactive['message']);
                                }
                                 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error deactivating Action Required. ',
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

    public function reactivate_document_type(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('7', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('7', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $reqaction_code = $this->request->getPost('reqaction_code');
                            $get_action_required = $this->actionmodel->getActionByRequire($reqaction_code, $status = "Inactive");

                            if($get_action_required){

                                $data = [
                                    'act_rstatus' => 'Active',
                                ];

                                $reactivate = $this->actionmodel->update_action_required($reqaction_code,$data);

                                if($reactivate['success']){

                                    $data = [
                                        'success' => true,
                                        'message' => $reactivate['message']
                                    ];
                                }else{
                                    throw new \Exception("Reactivate Error: " . $reactivate['message']);
                                }
                                 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error reactivating Document Type. ',
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

    public function get_action_required(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('7', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('7', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $reqaction_code = $this->request->getPost('reqaction_code');
                            $get_action_required = $this->actionmodel->getActionByRequire($reqaction_code);

                            if($get_action_required){

                                $data = [
                                    'success' => true,
                                    'data' => [
                                        'reqaction_code' => $get_action_required['reqaction_code'],
                                        'reqaction_desc' => $get_action_required['reqaction_desc'],
                                        'reqaction_done' => $get_action_required['reqaction_done'],
                                    ],
                                    
                                ]; 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error fetching Action Required Details.'
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

}