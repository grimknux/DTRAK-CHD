<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditTrailModel extends Model
{
    protected $table      = 'audit_trail';
    
    protected $primaryKey = 'trailcode';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['trail_controlno', 'trail_table', 'trail_column', 'trail_action', 'trail_old_value', 'trail_new_value', 'timestamp', 'trail_date', 'trail_time', 'trail_remarks', 'trail_by', 'trail_ip'];


    public function compareUpdateData($oldData, $newData){
        $changes = [];

        foreach ($newData as $key => $value) {
            if ($oldData[$key] !== $value) {
                $changes[$key] = [
                    'old_value' => $oldData[$key],
                    'new_value' => $value
                ];
            }
        }

        return $changes;
    }


    public function insertAuditTrail($id, $table, $user, $action)
    {
        if($this->insert([
            'trail_controlno' => $id,
            'trail_action' => $action,
            'trail_table' => $table,
            'trail_column' => 'n/a',
            'trail_old_value' => 'n/a',
            'trail_new_value' => 'n/a',
            'trail_date' => date('Y-m-d'),
            'trail_time' => date('H:i:s'),
            'trail_by' => $user,
            'trail_ip' => $_SERVER['REMOTE_ADDR'], // Get the IP address of the user
        ])){
            return true;
        }

        return false;
        
    }

    public function insertAuditTrailSoftDelete($id, $table, $action = 'DELETE')
    {
        if($this->insert([
            'trail_controlno' => $id,
            'trail_action' => $action,
            'trail_table' => $table,
            'trail_column' => 'deleted_at',
            'trail_old_value' => NULL,
            'trail_new_value' => date('Y-m-d H:is'),
            'trail_date' => date('Y-m-d'),
            'trail_time' => date('H:i:s'),
            'trail_by' => session()->get('logged_user'),
            'trail_ip' => $_SERVER['REMOTE_ADDR'], // Get the IP address of the user
        ])){
            return true;
        }

        return false;
        
    }


    public function insertAuditTrailForUpdate($id, $table, $comparedData, $user)
    {

        // Insert a record into the audit trail table for each changed column
        foreach ($comparedData as $column => $change) {
            $this->insert([
                'trail_controlno' => $id,
                'trail_action' => 'UPDATE',
                'trail_table' => $table,
                'trail_column' => $column,
                'trail_old_value' => $change['old_value'],
                'trail_new_value' => $change['new_value'],
                'trail_date' => date('Y-m-d'),
                'trail_time' => date('H:i:s'),
                'trail_by' => $user,
                'trail_ip' => $_SERVER['REMOTE_ADDR'], // Get the IP address of the user
            ]);
        }
    }


}