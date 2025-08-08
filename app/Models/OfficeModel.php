<?php

namespace App\Models;

use CodeIgniter\Model;

class OfficeModel extends Model
{
    protected $table      = 'office';
    
    protected $primaryKey = 'officecode';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['officecode', 'officename', 'shortname', 'regcode', 'headoffice', 'office_division'];


    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function getOfficeDataById($officecode){
        
        return $this->find($officecode);

    }

    public function getOfficeCode($empcode){
        $builder = $this->db->table('action_officer ao');
        $builder->select('*');
        $builder->select('aof.officecode AS officecodes');
        $builder->join('action_office aof', 'ao.empcode = aof.empcode', 'left');
        $builder->where('ao.empcode', $empcode);

        $query = $builder->get();
        $result = $query->getResult();

        $newofficecode = array();

        foreach ($result as $row) {
            array_push($newofficecode, $row->officecodes);
        }

        return $newofficecode;
    }

    public function getEmpOffice($empcode){
        $builder = $this->db->table('action_officer ao');
        $builder->select('aof.*, o.*');
        $builder->join('action_office aof', 'ao.empcode = aof.empcode', 'left');
        $builder->join('office o', 'o.officecode = aof.officecode', 'left');
        $builder->where('ao.empcode', $empcode);

        $query = $builder->get();
        $result = $query->getResultArray();

        return $result;
    }

    public function getOfficeExceptCurrent($currentoffice){
        
        $result = $this->where('officecode !=', $currentoffice)
                       ->where('officecode !=', '00028')
                       ->where('regcode', '01')
                       ->orderBy('officename ASC')->findAll();

        return $result;

    }

    public function getOffice(){
   
        $result = $this->where('officecode !=', '00028')
                       ->where('regcode', '01')
                       ->orderBy('officename ASC')->findAll();

        return $result;

    }

    
}

