<?php

namespace App\Controllers\Administrator;
use App\Controllers\BaseController;

use App\Libraries\CustomObj;
use App\Models\OfficeModel;
use App\Models\UserLevelModel;
use App\Models\UserModel;
use App\Models\AdminMenuModel;
use App\Models\DocumentTypeModel;
use App\Models\AuditTrailModel;


class DocumentType extends BaseController
{

    public $validation;
    public $customobj;
    public $OfficeModel;
    public $userlevelmodel;
    public $usermodel;
    public $adminmenumodel;
    public $documenttypemodel;
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
        $this->documenttypemodel = new DocumentTypeModel();
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

        if(!in_array('3', $admin_menu)){
            return redirect()->to(base_url('/'));
        }
        
        
        $navi_bread = "<li>Reference Table</li>
        <li>Document Type</li>";
        $data = [
            //'header' => 'Released and Processed (Tagged as "Done") Documents',
            'header' => '<i class="fa fa-gears"></i> Reference Table',
            'navactive' => 'reference',
            'navsubactive' => 'ref_document_type',
            'bread' => $navi_bread,

            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('administrator/reference-document-type', $data);
    }

    public function view_document_type_table(){

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

        if(!in_array('3', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

            if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                try {
                    $data = [];
                    $get_doc_types = $this->documenttypemodel->get_doc_types();
                    $cnt = "";

                    if($get_doc_types){
                        
                        foreach ($get_doc_types as $doctype) {
                            $cnt ++;

                            if($doctype['status'] == 'Active'){
                                $btn = '<div class="btn-group">
                                        <a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right text-left">
                                            <li class="dropdown-header">
                                                <i class="fa fa-user pull-right"></i> <strong>Action</strong>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="edit_document_type list-group-item-action list-group-item-info" title="Edit!">
                                                    <i class="fa fa-pencil-square-o pull-right"></i>
                                                    Edit
                                                </a>
                                            </li>
                                            <li>
                                                 <a href="javascript:void(0)" class="inactive_document_type list-group-item-action list-group-item-warning" title="Tag as Inactive!">
                                                    <i class="fa fa-minus-circle pull-right"></i>
                                                    Deactivate
                                                </a>
                                            </li>
                                            <li>
                                                 <a href="javascript:void(0)" class="delete_document_type list-group-item-action list-group-item-danger" title="Delete!">
                                                    <i class="fa fa-trash pull-right"></i>
                                                    Delete
                                                 </a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    
                                    ';
                            }else{
                                if (!empty($doctype['deleted_at'])) {
                                    $btn = 'Deleted!';
                                }else{
                                    $btn = 'Inactive <br> <a href="javascript:void(0)" class="reactivate_document_type" title="Reactivate Document Type!">Reactivate?</a>';
                                }
                                
                            }
                            
                            $data[] = [
                                'cnt' => $cnt,
                                'type_code' => $doctype['type_code'],
                                'type_desc' => $doctype['type_desc'],
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

    public function add_document_type(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('3', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('3', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $rules = [
                                'doc_type' => [
                                    'rules' => 'required|doctypeUnique',
                                    'errors' => [
                                        'required' => 'Please enter Document Type.',
                                        'doctypeUnique' => 'Document Type already exists!',
                                    ],
                                ],

                            ];
                            
                            if($this->validate($rules))
                            {
                                $type_code = $this->documenttypemodel->generate_simple_code();

                                $data = [
                                    'type_code' => $type_code,
                                    'type_desc' => $this->request->getPost('doc_type'),
                                    'status' => 'Active',
                                ];

                                $insert = $this->documenttypemodel->insert_document_type($data);

                                if($insert['success']){
                                    $data = [
                                        'success' => true,
                                        'message' => 'Successfully added Document Type'
                                    ];
                                }else{
                                     $data = [
                                        'success' => false,
                                        'message' => "Error: ".$insert['message'] . $this->request->getPost('doc_type') . $type_code
                                    ];
                                }
                        
                            } else {   
                                
                                $data = [
                                    'success' => false,
                                    'formnotvalid' => true,
                                    'data' => [
                                        'doc_type' => $this->validation->getError('doc_type'),
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

    public function update_document_type(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('3', $admin_menu)){
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
                            $type_code = $this->request->getPost('type_code');
                            
                            $rules = [
                                'doc_type' => [
                                    'rules' => 'required|doctypeUnique['.$type_code.']',
                                    'errors' => [
                                        'required' => 'Please enter Document Type.',
                                        'doctypeUnique' => 'Document Type already exists!',
                                    ],
                                ],

                            ];
                            
                            if($this->validate($rules))
                            {
                                

                                $data = [
                                    'type_desc' => $this->request->getPost('doc_type'),
                                ];

                                $update = $this->documenttypemodel->update_document_type($type_code,$data);

                                if($update['success']){
                                    $data = [
                                        'success' => true,
                                        'message' => 'Successfully updated Document Type'
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
                                        'doc_type' => $this->validation->getError('doc_type'),
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

    public function delete_document_type(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('3', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('3', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $type_code = $this->request->getPost('type_code');
                            $get_doctype = $this->documenttypemodel->get_doc_type($type_code);

                            if($get_doctype){

                                $data = [
                                    'status' => 'Inactive',
                                ];

                                $delete = $this->documenttypemodel->delete_document_type($type_code,$data);

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
                                    'message' => 'Error deleting Document Type. ',
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

    public function inactive_document_type(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('3', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('3', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $type_code = $this->request->getPost('type_code');
                            $get_doctype = $this->documenttypemodel->get_doc_type($type_code);

                            if($get_doctype){

                                $data = [
                                    'status' => 'Inactive',
                                ];

                                $inactive = $this->documenttypemodel->update_document_type($type_code,$data);

                                if($inactive['success']){

                                    $data = [
                                        'success' => true,
                                        'message' => $inactive['message']
                                    ];
                                }else{
                                    throw new \Exception("Delete Error: " . $inactive['message']);
                                }
                                 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error deactivating Document Type. ',
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

        if(!in_array('3', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('3', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $type_code = $this->request->getPost('type_code');
                            $get_doctype = $this->documenttypemodel->get_doc_type($type_code, $status = "Inactive");

                            if($get_doctype){

                                $data = [
                                    'status' => 'Active',
                                ];

                                $inactive = $this->documenttypemodel->update_document_type($type_code,$data);

                                if($inactive['success']){

                                    $data = [
                                        'success' => true,
                                        'message' => $inactive['message']
                                    ];
                                }else{
                                    throw new \Exception("Delete Error: " . $inactive['message']);
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

    public function get_document_type(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if(!session()->get('user_level') == '-1'){
            return redirect()->to(base_url('/'));
        }

        $admin_menu = explode(',', session()->get('admin_menu'));

        if(!in_array('3', $admin_menu)){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    if(session()->get('user_level') == "-1" && in_array('3', $admin_menu)){

                        try {

                            $logged_user = $this->session->get('logged_user');
                            $getuser = $this->usermodel->getUser($logged_user);
                            
                            $type_code = $this->request->getPost('type_code');
                            $get_doctype = $this->documenttypemodel->get_doc_type($type_code);

                            if($get_doctype){

                                $data = [
                                    'success' => true,
                                    'data' => [
                                        'type_code' => $get_doctype['type_code'],
                                        'type_desc' => $get_doctype['type_desc'],
                                    ],
                                    
                                ]; 
                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error fetching Document Type Details.'
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