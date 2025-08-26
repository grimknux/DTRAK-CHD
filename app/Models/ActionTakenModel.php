<?php

namespace App\Models;

use CodeIgniter\Model;

class ActionTakenModel extends Model
{
    protected $table      = 'action_taken';
    
    protected $primaryKey = 'action_code';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['action_code', 'action_desc', 'act_tstatus', 'created_at', 'updated_at', 'deleted_at'];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function get_action_taken_active(){

        $actionrequired = $this->where('act_tstatus', 'Active')->orderBy('action_desc', 'ASC')->findAll();
        return $actionrequired;
        
    }

}

