
<?php
    defined('BASEPATH') or exit('No direct script access allowed');

    class PPMPModel extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function getPPMPListView($start, $length)
        {
            $this->db->select('DISTINCT(t1.ppmp_year)');
            $this->db->where(array('t1.archived' => 0));
            $num = $this->db->get('tbl_ppmp t1')->num_rows();

            $this->db->select('DISTINCT(t1.ppmp_year), t2.fullname');
            $this->db->where(array('t1.archived' => 0));
            if ($length > 0) {
                $this->db->limit($length, $start);
            }
            $this->db->join('aauth_users t2', 't2.id = t1.created_by', 'left');
            $res = $this->db->get('tbl_ppmp t1')->result();

            return array($res, $num);
        }

        public function getPPMPItemListView($start, $length)
        {
            $this->db->select('t1.ppmp_id');
            $this->db->where(array('t1.archived' => 0));
            $num = $this->db->get('tbl_ppmp t1')->num_rows();

            $this->db->select('t1.ppmp_id, t1.ppmp_year, t1.ppmp_general_description, t1.ppmp_project_type, t1.ppmp_cost, t1.ppmp_pre_proc, t1.ppmp_remarks,
                                t2.unit_name, t3.fund_name, t2.fullname');
            $this->db->where(array('t1.archived' => 0));
            if ($length > 0) {
                $this->db->limit($length, $start);
            }
            $this->db->join('aauth_users t2', 't2.id = t1.created_by', 'left');
            $res = $this->db->get('tbl_ppmp t1')->result();

            return array($res, $num);
        }
    }
?>
