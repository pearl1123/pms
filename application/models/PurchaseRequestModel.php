<?php
    defined('BASEPATH') or exit('No direct script access allowed');

    class PurchaseRequestModel extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
        }

        // GET ATTACHMENT LIST
        // =========================================================================================================================================
        public function getPRList($start, $length)
        {
            $this->db->select('l1.pr_id');
            $this->db->where(array('archived' => 0));
            $num = $this->db->get('tbl_purchase_request l1')->num_rows();

            $this->db->select('t1.pr_id, t1.pr_no, t1.date_1, t1.sai_no, t1.date_2, t1.unit_cost, t1.quantity, t1.remarks, t1.requested_by, t1.designation,
                               t1.run_date, t1.approved_by, t1.date_created, t2.office_desc, t3.unit_code, t4.fullname, p.proc_name');
            $this->db->where(array('t1.archived' => 0));
            if($length > 0) {
                $this->db->limit($length, $start);
            }
            $this->db->join('lib_office t2', 't2.office_id = t1.office_id', 'left');
            $this->db->join('lib_unit t3', 't3.unit_id = t1.unit_id', 'left');
            $this->db->join('aauth_users t4', 't4.id = t1.created_by', 'left');
            $this->db->join('lib_procurement_mode p', 'p.proc_id = t1.proc_id', 'left');
            $res = $this->db->get('tbl_purchase_request t1')->result();

            return array($res, $num);
        }

        // GET PURCHASE REQUEST BY ID
        // =========================================================================================================================================
        public function getPurchaseRequestById($pr_id)
        {
            return $this->db->where('pr_id', $pr_id)->get('tbl_purchase_request')->row();
        }

        // SAVE PURCHASE REQUEST
        // =========================================================================================================================================
        public function savePurchaseRequest($data)
        {
            $this->db->insert("tbl_purchase_request", $data);
            return $this->db->insert_id();
        }

        // UPDATE PURCHASE REQUEST
        // =========================================================================================================================================
        public function updatePurchaseRequest($pr_data, $pr_id)
        {
            $this->db->update('tbl_purchase_request', $pr_data, array('pr_id' => $pr_id));
            return $this->db->affected_rows();
        }
    }
?>