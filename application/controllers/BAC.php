<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BAC extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        #load all that you need
        $this->load->library("aauth");
        $this->load->model('BACModel');
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

    public function getBACAttachments()
    {
        $bac_id = $this->input->post('bac_id');

        // Get the BAC's proc_id
        $bac = $this->db->select('proc_id')->where('bac_id', $bac_id)->get('tbl_bids_and_awards')->row();
        if (!$bac) {
            echo json_encode([]);
            return;
        }

        // Get the proc_code
        $proc = $this->db->select('proc_code')->where('proc_id', $bac->proc_id)->get('lib_procurement_mode')->row();
        if (!$proc) {
            echo json_encode([]);
            return;
        }

        // Get all active attachments for this procurement code and left join uploaded files
        $this->db->select('a.attachment_id, a.attachment_name, pa.required, lap.file_name, lap.original_file_name');
        $this->db->from('lib_procurement_attachment pa');
        $this->db->join('lib_attachments a', 'a.attachment_id = pa.attachment_id', 'inner');
        $this->db->join('lib_attachment_per_bac lap', "lap.attachment_id = a.attachment_id AND lap.bac_id = $bac_id", 'left');
        $this->db->where('pa.proc_code', $proc->proc_code);
        $this->db->where('a.archived', 0);
        $this->db->where('pa.archived', 0);

        $attachments = $this->db->get()->result();

        echo json_encode($attachments);
    }

    // BAC LIST VIEW
    // Displays module for BAC
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
        $this->load->view('BAC/listview');
        $this->load->view('BAC/modal_add');
        $this->load->view('BAC/modal_update');
        $this->load->view('BAC/BACattachment');
        $this->load->view('footer');
    }

    // BAC LIST
    // Gets list of BAC for data table
    // =========================================================================================================================================
    public function getBACList()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $l_model = new BACModel();

        $results = $l_model->getBACList($start, $length);

        $data = array();

        $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat" data-toggle="tooltip" title="Edit"><i class="fa fa-fw fa-pencil"></i></button> ';
        $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat mr-1" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-trash"></i> </button>';

        foreach ($results[0] as $r) {
            $btn_attachment = '<button type="button" id="btnAttachment" class="btn btn-sm btn-warning btn-flat mr-1" data-bacid="' . $r->bac_id . '" data-toggle="tooltip" title="Attachment"><i class="fa fa-fw fa-file"></i></button>';
            $btn_item = '<button type="button" id="btnAddItem" class="btn btn-sm btn-info btn-flat" data-bacid="' . $r->bac_id . '" data-toggle="tooltip" title="Item"><i class="fa fa-fw fa-shopping-cart"></i></button>';
            $data[] = array(
                'id'           => $r->bac_id,
                'bac_no'       => $r->bac_no,
                'bac_date'     => $r->date_1,
                'sai_no'       => $r->sai_no,
                'sai_date'     => $r->date_2,
                'unit_cost'    => $r->unit_cost,
                'quantity'     => $r->quantity,
                'remarks'      => $r->remarks,
                'requested_by' => $r->requested_by,
                'designation'  => $r->designation,
                'run_date'     => $r->run_date,
                'approved_by'  => $r->approved_by,
                'office_desc'  => $r->office_desc,
                'proc_name'    => $r->proc_name,
                'unit_code'    => $r->unit_code,
                'date_created' => $r->date_created,
                'encoded_by'   => $r->fullname,
                'actions'      => $btn_update . $btn_delete . $btn_attachment . $btn_item
            );
        }

        $output = array(
            "draw"            => $draw,
            "recordsTotal"    => $results[1],
            "recordsFiltered" => $results[1],
            "data"            => $data
        );

        echo json_encode($output);
        exit();
    }

    // GET BIDS AND AWARDS
    // Gets BAC record by id
    // =========================================================================================================================================
  
    // SAVE BIDS AND AWARDS
    // Saves a new BAC record
    // =========================================================================================================================================
    public function saveBAC()
    {
        $this->load->model('BACModel');

        $user_id = 1; // fallback if aauth not loaded
        if (isset($this->aauth)) {
            $user_id = $this->aauth->get_user_id();
        }

        // Get POST data
        $bac_no       = $this->input->post('bacNumber');
        $sai_no       = $this->input->post('saiNumber');
        $proc_id      = $this->input->post('proc_id');
        $office_id    = $this->input->post('bacOffice');
        $stock_id     = $this->input->post('bacStock');
        $quantity     = $this->input->post('bacQuantity');
        $unit_cost    = $this->input->post('bacUnitCost');
        $remarks      = $this->input->post('bacRemarks');
        $requested_by = $this->input->post('bacRequestedBy');
        $designation  = $this->input->post('bacDesignation');

        $today = date('Y-m-d');

        // Prepare data array
        $data = [
            'bac_no'       => $bac_no,
            'date_1'       => $today,
            'sai_no'       => $sai_no,
            'date_2'       => $today,
            'proc_id'      => $proc_id,
            'office_id'    => $office_id,
            'stock_id'     => $stock_id,
            'quantity'     => $quantity,
            'unit_cost'    => $unit_cost,
            'remarks'      => $remarks,
            'requested_by' => $requested_by,
            'designation'  => $designation,
            'created_by'   => $user_id,
            'date_created' => date('Y-m-d H:i:s'),
            'archived'     => 0
        ];

        // Save via model
        $insert_id = $this->BACModel->saveBAC($data);

        if ($insert_id) {
            $this->session->set_flashdata('success', 'Bids and Awards record successfully saved!');
            redirect('BAC');
        } else {
            $db_error = $this->db->error();
            log_message('error', 'Bids and Awards save failed: ' . print_r($db_error, true));
            $this->session->set_flashdata('fail', 'Failed to create new Bids and Awards record. Check logs.');
            redirect('BAC');
        }
    }

    // UPDATE BIDS AND AWARDS
    // Updates a BAC record
    // =========================================================================================================================================
  

    // DELETE BIDS AND AWARDS
    // Deletes a BAC record
    // =========================================================================================================================================
    public function deleteBAC()
    {
        $bac_model = new BACModel();

        $bac_id = $_POST['bac_id'];

        $bac_data = array(
            'modified_by'   => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived'      => 1
        );

        $this->security->xss_clean($bac_data);
        $res = $bac_model->updateBAC($bac_data, $bac_id);
        if ($res > 0) {
            $this->session->set_flashdata('success', 'Successfully deleted Bids and Awards record.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to delete Bids and Awards record.');
        }

        redirect('BAC/index');
    }

    // ATTACHMENT PER BAC
    // Save attachment per BAC record
    // =========================================================================================================================================
    public function uploadAttachment()
    {
        $bac_id       = $this->input->post('bac_id');
        $attachment_id = $this->input->post('attachment_id');
        $user_id      = $this->session->userdata('id');

        if (empty($_FILES['file']['name'])) {
            echo json_encode(['success' => false, 'message' => 'No file selected.']);
            return;
        }

        // Ensure folder exists
        $upload_path = './assets/uploads/bac_attachments/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png';
        $config['max_size']      = 5120;
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {

            $upload_data   = $this->upload->data();
            $original_name = $upload_data['client_name'];

            // Get BAC to retrieve proc_id
            $bac     = $this->db->select('proc_id')->where('bac_id', $bac_id)->get('tbl_bids_and_awards')->row();
            $proc_id = $bac->proc_id ?? null;

            // Check if record already exists
            $existing = $this->db->where('bac_id', $bac_id)
                ->where('attachment_id', $attachment_id)
                ->get('lib_attachment_per_bac')
                ->row();

            $data = [
                'bac_id'             => $bac_id,
                'attachment_id'      => $attachment_id,
                'file_name'          => $upload_data['file_name'],
                'original_file_name' => $original_name,
                'proc_id'            => $proc_id,
                'archived'           => 0
            ];

            if ($existing) {
                // Update existing row
                $data['date_modified'] = date('Y-m-d H:i:s');
                $data['modified_by']   = $user_id;

                $this->db->where('attachment_per_id', $existing->attachment_per_id)
                    ->update('lib_attachment_per_bac', $data);
            } else {
                // Insert new row
                $data['date_created'] = date('Y-m-d H:i:s');
                $data['created_by']   = $user_id;

                $this->db->insert('lib_attachment_per_bac', $data);
            }

            echo json_encode(['success' => true, 'message' => 'File uploaded successfully.', 'file_name' => $upload_data['file_name']]);
        } else {
            // Upload failed
            $error = $this->upload->display_errors('', '');
            echo json_encode(['success' => false, 'message' => $error]);
        }
    }
}