<?php

    defined('BASEPATH') or exit('No direct script access allowed');
    require_once APPPATH . 'third_party/PhpSpreadsheet/Spreadsheet.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    class Dashboard extends CI_Controller {
        public function __construct(){
            parent::__construct();
            $this->load->library("Excel"); 
            $this->load->library("aauth");
            $this->load->model('PPMPModel');
            $this->load->model('DashboardModel');
            $this->load->model('LibraryModel');

            if (!$this->session->logged_in) {
                $this->session->set_flashdata('fail', 'Session expired. Please Sign in');
                redirect('User/index');
            }
        }

        public function index() {
            // $data['offices'] = $this->LibraryModel->getOffices();
            // $data['ppmp_count'] = $this->PPMPModel->getPPMPCountByStatus();
            // $data['ppmp_count_by_office'] = $this->PPMPModel->getPPMPCountByOffice();
            // $data['ppmp_count_by_category'] = $this->PPMPModel->getPPMPCountByCategory();

            $data['for_review_count'] = $this->PPMPModel->getPPMPCountForReview();
            $data['for_approval_count'] = $this->PPMPModel->getPPMPCountForApproval();

            $this->load->view('header');
            $this->load->view('dashboard/main', $data);
            $this->load->view('footer');
        }

        public function getPPMPForReviewList(){
            $draw   = intval($this->input->get("draw"));
            $start  = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));

            $results = $this->PPMPModel->getPPMPForReviewListView($start, $length);

            $data = array();

            foreach ($results[0] as $r) {
                $btn_view = '<div class="btn-action-group">'
                    . '<a href="' . base_url("PPMP/ppmpList/") . $r->ppmp_year . '/' . $r->ppmp_id . '" '
                    . 'class="btn btn-sm btn-primary" title="View Items">'
                    . '<i class="fa fa-fw fa-folder-open"></i></a>';

                $btn_export = '<a href="' . base_url("PPMP/exportPPMPPDF/") . $r->ppmp_year . '/' . $r->ppmp_id . '" target="_blank" class="btn btn-sm btn-success" title="Export to PDF"> <i class="fas fa-file-pdf mr-1"></i> PDF </a>';
                $btn_workflow = '<button type="button" class="btn btn-sm btn-warning btnRoutePPMP" data-id="' . $r->ppmp_id . '" title="Route PPMP"><i class="fas fa-paper-plane"></i></button>';
                $btn_history = '<a href="' .base_url('PPMP/submissionHistory/' . $r->ppmp_id) .'" class="btn btn-sm btn-info"title="Routing History"><i class="fas fa-history"></i></a>';
                
                $data[] = array(
                    'year'       => $r->ppmp_year,
                    'encoded_by' => $r->fullname,
                    'actions'    => $btn_view . $btn_export . $btn_workflow. $btn_history. '</div>'
                );
            }

            $output = array(
                "draw"            => $draw,
                "recordsTotal"    => $results[1],
                "recordsFiltered" => $results[1],
                "data"            => $data
            );

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
        }
       
    }
?>