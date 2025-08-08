<?php

namespace App\Models;

use CodeIgniter\Model;

class IncomingModel extends Model
{
    
    
    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
    }

    public function incomingQuery($office,$status,$ds = false)
    {
        try {
            $builder = $this->db->table('docdetails dd');

            $builder->select([
                'dd.date_rcv',
                'dd.time_rcv',
                'dd.doc_controlno AS dcon',
                'dd.sequence_no AS ddseq',
                'dds.sequence_no AS prevseq',
                'dd.office_destination',
                'dd.doc_detailno AS ddetail',
                'dd.status AS stats',
                'dd.date_log AS datelog',
                'dd.time_log AS timelog',
                'dd.date_action',
                'dd.time_action',
                'dd.created_date AS dcreated',
                'dd.action_code AS actioncode',
                'at.action_desc AS actiondesc',
                'dr.subject',
                'dr.remarks AS drRem',
                'dr.filename AS attachfile',
                'dds.office_destination AS prevoffice_dest',
                'o.shortname AS origoffice',
                'o.officecode AS origofficecode',
                'po.shortname AS prevoffice',
                'GROUP_CONCAT(DISTINCT dt.type_desc) AS ddoctype',
                'dtt.type_desc AS odoctype',
                'ar.reqaction_desc AS reqaction',
                'ar.reqaction_done AS reqdonecode'
            ]);

            $builder->join('docregistry dr', 'dd.route_no = dr.route_no', 'left');
            $builder->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dd.prev_sequence_no = dds.sequence_no AND dds.detail_status= "Active"', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->join('action_officer ao', 'dr.empcode = ao.empcode AND ao.deleted_at IS NULL', 'left');
            $builder->join('registry_doctype rd', 'dr.route_no = rd.route_no', 'left');
            $builder->join('doc_type dt', 'rd.type_code = dt.type_code AND dt.deleted_at IS NULL', 'left');
            $builder->join('doc_type dtt', 'dr.type_code = dtt.type_code AND dtt.deleted_at IS NULL', 'left');
            $builder->join('office po', 'dds.office_destination = po.officecode', 'left');
            $builder->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left');
            $builder->join('action_taken at', 'dd.action_code = at.action_code', 'left');

            $builder->whereIn('dd.office_destination', $office);
            $builder->where('dd.status', $status);
            if($ds){
                $builder->where('dd.ifdisseminate', 'Y');
            }else{
                $builder->where('dd.ifdisseminate', 'N');
            }
            $builder->where('dd.is_deleted', '0');
            $builder->where('dd.detail_status', 'Active');
            $builder->where('dr.registry_status', 'Active');

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


    public function releasedQuery($office,$status)
    {
        try {
            $builder = $this->db->table('docdetails dd');
            $builder->select([
                'dd.date_rcv',
                'dd.time_rcv',
                'dd.doc_controlno AS dcon',
                'dd.sequence_no AS ddseq',
                'dds.sequence_no AS prevseq',
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
                'adp.lastname AS rel_lastname',
                'adp.firstname AS rel_firstname',
                'adp.middlename AS rel_middlename',
                'adp.office_rep AS rel_office_rep',
                'at.action_desc AS actiondesc',
                'dr.subject',
                'dr.remarks AS drRem',
                'dr.filename AS attachfile',
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
            $builder->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dd.prev_sequence_no = dds.sequence_no AND dds.detail_status= "Active"', 'left');
            $builder->join('docdetails ddp', 'dd.doc_controlno = ddp.doc_controlno AND dd.sequence_no = ddp.prev_sequence_no AND ddp.detail_status= "Active"', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->join('action_officer ao', 'dr.empcode = ao.empcode AND ao.deleted_at IS NULL', 'left');
            $builder->join('action_officer adp', 'ddp.action_officer = adp.empcode AND adp.deleted_at IS NULL', 'left');
            $builder->join('registry_doctype rd', 'dr.route_no = rd.route_no', 'left');
            $builder->join('doc_type dt', 'rd.type_code = dt.type_code AND dt.deleted_at IS NULL', 'left');
            $builder->join('doc_type dtt', 'dr.type_code = dtt.type_code AND dtt.deleted_at IS NULL', 'left');
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
                //->orWhere('ddp.doc_detailno IS NULL')
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
                'ddp.action_officer',
                'adp.lastname',
                'adp.firstname',
                'adp.middlename',
                'adp.office_rep',
            ]);

            $builder->orderBy('dd.date_log', 'DESC');

            $result = $builder->get()->getResultArray();

            if (!empty($result)) {
                return $result;
            } else {
                log_message('error', 'Result Query Returned 0 Rows');
                return false;
            }

        } catch (\Exception $e) {
            log_message('error', 'Error in releasedQuery: ' . $e->getMessage());
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
            $builder->join('doc_type dt', 'rdt.type_code = dt.type_code AND dt.deleted_at IS NULL', 'left');
            $builder->join('office o', 'dr.officecode = o.officecode', 'left');
            $builder->join('action_officer ao', 'dr.empcode = ao.empcode AND ao.deleted_at IS NULL', 'left');
            $builder->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left');
            $builder->join('docdetails dds', 'dd.prev_sequence_no = dds.sequence_no AND dds.detail_status = "Active"', 'left');
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
                                ->join('doc_type dt', 'rdt.type_code = dt.type_code AND dt.deleted_at IS NULL', 'left')
                                ->join('office o', 'dr.officecode = o.officecode', 'left')
                                ->join('action_officer ao', 'dr.empcode = ao.empcode AND ao.deleted_at IS NULL', 'left')
                                ->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left')
                                ->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dd.prev_sequence_no = dds.sequence_no AND dds.detail_status= "Active"', 'left')
                                ->join('docdetails ddp', 'dd.doc_controlno = ddp.doc_controlno AND dd.sequence_no = ddp.prev_sequence_no AND ddp.detail_status= "Active"', 'left')
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
                                ->join('doc_type dt', 'rdt.type_code = dt.type_code AND dt.deleted_at IS NULL', 'left')
                                ->join('office o', 'dr.officecode = o.officecode', 'left')
                                ->join('action_officer ao', 'dr.empcode = ao.empcode AND ao.deleted_at IS NULL', 'left')
                                ->join('action_required ar', 'dd.action_required = ar.reqaction_code', 'left')
                                ->join('docdetails dds', 'dd.doc_controlno = dds.doc_controlno AND dd.prev_sequence_no = dds.sequence_no AND dds.detail_status= "Active"', 'left')
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


    
}

