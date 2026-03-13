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
            $l_model = new LibraryModel();

            $data = array(
                'csrf' => $this->csrf(),
                'csrf_ajax' => $this->csrf_ajax(),
                'fullname' => $this->session->fullname,
                'year' => $year,
                'unit' => $l_model->getActiveUnits(),
                'mode' => $l_model->getActiveMode(),
                'source' => $l_model->getActiveSource(),
                'attachment' => $l_model->getActiveAttachment()
            );

            #notification placeholder for now need to configure this later
            $data['notif'] = 1;
            $data['config'] = 1;
            $data['ondue'] = 1;

            $this->load->view('header', $data);
            $this->load->view('ppmp/ppmpitems');
            $this->load->view('ppmp/modal_add');
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

                $project_type = 0;

                switch($r->ppmp_project_type){
                    case 1:
                        $project_type = "Goods";
                    break;

                    case 2:
                        $project_type = "Infrastructure";
                    break;

                    case 3:
                        $project_type = "Consulting Services";
                    break;

                    default:
                        $project_type = "";
                    break;
                }

                $data[] = array(
                    'id' => $r->ppmp_id,
                    'year' => $r->ppmp_year,
                    'general_description' => $r->ppmp_genaral_description,
                    'project_type' => $project_type,
                    'quantity' => $r->ppmp_quantity . ' ' .$r->unit_code,
                    'cost' => $r->ppmp_cost,
                    'proc_mode'=> $r->proc_code,
                    'fund_name' => $r->fund_name,
                    'pre-proc' => $r->ppmp_pre_proc,
                    'remarks' => $r->ppmp_remarks,
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