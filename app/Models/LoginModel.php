<?php

namespace App\Models;

use CodeIgniter\Model;


class LoginModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }


    public function verifyUser($user)
    {
        
        $builder = $this->db->table('action_officer');
        $builder->where('empcode',$user)->where('status', 'A');
        
        $result = $builder->get();

        if(count($result->getResultArray()) == 1)
        {
            return $result->getRowArray();
        }
        else
        {
            return false;
        }
    }


    public function saveLoginInfo($data)
    {
        $builder = $this->db->table('login_activity');
        $builder->insert($data);
        
        if($this->db->affectedRows() >= 1)
        {
            return $this->db->insertID();
        }
        else
        {
            return false;
        }
    } 
    

    public function updateLogoutTime($id)
    {
        $builder = $this->db->table('login_activity');
        $builder->where('id',$id);
        $builder->update(['logout_time' => date('Y-m-d H:i:s')]);

        if($this->db->affectedRows() > 0)
        {
            return true;
        }
    }

}
