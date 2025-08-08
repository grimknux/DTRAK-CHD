<?php

namespace App\Models;

use CodeIgniter\Model;

class PDFModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getEmployee($emptype, $division = null, $section = null)
    {
        $builder = $this->db->table('employee');
        $builder->where('TypeOfEmployment', $emptype);
        

        // Add other conditions if the parameters are provided and not null
        if ($division != null) {
            $builder->where('Division', $division);
        }

        if ($section != null) {
            $builder->where('AreaOfAssignment', $section);
        }


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

    public function getEmployeeBulk($employee)
    {

        $builder = $this->db->table('employee');
        $builder->whereIn('ID', $employee);
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