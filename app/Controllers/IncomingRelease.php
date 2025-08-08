<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\DocumentRegistryModel;
use App\Models\DocumentControlModel;
use App\Models\ActionModel;
use Config\Database;


class IncomingRelease extends BaseController
{
    public $validation;
    public $customobj;
    public $IncomingModel;
    public $UserModel;
    public $OfficeModel;
    public $DocumentDetailModel;
    public $documentregistrymodel;
    public $DocumentControlModel;
    public $actionmodel;

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
        $this->documentcontrolmodel = new DocumentControlModel();
        $this->actionmodel = new ActionModel();
        $this->db = Database::connect();
        
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
        
        $navi_bread = "<li>Incoming</li>
                       <li>Receiving and Releasing</li>
                       <li>For Release</li>";

        $getOfficeCode = $this->IncomingModel->getOfficeCode(session()->get('logged_user'));
        $getOffice = $this->OfficeModel->getOfficeExceptCurrent($getOfficeCode);
        $getActionRequired = $this->actionmodel->getActionRequired()['data'];

        $data = [
            'header' => 'Receiving and Releasing',
            'navactive' => 'incoming',
            'navsubactive' => 'receiveaction',
            'rnav' => 'release',
            'bread' => $navi_bread,
            'officeDestinations' => $getOffice,
            'actionReq' => $getActionRequired,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('document-release', $data);
    }

    public function forrelease(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url());
        }

        if ($this->request->isAJAX()) {

            if($this->request->getMethod() == 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $data = [];
                        $getOfficeCode = $this->IncomingModel->getOfficeCode(session()->get('logged_user'));
                        $status = ['T'];
                        $incomingQuery = $this->IncomingModel->incomingQuery($getOfficeCode,$status);

                        if($incomingQuery){
                            
                            foreach ($incomingQuery as $row) {

                                if(!empty($row['date_rcv'])){
                                    $datercv = date('M d Y',strtotime($row['date_rcv']))."<br>".date('h:i:s a', strtotime($row['time_rcv']));
                                }else{
                                    $datercv = "";
                                }

                                if(!empty($row['attachfile']) || $row['attachfile'] !== "" ){
                                    $attachment = "<a href='".base_url().'doctoreceive/receive/viewfile/'.$row['attachfile']."' target='_blank'><em>View Attachment</em></a>";
                                }else{
                                    $attachment = "N/A";
                                }
            
                                $docdetail = $row['ddetail'];
                                
                                $actBtn = "<li><a href='javascript:void(0)' class='done-rel enable-tooltip' data-docdetail='".$docdetail."' title='Tag as Done!'><i class='fa fa-list-alt fa-fw pull-right'></i>Tag as Done!</a></li>";

                                $dissBtn = "<li><a href='javascript:void(0)' class='diss-modal enable-tooltip' data-docdetail='".$docdetail."' data-toggle='modal' title='Disseminate Document!'><i class='fa fa-share pull-right'></i></i>Disseminate Document</a></li>";

                                $btn = "<div class='btn-group'>
                                            <a class='btn btn-xs btn-primary rel-modal' data-docdetail='".$docdetail."' data-toggle='modal' title='Release Document!'><i class='gi gi-disk_export'></i> Release</a>
                                            <a href='javascript:void(0)' data-toggle='dropdown' class='btn btn-default btn-xs dropdown-toggle'><span class='caret'></span></a>
                                            <ul class='dropdown-menu dropdown-menu-right text-left'>
                                                <li class='dropdown-header'>
                                                    <i class='fa fa-user pull-right'></i> <strong>OTHER ACTIONS</strong>
                                                </li>
                                                ".$actBtn."
                                                ".$dissBtn."
                                            </ul>
                                        </div>";

                                $data[] = [
                                    'controlno' => $row['dcon'],
                                    'originating' => $row['origoffice'],
                                    'previous' => $row['prevoffice'],
                                    'subject' => "<strong>".htmlspecialchars($row['subject'])."</strong> <br><br> Action Required: <strong>".$row['reqaction']."</strong> <br> Attachment: <strong>".$attachment."</strong>",
                                    'remarks' => $row['drRem'],
                                    'attachment' => $attachment,
                                    'doctype' => str_replace(",", ", ", $row['ddoctype']),
                                    'actionrequire' => $row['reqaction'],
                                    'datelog' => date('F d, Y', strtotime($row['date_rcv'])) . "<br>" . $row['time_rcv'],
                                    'docdetail' => $row['ddetail'],
                                    'actioncode' => $row['actioncode'],
                                    'actiondesc' => $row['actiondesc'],
                                    'btnaction' => $btn,
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

    function getReleaseData(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{
                        $docdetail = $this->request->getVar('id');
                        $getOfficeCode = $this->IncomingModel->getOfficeCode(session()->get('logged_user'));
                        $status = "T";
                        $logged_user = $this->session->get("logged_user");
                        $getuser = $this->UserModel->getUser($logged_user,$docdetail,"incoming");
                       
                        $data = array();

                        if($getuser['success']){
                            $user = $custom->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']);

                                $receiveData = $this->IncomingModel->receiveData($docdetail,$status);
                                $actiontaken = $this->actionmodel->getActionDone();
                                $getOffice = $this->OfficeModel->getOfficeExceptCurrent($getOfficeCode);
                                $getActionRequired = $this->actionmodel->getActionRequired()['data'];


                                if($receiveData){

                                    if(!empty($receiveData['attachment']) || $receiveData['attachment'] !== "" ){
                                        $attachment = "<a href='".base_url().'doctoreceive/receive/viewfile/'.$receiveData['attachment']."' target='_blank'><em>View Attachment</em></a>";
                                    }else{
                                        $attachment = "N/A";
                                    }

                                    $data = [
                                        'routeno' => $receiveData['routeno'],
                                        'controlno' => $receiveData['dcon'],
                                        'detailno' => $receiveData['ddetail'],
                                        'subject' => $receiveData['subject'],
                                        'doctype' => $receiveData['ddoctype'],
                                        'origoffice' => $receiveData['origoffice'],
                                        'prevoffice' => $receiveData['prevoffice'],
                                        'origemp' => $custom->convertEMP($receiveData['lastname'], $receiveData['firstname'], $receiveData['middlename'], $receiveData['orep']),
                                        'exofficecode' => $receiveData['exofficecode'],
                                        'exempname' => $receiveData['exempname'],
                                        'pageno' => $receiveData['pageno'],
                                        'attachment' => $attachment,
                                        'actiondone' => $receiveData['actiondone'],
                                        'remarks' => $receiveData['remarks'],
                                        'officelist' => $getOffice,
                                        'actionrequirelist' => $getActionRequired,
                                        'daterec' => date('Y-m-d'),
                                        'timerec' => date('H:i:s'),
                                        'forwardby' => $user,
                                    ];
                                    
                                    $data['success'] = true;
                                }else{

                                    $data['success'] = false;
                                    $data['message'] = "Error retrieving data. Action has already been taken on this document!";
                                    $data['reload'] = true;

                                }

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


    public function releaseDocument(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {

                        $logged_user = $this->session->get('logged_user');

                        $rules = [
                            'rel_destination' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Release Destination Office!',
                                ],
                            ],
                            'rel_destemp' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Release Destination Employee!',
                                ],
                            ],
                            'rel_actionrequire' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Required!',
                                ],
                            ],
                            
                        ];

                        if($this->validate($rules))
                        {

                            $doc_detailno = $this->request->getPost('rel_detailno');
                            $officedestination = $this->request->getPost('rel_destination');
                            $empdestination = $this->request->getPost('rel_destemp');
                            $actionrequire = $this->request->getPost('rel_actionrequire');
                            $remarks = $this->request->getPost('rel_relremarks');
                        
                            $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");

                            if($getuser['success']){
                                
                                $office = $this->UserModel->getUserOffice($logged_user);
                                $officecode = array_column($office, 'officecode');
                                $status = "T";

                                if($this->documentdetailmodel->checkDocIfValid($officecode,$doc_detailno)){

                                    $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                                    if($receiveData){

                                        $data = [

                                            'status' => 'O',
                                            'release_by' => $logged_user,
                                            'remarks2' => $remarks,
                                            'release_date' => date('Y-m-d'),
                                            'release_time' => date('H:i:s'),
                                            
                                        ];

                                        $updateStatus = $this->documentdetailmodel->updateStatus($doc_detailno, $data);

                                        if($updateStatus['success']){
                                            
                                            $generateDocumentDetailNo = $this->documentdetailmodel->generateDocumentDetailNo();

                                            $insertdata = [
                                                'doc_detailno' => $generateDocumentDetailNo,
                                                'route_no' => $receiveData['routeno'],
                                                'doc_controlno' => $receiveData['dcon'],
                                                'sequence_no' => $receiveData['seqno'] + 1,
                                                'prev_sequence_no' => $receiveData['seqno'],
                                                'office_destination' => $officedestination,
                                                'action_officer' => $empdestination,
                                                'action_required' => $actionrequire,
                                                'entry_by' => $this->customobj->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']),
                                                'emp_entry' => $logged_user,
                                                'no_page' => $receiveData['pageno'],
                                                'remarks' => $remarks,
                                                'date_log' => date('Y-m-d'),
                                                'time_log' => date('H:i:s'),
                                                'modified_by' => $logged_user,
                                                
                                            ];

                                            $insertDocumentDetail = $this->documentdetailmodel->insertDocumentDetail($insertdata);

                                            if ($insertDocumentDetail['success']) {

                                                $getOffice = $this->OfficeModel->getOfficeDataById($officedestination);

                                                $data = ['success' => true, 'message' => 'Document Successfully Release to '.$getOffice['officename'].'!'];
                                            }else{
                                                $data = ['success' => false, 'message' => 'Failed to insert Release Destination Office. Manually add Destination in Released Documents Page.'];
                                            }
                                            
                                        }else{
                                            $data = ['success' => true, 'message' => $updateStatus['message']];
                                        }
                                        
                                    }else{
                                        $data = ['success' => false, 'message' => 'Error retrieving data. Action has already been taken on this document!', 'reload' => true];
                                    }

                                }else{

                                    $data = ['success' => false, 'message' => 'You are not allowed to access this Document!'];

                                }

                            }else{

                                $data['success'] = false;
                                $data['message'] = $getuser['message'];
                            }

                        } else {   
                            
                            $data = [
                                'success' => false,
                                'formnotvalid' => true,
                                'data' => [
                                    'rel_destination' => $this->validation->getError('rel_destination'),
                                    'rel_destemp' => $this->validation->getError('rel_destemp'),
                                    'rel_actionrequire' => $this->validation->getError('rel_actionrequire'),
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


    public function releaseBulkDocument(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $logged_user = $this->session->get('logged_user');

                        $rules = [
                            'bulkrel_officedestination' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Office Destination!',
                                ],
                            ],
                            'bulkrel_actionofficer' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Officer!',
                                ],
                            ],
                            'bulkrel_actionrequire' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Required!',
                                ],
                            ],
                            
                        ];

                        if($this->validate($rules))
                        {
                            $detailno = $this->request->getPost('detailno');
                            $docinfo = json_decode($detailno, true);

                            $office = $this->UserModel->getUserOffice($logged_user);
                            $officecode = array_column($office, 'officecode');

                            if(empty($detailno)){

                                $data = [
                                    'success' => false,
                                    'message' => 'No document IDs provided for release.',
                                ];

                            }else{

                                $status = "T";
                                $checkinfo = true;
                                $controlId = [];
    
                                $inputdata = [
                                    'officedestination' => $this->request->getPost('bulkrel_officedestination'),
                                    'actionofficer' => $this->request->getPost('bulkrel_actionofficer'),
                                    'actionrequire' => $this->request->getPost('bulkrel_actionrequire'),
                                    'remarks' => "",
    
                                ];
    
                                foreach ($docinfo as $doc) {
    
                                    $receiveData = $this->IncomingModel->receiveData($doc['rowId'],$status);
    
                                    if (!$receiveData) {
                                        $controlId[] = $doc['controlId'];
                                        $checkinfo = false;
                                    }
    
                                }
    
                                if($checkinfo){

                                    $models = [
                                        'customobj' => $this->customobj,
                                        'IncomingModel' => $this->IncomingModel,
                                        'UserModel' => $this->UserModel,
                                    ];
    
                                    $releaseBulk = $this->documentdetailmodel->releaseBulk($docinfo, $inputdata, $officecode, $logged_user, $models);
    
                                    if($releaseBulk['success']){

                                        $data = ['success' => true, 'message' => $releaseBulk['message'], 'data' => $releaseBulk['data']];

                                    }else{
                                        $data = ['success' => false, 'message' => 'Error releasing document. ' .$releaseBulk['message'],  'reload' => true];
                                    }
    
                                }else{
                                    $data = ['success' => false, 'message' => 'Error retrieving data. The following document has already been released: ' . implode(', ', $controlId),  'reload' => true];
                                }
                                
                            }


                           

                            

                        }else{

                            $data = [
                                'success' => false,
                                'formnotvalid' => true,
                                'data' => [
                                    'bulkrel_officedestination' => $this->validation->getError('bulkrel_officedestination'),
                                    'bulkrel_actionofficer' => $this->validation->getError('bulkrel_actionofficer'),
                                    'bulkrel_actionrequire' => $this->validation->getError('bulkrel_actionrequire'),
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



    public function releaseBulkData(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $logged_user = $this->session->get('logged_user');
                        $getOfficeCode = $this->IncomingModel->getOfficeCode(session()->get('logged_user'));

                        $getOffice = $this->OfficeModel->getOfficeExceptCurrent($getOfficeCode);
                        $getActionRequired = $this->actionmodel->getActionRequired()['data'];
                   
                        if($getOffice && $getActionRequired){
                            $data = [
                                'success' => true,
                                'officedestination' => $getOffice,
                                'actionrequirelist' => $getActionRequired,
    
                            ];
                        }else{
                            $data = [
                                'success' => false,
                                'message' => "Failed to get Destination Data."
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


    function getDisseminateData(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{
                        $docdetail = $this->request->getVar('id');
                        $getOfficeCode = $this->IncomingModel->getOfficeCode(session()->get('logged_user'));
                        $status = "T";
                        $logged_user = $this->session->get("logged_user");
                        $getuser = $this->UserModel->getUser($logged_user,$docdetail,"incoming");
                       
                        $data = array();

                        if($getuser['success']){
                            $user = $custom->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']);

                                $receiveData = $this->IncomingModel->receiveData($docdetail,$status);
                                $actiontaken = $this->actionmodel->getActionDone();
                                $getOffice = $this->OfficeModel->getOfficeExceptCurrent($getOfficeCode);
                                $getActionRequired = $this->actionmodel->getActionRequired()['data'];


                                if($receiveData){

                                    if(!empty($receiveData['attachment']) || $receiveData['attachment'] !== "" ){
                                        $attachment = "<a href='".base_url().'doctoreceive/receive/viewfile/'.$receiveData['attachment']."' target='_blank'><em>View Attachment</em></a>";
                                    }else{
                                        $attachment = "N/A";
                                    }

                                    $data = [
                                        'routeno' => $receiveData['routeno'],
                                        'controlno' => $receiveData['dcon'],
                                        'detailno' => $receiveData['ddetail'],
                                        'subject' => $receiveData['subject'],
                                        'doctype' => $receiveData['ddoctype'],
                                        'origoffice' => $receiveData['origoffice'],
                                        'prevoffice' => $receiveData['prevoffice'],
                                        'origemp' => $custom->convertEMP($receiveData['lastname'], $receiveData['firstname'], $receiveData['middlename'], $receiveData['orep']),
                                        'exofficecode' => $receiveData['exofficecode'],
                                        'exempname' => $receiveData['exempname'],
                                        'pageno' => $receiveData['pageno'],
                                        'attachment' => $attachment,
                                        'actiondone' => $receiveData['actiondone'],
                                        'remarks' => $receiveData['remarks'],
                                        'officelist' => $getOffice,
                                        'actionrequirelist' => $getActionRequired,
                                        'daterec' => date('Y-m-d'),
                                        'timerec' => date('H:i:s'),
                                        'forwardby' => $user,
                                    ];
                                    
                                    $data['success'] = true;
                                }else{

                                    $data['success'] = false;
                                    $data['message'] = "Error retrieving data. Action has already been taken on this document!";
                                    $data['reload'] = true;

                                }

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


    public function addDocumentDissemination(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    $this->db->transStart();

                    try {

                        $logged_user = $this->session->get('logged_user');
                        $getuser = $this->UserModel->getUser($logged_user)['data'];
                        $status = "T";
                        
                        $rules = [
                            'diss_office_destination.*' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Office Destination!',
                                ],
                            ],
                            'diss_action_officer.*' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Officer!',
                                ],
                            ],
                            'diss_action_required.*' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Required!',
                                ],
                            ],

                        ];
                        
                        if($this->validate($rules))
                        {

                            $routeno = $this->request->getPost('diss_routeno');
                            $docdetail = $this->request->getPost('diss_detailno');

                            $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                            $getuser = $this->UserModel->getUser($logged_user,$docdetail,"incoming");

                            if($getuser['success']){

                                $officeDestinations = $this->request->getPost('diss_office_destination');
                                $actionOfficers = $this->request->getPost('diss_action_officer');
                                $actionRequireds = $this->request->getPost('diss_action_required');
                                $remarks = $this->request->getPost('diss_remarks');
                                $remarks2 = 'Document Disseminated!';
                                $ds = true;
                                
                                $getDocumentData = $this->documentregistrymodel->getDocumentData($routeno);
                                $receiveData = $this->IncomingModel->receiveData($docdetail,$status);
                                $errorArray = [];

                                    if($receiveData){

                                        $tagAsDone = $this->tagAsDone($docdetail,$remarks2,$ds);

                                        if(!$tagAsDone['success']){
                                            throw new \Exception('Failed to disseminate document.');
                                        }

                                        $createDisseminateDocument = $this->createDisseminateDocument($routeno,$receiveData['dcon'],$officecode,$remarks);

                                        if($createDisseminateDocument['success']){

                                            foreach ($officeDestinations as $index => $officeDestination) {

                                                $generateDocumentControlNo = $this->documentcontrolmodel->generateDocumentControlNo();
                                                $generateDocumentDetailNo = $this->documentdetailmodel->generateDocumentDetailNo();
    
                                                $getDocumentDataForInsert = $this->documentregistrymodel->getDocumentData($createDisseminateDocument['routeno']);

                                                if(!$getDocumentDataForInsert){
                                                    throw new \Exception('Failed to dessiminate data error fetching document data.');
                                                }

                                                $insertdata = [
                                                    'doc_detailno' => $generateDocumentDetailNo,
                                                    'route_no' => $createDisseminateDocument['routeno'],
                                                    'doc_controlno' => $generateDocumentControlNo,
                                                    'sequence_no' => 1,
                                                    'office_destination' => $officeDestination,
                                                    'action_officer' => $actionOfficers[$index],
                                                    'action_required' => $actionRequireds[$index],
                                                    'entry_by' => $this->customobj->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']),
                                                    'emp_entry' => $logged_user,
                                                    'no_page' => $getDocumentDataForInsert['no_page'],
                                                    'remarks' => $remarks,
                                                    'date_log' => date('Y-m-d'),
                                                    'time_log' => date('H:i:s'),
                                                    'modified_by' => $logged_user,
                                                    
                                                ];
    
                                                $insertDocumentDetail = $this->documentcontrolmodel->insertDocumentControl($insertdata);
    
                                                if (!$insertDocumentDetail['success']) {
                                                    $getOfficeDataById = $this->OfficeModel->getOfficeDataById($officeDestination);
                                                    throw new \Exception('Failed to dessiminate data: ' .$getOfficeDataById['officename'] . ". " . $insertDocumentDetail['message']);

                                                }
                                            }

                                            $data = [
                                                'success' => true,
                                                'message' => 'Successfully Disseminated Document!',
                                                'rn' => $createDisseminateDocument['routeno']
                                            ];

                                        }else{

                                            throw new \Exception('Failed to dessiminate data: ' . $createDisseminateDocument['message']);
                                        
                                        }

                                    }else{

                                        $data['success'] = false;
                                        $data['message'] = "Error retrieving data. Action has already been taken on this document!";
                                        $data['reload'] = true;
    
                                    }

                            }else{

                                $data['success'] = false;
                                $data['message'] = 'You are not authorize to Disseminate this Document.';
                            }
        
                        } else {   
                            $errors = [
                                'diss_office_destination' => [],
                                'action_officer' => [],
                                'action_required' => []
                            ];
                            
                            $officeDestinations = $this->request->getVar('diss_office_destination');
                            foreach ($officeDestinations as $key => $value) {
                                if ($this->validation->hasError("diss_office_destination.$key")) {
                                    $errors['diss_office_destination'][$key] = $this->validation->getError("diss_office_destination.$key");
                                }
                            }

                            $actionOfficers = $this->request->getVar('diss_action_officer');
                            foreach ($actionOfficers as $key => $value) {
                                if ($this->validation->hasError("diss_action_officer.$key")) {
                                    $errors['diss_action_officer'][$key] = $this->validation->getError("diss_action_officer.$key");
                                }
                            }

                            $actionRequireds = $this->request->getVar('diss_action_required');
                            foreach ($actionRequireds as $key => $value) {
                                if ($this->validation->hasError("diss_action_required.$key")) {
                                    $errors['diss_action_required'][$key] = $this->validation->getError("diss_action_required.$key");
                                }
                            }
                            
                            $data = [
                                'success' => false,
                                'formnotvalid' => true,
                                'data' => $errors,
                            ];
                            
                        }

                        $this->db->transComplete();

                        if ($this->db->transStatus() === false) {
                            throw new \Exception('Failed to disseminate document.');
                        }

                        return $this->response->setJSON($data);

                    } catch (\Exception $e) {

                        $this->db->transRollback();
                        log_message('error', 'Error occurred while disseminating data: ' . $e->getMessage());

                        $data = [
                            'success' => false,
                            'message' => $e->getMessage() // Return the exception message
                        ];

                        return $this->response->setJSON($data);
                        
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


    function getTagData(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{
                        $docdetail = $this->request->getVar('id');
                        $status = "T";
                        $logged_user = $this->session->get("logged_user");
                        $getuser = $this->UserModel->getUser($logged_user,$docdetail,"incoming");
                       
                        $data = array();

                        if($getuser['success']){
                            $user = $custom->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']);

                                $receiveData = $this->IncomingModel->receiveData($docdetail,$status);
                                $actiontaken = $this->actionmodel->getActionDone();
                                

                                if($receiveData){

                                    $data = [
                                        'routeno' => $receiveData['routeno'],
                                        'controlno' => $receiveData['dcon'],
                                        'detailno' => $receiveData['ddetail'],
                                        'subject' => $receiveData['subject'],
                                        'doctype' => $receiveData['ddoctype'],
                                    ];
                                    
                                    $data['success'] = true;
                                }else{

                                    $data['success'] = false;
                                    $data['message'] = "Error retrieving data. Action has already been taken on this document!";
                                    $data['reload'] = true;

                                }

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


    public function tagdoneDocument(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {

                        $logged_user = $this->session->get('logged_user');
                        $doc_detailno = $this->request->getPost('detailno');
                        $remarksdone = $this->request->getPost('tag_remarks');
                        $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");

                        if($getuser['success']){
                            
                            $office = $this->UserModel->getUserOffice($logged_user);
                            $officecode = array_column($office, 'officecode');
                            $status = "R";

                            if($this->documentdetailmodel->checkDocIfValid($officecode,$doc_detailno)){

                                $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                                if($receiveData){
                                    $data = [

                                        'status' => 'I',
                                        'remarks2' => $remarksdone,
                                        
                                    ];

                                    if($filedes){
                                        $data['remarks2'] = $this->request->getPost('relremarks');
                                        $data['status'] = 'I';
                                    }

                                    $updateStatus = $this->documentdetailmodel->updateStatus($doc_detailno, $data);

                                    if($updateStatus['success']){
                                        $data = ['success' => true, 'message' => 'Action Done!'];
                                    }else{
                                        $data = ['success' => false, 'message' => $updateStatus['message']];
                                    }

                                
                                    //$data = ['success' => true, 'message' => $data];
                                    
                                }else{
                                    $data = ['success' => false, 'message' => 'Error retrieving data. Action has already been taken on this document!', 'reload' => true];
                                }

                            }else{

                                $data = ['success' => false, 'message' => 'You are not allowed to access this Document!'];

                            }

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

        }else{

            log_message('error', 'An error occurred in forreceive(): Invalid Ajax Request.');
            return $this->response->setStatusCode(400)->setBody(json_encode(['error' => 'Invalid Ajax Request']));

        }

    }


    private function createDisseminateDocument($routeno,$doccontrolno,$officecode,$remarks){
        
        $getDocumentData = $this->documentregistrymodel->getDocumentData($routeno);
        $type = 'DS';
        $generateDocumentRegistryNo = $this->documentregistrymodel->generateDocumentRegistryNo($type);
        $logged_user = $this->session->get('logged_user');

        $doctype = explode(",", $getDocumentData['ddoctype']);

        $documentdata = [
            'route_no' => $generateDocumentRegistryNo,
            'office_controlno' => $getDocumentData['office_controlno'],
            'ref_office_controlno' => $doccontrolno,
            'subject' => $getDocumentData['subject'],
            'empcode' =>  $logged_user,
            'no_page' => $getDocumentData['no_page'],
            'officecode' => $officecode,
            'doctype' => $doctype,
            'userid' =>  $logged_user,
            'sourcetype' => $getDocumentData['sourcetype'],
            'exdoc_controlno' => $getDocumentData['exdoc_controlno'],
            'exofficecode' => $getDocumentData['exofficecode'],
            'exempname' => $getDocumentData['exempname'],
            'datelog' => date('Y-m-d'),
            'timelog' => date('H:i:s'),
            'filename' =>  $getDocumentData['filename'],
            'attachlist' => $getDocumentData['attachlist'],
            'remarks' => $remarks,
            'last_modified_by' =>  $logged_user,
        ];


        $insertNewDocument = $this->documentregistrymodel->insertNewDocument($documentdata);


        if($insertNewDocument['success']){
            $data = [
                'success' => true,
                'routeno' => $generateDocumentRegistryNo,
            ];

        }else{

            $data = [
                'success' => false,
                'message' => $insertNewDocument['message'],
            ];
        }


        return $data;

    }


    private function tagAsDone($docdetail,$remarks,$ds = false){

        $logged_user = $this->session->get('logged_user');

        $data = [
            'status' => 'I',
            'remarks2' => $remarks,
        ];
        
        if($ds){
            $data['ifdisseminate'] = 'Y';  
        }

        $updateStatus = $this->documentdetailmodel->updateStatus($docdetail, $data);

        if($updateStatus['success']){
            $data = [
                'success' => true,
            ];

        }else{

            $data = [
                'success' => false,
                'message' => $updateStatus['message'],
            ];
        }


        return $data;
    }



}
 