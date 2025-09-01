<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\DocumentRegistryModel;
use App\Models\DocumentControlModel;
use App\Models\DocumentTypeModel;
use App\Models\ActionModel;
use App\Models\HolidayModel;


class OutgoingDestination extends BaseController
{

    public $validation;
    public $customobj;
    public $IncomingModel;
    public $UserModel;
    public $OfficeModel;
    public $DocumentDetailModel;
    public $documentregistrymodel;
    public $DocumentTypeModel;
    public $DocumentControlModel;
    public $ActionModel;
    public $HolidayModel;

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
        $this->documentcontrolmodel = new DocumentControlModel();
        $this->actionmodel = new ActionModel();
        $this->holidaymodel = new HolidayModel();
        
        $this->session = session();
        helper(['form','html','cookie','array', 'test', 'url']);
    }


    public function index($routeno)
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
        <li><a href='".base_url('docview/outgoing')."'>Originating and Outgoing</a></li>
        <li>Destination</li>";

        $logged_user = $this->session->get("logged_user");
        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
        $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$routeno);

        if(!$checkOfficeIfValid){

            return redirect()->to(base_url('/'));

            //change later on 
        }

        $getDocumentData = $this->documentregistrymodel->getDocumentData($routeno);
        $offices = $this->OfficeModel->getOfficeExceptCurrent($getDocumentData['officecode']);
        $getActionRequired = $this->actionmodel->get_action_required_active();
        $getOfficeDest = $this->documentdetailmodel->getOfficeDest($routeno);
        $destinationCodes = array_column($getOfficeDest, 'office_destination');

        $checkIfDestinationExists = $this->documentdetailmodel->checkIfDestinationExists($routeno);

        $data = [
            'header' => '<i class="fa fa fa-send"></i> Document Outgoing (Outbox)',
            'navactive' => 'outgoing',
            'navsubactive' => 'outgoaction',
            'docdata' => $getDocumentData,
            'bread' => $navi_bread,
            'officeDestinations' => $offices,
            'officedest' => $destinationCodes,
            'actionReq' => $getActionRequired,
            'checkdest' => $checkIfDestinationExists,
            'routeno' => $routeno,
            'entryby' => $this->customobj->convertEMP($getDocumentData['lastname'], $getDocumentData['firstname'], $getDocumentData['middlename'], $getDocumentData['orep']),
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
            //'rnav' => 'receive',
        ];

        return view('destination-outgoing', $data);
    }
    


    public function controlDestinationData(){

        $routeno = $this->request->getPost('routeno'); 

        $getHoliday = $this->holidaymodel->getHoliday();

        $getDocControl = $this->documentcontrolmodel->getDocControl($routeno);
        $documentControls = [];

        foreach($getDocControl as $doccontrol){
            $docControlId = $doccontrol['docno'];

            if (!isset($documentControls[$docControlId])) {
                $documentControls[$docControlId] = [
                    'control_id' => $doccontrol['docno'],
                    'date_time_log' => $doccontrol['created_date'],
                    //'name' => $doccontrol['doc_controlno'], // assuming `name` is a field in document_control
                    'destinations' => [],
                    'oth_dest' => []
                ];
            }

            if ($doccontrol['doc_detailno']) {
                
                $datetime_rcv = "";
                $datetime_act = "";
                $datetime_rel = "";
                $rcvTorel = "";

                if (isset($doccontrol['date_rcv']) && $doccontrol['date_rcv'] !== '') {
                    $datetime_rcv = date('F d, Y', strtotime($doccontrol['date_rcv'])) ."<br>".date('H:i:s', strtotime($doccontrol['time_rcv']));
                }

                if (isset($doccontrol['date_action']) && $doccontrol['date_action'] !== '') {
                    $datetime_act = date('F d, Y', strtotime($doccontrol['date_action'])) ."<br>".date('H:i:s', strtotime($doccontrol['time_action']));
                }

                if (isset($doccontrol['release_date']) && $doccontrol['release_date'] !== '') {
                    $datetime_rel = date('F d, Y', strtotime($doccontrol['release_date'])) ."<br>".date('H:i:s', strtotime($doccontrol['release_time']));
                }


                if (isset($doccontrol['date_rcv']) && $doccontrol['date_rcv'] !== '' && isset($doccontrol['release_date']) && $doccontrol['release_date'] !== '') {

                    $datetimercv = $doccontrol['date_rcv'] . " " . $doccontrol['time_rcv'];
                    $datetimerel = $doccontrol['release_date'] . " " . $doccontrol['release_time'];
                    

                    $rcvTorel = $this->customobj->calculateTime24Hrs($datetimercv, $datetimerel, $getHoliday);

                }else{

                    if($doccontrol['docstatus'] == "I" && isset($doccontrol['date_action']) && $doccontrol['date_action'] !== ''){

                        $datetimercv = $doccontrol['date_rcv'] . " " . $doccontrol['time_rcv'];
                        $datetimerel = $doccontrol['date_action'] . " " . $doccontrol['time_action'];

                        $rcvTorel = $this->customobj->calculateTime24Hrs($datetimercv, $datetimerel, $getHoliday);
                        
                    }else{

                        $rcvTorel = ['days' => '', 'hours' => '', 'minutes' => '', 'total_seconds' => ''];

                    }

                }

                if (isset($doccontrol['date_rcv']) && $doccontrol['date_rcv'] !== '' && isset($doccontrol['next_date_rcv']) && $doccontrol['next_time_rcv'] !== '') {
                    $datetimercv = $doccontrol['date_rcv'] . " " . $doccontrol['time_rcv'];
                    $datetimerel = $doccontrol['next_date_rcv'] . " " . $doccontrol['next_time_rcv'];

                    $rcvTorcv = $this->customobj->calculateTime24Hrs($datetimercv, $datetimerel, $getHoliday);

                }else{
                    $rcvTorcv = ['days' => '', 'hours' => '', 'minutes' => '', 'total_seconds' => ''];
                }

                $buttons = '';
                $isSingleDestination = count($documentControls[$docControlId]['destinations']) === 0;
                $isLastDestination = empty($doccontrol['next_date_rcv']);

                if ($isSingleDestination && $doccontrol['docstatus'] === 'A' && empty($doccontrol['received_by']) && empty($doccontrol['received_date']) && empty($doccontrol['received_time'])) {

                    $buttons = "</div><a class='btn btn-xs btn-primary change-desti enable-tooltip' data-id='".$doccontrol['doc_detailno']."' data-toggle='modal' title='Change Destination'><i class='fa fa-pencil-square'></i> Change Destination</a>

                    <a class='btn btn-xs btn-danger delete-desti enable-tooltip' data-control='".$doccontrol['doc_controlno']."' data-id='".$doccontrol['doc_detailno']."' data-toggle='modal' title='Delete Destination'><i class='fa fa-trash'></i> Delete Destination</a>";
                    
                }

                

                $documentControls[$docControlId]['destinations'][] = [
                    'detailno' => $doccontrol['doc_detailno'],
                    'sequence' => $doccontrol['sequence_no'],
                    'status' => $doccontrol['docstatus'],
                    'action_required' => $doccontrol['reqaction_desc'],
                    'received_by' => $doccontrol['receive_by'],
                    'received_date' => $doccontrol['date_rcv'],
                    'received_time' => $doccontrol['time_rcv'],
                    'officeshort' => $doccontrol['shortname'],
                    'officename' => $doccontrol['officename'],
                    'remarks' => $doccontrol['remarks'],
                    'remarks2' => $doccontrol['remarks2'] ?? '',
                    'action_officer' => $this->customobj->convertEMP($doccontrol['lastname'], $doccontrol['firstname'], $doccontrol['middlename'], $doccontrol['office_rep']),
                    'action_officer_rcv' => $this->customobj->convertEMP($doccontrol['rcv_lastname'], $doccontrol['rcv_firstname'], $doccontrol['rcv_middlename'], $doccontrol['rcv_orep']),
                    'datetime_rcv' => $datetime_rcv,
                    'action_officer_act' => $this->customobj->convertEMP($doccontrol['act_lastname'], $doccontrol['act_firstname'], $doccontrol['act_middlename'], $doccontrol['act_orep']),
                    'datetime_act' => $datetime_act,
                    'action_done' => $doccontrol['action_desc'] ?? '',
                    'action_officer_rel' => $this->customobj->convertEMP($doccontrol['rel_lastname'], $doccontrol['rel_firstname'], $doccontrol['rel_middlename'], $doccontrol['rel_orep']),
                    'datetime_rel' => $datetime_rel,
                    'rcvTorcv' => "Day: <b>".$rcvTorcv['days']."</b><br>Hour: <b>".$rcvTorcv['hours']."</b><br>Minutes: <b>".$rcvTorcv['minutes']."</b>",
                    'rcvTorel' => "Day: <b>".$rcvTorel['days']."</b><br>Hour: <b>".$rcvTorel['hours']."</b><br>Minutes: <b>".$rcvTorel['minutes']."</b>",
                    'action' => $buttons,
                ];

                
                
            }


        }

        foreach ($documentControls as $loop){
            $getdata = $this->getRecursion($docControlId);

            if($getdata){
                $documentControls[$docControlId]['oth_dest'] = array_merge($documentControls[$docControlId]['oth_dest'], $getdata);
            }

        }


        return $this->response->setJSON(array_values($documentControls));
    }


    private function getRecursion($docno){

        $getRoute =  $this->documentregistrymodel->getRouteno($docno);
 
        $getHoliday = $this->holidaymodel->getHoliday();

        if($getRoute){
            $routeno = $getRoute['route_no'];
            $getDocControl = $this->documentcontrolmodel->getDocControl($routeno);
            $getDocInfo = $this->documentregistrymodel->getDocInfo($routeno);
            $refDocumentControls = [];
            
            foreach($getDocControl as $control){
                $docControlId = $control['docno'];

                if(!isset($refDocumentControls[$docControlId])) {
                    $refDocumentControls[$docControlId] = [
                        'ref_control_id' => $control['docno'],
                        'reference' => [
                            'subject' => $getDocInfo['subject'],
                            'office' => $getDocInfo['officename'],
                            'doctype' => $getDocInfo['ddoctype_desc'],
                            'date_log' => $getDocInfo['datelog'],
                            'time_log' => $getDocInfo['timelog'],
                        ],
                        //'name' => $doccontrol['doc_controlno'], // assuming `name` is a field in document_control
                        'ref_destinations' => [],
                    ];
                    
                }

                $datetime_rcv = "";
                $datetime_act = "";
                $datetime_rel = "";
                $rcvTorel = "";

                if (isset($control['date_rcv']) && $control['date_rcv'] !== '') {
                    $datetime_rcv = date('F d, Y', strtotime($control['date_rcv'])) ."<br>".date('H:i:s', strtotime($control['time_rcv']));
                }

                if (isset($control['date_action']) && $control['date_action'] !== '') {
                    $datetime_act = date('F d, Y', strtotime($control['date_action'])) ."<br>".date('H:i:s', strtotime($control['time_action']));
                }

                if (isset($control['release_date']) && $control['release_date'] !== '') {
                    $datetime_rel = date('F d, Y', strtotime($control['release_date'])) ."<br>".date('H:i:s', strtotime($control['release_time']));
                }


                if (isset($control['date_rcv']) && $control['date_rcv'] !== '' && isset($control['release_date']) && $control['release_date'] !== '') {

                    $datetimercv = $control['date_rcv'] . " " . $control['time_rcv'];
                    $datetimerel = $control['release_date'] . " " . $control['release_time'];
                    

                    $rcvTorel = $this->customobj->calculateTime24Hrs($datetimercv, $datetimerel, $getHoliday);

                }else{

                    if($control['docstatus'] == "I" && isset($control['date_action']) && $control['date_action'] !== ''){

                        $datetimercv = $control['date_rcv'] . " " . $control['time_rcv'];
                        $datetimerel = $control['date_action'] . " " . $control['time_action'];

                        $rcvTorel = $this->customobj->calculateTime24Hrs($datetimercv, $datetimerel, $getHoliday);
                        
                    }else{

                        $rcvTorel = ['days' => '', 'hours' => '', 'minutes' => '', 'total_seconds' => ''];

                    }

                }

                if (isset($control['date_rcv']) && $control['date_rcv'] !== '' && isset($control['next_date_rcv']) && $control['next_time_rcv'] !== '') {
                    $datetimercv = $control['date_rcv'] . " " . $control['time_rcv'];
                    $datetimerel = $control['next_date_rcv'] . " " . $control['next_time_rcv'];

                    $rcvTorcv = $this->customobj->calculateTime24Hrs($datetimercv, $datetimerel, $getHoliday);

                }else{
                    $rcvTorcv = ['days' => '', 'hours' => '', 'minutes' => '', 'total_seconds' => ''];
                }

                $refDocumentControls[$docControlId]['ref_destinations'][] = [
                    'detailno' => $control['doc_detailno'],
                    'sequence' => $control['sequence_no'],
                    'status' => $control['docstatus'],
                    'action_required' => $control['reqaction_desc'],
                    'received_by' => $control['receive_by'],
                    'received_date' => $control['date_rcv'],
                    'received_time' => $control['time_rcv'],
                    'officeshort' => $control['shortname'],
                    'officename' => $control['officename'],
                    'remarks' => $control['remarks'],
                    'remarks2' => $control['remarks2'] ?? '',
                    'action_officer' => $this->customobj->convertEMP($control['lastname'], $control['firstname'], $control['middlename'], $control['office_rep']),
                    'action_officer_rcv' => $this->customobj->convertEMP($control['rcv_lastname'], $control['rcv_firstname'], $control['rcv_middlename'], $control['rcv_orep']),
                    'datetime_rcv' => $datetime_rcv,
                    'action_officer_act' => $this->customobj->convertEMP($control['act_lastname'], $control['act_firstname'], $control['act_middlename'], $control['act_orep']),
                    'datetime_act' => $datetime_act,
                    'action_done' => $control['action_desc'] ?? '',
                    'action_officer_rel' => $this->customobj->convertEMP($control['rel_lastname'], $control['rel_firstname'], $control['rel_middlename'], $control['rel_orep']),
                    'datetime_rel' => $datetime_rel,
                    'rcvTorcv' => "Day: <b>".$rcvTorcv['days']."</b><br>Hour: <b>".$rcvTorcv['hours']."</b><br>Minutes: <b>".$rcvTorcv['minutes']."</b>",
                    'rcvTorel' => "Day: <b>".$rcvTorel['days']."</b><br>Hour: <b>".$rcvTorel['hours']."</b><br>Minutes: <b>".$rcvTorel['minutes']."</b>",
                    'action' => '',
                ];

            }

            foreach ($refDocumentControls as $loop){
                $getdata = $this->getRecursion($docControlId);
                    if($getdata){
                        $refDocumentControls = array_merge($refDocumentControls, array_values($getdata));
                    }
            }
           

            return array_values($refDocumentControls);

        }else{
            return false;
        }
       
    }


    public function refreshDestinatioDetails($routeno){

        $documentControls = $this->controlDestinationData($routeno);

        foreach ($destinations as $index => $destination) {

        }

    }


    public function addDocumentDestination(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {

            if ($this->request->getMethod() === 'post') {

                $csrfToken = $this->request->getPost('csrf_token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {

                        $logged_user = $this->session->get('logged_user');
                        $getuser = $this->UserModel->getUser($logged_user)['data'];
                        $routeno = $this->request->getPost('routeno');
                        
                        $rules = [
                            'office_destination.*' => [
                                'rules' => 'required|checkIfOfficeExists['.$routeno.']',
                                'errors' => [
                                    'required' => 'Please select Office Destination!',
                                    'checkIfOfficeExists' => 'Office Destination Already Exists!',
                                ],
                            ],
                            'action_officer.*' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Officer!',
                                ],
                            ],
                            'action_required.*' => [
                                'rules' => 'required',
                                'errors' => [
                                    'required' => 'Please select Action Required!',
                                ],
                            ],

                        ];
                        
                        if($this->validate($rules))
                        {

                            $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                            $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$routeno);

                            if($checkOfficeIfValid){

                                $officeDestinations = $this->request->getPost('office_destination');
                                $actionOfficers = $this->request->getPost('action_officer');
                                $actionRequireds = $this->request->getPost('action_required');
                                
                                $getDocumentData = $this->documentregistrymodel->getDocumentData($routeno);

                                $errorArray = [];

                                foreach ($officeDestinations as $index => $officeDestination) {

                                    $generateDocumentControlNo = $this->documentcontrolmodel->generateDocumentControlNo();
                                    $generateDocumentDetailNo = $this->documentdetailmodel->generateDocumentDetailNo();

                                    $insertdata = [
                                        'doc_detailno' => $generateDocumentDetailNo,
                                        'route_no' => $routeno,
                                        'doc_controlno' => $generateDocumentControlNo,
                                        'sequence_no' => 1,
                                        'office_destination' => $officeDestination,
                                        'action_officer' => $actionOfficers[$index],
                                        'action_required' => $actionRequireds[$index],
                                        'entry_by' => $this->customobj->convertEMP($getuser['lastname'], $getuser['firstname'], $getuser['middlename'], $getuser['office_rep']),
                                        'emp_entry' => $logged_user,
                                        'no_page' => $getDocumentData['no_page'],
                                        'remarks' => null,
                                        'date_log' => date('Y-m-d'),
                                        'time_log' => date('H:i:s'),
                                        'modified_by' => $logged_user,
                                        
                                    ];

                                    

                                    // Replace with your model's insert method
                                    $insertDocumentDetail = $this->documentcontrolmodel->insertDocumentControl($insertdata);

                                    if (!$insertDocumentDetail['success']) {
                                        $getOfficeDataById = $this->OfficeModel->getOfficeDataById($officeDestination);
                                        $errorArray[] = $getOfficeDataById['officename'] . ". " .  $insertDocumentDetail['message'];
                                    }
                                }

                                if (!empty($errorArray)) {
                                    
                                    $errorString = implode(', ', $errorArray);
                                
                                    
                                    $data = [
                                        'success' => false,
                                        'message' => 'Failed to insert data for the following Office Destinations: ' . $errorString
                                    ];
                                } else {
                                    
                                    $data = [
                                        'success' => true,
                                        'message' => 'Data inserted successfully!',
                                        'routeno' => $routeno,
                                    ];
                                }

                            }else{

                                $data['success'] = false;
                                $data['message'] = 'You are not authorize to add Destination.';
                            }
        
                        } else {   
                            $errors = [
                                'office_destination' => [],
                                'action_officer' => [],
                                'action_required' => []
                            ];
                            
                            $officeDestinations = $this->request->getVar('office_destination');
                            foreach ($officeDestinations as $key => $value) {
                                if ($this->validation->hasError("office_destination.$key")) {
                                    $errors['office_destination'][$key] = $this->validation->getError("office_destination.$key");
                                }
                            }

                            $actionOfficers = $this->request->getVar('action_officer');
                            foreach ($actionOfficers as $key => $value) {
                                if ($this->validation->hasError("action_officer.$key")) {
                                    $errors['action_officer'][$key] = $this->validation->getError("action_officer.$key");
                                }
                            }

                            $actionRequireds = $this->request->getVar('action_required');
                            foreach ($actionRequireds as $key => $value) {
                                if ($this->validation->hasError("action_required.$key")) {
                                    $errors['action_required'][$key] = $this->validation->getError("action_required.$key");
                                }
                            }
                            
                            $data = [
                                'success' => false,
                                'formnotvalid' => true,
                                'data' => $errors,
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


    function getDestinationData(){

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
                       
                        $data = array();
                    
                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$getDetailData['route_no']);

                        if($checkOfficeIfValid){
                            
                            $offices = $this->OfficeModel->getOffice();
                            $userByOffice = $this->UserModel->getUsersByOffice($getDetailData['office_destination']);
                            $actionrequired = $this->actionmodel->getActionRequired();

                            $getOfficeDest = $this->documentdetailmodel->getOfficeDestChange($getDetailData['route_no'], $getDetailData['doc_controlno']);

                            $otherDestinations = array_column($getOfficeDest, 'office_destination');


                            $data = [
                                'success' => true,
                                'office' => $offices,
                                'detaildata' => $getDetailData,
                                'otherDestinations' => $otherDestinations,
                                'officeuser' => $userByOffice['data'],
                                'action_required' => $actionrequired['data'],
                            ];

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


    function getActionOfficerByOffice(){

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
                        $officedestination = $this->request->getVar('officedestination');
                       
                        $data = array();

                        if($getuser['success']){

                            $userByOffice = $this->UserModel->getUsersByOffice($officedestination);

                            $data = [
                                'success' => true,
                                'officeuser' => $userByOffice['data'],
                            ];
                            

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

                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$getDetailData['route_no']);

                        if($checkOfficeIfValid){
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


    public function deleteDestination(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url());
        }

        if ($this->request->isAJAX()) {

            if($this->request->getMethod() == 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $getOfficeCode = $this->OfficeModel->getOfficeCode(session()->get('logged_user'));

                        $logged_user = $this->session->get('logged_user');
                        $id = $this->request->getPost('id');
                        $password = $this->request->getPost('password');
                        $data = [];

                        
                        $getDetailData = $this->documentdetailmodel->getDetailData($id);
                        $officecode = $this->OfficeModel->getOfficeCode($logged_user);
                        $checkOfficeIfValid = $this->documentregistrymodel->checkOfficeIfValid($officecode,$getDetailData['routeno']);
                        
                        $getuser = $this->UserModel->getUser($logged_user);
                        $verifyPassword = $this->customobj->verifyPassword($password,$getuser['data']['userpass']);
                        

                        if($checkOfficeIfValid){

                            if($verifyPassword){

                                $deleteDestination = $this->documentdetailmodel->deleteThisDestination($id,$logged_user);

                                if($deleteDestination['success']){
                                    $data = [
                                        'success' => true, 
                                        'message' => $deleteDestination['message'],
                                        'routeno' => $getDetailData['routeno'],
                                    ];
                                }else{
                                    $data = [
                                        'success' => false, 
                                        'message' => $deleteDestination['message'],
                                    ];
                                }

                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error deleting. Password does not match.',
                                ];
                            }

                        } else {
                            $data = [
                                'success' => false,
                                'message', 'You are not authorize to delete this destination.',
                            ];
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

}
 