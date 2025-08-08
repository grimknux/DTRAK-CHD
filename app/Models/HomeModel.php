<?php

namespace App\Models;

use CodeIgniter\Model;

class HomeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function staff()
    {
        $builder = $this->db->table('employee');
        $result = $builder->get();

        if(count($result->getResultArray()) >= 1)
        {
            return $result->getResultArray();
        }
        else
        {
            return false;
        }
    }

    public function getStaffData($id)
    {
        $builder = $this->db->table('employee')
                 ->where('ID', $id);
        $result = $builder->get();

        if(count($result->getResultArray()) == 1)
        {
            return $result->getRow();
        }
        else
        {
            return false;
        }
    }

    public function getSection($div)
    {
        $builder = $this->db->table('section')
                     ->where('division', $div);
        $result = $builder->get();

        if(count($result->getResultArray()) >= 1)
        {
            return $result->getResultArray();
        }
        else
        {
            return false;
        }
    }

    public function getDivision()
    {
        $builder = $this->db->table('division');
        $result = $builder->get();

        if(count($result->getResultArray()) >= 1)
        {
            return $result->getResultArray();
        }
        else
        {
            return false;
        }
    }

    
}

