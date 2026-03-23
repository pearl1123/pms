<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PurchaseRequest extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        #load all that you need
        $this->load->library("aauth");
        $this->load->model('PurchaseRequestModel');
        if (!$this->session->logged_in) {
            $this->session->set_flashdata('fail', 'Session expired. Please Sign in');
            redirect('User/index');
        }
    }

    public function csrf()
    {
        $name = $this->security->get_csrf_token_name();
        $hash = $this->security->get_csrf_hash();
        $html = '<input type="hidden" name="' . $name . '" value="' . $hash . '">';
        return $html;
    }

    public function csrf_ajax()
    {
        return array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
    }

    public function getProcurementModeList()
    {
        $this->db->where('archived', 0);
        $query = $this->db->get('lib_procurement_mode');
        echo json_encode($query->result());
    }

    public function getProcurementModeById()
    {
        $proc_id = $this->input->post('proc_id');
        $this->db->where('proc_id', $proc_id);
        $row = $this->db->get('lib_procurement_mode')->row();
        echo json_encode($row);
    }

    public function getPRAttachments()
    {
        $pr_id = $this->input->post('pr_id');

        // Get the PR's proc_id
        $pr = $this->db->select('proc_id')->where('pr_id', $pr_id)->get('tbl_purchase_request')->row();
        if (!$pr) {
            echo json_encode([]);
            return;
        }

        // Get the proc_code
        $proc = $this->db->select('proc_code')->where('proc_id', $pr->proc_id)->get('lib_procurement_mode')->row();
        if (!$proc) {
            echo json_encode([]);
            return;
        }

        // Get all active attachments for this procurement code and left join uploaded files
        $this->db->select('a.attachment_id, a.attachment_name, pa.required, lap.file_name, lap.original_file_name, lap.remarks');
        $this->db->from('lib_procurement_attachment pa');
        $this->db->join('lib_attachments a', 'a.attachment_id = pa.attachment_id', 'inner');
        $this->db->join('lib_attachment_per_pr lap', "lap.attachment_id = a.attachment_id AND lap.pr_id = $pr_id", 'left');
        $this->db->where('pa.proc_code', $proc->proc_code);
        $this->db->where('a.archived', 0);
        $this->db->where('pa.archived', 0);

        $attachments = $this->db->get()->result();

        echo json_encode($attachments);
    }

    // PR LIST VIEW
    // Displays module for PR;
    // =========================================================================================================================================
    public function index()
    {
        $data = array(
            'csrf' => $this->csrf(),
            'csrf_ajax' => $this->csrf_ajax(),
            'fullname' => $this->session->fullname
        );

        #notification placeholder for now need to configure this later
        $data['notif'] = 1;
        $data['config'] = 1;
        $data['ondue'] = 1;

        $data['attachments'] = $this->db
            ->where('archived', 0)
            ->get('lib_attachments')
            ->result();

        $this->load->view('header', $data);
        $this->load->view('purchaseRequest/listview');
        $this->load->view('purchaseRequest/modal_add');
        $this->load->view('purchaseRequest/modal_update');
        $this->load->view('purchaseRequest/attachment');
        $this->load->view('footer');
    }

    // PURCHASE REQUEST LIST VIEW
    // Gets list of purchase request for data table
    // =========================================================================================================================================
    public function getPRList()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $l_model = new PurchaseRequestModel();

        $results = $l_model->getPRList($start, $length);

        $data = array();

        $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat" data-toggle="tooltip" title="Edit"><i class="fa fa-fw fa-pencil"></i></button> ';
        $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat mr-1" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-trash"></i> </button>';
        $btn_send = '<button type="button" id="btnSend" class="btn btn-sm btn-success btn-flat mr-1" data-toggle="tooltip" title="Submit"><i class="fa fa-fw fa-send"></i> </button>';

        foreach ($results[0] as $r) {
            $btn_attachment = '<button type="button" class="btnAttachment btn btn-sm btn-warning btn-flat mr-1" data-prid="' . $r->pr_id . '" data-toggle="tooltip" title="Attachment"><i class="fa fa-fw fa-file"></i></button>';
            $btn_item       = '<button type="button" class="btnAddItem btn btn-sm btn-info btn-flat mr-1" data-prid="' . $r->pr_id . '" data-toggle="tooltip" title="Item"><i class="fa fa-fw fa-shopping-cart"></i></button>';
            $data[] = array(
                'id' => $r->pr_id,
                'pr_no' => $r->pr_no,
                'pr_date' => $r->date_1,
                'sai_no' => $r->sai_no,
                'sai_date' => $r->date_2,
                'unit_cost' => $r->unit_cost,
                'quantity' => $r->quantity,
                'remarks' => $r->remarks,
                'requested_by' => $r->requested_by,
                'designation' => $r->designation,
                'run_date' => $r->run_date,
                'approved_by' => $r->approved_by,
                'office_desc' => $r->office_desc,
                'proc_name' => $r->proc_name,
                'unit_code' => $r->unit_code,
                'date_created' => $r->date_created,
                'encoded_by' => $r->fullname,
                'review_status'  => $r->review_status ?? 'not_sent',
                'actions' => $btn_update . $btn_attachment . $btn_item . $btn_send . $btn_delete
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $results[1],
            "recordsFiltered" => $results[1],
            "data" => $data
        );

        echo json_encode($output);
        exit();
    }

    // GET PURCHASE REQUEST
    // Gets purchase request by id
    // =========================================================================================================================================
    public function getPR()
    {
        $pr_model = new PurchaseRequestModel();
        $pr_id = $this->input->post('pr_id');

        $res = $pr_model->getPurchaseRequestById($pr_id);

        echo json_encode($res);
        exit();
    }

    //VALIDATE PR INPUT
    //Validated input for unwanted entries; Set security checks
    // =========================================================================================================================================
    protected function validatePR($selector)
    {
        if ($selector == 'createPR') {
            $config = array(
                array('field' => 'prStock',       'label' => 'Stock',        'rules' => 'required|integer'),
                array('field' => 'prNumber',      'label' => 'PR Number',    'rules' => 'required'),
                // array('field' => 'saiNumber',     'label' => 'SAI Number',   'rules' => 'required'),
                array('field' => 'prOffice',      'label' => 'Office',       'rules' => 'required|integer'),
                // array('field' => 'proc_id',       'label' => 'Procurement Mode', 'rules' => 'required|integer'),
                // array('field' => 'prQuantity',    'label' => 'Quantity',     'rules' => 'required|integer'),
                // array('field' => 'prUnitCost',    'label' => 'Unit Cost',    'rules' => 'required|numeric'),
                // array('field' => 'prTotalCost',   'label' => 'Total Cost',   'rules' => 'required|numeric'),
                // array('field' => 'prRemarks',     'label' => 'Remarks',      'rules' => 'required'),
                array('field' => 'prRequestedBy', 'label' => 'Requested By', 'rules' => 'required'),
                array('field' => 'prDesignation', 'label' => 'Designation',  'rules' => 'required'),
            );
        } else if ($selector == 'updatePR') {
            $config = array(
                array('field' => 'prId',          'label' => 'PR ID',        'rules' => 'required|integer'),
                // array('field' => 'prStock',       'label' => 'Stock',        'rules' => 'required|integer'),
                array('field' => 'prNumber',      'label' => 'PR Number',    'rules' => 'required'),
                // array('field' => 'saiNumber',     'label' => 'SAI Number',   'rules' => 'required'),
                array('field' => 'prOffice',      'label' => 'Office',       'rules' => 'required|integer'),
                // array('field' => 'proc_id',       'label' => 'Procurement Mode', 'rules' => 'required|integer'),
                // array('field' => 'prQuantity',    'label' => 'Quantity',     'rules' => 'required|integer'),
                // array('field' => 'prUnitCost',    'label' => 'Unit Cost',    'rules' => 'required|numeric'),
                // array('field' => 'prTotalCost',   'label' => 'Total Cost',   'rules' => 'required|numeric'),
                // array('field' => 'prRemarks',     'label' => 'Remarks',      'rules' => 'required'),
                array('field' => 'prRequestedBy', 'label' => 'Requested By', 'rules' => 'required'),
                array('field' => 'prDesignation', 'label' => 'Designation',  'rules' => 'required'),
            );
        }

        return $this->form_validation->set_rules($config);
    }

    // SAVE PURCHASE REQUEST
    // Saves a new Purchase Request
    // =========================================================================================================================================
    public function savePR()
    {
        $this->load->model('PurchaseRequestModel');

        $user_id = 1;
        if (isset($this->aauth)) {
            $user_id = $this->aauth->get_user_id();
        }

        // Get POST data
        $pr_no       = $this->input->post('prNumber');
        $sai_no      = $this->input->post('saiNumber');
        $proc_id     = $this->input->post('proc_id');
        $office_id   = $this->input->post('prOffice');
        // $stock_id    = $this->input->post('prStock');
        // $quantity    = $this->input->post('prQuantity');
        // $unit_cost   = $this->input->post('prUnitCost');
        $remarks     = $this->input->post('prRemarks');
        $requested_by = $this->input->post('prRequestedBy');
        $designation = $this->input->post('prDesignation');

        $today = date('Y-m-d');

        $exists = $this->db
            ->where('pr_no', $pr_no)
            ->or_where('sai_no', $sai_no)
            ->where('archived', 0)
            ->get('tbl_purchase_request')
            ->row();

        if ($exists) {
            $this->session->set_flashdata('fail', 'Duplicate PR Number or SAI Number. Cannot save.');
            redirect('PurchaseRequest');
            return;
        }

        // Prepare data array
        $data = [
            'pr_no'       => $pr_no,
            'date_1'      => $today,
            'sai_no'      => $sai_no,
            'date_2'      => $today,
            'proc_id'     => $proc_id,
            'office_id'   => $office_id,
            // 'stock_id'    => $stock_id,
            // 'quantity'    => $quantity,
            // 'unit_cost'   => $unit_cost,
            'remarks'     => $remarks,
            'requested_by' => $requested_by,
            'designation' => $designation,
            'created_by'  => $user_id,
            'date_created' => date('Y-m-d H:i:s'),
            'archived'    => 0
        ];

        // Save via model
        $insert_id = $this->PurchaseRequestModel->savePurchaseRequest($data);

        if ($insert_id) {
            $this->session->set_flashdata('success', 'Purchase Request successfully saved!');
            redirect('PurchaseRequest');
        } else {
            $db_error = $this->db->error();
            log_message('error', 'Purchase Request save failed: ' . print_r($db_error, true));
            $this->session->set_flashdata('fail', 'Failed to create new Purchase Request. Check logs.');
            redirect('PurchaseRequest');
        }
    }

    // UPDATE PURCHASE REQUEST
    // Updates a Purchase Request
    // =========================================================================================================================================
    public function updatePR()
    {
        $pr_model = new PurchaseRequestModel();

        $pr_id = $_POST['prId'];

        $pr_data = array(
            'unit_id'       => isset($stock->unit_id) ? intval($stock->unit_id) : null,
            'office_id'     => $_POST['prOffice'] ?? '',
            'proc_id'     => $_POST['proc_id'] ?? '',
            // 'pr_no'         => $_POST['prNumber'] ?? '',
            // 'date_1'        => !empty($_POST['prNumber']) ? date("Y-m-d H:i:s") : null,
            // 'sai_no'        => !empty($_POST['saiNumber']) ? intval($_POST['saiNumber']) : NULL,
            // 'date_2'        => !empty($_POST['saiNumber']) ? date("Y-m-d H:i:s") : null,
            // 'stock_id'      => $_POST['prStock'] ?? '',
            // 'quantity'      => intval($_POST['prQuantity']) ?? intval(0),
            // 'unit_cost'     => floatval($_POST['prUnitCost']) ?? floatval(0),
            // 'total_cost'    => intval($_POST['prQuantity']) * floatval($_POST['prUnitCost']),
            'remarks'       => $_POST['prRemarks'] ?? '',
            'requested_by'  => $_POST['prRequestedBy'] ?? '',
            'designation'   => $_POST['prDesignation'] ?? '',
            // 'run_date'      => null,
            // 'approved_by'   => null,
            'modified_by'    => $this->session->userID,
            'date_modified'  => date("Y-m-d H:i:s")
        );

        $this->validatePR('updatePR');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($pr_data);
            $res = $pr_model->updatePurchaseRequest($pr_data, $pr_id);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully updated Purchase Request.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to update Purchase Request.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }

        redirect('PurchaseRequest/index');
    }

    // DELETE PURCHASE REQUEST
    // Deletes a Purchase Request
    // =========================================================================================================================================
    public function deletePR()
    {
        $pr_model = new PurchaseRequestModel();

        $pr_id = $_POST['pr_id'];

        $pr_data = array(
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived' => 1
        );

        $this->security->xss_clean($pr_data);
        $res = $pr_model->updatePurchaseRequest($pr_data, $pr_id);
        if ($res > 0) {
            $this->session->set_flashdata('success', 'Successfully deleted Purchase Request.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to delete Purchase Request.');
        }

        redirect('PurchaseRequest/index');
    }

    // ATTACHMENT PER PR
    // Save attachment per PR
    // =========================================================================================================================================
    public function uploadAttachment()
    {
        $pr_id         = $this->input->post('pr_id');
        $attachment_id = $this->input->post('attachment_id');
        $user_id       = $this->session->userdata('id');

        if (empty($_FILES['file']['name'])) {
            echo json_encode(['success' => false, 'message' => 'No file selected.']);
            return;
        }

        // Ensure folder exists
        $upload_path = './assets/uploads/pr_attachments/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path']    = $upload_path;
        $config['allowed_types']  = 'pdf|doc|docx|jpg|jpeg|png';
        $config['max_size']       = 5120;
        $config['encrypt_name']   = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $error = $this->upload->display_errors('', '');
            echo json_encode(['success' => false, 'message' => $error]);
            return;
        }

        $upload_data   = $this->upload->data();
        $original_name = $upload_data['client_name'];

        // Get PR to retrieve proc_id
        $pr      = $this->db->select('proc_id')
            ->where('pr_id', $pr_id)
            ->get('tbl_purchase_request')
            ->row();
        $proc_id = $pr->proc_id ?? null;

        // Check if an ACTIVE (non-archived) record already exists for this pr + attachment
        $existing = $this->db
            ->where('pr_id',         $pr_id)
            ->where('attachment_id', $attachment_id)
            ->where('archived',      0)
            ->get('lib_attachment_per_pr')
            ->row();

        $data = [
            'pr_id'              => $pr_id,
            'attachment_id'      => $attachment_id,
            'proc_id'            => $proc_id,
            'file_name'          => $upload_data['file_name'],
            'original_file_name' => $original_name,
            'archived'           => 0
        ];

        if ($existing) {
            // Replace the existing file
            $data['date_modified'] = date('Y-m-d H:i:s');
            $data['modified_by']   = $user_id;

            $this->db->where('attachment_per_id', $existing->attachment_per_id)
                ->update('lib_attachment_per_pr', $data);
        } else {
            // Insert fresh row
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['created_by']   = $user_id;

            $this->db->insert('lib_attachment_per_pr', $data);
        }

        echo json_encode([
            'success'   => true,
            'message'   => 'File uploaded successfully.',
            'file_name' => $upload_data['file_name']
        ]);
    }
    // SAVE PURCHASE REQUEST ITEM
    // Saves the purchase request item
    // =========================================================================================================================================
    public function savePRitem()
    {
        $pr_id       = $this->input->post('pr_id');
        $pr_item_ids = $this->input->post('prItemId');
        $stock_ids   = $this->input->post('prStock');
        $quantities  = $this->input->post('prQuantity');
        $unit_costs  = $this->input->post('prUnitCost');
        $totals      = $this->input->post('prTotalCost');

        $user_id = $this->session->userdata('id');

        if (empty($pr_id) || empty($stock_ids) || empty($quantities) || empty($unit_costs)) {
            log_message('error', 'PR ITEM VALIDATION FAILED: ' . json_encode($_POST));
            $this->session->set_flashdata('fail', 'Missing required fields.');
            redirect('PurchaseRequest/index');
            return;
        }

        $submitted_ids = [];

        foreach ($stock_ids as $i => $stock_id) {
            $pr_item_id = isset($pr_item_ids[$i]) ? intval($pr_item_ids[$i]) : 0;
            $quantity   = $quantities[$i] ?? 0;
            $unit_cost  = $unit_costs[$i] ?? 0;
            $total_cost = $totals[$i] ?? ($quantity * $unit_cost);

            if (!$stock_id || $quantity <= 0 || $unit_cost <= 0) continue;

            if ($pr_item_id > 0) {
                // UPDATE existing row
                $data = [
                    'stock_id'      => $stock_id,
                    'quantity'      => $quantity,
                    'unit_cost'     => $unit_cost,
                    'total_cost'    => $total_cost,
                    'date_modified' => date('Y-m-d H:i:s'),
                    'modified_by'   => $user_id
                ];
                $this->db->where('pr_item_id', $pr_item_id)
                    ->where('pr_id', $pr_id)
                    ->update('lib_purchase_request_items', $data);

                $submitted_ids[] = $pr_item_id;
            } else {
                // INSERT new row
                $data = [
                    'pr_id'        => $pr_id,
                    'stock_id'     => $stock_id,
                    'quantity'     => $quantity,
                    'unit_cost'    => $unit_cost,
                    'total_cost'   => $total_cost,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by'   => $user_id,
                    'archived'     => 0
                ];
                $this->db->insert('lib_purchase_request_items', $data);
                $submitted_ids[] = (int) $this->db->insert_id();
            }
        }

        // Archive rows belonging to this PR that were NOT in the submitted form
        $existing_ids = $this->db
            ->select('pr_item_id')
            ->where('pr_id', $pr_id)
            ->where('archived', 0)
            ->get('lib_purchase_request_items')
            ->result_array();

        $existing_ids = array_column($existing_ids, 'pr_item_id');

        // Find IDs to archive = existing in DB but not submitted
        $to_archive = array_diff($existing_ids, $submitted_ids);

        if (!empty($to_archive)) {
            $this->db->where_in('pr_item_id', $to_archive)
                ->where('pr_id', $pr_id) // safety
                ->update('lib_purchase_request_items', [
                    'archived'      => 1,
                    'date_modified' => date('Y-m-d H:i:s'),
                    'modified_by'   => $user_id
                ]);
            log_message('debug', 'PR ITEMS ARCHIVED: ' . json_encode($to_archive));
        }

        $this->session->set_flashdata('success', 'Purchase Request items successfully saved.');
        redirect('PurchaseRequest/index');
    }

    // GET ITEM IF EXIST
    // Get the item if existing
    // ========================================================================================================================================= 
    public function getPRItem()
    {
        $pr_id = $this->input->post('pr_id');
        $pr_model = new PurchaseRequestModel();
        $items = $pr_model->getPRItem($pr_id);

        echo json_encode($items); // this will now return an array
    }

    // SAVE REMARKS
    // Save the remarks inside the attachment per PR
    // =========================================================================================================================================
    public function saveRemarks()
    {
        $pr_id      = $this->input->post('pr_id');
        $remarks    = $this->input->post('remarks'); // array of [attachment_id, remarks]
        $user_id    = $this->session->userdata('id');

        if (empty($pr_id) || empty($remarks)) {
            echo json_encode(['success' => false, 'message' => 'Missing data.']);
            return;
        }

        foreach ($remarks as $item) {
            $attachment_id = $item['attachment_id'];
            $remark_text   = $item['remarks'];

            $existing = $this->db
                ->where('pr_id', $pr_id)
                ->where('attachment_id', $attachment_id)
                ->get('lib_attachment_per_pr')
                ->row();

            if ($existing) {
                $this->db->where('attachment_per_id', $existing->attachment_per_id)
                    ->update('lib_attachment_per_pr', [
                        'remarks'       => $remark_text,
                        'date_modified' => date('Y-m-d H:i:s'),
                        'modified_by'   => $user_id
                    ]);
            }
            // If no existing record, remarks will be saved on first file upload
        }

        echo json_encode(['success' => true, 'message' => 'Remarks saved.']);
    }

    // SEND PURCHASE REQUEST
    // Submit the PR for review
    // =========================================================================================================================================
    public function sendPR()
    {
        $pr_id   = $this->input->post('pr_id');
        $user_id = $this->session->userdata('id');

        if (empty($pr_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing PR ID.']);
            return;
        }

        $pr = $this->db
            ->where('pr_id',   $pr_id)
            ->where('archived', 0)
            ->get('tbl_purchase_request')
            ->row();

        if (!$pr) {
            echo json_encode(['success' => false, 'message' => 'Purchase Request not found.']);
            return;
        }

        if (empty($pr->proc_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'This PR has no Procurement Mode set. Please update it before sending.'
            ]);
            return;
        }

        $item_count = $this->db
            ->where('pr_id',   $pr_id)
            ->where('archived', 0)
            ->count_all_results('lib_purchase_request_items');

        if ($item_count === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'This PR has no items. Please add at least one item before sending.'
            ]);
            return;
        }

        $already_sent = $this->db
            ->where('pr_id',   $pr_id)
            ->where('archived', 0)
            ->get('tbl_purchase_request_review')
            ->row();

        if ($already_sent) {
            if (strtolower($already_sent->status) === 'rejected') {
                
                $this->db
                    ->where('pr_id', $pr_id)
                    ->where('archived', 0)
                    ->update('tbl_purchase_request_review', [
                        'archived'      => 1,
                        'modified_by'   => $user_id,
                        'date_modified' => date('Y-m-d H:i:s')
                    ]);
            } else {
                
                echo json_encode([
                    'success' => false,
                    'message' => 'This Purchase Request has already been sent and is currently ' . ucfirst($already_sent->status) . '.'
                ]);
                return;
            }
        }

        $proc = $this->db
            ->select('proc_id, proc_code, proc_name')
            ->where('proc_id',  $pr->proc_id)
            ->where('archived', 0)
            ->get('lib_procurement_mode')
            ->row();

        if (!$proc) {
            echo json_encode([
                'success' => false,
                'message' => 'Procurement Mode not found. Please check the PR settings.'
            ]);
            return;
        }

        $now = date('Y-m-d H:i:s');

        $attachments = $this->db
            ->where('pr_id',   $pr_id)
            ->where('archived', 0)
            ->get('lib_attachment_per_pr')
            ->result();

        $proc_attachments = $this->db
            ->where('proc_code', $proc->proc_code)
            ->where('archived',  0)
            ->get('lib_procurement_attachment')
            ->result();

        $proc_attch_map = [];
        foreach ($proc_attachments as $pa) {
            $proc_attch_map[$pa->attachment_id] = $pa->proc_attch_id;
        }

        $items = $this->db
            ->select('pr_item_id')
            ->where('pr_id',   $pr_id)
            ->where('archived', 0)
            ->get('lib_purchase_request_items')
            ->result();

        $rows = [];

        // One row per uploaded attachment
        foreach ($attachments as $att) {
            $rows[] = [
                'attachment_per_id' => $att->attachment_per_id,
                'pr_id'             => $pr_id,
                'attachmend_id'     => $att->attachment_id,
                'pr_item_id'        => null,
                'proc_id'           => $pr->proc_id,
                'proc_attch_id'     => $proc_attch_map[$att->attachment_id] ?? null,
                'status'            => 'pending',
                'date_created'      => $now,
                'created_by'        => $user_id,
                'archived'          => 0
            ];
        }

        foreach ($items as $item) {
            $rows[] = [
                'attachment_per_id' => null,
                'pr_id'             => $pr_id,
                'attachmend_id'     => null,
                'pr_item_id'        => $item->pr_item_id,
                'proc_id'           => $pr->proc_id,
                'proc_attch_id'     => null,
                'status'            => 'pending',
                'date_created'      => $now,
                'created_by'        => $user_id,
                'archived'          => 0
            ];
        }

        // insert one base row so the PR still appears in the review queue
        if (empty($rows)) {
            $rows[] = [
                'attachment_per_id' => null,
                'pr_id'             => $pr_id,
                'attachmend_id'     => null,
                'pr_item_id'        => null,
                'proc_id'           => $pr->proc_id,
                'proc_attch_id'     => null,
                'status'            => 'pending',
                'date_created'      => $now,
                'created_by'        => $user_id,
                'archived'          => 0
            ];
        }

        $this->db->insert_batch('tbl_purchase_request_review', $rows);
        $affected = $this->db->affected_rows();

        if ($affected > 0) {
            log_message('info', 'PR #' . $pr_id . ' sent for review by user #' . $user_id . ' — ' . $affected . ' review rows inserted.');
            echo json_encode([
                'success' => true,
                'message' => 'Purchase Request <b>PR No. ' . htmlspecialchars($pr->pr_no) . '</b> has been successfully sent for review.'
            ]);
        } else {
            log_message('error', 'sendPR failed for pr_id=' . $pr_id . ' — insert_batch returned 0 rows.');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send Purchase Request. Please try again.'
            ]);
        }

        exit();
    }
}
