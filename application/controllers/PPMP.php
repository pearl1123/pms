<?php

    defined('BASEPATH') or exit('No direct script access allowed');

    class PPMP extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();

            #load all that you need
            $this->load->library("aauth");
            $this->load->model('PPMPModel');
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




        // PPMP LIST VIEW
        // Displays module for PPMP;
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
            $this->load->view('ppmp/listview');
            $this->load->view('footer');
        }

        // PPMP ITEM LIST VIEW
        // Displays module for PPMP;
        // =========================================================================================================================================
        public function ppmpList($year)
        {
            $data = array(
                'csrf' => $this->csrf(),
                'csrf_ajax' => $this->csrf_ajax(),
                'fullname' => $this->session->fullname,
                'year' => $year
            );

            #notification placeholder for now need to configure this later
            $data['notif'] = 1;
            $data['config'] = 1;
            $data['ondue'] = 1;

            $this->load->view('header', $data);
            $this->load->view('ppmp/ppmpitems');
            $this->load->view('footer');
        }

        // PPMP LIST VIEW
        // Gets list of PPMP for data table
        // =========================================================================================================================================
        public function getPPMPList(){
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));
            $l_model = new PPMPModel();

            $results = $l_model->getPPMPListView($start, $length);

            $data = array();

            foreach($results[0] as $r){
                
                $btn_update = '<a href="'.base_url("PPMP/ppmpList/") . $r->ppmp_year.'" id="btnUpdate" class="btn btn-sm btn-primary btn-flat"><i class="fa fa-fw fa-pencil"></i></a> ';
                $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i> </button>';

                $data[] = array(
                    'year' => $r->ppmp_year,
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

        // PPMP ITEM LIST VIEW
        // Gets list of PPMP items for data table
        // =========================================================================================================================================
        public function getPPMPItemList()
        {
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));
            $l_model = new PPMPModel();

            $results = $l_model->getPPMPItemListView($start, $length);

            $data = array();

            $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat" data-toggle="tooltip" title=""><i class="fa fa-fw fa-pencil"></i></button> ';
            $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i> </button>';

            foreach($results[0] as $r){
                $data[] = array(
                    'id' => $r->ppmp_id,
                    'year' => $r->ppmp_year,
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