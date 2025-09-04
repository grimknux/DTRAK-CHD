<?php

namespace App\Models;
use App\Models\audittrailmodel;

use CodeIgniter\Model;

class ActionModel extends Model
{
    protected $table      = 'action_required';
    
    protected $primaryKey = 'reqaction_code';

    protected $useAutoIncrement = false;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['reqaction_code', 'reqaction_desc', 'reqaction_done', 'act_rstatus', 'created_at', 'updated_at', 'deleted_at'];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    private $audittrailmodel;

    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
        
        $this->audittrailmodel = new audittrailmodel();
    }

    public function getActionRequired(){
        
        try {

        $result = $this->orderBy('reqaction_desc ASC')->findAll();

        return [
            'success' => true,
            'data' => $result,
        ];

        } catch (\Exception $e) {

            return ['success' => false, 'message' => $e->getMessage()];

        }

    }

    public function getActionRequire()
    {
        try {
            $builder = $this->db->table('action_required ar');
            $builder->select('ar.*, at.action_desc');
            $builder->where('ar.deleted_at IS NULL', null, false);
            $builder->join('action_taken at', 'at.action_code = ar.reqaction_done', 'left');
            $builder->orderBy('ar.reqaction_desc', 'ASC');

            $result = $builder->get()->getResultArray();

            return $result;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function action_required_exists(string $type_desc, ?string $excludeId = null): bool
    {
        $query = $this->where('reqaction_desc', $type_desc)
                ->where('deleted_at IS NULL', null, false);

        if (!empty($excludeId)) {
            $query->where('reqaction_code !=', $excludeId); // Exclude current record on edit
        }

        return $query->countAllResults() > 0;
    }

    public function getActionRequireReturn(){

        return $this->whereIn('reqaction_code', ['00025', '00070', '00075'])
                    ->orderBy('reqaction_code ASC')
                    ->findAll();

    }

    public function getActionDone(){

        $builder = $this->db->table('action_taken');
        $builder->orderBy('action_desc', 'asc');
        $query = $builder->get()->getResultArray();
        
        return $query;

    }

    public function getActionTaken(){

        $builder = $this->db->table('action_taken');
        $builder->where('action_desc', 'asc');
        $builder->orderBy('action_desc', 'asc');
        $query = $builder->get()->getResultArray();
        return $actionrequired;
        
    }


    public function checkActionTaken($actioncode){
        
        $builder = $this->db->table('action_taken');
        $builder->where('action_code', $actioncode);

        $query = $builder->get()->getRowArray();

        if (empty($query)) {
            return false;
        } else {
            return true;
        }
    }

    /*public function getActionByRequire($actionrequire){

        return $this->where('reqaction_code', $actionrequire)->first();
    }*/

   public function getActionByRequire($reqaction_code, $status = "Active")
    {
        try {
            $builder = $this->db->table('action_required'); // 👈 replace with your actual table name
            $builder->select('*');
            $builder->where('reqaction_code', $reqaction_code);
            $builder->where('act_rstatus', $status);

            $result = $builder->get()->getRowArray();

            if ($result !== null) {
                return $result;
            } else {
                log_message('error', 'getActionByRequire: No active action found for code: ' . $reqaction_code);
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'getActionByRequire Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function get_action_required_active(){

       try {

            $result = $this->where('act_rstatus', 'Active')->where('deleted_at IS NULL', null, false)->orderBy('reqaction_desc', 'ASC')->findAll();

            if (!empty($result)) {
                return $result;
            } else {
                log_message('error', 'Result Query Returned 0 Rows');
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in get_action_required(): ' . $e->getMessage());
            return false;
        }

    }


    public function get_action_required(){

       try {

            $result = $this->where('deleted_at IS NULL', null, false)->where('deleted_at IS NULL', null, false)->orderBy('reqaction_desc', 'ASC')->findAll();

            if (!empty($result)) {
                return $result;
            } else {
                log_message('error', 'Result Query Returned 0 Rows');
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in get_action_required(): ' . $e->getMessage());
            return false;
        }

    }

    public function insert_action_required($data){
        
        try{

            $this->db->transStart();

            if (!$this->insert($data)) {
                throw new \Exception("An error occurred during the creation of Action Required.");
            }

            $this->audittrailmodel->insertAuditTrail($data['reqaction_code'], 'action_required', session()->get('logged_user'), 'INSERT');
            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update Action Required. Error: Audit Trail.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while saving Action Required.");
            }

            return [
                'success' => true,
                'message' => 'Successfully Added Action Required.'
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error creating Action Required :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function update_action_required($id,$data)
    {

        $oldData = $this->find($id);

        try{

            $this->db->transStart();

            if(!$this->update($id,$data)){
                throw new \Exception("An error occurred during the updating of Action Required.");
            }

            $comparedData = $this->audittrailmodel->compareUpdateData((array) $oldData, $data);
            $this->audittrailmodel->insertAuditTrailForUpdate($id, $this->table, $comparedData, session()->get('logged_user'));

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while updating Action Required.");
            }

            return [
                'success' => true,
                'message' => 'Successfully Updated Action Required.'
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error updating Action Required :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }
    

    public function generate_action_required_code(): string
    {
        $this->db->transStart();

        // Get the max existing type_code
        $maxSequence = $this->db->table('action_required')
            ->select('MAX(reqaction_code) as maxactreqcode')
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        $currentSequence = 0;
        if ($maxSequence && $maxSequence['maxactreqcode']) {
            $currentSequence = (int)$maxSequence['maxactreqcode'];
        }

        // Increment and pad to 5 digits
        $newSequence = str_pad($currentSequence + 1, 5, '0', STR_PAD_LEFT);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Failed to generate Action Required Code');
        }

        return $newSequence; // e.g. 00001
    }


    public function delete_action_required($id,$data){

        $oldData = $this->find($id);
        try{

            $this->db->transStart();
            
            $message = "Successfully Action Required.";
            if(!$this->update($id,$data)){
                throw new \Exception("An error occurred during the updating of Action Required.");
            }

            if(!$this->isActReqInUse($id)){
                if(!$this->delete($id)){
                    throw new \Exception("An error occurred during the deleting of Action Required.");
                }

                $this->audittrailmodel->insertAuditTrailSoftDelete($id, 'reqaction_code', 'DELETE');

            }else{
                $message = "Action Required already used in transactions. Deactivated Action Required.";
            }

            $comparedData = $this->audittrailmodel->compareUpdateData((array) $oldData, $data);
            $this->audittrailmodel->insertAuditTrailForUpdate($id, $this->table, $comparedData, session()->get('logged_user'));
            
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while deleting Action Required.");
            }

            return [
                'success' => true,
                'message' => $message
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error deleting Action Required: {$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    private function isActReqInUse(string $id): bool
    {

        $builder = $this->db->table('docdetails');
        $builder->where('action_required', $id);
        $result = $builder->countAllResults();

        return ($result > 0 || $result > 0);
    }

}

