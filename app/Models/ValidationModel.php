<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidationModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getDivisionValues()
    {
        return $this->db->table('division')->select('divcode')->get()->getResultArray();
    }

    public function getSectionValues($divisionValue)
    {
        return $this->db->table('section')->select('sectionID')->where('division', $divisionValue)->get()->getResultArray();
    }


    
}

