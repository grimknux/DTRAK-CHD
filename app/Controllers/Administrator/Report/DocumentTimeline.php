<?php

namespace App\Controllers\Administrator\Report;
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


class DocumentTimeline extends BaseController
{

    public $validation;
    public $customobj;
    public $ReportModel;
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
        $this->ReportModel = new ReportModel();
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

        if(!in_array('6', $admin_menu)){
            return redirect()->to(base_url('/'));
        }
        
        $navi_bread = "<li>Administrative Report</li>
        <li>Document Timeline</li>";

        $getOffice = $this->OfficeModel->getOffice(session()->get('logged_user'));
        $getDocType = $this->documenttypemodel->getDocType();

        $data = [
            //'header' => 'Released and Processed (Tagged as "Done") Documents',
            'header' => '<i class="fa fa-file-text"></i> Administrative Report',
            'navactive' => 'admin_report_tables',
            'navsubactive' => 'admin_doc_time',
            'bread' => $navi_bread,
            'getOffice' => $getOffice,
            'getDocType' => $getDocType,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('administrator/report/timeline-view', $data);
    }


    public function reportTimeline(){

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

                        $officeFilter = $this->request->getPost('officeFilter') ? $this->request->getPost('officeFilter')  : "";
                        $doctypeFilter = $this->request->getPost('doctypeFilter') ? $this->request->getPost('doctypeFilter')  : "";
                        $docstatusFilter = $this->request->getPost('docstatusFilter') ? $this->request->getPost('docstatusFilter')  : "";
                       

                        $filterValue = [
                            'officeFilter' => $officeFilter,
                            'doctypeFilter' => $doctypeFilter,
                            'docstatusFilter' => $docstatusFilter,
                        ];

                        $timelineQuery = $this->ReportModel->timelineQuery($limit, $offset, $searchValue, $filterValue);
                        $cnt = $offset;

                        if(!$timelineQuery['success']){
                                throw new \Exception($timelineQuery['message']);
                        }

                        foreach ($timelineQuery['data'] as $row) {
                            $cnt++;

                            $first_rcv_datetime = $row['first_date_rcv'] . " " . $row['first_time_rcv'];
                            $last_rcv_datetime = '';
                            $last_rel_datetime = '';
                            $released = ['O', 'F', 'H'];
                            $action_done = ['I', 'T'];
                            
                            if($row['last_seq_status'] == 'A'){
                                if(in_array($row['prev_last_seq_status'], $released)){ //released - forwarded - returned
                                    $last_rel_datetime = $row['prev_last_seq_release_date'] . " " . $row['prev_last_seq_release_time'];
                                    $last_rcv_datetime = $row['prev_last_seq_date_rcv'] . " " . $row['prev_last_seq_time_rcv'];
                                    if($row['prev_last_seq_status'] == 'O'){
                                        $remarks = 'Released to: ' . $row['current_office'];
                                    }elseif($row['prev_last_seq_status'] == 'F'){
                                        $remarks = 'Forwarded to: ' . $row['current_office'];
                                    }elseif($row['prev_last_seq_status'] == 'H'){
                                        $remarks = 'Returned to: ' . $row['current_office'];
                                    }
                                }elseif(in_array($row['prev_last_seq_status'], $action_done)){ //action - done
                                    $last_rel_datetime = $row['prev_last_seq_date_action'] . " " . $row['prev_last_seq_time_action'];
                                    $last_rcv_datetime = $row['prev_last_seq_date_rcv'] . " " . $row['prev_last_seq_time_rcv'];
                                    if($row['prev_last_seq_status'] == 'I'){
                                        $remarks = $row['prev_last_seq_remarks2'];
                                    }elseif($row['prev_last_seq_status'] == 'T'){
                                        $remarks = 'Action Taken: <b>' . $row['prev_action_taken'] . '</b>';
                                    }
                                }else{ //receieved
                                    $last_rel_datetime = '';
                                    $last_rcv_datetime = $row['prev_last_seq_date_rcv'] . " " . $row['prev_last_seq_time_rcv'];
                                    $remarks = 'Last Received at: <br><b>' . $row['current_office'] . '</b><br><b>' . $last_rcv_datetime . '</b>';
                                }
                            }else{
                                 if(in_array($row['last_seq_status'], $released)){ //eleased - forwarded - returned
                                    $last_rel_datetime = $row['last_seq_release_date'] . " " . $row['last_seq_release_time'];
                                    $last_rcv_datetime = $row['last_seq_date_rcv'] . " " . $row['last_seq_time_rcv'];
                                }elseif(in_array($row['last_seq_status'], $action_done)){ //action - done
                                    $last_rel_datetime = $row['last_seq_date_action'] . " " . $row['last_seq_time_action'];
                                    $last_rcv_datetime = $row['last_seq_date_rcv'] . " " . $row['last_seq_time_rcv'];
                                    if($row['last_seq_status'] == 'I'){
                                        $remarks = $row['last_seq_remarks2'];
                                    }elseif($row['last_seq_status'] == 'T'){
                                        $remarks = 'Action Taken: <b>' . $row['last_action_taken'] . '</b>';
                                    }
                                }else{ //received
                                    $last_rel_datetime = '';
                                    $last_rcv_datetime = $row['last_seq_date_rcv'] . " " . $row['last_seq_time_rcv'];
                                    $remarks = 'Last Received at: <br><b>' . $row['current_office'] . '</b><br><b>' . $last_rcv_datetime . '</b>';
                                }
                            }

                            $datetime_rcv = $this->customobj->calculateTime24Hrs($first_rcv_datetime, $last_rcv_datetime);
                            $datetime_rel = $last_rel_datetime ? $this->customobj->calculateTime24Hrs($first_rcv_datetime, $last_rel_datetime) : ['days' => '', 'hours' => '', 'minutes' => '', 'total_seconds' => ''];


                            $table_data[] = [
                                'cnt' => $cnt,
                                'docno' => $row['dcon'],
                                'originating' => $row['orig_office'],
                                'current' => $row['current_office'],
                                'subject' => "<strong>".htmlspecialchars($row['subject'])."</strong> " . $datetime_rel['minutes'],
                                'doctype' => str_replace(",", ", ", $row['type_descs']),
                                'rcv_day' => $datetime_rcv['days'],
                                'rcv_hours' => $datetime_rcv['hours'],
                                'rcv_minutes' => $datetime_rcv['minutes'],
                                'rel_day' => $datetime_rel['days'],
                                'rel_hours' => $datetime_rel['hours'],
                                'rel_minutes' => $datetime_rel['minutes'],
                                'doc_remarks' => $remarks,
                            ];
                            
                        }

                        if($officeFilter){
                            $getOfficeDataById = $this->OfficeModel->getOfficeDataById($officeFilter);
                            $office = $getOfficeDataById['officename'];
                        }else{
                            $office = '';
                        }
                        

                        $data = [
                            "draw" => intval($this->request->getPost('draw')),
                            "recordsTotal" => $timelineQuery['totalRecords'],
                            "recordsFiltered" => $timelineQuery['recordsFiltered'],
                            "data" => $table_data,
                            "office" => $office,
                            "report_date" => '',
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
 