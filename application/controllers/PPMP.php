<?php

    defined('BASEPATH') or exit('No direct script access allowed');

    class PPMP extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();

            $this->load->library("aauth");
            $this->load->model('PPMPModel');

            if (!$this->session->logged_in) {
                $this->session->set_flashdata('fail', 'Session expired. Please Sign in');
                redirect('User/index');
            }
        }

        private function validatePPMP()
        {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('ppmp_year', 'PPMP Year', 'required|numeric|exact_length[4]');
            $this->form_validation->set_rules('ppmp_general_description', 'Description', 'required|trim');
            $this->form_validation->set_rules('ppmp_project_type', 'Project Type', 'required|numeric');
            $this->form_validation->set_rules('ppmp_quantity', 'Quantity', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('unit_id', 'Unit', 'required|numeric');
            $this->form_validation->set_rules('proc_id', 'Procurement Mode', 'required|numeric');
            $this->form_validation->set_rules('ppmp_start_proc', 'Start Date', 'required');
            $this->form_validation->set_rules('ppmp_end_proc', 'End Date', 'required');
            $this->form_validation->set_rules('ppmp_delivery', 'Delivery Date', 'required');
            $this->form_validation->set_rules('fund_id', 'Fund Source', 'required|numeric');
            $this->form_validation->set_rules('ppmp_budget', 'Budget', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('ppmp_cost', 'Cost', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('ppmp_remarks', 'Remarks', 'required|trim');
        }

        private function getRowValue($row, $field, $default = '')
        {
            return isset($row->$field) ? $row->$field : $default;
        }

        public function csrf()
        {
            $name = $this->security->get_csrf_token_name();
            $hash = $this->security->get_csrf_hash();
            return '<input type="hidden" name="' . $name . '" value="' . $hash . '">';
        }

        public function csrf_ajax()
        {
            return array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
        }

        public function index()
        {
            $data = array(
                'csrf'         => $this->csrf(),
                'csrf_ajax'    => $this->csrf_ajax(),
                'fullname'     => $this->session->fullname,
                'current_year' => date('Y')
            );

            $data['notif']  = 1;
            $data['config'] = 1;
            $data['ondue']  = 1;

            $this->load->view('header', $data);
            $this->load->view('ppmp/listview');
            $this->load->view('footer');
        }

        public function ppmpList($year)
        {
            $year    = (int) $year;
            $l_model = new LibraryModel();

            $data = array(
                'csrf'       => $this->csrf(),
                'csrf_ajax'  => $this->csrf_ajax(),
                'fullname'   => $this->session->fullname,
                'year'       => $year,
                'unit'       => $l_model->getActiveUnits(),
                'mode'       => $l_model->getActiveMode(),
                'source'     => $l_model->getActiveSource(),
                'attachment' => $l_model->getActiveAttachment()
            );

            $data['notif']  = 1;
            $data['config'] = 1;
            $data['ondue']  = 1;

            $this->load->view('header', $data);
            $this->load->view('ppmp/ppmpitems');
            $this->load->view('ppmp/modal_add');
            $this->load->view('footer');
        }

        public function getPPMPList()
        {
            $draw   = intval($this->input->get("draw"));
            $start  = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));

            $results = $this->PPMPModel->getPPMPListView($start, $length);

            $data = array();

            foreach ($results[0] as $r) {
                $btn_view = '<div class="btn-action-group">'
                    . '<a href="' . base_url("PPMP/ppmpList/") . $r->ppmp_year . '" '
                    . 'class="btn btn-sm btn-primary" title="View Items">'
                    . '<i class="fa fa-fw fa-folder-open"></i></a>'
                    . '</div>';

                $data[] = array(
                    'year'       => $r->ppmp_year,
                    'encoded_by' => $r->fullname,
                    'actions'    => $btn_view
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

        public function getPPMPItemList()
        {
            $year   = (int) $this->uri->segment(3);
            $draw   = intval($this->input->get("draw"));
            $start  = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));

            $results = $this->PPMPModel->getPPMPItemListView($start, $length, $year);

            $data = array();

            foreach ($results[0] as $r) {
                $project_type = '';
                switch ((int) $this->getRowValue($r, 'ppmp_project_type', 0)) {
                    case 1: $project_type = 'Goods';               break;
                    case 2: $project_type = 'Infrastructure';      break;
                    case 3: $project_type = 'Consulting Services'; break;
                }

                $quantity = $this->getRowValue($r, 'ppmp_quantity_description', '');
                if ($quantity === '') {
                    $quantity = trim(
                        $this->getRowValue($r, 'ppmp_quantity_value', '')
                        . ' '
                        . $this->getRowValue($r, 'unit_code', '')
                    );
                }

                $item_id   = $this->getRowValue($r, 'ppmp_item_id', 0);
                $item_year = $this->getRowValue($r, 'ppmp_year', '');

                $actions = '<div class="btn-action-group">'
                    . '<button type="button" id="btnUpdate" '
                    . 'class="btn btn-sm btn-primary" title="Edit">'
                    . '<i class="fa fa-fw fa-pencil"></i></button>'
                    . '<button type="button" id="btnDel" '
                    . 'data-id="' . $item_id . '" '
                    . 'class="btn btn-sm btn-danger" title="Delete">'
                    . '<i class="fa fa-fw fa-trash"></i></button>'
                    . '</div>';

                $data[] = array(
                    'id'                  => $item_id,
                    'year'                => $item_year,
                    'general_description' => $this->getRowValue($r, 'ppmp_general_description', ''),
                    'project_type'        => $project_type,
                    'quantity'            => $quantity,
                    'cost'                => $this->getRowValue($r, 'ppmp_cost', ''),
                    'proc_mode'           => $this->getRowValue($r, 'proc_code', ''),
                    'fund_name'           => $this->getRowValue($r, 'fund_name', ''),
                    'pre-proc'            => $this->getRowValue($r, 'ppmp_pre_proc', 0),
                    'remarks'             => $this->getRowValue($r, 'ppmp_remarks', ''),
                    'encoded_by'          => $this->getRowValue($r, 'fullname', ''),
                    'actions'             => $actions
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

        public function savePPMP()
        {
            $ppmp_year = (int) $this->input->post('ppmp_year');

            $this->validatePPMP();

            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('fail', validation_errors());
                redirect('PPMP/ppmpList/' . $ppmp_year);
                return;
            }

            $start_proc = $this->input->post('ppmp_start_proc', true);
            $end_proc   = $this->input->post('ppmp_end_proc', true);
            $delivery   = $this->input->post('ppmp_delivery', true);
            $unit_id    = (int) $this->input->post('unit_id');
            $quantity   = $this->input->post('ppmp_quantity', true);
            $budget     = $this->input->post('ppmp_budget', true);

            if ($end_proc < $start_proc) {
                $this->session->set_flashdata('fail', 'End date must be on or after the start date.');
                redirect('PPMP/ppmpList/' . $ppmp_year);
                return;
            }

            if (!empty($delivery) && !empty($start_proc) && $delivery < $start_proc) {
                $this->session->set_flashdata('fail', 'Delivery date must be on or after the start date.');
                redirect('PPMP/ppmpList/' . $ppmp_year);
                return;
            }

            $header = $this->PPMPModel->getPPMPHeaderByYear($ppmp_year);

            if ($header) {
                $ppmp_id = $header->ppmp_id;
            } else {
                $header_data = array(
                    'ppmp_year'    => $ppmp_year,
                    'office_id'    => 0,
                    'created_by'   => $this->session->userID,
                    'date_created' => date('Y-m-d H:i:s'),
                    'archived'     => 0
                );

                $header_res = $this->PPMPModel->createPPMPHeader($header_data);

                if (empty($header_res['success'])) {
                    $error_message = !empty($header_res['error']) ? $header_res['error'] : 'Failed to create PPMP header.';
                    $this->session->set_flashdata('fail', $error_message);
                    redirect('PPMP/ppmpList/' . $ppmp_year);
                    return;
                }

                $ppmp_id = $header_res['ppmp_id'];
            }

            $unit      = $this->db->select('unit_code')->where('unit_id', $unit_id)->get('lib_unit')->row();
            $unit_code = $unit ? $unit->unit_code : '';

            $ppmp_data = array(
                'ppmp_id'                   => $ppmp_id,
                'ppmp_general_description'  => $this->input->post('ppmp_general_description', true),
                'ppmp_project_type'         => (int) $this->input->post('ppmp_project_type'),
                'ppmp_quantity_description' => trim($unit_code . ': ' . $quantity . ' x ' . $budget),
                'ppmp_quantity_value'       => $quantity,
                'unit_id'                   => $unit_id,
                'proc_id'                   => (int) $this->input->post('proc_id'),
                'ppmp_pre_proc'             => $this->input->post('ppmp_pre_proc') ? 1 : 0,
                'ppmp_start_proc'           => $start_proc,
                'ppmp_end_proc'             => $end_proc,
                'ppmp_delivery'             => $delivery,
                'fund_id'                   => (int) $this->input->post('fund_id'),
                'ppmp_budget'               => $budget,
                'ppmp_class_id'             => 1,
                'ppmp_cost'                 => $this->input->post('ppmp_cost', true),
                'ppmp_supporting_docs'      => null,
                'ppmp_remarks'              => $this->input->post('ppmp_remarks', true),
                'created_by'                => $this->session->userID,
                'date_created'              => date('Y-m-d H:i:s'),
                'archived'                  => 0
            );

            $this->security->xss_clean($ppmp_data);
            $res = $this->PPMPModel->savePPMPItem($ppmp_data);

            if (!empty($res['success'])) {
                $this->session->set_flashdata('success', 'Successfully created PPMP item.');
            } else {
                $error_message = !empty($res['error']) ? $res['error'] : 'Failed to create PPMP item.';
                $this->session->set_flashdata('fail', $error_message);
            }

            redirect('PPMP/ppmpList/' . $ppmp_year);
        }

        public function deletePPMPItem()
        {
            $id = (int) $this->input->post('id');

            if ($id <= 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid item ID.']);
                return;
            }

            $res = $this->PPMPModel->deletePPMPItem($id);

            if (!empty($res['success'])) {
                echo json_encode(['success' => true]);
            } else {
                $error_message = !empty($res['error']) ? $res['error'] : 'Failed to delete PPMP item.';
                echo json_encode(['success' => false, 'error' => $error_message]);
            }
        }
    }
?>