<?php

namespace App\Models;

use CodeIgniter\Model;

class ActionModel extends Model
{
    protected $table      = 'action_required';
    
    protected $primaryKey = 'reqaction_code';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['reqaction_code', 'reqaction_desc', 'reqaction_done', 'act_status'];


    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
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


    public function getActionByRequire($actionrequire){

        return $this->where('reqaction_code', $actionrequire)->first();
    }
    
}

