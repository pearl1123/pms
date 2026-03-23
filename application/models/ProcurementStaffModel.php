<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProcurementStaffModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // GET REVIEW LIST
    // Returns distinct PRs that have a review record, with attachment counts.
    // =========================================================================================================================================
    public function getReviewList($start, $length, $status_filter = null)
    {
        // ---- COUNT QUERY ----
        $this->db->select('COUNT(DISTINCT r.pr_id) as total');
        $this->db->from('tbl_purchase_request_review r');
        $this->db->where('r.archived', 0);
        if (!empty($status_filter)) {
            $this->db->where('r.status', $status_filter);
        }
        $count_row = $this->db->get()->row();
        $total     = $count_row ? intval($count_row->total) : 0;

        // ---- DATA QUERY ----
        $this->db->select("
                MIN(r.preq_id)                                              AS preq_id,
                r.pr_id,
                pr.pr_no,
                pr.sai_no,
                pr.date_created,
                p.proc_name,
                o.office_desc,
                u.fullname                                                  AS encoded_by,
                r.status,

                -- Total ACTIVE required attachments for this procurement mode
                (
                    SELECT COUNT(*)
                    FROM   lib_procurement_attachment pa2
                    INNER JOIN lib_attachments a2 ON a2.attachment_id = pa2.attachment_id
                    WHERE  pa2.proc_code = p.proc_code
                    AND    pa2.archived  = 0
                    AND    a2.archived   = 0
                )                                                           AS total_attachments,

                -- Uploaded files that match ACTIVE required attachments for this PR
                (
                    SELECT COUNT(*)
                    FROM   lib_attachment_per_pr lap2
                    INNER JOIN lib_procurement_attachment pa3 ON pa3.attachment_id = lap2.attachment_id
                                                            AND pa3.proc_code     = p.proc_code
                                                            AND pa3.archived      = 0
                    INNER JOIN lib_attachments a3             ON a3.attachment_id  = lap2.attachment_id
                                                            AND a3.archived       = 0
                    WHERE  lap2.pr_id    = r.pr_id
                    AND    lap2.archived = 0
                    AND    lap2.file_name IS NOT NULL
                    AND    lap2.file_name != ''
                )                                                           AS uploaded_count
            ", FALSE);

        $this->db->from('tbl_purchase_request_review r');
        $this->db->join('tbl_purchase_request pr',  'pr.pr_id    = r.pr_id',     'left');
        $this->db->join('lib_procurement_mode p',   'p.proc_id   = pr.proc_id',  'left');
        $this->db->join('lib_office o',             'o.office_id = pr.office_id', 'left');
        $this->db->join('aauth_users u',            'u.id        = pr.created_by', 'left');

        $this->db->where('r.archived', 0);

        if (!empty($status_filter)) {
            $this->db->where('r.status', $status_filter);
        }

        $this->db->group_by('r.pr_id');
        $this->db->order_by('r.pr_id', 'DESC');

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $rows = $this->db->get()->result();

        return [$rows, $total];
    }

    // GET REVIEW SUMMARY
    // Returns counts grouped by status for the summary cards.
    // =========================================================================================================================================
    public function getReviewSummary()
    {
        // Count distinct PRs per status (not individual review rows)
        $this->db->select('status, COUNT(DISTINCT pr_id) AS cnt', FALSE);
        $this->db->from('tbl_purchase_request_review');
        $this->db->where('archived', 0);
        $this->db->group_by('status');

        return $this->db->get()->result();
    }

    // GET PR DETAIL FOR REVIEW
    // Returns the PR header joined with office, procurement mode,
    // =========================================================================================================================================
    public function getPRDetailForReview($pr_id, $preq_id = null)
    {
        $this->db->select("
            pr.pr_id,
            pr.pr_no,
            pr.sai_no,
            pr.date_1,
            pr.date_2,
            pr.remarks,
            pr.requested_by,
            pr.designation,
            pr.date_created,
            p.proc_id,
            p.proc_name,
            p.proc_code,
            o.office_desc,
            u.fullname  AS encoded_by,
            r.status,
            r.preq_id
        ", FALSE);

        $this->db->from('tbl_purchase_request pr');
        $this->db->join('lib_procurement_mode p',          'p.proc_id   = pr.proc_id',    'left');
        $this->db->join('lib_office o',                    'o.office_id = pr.office_id',  'left');
        $this->db->join('aauth_users u',                   'u.id        = pr.created_by', 'left');
        $this->db->join('tbl_purchase_request_review r',   'r.pr_id     = pr.pr_id AND r.archived = 0', 'left');

        $this->db->where('pr.pr_id', $pr_id);
        $this->db->where('pr.archived', 0);

        // If a specific preq_id is supplied, pin to that review row
        if (!empty($preq_id)) {
            $this->db->where('r.preq_id', $preq_id);
        }

        $this->db->limit(1);

        return $this->db->get()->row();
    }

    // GET PR ITEMS
    // Returns all active items for a PR with item description and unit
    // =========================================================================================================================================
    public function getPRItems($pr_id)
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
                CASE WHEN t2.item_source = "library"  THEN i_lib.item_description   ELSE NULL END,
                CASE WHEN t2.item_source = "diet"     THEN i_diet.name              ELSE NULL END,
                CASE WHEN t2.item_source = "pharmacy" THEN i_pharm.brand_name       ELSE NULL END,
                "Unknown Item"
            ) AS item_description,
            COALESCE(
                CASE WHEN t2.item_source = "library"  THEN u_lib.unit_code          ELSE NULL END,
                CASE WHEN t2.item_source = "diet"     THEN i_diet.unit              ELSE NULL END,
                CASE WHEN t2.item_source = "pharmacy" THEN i_dosage.dosage_name     ELSE NULL END,
                ""
            ) AS unit_code
        ', FALSE);

        $this->db->from('lib_purchase_request_items t1');
        $this->db->join('lib_stocks t2',                    't2.stock_id = t1.stock_id',                              'left');

        // Library source
        $this->db->join('lib_item i_lib',                   'i_lib.item_id  = t2.item_id AND t2.item_source = "library"',  'left');
        $this->db->join('lib_unit u_lib',                   'u_lib.unit_id  = t2.unit_id AND t2.item_source = "library"',  'left');

        // Diet source
        $this->db->join('isms_db.item_list i_diet',         'i_diet.id      = t2.item_id AND t2.item_source = "diet"',     'left');

        // Pharmacy source
        $this->db->join('isms_db.pcsr_brand_info i_pharm',  'i_pharm.id     = t2.item_id AND t2.item_source = "pharmacy"', 'left');
        $this->db->join('isms_db.pcsr_generic_info i_gen',  'i_gen.id       = i_pharm.generic_id',                         'left');
        $this->db->join('isms_db.pcsr_dosage_form i_dosage', 'i_dosage.id    = i_gen.dosage_id',                             'left');

        $this->db->where('t1.pr_id',   $pr_id);
        $this->db->where('t1.archived', 0);

        return $this->db->get()->result();
    }

    // GET PR ATTACHMENTS
    // Returns ALL required attachments for the PR's procurement mode,
    // =========================================================================================================================================
    public function getPRAttachments($pr_id)
    {
        // First get the proc_code for this PR
        $pr = $this->db
            ->select('pr.proc_id, p.proc_code')
            ->from('tbl_purchase_request pr')
            ->join('lib_procurement_mode p', 'p.proc_id = pr.proc_id', 'left')
            ->where('pr.pr_id', $pr_id)
            ->where('pr.archived', 0)
            ->limit(1)
            ->get()
            ->row();

        if (!$pr) return [];

        $this->db->select("
            pa.proc_attch_id,
            pa.attachment_id,
            pa.required,
            a.attachment_name,
            lap.attachment_per_id,
            lap.file_name,
            lap.original_file_name,
            lap.remarks
        ", FALSE);

        $this->db->from('lib_procurement_attachment pa');
        $this->db->join(
            'lib_attachments a',
            'a.attachment_id = pa.attachment_id AND a.archived = 0',
            'inner'
        );
        $this->db->join(
            'lib_attachment_per_pr lap',
            'lap.attachment_id = pa.attachment_id
         AND lap.pr_id = ' . $this->db->escape($pr_id) . '
         AND lap.archived = 0',
            'left'
        );

        $this->db->where('pa.proc_code', $pr->proc_code);
        $this->db->where('pa.archived',  0);
        $this->db->order_by('pa.proc_attch_id', 'ASC');

        return $this->db->get()->result();

        // All required attachments for the procurement mode OUTER-joined with uploads
        $this->db->select('
            a.attachment_id,
            a.attachment_name,
            pa.required,
            pa.proc_attch_id,
            lap.attachment_per_id,
            lap.file_name,
            lap.original_file_name,
            lap.remarks
        ', FALSE);

        $this->db->from('lib_procurement_attachment pa');
        $this->db->join('lib_attachments a',       'a.attachment_id  = pa.attachment_id', 'inner');
        $this->db->join(
            'lib_attachment_per_pr lap',
            "lap.attachment_id = a.attachment_id AND lap.pr_id = " . $this->db->escape($pr_id) . " AND lap.archived = 0",
            'left'
        );

        $this->db->where('pa.proc_code', $pr->proc_code);
        $this->db->where('pa.archived',  0);
        $this->db->where('a.archived',   0);
        $this->db->order_by('a.attachment_id', 'DESC');

        return $this->db->get()->result();
    }

    // =========================================================================
    // GET REVIEW BY ID
    // Fetches a single review record by preq_id and pr_id.
    // Used to validate that the record exists and is still pending.
    // =========================================================================
    public function getReviewById($preq_id, $pr_id)
    {
        return $this->db
            ->select('preq_id, pr_id, status, archived')
            ->from('tbl_purchase_request_review')
            ->where('preq_id', $preq_id)
            ->where('pr_id',   $pr_id)
            ->where('archived', 0)
            ->limit(1)
            ->get()
            ->row();
    }

    // =========================================================================
    // UPDATE REVIEW STATUS
    // Updates ALL review rows for a given pr_id (approved / rejected).
    // A PR can have multiple rows (one per attachment + one per item),
    // so all rows are updated together.
    // =========================================================================
    public function updateReviewStatus($data, $pr_id)
    {
        $this->db->where('pr_id',   $pr_id);
        $this->db->where('archived', 0);
        $this->db->update('tbl_purchase_request_review', $data);

        return $this->db->affected_rows();
    }
}
