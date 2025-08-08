<?php

namespace App\Models;

use CodeIgniter\Model;

class UserLevelModel extends Model
{
    protected $table      = 'userlevels';
    
    protected $primaryKey = 'UserLevelID';

    protected $useAutoIncrement = false;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['UserLevelID', 'UserLevelName'];



    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getUserLevels()
    {
         $result = $this->findAll();

        return $result;
    }
}

