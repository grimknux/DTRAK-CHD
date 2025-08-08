<?php

namespace App\Controllers\Reports;
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


class DocumentReleased extends BaseController
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
        }
        
        $navi_bread = "<li>Report</li>
        <li>Released Documents</li>";

        $getEmpOffice = $this->OfficeModel->getEmpOffice(session()->get('logged_user'));

        $data = [
            //'header' => 'Released and Processed (Tagged as "Done") Documents',
            'header' => '<i class="fa fa-file-text-o"></i> Reports',
            'navactive' => 'report_tables',
            'navsubactive' => 'report_release',
            'bread' => $navi_bread,
            'getEmpOffice' => $getEmpOffice,
            
            'admin' => $admin,
            'admin_menu' => $admin_menu,
        ];

        return view('reports/document-released', $data);
    }


    public function reportReleased(){

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
                        $getOfficeCode = $this->OfficeModel->getOfficeCode(session()->get('logged_user'));
                        $status = ['O','I','F'];

                        $limit = $this->request->getPost('length');   // Rows per page
                        $offset = $this->request->getPost('start'); 
                        $searchValue = $this->request->getPost('search')['value'] ?? null;  

                        $datefromFilter = $this->request->getPost('datefromFilter') ? $this->request->getPost('datefromFilter')  : "";
                        $datetoFilter = $this->request->getPost('datetoFilter') ? $this->request->getPost('datetoFilter')  : "";
                        $officeempFilter = $this->request->getPost('officeempFilter') ? $this->request->getPost('officeempFilter')  : "";

                        $filterValue = [
                            'datefromFilter' => $datefromFilter,
                            'datetoFilter' => $datetoFilter,
                            'officeempFilter' => $officeempFilter,
                        ];

                        $documentQuery = $this->ReportModel->documentQuery($getOfficeCode, $status, $limit, $offset, $searchValue, $filterValue);
                        $cnt = $offset;

                        if(!$documentQuery['success']){
                                throw new \Exception($documentQuery['message']);
                        }

                        foreach ($documentQuery['data'] as $row) {
                            $cnt++;
                            $process_detail = "";

                            if ($row['stats'] == 'O') {
                                $process_detail = '
                                    Date Released: <b>' . (($row['release_date']) ? date('M d Y', strtotime($row['release_date'])) . " " . date('h:i:s a', strtotime($row['release_time'])) : "N/A") . '</b>
                                    Released to: <b>' . (($row['nextoffice']) ? $row['nextoffice'] : "N/A") . '</b> <br>
                                    Received Date: <b>' . (($row['dest_date_rcv']) ? date('M d Y', strtotime($row['dest_date_rcv'])) . " " . date('M d Y', strtotime($row['dest_time_rcv'])) : "N/A") . '</b>
                                ';
                            }elseif ($row['stats'] == 'I') {
                                $process_detail = '
                                    Filed: <br>'.$row['d_rem2'].'
                                ';
                            }elseif ($row['stats'] == 'F') {
                                $process_detail = '
                                    Forwarded to: <b>' . (($row['nextoffice']) ? $row['nextoffice'] : "N/A") . '</b> <br>
                                    Received Date: <b>' . (($row['dest_date_rcv']) ? date('M d Y', strtotime($row['dest_date_rcv'])) . " " . date('h:i:s a', strtotime($row['dest_time_rcv'])) : "N/A") . '</b>
                                ';
                            }


                            $table_data[] = [
                                'cnt' => $cnt,
                                'docno' => $row['dcon'],
                                'originating' => $row['origoffice'],
                                'previous' => $row['prevoffice'],
                                'subject' => "<strong>".htmlspecialchars($row['subject'])."</strong>",
                                'doctype' => str_replace(",", ", ", $row['ddoctype']),
                                'date_time_rcv' => date('M d, Y', strtotime($row['date_rcv'])) . "<br>" . date('h:i:s A', strtotime($row['time_rcv'])),
                                'rcv_by' => $row['rec_lastname'] . ", " . $row['rec_firstname'] . " " . $row['rec_middlename'],
                                'date_time_act' => date('M d, Y', strtotime($row['date_action'])) . "<br>" . date('h:i:s A', strtotime($row['time_action'])),
                                'act_by' => $row['act_lastname'] . ", " . $row['act_firstname'] . " " . $row['act_middlename'],
                                'act_taken' => $row['actiondesc'],
                                'process_detail' => $process_detail
                            ];
                            
                        }

                        $getOfficeDataById = $this->OfficeModel->getOfficeDataById($officeempFilter);
                        
                        if(($datefromFilter != "" && $datetoFilter != "") && ($datefromFilter != $datetoFilter) ){
                            $report_date = "REPORT from <strong>" . date('F j, Y', strtotime($datefromFilter)) . "</strong> to <strong>" . date('F j, Y', strtotime($datetoFilter)) . "</strong>";
                        }else if($datefromFilter != "" && $datetoFilter == ""){
                            $report_date = "REPORT from <strong>" . date('F j, Y', strtotime($datefromFilter)) . "</strong> to <strong>" . date('F j, Y') . "</strong>";
                        }else if($datefromFilter == "" && $datetoFilter != ""){
                            $report_date = "REPORT as of <strong>" . date('F j, Y', strtotime($datetoFilter)) . "</strong>";
                        }else if(($datefromFilter == $datetoFilter) && ($datefromFilter != "" && $datetoFilter != "")){
                            $report_date = "REPORT for<strong>" . date('F j, Y', strtotime($datetoFilter)) . "</strong>";
                        }else{
                            $report_date = "REPORT as of <strong>" . date('F j, Y') . "</strong>";
                        }

                        $data = [
                            "draw" => intval($this->request->getPost('draw')),
                            "recordsTotal" => $documentQuery['totalRecords'],
                            "recordsFiltered" => $documentQuery['recordsFiltered'],
                            "data" => $table_data,
                            "office" => $getOfficeDataById['officename'],
                            "report_date" => $report_date,
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
 