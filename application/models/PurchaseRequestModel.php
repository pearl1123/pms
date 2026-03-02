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
                               t1.run_date, t1.approved_by, t2.office_desc, t3.unit_code, t4.fullname');
            $this->db->where(array('t1.archived' => 0));
            if($length > 0) {
                $this->db->limit($length, $start);
            }
            $this->db->join('lib_office t2', 't2.office_id = t1.office_id', 'left');
            $this->db->join('lib_unit t3', 't3.unit_id = t1.unit_id', 'left');
            $this->db->join('aauth_users t4', 't4.id = t1.created_by', 'left');
            $res = $this->db->get('tbl_purchase_request t1')->result();

            return array($res, $num);
        }
    }
?>