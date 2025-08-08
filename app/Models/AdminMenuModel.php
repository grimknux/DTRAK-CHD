<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminMenuModel extends Model
{
    protected $table      = 'admin_menu';
    
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['id', 'admin_menu', 'created_at'];

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

    public function get_admin_menus()
    {
         $result = $this->findAll();

        return $result;
    }
}

