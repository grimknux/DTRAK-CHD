<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\ActionModel;


class IncomingReceiving extends BaseController
{
    public $customobj;
    public $IncomingModel;
    public $UserModel;
    public $OfficeModel;
    public $DocumentDetailModel;
    public $actionmodel;

    public $session;

    public function __construct()

    {
        
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
                       <li>For Receive</li>";

        $data = [
            'header' => 'Receiving and Releasing',
            'navactive' => 'incoming',
            'navsubactive' => 'receiveaction',
            'rnav' => 'receive',
            'bread' => $navi_bread,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('document-receiving', $data);
    }

    public function forreceive(){

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
                        $status = ['A'];
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
                                
                                $rcvBtn = "<div class='btn-group'><a type='submit' class='btn btn-xs btn-info insta-rcv enable-tooltip' data-did='".$docdetail."' title='Instant Receive!'><i class='fa fa-bolt fa-fw'></i></a>&nbsp;<a class='btn btn-xs btn-success rcv-modal enable-tooltip' data-docdetail='".$docdetail."' data-toggle='modal' title='Receive Document!'><i class='fa fa-download'></i> Receive</a></div>";
            
                                $data[] = [
                                    'controlno' => $row['dcon'],
                                    'originating' => $row['origoffice'],
                                    'previous' => $row['prevoffice'],
                                    'subject' => "<strong>".htmlspecialchars($row['subject'])."</strong> <br><br> Action Required: <strong>".$row['reqaction']."</strong> <br> Attachment: <strong>".$attachment."</strong>",
                                    'remarks' => $row['drRem'],
                                    'attachment' => $attachment,
                                    'doctype' => str_replace(",", ", ", $row['ddoctype']),
                                    'actionrequire' => $row['reqaction'],
                                    'datelog' => date('F d, Y', strtotime($row['datelog'])) . "<br>" . $row['timelog'],
                                    'btnaction' => $rcvBtn,
                                    'docdetail' => $row['ddetail'],
                                    'btnaction2' => 'Action',
                                    'btnaction3' => 'Release',
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

    function getReceiveData(){

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
                        $status = "A";
                        $logged_user = $this->session->get("logged_user");
                        $getuser = $this->UserModel->getUser($logged_user,$docdetail,"incoming");
                       
                        $data = array();

                        if($getuser['success']){
                            $user = $custom->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']);

                                $receiveData = $this->IncomingModel->receiveData($docdetail,$status);

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
                                        'daterec' => date('Y-m-d'),
                                        'timerec' => date('H:i:s'),
                                        'receiveby' => $user,
                                    ];
                                    
                                    $data['success'] = true;
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


    public function receiveDocument(){
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
                            $status = "A";

                            if($this->documentdetailmodel->checkDocIfValid($officecode,$doc_detailno)){

                                $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                                if($receiveData){
                                    $data = [

                                        'status' => 'R',
                                        'receive_by' => $logged_user,
                                        'date_rcv' => date('Y-m-d'),
                                        'time_rcv' => date('H:i:s'),
                                        'datelog_rcv' => date('Y-m-d'),
                                        'timelog_rcv' => date('H:i:s'),
                                        
                                    ];
    
                                    $updateStatus = $this->documentdetailmodel->updateStatus($doc_detailno, $data);
    
                                    if($updateStatus['success']){
                                        $data = ['success' => true, 'message' => 'Document Received!'];
                                    }else{
                                        $data = ['success' => true, 'message' => $updateStatus['message']];
                                    }
    
                                }else{
                                    $data = ['success' => false, 'message' => 'Error retrieving data. The document has already been received!', 'reload' => true];
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

    
    public function receiveBulkDocument(){
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
                        $status = "A";
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

                                'status' => 'R',
                                'receive_by' => $logged_user,
                                'date_rcv' => date('Y-m-d'),
                                'time_rcv' => date('H:i:s'),
                                'datelog_rcv' => date('Y-m-d'),
                                'timelog_rcv' => date('H:i:s'),
                                
                            ];

                            $updateStatus = $this->documentdetailmodel->updateStatusBulk($docinfo, $data, $officecode, 'receive');
                
                            if($updateStatus['success']){
                                $data = ['success' => true, 'message' => 'Document Received!'];
                            }else{
                                $data = ['success' => false, 'message' => 'error'];
                            }
                        }else{
                            $data = ['success' => false, 'message' => 'Error retrieveing data. Document ' . implode(', ', $controlId) . ' has already been received!', 'reload' => true];
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

        $attachoffice = $this->IncomingModel->checkAttachIfValidUser($filename);

        if ($attachoffice && isset($attachoffice[0]['office_destination'])) {
            $attachOfficeDestination = $attachoffice[0]['office_destination'];

            $useroffice = $this->UserModel->getUserOffice($logged_user);

            if (file_exists($filePath)) {

                if (in_array($attachOfficeDestination, array_column($useroffice, 'officecode'))) {
                    return $this->response->setHeader('Content-Type', mime_content_type($filePath))
                                        ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                                        ->setBody(file_get_contents($filePath));
                } else {
                    return $this->response->setBody($attachOfficeDestination)->setStatusCode(403);
                }

            } else {
                return $this->response->setBody('File not found')->setStatusCode(404);
            }
        } else {
            return $this->response->setBody('Invalid file or access denied')->setStatusCode(403);
        }
    }


    

}
 