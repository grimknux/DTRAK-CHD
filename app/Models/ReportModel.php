<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    
    
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function documentQuery($office,$status,$limit,$offset,$searchValue,$filterValue,$ds = false)
    {
        try {

            $limit = intval($limit);
            $offset = intval($offset);

            $total_builder = $this->db->table('docdetails dd');
            $total_builder->select('dd.*');
            $total_builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');

            $total_builder->whereIn('dd.office_destination', $office);
            $total_builder->whereIn('dd.status', $status);
            if($ds){
                $total_builder->where('dd.ifdisseminate', 'Y');
            }else{
                $total_builder->where('dd.ifdisseminate', 'N');
            }
            $total_builder->where('dd.is_deleted', '0');
            $total_builder->where('dd.detail_status', 'Active');
            $total_builder->where('dr.registry_status', 'Active');
            $total_builder->groupBy([
                'dd.doc_controlno',
                'dd.date_rcv',
                'dd.time_rcv',
                'dd.date_action',
                'dd.time_action',
                'dd.sequence_no',
                'dd.office_destination',
                'dd.doc_detailno',
                'dd.status',
                'dd.action_code',
                
            ]);


            $builder = $this->db->table('docdetails dd');
            $builder->select([
                'dd.date_rcv',
                'dd.time_rcv',
                'dd.receive_by',
                'dd.doc_controlno AS dcon',
                'dd.sequence_no AS ddseq',
                'dd.office_destination',
                'dd.doc_detailno AS ddetail',
                'dd.status AS stats',
                'dd.date_log AS datelog',
                'dd.time_log AS timelog',
                'dd.date_action',
                'dd.time_action',
                'dd.release_date',
                'dd.release_time',
                'dd.remarks as d_rem',
                'dd.remarks2 as d_rem2',
                'aorec.lastname as rec_lastname',
                'aorec.firstname as rec_firstname',
                'aorec.middlename as rec_middlename',
                'aorec.office_rep as rec_office_rep',
                'aoact.lastname as act_lastname',
                'aoact.firstname as act_firstname',
                'aoact.middlename as act_middlename',
                'aoact.office_rep as act_office_rep',
                'aorel.lastname as rel_lastname',
                'aorel.firstname as rel_firstname',
                'aorel.middlename as rel_middlename',
                'aorel.office_rep as rel_office_rep',
                'dd.created_date AS dcreated',
                'dd.action_code AS actioncode',
                'at.action_desc AS actiondesc',
                'dr.subject',
                'dr.remarks AS drRem',
                'dr.filename AS attachfile',
                'dds.sequence_no AS prevseq',
                'dds.office_destination AS prevoffice_dest',
                'ddp.date_rcv as dest_date_rcv',
                'ddp.time_rcv as dest_time_rcv',
                'o.shortname AS origoffice',
                'o.officecode AS origofficecode',
                'po.shortname AS prevoffice',
                'pop.shortname AS nextoffice',
                'GROUP_CONCAT(DISTINCT dt.type_desc) AS ddoctype',
                'dtt.type_desc AS odoctype',
                'ar.reqaction_desc AS reqaction',
                'ar.reqaction_done AS reqdonecode'
            ]);

            $builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');
            $builder->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dd.prev_sequence_no = dds.sequence_no AND dds.detail_status= "Active"', 'left');
            $builder->join('docdetails ddp', 'dd.doc_controlno = ddp.doc_controlno AND dd.sequence_no = ddp.prev_sequence_no AND ddp.detail_status= "Active"', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->join('action_officer ao', 'dr.empcode = ao.empcode', 'left');
            $builder->join('action_officer aorec', 'dd.receive_by = aorec.empcode', 'left');
            $builder->join('action_officer aoact', 'dd.action_by = aoact.empcode', 'left');
            $builder->join('action_officer aorel', 'dd.release_by = aorel.empcode', 'left');
            $builder->join('registry_doctype rd', 'dr.route_no = rd.route_no', 'left');
            $builder->join('doc_type dt', 'rd.type_code = dt.type_code', 'left');
            $builder->join('doc_type dtt', 'dr.type_code = dtt.type_code', 'left');
            $builder->join('office po', 'dds.office_destination = po.officecode', 'left');
            $builder->join('office pop', 'ddp.office_destination = pop.officecode', 'left');
            $builder->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left');
            $builder->join('action_taken at', 'dd.action_code = at.action_code', 'left');

            $builder->whereIn('dd.office_destination', $office);
            $builder->whereIn('dd.status', $status);
            if($ds){
                $builder->where('dd.ifdisseminate', 'Y');
            }else{
                $builder->where('dd.ifdisseminate', 'N');
            }
            $builder->where('dd.is_deleted', '0');
            $builder->where('dd.detail_status', 'Active');
            $builder->where('dr.registry_status', 'Active');
            if (!empty($searchValue)) {
                $builder->groupStart()
                        ->like('dd.date_rcv', $searchValue)
                        ->orLike('dd.time_rcv', $searchValue)
                        ->orLike('dd.receive_by', $searchValue)
                        ->orLike('dd.doc_controlno', $searchValue)
                        ->orLike('o.shortname', $searchValue)
                        ->orLike('po.shortname', $searchValue)
                        ->orLike('dr.subject', $searchValue)
                        ->orLike('dt.type_desc', $searchValue)
                        ->groupEnd();
            }

            if (!empty($filterValue['datefromFilter']) && !empty($filterValue['datetoFilter'])) {
                $builder->groupStart()
                    ->where('dd.date_rcv >=', $filterValue['datefromFilter'])
                    ->where('dd.date_rcv <=', $filterValue['datetoFilter'])
                    ->groupEnd();
            } elseif (!empty($filterValue['datefromFilter'])) {
                $builder->where('dd.date_rcv >=', $filterValue['datefromFilter']);
            } elseif (!empty($filterValue['datetoFilter'])) {
                $builder->where('dd.date_rcv <=', $filterValue['datetoFilter']);
            }
            
            if(!empty($filterValue['officeempFilter'])){
                $builder->where('dd.office_destination', $filterValue['officeempFilter']);
            }

            $builder->groupBy([
                'dd.doc_controlno',
                'dd.date_rcv',
                'dd.time_rcv',
                'dd.date_action',
                'dd.time_action',
                'dd.sequence_no',
                'dd.office_destination',
                'dd.doc_detailno',
                'dd.status',
                'dd.action_code',
                'dd.remarks',
                'dd.remarks2',
                'aorec.lastname',
                'aorec.firstname',
                'aorec.middlename',
                'aorec.office_rep',
                'aoact.lastname',
                'aoact.firstname',
                'aoact.middlename',
                'aoact.office_rep',
                'aorel.lastname',
                'aorel.firstname',
                'aorel.middlename',
                'aorel.office_rep',
                'dd.release_date',
                'dd.release_time',
                'at.action_desc',
                'dr.subject',
                'dr.remarks',
                'dr.filename',
                'dds.sequence_no',
                'dds.office_destination',
                'ddp.date_rcv',
                'ddp.time_rcv',
                'o.shortname',
                'o.officecode',
                'po.shortname',
                'pop.shortname',
                'dtt.type_desc',
                'ar.reqaction_desc',
                
            ]);

            $builder->orderBy('dd.date_rcv', 'DESC');

            $filtered_builder = clone $builder;

            if(!empty($limit) && $limit != -1) {
                $get_query = $builder->limit($limit, $offset)->get()->getResultArray();

            }else{
                $get_query = $builder->get()->getResultArray();
            }
            $totalRecords = $total_builder->countAllResults(false);  // Total unfiltered records
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


    public function releasedQuery($office,$status)
    {
        try {
            $builder = $this->db->table('docdetails dd');
            $builder->select([
                'dd.date_rcv',
                'dd.time_rcv',
                'dd.doc_controlno AS dcon',
                'dd.sequence_no AS ddseq',
                'dd.office_destination',
                'dd.doc_detailno AS ddetail',
                'dd.status AS stats',
                'dd.date_log AS datelog',
                'dd.time_log AS timelog',
                'dd.created_date AS dcreated',
                'dd.action_code AS actioncode',
                'dd.release_date AS reldate',
                'dd.release_time AS reltime',
                'ddp.action_officer AS actionofficer',
                'at.action_desc AS actiondesc',
                'dr.subject',
                'dr.remarks AS drRem',
                'dr.filename AS attachfile',
                'dds.sequence_no AS prevseq',
                'dds.office_destination AS prevoffice_dest',
                'o.shortname AS origoffice',
                'o.officecode AS origofficecode',
                'po.shortname AS prevoffice',
                'GROUP_CONCAT(DISTINCT dt.type_desc) AS ddoctype',
                'dtt.type_desc AS odoctype',
                'ar.reqaction_desc AS reqaction',
                'ar.reqaction_done AS reqdonecode',
                'ddp.doc_detailno AS ddpdetail',
                'ddp.status AS ddpstatus',
                'pop.officecode AS pop_officecode',
                'pop.officename AS pop_officename',
                'ds.status_desc AS statusdesc',
                'arr.reqaction_desc AS destactionrequire'
            ]);
            
            $builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');
            $builder->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dds.sequence_no = (dd.sequence_no - 1)', 'left');
            $builder->join('docdetails ddp', 'dd.doc_controlno = ddp.doc_controlno AND ddp.sequence_no = (dd.sequence_no + 1)', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->join('action_officer ao', 'dr.empcode = ao.empcode', 'left');
            $builder->join('registry_doctype rd', 'dr.route_no = rd.route_no', 'left');
            $builder->join('doc_type dt', 'rd.type_code = dt.type_code', 'left');
            $builder->join('doc_type dtt', 'dr.type_code = dtt.type_code', 'left');
            $builder->join('office po', 'dds.office_destination = po.officecode', 'left');
            $builder->join('office pop', 'ddp.office_destination = pop.officecode', 'left');
            $builder->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left');
            $builder->join('action_taken at', 'dd.action_code = at.action_code', 'left');
            $builder->join('docstatus ds', 'dd.status = ds.status_code', 'left');
            $builder->join('action_required arr', 'ddp.action_required = arr.reqaction_code', 'left');
            
            $builder->whereIn('dd.office_destination', $office); // Replace with actual values
            $builder->whereIn('dd.status', $status); // Replace with actual values
            $builder->where('dd.is_deleted', '0');
            $builder->where('dd.detail_status', 'Active');
            $builder->where('dr.registry_status', 'Active');
            $builder->groupStart()
                ->where('ddp.status', 'A')
                ->orWhere('ddp.doc_detailno IS NULL')
                ->groupEnd();
            
            $builder->groupBy([
                'dd.doc_controlno',
                'dd.date_rcv',
                'dd.time_rcv',
                'dd.sequence_no',
                'dd.office_destination',
                'dd.doc_detailno',
                'dd.status',
                'dd.action_code',
                'at.action_desc',
                'dr.subject',
                'dr.remarks',
                'dr.filename',
                'dds.sequence_no',
                'dds.office_destination',
                'o.shortname',
                'o.officecode',
                'po.shortname',
                'dtt.type_desc',
                'ar.reqaction_desc',
                'ddp.doc_detailno',
                'ddp.status',
                'pop.officecode',
                'pop.officename',
                'ds.status_desc',
                'ddp.action_officer'
            ]);

            $builder->orderBy('dd.date_log', 'DESC');

            $result = $builder->get();

            if(count($result->getResultArray()) >= 1)
            {
                return $result->getResultArray();
            }
            else
            {
                log_message('error', 'Result Query Return with 0 Rows');
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in incomingQuery: ' . $e->getMessage());
            return false;
        }
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

    

    public function receiveData($docdetail,$status){

        try {
            $builder = $this->db->table('docdetails dd');
            $builder->select('
                dd.doc_controlno AS dcon,
                dd.doc_detailno AS ddetail,
                dd.sequence_no AS seqno,
                dds.sequence_no AS prev_seqno,
                dds.office_destination,
                dr.route_no AS routeno,
                dr.subject,
                GROUP_CONCAT(DISTINCT(dt.type_desc)) AS ddoctype,
                o.officename AS origoffice,
                op.officename AS prevoffice,
                ao.firstname,
                ao.middlename,
                ao.lastname,
                dr.exofficecode,
                dr.exempname,
                dr.no_page AS pageno,
                dr.filename AS attachment,
                dr.userid,
                dr.officecode,
                dr.remarks,
                ao.office_rep AS orep,
                ar.reqaction_desc AS actionreq,
                ar.reqaction_done AS actiondone,
            ');

            $builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');
            $builder->join('registry_doctype rdt', 'dr.route_no = rdt.route_no', 'left');
            $builder->join('doc_type dt', 'rdt.type_code = dt.type_code', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->join('action_officer ao', 'dr.empcode = ao.empcode', 'left');
            $builder->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left');
            $builder->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dds.sequence_no = (dd.sequence_no - 1)', 'left');
            $builder->join('office op', 'dds.office_destination = op.officecode', 'left');

            $builder->where('dd.doc_detailno', $docdetail);
            $builder->where('dd.status', $status);
            $builder->where('dd.is_deleted', '0');
            $builder->where('dd.detail_status', 'Active');
            $builder->where('dr.registry_status', 'Active');

            // Ensure grouping matches selected fields correctly
            $builder->groupBy([
                'dd.doc_controlno',
                'dd.doc_detailno',
                'dd.sequence_no',
                'dds.sequence_no',
                'dds.office_destination',
                'dr.route_no',
                'dr.subject',
                'o.shortname',
                'o.officename',
                'op.shortname',
                'ao.firstname',
                'ao.middlename',
                'ao.lastname',
                'dr.exofficecode',
                'dr.exempname',
                'dr.no_page',
                'dr.filename',
                'dr.userid',
                'dr.officecode',
                'dr.remarks',
                'ao.office_rep',
                'ar.reqaction_desc',
                'ar.reqaction_done'
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


    public function receiveDataArrayWithSeq($docdetail){

        try {
            $builder = $this->db->table('docdetails dd')
                                ->select('
                                    dd.doc_controlno AS dcon,
                                    dd.doc_detailno AS ddetail,
                                    dd.sequence_no AS seqno,
                                    dds.sequence_no AS prev_seqno,
                                    dds.office_destination,
                                    dr.route_no AS routeno,
                                    dr.subject,
                                    GROUP_CONCAT(DISTINCT(dt.type_desc)) AS ddoctype,
                                    o.officename AS origoffice,
                                    op.officename AS prevoffice,
                                    ao.firstname,
                                    ao.middlename,
                                    ao.lastname,
                                    dr.exofficecode,
                                    dr.exempname,
                                    dr.no_page AS pageno,
                                    dr.filename AS attachment,
                                    dr.userid,
                                    dr.officecode,
                                    dr.remarks,
                                    ao.office_rep AS orep,
                                    ar.reqaction_desc AS actionreq,
                                    ar.reqaction_done AS actiondone,
                                    ddp.doc_detailno AS ddpdetail,
                                    ddp.status AS ddpstatus,
                                ')
                                ->join('docregistry dr', 'dd.route_no = dr.route_no', 'left')
                                ->join('registry_doctype rdt', 'dr.route_no = rdt.route_no', 'left')
                                ->join('doc_type dt', 'rdt.type_code = dt.type_code', 'left')
                                ->join('office o', 'dr.officecode = o.officecode', 'left')
                                ->join('action_officer ao', 'dr.empcode = ao.empcode', 'left')
                                ->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left')
                                ->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dds.sequence_no = (dd.sequence_no - 1)', 'left')
                                ->join('docdetails ddp', 'dd.doc_controlno = ddp.doc_controlno AND ddp.sequence_no = (dd.sequence_no + 1)', 'left')
                                ->join('office op', 'dds.office_destination = op.officecode', 'left')

                                ->where('dd.doc_detailno', $docdetail)
                                ->groupStart()
                                    ->where('ddp.status', '')
                                    ->orWhere('ddp.status IS NULL')
                                    ->groupEnd()
                                ->where('dd.is_deleted', '0')
                                ->where('dd.detail_status', 'Active')
                                ->where('dr.registry_status', 'Active')
                                ->groupBy([
                                    'dd.doc_controlno',
                                    'dd.doc_detailno',
                                    'dd.sequence_no',
                                    'dds.sequence_no',
                                    'dds.office_destination',
                                    'dr.route_no',
                                    'dr.subject',
                                    'o.shortname',
                                    'o.officename',
                                    'op.shortname',
                                    'ao.firstname',
                                    'ao.middlename',
                                    'ao.lastname',
                                    'dr.exofficecode',
                                    'dr.exempname',
                                    'dr.no_page',
                                    'dr.filename',
                                    'dr.userid',
                                    'dr.officecode',
                                    'dr.remarks',
                                    'ao.office_rep',
                                    'ar.reqaction_desc',
                                    'ar.reqaction_done',
                                    'ddp.doc_detailno',
                                    'ddp.status'
                                ]);

            $result = $builder->get();
            
            if ($result->getNumRows() > 0) {
                return $result->getRowArray();
            } else {
                throw new \Exception("No Rows Returned!");

            }

        } catch (\Exception $e) {
            log_message('error', "Error retrieving document :{$e->getMessage()}");
            return false;
        }
    }

    public function receiveDataArray($docdetail,$status){

        try {
            $builder = $this->db->table('docdetails dd')
                                ->select('
                                    dd.doc_controlno AS dcon,
                                    dd.doc_detailno AS ddetail,
                                    dd.sequence_no AS seqno,
                                    dds.sequence_no AS prev_seqno,
                                    dds.office_destination,
                                    dr.route_no AS routeno,
                                    dr.subject,
                                    GROUP_CONCAT(DISTINCT(dt.type_desc)) AS ddoctype,
                                    o.officename AS origoffice,
                                    op.officename AS prevoffice,
                                    ao.firstname,
                                    ao.middlename,
                                    ao.lastname,
                                    dr.exofficecode,
                                    dr.exempname,
                                    dr.no_page AS pageno,
                                    dr.filename AS attachment,
                                    dr.userid,
                                    dr.officecode,
                                    dr.remarks,
                                    ao.office_rep AS orep,
                                    ar.reqaction_desc AS actionreq,
                                    ar.reqaction_done AS actiondone
                                ')
                                ->join('docregistry dr', 'dd.route_no = dr.route_no', 'left')
                                ->join('registry_doctype rdt', 'dr.route_no = rdt.route_no', 'left')
                                ->join('doc_type dt', 'rdt.type_code = dt.type_code', 'left')
                                ->join('office o', 'dr.officecode = o.officecode', 'left')
                                ->join('action_officer ao', 'dr.empcode = ao.empcode', 'left')
                                ->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left')
                                ->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dds.sequence_no = (dd.sequence_no - 1)', 'left')
                                ->join('office op', 'dds.office_destination = op.officecode', 'left')

                                ->where('dd.doc_detailno', $docdetail)
                                ->whereIn('dd.status', $status)
                                ->where('dd.is_deleted', '0')
                                ->where('dd.detail_status', 'Active')
                                ->where('dr.registry_status', 'Active')
                                ->groupBy([
                                    'dd.doc_controlno',
                                    'dd.doc_detailno',
                                    'dd.sequence_no',
                                    'dds.sequence_no',
                                    'dds.office_destination',
                                    'dr.route_no',
                                    'dr.subject',
                                    'o.shortname',
                                    'o.officename',
                                    'op.shortname',
                                    'ao.firstname',
                                    'ao.middlename',
                                    'ao.lastname',
                                    'dr.exofficecode',
                                    'dr.exempname',
                                    'dr.no_page',
                                    'dr.filename',
                                    'dr.userid',
                                    'dr.officecode',
                                    'dr.remarks',
                                    'ao.office_rep',
                                    'ar.reqaction_desc',
                                    'ar.reqaction_done'
                                ]);

            $result = $builder->get();
            
            if ($result->getNumRows() > 0) {
                return $result->getRowArray();
            } else {
                throw new \Exception("No Rows Returned!");

            }

        } catch (\Exception $e) {
            log_message('error', "Error retrieving document :{$e->getMessage()}");
            return false;
        }
    }


    public function checkAttachIfValidUser($filename){
        $builder = $this->db->table('docdetails dd');
        $builder->select('dd.office_destination');
        $builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');
        $builder->where('dr.filename', $filename);
        $query = $builder->get()->getResultArray();

        return $query;
    }

    public function timelineQuery($limit,$offset,$searchValue,$filterValue,$ds = false) 
    {

        try {

            $total_builder = $this->db->table('docdetails dd')->select('dd.doc_controlno');
            $total_builder->where('dd.sequence_no', 1);
            $total_builder->where('dd.status !=', 'A');
            $total_builder->groupBy([
                'dd.doc_controlno',
            ]);
                
            // Subquery: last_seq
            $lastSeqSubQuery = "
                SELECT docdetails.doc_controlno,
                docdetails.sequence_no,
                docdetails.office_destination,
                docdetails.date_rcv,
                docdetails.time_rcv,
                docdetails.date_action,
                docdetails.time_action,
                docdetails.release_date,
                docdetails.release_time,
                docdetails.status,
                docdetails.prev_sequence_no,
                docdetails.remarks, docdetails.remarks2, 
                docdetails.action_code
                
                FROM docdetails
                JOIN (
                    SELECT doc_controlno, MAX(sequence_no) AS max_seq
                    FROM docdetails
                    GROUP BY doc_controlno
                ) max_tracker 
                ON docdetails.doc_controlno = max_tracker.doc_controlno 
                AND docdetails.sequence_no = max_tracker.max_seq
            ";

            // Subquery: ddoctype
            $ddoctypeSubQuery = "
                SELECT 
                    rd.route_no,
                    GROUP_CONCAT(DISTINCT dt.type_code ORDER BY dt.type_code SEPARATOR ', ') AS type_codes,
                    GROUP_CONCAT(DISTINCT dt.type_desc ORDER BY dt.type_desc SEPARATOR ', ') AS type_descs
                FROM registry_doctype rd
                JOIN doc_type dt ON rd.type_code = dt.type_code
                GROUP BY rd.route_no
            ";

            // Main Query
            $builder = $this->db->table('docdetails dd');

            $builder->select("
                dd.doc_controlno as dcon,
                dr.subject,
                o.shortname AS orig_office,
                po.shortname AS current_office,
                prev_po.shortname AS prev_current_office,
                ddoctype.type_codes,
                ddoctype.type_descs,
                dd.sequence_no AS first_seqno,
                dd.date_rcv AS first_date_rcv,
                dd.time_rcv AS first_time_rcv,
                last_seq.office_destination AS last_office_destination,
                last_seq.sequence_no AS last_seqno,
                last_seq.status AS last_seq_status,
                last_seq.date_rcv AS last_seq_date_rcv,
                last_seq.time_rcv AS last_seq_time_rcv,
                last_seq.date_action AS last_seq_date_action,
                last_seq.time_action AS last_seq_time_action,
                last_seq.release_date AS last_seq_release_date,
                last_seq.release_time AS last_seq_release_time,
                last_seq.remarks AS last_seq_remarks,
                last_seq.remarks2 AS last_seq_remarks2,,
                last_seq_at.action_desc as last_action_taken,
                prev_last_seq.office_destination AS prev_last_office_destination,
                prev_last_seq.sequence_no AS prev_last_seqno,
                prev_last_seq.status AS prev_last_seq_status,
                prev_last_seq.date_rcv AS prev_last_seq_date_rcv,
                prev_last_seq.time_rcv AS prev_last_seq_time_rcv,
                prev_last_seq.date_action AS prev_last_seq_date_action,
                prev_last_seq.time_action AS prev_last_seq_time_action,
                prev_last_seq.release_date AS prev_last_seq_release_date,
                prev_last_seq.release_time AS prev_last_seq_release_time,
                prev_last_seq.remarks AS prev_last_seq_remarks,
                prev_last_seq.remarks2 AS prev_last_seq_remarks2,
                prev_seq_at.action_desc as prev_action_taken
            ");

            $builder->join("({$lastSeqSubQuery}) last_seq", 'dd.doc_controlno = last_seq.doc_controlno', 'left');
            $builder->join('docdetails prev_last_seq', 'last_seq.doc_controlno = prev_last_seq.doc_controlno AND last_seq.prev_sequence_no = prev_last_seq.sequence_no', 'left');
            $builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->join('office po', 'last_seq.office_destination = po.officecode', 'left');
            $builder->join('office prev_po', 'prev_last_seq.office_destination = prev_po.officecode', 'left');
            $builder->join('action_taken prev_seq_at', 'prev_last_seq.action_code = prev_seq_at.action_code', 'left');
            $builder->join('action_taken last_seq_at', 'last_seq.action_code = last_seq_at.action_code', 'left');
            $builder->join("({$ddoctypeSubQuery}) ddoctype", 'dr.route_no = ddoctype.route_no', 'left');

            // Filters
            $builder->where('dd.sequence_no', 1);
            $builder->where('dd.status !=', 'A');
            if (!empty($searchValue)) {
                $builder->groupStart()
                        ->like('dd.doc_controlno', $searchValue)
                        ->orLike('dr.subject', $searchValue)
                        ->orLike('o.shortname', $searchValue)
                        ->orLike('po.shortname', $searchValue)
                        ->orLike('ddoctype.type_descs', $searchValue)
                        ->orLike('last_seq_at.action_desc', $searchValue)
                        ->orLike('prev_seq_at.action_desc', $searchValue)
                        ->groupEnd();
            }

            if (!empty($filterValue['officeFilter'])) {
                $builder->where('dr.officecode', $filterValue['officeFilter']);
            }
            if (!empty($filterValue['doctypeFilter'])) {
                $builder->where("FIND_IN_SET('" . esc($filterValue['doctypeFilter']) . "', ddoctype.type_codes) > 0");
            }
            if (!empty($filterValue['docstatusFilter'])) {
                if($filterValue['docstatusFilter'] == 'done'){
                    $builder->where('last_seq.status',  'I');
                }elseif($filterValue['docstatusFilter'] == 'ongoing'){
                    $builder->where('last_seq.status !=',  'I');
                }
            }

            $builder->orderBy('dd.doc_controlno', 'ASC');

            $filtered_builder = clone $builder;

            if(!empty($limit) && $limit != -1) {
                $get_query = $builder->limit($limit, $offset)->get()->getResultArray();

            }else{
                $get_query = $builder->get()->getResultArray();
            }
            $totalRecords = $total_builder->countAllResults(false);  // Total unfiltered records
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
    
}




/*
$total_builder = $this->db->table('docdetails dd');
            $total_builder->select('dd.*');
            $total_builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');
            $total_builder->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dds.sequence_no = (dd.sequence_no - 1)', 'left');
            $total_builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $total_builder->join('action_officer ao', 'dr.empcode = ao.empcode', 'left');
            $total_builder->join('registry_doctype rd', 'dr.route_no = rd.route_no', 'left');
            $total_builder->join('doc_type dt', 'rd.type_code = dt.type_code', 'left');
            $total_builder->join('doc_type dtt', 'dr.type_code = dtt.type_code', 'left');
            $total_builder->join('office po', 'dds.office_destination = po.officecode', 'left');
            $total_builder->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left');
            $total_builder->join('action_taken at', 'dd.action_code = at.action_code', 'left');

            $total_builder->whereIn('dd.office_destination', $office);
            $total_builder->where('dd.status', $status);
            if($ds){
                $total_builder->where('dd.ifdisseminate', 'Y');
            }else{
                $total_builder->where('dd.ifdisseminate', 'N');
            }
            $total_builder->where('dd.is_deleted', '0');
            $total_builder->where('dd.detail_status', 'Active');
            $total_builder->where('dr.registry_status', 'Active');
            $total_builder->groupBy([
                'dd.doc_controlno',
                'dd.date_rcv',
                'dd.time_rcv',
                'dd.date_action',
                'dd.time_action',
                'dd.sequence_no',
                'dd.office_destination',
                'dd.doc_detailno',
                'dd.status',
                'dd.action_code',
                'at.action_desc',
                'dr.subject',
                'dr.remarks',
                'dr.filename',
                'dds.sequence_no',
                'dds.office_destination',
                'o.shortname',
                'o.officecode',
                'po.shortname',
                'dtt.type_desc',
                'ar.reqaction_desc',
                
            ]);*/