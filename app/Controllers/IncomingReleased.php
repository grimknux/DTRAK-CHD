<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\DocumentRegistryModel;
use App\Models\ActionModel;


class IncomingReleased extends BaseController
{
    public $validation;
    public $customobj;
    public $IncomingModel;
    public $UserModel;
    public $OfficeModel;
    public $DocumentDetailModel;
    public $documentregistrymodel;
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
            'rnav' => 'released',
            'bread' => $navi_bread,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('document-released', $data);
    }

    public function forreleased(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url());
        }

        if ($this->request->isAJAX()) {

            if($this->request->getMethod() == 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $data = [];
                        $logged_user = $this->session->get("logged_user");
                        $getOfficeCode = $this->IncomingModel->getOfficeCode($logged_user);
                        $status = ['O','F','H'];
                        $releasedQuery = $this->IncomingModel->releasedQuery($getOfficeCode,$status);

                        if($releasedQuery){
                            
                            foreach ($releasedQuery as $row) {

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
                                $pdocdetail = $row['ddpdetail'];
                                $releasedate = $row['reldate'];
                                $releasedate = $row['reltime'];


                                $getActionDone = $this->actionmodel->getActionDone();
                                $actionBtn = "";
                                $destination = "";
                                $actionreq = "";
                                $actionby = "";
                                
                                if($pdocdetail == ""){

                                    $changeDestBtn = "";
                                    $destination = "No assigned Destination. Please select <b>\"Add Destination\"</b>";
                                    $actionreq = "N/A";

                                    $actionBtn = "<a href='javascript:void(0)' class='btn btn-xs btn-danger add-desti enable-tooltip' data-docdetail='".$docdetail."' data-toggle='modal' title='Add Destination'><i class='gi gi-repeat'></i> Add</a>";
                                    
                                }else{

                                    $actionBtn = "<a class='btn btn-xs btn-warning change-desti enable-tooltip' data-destoffice='".$row['pop_officecode']."' data-docdetail='".$pdocdetail."' data-toggle='modal' title='Change Destination'><i class='fa fa-location-arrow'></i> Change</a>";
                                    
                                    $destination =  $row['pop_officename'];
                                    $actionreq = $row['destactionrequire'];
                                    $actionby = "<b style='font-size: 8px'>" .$this->customobj->convertEMP($row['rel_lastname'], $row['rel_firstname'], $row['rel_middlename'], $row['rel_office_rep']) . "</b>";
                                }
                                

                                

                                $btn = $actionBtn;

                                $timestamp = "";
                                $datelog = "N/A";
                                if(!is_null($row['reldate'])){
                                    $timestamp = $row['reldate'] . " " . $row['reltime'];
                                    $datelog = date('F d, Y', strtotime($row['reldate'])) . "<br>" . date('h:i:s a', strtotime($row['reltime']));
                                }
                                
                                $data[] = [
                                    'controlno' => $row['dcon'],
                                    'subject' => "<strong>".htmlspecialchars($row['subject'])."</strong>",
                                    'remarks' => $row['drRem'],
                                    'attachment' => $attachment,
                                    'doctype' => str_replace(",", ", ", $row['ddoctype']),
                                    'destination' => $destination . "<br>" . $actionby,
                                    'destinationcode' => $row['pop_officecode'],
                                    'datelog' => $datelog,
                                    'status' => $row['statusdesc'],
                                    'timestamp' => strtotime($timestamp),
                                    'docdetail' => $row['ddetail'],
                                    'docdetailp' => $row['ddpdetail'],
                                    'actiondone' => $actionreq,
                                    'actionrequire' => $actionreq,
                                    'listaction' => json_encode($getActionDone),
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


    function getDestinationDataChange(){

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
                        $doc_detailno = $this->request->getPost('id');
                        $destoffice = $this->request->getPost('destoffice');
                        $getDetailData = $this->documentdetailmodel->getDetailData($doc_detailno);
                        $status = "A";
                       
                        $data = array();
                    
                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incomingreld");
                        $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                        if($getuser['success']){

                            if($receiveData){

                                if($getDetailData['office_destination'] == $destoffice){
                                    

                                    $offices = $this->OfficeModel->getOfficeExceptCurrent($officecode);
                                    $userByOffice = $this->UserModel->getUsersByOffice($getDetailData['office_destination']);
                                    $actionrequired = $this->actionmodel->get_action_required_active();

                                    $data = [
                                        'success' => true,
                                        'office' => $offices,
                                        'detaildata' => $getDetailData,
                                        'officeuser' => $userByOffice['data'],
                                        'action_required' => $actionrequired,
                                    ];

                                }else{
                                    $data = ['success' => false, 'message' => 'Document destination was already changed. Table will reload.', 'reload' => true];
                                }

                            }else{

                                $data['success'] = false;
                                $data['message'] = "Error retrieving data. The document has already been received!";
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



    public function changeDestination(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {

                        $logged_user = $this->session->get('logged_user');
                        $doc_detailno = $this->request->getPost('dd');
                        $getDetailData = $this->documentdetailmodel->getDetailData($doc_detailno);
                        $status = "A";

                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$getDetailData['route_no']);
                        $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incomingreld");
                        $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                        if($getuser['success']){

                            $rules = [
                                'change_office_destination' => [
                                    'rules' => 'required|checkIfOfficeExists['.$getDetailData['route_no'].','.$getDetailData['doc_controlno'].']',
                                    'errors' => [
                                        'required' => 'Please select Office Destination!',
                                        'checkIfOfficeExists' => 'Office Destination Already Exists!',
                                    ],
                                ],
                                'change_action_officer' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Action Officer!',
                                    ],
                                ],
                                'change_action_required' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Action Required!',
                                    ],
                                ],
    
                            ];
                            
                            if($this->validate($rules))
                            {
                                
                                if($receiveData){
                                    
                                    $changeDesData = [
                                        'doc_detailno' => $doc_detailno,
                                        'office_destination' => $this->request->getPost('change_office_destination'),
                                        'action_officer' => $this->request->getPost('change_action_officer'),
                                        'action_required' => $this->request->getPost('change_action_required'),
                                        'modified_by' =>  $logged_user,
                                        'modified_date' =>  date('Y-m-d H:i:s'),
                                    ];
        
                                    $updateDestination = $this->documentdetailmodel->updateDestination($changeDesData);
                                   
                                    if($updateDestination['success']){
                                        $data = [
                                            'success' => true,
                                            'message' => $updateDestination['message'],
                                            'routeno' => $getDetailData['routeno'],
                                        ];
        
                                    }else{
        
                                        $data = [
                                            'success' => false,
                                            'message' => $updateDestination['message'],
                                        ];
                                    }
    
                                }else{
                                    $data = [
                                        'success' => false,
                                        'message' => 'Error retrieving data. The document has already been received!',
                                        'reload' => true
                                    ];
                                }
                                
            
                            } else {   
                                
                                $data = [
                                    'success' => false,
                                    'formnotvalid' => true,
                                    'data' => [
                                        'change_office_destination' => $this->validation->getError('change_office_destination'),
                                        'change_action_officer' => $this->validation->getError('change_action_officer'),
                                        'change_action_required' => $this->validation->getError('change_action_required'),
                                    ],
                                ];
                                
                            }
                        } else {

                            $data = [
                                'success' => false,
                                'message' => 'You are not authorize to change this destination',
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


    function getDestinationDataAdd(){

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
                        $doc_detailno = $this->request->getPost('id');
                        $getDetailData = $this->documentdetailmodel->getDetailData($doc_detailno);
                        $status = ['O','F','H'];
                       
                        $data = array();
                    
                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");
                        $receiveData = $this->IncomingModel->receiveDataArray($doc_detailno,$status);

                        if($getuser['success']){

                            if($receiveData){                                   

                                $offices = $this->OfficeModel->getOfficeExceptCurrent($officecode);
                                $userByOffice = $this->UserModel->getUsersByOffice($getDetailData['office_destination']);
                                $actionrequired = $this->actionmodel->get_action_required_active();

                                $data = [
                                    'success' => true,
                                    'office' => $offices,
                                    'detaildata' => $getDetailData,
                                    'officeuser' => $userByOffice['data'],
                                    'action_required' => $actionrequired,
                                ];

                            }else{

                                $data['success'] = false;
                                $data['message'] = "Error retrieving data. The document has already been received!";
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


    public function addDestination(){
        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {

                        $logged_user = $this->session->get('logged_user');
                        $doc_detailno = $this->request->getPost('dda');
                        $getDetailData = $this->documentdetailmodel->getDetailData($doc_detailno);

                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");
                        $receiveData = $this->IncomingModel->receiveDataArrayWithSeq($doc_detailno);

                        if($getuser['success']){

                            $rules = [
                                'add_office_destination' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Office Destination!',
                                    ],
                                ],
                                'add_action_officer' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Action Officer!',
                                    ],
                                ],
                                'add_action_required' => [
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Please select Action Required!',
                                    ],
                                ],
    
                            ];
                            
                            if($this->validate($rules))
                            {
                                
                                if($receiveData){

                                    $generateDocumentDetailNo = $this->documentdetailmodel->generateDocumentDetailNo();
                                    $status = 'A';

                                    $addDesData = [
                                        'doc_detailno' => $generateDocumentDetailNo,
                                        'route_no' => $getDetailData['route_no'],
                                        'doc_controlno' => $getDetailData['doc_controlno'],
                                        'sequence_no' => $getDetailData['sequence_no'] + 1,
                                        'office_destination' => $this->request->getPost('add_office_destination'),
                                        'action_officer' => $this->request->getPost('add_action_officer'),
                                        'action_required' => $this->request->getPost('add_action_required'),
                                        'entry_by' => $this->customobj->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']),
                                        'emp_entry' => $logged_user,
                                        'status' => $status,
                                        'no_page' => $getDetailData['no_page'],
                                        'modified_by' =>  $logged_user,
                                        'date_log' =>  date('Y-m-d'),
                                        'time_log' =>  date('H:i:s'),
                                        'modified_date' =>  date('Y-m-d H:i:s'),
                                    ];
        
                                    $addDestination = $this->documentdetailmodel->insertDocumentDetail($addDesData);
                                   
                                    if($addDestination['success']){

                                        $data = [
                                            'success' => true,
                                            'message' => 'Successfully added document destination!',
                                            'routeno' => $getDetailData['routeno'],
                                        ];
        
                                    }else{
        
                                        $data = [
                                            'success' => false,
                                            'message' => $updateDestination['message'],
                                        ];
                                    }
    
                                }else{

                                    $data = [
                                        'success' => false,
                                        'message' => 'Error retrieving data. The document already has a Destination!',
                                        'reload' => true
                                    ];
                                    
                                }
                                
            
                            } else {   
                                
                                $data = [
                                    'success' => false,
                                    'formnotvalid' => true,
                                    'data' => [
                                        'add_office_destination' => $this->validation->getError('add_office_destination'),
                                        'add_action_officer' => $this->validation->getError('add_action_officer'),
                                        'add_action_required' => $this->validation->getError('add_action_required'),
                                    ],
                                ];
                                
                            }
                        } else {

                            $data = [
                                'success' => false,
                                'message' => 'You are not authorize to change this destination',
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
 