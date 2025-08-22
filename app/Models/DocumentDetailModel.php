<?php

namespace App\Models;
use App\Models\audittrailmodel;
use App\Models\IncomingModel;

use CodeIgniter\Model;

class DocumentDetailModel extends Model
{

    protected $table      = 'docdetails';
    
    protected $primaryKey = 'doc_detailno';

    protected $useAutoIncrement = false;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['doc_detailno','route_no','doc_controlno','sequence_no', 'prev_sequence_no', 'office_destination','action_officer', 'action_required', 'entry_by', 'emp_entry', 'status', 'no_page', 'receive_by','date_rcv','time_rcv','datelog_rcv','timelog_rcv','action_by', 'action_code', 'date_action', 'time_action', 'datelog_action', 'timelog_action','release_by','release_date','release_time','remarks','remarks2', 'ifreturn', 'ifdisseminate', 'office_fwd_return', 'sourcetype', 'filename','attachlist', 'date_required', 'time_required', 'date_log', 'time_log', 'detail_status', 'is_deleted', 'modified_by', 'modified_date'];

    public $audittrailmodel;
    public $IncomingModel;


    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();

        $this->audittrailmodel = new audittrailmodel();
        $this->IncomingModel = new IncomingModel();
    }


    public function updateStatus($id, $data)
    {
        $this->db->transStart();

        try {

            if (!$this->update($id, $data)) {
                throw new \Exception('Failed to update document status.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update document status.');
            }

            return [
                'success' => true,
                'message' => ''
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }


    public function updateStatusBulk($id, $data, $officecode, $type)
    {
        $this->db->transStart();

        try {
            
            foreach ($id as $row) {
                $detail_id = $row['rowId'];
                if($type == 'action'){
                    $data['action_code'] = $row['actiondone'];
                }

                if($this->checkDocIfValid($officecode,$detail_id)){

                    if (!$this->update($detail_id, $data)) {
                        throw new \Exception('Failed to update document status for row: ' . $detail_id);
                    }

                }else{

                    throw new \Exception('Document is invalid for row: ' . $detail_id);

                }

            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update document status.');
            }

            return [
                'success' => true,
                'message' => ''
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }


    public function releaseBulk($id, $inputdata, $officecode, $logged_user, $models)
    {
        $this->db->transStart();

        try {

            $officedestination = $inputdata['officedestination'];
            $actionofficer = $inputdata['actionofficer'];
            $actionrequire = $inputdata['actionrequire'];
            $remarks = $inputdata['remarks'];
            $status = "T";

            //models
            $customobj = $models['customobj'];
            $IncomingModel = $models['IncomingModel'];
            $UserModel = $models['UserModel'];

            $insertdata = [];
            foreach ($id as $row) {

                $detail_id = $row['rowId'];

                $getuser = $UserModel->getUser($logged_user,$detail_id,"incoming");
                if(!$getuser['success']){
                    throw new \Exception("{$getuser['message']}");
                }
                
                $data = [

                    'status' => 'O',
                    'release_by' => $logged_user,
                    'release_date' => date('Y-m-d'),
                    'release_time' => date('H:i:s'),
                    'remarks2' => $remarks,
                    
                ];
                
                $receiveData = $this->IncomingModel->receiveData($detail_id,$status);

                if (!$receiveData) { 
                    throw new \Exception("Failed to retrieve Document Data.:" . $detail_id . " - " . $status);
                }

                if ($this->checkDocIfValid($officecode, $detail_id) === false) {
                    throw new \Exception("Document is invalid for row: {$detail_id}");
                }

                $generateDocumentDetailNo = $this->generateDocumentDetailNo();

                if (!$generateDocumentDetailNo) {
                    throw new \Exception("Failed to generate a document detail number.");
                }

                $seqno = $receiveData['seqno'] + 1;

                $checkSeqExists = $this->checkSeqExists($receiveData['dcon'],$seqno);
                
                if($checkSeqExists){
                    throw new \Exception("Sequence number already exists for document: ".$receiveData['dcon']);
                }

                $insertdata = [

                    'doc_detailno' => $generateDocumentDetailNo,
                    'route_no' => $receiveData['routeno'],
                    'doc_controlno' => $receiveData['dcon'],
                    'sequence_no' => $receiveData['seqno'] + 1,
                    'prev_sequence_no' => $receiveData['seqno'],
                    'office_destination' => $officedestination,
                    'action_officer' => $actionofficer,
                    'action_required' => $actionrequire,
                    'entry_by' => $customobj->convertEMP($getuser['data']['lastname'], $getuser['data']['firstname'], $getuser['data']['middlename'], $getuser['data']['office_rep']),
                    'emp_entry' => $logged_user,
                    'no_page' => $receiveData['pageno'],
                    'remarks' => $remarks,
                    'date_log' => date('Y-m-d'),
                    'time_log' => date('H:i:s'),
                    'modified_by' => $logged_user,
                    
                ];


                //ACTION
                if (!$this->update($detail_id, $data)) {
                    throw new \Exception("Failed to update document status for row: {$detail_id}.");
                }

                $insertDocumentDetail = $this->insertDocumentDetail($insertdata);

                if($insertDocumentDetail['success'] === false) {
                    throw new \Exception("Failed to release document. " . $insertDocumentDetail['message']);
                }

            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to release document.');
            }


            return [
                'success' => true,
                'message' => 'Documents released successfully.',
                'data' => $insertdata
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error releasing bulk documents :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }


    private function checkSeqExists($controlno,$seqno){

        $document = $this->where('doc_controlno', $controlno)
                         ->where('sequence_no', $seqno)
                         ->where('detail_status', 'Active')
                         ->first();

        if($document){
            return true;
        }

        return false;
    }

    public function checkDocIfValid($office,$docdetailno){

        $document = $this->whereIn('office_destination', $office)
                         ->where('doc_detailno', $docdetailno)
                         ->where('detail_status', 'Active')
                         ->first();

        return $document ?: false;
    }


    public function checkIfReceived($routeno){

        $query = $this->where('route_no', $routeno)
                  ->where('sequence_no', '1')
                  ->get()->getResultArray();

        if (empty($query)) {
            return true;
        }

        foreach ($query as $row) {
            if ($row['status'] !== 'A') {
                return false;
            }
        }

        return true;   

    }

    public function generateDocumentDetailNo(){

        $this->db->transStart();

        $year = date('Y');

        $uniqueCode = '';

        do {

            $maxSequence = $this->select('MAX(doc_detailno) as maxdetailno')
                                ->like('doc_detailno', $year.'-')
                                ->first();
            //$maxSequence = $this->db->table('docdetails')->select('MAX(doc_detailno) as maxdetailno')->like('doc_detailno', $year.'-')->get()->getRowArray();

            $currentSequence = 0;
            if ($maxSequence && $maxSequence['maxdetailno']) {
                $currentSequence = (int)substr($maxSequence['maxdetailno'], 6);
            }

            $newSequence = str_pad($currentSequence + 1, 9, '0', STR_PAD_LEFT);
            $uniqueCode = "{$year}-{$newSequence}";


            $existingCode = $this->where('doc_detailno', $uniqueCode)->first();
        
        } while ($existingCode);


        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return $uniqueCode;

    }


    public function insertDocumentDetail($docdetaildata) {

        try {

            if (!$this->insert($docdetaildata)) {
                throw new \Exception('Failed to insert document destinations.');
            }
            
            if (!$this->audittrailmodel->insertAuditTrail($docdetaildata['doc_detailno'], 'docdetail', $docdetaildata['emp_entry'], 'INSERT')) {
                throw new \Exception('Failed to insert document destination. Error: Audit Trail.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Adding of Document Destination failed.');
            }
    
            return [
                'success' => true,
            ];
    
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage()); 

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getOfficeDest($routeno){

        return $this->where('route_no', $routeno)
                    ->where('sequence_no', '1')
                    ->where('is_deleted', 0)
                    ->where('detail_status', 'Active')
                    ->findAll();

    }

    public function getOfficeDestChange($routeno,$controlno){

        return $this->where('route_no', $routeno)
                    ->where('sequence_no', '1')
                    ->where('doc_controlno !=', $controlno)
                    ->where('detail_status =', 'Active')
                    ->where('is_deleted =', 0)
                    ->findAll();

    }

    public function checkIfDestinationExists($routeno){
        $tenMinutesAgo = date('Y-m-d H:i:s', strtotime('-10 minutes'));

        $query = $this->where('route_no', $routeno)
                      ->where('sequence_no', '1')
                      ->where('detail_status', 'Active')
                      ->where('is_deleted', '0')
                      ->where('created_date >=', $tenMinutesAgo)
                      ->get();

        if ($query->getNumRows() > 0) {
            return true;
        } else {
            $queryNoRows = $this->where('route_no', $routeno) ->where('detail_status', 'Active')->where('is_deleted', '0')->get();
    
            if ($queryNoRows->getNumRows() === 0) {
                return true;
            } else {
                return false;
            }
        }
    }


    public function checkIfDestinationExistsValidation($routeno, $officedes, $controlno = null){
        
        $builder = $this->where('route_no', $routeno);
        $builder->where('office_destination', $officedes);
        $builder->where('sequence_no', '1');
        $builder->where('detail_status', 'Active');
        $builder->where('is_deleted', '0');
        
        if ($controlno !== null) {
            $builder->where('doc_controlno !=', $controlno);
        }

        $query = $builder->get();

        if ($query->getNumRows() === 0) {
            return true;
        } else {
            return false;
        }
        
    }

    /*public function checkIfDestinationExistsValidation($docdetail, $officecode)
    {
        $builder = $this->db->table('docdetails dd');
        $builder->join('docregistry dr', 'dd.route_no = dr.route_no');
        $builder->where('dd.doc_detailno', $docdetail);
        $builder->where('dr.officecode', $officecode);
        
        $query = $builder->get();
        
        if ($query->getNumRows() === 0) {
            return false;
        } else {
            return true;
        }
    }*/

    public function checkIfDestExists($routeno)
    {
        $builder = $this->db->table('docdetails dd')
            ->where('dd.route_no', $routeno)
            ->where('detail_status', 'Active')
            ->where('is_deleted', '0');

        return $builder->countAllResults() === 0;
    }


    public function getDetailData($doc_detailno){

        $builder = $this->db->table('docdetails dd');
        $builder->select([
            'dd.*',
            'dr.route_no as routeno',
            'o.officename as officename',
            'o.shortname as officeshort',
            'ao.*',
            'ar.*'
        ]);
        $builder->join('office o', 'dd.office_destination = o.officecode', 'left');
        $builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');
        $builder->join('action_officer ao', 'dd.action_officer = ao.empcode AND ao.deleted_at IS NULL', 'left');
        $builder->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left');
        $builder->where('dd.doc_detailno', $doc_detailno);
        $query = $builder->get();
        
        $result = $query->getRowArray();

        return $result;
    }


    public function updateDestination($changeDesData){

        $this->db->transStart();

        $doc_detailno = $changeDesData['doc_detailno'];
        $modified_by = $changeDesData['modified_by'];

        unset($changeDesData['doc_detailno']);

        $oldData = $this->find($doc_detailno);

        try {

            $this->update($doc_detailno, $changeDesData);
            unset($changeDesData['modified_date']);

            $comparedData = $this->audittrailmodel->compareUpdateData((array) $oldData, $changeDesData);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to change destination.');
            }

            $this->audittrailmodel->insertAuditTrailForUpdate($doc_detailno, $this->table, $comparedData, $modified_by);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to change destination. Error: Audit Trail.');
            }
   
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Changing of destination failed.');
            }


            return [
                'success' => true,
                'message' => "Successfully changed destination.",
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }


    public function deleteThisDestination($id,$user){
        $this->db->transStart();

        try {

            $this->update($id, ['detail_status' => 'Inactive', 'is_deleted' => '1']);
            $this->db->table('doccontrol')->where('doc_controlno', $id)
                    ->update([
                        'control_status' => 'Inactive',
                        'is_deleted'    => 1
                    ]);

            $this->audittrailmodel->insertAuditTrail($id, 'docdetails', $user, 'DELETE');
            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to delete destination. Error: Audit Trail.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to delete destination.');
            }

            return [
                'success' => true,
                'message' => "Office Destination has been deleted.",
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }


    public function deleteThisDestination2($id,$user){
        
        $this->db->transStart();

        try {

            $this->db->table('docdetails')->where('doc_controlno', $id)
                    ->update([
                        'detail_status' => 'Inactive',
                        'is_deleted'    => 1
                    ]);
            
            $this->db->table('doccontrol')->where('doc_controlno', $id)
                    ->update([
                        'control_status' => 'Inactive',
                        'is_deleted'    => 1
                    ]);

            $this->audittrailmodel->insertAuditTrail($id, 'docdetails', $user, 'DELETE');

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to delete destination.');
            }

            return [
                'success' => true,
                'message' => 'Office Destination has been deleted.',
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }


    public function checkOfficeIfValid($officeCodes, $docRegistryNo)
    {
        $document = $this->db->table('docregistry')
                            ->whereIn('officecode', $officeCodes)
                            ->where('route_no', $docRegistryNo)
                            ->where('is_deleted', 0)
                            ->where('registry_status', 'Active')
                            ->first();

        return $document !== null;
    }


    public function undoneStatus($docdetail, $data)
    {
        $this->db->transStart();

        try {

            if (!$this->updateStatus($docdetail, $data)) {
                throw new \Exception('Failed to update document status.');
            }

            if (!$this->audittrailmodel->insertAuditTrail($docdetail, 'docdetail', $data['modified_by'], 'UPDATE')) {
                throw new \Exception('Failed to update document status. Error: Audit Trail.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update document status.');
            }

            return [
                'success' => true,
                'message' => ''
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }



    
}

