<?php
    defined('BASEPATH') or exit('No direct script access allowed');

    class BACModel extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
        }

        // GET BIDS AND AWARDS LIST
        // =========================================================================================================================================
        public function getBACList($start, $length)
        {
            $this->db->select('l1.bac_id');
            $this->db->where(array('archived' => 0));
            $num = $this->db->get('tbl_bids_and_awards l1')->num_rows();

            $this->db->select('t1.bac_id, t1.bac_no, t1.date_1, t1.sai_no, t1.date_2, t1.unit_cost, t1.quantity, t1.remarks, t1.requested_by, t1.designation,
                               t1.run_date, t1.approved_by, t1.date_created, t2.office_desc, t3.unit_code, t4.fullname, p.proc_name');
            $this->db->where(array('t1.archived' => 0));
            if($length > 0) {
                $this->db->limit($length, $start);
            }
            $this->db->join('lib_office t2', 't2.office_id = t1.office_id', 'left');
            $this->db->join('lib_unit t3', 't3.unit_id = t1.unit_id', 'left');
            $this->db->join('aauth_users t4', 't4.id = t1.created_by', 'left');
            $this->db->join('lib_procurement_mode p', 'p.proc_id = t1.proc_id', 'left');
            $res = $this->db->get('tbl_bids_and_awards t1')->result();

            return array($res, $num);
        }

        // GET BIDS AND AWARDS BY ID
        // =========================================================================================================================================
        public function getBACById($bac_id)
        {
            return $this->db->where('bac_id', $bac_id)->get('tbl_bids_and_awards')->row();
        }

        // SAVE BIDS AND AWARDS
        // =========================================================================================================================================
        public function saveBAC($data)
        {
            $this->db->insert("tbl_bids_and_awards", $data);
            return $this->db->insert_id();
        }

        // UPDATE BIDS AND AWARDS
        // =========================================================================================================================================
        public function updateBAC($bac_data, $bac_id)
        {
            $this->db->update('tbl_bids_and_awards', $bac_data, array('bac_id' => $bac_id));
            return $this->db->affected_rows();
        }
    }
?>