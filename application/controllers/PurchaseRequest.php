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

            $this->load->view('header', $data);
            $this->load->view('purchaseRequest/listview');
            $this->load->view('purchaseRequest/modal_add');
            $this->load->view('purchaseRequest/modal_update');
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

            $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat" data-toggle="tooltip" title=""><i class="fa fa-fw fa-pencil"></i></button> ';
            $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i> </button>';

            foreach($results[0] as $r){
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
                    'unit_code' => $r->unit_code,
                    'date_created' => $r->date_created,
                    'encoded_by' => $r->fullname,
                    'actions' => $btn_update . $btn_delete
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
                    array('field' => 'prQuantity',    'label' => 'Quantity',     'rules' => 'required|integer'),
                    array('field' => 'prUnitCost',    'label' => 'Unit Cost',    'rules' => 'required|numeric'),
                    array('field' => 'prTotalCost',   'label' => 'Total Cost',   'rules' => 'required|numeric'),
                    // array('field' => 'prRemarks',     'label' => 'Remarks',      'rules' => 'required'),
                    array('field' => 'prRequestedBy', 'label' => 'Requested By', 'rules' => 'required'),
                    array('field' => 'prDesignation', 'label' => 'Designation',  'rules' => 'required'),
                );
            }
            else if ($selector == 'updatePR') {
                $config = array(
                    array('field' => 'prId',          'label' => 'PR ID',        'rules' => 'required|integer'),
                    array('field' => 'prStock',       'label' => 'Stock',        'rules' => 'required|integer'),
                    array('field' => 'prNumber',      'label' => 'PR Number',    'rules' => 'required'),
                    // array('field' => 'saiNumber',     'label' => 'SAI Number',   'rules' => 'required'),
                    array('field' => 'prOffice',      'label' => 'Office',       'rules' => 'required|integer'),
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
            $l_model = new LibraryModel();
            $pr_model = new PurchaseRequestModel();

            $stock_id = $_POST['prStock'];
            $stock = $l_model->getStockById($stock_id);

            $pr_data = array(
                'unit_id'       => isset($stock->unit_id) ? intval($stock->unit_id) : null,
                'office_id'     => $_POST['prOffice'] ?? '',
                'pr_no'         => $_POST['prNumber'] ?? '',
                'date_1'        => !empty($_POST['prNumber'])  ? date("Y-m-d H:i:s") : null,
                'sai_no'        => !empty($_POST['saiNumber']) ? intval($_POST['saiNumber']) : null,
                'date_2'        => !empty($_POST['saiNumber']) ? date("Y-m-d H:i:s") : null,
                'stock_id'      => $_POST['prStock'] ?? '',
                'quantity'      => intval($_POST['prQuantity']) ?? intval(0),
                'unit_cost'     => floatval($_POST['prUnitCost']) ?? intval(0),
                // 'total_cost'    => intval($_POST['prQuantity']) * floatval($_POST['prUnitCost']),
                'remarks'       => $_POST['prRemarks'] ?? '',
                'requested_by'  => $_POST['prRequestedBy'] ?? '',
                'designation'   => $_POST['prDesignation'] ?? '',
                // 'run_date'      => null,
                // 'approved_by'   => null,
                'created_by'    => $this->session->userID,
                'date_created'  => date("Y-m-d H:i:s"),
                'archived'      => 0
            );

            $this->validatePR('createPR');

            if ($this->form_validation->run()) {
                $this->security->xss_clean($pr_data);
                $res = $pr_model->savePurchaseRequest($pr_data);
                if ($res > 0) {
                    $this->session->set_flashdata('success', 'Successfully created new Purchase Request.');
                } else {
                    $this->session->set_flashdata('fail', 'Failed to create new Purchase Request.');
                }
            } else {
                $this->session->set_flashdata('fail', validation_errors());
            }

            redirect('PurchaseRequest/index');
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
    }
?>