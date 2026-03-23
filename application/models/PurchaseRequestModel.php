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
                           t1.run_date, t1.approved_by, t1.date_created, t2.office_desc, t3.unit_code, t4.fullname, p.proc_name,
                           (
                               SELECT COALESCE(
                                   (SELECT status
                                    FROM tbl_purchase_request_review
                                    WHERE pr_id = t1.pr_id
                                    AND archived = 0
                                    LIMIT 1),
                                   "not_sent"
                               )
                           ) AS review_status', FALSE);

        $this->db->where(array('t1.archived' => 0));
        $this->db->order_by('t1.date_created', 'DESC');
        if ($length > 0) {
            $this->db->limit($length, $start);
        }
        $this->db->join('lib_office t2', 't2.office_id = t1.office_id', 'left');
        $this->db->join('lib_unit t3',   't3.unit_id   = t1.unit_id',   'left');
        $this->db->join('aauth_users t4', 't4.id        = t1.created_by', 'left');
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

    // GET PR ITEMS
    // =========================================================================================================================================
    public function getPRItem($pr_id)
    {
        $this->db->select('
        t1.pr_item_id,
        t1.pr_id,
        t1.stock_id,
        t1.quantity,
        t1.unit_cost,
        t1.total_cost,
        t2.stock_onhand,
        t2.item_source,
        COALESCE(
            CASE WHEN t2.item_source = "library"  THEN i_lib.item_description  ELSE NULL END,
            CASE WHEN t2.item_source = "diet"      THEN i_diet.name             ELSE NULL END,
            CASE WHEN t2.item_source = "pharmacy"  THEN i_pharm.brand_name      ELSE NULL END,
            "Unknown Item"
        ) as item_description,
        COALESCE(
            CASE WHEN t2.item_source = "library"  THEN u_lib.unit_code          ELSE NULL END,
            CASE WHEN t2.item_source = "diet"      THEN i_diet.unit             ELSE NULL END,
            CASE WHEN t2.item_source = "pharmacy"  THEN i_dosage.dosage_name    ELSE NULL END,
            ""
        ) as unit_code
    ', FALSE);

        $this->db->from('lib_purchase_request_items t1');
        $this->db->join('lib_stocks t2',              't2.stock_id = t1.stock_id',                   'left');

        // Library source
        $this->db->join('lib_item i_lib',             'i_lib.item_id = t2.item_id AND t2.item_source = "library"',   'left');
        $this->db->join('lib_unit u_lib',             'u_lib.unit_id = t2.unit_id AND t2.item_source = "library"',   'left');

        // Diet source
        $this->db->join('isms_db.item_list i_diet',   'i_diet.id = t2.item_id AND t2.item_source = "diet"',          'left');

        // Pharmacy source
        $this->db->join('isms_db.pcsr_brand_info i_pharm',  'i_pharm.id = t2.item_id AND t2.item_source = "pharmacy"',       'left');
        $this->db->join('isms_db.pcsr_generic_info i_gen',  'i_gen.id = i_pharm.generic_id',                                  'left');
        $this->db->join('isms_db.pcsr_dosage_form i_dosage', 'i_dosage.id = i_gen.dosage_id',                                   'left');

        $this->db->where('t1.pr_id', $pr_id);
        $this->db->where('t1.archived', 0);

        return $this->db->get()->result();
    }
}
