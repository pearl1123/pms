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
        $this->db->select('a.attachment_id, a.attachment_name, pa.required, lap.file_name, lap.original_file_name');
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

    // ATTACHMENT LIST VIEW
    // Gets list of attachments for data table
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

        foreach ($results[0] as $r) {
            $btn_attachment = '<button type="button" id="btnAttachment" class="btn btn-sm btn-warning btn-flat" data-prid="' . $r->pr_id . '" data-toggle="tooltip" title="Attachment"><i class="fa fa-fw fa-file"></i></button>';
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
                'actions' => $btn_update . $btn_delete . $btn_attachment
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
                array('field' => 'proc_id',       'label' => 'Procurement Mode', 'rules' => 'required|integer'),
                array('field' => 'prQuantity',    'label' => 'Quantity',     'rules' => 'required|integer'),
                array('field' => 'prUnitCost',    'label' => 'Unit Cost',    'rules' => 'required|numeric'),
                array('field' => 'prTotalCost',   'label' => 'Total Cost',   'rules' => 'required|numeric'),
                // array('field' => 'prRemarks',     'label' => 'Remarks',      'rules' => 'required'),
                array('field' => 'prRequestedBy', 'label' => 'Requested By', 'rules' => 'required'),
                array('field' => 'prDesignation', 'label' => 'Designation',  'rules' => 'required'),
            );
        } else if ($selector == 'updatePR') {
            $config = array(
                array('field' => 'prId',          'label' => 'PR ID',        'rules' => 'required|integer'),
                array('field' => 'prStock',       'label' => 'Stock',        'rules' => 'required|integer'),
                array('field' => 'prNumber',      'label' => 'PR Number',    'rules' => 'required'),
                // array('field' => 'saiNumber',     'label' => 'SAI Number',   'rules' => 'required'),
                array('field' => 'prOffice',      'label' => 'Office',       'rules' => 'required|integer'),
                array('field' => 'proc_id',       'label' => 'Procurement Mode', 'rules' => 'required|integer'),
                array('field' => 'prQuantity',    'label' => 'Quantity',     'rules' => 'required|integer'),
                array('field' => 'prUnitCost',    'label' => 'Unit Cost',    'rules' => 'required|numeric'),
                array('field' => 'prTotalCost',   'label' => 'Total Cost',   'rules' => 'required|numeric'),
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

        $user_id = 1; // fallback if aauth not loaded
        if (isset($this->aauth)) {
            $user_id = $this->aauth->get_user_id();
        }

        // Get POST data
        $pr_no       = $this->input->post('prNumber');
        $sai_no      = $this->input->post('saiNumber');
        $proc_id     = $this->input->post('proc_id');
        $office_id   = $this->input->post('prOffice');
        $stock_id    = $this->input->post('prStock');
        $quantity    = $this->input->post('prQuantity');
        $unit_cost   = $this->input->post('prUnitCost');
        $remarks     = $this->input->post('prRemarks');
        $requested_by = $this->input->post('prRequestedBy');
        $designation = $this->input->post('prDesignation');

        $today = date('Y-m-d');

        // Prepare data array
        $data = [
            'pr_no'       => $pr_no,
            'date_1'      => $today,
            'sai_no'      => $sai_no,
            'date_2'      => $today,
            'proc_id'     => $proc_id,
            'office_id'   => $office_id,
            'stock_id'    => $stock_id,
            'quantity'    => $quantity,
            'unit_cost'   => $unit_cost,
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
        $l_model = new LibraryModel();
        $pr_model = new PurchaseRequestModel();

        $pr_id = $_POST['prId'];
        $stock_id = $_POST['prStock'];
        $stock = $l_model->getStockById($stock_id);

        $pr_data = array(
            'unit_id'       => isset($stock->unit_id) ? intval($stock->unit_id) : null,
            'office_id'     => $_POST['prOffice'] ?? '',
            'proc_id'     => $_POST['proc_id'] ?? '',
            // 'pr_no'         => $_POST['prNumber'] ?? '',
            // 'date_1'        => !empty($_POST['prNumber']) ? date("Y-m-d H:i:s") : null,
            // 'sai_no'        => !empty($_POST['saiNumber']) ? intval($_POST['saiNumber']) : NULL,
            // 'date_2'        => !empty($_POST['saiNumber']) ? date("Y-m-d H:i:s") : null,
            'stock_id'      => $_POST['prStock'] ?? '',
            'quantity'      => intval($_POST['prQuantity']) ?? intval(0),
            'unit_cost'     => floatval($_POST['prUnitCost']) ?? floatval(0),
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
        $pr_id = $this->input->post('pr_id');
        $attachment_id = $this->input->post('attachment_id');
        $user_id = $this->session->userdata('id');

        if (empty($_FILES['file']['name'])) {
            echo json_encode(['success' => false, 'message' => 'No file selected.']);
            return;
        }

        // Ensure folder exists
        $upload_path = './assets/uploads/pr_attachments/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png';
        $config['max_size'] = 5120;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {

            $upload_data = $this->upload->data();
            $original_name = $upload_data['client_name'];

            // Get PR to retrieve proc_id
            $pr = $this->db->select('proc_id')->where('pr_id', $pr_id)->get('tbl_purchase_request')->row();
            $proc_id = $pr->proc_id ?? null;

            // Check if record already exists
            $existing = $this->db->where('pr_id', $pr_id)
                ->where('attachment_id', $attachment_id)
                ->get('lib_attachment_per_pr')
                ->row();

            $data = [
                'pr_id' => $pr_id,
                'attachment_id' => $attachment_id,
                'file_name' => $upload_data['file_name'],
                'original_file_name' => $original_name,
                'proc_id' => $proc_id,
                'archived' => 0
            ];

            if ($existing) {
                // Update existing row
                $data['date_modified'] = date('Y-m-d H:i:s');
                $data['modified_by'] = $user_id;

                $this->db->where('attachment_per_id', $existing->attachment_per_id)
                    ->update('lib_attachment_per_pr', $data);
            } else {
                // Insert new row
                $data['date_created'] = date('Y-m-d H:i:s');
                $data['created_by'] = $user_id;

                $this->db->insert('lib_attachment_per_pr', $data);
            }

            echo json_encode(['success' => true, 'message' => 'File uploaded successfully.', 'file_name' => $upload_data['file_name']]);
        } else {
            // Upload failed
            $error = $this->upload->display_errors('', '');
            echo json_encode(['success' => false, 'message' => $error]);
        }
    }
}
