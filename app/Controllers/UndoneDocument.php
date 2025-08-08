<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\DocumentRegistryModel;
use App\Models\ActionModel;


class UndoneDocument extends BaseController
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
            'rnav' => 'undone',
            'bread' => $navi_bread,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('document-undone', $data);
    }

    public function forundone(){

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
                        $status = 'I';
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
                                $actionBtn = "<a class='btn btn-xs btn-warning undone-doc enable-tooltip' data-docdetail='".$docdetail."' data-toggle='modal' title='Change Destination'><i class='fa fa-undo'></i> Undone Doc</a>";
                                
                                $btn = $actionBtn;
                                
                                $data[] = [
                                    'controlno' => $row['dcon'],
                                    'originating' => $row['origoffice'],
                                    'previous' => $row['prevoffice'],
                                    'subject' => "<strong>".htmlspecialchars($row['subject'])."</strong> <br><br> Action Done: <strong>".$row['reqaction']."</strong> <br> Attachment: <strong>".$attachment."</strong>",
                                    'remarks' => $row['drRem'],
                                    'attachment' => $attachment,
                                    'doctype' => str_replace(",", ", ", $row['ddoctype']),
                                    'actionrequire' => $row['reqaction'],
                                    'datelog' => date('F d, Y', strtotime($row['date_action'])) . "<br>" . $row['time_action'],
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


    function undoneDocument(){

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
                        $password = $this->request->getPost('password');
                        $getDetailData = $this->documentdetailmodel->getDetailData($doc_detailno);
                        $status = "I";
                       
                        $data = array();
                    
                        $getuser = $this->UserModel->getUser($logged_user,$doc_detailno,"incoming");
                        $receiveData = $this->IncomingModel->receiveData($doc_detailno,$status);

                        
                        if($getuser['success']){

                            $verifyPassword = $this->customobj->verifyPassword($password,$getuser['data']['userpass']);

                            if($verifyPassword){

                                if($receiveData){

                                    $undonedata = [
                                        'status' => 'T',
                                        'modified_by' => $logged_user
                                    ];

                                    $undoneStatus = $this->documentdetailmodel->undoneStatus($doc_detailno,$undonedata);

                                    if($undoneStatus['success']){

                                        $data['success'] = true;
                                        $data['message'] = "Successfully Undone Document!";

                                    }else{
                                        $data['success'] = false;
                                        $data['message'] = $undoneStatus['message'];

                                    }

                                }else{

                                    $data['success'] = false;
                                    $data['message'] = "Error retrieving data. The document was already undone.";
                                    $data['reload'] = true;

                                }

                            }else{
                                $data = [
                                    'success' => false,
                                    'message' => 'Error updating status. Password does not match.',
                                ];
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


}
 