<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProcurementStaff extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('aauth');
        $this->load->model('ProcurementStaffModel');

        if (!$this->session->logged_in) {
            $this->session->set_flashdata('fail', 'Session expired. Please Sign in.');
            redirect('User/index');
        }
      
    }

    public function csrf()
    {
        $name  = $this->security->get_csrf_token_name();
        $hash  = $this->security->get_csrf_hash();
        return '<input type="hidden" name="' . $name . '" value="' . $hash . '">';
    }

    public function csrf_ajax()
    {
        return [
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ];
    }

    public function index()
    {
        $data = [
            'csrf'      => $this->csrf(),
            'csrf_ajax' => $this->csrf_ajax(),
            'fullname'  => $this->session->fullname
        ];

        $data['notif']  = 1;
        $data['config'] = 1;
        $data['ondue']  = 1;

        $this->load->view('header', $data);
        $this->load->view('procurement_staff/listview');
        $this->load->view('footer');
    }

    // GET REVIEW LIST
    // Server-side DataTable list of PRs that have been sent for review
    // =========================================================================================================================================
    public function getReviewList()
    {
        $draw          = intval($this->input->get('draw'));
        $start         = intval($this->input->get('start'));
        $length        = intval($this->input->get('length'));
        $status_filter = $this->input->get('status_filter');

        $model   = new ProcurementStaffModel();
        $results = $model->getReviewList($start, $length, $status_filter);

        $data = [];

        foreach ($results[0] as $r) {

            $uploaded = intval($r->uploaded_count);
            $total    = intval($r->total_attachments);
            $missing  = $total - $uploaded;

            if ($total > 0) {
                $att_html  = '<span class="badge badge-success mr-1" title="Uploaded">'
                    . '<i class="fas fa-check mr-1"></i>' . $uploaded . ' Uploaded</span>';
                if ($missing > 0) {
                    $att_html .= '<span class="badge badge-danger" title="Missing">'
                        . '<i class="fas fa-times mr-1"></i>' . $missing . ' Missing</span>';
                }
            } else {
                $att_html = '<span class="badge badge-secondary">No Attachments</span>';
            }

            $btn_review = '<button type="button" class="btnReview btn btn-sm btn-primary btn-flat mr-1"'
                . ' data-toggle="tooltip" title="Review">'
                . '<i class="fa fa-fw fa-eye"></i></button>';

            // $btn_approve = '<button type="button" class="btnApprove btn btn-sm btn-success btn-flat mr-1"'
            //     . ' data-toggle="tooltip" title="Approve">'
            //     . '<i class="fa fa-fw fa-check"></i></button>';

            // $btn_reject = '<button type="button" class="btnReject btn btn-sm btn-danger btn-flat mr-1"'
            //     . ' data-toggle="tooltip" title="Reject">'
            //     . '<i class="fa fa-fw fa-times"></i></button>';

            // $btn_attachments = '<button type="button" class="btnViewAttachments btn btn-sm btn-warning btn-flat"'
            //     . ' data-toggle="tooltip" title="View Attachments">'
            //     . '<i class="fa fa-fw fa-paperclip"></i></button>';

            // $status = strtolower($r->status ?? 'pending');
            // if ($status !== 'pending') {
            //     $btn_approve = '<button type="button" class="btn btn-sm btn-success btn-flat mr-1" disabled>'
            //         . '<i class="fa fa-fw fa-check"></i></button>';
            //     $btn_reject  = '<button type="button" class="btn btn-sm btn-danger btn-flat mr-1" disabled>'
            //         . '<i class="fa fa-fw fa-times"></i></button>';
            // }

            $data[] = [
                'preq_id'            => $r->preq_id,
                'pr_id'              => $r->pr_id,
                'pr_no'              => $r->pr_no              ?? '—',
                'sai_no'             => $r->sai_no             ?? '—',
                'proc_name'          => $r->proc_name          ?? '—',
                'office_desc'        => $r->office_desc        ?? '—',
                'attachment_summary' => $att_html,
                'encoded_by'         => $r->encoded_by         ?? '—',
                'date_created'       => $r->date_created       ?? null,
                'status'             => $r->status             ?? 'pending',
                'actions'            => $btn_review
            ];
        }

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $results[1],
            'recordsFiltered' => $results[1],
            'data'            => $data
        ]);
        exit();
    }

    // GET REVIEW SUMMARY
    // Returns counts per status for the summary cards
    // =========================================================================================================================================
    public function getReviewSummary()
    {
        $model   = new ProcurementStaffModel();
        $summary = $model->getReviewSummary();

        $counts = [
            'pending'  => 0,
            'approved' => 0,
            'rejected' => 0,
            'total'    => 0
        ];

        foreach ($summary as $row) {
            $s = strtolower($row->status);
            if (isset($counts[$s])) {
                $counts[$s] = intval($row->cnt);
            }
            $counts['total'] += intval($row->cnt);
        }

        echo json_encode($counts);
        exit();
    }

    // GET REVIEW DETAIL
    // Returns full PR info + items + attachments for the Review Modal
    // =========================================================================================================================================
    public function getReviewDetail()
    {
        $pr_id   = $this->input->post('pr_id');
        $preq_id = $this->input->post('preq_id');

        if (empty($pr_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing PR ID.']);
            return;
        }

        $model = new ProcurementStaffModel();

        $pr = $model->getPRDetailForReview($pr_id, $preq_id);

        if (!$pr) {
            echo json_encode(['success' => false, 'message' => 'Purchase Request not found.']);
            return;
        }

        $items = $model->getPRItems($pr_id);

        // Attachments (all required attachments for the proc mode, left-joined with uploaded files)
        $attachments = $model->getPRAttachments($pr_id);

        echo json_encode([
            'success'     => true,
            'pr'          => $pr,
            'items'       => $items,
            'attachments' => $attachments
        ]);
        exit();
    }

    // GET PR ATTACHMENTS BY PR
    // Lightweight attachment list used by the View Attachments quick modal
    // =========================================================================================================================================
    public function getPRAttachmentsByPr()
    {
        $pr_id = $this->input->post('pr_id');

        if (empty($pr_id)) {
            echo json_encode([]);
            return;
        }

        $model = new ProcurementStaffModel();
        $attachments = $model->getPRAttachments($pr_id);

        echo json_encode($attachments);
        exit();
    }

    // UPDATE REVIEW STATUS
    // Approves or rejects a PR review record
    // =========================================================================================================================================
    public function updateReviewStatus()
    {
        $pr_id              = $this->input->post('pr_id');
        $preq_id            = $this->input->post('preq_id');
        $status             = $this->input->post('status');
        $attachment_remarks = $this->input->post('attachment_remarks');
        $user_id            = $this->session->userdata('id');

        if (empty($pr_id) || empty($preq_id) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            return;
        }

        $allowed_statuses = ['approved', 'rejected'];
        if (!in_array(strtolower($status), $allowed_statuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
            return;
        }

        $model  = new ProcurementStaffModel();
        $review = $model->getReviewById($preq_id, $pr_id);

        if (!$review) {
            echo json_encode(['success' => false, 'message' => 'Review record not found.']);
            return;
        }

        if (strtolower($review->status) !== 'pending') {
            echo json_encode([
                'success' => false,
                'message' => 'This PR has already been ' . $review->status . ' and cannot be changed.'
            ]);
            return;
        }

        // Update main review status
        $update_data = [
            'status'        => strtolower($status),
            'modified_by'   => $user_id,
            'date_modified' => date('Y-m-d H:i:s')
        ];

        $model->updateReviewStatus($update_data, $pr_id);

        // Check for DB error instead of affected_rows
        $db_error = $this->db->error();
        if (!empty($db_error['code'])) {
            log_message('error', 'updateReviewStatus DB error: ' . print_r($db_error, true));
            echo json_encode(['success' => false, 'message' => 'Failed to update review status. Please try again.']);
            exit();
        }

        // Save per-attachment remarks into lib_attachment_per_pr
        if (!empty($attachment_remarks) && is_array($attachment_remarks)) {
            foreach ($attachment_remarks as $item) {
                $att_per_id = intval($item['attachment_per_id'] ?? 0);
                $att_remark = $this->security->xss_clean($item['remarks'] ?? '');

                if ($att_per_id > 0) {
                    $this->db->where('attachment_per_id', $att_per_id)
                        ->update('lib_attachment_per_pr', [
                            'remarks'       => $att_remark,
                            'date_modified' => date('Y-m-d H:i:s'),
                            'modified_by'   => $user_id
                        ]);
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Purchase Request has been successfully ' . ucfirst(strtolower($status)) . '.'
        ]);
        exit();
    }

    // OPEN REVIEW
    // Open a new page to review the attachments
    // =========================================================================================================================================
    public function review($pr_id = null, $preq_id = null)
    {
        if (empty($pr_id) || empty($preq_id)) {
            $this->session->set_flashdata('fail', 'Invalid PR review link.');
            redirect('ProcurementStaff');
        }

        $data = [
            'csrf'      => $this->csrf(),
            'csrf_ajax' => $this->csrf_ajax(),
            'fullname'  => $this->session->fullname,
            'pr_id'     => $pr_id,
            'preq_id'   => $preq_id
        ];

        $data['notif']  = 1;
        $data['config'] = 1;
        $data['ondue']  = 1;

        $this->load->view('header', $data);
        $this->load->view('procurement_staff/review');
        $this->load->view('footer');
    }
}
