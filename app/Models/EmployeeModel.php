<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table      = 'employee';
    protected $primaryKey = 'ID';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['Employee_ID', 'FirstName', 'MiddleName', 'LastName', 'Suffix', 'Gender', 'Position', 'AreaOfAssignment', 'Division', 'Regular_SubAllotment', 'ContractDuration_start', 'ContractDuration_end', 'InclusiveDateOfEmployment', 'SalaryGrade', 'Salary', 'PRC', 'Address', 'Birthdate', 'PlaceOfBirth', 'NameOfPersonToNotify', 'Bloodtype', 'TINNumber', 'Philhealth', 'SSS', 'PagIbigNumber', 'CPNumber', 'TypeOfEmployment', 'NickName', 'NameExt', 'Signature', 'ProfilePhoto', 'EmailAddress'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

   
    public function updateEmployee($data)
    {
        try {

            $id = $data['ID']; // Assuming you have an 'id' field in the $data array

            // Remove the 'id' field from the $data array before updating
            unset($data['ID']);

            $this->update($id, $data);
            return ['xstatus' => 'success', 'message' => 'Successfully inserted employee!'];
        } catch (\Exception $e) {
            return ['xstatus' => 'error', 'message' => $e->getMessage()];
        }
    }


    public function getEmployee($id){
        try {

            return $this->find($id);

        } catch (\Exception $e) {

            return ['xstatus' => 'error', 'message' => $e->getMessage()];
        }
    }



    public function insertDataFromCSV($csvFile)
    {
        $data = [];
        try {
            $file = $csvFile->getTempName();
            $fileHandler = fopen($file, 'r');

            if (!$fileHandler) {
                return ['xstatus' => 'error', 'message' => 'Error opening CSV file!'];
            }

            $headerSkipped = false; // Flag to track if the header row is skipped

            while (($rowData = fgetcsv($fileHandler)) !== false) {
                // Skip the header row
                if (!$headerSkipped) {
                    $headerSkipped = true;
                    continue;
                }

                // Sanitize and escape data before inserting into the database
                $row = array_map(function ($value) {
                    return $this->db->escapeString($value);
                }, $rowData);

                // Check if the ID already exists in the database
                $existingData = $this->where('Employee_ID', $row[0])->first();

                // If the ID does not exist, add the data to the insert batch
                if (!$existingData) {
                    if(!empty($row[10]) || $row[10] != ""){
                        $startdate = date('Y-m-d', strtotime($row[10]));
                    }else{
                        $startdate = '0000-00-00';
                    }

                    if(!empty($row[11]) || $row[11] != ""){
                        $enddate = date('Y-m-d', strtotime($row[11]));
                    }else{
                        $enddate = '0000-00-00';
                    }

                    if(!empty($row[12]) || $row[12] != ""){
                        $inclusive = date('Y-m-d', strtotime($row[12]));
                    }else{
                        $inclusive = '0000-00-00';
                    }

                    if(!empty($row[17]) || $row[17] != ""){
                        $bdate = date('Y-m-d', strtotime($row[17]));
                    }else{
                        $bdate = '0000-00-00';
                    }

                    $data[] = [
                        'Employee_ID'  => $row[0],
                        'FirstName' => $row[2],
                        'MiddleName' => $row[3],
                        'LastName' => $row[1],
                        'Suffix' => $row[4],
                        'Gender' => $row[5],
                        'Position' => $row[6],
                        'AreaOfAssignment' => $row[7],
                        'Division' => $row[8],
                        'Regular_SubAllotment' => $row[9],
                        'ContractDuration_start' => $startdate,
                        'ContractDuration_end' => $enddate,
                        'InclusiveDateOfEmployment' => $inclusive,
                        'SalaryGrade' => $row[13],
                        'Salary' => $row[14],
                        'PRC' => $row[15],
                        'Address' => $row[16],
                        'Birthdate' => $bdate,
                        'PlaceOfBirth' => $row[18],
                        'NameOfPersonToNotify' => $row[19],
                        'CPNumber' => $row[20],
                        'Bloodtype' => $row[21],
                        'TINNumber' => $row[22],
                        'Philhealth' => $row[23],
                        'SSS' => $row[24],
                        'PagIbigNumber' => $row[25],
                        'EmailAddress' => $row[26],
                        'TypeOfEmployment' => $row[27],
                        'Signature' => $row[28],
                        'ProfilePhoto' => $row[29],
                        //'NickName' => $row[30],
                        //'NameExt' => $row[31]
                    ];
                }
            }

            
            fclose($fileHandler);

            if (!empty($data)) {
                $this->insertBatch($data);
                //$NameOfPersonToNotify = $data[count($data) - 1]['NameOfPersonToNotify'];

                return ['xstatus' => 'success', 'message' => 'Successfully inserted employee.'];
            } else {
                return ['xstatus' => 'error', 'message' => 'No new data to insert!'];
            }
        } catch (\Exception $e) {
            return ['xstatus' => 'error', 'message' => $e->getMessage()];
        }
    }
}