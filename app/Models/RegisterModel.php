<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;


class RegisterModel
{
    protected $db;
    public function __construct(ConnectionInterface &$db)
    {
        $this->db =& $db;
    }

    public function createUser($data)
    {
        
        $builder = $this->db->table('administrator');
        $res = $builder->insert($data);
        if($this->db->affectedRows() >= 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function verifyUniid($id)
    {
        $builder = $this->db->table('administrator');
        $builder->select('activation_date,uniid,Status')
                ->where('uniid',$id);
        $result = $builder->get(); 
        //echo count($result->getResultArray());
        //echo $builder->countAll();
        //echo $result->resultID->num_rows();
        if(count($result->getResultArray()) == 1)
        //if($builder->countAll() == 1)
        {
            return $result->getRow();
        }
        else
        {
            return false;
        }
        

       
    }

    public function updateStatus($uniid)
    {
        $builder = $this->db->table('administrator');
        $builder->where('uniid',$uniid);
        $builder->update(['Status'=>'Active']);
        if($this->db->affectedRows()==1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
