<?php

namespace App\Controllers;
use App\Libraries\CustomObj;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\DocumentRegistryModel;
use App\Models\DocumentTypeModel;
use App\Models\ActionModel;


class Miscellaneous extends BaseController
{

    public $validation;
    public $customobj;
    public $IncomingModel;
    public $UserModel;
    public $OfficeModel;
    public $DocumentDetailModel;
    public $DocumentTypeModel;
    public $ActionModel;

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
        $this->actionmodel = new ActionModel();
        
        $this->session = session();
        helper(['form','html','cookie','array', 'test', 'url']);
    }


    public function getUserByOffice(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{

                        $office_destination = $this->request->getPost('office_destination');

                        $getUsersByOffice = $this->UserModel->getUsersByOffice($office_destination);

                        if($getUsersByOffice['success']){

                            $getuserdata = array();
                            foreach($getUsersByOffice['data'] as $getuser){
                                $getuserdata[] = [
                                    'actionofficerid' => $getuser['empcode'],
                                    'actionofficername' => $this->customobj->convertEMPNoRep($getuser['lastname'], $getuser['firstname'], $getuser['middlename']),
                                ];
                            }

                            $data = [
                                'success' => true,
                                'data' => $getuserdata,
                            ];

                        }else{

                            $data = [
                                'success' => false,
                                'message' => $getUsersByOffice['message'],
                            ];

                        }

                    } catch (Exception $e) {

                        logmessage('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
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


    public function getActionReq(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url('/'));
        }

        if ($this->request->isAJAX()) {
            $custom = $this->customobj;

            if ($this->request->getMethod() === 'post'){

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if($custom->validateCSRFToken($csrfToken)){

                    try{

                        $getActionRequired = $this->actionmodel->getActionRequired();

                        if($getActionRequired['success']){

                            $data = [
                                'success' => true,
                                'data' => $getActionRequired['data'],
                            ];

                        }else{

                            $data = [
                                'success' => false,
                                'message' => $getActionRequired['message'],
                            ];

                        }

                    } catch (Exception $e) {

                        logmessage('error', 'Error occurred while retrieving the data in forreceive(): ' . $e->getMessage());
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
 