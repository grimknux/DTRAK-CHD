<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\ActionModel;


class IncomingFwdRet extends BaseController
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


    function getForwardData(){

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
                        $status = "R";
                        $logged_user = $this->session->get("logged_user");
                        $getuser = $this->UserModel->getUser($logged_user,$docdetail,"incoming");
                       
                        $data = array();

                        if($getuser['success']){
                            $user = $custom->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']);

                                $receiveData = $this->IncomingModel->receiveData($docdetail,$status);
                                $actiontaken = $this->actionmodel->getActionDone();
                                $getOffice = $this->OfficeModel->getOfficeExceptCurrent($getOfficeCode);
                                $getActionRequired = $this->actionmodel->get_action_required_active();


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


    public function forwardDocument(){
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
                            'fwd_destination' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Forward Destination Office!',
                                ],
                            ],
                            'fwd_destemp' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Forward Destination Employee!',
                                ],
                            ],
                            'fwd_actionrequire' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Required!',
                                ],
                            ],
                            
                        ];

                        if($this->validate($rules))
                        {

                            $doc_detailno = $this->request->getPost('fwd_detailno');
                            $officedestination = $this->request->getPost('fwd_destination');
                            $empdestination = $this->request->getPost('fwd_destemp');
                            $actionrequire = $this->request->getPost('fwd_actionrequire');
                            $remarks = $this->request->getPost('fwd_fwdremarks');
                        
                            $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");

                            if($getuser['success']){
                                
                                $office = $this->UserModel->getUserOffice($logged_user);
                                $officecode = array_column($office, 'officecode');
                                $status = "R";

                                if($this->documentdetailmodel->checkDocIfValid($officecode,$doc_detailno)){

                                    $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                                    if($receiveData){

                                        $data = [

                                            'status' => 'F',
                                            'action_by' => $logged_user,
                                            'action_code' => '00061',
                                            'date_action' => date('Y-m-d'),
                                            'time_action' => date('H:i:s'),
                                            'datelog_action' => date('Y-m-d'),
                                            'timelog_action' => date('H:i:s'),
                                            'release_by' => $logged_user,
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

                                                $data = ['success' => true, 'message' => 'Document Successfully Forwarded to '.$getOffice['officename'].'!'];
                                            }else{
                                                $data = ['success' => false, 'message' => 'Failed to insert Forward Destination Office. Manually add Destination in Released Documents Page.'];
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
                                    'fwd_destination' => $this->validation->getError('fwd_destination'),
                                    'fwd_destemp' => $this->validation->getError('fwd_destemp'),
                                    'fwd_actionrequire' => $this->validation->getError('fwd_actionrequire'),
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



    function getReturnData(){

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
                        $status = "R";
                        $logged_user = $this->session->get("logged_user");
                        $getuser = $this->UserModel->getUser($logged_user,$docdetail,"incoming");
                       
                        $data = array();

                        if($getuser['success']){
                            $user = $custom->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']);

                                $receiveData = $this->IncomingModel->receiveData($docdetail,$status);
                                $actiontaken = $this->actionmodel->getActionDone();
                                $getOffice = $this->OfficeModel->getOfficeExceptCurrent($getOfficeCode);
                                $getActionRequired = $this->actionmodel->getActionRequireReturn();

                                if(in_array($receiveData['officecode'], $getOfficeCode)){
                                    throw new \Exception('Cannot use Return function. You are the originating office!');
                                }

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
                                        'officecode' => $receiveData['officecode'],
                                        'origemp' => $custom->convertEMP($receiveData['lastname'], $receiveData['firstname'], $receiveData['middlename'], $receiveData['orep']),
                                        'exofficecode' => $receiveData['exofficecode'],
                                        'exempname' => $receiveData['exempname'],
                                        'pageno' => $receiveData['pageno'],
                                        'attachment' => $attachment,
                                        'actiondone' => $receiveData['actiondone'],
                                        'remarks' => $receiveData['remarks'],
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
                        //return $this->response->setStatusCode(500)->setBody(json_encode(['error' => 'Error occurred while retrieving the data: ' . $e->getMessage()]));

                        $data = [
                            'success' => false,
                            'message' => $e->getMessage() // Return the exception message
                        ];
                        
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


    public function returnDocument(){
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
                            'ret_destemp' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Return Destination Employee!',
                                ],
                            ],
                            'ret_actionrequire' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Required!',
                                ],
                            ],
                            
                        ];

                        if($this->validate($rules))
                        {

                            $doc_detailno = $this->request->getPost('ret_detailno');
                            $empdestination = $this->request->getPost('ret_destemp');
                            $actionrequire = $this->request->getPost('ret_actionrequire');
                            $remarks = $this->request->getPost('ret_retremarks');
                        
                            $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");

                            if($getuser['success']){
                                
                                $office = $this->UserModel->getUserOffice($logged_user);
                                $officecode = array_column($office, 'officecode');
                                $status = "R";

                                if($this->documentdetailmodel->checkDocIfValid($officecode,$doc_detailno)){

                                    $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                                    if($receiveData){

                                        $data = [

                                            'status' => 'H',
                                            'action_by' => $logged_user,
                                            'action_code' => '00061',
                                            'date_action' => date('Y-m-d'),
                                            'time_action' => date('H:i:s'),
                                            'datelog_action' => date('Y-m-d'),
                                            'timelog_action' => date('H:i:s'),
                                            'release_by' => $logged_user,
                                            'release_date' => date('Y-m-d'),
                                            'release_time' => date('H:i:s'),
                                            'remarks2' => $remarks,
                                            
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
                                                'office_destination' => $receiveData['officecode'],
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

                                                $getOffice = $this->OfficeModel->getOfficeDataById($receiveData['officecode']);

                                                $data = ['success' => true, 'message' => 'Document Successfully Returned to '.$getOffice['officename'].'!'];
                                            }else{
                                                $data = ['success' => false, 'message' => 'Failed to insert Forward Destination Office. Manually add Destination in Released Documents Page.'];
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
                                    'ret_destemp' => $this->validation->getError('ret_destemp'),
                                    'ret_actionrequire' => $this->validation->getError('ret_actionrequire'),
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


}
 