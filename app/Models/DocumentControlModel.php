<?php

namespace App\Models;
use App\Models\DocumentDetailModel;
use App\Models\audittrailmodel;

use CodeIgniter\Model;

class DocumentControlModel extends Model
{

    protected $table      = 'doccontrol';
    
    protected $primaryKey = 'doc_controlno';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['route_no', 'doc_controlno', 'emp_entry', 'modified_by'];

    public $documentdetailmodel;
    public $audittrailmodel;

    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();

        $this->documentdetailmodel = new DocumentDetailModel();
        $this->audittrailmodel = new audittrailmodel();
    }

    public function generateDocumentControlNo(){

        $this->db->transStart();

        $year = date('Y');

        $uniqueCode = '';

        do {

            $maxSequence = $this->select('MAX(doc_controlno) as maxdocno')
                                ->like('doc_controlno', $year.'-')
                                ->first();

            $currentSequence = 0;
            if ($maxSequence && $maxSequence['maxdocno']) {
                $currentSequence = (int)substr($maxSequence['maxdocno'], 6);
            }

            $newSequence = str_pad($currentSequence + 1, 9, '0', STR_PAD_LEFT);
            $uniqueCode = "D{$year}-{$newSequence}";


            $existingCode = $this->where('doc_controlno', $uniqueCode)->first();
        
        } while ($existingCode);


        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Failed to generate unique code');
        }

        return $uniqueCode;

    }

    public function insertDocumentControl($docdetaildata){

        $this->db->transStart();

        try {

            $this->insert($docdetaildata);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to insert document control.');
            }

            $this->audittrailmodel->insertAuditTrail($docdetaildata['doc_controlno'], 'doccontrol', $docdetaildata['emp_entry'], 'INSERT');
            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to insert document data. Error: Audit Trail.');
            }

            $insertDocumentDetail = $this->documentdetailmodel->insertDocumentDetail($docdetaildata);
            
            if(!$insertDocumentDetail['success']){
                throw new \Exception($insertDocumentDetail['message']);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Adding of Document Control failed.');
            }

            return [
                'success' => true,
                'message' => "Successfully added Document Destination.",
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


    public function getDocControl($routeno, $status = true){

        $builder = $this->db->table('doccontrol dc');
        $builder->select([
            'dc.route_no as routeno',
            'dc.doc_controlno as docno',
            'dc.created_date',
            'dd.*',
            'dd.status as docstatus',
            'next_dd.date_rcv as next_date_rcv',
            'next_dd.time_rcv as next_time_rcv',
            'o.officename',
            'o.shortname',
            'ao.*',
            'aorcv.lastname as rcv_lastname',
            'aorcv.firstname as rcv_firstname',
            'aorcv.middlename as rcv_middlename',
            'aorcv.office_rep as rcv_orep',
            'aoact.lastname as act_lastname',
            'aoact.firstname as act_firstname',
            'aoact.middlename as act_middlename',
            'aoact.office_rep as act_orep',
            'aorel.lastname as rel_lastname',
            'aorel.firstname as rel_firstname',
            'aorel.middlename as rel_middlename',
            'aorel.office_rep as rel_orep',
            'ar.*',
            'at.*',
        ]);
        $builder->join('docdetails dd', 'dc.doc_controlno = dd.doc_controlno', 'left');
        $builder->join('docdetails next_dd', '(dd.doc_controlno = next_dd.doc_controlno AND next_dd.sequence_no = dd.sequence_no + 1)', 'left');
        $builder->join('office o', 'dd.office_destination = o.officecode', 'left');
        $builder->join('action_officer ao', 'dd.action_officer = ao.empcode AND ao.deleted_at IS NULL', 'left');
        $builder->join('action_officer aorcv', 'dd.receive_by = aorcv.empcode AND aorcv.deleted_at IS NULL', 'left');
        $builder->join('action_officer aoact', 'dd.action_by = aoact.empcode AND aoact.deleted_at IS NULL', 'left');
        $builder->join('action_officer aorel', 'dd.release_by = aorel.empcode AND aorel.deleted_at IS NULL', 'left');
        $builder->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left');
        $builder->join('action_taken at', 'dd.action_code = at.action_code', 'left');
        $builder->where('dc.route_no', $routeno);

        if($status){
            $builder->where('dc.is_deleted', 0);
            $builder->where('dc.control_status', 'Active');
            $builder->where('dd.is_deleted', 0);
            $builder->where('dd.detail_status', 'Active');
        }

        $builder->orderBy('dc.doc_controlno', 'DESC');
        $builder->orderBy('dd.sequence_no', 'ASC');
        $query = $builder->get();
        
        $result = $query->getResultArray();

        return $result;

    }


    
}

