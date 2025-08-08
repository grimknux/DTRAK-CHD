<?php

namespace App\Models;

use CodeIgniter\Model;

class HolidayModel extends Model
{
    protected $table      = 'holiday';
    
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['holiday', 'date_holiday'];


    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getHoliday(){
        
        return $this->findAll();

    }


    
}

