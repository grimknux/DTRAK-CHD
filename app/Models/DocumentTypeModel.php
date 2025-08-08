<?php

namespace App\Models;
use App\Models\audittrailmodel;

use CodeIgniter\Model;

class DocumentTypeModel extends Model
{

    protected $table      = 'doc_type';
    
    protected $primaryKey = 'type_code';

    protected $useAutoIncrement = false;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['type_code','type_desc','status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    
    public $audittrailmodel;

    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
        
        $this->audittrailmodel = new audittrailmodel();
    }

    public function getDocType(){

        $doctype = $this->where('status', 'Active')->orderBy('type_desc', 'ASC')->findAll();
        return $doctype;
        
    }

    public function get_doc_types(){

       try {

            $result = $this->where('deleted_at IS NULL', null, false)->orderBy('type_desc', 'ASC')->findAll();

            if (!empty($result)) {
                return $result;
            } else {
                log_message('error', 'Result Query Returned 0 Rows');
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in get_doc_type(): ' . $e->getMessage());
            return false;
        }

    }

    public function get_doc_type($type_code, $status="Active")
    {
        try {
            $builder = $this->db->table('doc_type');
            $builder->select('*');
            $builder->where('type_code', $type_code);
            $builder->where('status', $status);
        
            $result = $builder->get()->getRowArray();

            if ($result !== null) {
                return $result;
            } else {
                log_message('error', 'get_user: No active Document type found for type code: ' . $type_code);
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'get_user Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function insert_document_type($data){
        
        try{

            $this->db->transStart();

            if (!$this->insert($data)) {
                throw new \Exception("An error occurred during the creation of Document Type.");
            }

            $this->audittrailmodel->insertAuditTrail($data['type_code'], 'doc_type', session()->get('logged_user'), 'INSERT');
            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update Document Type. Error: Audit Trail.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while saving Document Type.");
            }

            return [
                'success' => true,
                'message' => 'Successfully Added Document Type.'
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error creating Document Type :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function update_document_type($id,$data)
    {

        $oldData = $this->find($id);

        try{

            $this->db->transStart();

            if(!$this->update($id,$data)){
                throw new \Exception("An error occurred during the updating of Document Type.");
            }

            $comparedData = $this->audittrailmodel->compareUpdateData((array) $oldData, $data);
            $this->audittrailmodel->insertAuditTrailForUpdate($id, $this->table, $comparedData, session()->get('logged_user'));

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while updating Document Type.");
            }

            return [
                'success' => true,
                'message' => 'Successfully Updated Document Type.'
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error updating Document Type :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function delete_document_type($id,$data){

        $oldData = $this->find($id);
        try{

            $this->db->transStart();
            
            $message = "Successfully Deleted Document Type.";
            if(!$this->update($id,$data)){
                throw new \Exception("An error occurred during the updating of Document Type.");
            }

            if(!$this->isDocTypeInUse($id)){
                if(!$this->delete($id)){
                    throw new \Exception("An error occurred during the deleting of Document Type.");
                }

                $this->audittrailmodel->insertAuditTrailSoftDelete($id, 'doc_type', 'DELETE');

            }else{
                $message = "Document Type already used in transactions. Deactivated Document Type.";
            }

            $comparedData = $this->audittrailmodel->compareUpdateData((array) $oldData, $data);
            $this->audittrailmodel->insertAuditTrailForUpdate($id, $this->table, $comparedData, session()->get('logged_user'));
            
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while deleting Document Type.");
            }

            return [
                'success' => true,
                'message' => $message
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error deleting Document Type:{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function getCurrentDoctype($routeno){
        $builder = $this->db->table('registry_doctype');
        $builder->select('type_code');
        $builder->where('route_no', $routeno);
        $result = $builder->get()->getResultArray();

        return $result;
    }

   public function document_type_exists(string $type_desc, ?string $excludeId = null): bool
    {
        $builder = $this->db->table('doc_type');

        $builder->where('type_desc', $type_desc)
                ->where('deleted_at IS NULL', null, false);

        if (!empty($excludeId)) {
            $builder->where('type_code !=', $excludeId); // Exclude current record on edit
        }

        return $builder->countAllResults() > 0;
    }

    public function generate_simple_code()
    {
        $this->db->transStart();

        $uniqueCode = '';

        do {
            $maxSequence = $this->db->table('doc_type')->select('MAX(type_code) as maxtypecode')->get()->getRowArray();

            $currentSequence = 0;
            if ($maxSequence && $maxSequence['maxtypecode']) {
                $currentSequence = (int)$maxSequence['maxtypecode'];
            }

            $newSequence = str_pad($currentSequence + 1, 5, '0', STR_PAD_LEFT);
            $uniqueCode = $newSequence;

            $existingCode = $this->builder()
                     ->where('type_code', $uniqueCode)
                     ->where('deleted_at IS NULL', null, false)
                     ->get()
                     ->getRow();

        } while ($existingCode);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Failed to generate Document Type Code');
        }

        return $uniqueCode;
    }

    private function isDocTypeInUse(string $id): bool
    {
        // Check in docregistry
        $builder = $this->db->table('registry_doctype');
        $builder->where('type_code', $id);
        $usedInRegistryDoctype = $builder->countAllResults();

        return ($usedInRegistryDoctype > 0 || $usedInRegistryDoctype > 0);
    }

    
}

