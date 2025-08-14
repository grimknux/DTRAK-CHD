<?php

namespace App\Models;
use App\Models\audittrailmodel;

use CodeIgniter\Model;

class DocumentRegistryModel extends Model
{

    protected $table      = 'docregistry';
    
    protected $primaryKey = 'route_no';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['docregistry_id', 'route_no','subject','empcode','exofficecode','exempname','no_page', 'office_controlno', 'ref_office_controlno', 'officecode', 'filename', 'userid','insequence','remarks','sourcetype','datelog','timelog', 'attachlist', 'last_modified_by', 'modified_date', 'registry_status', 'is_deleted', 'created_at', 'exdoc_controlno'];

    public $audittrailmodel;

    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();

        $this->audittrailmodel = new audittrailmodel();
    }

    public function OutgoingQuery($office){

        $builder = $this->db->table('docregistry dr');
        $builder->select([
            'dr.route_no',
            'dr.datelog',
            'dr.subject',
            'dr.officecode',
            'dr.empcode',
            'dr.no_page',
            'dr.filename',
            'dr.attachlist',
            'dr.remarks',
            'dr.ref_office_controlno',
            'o.shortname AS officeshort',
            'o.officename',
            'ao.lastname',
            'ao.firstname',
            'ao.middlename',
            'ao.office_rep AS orep',
            'GROUP_CONCAT(DISTINCT(dt.type_desc)) AS ddoctype'
        ]);

        $builder->join('registry_doctype rd', 'dr.route_no = rd.route_no', 'left');
        $builder->join('doc_type dt', 'rd.type_code = dt.type_code', 'left');
        $builder->join('action_officer ao', 'dr.empcode = ao.empcode AND ao.status = "A"', 'left');
        $builder->join('office o', 'dr.officecode = o.officecode', 'left');
        $builder->whereIn('dr.officecode', $office);
        $builder->where('dr.is_deleted', 0);
        $builder->where('dr.registry_status', 'Active');
        
        $builder->groupBy([
            'docregistry_id',
            'dr.route_no',
            'dr.datelog',
            'dr.subject',
            'dr.officecode',
            'dr.empcode',
            'dr.no_page',
            'dr.filename',
            'dr.attachlist',
            'dr.ref_office_controlno',
            'dr.remarks',
            'o.shortname',
            'o.officename',
            'ao.lastname',
            'ao.firstname',
            'ao.middlename',
            'ao.office_rep',
        ]);
        
        $builder->orderBy('dr.docregistry_id', 'DESC');
        
        $docregistry = $builder->get()->getResultArray();

        return $docregistry;
        
    }

    public function documentManagement($limit,$offset,$searchValue,$filterValue,$ds = false) 
    {

        try {
            

            $builder = $this->db->table('docregistry dr');

            $builder->select("
                dr.route_no,
                GROUP_CONCAT(DISTINCT dd.doc_controlno) as docno,
                dr.ref_office_controlno,
                dr.subject,
                GROUP_CONCAT(DISTINCT dt.type_desc) as ddoctype,
                o.shortname as orig_office,
                ao.lastname,
                ao.firstname,
                ao.middlename,
                ao.office_rep as orep,
                dr.no_page,
                dr.filename,
                dr.remarks,
                dr.registry_status

                
            ");
            $builder->join('docdetails dd', 'dr.route_no = dd.route_no', 'left');
            $builder->join('registry_doctype rd', 'dr.route_no = rd.route_no', 'left');
            $builder->join('doc_type dt', 'rd.type_code = dt.type_code', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->join('action_officer ao', 'dr.empcode = ao.empcode', 'left');

            if (!empty($searchValue)) {
                $builder->groupStart()
                        ->like('dr.route_no', $searchValue)
                        ->orLike('dd.doc_controlno', $searchValue)
                        ->orLike('dp.doc_controlno', $searchValue)
                        //->orLike('dr.ref_office_controlno', $searchValue)
                        ->orLike('dr.subject', $searchValue)
                        ->orLike('dt.type_desc', $searchValue)
                        ->orLike('o.shortname', $searchValue)
                        ->orLike('ao.lastname', $searchValue)
                        ->orLike('ao.firstname', $searchValue)
                        ->orLike('ao.middlename', $searchValue)
                        ->orLike('dr.no_page', $searchValue)
                        ->orLike('dr.filename', $searchValue)
                        ->orLike('dr.remarks', $searchValue)
                        ->groupEnd();
            }

            if (!empty($filterValue['routeNoFilter'])) {
                $builder->where('dr.route_no', $filterValue['routeNoFilter']);
            }
            if (!empty($filterValue['documentControlFilter'])) {
                $builder->where('dd.doc_controlno', $filterValue['documentControlFilter']);
            }
            if (!empty($filterValue['subjectFilter'])) {
                $builder->like('dr.subject', $filterValue['subjectFilter']);
            }

            $builder->groupBy([
                'dr.route_no',
                'dr.ref_office_controlno',
                'dr.subject',
                'o.shortname',
                'ao.lastname',
                'ao.firstname',
                'ao.middlename',
                'ao.office_rep',
                'dr.no_page',
                'dr.filename',
                'dr.remarks',
                'dr.registry_status'
            ]);

            $builder->orderBy('dr.route_no', 'ASC');

            $filtered_builder = clone $builder;

            if(!empty($limit) && $limit != -1) {
                $get_query = $builder->limit($limit, $offset)->get()->getResultArray();

            }else{
                $get_query = $builder->get()->getResultArray();
            }
            $totalRecords = $this->countAllResults(false);  // Total unfiltered records
            $filteredRecords = $filtered_builder->countAllResults(false); // Total after filtering

            return [
                'success' => true,
                'data' => $get_query,
                'totalRecords' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
            ];
    
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }

    }
    

    public function getDocumentData($routeno){

        try {
            $builder = $this->db->table('docregistry dr');
            $builder->select([
                'dr.route_no',
                'dr.docregistry_id',
                'dr.office_controlno',
                'dr.datelog',
                'dr.subject',
                'dr.officecode',
                'dr.empcode',
                'dr.no_page',
                'dr.filename',
                'dr.attachlist',
                'dr.remarks',
                'dr.sourcetype',
                'dr.ref_office_controlno',
                'dr.exdoc_controlno',
                'dr.exofficecode',
                'dr.exempname',
                'dr.registry_status',
                'dr.datelog',
                'dr.timelog',
                'o.shortname AS officeshort',
                'o.officename',
                'ao.lastname',
                'ao.firstname',
                'ao.middlename',
                'ao.office_rep AS orep',
                'GROUP_CONCAT(DISTINCT(dt.type_code)) AS ddoctype',
                'GROUP_CONCAT(DISTINCT(dt.type_desc)) AS ddoctype_desc'
            ]);

            $builder->join('registry_doctype rd', 'dr.route_no = rd.route_no', 'left');
            $builder->join('doc_type dt', 'rd.type_code = dt.type_code AND dt.deleted_at IS NULL', 'left');
            $builder->join('action_officer ao', 'dr.empcode = ao.empcode AND ao.deleted_at IS NULL', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->where('dr.route_no', $routeno);
            $builder->where('dr.is_deleted', 0);
            $builder->where('dr.registry_status', 'Active');
            
            $builder->groupBy([
                'dr.route_no',
                'dr.docregistry_id',
                'dr.office_controlno',
                'dr.datelog',
                'dr.subject',
                'dr.officecode',
                'dr.empcode',
                'dr.no_page',
                'dr.filename',
                'dr.attachlist',
                'dr.datelog',
                'dr.timelog',
                'dr.remarks',
                'o.shortname',
                'o.officename',
                'ao.lastname',
                'ao.firstname',
                'ao.middlename',
                'ao.office_rep',
            ]);
            
            $result = $builder->get();
            
            if ($result->getNumRows() > 0) {
                return $result->getRowArray();
            } else {
                throw new \Exception("No rows returned!");

            }

        } catch (\Exception $e) {
            log_message('error', "Error retrieving document :{$e->getMessage()}");
            
            return false;
        }
        
    }

    public function generateDocumentRegistryNo($type = 'R') {

        $this->db->transStart();

        $year = date('Y');

        $uniqueCode = '';

        do {

            $maxSequence = $this->select('route_no as maxcode')->orderby('docregistry_id', 'DESC')
                                ->first();

            $currentSequence = 0;
            if ($maxSequence && $maxSequence['maxcode']) {
                $parts = explode('-', $maxSequence['maxcode']);
                if (count($parts) == 2) {
                    $currentSequence = (int)$parts[1];
                }
            }

            $newSequence = str_pad($currentSequence + 1, 9, '0', STR_PAD_LEFT);
            $uniqueCode = "{$type}{$year}-{$newSequence}";


            $existingCode = $this->where('route_no', $uniqueCode)->first();
        
        } while ($existingCode);


        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Failed to generate unique code');
        }

        return $uniqueCode;

    }


    public function insertNewDocument($documentdata){

        $this->db->transStart();

        $typcode = $documentdata['doctype'];
        unset($documentdata['doctype']);

        try {

            $route_no = $documentdata['route_no'];
            $this->insert($documentdata);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to insert document data.');
            }


            foreach ($typcode as $docType) {
                $doctypeData = [
                    'type_code' => $docType,
                    'route_no' => $route_no,
                ];
    
                $this->db->table('registry_doctype')->insert($doctypeData);
    
                $lastInsertedId = $this->db->insertID();

                if ($this->db->transStatus() === false) {
                    throw new \Exception('Failed to insert document type.');
                }

                $this->audittrailmodel->insertAuditTrail($lastInsertedId, 'registry_doctype', $documentdata['userid'], 'INSERT');
                if ($this->db->transStatus() === false) {
                    throw new \Exception('Failed to insert document type. Error: Audit Trail.');
                }
    
            }


            $this->audittrailmodel->insertAuditTrail($documentdata['route_no'], 'docregistry', $documentdata['userid'], 'INSERT');
            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to insert document data. Error: Audit Trail.');
            }
   
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Add Document failed.');
            }

            return [
                'success' => true,
                'message' => "Successfully added document \n Document No.: ".$route_no.".",
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }


    public function updateDocument($documentdata){

        $this->db->transStart();

        $route_no = $documentdata['route_no'];
        $editdoctype = $documentdata['doctype'];
        $existdoctype = $documentdata['existdoctype'];
        $last_modified_by = $documentdata['last_modified_by'];
        $userid = $documentdata['userid'];

        unset($documentdata['doctype']);
        unset($documentdata['route_no']);
        unset($documentdata['existdoctype']);
        unset($documentdata['userid']);

        $oldData = $this->find($route_no);

        try {

            $this->update($route_no, $documentdata);
            unset($documentdata['modified_date']);

            $comparedData = $this->audittrailmodel->compareUpdateData((array) $oldData, $documentdata);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update document data.');
            }



            $doctypes_to_add = array_diff($editdoctype, $existdoctype);
            $doctypes_to_remove = array_diff($existdoctype, $editdoctype);


            foreach ($doctypes_to_add as $doctype) {

                $doctypeData = [
                    'type_code' => $doctype,
                    'route_no' => $route_no,
                ];
    
                $this->db->table('registry_doctype')->insert($doctypeData);
    
                $lastInsertedId = $this->db->insertID();

                if ($this->db->transStatus() === false) {
                    throw new \Exception('Failed to update document type.');
                }

                $this->audittrailmodel->insertAuditTrail($lastInsertedId, 'registry_doctype', $userid, 'INSERT_UPDATE');
                if ($this->db->transStatus() === false) {
                    throw new \Exception('Failed to update document. Error: Audit Trail.');
                }
    
            }


            foreach ($doctypes_to_remove as $doctype) {

                
                $query = $this->db->table('registry_doctype')
                      ->select('id')  // Assuming 'id' is the primary key column
                      ->where('route_no', $route_no)
                      ->where('type_code', $doctype)
                      ->get();

                $result = $query->getRowArray();

                if ($result) {

                    $deletedId = $result['id']; 

                    $this->db->table('registry_doctype')
                             ->where('id', $deletedId)
                             ->delete();

                    if ($this->db->transStatus() === false) {
                        throw new \Exception('Failed to update document type.');
                    }

                    $this->audittrailmodel->insertAuditTrail($deletedId, 'registry_doctype', $userid, 'DELETE_UPDATE');
                    if ($this->db->transStatus() === false) {
                        throw new \Exception('Failed to update document type. Error: Audit Trail.');
                    }
                }
                
    
            }

            $this->audittrailmodel->insertAuditTrailForUpdate($route_no, $this->table, $comparedData, $userid);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to update document. Error: Audit Trail.');
            }
   
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Updating of Document failed.');
            }

            return [
                'success' => true,
                'message' => "Successfully updated document \n Document No.: ".$route_no.".",
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }

    public function updateDocumentAttachment($documentdata){

        $this->db->transStart();

        $route_no = $documentdata['route_no'];
        $last_modified_by = $documentdata['last_modified_by'];

        unset($documentdata['route_no']);

        $oldData = $this->find($route_no);

        try {

            $this->update($route_no, $documentdata);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Updating of attachment error. Failed to update attachment.');
            }

            $this->audittrailmodel->insertAuditTrail($route_no, 'registry_doctype', $last_modified_by, 'UPDATE_ATTACH');

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Updating of attachment error. Updating of attachment failed. Error: Audit Trail.');
            }

            return [
                'success' => true,
                'message' => "Successfully updated attachment.",
            ];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(), // Return the exception message
            ];
        }
    }


    public function deleteThisDocument($id,$user){
        $this->db->transStart();

        try {

            $this->update($id, ['registry_status' => 'Inactive', 'is_deleted' => '1']);

            $this->audittrailmodel->insertAuditTrail($id, 'docregistry', $user, 'DELETE');
            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to delete document. Error: Audit Trail.');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Failed to delete document.');
            }

            return [
                'success' => true,
                'message' => "Your document has been deleted.",
            ];


        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage() // Return the exception message
            ];
        }
    }

    public function checkAttachIfValidUser($attach) {
        $officecode = $this->select('officecode')
                            ->where('filename', $attach)
                            ->first();
    
        return $officecode;
    }

    public function checkOfficeIfValid($office,$docregistryno){

        $document = $this->whereIn('officecode', $office)
                         ->where('route_no', $docregistryno)
                         ->where('is_deleted', 0)
                         ->where('registry_status', 'Active')
                         ->first();

        return !is_null($document);
    }

    public function getDoneDocument($office){

        $document = $this->db->table('docdetails')
                         ->select('doc_controlno')
                         ->where('status', 'I')
                         ->where('office_destination', $office)
                         ->get()->getResultArray();

        return $document;
    }


    public function getRouteno($controlno) {

        $query = $this->like('ref_office_controlno', $controlno)->first();

        if (empty($query)) {
            return false;
        }

        return $query;
    }

    public function getDocInfo($routeno){

        $query = $this->db->table('docregistry dr');
        $query->select([
            'dr.subject',
            'dr.datelog',
            'dr.timelog',
            'o.officename',
            'GROUP_CONCAT(DISTINCT(dt.type_code)) AS ddoctype',
            'GROUP_CONCAT(DISTINCT(dt.type_desc)) AS ddoctype_desc'
        ]);
        $query->join('office o', 'dr.officecode = o.officecode');
        $query->join('registry_doctype rd', 'rd.route_no = dr.route_no');
        $query->join('doc_type dt', 'rd.type_code = dt.type_code AND dt.deleted_at IS NULL', 'left');
        $query->groupBy([
            'dr.subject',
            'o.officename',
            'dr.datelog',
            'dr.timelog'
        ]);    
        $query->where('dr.route_no', $routeno); 
        $query = $query->get()->getRowArray();

        return $query;

    }

    

    
}

