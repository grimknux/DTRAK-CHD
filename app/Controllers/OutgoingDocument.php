<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\DocumentRegistryModel;
use App\Models\DocumentTypeModel;
use App\Models\AuditTrailModel;


class OutgoingDocument extends BaseController
{

    public $validation;
    public $customobj;
    public $IncomingModel;
    public $UserModel;
    public $OfficeModel;
    public $DocumentDetailModel;
    public $DocumentTypeModel;
    public $AuditTrailModel;

    public $session;

    public function __construct()

    {

        $this->validation = \Config\Services::validation();
        $this->customobj = new CustomObj();
        $this->IncomingModel = new IncomingModel();
        $this->UserModel = new UserModel();
        $this->OfficeModel = new OfficeModel();
        $this->documentdetailmodel = new DocumentDetailModel();
        $this->documentregistrymodel = new DocumentRegistryModel();
        $this->documenttypemodel = new DocumentTypeModel();
        $this->audittrailmodel = new AuditTrailModel();
        
        $this->session = session();
        helper(['form','html','cookie','array', 'test', 'url']);
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
        }
        
        $navi_bread = "<li>Outgoing</li>
        <li>Originating and Outgoing</li>";


        $data = [
            'header' => '<i class="fa fa fa-send"></i> Document Outgoing (Outbox)',
            'navactive' => 'outgoing',
            'navsubactive' => 'outgoaction',
            'code' => $this->documentregistrymodel->generateDocumentRegistryNo(),
            'bread' => $navi_bread,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
            //'rnav' => 'receive',
        ];

        return view('document-outgoing', $data);
    }


    public function foroutgoing(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url());
        }

        if ($this->request->isAJAX()) {

            if($this->request->getMethod() == 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $data = [];
                        $getOfficeCode = $this->OfficeModel->getOfficeCode(session()->get('logged_user'));
                        $OutgoingQuery = $this->documentregistrymodel->OutgoingQuery($getOfficeCode);
                        

                        if($OutgoingQuery){
                            foreach ($OutgoingQuery as $row) {

                                $attachment = "";
                                $checkIfReceived = $this->documentdetailmodel->checkIfReceived($row['route_no']);
                                $checkIfDestinationExists = $this->documentdetailmodel->checkIfDestExists($row['route_no']);

                                if(!empty($row['filename']) || $row['filename'] !== "" ){
                                    $attachment = "<a href='".base_url().'docview/outgoing/viewfile/'.$row['filename']."' target='_blank'><div class='media-items-content'><i class='fa fa-file-pdf-o fa-2x text-danger'></i></div>View</a>";
                                }else{
                                    $attachment = "No<br>Attachment";
                                }

                                $edit_btn = "";
                                $attach_btn = "";
                                $delete_btn = "";

                                if($checkIfDestinationExists){
                                    $delete_btn = "<a class='btn btn-xs btn-danger delete-doc enable-tooltip' data-id='".$row['route_no']."' title='Delete'><i class='fa fa-trash-o'></i></a>";
                                }

                                if($checkIfReceived){
                                    $edit_btn = "<a class='btn btn-xs btn-success edit-doc-modal enable-tooltip' data-id='".$row['route_no']."' data-toggle='modal' title='Edit Document'><i class='fa fa-pencil-square' style='margin-left: 5px; margin-right: 5px;'></i></a>";
                                    $attach_btn = "<a class='btn btn-xs btn-warning edit-attach-modal enable-tooltip' data-id='".$row['route_no']."' data-toggle='modal' title='Edit Attachment'><i class='fa fa-file-pdf-o' style='margin-left: 5px; margin-right: 5px;'></i></a>";
                                }else{
                                    $edit_btn = "";
                                    $attach_btn = "";
                                    $delete_btn = "";
                                }

                                $action = "
                                <a href='".base_url('docview/outgoing/destination/'.$row['route_no'])."' a class='btn btn-xs btn-primary enable-tooltip' title='Document Destination'><i class='fa fa-send'></i> Destination</a>

                                <div class='btn-group'>" 
                                    . $edit_btn . $attach_btn . $delete_btn .
                                "</div>";
            
                                $data[] = [
                                    'action' => $action,    
                                    'attachment' => $attachment,
                                    'datelog' => $row['datelog'],
                                    'routeno' => $row['route_no'],
                                    'refcontrolno' => $row['ref_office_controlno'],
                                    'subject' => "<strong>".htmlspecialchars($row['subject'])."</strong>",
                                    'ddoctype' => $row['ddoctype'],
                                    'officecode' => $row['officeshort'],
                                    'entryby' => $this->customobj->convertEMP($row['lastname'], $row['firstname'], $row['middlename'], $row['orep']),
                                    'pageno' => ($row['no_page'] == 0) ? '' : $row['no_page'],
                                    'attachlist' => $row['attachlist'],
                                    'remarks' => $row['remarks'],
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
                
            } else {
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));
            }
        }else {

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
        
        
    }


    function getOutgoingData(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{

                        $data = array();
                        $logged_user = $this->session->get("logged_user");
                        $getuser = $this->UserModel->getUser($logged_user);
                       
                        $user = $custom->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']);

                        $office = $this->UserModel->getUserOffice($logged_user);
                        $doctype = $this->documenttypemodel->getDocType();

                        $data = [
                            'success' => true,
                            'office' => $office,
                            'doctype' => $doctype,
                            'entryby' => $user,
                            
                        ];
                        
                    } catch (\Exception $e) {

                        log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the datas: ' . $e->getMessage().$getuser['message']]));
                        
                    }

                    return $this->response->setJSON($data);

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else {

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }

    }


    function editOutgoingData(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{

                        $logged_user = $this->session->get("logged_user");
                        $status = "A";
                        $doc_registryno = $this->request->getVar('id');
                        $data = array();

                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$doc_registryno);

                        if($checkOfficeIfValid){

                            $getuser = $this->UserModel->getUser($logged_user);
                            $office = $this->UserModel->getUserOffice($logged_user); 
                            $doctype = $this->documenttypemodel->getDocType();

                            $user = $custom->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']);

                            $getDocumentData = $this->documentregistrymodel->getDocumentData($doc_registryno);

                            if($getDocumentData){
                                $data = [
                                    'entryby' => $user,
                                    'routeno' => $getDocumentData['route_no'],
                                    'docregistry_id' => $getDocumentData['docregistry_id'],
                                    'doctype' => $doctype,
                                    'doctype_selected' => $getDocumentData['ddoctype'],
                                    'orig_office' => $office,
                                    'orig_office_selected' => $getDocumentData['officecode'],
                                    'officecontrolno' => $getDocumentData['office_controlno'],
                                    'docrefcontrolno' => $getDocumentData['ref_office_controlno'],
                                    'subject' => $getDocumentData['subject'],
                                    'source' => $getDocumentData['sourcetype'],
                                    'attachdocs' => $getDocumentData['attachlist'],
                                    'nopage' => $getDocumentData['no_page'],
                                    'exDocNo' => $getDocumentData['exdoc_controlno'],
                                    'exDocOffice' => $getDocumentData['exofficecode'],
                                    'exDocEmp' => $getDocumentData['exempname'],
                                    'remarks' => $getDocumentData['remarks'],
                                ];

                                $data['success'] = true;

                            }else{

                                log_message('error', 'An error occured while retrieving data!');
                                return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'An error occured while retrieving data.']));

                            }
                               
                        }else{

                            $data['success'] = false;
                            $data['message'] = 'You are not authorize to access this Document.';
                        }

                    } catch (\Exception $e) {

                        log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                        
                    }

                    return $this->response->setJSON($data);

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else {

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }

    }

    function editOutgoingAttach(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{

                        $logged_user = $this->session->get("logged_user");
                        $status = "A";
                        $doc_registryno = $this->request->getVar('id');
                        $data = array();

                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$doc_registryno);

                        if($checkOfficeIfValid){

                            $getDocumentData = $this->documentregistrymodel->getDocumentData($doc_registryno);

                            if($getDocumentData){
                                $data = [
                                    'routeno' => $getDocumentData['route_no'],
                                    'ddoctype_desc' => $getDocumentData['ddoctype_desc'],
                                    'subject' => $getDocumentData['subject'],
                                    'attachdocs' => $getDocumentData['attachlist'],
                                    'attachment' => $getDocumentData['filename'],
                                    'nopage' => $getDocumentData['no_page'],
                                ];

                                $data['success'] = true;

                            }else{

                                log_message('error', 'An error occured while retrieving data!');
                                return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'An error occured while retrieving data.']));

                            }

                        }else{

                            $data['success'] = false;
                            $data['message'] = 'You are not authorize to access this Document.';
                        }

                    } catch (\Exception $e) {

                        log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                        
                    }

                    return $this->response->setJSON($data);

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else {

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }

    }


    public function addOutgoingDocument(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {

                        $logged_user = $this->session->get('logged_user');
                        $getuser = $this->UserModel->getUser($logged_user);
                        
                        $rules = [
                            'doc_type' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Document Type!',
                                ],
                            ],
                            'office_controlno' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please enter the Document Control No.!',
                                ],
                            ],
                            'doc_subject' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please enter subject!',
                                ],
                            ],
                            'doc_source' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Source Type',
                                ],
                            ],
                            'orig_officeEx' => [
                                'rules' => 'requiredIfExternal[doc_source]',
                                'errors' => [
                                    'requiredIfExternal' => 'Please enter External Originating Office',
                                ],
                            ],
                            'orig_office' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Unit of Measure',
                                ],
                            ],
                            'doc_attachment' => [
                                'rules' => 'uploaded[doc_attachment]|max_size[doc_attachment,10240]|ext_in[doc_attachment,pdf]',
                                'errors' => [
                                    'uploaded' => 'Please upload a file.',
                                    'max_size' => 'The file size must not exceed 10 MB.',
                                    'ext_in' => 'The file type must be pdf only.',
                                ],
                            ],

                        ];
                        
                        if($this->validate($rules))
                        {

                            $generateDocumentRegistryNo = $this->documentregistrymodel->generateDocumentRegistryNo();


                            $attachment = 'ATTACH-'.$generateDocumentRegistryNo.'.pdf';

                            $file = $this->request->getFile('doc_attachment');

                                if (is_dir('Z:')) {
                                    $destinationPath = 'Z:/';

                                    if ($file->isValid() && !$file->hasMoved()) {
                                        $file->move($destinationPath, $attachment);

                                        $docref_controlno = $this->request->getPost('docref_controlno'); 

                                        if (!empty($docref_controlno)) {
                                            
                                            $concatDocRef = implode(',', $docref_controlno);
                                        } else {
                                            $concatDocRef = '';
                                        }

                                        $documentdata = [
                                            'route_no' => $generateDocumentRegistryNo,
                                            'office_controlno' => $this->request->getPost('office_controlno'),
                                            'ref_office_controlno' => $concatDocRef,
                                            'subject' => $this->request->getPost('doc_subject'),
                                            'empcode' =>  $logged_user,
                                            'no_page' => $this->request->getPost('doc_page'),
                                            'officecode' => $this->request->getPost('orig_office'),
                                            'doctype' => $this->request->getPost('doc_type'),
                                            'userid' =>  $logged_user,
                                            'sourcetype' => $this->request->getPost('doc_source'),
                                            'exdoc_controlno' => $this->request->getPost('orig_docnoEx'),
                                            'exofficecode' => $this->request->getPost('orig_officeEx'),
                                            'exempname' => $this->request->getPost('orig_empEx'),
                                            'datelog' => date('Y-m-d'),
                                            'timelog' => date('H:i:s'),
                                            'filename' =>  $attachment,
                                            'attachlist' => $this->request->getPost('attach_docs'),
                                            'remarks' => $this->request->getPost('doc_remarks'),
                                            'last_modified_by' =>  $logged_user,
                                        ];
            
            
                                        $insertNewDocument = $this->documentregistrymodel->insertNewDocument($documentdata);
                    
                                        if($insertNewDocument['success']){
                                            $data = [
                                                'success' => true,
                                                'message' => $insertNewDocument['message'],
                                            ];

                                        }else{

                                            unlink($destinationPath . $attachment);
                                            $data = [
                                                'success' => false,
                                                'message' => $insertNewDocument['message'],
                                            ];
                                        }

                                    } else {
                                        $data = [
                                            'success' => false,
                                            'message' => 'File upload failed.',
                                        ];
                                    }

                                } else {
                                    $data = [
                                        'success' => false,
                                        'message' => 'Network folder is not accessible.',
                                    ];
                                    
                                }

                           
        
                        } else {   
                            
                            $data = [
                                'success' => false,
                                'formnotvalid' => true,
                                'data' => [
                                    'doc_type' => $this->validation->getError('doc_type'),
                                    'office_controlno' => $this->validation->getError('office_controlno'),
                                    'doc_subject' => $this->validation->getError('doc_subject'),
                                    'doc_source' => $this->validation->getError('doc_source'),
                                    'orig_officeEx' => $this->validation->getError('orig_officeEx'),
                                    'orig_office' => $this->validation->getError('orig_office'),
                                    'doc_attachment' => $this->validation->getError('doc_attachment'),
                                ],
                            ];
                            
                        }

                    } catch (\Exception $e) {

                        log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                        
                    }

                    return $this->response->setJSON($data);

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

    public function updateOutgoingDocument(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {

                        $logged_user = $this->session->get('logged_user');
                        $getuser = $this->UserModel->getUser($logged_user);
                        
                        $rules = [
                            'orig_office_edit' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Unit of Measure',
                                ],
                            ],
                            'route_no_edit' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please enter the Document Registry Number',
                                ],
                            ],
                            'doc_type_edit' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Document Type!',
                                ],
                            ],
                            'office_controlno_edit' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please enter the Document Control No.!',
                                ],
                            ],
                            'doc_subject_edit' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please enter subject!',
                                ],
                            ],
                            'doc_source_edit' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Source Type',
                                ],
                            ],
                            'orig_officeEx_edit' => [
                                'rules' => 'requiredIfExternal[doc_source]',
                                'errors' => [
                                    'requiredIfExternal' => 'Please enter External Originating Office',
                                ],
                            ],

                        ];
                        
                        if($this->validate($rules))
                        {

                            $routeno = $this->request->getPost('route_no_edit');
                            $officecode = $this->OfficeModel->getOfficeCode($logged_user);

                            $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$routeno);

                            if($checkOfficeIfValid){

                                $getCurrentDoctype = $this->documenttypemodel->getCurrentDoctype($routeno);

                                $doctypes = array_column($getCurrentDoctype, 'type_code');
                        
                                $docref_controlno = $this->request->getPost('docref_controlno_edit'); 

                                if (!empty($docref_controlno)) {
                                    
                                    $concatDocRef = implode(',', $docref_controlno);
                                } else {
                                    $concatDocRef = '';
                                }

                                $documentdata = [
                                    'route_no' => $routeno,
                                    'officecode' => $this->request->getPost('orig_office_edit'),
                                    'office_controlno' => $this->request->getPost('office_controlno_edit'),
                                    'ref_office_controlno' => $concatDocRef,
                                    'subject' => $this->request->getPost('doc_subject_edit'),
                                    'no_page' => $this->request->getPost('doc_page_edit'),
                                    'doctype' => $this->request->getPost('doc_type_edit'),
                                    'existdoctype' => $doctypes,
                                    'sourcetype' => $this->request->getPost('doc_source_edit'),
                                    'exdoc_controlno' => $this->request->getPost('orig_docnoEx_edit'),
                                    'exofficecode' => $this->request->getPost('orig_officeEx_edit'),
                                    'exempname' => $this->request->getPost('orig_empEx_edit'),
                                    'modified_date' => date('Y-m-d H:i:s'),
                                    'attachlist' => $this->request->getPost('attach_docs_edit'),
                                    'remarks' => $this->request->getPost('doc_remarks_edit'),
                                    'last_modified_by' =>  $logged_user,
                                    'userid' =>  $logged_user,
                                ];

                                $insertNewDocument = $this->documentregistrymodel->updateDocument($documentdata);
            
                                if($insertNewDocument['success']){
                                    $data = [
                                        'success' => true,
                                        'message' => $insertNewDocument['message'],
                                    ];

                                }else{

                                    $data = [
                                        'success' => false,
                                        'message' => $insertNewDocument['message'],
                                    ];
                                }

                            }else{

                                $data['success'] = false;
                                $data['message'] = 'You are not authorize to access this Document.';
                            }

                        } else {   
                            
                            $data = [
                                'success' => false,
                                'formnotvalid' => true,
                                'data' => [
                                    'orig_office_edit' => $this->validation->getError('orig_office_edit'),
                                    'doc_type_edit' => $this->validation->getError('doc_type_edit'),
                                    'route_no_edit_edit' => $this->validation->getError('route_no_edit_edit'),
                                    'office_controlno_edit' => $this->validation->getError('office_controlno_edit'),
                                    'doc_subject_edit' => $this->validation->getError('doc_subject_edit'),
                                    'doc_source_edit' => $this->validation->getError('doc_source_edit'),
                                    'orig_officeEx_edit' => $this->validation->getError('orig_officeEx_edit'),
                                ],
                            ];
                            
                        }

                    } catch (\Exception $e) {

                        log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                        
                    }

                    return $this->response->setJSON($data);

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

    public function deleteOutgoingDocument(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url());
        }

        if ($this->request->isAJAX()) {

            if($this->request->getMethod() == 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $doc_registryno = $this->request->getPost('id');
                        $password = $this->request->getPost('password');
                        $logged_user = $this->session->get('logged_user');
                        $data = [];

                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$doc_registryno);

                        if($checkOfficeIfValid){

                            if($doc_registryno){

                                $getuser = $this->UserModel->getUser($logged_user);
                                $verifyPassword = $this->customobj->verifyPassword($password,$getuser['data']['userpass']);

                                if($verifyPassword){

                                    $deleteDocument = $this->documentregistrymodel->deleteThisDocument($doc_registryno,$logged_user);

                                    if($deleteDocument['success']){
                                        $data = [
                                            'success' => true, 
                                            'message' => $deleteDocument['message'],
                                        ];
                                    }else{
                                        $data = [
                                            'success' => false, 
                                            'message' => $deleteDocument['message'],
                                        ];
                                    }

                                }else{
                                    $data = [
                                        'success' => false,
                                        'message' => 'Error deleting. Password does not match.',
                                    ];
                                }
                                
                            } else{
                                $data = [
                                    'success' => false,
                                    'message', 'Error deleting. Invalid Document.',
                                ];
                            }
                        }else{
                            $data['success'] = false;
                            $data['message'] = 'You are not authorize to delete this Document.';
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
                
            } else {
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));
            }
        }else {

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
        
        
    }


    public function updateDocumentAttachment(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        
                        $rules = [
                            
                            'attach_attachment' => [
                                'rules' => 'uploaded[attach_attachment]|max_size[attach_attachment,10240]|ext_in[attach_attachment,pdf]',
                                'errors' => [
                                    'uploaded' => 'Please upload a file.',
                                    'max_size' => 'The file size must not exceed 10 MB.',
                                    'ext_in' => 'The file type must be pdf only.',
                                ],
                            ],

                        ];
                        
                        if($this->validate($rules)) {

                            $doc_registryno = $this->request->getPost('attach_routeno_code');
                            $logged_user = $this->session->get("logged_user");

                            $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                            $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$doc_registryno);

                            if($checkOfficeIfValid){

                                $attachment = 'ATTACH-'.$doc_registryno.'.pdf';
                                $file = $this->request->getFile('attach_attachment');
                            
                                if (is_dir('Z:')) {
                                    $destinationPath = 'Z:/';

                                    if ($file->isValid() && !$file->hasMoved()) {
                                        
                                        $documentdata = [
                                            'route_no' => $doc_registryno,
                                            'modified_date' => date('Y-m-d H:i:s'),
                                            'last_modified_by' =>  $logged_user,
                                        ];
            
                                        $updateAttachment = $this->documentregistrymodel->updateDocumentAttachment($documentdata);
                    
                                        if($updateAttachment['success']){

                                            if (file_exists($destinationPath . $attachment)) {
                                                if (!unlink($destinationPath . $attachment)) {
                                                    
                                                    return $this->response->setJSON([
                                                        'success' => false,
                                                        'message' => 'Updating of attachment error. Failed to replace existing attachment.',
                                                    ]);
                                                }
                                            }

                                            if ($file->move($destinationPath, $attachment)) {
                                                
                                                $data = [
                                                    'success' => true,
                                                    'message' => $updateAttachment['message'],
                                                ];
                                            } else {
                                                
                                                $data = [
                                                    'success' => false,
                                                    'message' => 'Updating of attachment error. Failed to move file.',
                                                ];
                                            }

                                        }else{

                                            $data = [
                                                'success' => false,
                                                'message' => $updateAttachment['message'],
                                            ];
                                        }

                                    } else {
                                        $data = [
                                            'success' => false,
                                            'message' => 'File upload failed.',
                                        ];
                                    }

                                } else {
                                    $data = [
                                        'success' => false,
                                        'message' => 'Network folder is not accessible.',
                                    ];
                                    
                                }

                            }else{

                                $data['success'] = false;
                                $data['message'] = 'You are not authorize to access this Document.';
                            }
        
                        } else {   
                            
                            $data = [
                                'success' => false,
                                'formnotvalid' => true,
                                'data' => [
                                    'attach_attachment' => $this->validation->getError('attach_attachment'),
                                ],
                            ];
                            
                        }

                    } catch (\Exception $e) {

                        log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                        
                    }

                    return $this->response->setJSON($data);

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


    public function viewFile($filename)
    {
        $logged_user = $this->session->get('logged_user');
        $filePath = 'Z:/' . $filename;

        $attachoffice = $this->documentregistrymodel->checkAttachIfValidUser($filename);
        $useroffice = $this->UserModel->getUserOffice($logged_user);



        if (file_exists($filePath)) {

            if ($attachoffice && in_array($attachoffice['officecode'], array_column($useroffice, 'officecode'))) {

                return $this->response->setHeader('Content-Type', mime_content_type($filePath))
                                    ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                                    ->setBody(file_get_contents($filePath));
            } else {
                return $this->response->setBody('You are not allowed to view this file!')->setStatusCode(403);
            }

        } else {
            return $this->response->setBody('File not found')->setStatusCode(404);
        }
    }


    public function getDoneDocs(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{

                        $logged_user = $this->session->get("logged_user");
                        $getuser = $this->UserModel->getUser($logged_user);
                       
                        $data = array();

                        if($getuser['success']){

                            $office = $this->request->getPost('office');
                            
                            if($office){
                                $data['doc_ref'] = $this->documentregistrymodel->getDoneDocument($office);
                            } else{
                                $data['doc_ref'] = [];
                            }
                            
                            $data['success'] = true;

                        }else{

                            $data['success'] = false;
                            $data['message'] = $getuser['message'];
                        }

                    } catch (\Exception $e) {

                        log_message('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));
                        
                    }

                    return $this->response->setJSON($data);

                }else{
                    
                    log_message('error', 'Invalid CSRF token. URL: ' . current_url() . ', IP: ' . $this->request->getIPAddress());
                    return $this->response->setStatusCode(403)->setBody(json_encode(['error' => 'Invalid CSRF token']));

                }
                
            }else{
                
                log_message('error', 'An error occurred in forreceive(): Method Not Allowed.');
                return $this->response->setStatusCode(405)->setBody(json_encode(['error' => 'Method not Allowed']));

            }

        }else {

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }
    }


}
 