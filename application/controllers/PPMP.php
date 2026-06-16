<?php

    defined('BASEPATH') or exit('No direct script access allowed');
    require_once APPPATH . 'third_party/PhpSpreadsheet/Spreadsheet.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    class PPMP extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->load->library("Excel"); 
            $this->load->library("aauth");
            $this->load->model('PPMPModel');
            $this->load->model('LibraryModel');

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
                'current_year' => date('Y'),
                'routing_users' => $this->PPMPModel->getUsersForPPMPRouting()
            );

            $data['notif']  = 1;
            $data['config'] = 1;
            $data['ondue']  = 1;

            $this->load->view('header', $data);
            $this->load->view('ppmp/listview');
            $this->load->view('ppmp/modal_submit');
            $this->load->view('ppmp/submission_inbox');
            $this->load->view('footer');
        }

        public function ppmpList($year,$ppmp_id){
            $year    = (int) $year;
            $l_model = new LibraryModel();

            $data = array(
                'csrf'       => $this->csrf(),
                'csrf_ajax'  => $this->csrf_ajax(),
                'fullname'   => $this->session->fullname,
                'year'       => $year,
                'ppmp_id'   => (int)$ppmp_id,
                'unit'       => $l_model->getActiveUnits(),
                'mode'       => $l_model->getActiveMode(),
                'source'     => $l_model->getActiveSource(),
                'attachment' => $l_model->getActiveAttachment(),
                'items'      => $l_model->getActiveItems()
            );

            $data['notif']  = 1;
            $data['config'] = 1;
            $data['ondue']  = 1;

            $this->load->view('header', $data);
            $this->load->view('ppmp/ppmpitems',$data);
            $this->load->view('ppmp/modal_add');
            $this->load->view('ppmp/modal_edit');
            $this->load->view('footer');
        }

        public function getPPMPList(){
            $draw   = intval($this->input->get("draw"));
            $start  = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));

            $results = $this->PPMPModel->getPPMPListView($start, $length);

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
                    'actions'             => ''
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

        public function getPPMPProjectList()
        {
            $year = (int) $this->uri->segment(3);

            $draw   = intval($this->input->get('draw'));
            $start  = intval($this->input->get('start'));
            $length = intval($this->input->get('length'));

            $search = $this->input->get('search');
            $searchValue = isset($search['value']) ? $search['value'] : '';

            $order = $this->input->get('order');
            $orderColumnIndex = isset($order[0]['column']) ? intval($order[0]['column']) : 1;
            $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

            $columns = [
                0 => null,
                1 => 't6.ppmp_project_id',
                2 => 't1.ppmp_year',
                3 => 't6.ppmp_general_description',
                4 => 't6.ppmp_project_type',
                5 => 't4.proc_code',
                6 => 't3.fund_name',
                7 => 't6.ppmp_budget',
                8 => 't5.fullname',
                9 => null
            ];

            $orderColumn = isset($columns[$orderColumnIndex]) && $columns[$orderColumnIndex] !== null
                ? $columns[$orderColumnIndex]
                : 't6.ppmp_project_id';

            $recordsTotal = $this->PPMPModel->countPPMPProjects($year);
            $recordsFiltered = $this->PPMPModel->countFilteredPPMPProjects($year, $searchValue);
            $projects = $this->PPMPModel->getPPMPProjectListView(
                $start,
                $length,
                $year,
                $searchValue,
                $orderColumn,
                $orderDir
            );

            $data = [];

            foreach ($projects as $r) {
                $projectType = '';

                switch ((int) $r->ppmp_project_type) {
                    case 1:
                        $projectType = 'Goods';
                        break;
                    case 2:
                        $projectType = 'Infrastructure';
                        break;
                    case 3:
                        $projectType = 'Consulting Services';
                        break;
                }

                $projectId = $r->ppmp_project_id;

                $actions = '<div class="btn-action-group">'
                    . '<button type="button" class="btn btn-sm btn-primary btnUpdateProject" '
                    . 'data-id="' . $projectId . '" title="Edit">'
                    . '<i class="fa fa-fw fa-pencil"></i></button>'
                    . '<button type="button" class="btn btn-sm btn-danger btnDeleteProject" '
                    . 'data-id="' . $projectId . '" title="Delete">'
                    . '<i class="fa fa-fw fa-trash"></i></button>'
                    . '</div>';

                $data[] = [
                    'ppmp_project_id'     => $projectId,
                    'year'                => $r->ppmp_year,
                    'general_description' => $r->ppmp_general_description,
                    'project_type'        => $projectType,
                    'proc_mode'           => $r->proc_code,
                    'fund_name'           => $r->fund_name,
                    'office_name'         => $r->office_abbr,
                    'budget'              => $r->ppmp_budget,
                    'encoded_by'          => $r->fullname,
                    'actions'             => $actions
                ];
            }

            $output = [
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data
            ];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
        }


    public function getPPMPProjectItems()
    {
        $projectId = (int) $this->uri->segment(3);

        $items = $this->PPMPModel->getItemsByProject($projectId);

        $data = [];

        foreach ($items as $item) {
            $classification = '';

            switch ((int) $item->ppmp_classification) {
                case 1:
                    $classification = 'MOOE';
                    break;
                case 2:
                    $classification = 'CO';
                    break;
                default:
                    $classification = '—';
                    break;
            }

            $actions = '<div class="btn-action-group">'
                . '<button type="button" class="btn btn-sm btn-primary btnUpdateItem" '
                . 'data-id="' . $item->ppmp_item_id . '" title="Edit Item">'
                . '<i class="fa fa-fw fa-pencil"></i></button>'
                . '<button type="button" class="btn btn-sm btn-danger btnDeleteItem" '
                . 'data-id="' . $item->ppmp_item_id . '" title="Delete Item">'
                . '<i class="fa fa-fw fa-trash"></i></button>'
                . '</div>';

            $data[] = [
                'ppmp_item_id'  => $item->ppmp_item_id,
                'item_description' => $item->item_description,
                'classification'=> $classification,
                'ppmp_quantity' => $item->ppmp_quantity,
                'unit_code'     => $item->unit_code,
                'ppmp_cost'     => $item->ppmp_cost,
                'actions'       => ''
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'data' => $data
            ]));
    }

        public function savePPMP1()
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

        public function savePPMP(){
            $this->load->model('PPMPModel');

            $user_id = $this->session->userdata('userID');
            $office_id = $this->session->userdata('office');

            $ppmp_id = $this->input->post('ppmp_id'); // from tbl_ppmp_temp
            $ppmp_year = $this->input->post('ppmp_year');

            $result = $this->PPMPModel->save_ppmp_projects([
                'ppmp_id' => $ppmp_id,
                'ppmp_year' => $ppmp_year,
                'office_id' => $office_id,
                'created_by' => $user_id,
                'post' => $this->input->post()
            ]);

            if ($result) {
                $this->session->set_flashdata('success', 'PPMP successfully saved.');
            } else {
                $this->session->set_flashdata('error', 'Failed to save PPMP.');
            }

            redirect('PPMP');
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

        public function getPPMPProjectForEdit()
        {
            $projectId = (int) $this->uri->segment(3);

            $project = $this->PPMPModel->getProjectForEdit($projectId);

            if (!$project) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Project not found.'
                ]);
                return;
            }

            $items = $this->PPMPModel->getProjectItemsForEdit($projectId);

            echo json_encode([
                'success' => true,
                'project' => $project,
                'items' => $items
            ]);
        }


        public function updatePPMPProject()
        {
            $this->load->model('PPMPModel');

            $userId = $this->session->userdata('user_id');

            if (!$userId) {
                $userId = $this->session->userdata('id');
            }

            $post = $this->input->post();

            if (empty($post['ppmp_project_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing project ID.'
                ]);
                return;
            }

            if (empty($post['item_id']) || count($post['item_id']) === 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'At least one item is required.'
                ]);
                return;
            }

            $result = $this->PPMPModel->updateProjectWithItems($post, $userId);

            if ($result) {

                $this->session->set_flashdata(
                    'success',
                    'PPMP Project successfully updated.'
                );

            } else {

                $this->session->set_flashdata(
                    'error',
                    'Failed to update PPMP Project.'
                );
            }

            redirect($_SERVER['HTTP_REFERER']);
        }

        public function saveInlinePPMPItem()
        {
            $this->load->model('PPMPModel');

            $userId = $this->session->userdata('user_id');
            if (!$userId) {
                $userId = $this->session->userdata('id');
            }

            $post = $this->input->post();

            if (empty($post['ppmp_project_id']) || empty($post['item_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required item data.'
                ]);
                return;
            }

            $result = $this->PPMPModel->saveInlinePPMPItem($post, $userId);

            echo json_encode($result);
        }


        public function deleteInlinePPMPItem()
        {
            $this->load->model('PPMPModel');

            $userId = $this->session->userdata('user_id');
            if (!$userId) {
                $userId = $this->session->userdata('id');
            }

            $itemId = (int) $this->input->post('ppmp_item_id');

            if ($itemId <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid item ID.'
                ]);
                return;
            }

            $result = $this->PPMPModel->deleteInlinePPMPItem($itemId, $userId);

            echo json_encode($result);
        }

        public function exportPPMPPDF($year, $ppmp_id)
        {
            $this->load->model('PPMPModel');

            $data['year']     = (int) $year;
            $data['ppmp_id']  = (int) $ppmp_id;
            $data['projects'] = $this->PPMPModel->getPPMPPDFData($year, $ppmp_id);

            $this->load->view('ppmp/pdf_ppmp_export', $data);
        }

        public function exportPPMPExcel($year = null)
        {
            $this->load->model('PPMPModel');

            $year = $year ?: date('Y');
            $projects = $this->PPMPModel->getAllPPMPProjectsForYear($year);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('PPMP ' . $year);

            $sheet->setCellValue('A1', 'LUNG CENTER OF THE PHILIPPINES');
            $sheet->setCellValue('A2', 'PROJECT PROCUREMENT MANAGEMENT PLAN');
            $sheet->setCellValue('A3', 'Fiscal Year: ' . $year);

            $sheet->setCellValue('A5', 'Office');
            $sheet->setCellValue('B5', 'Project Description');
            $sheet->setCellValue('C5', 'Project Type');
            $sheet->setCellValue('D5', 'Procurement Mode');
            $sheet->setCellValue('E5', 'Fund Source');
            $sheet->setCellValue('F5', 'Item');
            $sheet->setCellValue('G5', 'Quantity');
            $sheet->setCellValue('H5', 'Unit');
            $sheet->setCellValue('I5', 'Cost');

            $row = 6;

            foreach ($projects as $project) {
                foreach ($project['items'] as $item) {
                    $sheet->setCellValue('A' . $row, $project['office_name']);
                    $sheet->setCellValue('B' . $row, $project['general_description']);
                    $sheet->setCellValue('C' . $row, $project['project_type']);
                    $sheet->setCellValue('D' . $row, $project['proc_mode']);
                    $sheet->setCellValue('E' . $row, $project['fund_name']);
                    $sheet->setCellValue('F' . $row, $item['item_description']);
                    $sheet->setCellValue('G' . $row, $item['ppmp_quantity']);
                    $sheet->setCellValue('H' . $row, $item['unit_code']);
                    $sheet->setCellValue('I' . $row, $item['ppmp_cost']);
                    $row++;
                }
            }

            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'PPMP_' . $year . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            if (ob_get_length()) {
                ob_end_clean();
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }

        public function submitPPMP()
        {
            $this->load->model('PPMPModel');

            $ppmp_id = (int) $this->input->post('ppmp_id');
            $submitted_to = (int) $this->input->post('submitted_to');
            $submission_type = (int) $this->input->post('submission_type');
            $remarks = $this->input->post('remarks', true);

            $submitted_by = (int) $this->session->userdata('userID');
            $office_id = (int) $this->session->userdata('office');

            if ($ppmp_id <= 0 || $submitted_to <= 0 || $submission_type <= 0) {
                $this->session->set_flashdata('fail', 'Please complete all required submission fields.');
                redirect($_SERVER['HTTP_REFERER']);
                return;
            }

            $result = $this->PPMPModel->createPPMPSubmission([
                'ppmp_id' => $ppmp_id,
                'office_id' => $office_id,
                'submitted_by' => $submitted_by,
                'submitted_to' => $submitted_to,
                'submission_type' => $submission_type,
                'remarks' => $remarks
            ]);

            if ($result) {
                $this->session->set_flashdata('success', 'PPMP successfully submitted.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to submit PPMP.');
            }

            redirect($_SERVER['HTTP_REFERER']);
        }


    public function actOnPPMPSubmission()
    {
        $this->load->model('PPMPModel');

        $submission_id = (int) $this->input->post('submission_id');
        $action = (int) $this->input->post('action');
        $remarks = $this->input->post('action_remarks', true);
        $user_id = (int) $this->session->userdata('userID');

        if ($submission_id <= 0 || $action <= 0) {
            $this->session->set_flashdata('fail', 'Invalid action.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $result = $this->PPMPModel->actOnPPMPSubmission(
            $submission_id,
            $action,
            $remarks,
            $user_id
        );

        if ($result) {
            $this->session->set_flashdata('success', 'PPMP submission updated.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to update submission.');
        }

        redirect($_SERVER['HTTP_REFERER']);
    }


    public function ppmpSubmissionInbox()
    {
        $this->load->model('PPMPModel');

        $user_id = (int) $this->session->userdata('userID');

        $data['fullname'] = $this->session->fullname;
        $data['submissions'] = $this->PPMPModel->getPPMPSubmissionInbox($user_id);

        $this->load->view('header', $data);
        $this->load->view('ppmp/submission_inbox', $data);
        $this->load->view('footer');
    }

    public function routePPMP(){
        $this->load->model('PPMPModel');

        $ppmp_id     = (int) $this->input->post('ppmp_id');
        $to_user_id  = (int) $this->input->post('to_user_id');
        $action_type = $this->input->post('action_type', true);
        $remarks     = $this->input->post('remarks', true);

        $from_user_id = (int) $this->session->userdata('userID');
        $office_id    = (int) $this->session->userdata('office');

        if ($ppmp_id <= 0 || empty($action_type)) {
            $this->session->set_flashdata('fail', 'Invalid PPMP routing request.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $result = $this->PPMPModel->routePPMP([
            'ppmp_id'      => $ppmp_id,
            'office_id'    => $office_id,
            'from_user_id' => $from_user_id,
            'to_user_id'   => $to_user_id,
            'action_type'  => $action_type,
            'remarks'      => $remarks
        ]);

        if ($result) {
            $this->session->set_flashdata('success', 'PPMP workflow updated successfully.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to update PPMP workflow.');
        }

        redirect($_SERVER['HTTP_REFERER']);
    }


    public function ppmpWorkflowHistory($ppmp_id)
    {
        $this->load->model('PPMPModel');

        $data['ppmp_id'] = (int) $ppmp_id;
        $data['history'] = $this->PPMPModel->getPPMPWorkflowHistory($ppmp_id);

        $this->load->view('header', $data);
        $this->load->view('ppmp/workflow_history', $data);
        $this->load->view('footer');
    }


    public function ppmpInbox()
    {
        $this->load->model('PPMPModel');

        $user_id = (int) $this->session->userdata('userID');

        $data['inbox'] = $this->PPMPModel->getPPMPInbox($user_id);

        $this->load->view('header', $data);
        $this->load->view('ppmp/workflow_inbox', $data);
        $this->load->view('footer');
    }
    }
?>