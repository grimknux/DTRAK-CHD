<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\ActionModel;


class IncomingAction extends BaseController
{
    public $validation;
    public $customobj;
    public $IncomingModel;
    public $UserModel;
    public $OfficeModel;
    public $DocumentDetailModel;
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
        $this->actionmodel = new ActionModel();
        
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
                       <li>For Action</li>";

        $data = [
            'header' => 'Receiving and Releasing',
            'navactive' => 'incoming',
            'navsubactive' => 'receiveaction',
            'rnav' => 'action',
            'bread' => $navi_bread,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('document-action', $data);
    }

    public function foraction(){
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
                        $status = ['R'];
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

                                $getActionDone = $this->actionmodel->getActionDone();
                                
                                $actBtn = "<a type='submit' class='btn btn-xs btn-info insta-act enable-tooltip' data-did='".$docdetail."' title='Instant Action!'><i class='fa fa-bolt fa-fw'></i></a>&nbsp;<a class='btn btn-xs btn-warning act-modal ' data-docdetail='".$docdetail."' data-toggle='modal' title='Action!'><i class='fa fa-download'></i> Action</a>";
                                
                                $fwdBtn = "<li><a href='javascript:void(0)' class='fwd-modal enable-tooltip' data-docdetail='".$docdetail."' data-toggle='modal' title='Forward to other office'><i class='fa fa-location-arrow pull-right'></i> Forward Document</a></li>";

                                $retBtn = "<li><a href='javascript:void(0)' class='ret-modal enable-tooltip' data-docdetail='".$docdetail."' data-toggle='modal' title='Return to originating office'><i class='gi gi-repeat pull-right'></i> Return Document</a></li>";

                                $btn = '<div class="btn-group">'.$actBtn.$fwdBtn.$retBtn.'</div>';

                                $btn = "<div class='btn-group'>
                                            ".$actBtn."
                                            <a href='javascript:void(0)' data-toggle='dropdown' class='btn btn-default btn-xs dropdown-toggle'><span class='caret'></span></a>
                                            <ul class='dropdown-menu dropdown-menu-right text-left'>
                                                <li class='dropdown-header'>
                                                    <i class='fa fa-user pull-right'></i> <strong>OTHER ACTIONS</strong>
                                                </li>
                                                ".$fwdBtn."
                                                ".$retBtn."
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
                                    'actiondone' => $row['reqdonecode'],
                                    'listaction' => json_encode($getActionDone),
                                    'btnaction' => $btn,
                                    'btnforward' => $fwdBtn,
                                    'btnreturn' => $retBtn,
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

    function getActionData(){

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
                        $status = "R";
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
                                        'origoffice' => $receiveData['origoffice'],
                                        'prevoffice' => $receiveData['prevoffice'],
                                        'origemp' => $custom->convertEMP($receiveData['lastname'], $receiveData['firstname'], $receiveData['middlename'], $receiveData['orep']),
                                        'exofficecode' => $receiveData['exofficecode'],
                                        'exempname' => $receiveData['exempname'],
                                        'pageno' => $receiveData['pageno'],
                                        'attachment' => $receiveData['attachment'],
                                        'actiontaken' => $actiontaken,
                                        'actiondone' => $receiveData['actiondone'],
                                        'daterec' => date('Y-m-d'),
                                        'timerec' => date('H:i:s'),
                                        'actionby' => $user,
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


    public function actionDocument(){
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
                            'act_taken' => [
                                'rules' => 'required|checkActionTakenExists',
                                'errors' => [
                                    'required' => 'Please select Action Taken!',
                                    'checkActionTakenExists' => 'Invalid Action Taken'
                                ],
                            ],
                            'relremarks' => [
                                'rules' => 'required_with[filedes]',
                                'errors' => [
                                    'required_with' => 'Please enter Remarks!',
                                ],
                            ],
                            
                        ];

                        if($this->validate($rules))
                        {

                            $doc_detailno = $this->request->getPost('detailno');
                            $act_taken = $this->request->getPost('act_taken');
                            $filedes = $this->request->getPost('filedes');
                        
                            $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");

                            if($getuser['success']){
                                
                                $office = $this->UserModel->getUserOffice($logged_user);
                                $officecode = array_column($office, 'officecode');
                                $status = "R";

                                if($this->documentdetailmodel->checkDocIfValid($officecode,$doc_detailno)){

                                    $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                                    if($receiveData){
                                        $data = [

                                            'status' => 'T',
                                            'action_by' => $logged_user,
                                            'action_code' => $act_taken,
                                            'date_action' => date('Y-m-d'),
                                            'time_action' => date('H:i:s'),
                                            'datelog_action' => date('Y-m-d'),
                                            'timelog_action' => date('H:i:s'),
                                            
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

                        } else {   
                            
                            $data = [
                                'success' => false,
                                'formnotvalid' => true,
                                'data' => [
                                    'act_taken' => $this->validation->getError('act_taken'),
                                    'relremarks' => $this->validation->getError('relremarks'),
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


    public function instaActionDocument(){
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
                        
                        $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");

                        if($getuser['success']){
                            
                            $office = $this->UserModel->getUserOffice($logged_user);
                            $officecode = array_column($office, 'officecode');
                            $status = "R";

                            $checkDocIfValid = $this->documentdetailmodel->checkDocIfValid($officecode,$doc_detailno);

                            if($checkDocIfValid){

                                $actionreq = $checkDocIfValid['action_required'];

                                $getActionByRequire = $this->actionmodel->getActionByRequire($actionreq);

                                    $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                                    if($receiveData){
                                        $data = [

                                            'status' => 'T',
                                            'action_by' => $logged_user,
                                            'action_code' => $getActionByRequire['reqaction_done'],
                                            'date_action' => date('Y-m-d'),
                                            'time_action' => date('H:i:s'),
                                            'datelog_action' => date('Y-m-d'),
                                            'timelog_action' => date('H:i:s'),
                                            
                                        ];

                                        $updateStatus = $this->documentdetailmodel->updateStatus($doc_detailno, $data);

                                        if($updateStatus['success']){
                                            $data = ['success' => true, 'message' => 'Action Taken!'];
                                        }else{
                                            $data = ['success' => false, 'message' => $updateStatus['message']];
                                        }

                                }else{
                                    $data = ['success' => false, 'message' => 'Error retrieving data. Action has already been taken on this document!', 'reload' => true];
                                }
                                //$data = ['success' => true, 'message' => $getActionByRequire['reqaction_done']];

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


    public function actionBulkDocument(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $logged_user = $this->session->get('logged_user');
                        $detailno = $this->request->getPost('detailno');
                        $docinfo = json_decode($detailno, true);
                            
                        $status = "R";
                        $checkinfo = true;
                        $controlId = [];

                        foreach ($docinfo as $doc) {

                            $receiveData = $this->IncomingModel->receiveData($doc['rowId'],$status);

                            if (!$receiveData) {
                                $controlId[] = $doc['controlId'];
                                $checkinfo = false; // Set the result to false if any query fails
                                //break; // Exit the loop as we don't need to check further
                            }
                        }

                        if($checkinfo){
                            $office = $this->UserModel->getUserOffice($logged_user);
                            $officecode = array_column($office, 'officecode');

                            $data = [

                                'status' => 'T',
                                'action_by' => $logged_user,
                                'date_action' => date('Y-m-d'),
                                'time_action' => date('H:i:s'),
                                'datelog_action' => date('Y-m-d'),
                                'timelog_action' => date('H:i:s'),
                                
                            ];

                            $updateStatus = $this->documentdetailmodel->updateStatusBulk($docinfo, $data, $officecode, 'action');
                
                            if($updateStatus['success']){
                                $data = ['success' => true, 'message' => 'Document Received!'];
                            }else{
                                $data = ['success' => false, 'message' => 'error'];
                            }

                        }else{
                            $data = ['success' => false, 'message' => 'Error retrieveing data. Action has already been taken for ' . implode(', ', $controlId) . '!', 'reload' => true];
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






}
 