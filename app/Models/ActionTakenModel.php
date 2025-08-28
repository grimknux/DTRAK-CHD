<?php

namespace App\Models;
use App\Models\audittrailmodel;

use CodeIgniter\Model;

class ActionTakenModel extends Model
{
    protected $table      = 'action_taken';
    
    protected $primaryKey = 'action_code';

    protected $useAutoIncrement = false;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['action_code', 'action_desc', 'act_tstatus', 'created_at', 'updated_at', 'deleted_at'];


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

    public function get_action_taken_active(){

        try {
            $result = $this->where('act_tstatus', 'Active')->orderBy('action_desc', 'ASC')->findAll();

            return $result;

        } catch (\Exception $e) {
            return false;
        }
        
    }

    public function get_action_taken_all()
    {
        try {
            $result = $this->where('deleted_at IS NULL', null, false)->orderBy('action_desc', 'ASC')->findAll();

            return $result;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function insert_action_taken($data){
        
        try{

            $this->db->transStart();

            if (!$this->insert($data)) {
                throw new \Exception("An error occurred during the creation of Action Taken.");
            }

            $this->audittrailmodel->insertAuditTrail($data['action_code'], 'action_taken', session()->get('logged_user'), 'INSERT');
            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update Action Taken. Error: Audit Trail.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception("Transaction failed while saving Action Taken.");
            }

            return [
                'success' => true,
                'message' => 'Successfully Added Action Taken.'
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', "Error creating Action Taken :{$e->getMessage()}");
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];

        }

    }

    public function action_taken_exists(string $taken_desc, ?string $excludeId = null): bool
    {
        $query = $this->where('action_desc', $taken_desc)
                ->where('deleted_at IS NULL', null, false);

        if (!empty($excludeId)) {
            $query->where('action_code !=', $excludeId); // Exclude current record on edit
        }

        return $query->countAllResults() > 0;
    }

    public function getActionRequireReturn(){

        return $this->whereIn('reqaction_code', ['00025', '00070', '00075'])
                    ->orderBy('reqaction_code ASC')
                    ->findAll();

    }


    public function generate_action_taken_code(): string
    {
        $this->db->transStart();

        // Get the max existing type_code
        $maxSequence = $this->db->table('action_taken')
            ->select('MAX(action_code) as maxacttakencode')
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        $currentSequence = 0;
        if ($maxSequence && $maxSequence['maxacttakencode']) {
            $currentSequence = (int)$maxSequence['maxacttakencode'];
        }

        // Increment and pad to 5 digits
        $newSequence = str_pad($currentSequence + 1, 5, '0', STR_PAD_LEFT);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Failed to generate Action Taken Code');
        }

        return $newSequence; // e.g. 00001
    }

}

