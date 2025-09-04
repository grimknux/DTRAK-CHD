<?php

namespace App\Models;
use App\Models\audittrailmodel;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'action_officer';
    protected $primaryKey = 'empid';

    protected $useAutoIncrement = false;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['empcode', 'empid', 'lastname', 'firstname', 'middlename', 'officecode', 'userpass', 'password', 'userlevel', 'office_rep', 'date_expiration', 'email', 'created_at', 'status', 'updated_at', 'deleted_at', 'modby', 'update_status', 'admin_menu'];

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

    public function getUser($id,$docno = "",$type = "")
    {
        try {

            $userData = $this->where('status', 'A')->where('empcode', $id)->get()->getRowArray();

            $officeMatch = true;

            if (!$userData) {
                throw new \Exception('You are not a valid user.');
            }

            if($type == "incoming"){
                $officeMatch = $this->checkIfValidUserIncoming($id,$docno);
            }

            if($type == "incomingreld"){
                $officeMatch = $this->checkIfValidUserIncomingReld($id,$docno);
            }

            if($type == "outgoing"){
                $officeMatch = $this->checkIfValidUserOutgoing($id,$docno);
            }            

            if (!$officeMatch) {
                throw new \Exception('You are not allowed to access this document.');
            }

            return [
                'success' => true,
                'data' => $userData,
                'message' => ''
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    function getUserInfo($id){
        try {
            $userData = $userData = $this->where('empcode', $id)->get()->getRowArray();

            return [
                'success' => true,
                'data' => $userData,
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    function getUserOffice($user){
        $builder = $this->db->table('action_office ao');
        $builder->select('ao.officecode as officecode, o.shortname as shortname, o.officename as officename');
        $builder->join('office o', 'ao.officecode = o.officecode', 'left');
        $builder->where('ao.empcode', $user);

        $offices = $builder->get()->getResultArray();

        //return array_column($offices, 'officecode');
        return $offices;
    }

    public function getUsersByOffice($currentoffice){

        try {

            $builder = $this->db->table('action_officer ao');
            $builder->select('ao.empcode, ao.lastname, ao.firstname, ao.middlename');
            $builder->join('action_office aof', 'ao.empcode = aof.empcode', 'left');
            $builder->where('aof.officecode', $currentoffice);
            $builder->where('aof.empcode !=', 'admin');
            $builder->where('ao.status', 'A');
            $builder->orderBy('ao.lastname ASC');
            $query = $builder->get();

            // Retrieve the results as an array
            $result = $query->getResultArray();

            return [
                'success' => true,
                'data' => $result,
                'message' => ''
            ];

        } catch (\Exception $e) {

            return ['success' => false, 'message' => $e->getMessage()];

        }

    }

    public function get_users(){

       try {
            $builder = $this->db->table('action_officer ao');
            $builder->select([
                'ao.empcode',
                'ao.empid',
                'ao.lastname',
                'ao.firstname',
                'ao.middlename',
                'ao.office_rep',
                'ao.status',
                'ao.date_expiration',
                'ul.UserLevelName as userleveldesc',
                'GROUP_CONCAT(DISTINCT o.shortname) AS offices',
            ]);
            
            $builder->join('action_office aof', 'ao.empcode = aof.empcode', 'left');
            $builder->join('office o', 'aof.officecode = o.officecode', 'left');
            $builder->join('userlevels ul', 'ao.userlevel = ul.UserLevelID', 'left');
            $builder->where('ao.deleted_at IS NULL', null, false);
            
            $builder->groupBy([
                'ao.empcode',
                'ao.empid',
                'ao.lastname',
                'ao.firstname',
                'ao.middlename',
                'ao.office_rep',
                'ao.status',
                'ao.date_expiration',
                'ul.UserLevelName',
            ]);

            $builder->orderBy('ao.firstname', 'ASC');

            $result = $builder->get()->getResultArray();

            if (!empty($result)) {
                return $result;
            } else {
                log_message('error', 'Result Query Returned 0 Rows');
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in releasedQuery: ' . $e->getMessage());
            return false;
        }

    }

    public function get_user($empid, $status="A")
    {
        try {
            $builder = $this->db->table('action_officer ao');
            $builder->select([
                'ao.empcode',
                'ao.empid',
                'ao.lastname',
                'ao.firstname',
                'ao.middlename',
                'ao.office_rep',
                'ao.userlevel',
                'ao.admin_menu',
                'GROUP_CONCAT(DISTINCT o.officecode) AS offices',
            ]);
            $builder->join('action_office aof', 'ao.empcode = aof.empcode', 'left');
            $builder->join('office o', 'aof.officecode = o.officecode', 'left');
            $builder->where('ao.empid', $empid);
            $builder->where('ao.status', $status);
            $builder->groupBy([
                'ao.empcode',
                'ao.empid',
                'ao.lastname',
                'ao.firstname',
                'ao.middlename',
                'ao.office_rep',
                'ao.userlevel',
                'ao.admin_menu',
            ]);

            $result = $builder->get()->getRowArray();

            if ($result !== null) {
                return $result;
            } else {
                log_message('error', 'get_user: No active user found for empid: ' . $empid);
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'get_user Exception: ' . $e->getMessage());
            return false;
        }
    }


    public function action_officer_exists($empcode){

        return $this->db->table('action_officer')->where('empcode', $empcode)->where('deleted_at IS NULL', null, false)->countAllResults() > 0;
    }

    public function action_officer_exists_by_empid($empid){

        return $this->db->table('action_officer')->where('empid', $empid)->countAllResults() > 0;
    }

    public function insert_action_officer($data){

        $offices = $data['offices'] ?? null;
        unset($data['offices']);
        
        try{

            $this->db->transStart();

            if (!$this->insert($data)) {
                throw new \Exception("An error occurred during the creation of Action Officer.");
            }
            
            if (is_array($offices) && !empty($offices)) {
                foreach ($offices as $officeCode) {
                    $this->db->table('action_office')->insert([
                        'empcode'    => $data['empcode'],
                        'officecode' => $officeCode
                    ]);
                }
            }

            $this->audittrailmodel->insertAuditTrail($data['empid'], 'action_officer', session()->get('logged_user'), 'INSERT');
            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update action officer. Error: Audit Trail.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while saving office assignments.");
            }

            return [
                'success' => true,
                'message' => 'Successfully Added Action Officer.'
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error creating Action Officer :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function update_action_officer_by_empcode($id,$data){

        $offices = $data['offices'] ?? null;
        unset($data['offices']);

        try{

            $this->db->transStart();

            $builder = $this->db->table('action_officer');

            $builder->where('empcode', $id);

            if (!$builder->update($data)) {
                throw new \Exception("An error occurred during the updating of Action Officer.");
            }

            if (is_array($offices)) {
                // Delete existing office assignments for the employee
                $this->db->table('action_office')
                    ->where('empcode', $data['empcode'])
                    ->delete();

                // Insert the new/updated office assignments
                if (!empty($offices)) {
                    foreach ($offices as $officeCode) {
                        $this->db->table('action_office')->insert([
                            'empcode'    => $data['empcode'],
                            'officecode' => $officeCode
                        ]);
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while updating office assignments.");
            }

            return [
                'success' => true,
                'message' => 'Successfully Updated Action Officer.'
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error updating Action Officer :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function update_action_officer($id,$data)
    {

        $offices = $data['offices'] ?? null;
        unset($data['offices']);

        $oldData = $this->find($id);
        try{

            $this->db->transStart();

            if(!$this->update($id,$data)){
                throw new \Exception("An error occurred during the updating of Action Officer.");
            }

            if (is_array($offices)) {
                // Delete existing office assignments for the employee
                $this->db->table('action_office')
                    ->where('empcode', $data['empcode'])
                    ->delete();

                $this->audittrailmodel->insertAuditTrail($data['empcode'], 'action_office', session()->get('logged_user'), 'DELETE');

                // Insert the new/updated office assignments
                if (!empty($offices)) {
                    foreach ($offices as $officeCode) {
                        $this->db->table('action_office')->insert([
                            'empcode'    => $data['empcode'],
                            'officecode' => $officeCode
                        ]);
                    }
                    $this->audittrailmodel->insertAuditTrail($data['empcode'], 'action_office', session()->get('logged_user'), 'INSERT');
                }

            }

            $comparedData = $this->audittrailmodel->compareUpdateData((array) $oldData, $data);
            $this->audittrailmodel->insertAuditTrailForUpdate($id, $this->table, $comparedData, session()->get('logged_user'));

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while updating office assignments.");
            }

            return [
                'success' => true,
                'message' => 'Successfully Updated Action Officer.'
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error updating Action Officer :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function delete_action_officer($id,$data){

        $empcode = $data['empcode'] ?? null;
        unset($data['empcode']);

        $oldData = $this->find($id);
        try{

            $this->db->transStart();
            
            $message = "Successfully Deleted Action Officer.";
            if(!$this->update($id,$data)){
                throw new \Exception("An error occurred during the updating of Action Officer.");
            }

            if(!$this->isEmpcodeInUse($empcode)){
                if(!$this->delete($id)){
                    throw new \Exception("An error occurred during the deleting of Action Officer.");
                }

                $this->audittrailmodel->insertAuditTrailSoftDelete($id, 'action_officer', 'DELETE');

                if(!$this->db->table('action_office')->where('empcode', $empcode)->delete()){
                    throw new \Exception("An error occurred during the deleting of Action Officer. Delete office.");
                }

                $this->audittrailmodel->insertAuditTrail($empcode, 'action_office', session()->get('logged_user'), 'DELETE');
            }else{
                $message = "Action Officer already used in transactions. Deactivated Action Officer.";
            }

            $comparedData = $this->audittrailmodel->compareUpdateData((array) $oldData, $data);
            $this->audittrailmodel->insertAuditTrailForUpdate($id, $this->table, $comparedData, session()->get('logged_user'));
            
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while deleting Action Officer.");
            }

            return [
                'success' => true,
                'message' => $message
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error deleting Action Officer :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function confirmUserPassword($oldPassword, $id)
    {
        $result = $this->where('empcode', $id)->first();
        if ($result && password_verify($oldPassword, trim($result['password']))) {
            return true;
        }

        return false;
    }


    private function checkIfValidUserIncoming($id,$docdetail){

        $builder = $this->db->table('action_office ao');
        $builder->select('ao.officecode');
        $builder->join('docdetails dd', 'dd.office_destination = ao.officecode');
        $builder->where('ao.empcode', $id);
        $builder->where('dd.doc_detailno', $docdetail);

        $docmatch = $builder->get()->getRow();


        return $docmatch;
    }

    private function checkIfValidUserIncomingReld($id,$docdetail){



        $builder = $this->db->table('docdetails dd');
        $builder->select('aoo.empcode');
        $builder->join('action_office ao', 'ao.empcode = dd.emp_entry', 'left');
        $builder->join('action_office aoo', 'aoo.officecode = ao.officecode', 'left');
        $builder->where('dd.doc_detailno', $docdetail);
        $builder->where('aoo.empcode', $id);

        $query = $builder->get();

        if ($query->getNumRows() > 0) {
            return true; // Data exists
        } else {
            return false; // No data found
        }
    }

    private function checkIfValidUserOutgoing($id,$docregistry){

        $builder = $this->db->table('action_office ao');
        $builder->select('ao.officecode');
        $builder->join('docregistry dr', 'dr.officecode = ao.officecode');
        $builder->where('ao.empcode', $id);
        $builder->where('dr.route_no', $docregistry);

        $docmatch = $builder->get()->getRow();


        return $docmatch;
    }

    private function isEmpcodeInUse(string $empcode): bool
    {

        $builder1 = $this->db->table('docdetails');
        $builder1->groupStart()
            ->where('action_officer', $empcode)
            ->orWhere('receive_by', $empcode)
            ->orWhere('action_by', $empcode)
            ->orWhere('release_by', $empcode)
            ->groupEnd();
        $usedInDocDetails = $builder1->countAllResults();

        // Check in docregistry
        $builder2 = $this->db->table('docregistry');
        $builder2->groupStart()
            ->where('empcode', $empcode)
            ->orWhere('userid', $empcode)
            ->orWhere('last_modified_by', $empcode)
            ->groupEnd();
        $usedInDocRegistry = $builder2->countAllResults();

        return ($usedInDocDetails > 0 || $usedInDocRegistry > 0);
    }


    

}