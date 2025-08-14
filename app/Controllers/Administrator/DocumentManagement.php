<?php

namespace App\Controllers\Administrator;
use App\Controllers\BaseController;

use App\Libraries\CustomObj;
use App\Models\ReportModel;
use App\Models\IncomingModel;
use App\Models\UserModel;
use App\Models\OfficeModel;
use App\Models\DocumentDetailModel;
use App\Models\DocumentRegistryModel;
use App\Models\DocumentTypeModel;
use App\Models\AuditTrailModel;


class DocumentManagement extends BaseController
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
            return redirect()->to(base_url('/'));
        }

        if(!in_array('5', $admin_menu)){
            return redirect()->to(base_url('/'));
        }
        
        
        $navi_bread = "<li>Document Management</li>";


        $data = [
            'header' => '<i class="fa fa-exclamation-circle"></i></i> Document Management',
            'navactive' => '',
            'navsubactive' => 'admin_doc_mgmt',
            'bread' => $navi_bread,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
            //'rnav' => 'receive',
        ];

        return view('administrator/document-management', $data);
    }


    public function view_document_management_table(){

        if(!session()->has('logged_user')){
            return redirect()->to(base_url());
        }

        if ($this->request->isAJAX()) {

            if($this->request->getMethod() == 'post') {

                $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');

                if (!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken)) {

                    try {
                        $data = [];
                        $table_data = [];

                        $limit = $this->request->getPost('length');   // Rows per page
                        $offset = $this->request->getPost('start'); 
                        $searchValue = $this->request->getPost('search')['value'] ?? null;  

                        $routeNoFilter = $this->request->getPost('routeNoFilter') ? $this->request->getPost('routeNoFilter')  : "";
                        $documentControlFilter = $this->request->getPost('documentControlFilter') ? $this->request->getPost('documentControlFilter')  : "";
                        $subjectFilter = $this->request->getPost('subjectFilter') ? $this->request->getPost('subjectFilter')  : "";
                       
                        if ($routeNoFilter === '' && $documentControlFilter === '' && $subjectFilter === '') {
                            $data = [
                                "draw" => intval($this->request->getPost('draw')),
                                "recordsTotal" => 0,
                                "recordsFiltered" => 0,
                                "data" => [],
                            ];

                            return $this->response->setJSON($data);
                        }



                        $filterValue = [
                            'routeNoFilter' => $routeNoFilter,
                            'documentControlFilter' => $documentControlFilter,
                            'subjectFilter' => $subjectFilter,
                        ];

                        $documentManagement = $this->documentregistrymodel->documentManagement($limit, $offset, $searchValue, $filterValue);
                        $cnt = $offset;

                        if(!$documentManagement['success']){
                                throw new \Exception($documentManagement['message']);
                        }

                        foreach ($documentManagement['data'] as $row) {
                            $cnt++;
                            $attachment = "";

                            if(!empty($row['filename']) || $row['filename'] !== "" ){
                                $attachment = "<a href='".base_url().'docview/outgoing/viewfile/'.$row['filename']."' target='_blank'><div class='media-items-content'><i class='fa fa-file-pdf-o fa-2x text-danger'></i></div>View</a>";
                            }else{
                                $attachment = "No<br>Attachment";
                            }

                            $checkDestinationStatus = $this->documentdetailmodel->checkIfDestExists($row['route_no']);
                            $destination_btn = '<a href="'.base_url("admin/document_management/destination/".$row["route_no"]).'" class="btn btn-xs btn-warning enable-tooltip" title="Document Destination"><i class="fa fa-send"></i> Destination</a>';

                            if ($row['registry_status'] == 'Active' && $checkDestinationStatus) {
                                $delete_btn = '<a href="javascript:void(0)" class="btn btn-xs btn-danger enable-tooltip delete_route" title="Delete!"><i class="fa fa-trash"></i></a>';
                            } else {
                                $delete_btn = '';
                            }
                            $btn = '<div class="btn-group">
                                '.$destination_btn.$delete_btn.'
                            </div>';

                            $table_data[] = [
                                'cnt' => $cnt,
                                'action' => $btn,
                                'routeno' => $row['route_no'],
                                'docno' => str_replace(",", ", ", $row['docno']),
                                'ref_controlno' => $row['ref_office_controlno'],
                                'subject' => "<strong>".htmlspecialchars($row['subject'])."</strong> ",
                                'doctype' => str_replace(",", ", ", $row['ddoctype']),
                                'orig_office' => $row['orig_office'],
                                'entryby' => $this->customobj->convertEMP($row['lastname'], $row['firstname'], $row['middlename'], $row['orep']),
                                'pageno' => $row['no_page'],
                                'attachment' => $attachment,
                                'remarks' => $row['remarks'],
                                'status' => $row['registry_status'],
                            ];
                            
                        }

                      
                        $data = [
                            "draw" => intval($this->request->getPost('draw')),
                            "recordsTotal" => $documentManagement['totalRecords'],
                            "recordsFiltered" => $documentManagement['recordsFiltered'],
                            "data" => $table_data,
                        ];
                        

                    } catch (\Exception $e) {

                        log_message('error', 'Error Data: ' . $e->getMessage());
                        $data = [
                            'success' => false,
                            'message' => $e->getMessage(),
                        ];

                        return $this->response->setJSON($data);
                        
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
 