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
    }
?>