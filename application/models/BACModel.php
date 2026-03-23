<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BACModel extends CI_Model
{
    public function getBACList($start, $length)
    {
        // 1. Get Total Count
        $this->db->where('archived', 0);
        $num = $this->db->count_all_results('tbl_bac');

        // 2. Get Data with Joins
        $this->db->select('t1.procurement_id as bac_id, t1.sai as sai_no, t1.department as office_desc, t1.status, t1.date_created, t4.fullname');
        $this->db->from('tbl_bac t1');
        $this->db->join('aauth_users t4', 't4.id = t1.created_by', 'left');
        $this->db->where('t1.archived', 0);

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();
        return array($res, $num);
    }
    // Siguraduhin na ang saveBAC mo ay nagbabalik ng ID (meron ka na nito)
    public function saveBAC($data)
    {
        $this->db->insert("tbl_bac", $data);
        return $this->db->insert_id();
    }

    // Dagdag natin ang update status sa review kung gusto mo i-separate sa Controller
    public function updateReviewTable($id, $data)
    {
        $this->db->where('preq_id', $id);
        return $this->db->update('tbl_purchase_request_review', $data);
    }
    public function updateBAC($bac_data, $bac_id)
    {
        $this->db->where('procurement_id', $bac_id);
        $this->db->update('tbl_bac', $bac_data);
        return $this->db->affected_rows();
    }

    public function getApprovedPRReview($start, $length)
    {
        // Count total approved (unique pr_id)
        $this->db->distinct();
        $this->db->select('pr_id');
        $this->db->where('status', 'approved');
        $this->db->where('archived', 0);
        $num = $this->db->get('tbl_purchase_request_review')->num_rows();

        // Get Data - unique per pr_id
        $this->db->select('
        t1.preq_id as bac_id, 
        t1.pr_id as bac_no, 
        t1.status, 
        t1.date_created, 
        t2.fullname as encoded_by,
        p.proc_name
    ');
        $this->db->from('tbl_purchase_request_review t1');
        $this->db->join('aauth_users t2', 't2.id = t1.created_by', 'left');
        $this->db->join('lib_procurement_mode p', 'p.proc_id = t1.proc_id', 'left');
        // $this->db->where('t1.status', 'approved');
        $this->db->where_in('t1.status', ['approved', 'BAC Approved', 'BAC Rejected']);
        $this->db->where('t1.archived', 0);
        $this->db->group_by('t1.pr_id'); // ← RITO YUNG FIX

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();
        return array($res, $num);
    }


    public function getPRItem($pr_id)
    {
        $this->db->select('
        t1.pr_item_id,
        t1.pr_id,
        t1.stock_id,
        t1.quantity,
        t1.unit_cost,
        t1.total_cost,
        t1.remarks,
        t2.item_source,
        COALESCE(
            CASE WHEN t2.item_source = "library"  THEN i_lib.item_description ELSE NULL END,
            CASE WHEN t2.item_source = "diet"     THEN i_diet.name            ELSE NULL END,
            CASE WHEN t2.item_source = "pharmacy" THEN i_pharm.brand_name     ELSE NULL END,
            "Unknown Item"
        ) as item_description,
        COALESCE(
            CASE WHEN t2.item_source = "library"  THEN u_lib.unit_code          ELSE NULL END,
            CASE WHEN t2.item_source = "diet"     THEN i_diet.unit              ELSE NULL END,
            CASE WHEN t2.item_source = "pharmacy" THEN i_dosage.dosage_name     ELSE NULL END,
            ""
        ) as unit_code
    ', FALSE);

        $this->db->from('procurement_db.lib_purchase_request_items t1');
        $this->db->join('procurement_db.lib_stocks t2', 't2.stock_id = t1.stock_id', 'left');

        // Join for General Library items (under procurement_db)
        $this->db->join('procurement_db.lib_item i_lib', 'i_lib.item_id = t2.item_id AND t2.item_source = "library"', 'left');
        $this->db->join('procurement_db.lib_unit u_lib', 'u_lib.unit_id = t2.unit_id AND t2.item_source = "library"', 'left');

        // Join for Diet items (under isms_db)
        $this->db->join('isms_db.item_list i_diet', 'i_diet.id = t2.item_id AND t2.item_source = "diet"', 'left');

        // Join for Pharmacy items (under isms_db)
        $this->db->join('isms_db.pcsr_brand_info i_pharm', 'i_pharm.id = t2.item_id AND t2.item_source = "pharmacy"', 'left');
        $this->db->join('isms_db.pcsr_generic_info i_gen', 'i_gen.id = i_pharm.generic_id', 'left');
        $this->db->join('isms_db.pcsr_dosage_form i_dosage', 'i_dosage.id = i_gen.dosage_id', 'left');

        $this->db->where('t1.pr_id', $pr_id);
        $this->db->where('t1.archived', 0);

        return $this->db->get()->result();
    }
    public function getDashboardStats()
    {
        $this->db->where('archived', 0);
        $all_reviews = $this->db->get('tbl_purchase_request_review')->result();

        $pending = 0;
        $approved = 0;
        $rejected = 0;
        $total = count($all_reviews);

        foreach ($all_reviews as $row) {
            if (strtolower($row->status) == 'pending') $pending++;
            if (strtolower($row->status) == 'approved') $approved++;
            if (strtolower($row->status) == 'rejected') $rejected++;
        }

        echo json_encode([
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'total' => $total
        ]);
    }
}
