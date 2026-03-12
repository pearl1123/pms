<?php
/*by Pearllsss 31012025*/

defined('BASEPATH') or exit('No direct script access allowed');

class Libraries extends CI_Controller
{
    protected $isms_db;

    public function __construct()
    {
        parent::__construct();

        #load all that you need
        $this->load->library("aauth");
        $this->load->model('LibraryModel');
        $this->isms_db = $this->load->database('isms', TRUE);
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






    // ATTACHMENT LIST VIEW
    // Displays module for attachment; Library Module
    // =========================================================================================================================================
    public function attachment()
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
        $this->load->view('libraries/attachment/listview');
        $this->load->view('libraries/attachment/modal_add');
        $this->load->view('libraries/attachment/modal_update');
        $this->load->view('footer');
    }

    // ATTACHMENT LIST VIEW
    // Gets list of attachments for data table
    // =========================================================================================================================================
    public function getAttachmentList()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $l_model = new LibraryModel();

        $results = $l_model->getAttachmentListView($start, $length);

        $data = array();

        $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat" data-toggle="tooltip" title=""><i class="fa fa-fw fa-pencil"></i></button> ';
        $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i> </button>';

        foreach ($results[0] as $r) {
            $data[] = array(
                'id' => $r->attachment_id,
                'name' => $r->attachment_name,
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

    //VALIDATE ATTACHMENT
    //Validated input for unwanted entries; Set security checks
    // =========================================================================================================================================
    protected function validateAttachment($selector)
    {
        if ($selector == 'createAttachment') {
            $config = array(
                array(
                    'field' => 'attachment_name',
                    'label' => 'Attachment Name',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                )
            );
        } else if ($selector == 'updateAttachment') {
            $config = array(
                array(
                    'field'   => 'attachment_id',
                    'label'   => 'Attachment ID',
                    'rules'   => 'required',
                ),
                array(
                    'field' => 'attachment_name',
                    'label' => 'Attachment Name',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                )
            );
        }

        return $this->form_validation->set_rules($config);
    }

    // SAVE ATTACHMENTS
    // Saves newly created attachment
    // =========================================================================================================================================
    public function saveAttachment()
    {
        $l_model = new LibraryModel();

        $attachment_data = array(
            'attachment_name' => htmlentities($_POST['attachment_name']),
            'created_by' => $this->session->userID,
            'date_created' => date("Y-m-d H:i:s")
        );

        $this->validateAttachment('createAttachment');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($attachment_data);
            $res = $l_model->saveAttachment($attachment_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created new attachment record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create new attachment record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/attachment');
    }

    // UPDATE ATTACHMENTS
    // Updates existing attachment
    // =========================================================================================================================================
    public function updateAttachment()
    {
        $l_model = new LibraryModel();

        $attachment_data = array(
            'attachment_name' => htmlentities($_POST['attachment_name']),
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s")
        );

        $param = array('attachment_id' => htmlentities($_POST['attachment_id']));

        $this->validateAttachment('updateAttachment');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($attachment_data);
            $res = $l_model->updateAttachment($attachment_data, $param);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully updated attachment record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to update attachment record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/attachment');
    }

    // DELETE ATTACHMENTS
    // Deletes existing attachment
    // =========================================================================================================================================
    public function deleteAttachment()
    {
        $l_model = new LibraryModel();

        $attachment_data = array(
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived' => 1
        );

        $param = array('attachment_id' => htmlentities($_POST['id']));

        $this->security->xss_clean($attachment_data);
        $res = $l_model->updateAttachment($attachment_data, $param);
        if ($res > 0) {
            $this->session->set_flashdata('success', 'Successfully deleted attachment record.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to delete attachment record.');
        }

        redirect('Libraries/attachment');
    }






    // ITEM LIST VIEW
    // Displays module for item; Library Module
    // =========================================================================================================================================
    public function item()
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
        $this->load->view('libraries/item/listview');
        $this->load->view('libraries/item/modal_add');
        $this->load->view('libraries/item/modal_update');
        $this->load->view('footer');
    }

    //VALIDATE ITEM
    //Validated input for unwanted entries; Set security checks
    // =========================================================================================================================================
    protected function validateItem($selector)
    {
        if ($selector == 'createItem') {
            $config = array(
                array(
                    'field' => 'item_description',
                    'label' => 'Item Description',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                )
            );
        } else if ($selector == 'updateItem') {
            $config = array(
                array(
                    'field'   => 'item_id',
                    'label'   => 'Item ID',
                    'rules'   => 'required',
                ),
                array(
                    'field' => 'item_description',
                    'label' => 'Item Description',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                )
            );
        }

        return $this->form_validation->set_rules($config);
    }

    // ITEM LIST VIEW
    // Gets list of item for data table
    // =========================================================================================================================================
    public function getItemList()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $l_model = new LibraryModel();

        $results = $l_model->getItemList($start, $length);

        $data = array();

        $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat" data-toggle="tooltip" title=""><i class="fa fa-fw fa-pencil"></i></button> ';
        $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i> </button>';

        foreach ($results[0] as $r) {
            $data[] = array(
                'id' => $r->item_id,
                'name' => $r->item_description,
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

    // SAVE ITEM
    // Saves newly created item
    // =========================================================================================================================================
    public function saveItem()
    {
        $l_model = new LibraryModel();

        $item_data = array(
            'item_description' => htmlentities($_POST['item_description']),
            'created_by' => $this->session->userID,
            'date_created' => date("Y-m-d H:i:s")
        );

        $this->validateItem('createItem');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($item_data);
            $res = $l_model->saveItem($item_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created new item record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create new item record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/item');
    }

    // UPDATE ITEM
    // Updates existing item
    // =========================================================================================================================================
    public function updateItem()
    {
        $l_model = new LibraryModel();

        $item_data = array(
            'item_description' => htmlentities($_POST['item_description']),
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s")
        );

        $param = array('item_id' => htmlentities($_POST['item_id']));

        $this->validateItem('updateItem');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($item_data);
            $res = $l_model->updateItem($item_data, $param);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully updated item record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to update item record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/item');
    }

    // DELETE ITEM
    // Deletes existing item
    // =========================================================================================================================================
    public function deleteItem()
    {
        $l_model = new LibraryModel();

        $item_data = array(
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived' => 1
        );

        $param = array('item_id' => htmlentities($_POST['id']));

        $this->security->xss_clean($item_data);
        $res = $l_model->updateItem($item_data, $param);
        if ($res > 0) {
            $this->session->set_flashdata('success', 'Successfully deleted item record.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to delete item record.');
        }

        redirect('Libraries/item');
    }





    // OFFICE LIST VIEW
    // Displays module for office; Library Module
    // =========================================================================================================================================
    public function office()
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
        $this->load->view('libraries/office/listview');
        $this->load->view('libraries/office/modal_add');
        $this->load->view('libraries/office/modal_update');
        $this->load->view('footer');
    }

    //VALIDATE OFFICE
    //Validated input for unwanted entries; Set security checks
    // =========================================================================================================================================
    protected function validateOffice($selector)
    {
        if ($selector == 'createOffice') {
            $config = array(
                array(
                    'field' => 'office_desc',
                    'label' => 'Office Description',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                ),
                array(
                    'field' => 'office_abbr',
                    'label' => 'Office Abbreviation',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                )
            );
        } else if ($selector == 'updateOffice') {
            $config = array(
                array(
                    'field'   => 'office_id',
                    'label'   => 'Office ID',
                    'rules'   => 'required',
                ),
                array(
                    'field' => 'office_desc',
                    'label' => 'Office Description',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                ),
                array(
                    'field' => 'office_abbr',
                    'label' => 'Office Abbreviation',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                )
            );
        }

        return $this->form_validation->set_rules($config);
    }

    // GET OFFICE 
    // Gets office by office id
    // =========================================================================================================================================
    public function getOfficeById()
    {
        $office_id = $this->input->post('office_id');

        $l_model = new LibraryModel();
        $office = $l_model->getOfficeById($office_id);

        echo json_encode($office);
        exit();
    }

    // OFFICE LIST VIEW
    // Gets list of offices for data table
    // =========================================================================================================================================
    public function getOfficeList()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $l_model = new LibraryModel();

        $results = $l_model->getOfficeList($start, $length);

        $data = array();

        $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat" data-toggle="tooltip" title=""><i class="fa fa-fw fa-pencil"></i></button> ';
        $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i> </button>';

        foreach ($results[0] as $r) {
            $data[] = array(
                'id' => $r->office_id,
                'name' => $r->office_desc,
                'abbr' => $r->office_abbr,
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

    // SAVE OFFICE
    // Saves newly created office
    // =========================================================================================================================================
    public function saveOffice()
    {
        $l_model = new LibraryModel();

        $office_data = array(
            'office_desc' => htmlentities($_POST['office_desc']),
            'office_abbr' => htmlentities($_POST['office_abbr']),
            'created_by' => $this->session->userID,
            'date_created' => date("Y-m-d H:i:s")
        );

        $this->validateOffice('createOffice');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($office_data);
            $res = $l_model->saveOffice($office_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created new office record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create new office record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/office');
    }

    // UPDATE OFFICE
    // Updates existing office
    // =========================================================================================================================================
    public function updateOffice()
    {
        $l_model = new LibraryModel();

        $office_data = array(
            'office_desc' => htmlentities($_POST['office_desc']),
            'office_abbr' => htmlentities($_POST['office_abbr']),
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s")
        );

        $param = array('office_id' => htmlentities($_POST['office_id']));

        $this->validateOffice('updateOffice');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($office_data);
            $res = $l_model->updateOffice($office_data, $param);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully updated office record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to update office record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/office');
    }

    // DELETE OFFICE
    // Deletes existing office
    // =========================================================================================================================================
    public function deleteOffice()
    {
        $l_model = new LibraryModel();

        $office_data = array(
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived' => 1
        );

        $param = array('office_id' => htmlentities($_POST['id']));

        $this->security->xss_clean($office_data);
        $res = $l_model->updateOffice($office_data, $param);
        if ($res > 0) {
            $this->session->set_flashdata('success', 'Successfully deleted office record.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to delete office record.');
        }

        redirect('Libraries/office');
    }




    // PROCUREMENT MODE LIST VIEW
    // Displays module for mode; Library Module
    // =========================================================================================================================================
    public function mode()
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
        $this->load->view('libraries/mode/listview');
        $this->load->view('libraries/mode/modal_add');
        $this->load->view('libraries/mode/modal_update');
        $this->load->view('footer');
    }

    //VALIDATE PROCUREMENT MODE
    //Validated input for unwanted entries; Set security checks
    // =========================================================================================================================================
    protected function validateMode($selector)
    {
        if ($selector == 'createMode') {
            $config = array(
                array(
                    'field' => 'proc_code',
                    'label' => 'Procurement Mode Code',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                ),
                array(
                    'field' => 'proc_name',
                    'label' => 'Procurement Mode Name',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                )
            );
        } else if ($selector == 'updateMode') {
            $config = array(
                array(
                    'field'   => 'proc_id',
                    'label'   => 'Procurment Mode ID',
                    'rules'   => 'required',
                ),
                array(
                    'field' => 'proc_code',
                    'label' => 'Procurement Mode Code',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                ),
                array(
                    'field' => 'proc_name',
                    'label' => 'Procurement Mode Name',
                    'rules' => 'regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\s]+$/]|required',
                )
            );
        }

        return $this->form_validation->set_rules($config);
    }

    // PROCUREMENT MODE LIST VIEW
    // Gets list of procurement modes for data table
    // =========================================================================================================================================
    public function getModeList()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $l_model = new LibraryModel();

        $results = $l_model->getModeListView($start, $length);

        $data = array();

        $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat" data-toggle="tooltip" title=""><i class="fa fa-fw fa-pencil"></i></button> ';
        $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i> </button>';

        foreach ($results[0] as $r) {
            $data[] = array(
                'id' => $r->proc_id,
                'code' => $r->proc_code,
                'name' => $r->proc_name,
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

    // SAVE PROCURMENT MODE
    // Saves newly created procurement mode
    // =========================================================================================================================================
    public function saveMode()
    {
        $l_model = new LibraryModel();

        $mode_data = array(
            'proc_code' => htmlentities($_POST['proc_code']),
            'proc_name' => htmlentities($_POST['proc_name']),
            'created_by' => $this->session->userID,
            'date_created' => date("Y-m-d H:i:s")
        );

        $this->validateMode('createMode');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($mode_data);
            $res = $l_model->saveMode($mode_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created new procurement mode record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create new procurement mode record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/mode');
    }

    // UPDATE PROCUREMENT MODE
    // Updates existing procurement mode
    // =========================================================================================================================================
    public function updateMode()
    {
        $l_model = new LibraryModel();

        $mode_data = array(
            'proc_code' => htmlentities($_POST['proc_code']),
            'proc_name' => htmlentities($_POST['proc_name']),
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s")
        );

        $param = array('proc_id' => htmlentities($_POST['proc_id']));

        $this->validateMode('updateMode');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($mode_data);
            $res = $l_model->updateMode($mode_data, $param);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully updated procurement mode record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to update procurement mode record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/mode');
    }

    // DELETE PROCUREMENT MODE
    // Deletes existing procurement mode
    // =========================================================================================================================================
    public function deleteMode()
    {
        $l_model = new LibraryModel();

        $mode_data = array(
            'modified_by' => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived' => 1
        );

        $param = array('proc_id' => htmlentities($_POST['id']));

        $this->security->xss_clean($mode_data);
        $res = $l_model->updateMode($mode_data, $param);
        if ($res > 0) {
            $this->session->set_flashdata('success', 'Successfully deleted procurement mode record.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to delete procurement mode record.');
        }

        redirect('Libraries/mode');
    }





    // PROCUREMENT SETTINGS VIEW
    // Displays module for settings; Library Module
    // =========================================================================================================================================
    public function procurementSettings()
    {
        $l_model = new LibraryModel();
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
        $this->load->view('libraries/procurementSettings/listview');
        $this->load->view('libraries/procurementSettings/modal_add');
        $this->load->view('footer');
    }

    // PROCUREMENT SETTINGS LIST VIEW
    // Gets list of procurement modes for data table
    // =========================================================================================================================================
    public function getSettingsList()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $l_model = new LibraryModel();

        $results = $l_model->getSettingsListView($start, $length);

        $data = array();

        foreach ($results[0] as $r) {
            $btn_update = '<a href="' . base_url("Libraries/updateProcurementSetting/") . $r->proc_code . '" id="btnUpdate" class="btn btn-sm btn-primary btn-flat"><i class="fa fa-fw fa-pencil"></i></a> ';
            $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i> </button>';

            $data[] = array(
                'id' => $r->proc_id,
                'code' => $r->proc_code,
                'name' => $r->proc_name,
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

    // VIEW UPDATE PROCUREMENT SETTING
    // Display view for procurement setting update
    // =========================================================================================================================================
    public function updateProcurementSetting($id)
    {
        $l_model = new LibraryModel();

        $attachment = $l_model->getSettingsUpdateView($id);

        $data = array(
            'csrf' => $this->csrf(),
            'csrf_ajax' => $this->csrf_ajax(),
            'fullname' => $this->session->fullname,
            'attachment' => $attachment[0],
            'proc_attach' => $attachment[1],
            'proc_code' => $attachment[2]
        );

        #notification placeholder for now need to configure this later
        $data['notif'] = 1;
        $data['config'] = 1;
        $data['ondue'] = 1;

        $this->load->view('header', $data);
        $this->load->view('libraries/procurementSettings/updateview');
        $this->load->view('footer');
    }

    // UPDATE PROCUREMENT MODE
    // Updates existing procurement mode
    // =========================================================================================================================================
    public function updateSettings($id, $code, $value)
    {
        $l_model = new LibraryModel();

        if ($value == 'checked') {
            $settings_data = array(
                'archived' => 1,
                'modified_by' => $this->session->userID,
                'date_modified' => date("Y-m-d H:i:s")
            );
            $param = array('attachment_id' => $id, 'proc_code' => $code);

            $res = $l_model->updateSettings($settings_data, $param);
        } else if ($value == 'unchecked') {
            $settings_data = array(
                'proc_code' => $code,
                'attachment_id' => $id,
                'created_by' => $this->session->userID,
                'date_created' => date("Y-m-d H:i:s")
            );

            $res = $l_model->saveSettings($settings_data);
        }

        redirect('Libraries/updateProcurementSetting/' . $code);
    }





    // UNIT LIST VIEW
    // Displays module for unit; Library Module
    // =========================================================================================================================================
    public function unit()
    {
        $data = array(
            'csrf'      => $this->csrf(),
            'csrf_ajax' => $this->csrf_ajax(),
            'fullname'  => $this->session->fullname,
        );

        $data['notif']  = 1;
        $data['config'] = 1;
        $data['ondue']  = 1;

        $this->load->view('header', $data);
        $this->load->view('libraries/unit/listview');
        $this->load->view('libraries/unit/modal_add');
        $this->load->view('libraries/unit/modal_update');
        $this->load->view('footer');
    }

    //VALIDATE UNIT
    //Validated input for unwanted entries; Set security checks
    // =========================================================================================================================================
    protected function validateUnit($selector)
    {
        if ($selector == 'createUnit') {
            $config = array(
                array(
                    'field' => 'unit_code',
                    'label' => 'Unit Code',
                    'rules' => 'required|trim|max_length[100]'
                ),
                array(
                    'field' => 'unit_description',
                    'label' => 'Unit Description',
                    'rules' => 'trim|max_length[255]'
                )
            );
        } else if ($selector == 'updateUnit') {
            $config = array(
                array(
                    'field' => 'unit_id',
                    'label' => 'Unit ID',
                    'rules' => 'required|integer'
                ),
                array(
                    'field' => 'unit_code',
                    'label' => 'Unit Code',
                    'rules' => 'required|trim|max_length[100]'
                ),
                array(
                    'field' => 'unit_description',
                    'label' => 'Unit Description',
                    'rules' => 'trim|max_length[255]'
                )
            );
        }

        return $this->form_validation->set_rules($config);
    }

    // UNIT LIST VIEW
    // Gets list of UNIT for data table
    // =========================================================================================================================================
    public function getUnitList()
    {
        $draw   = intval($this->input->get("draw"));
        $start  = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $search = $this->input->get('search')['value'];
        $order  = $this->input->get('order');
        $columns = $this->input->get('columns');

        $column_index = $order[0]['column'];
        $column_name = $columns[$column_index]['name'];
        $column_dir  = $order[0]['dir'];

        $l_model = new LibraryModel();
        list($results, $totalFiltered) = $l_model->getUnitList($start, $length, $search, $column_name, $column_dir);

        $data = [];
        $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat"><i class="fa fa-fw fa-pencil"></i></button> ';
        $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i></button>';

        foreach ($results as $r) {
            $data[] = [
                'id'               => $r->unit_id,
                'unit_code'        => $r->unit_code,
                'unit_description' => $r->unit_description,
                'encoded_by'       => $r->fullname,
                'actions'          => $btn_update . $btn_delete,
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalFiltered['total'],
            "recordsFiltered" => $totalFiltered['filtered'],
            "data" => $data
        ]);
        exit();
    }

    // SAVE UNIT
    // Saves newly created unit
    // =========================================================================================================================================
    public function saveUnit()
    {
        $l_model = new LibraryModel();

        $unit_data = array(
            'unit_code'        => $this->input->post('unit_code', TRUE),
            'unit_description' => $this->input->post('unit_description', TRUE),
            'created_by'       => $this->session->userID,
            'date_created'     => date("Y-m-d H:i:s"),
        );

        $this->validateUnit('createUnit');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($unit_data);
            $res = $l_model->saveUnit($unit_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created new unit record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create new unit record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }

        redirect('Libraries/unit');
    }

    // UPDATE UNIT
    // Updates existing unit
    // =========================================================================================================================================
    public function updateUnit()
    {
        $l_model = new LibraryModel();

        $unit_data = array(
            'unit_code'        => $this->input->post('unit_code', TRUE),
            'unit_description' => $this->input->post('unit_description', TRUE),
            'modified_by'      => $this->session->userID,
            'date_modified'    => date("Y-m-d H:i:s"),
        );

        $param = array('unit_id' => intval($_POST['unit_id']));

        $this->validateUnit('updateUnit');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($unit_data);
            $res = $l_model->updateUnit($unit_data, $param);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully updated unit record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to update unit record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }

        redirect('Libraries/unit');
    }

    // DELETE ITEM
    // Deletes existing item
    // =========================================================================================================================================
    public function deleteUnit()
    {
        $l_model = new LibraryModel();

        $unit_data = array(
            'modified_by'   => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived'      => 1,
        );

        $param = array('unit_id' => intval($_POST['id']));

        $this->security->xss_clean($unit_data);
        $res = $l_model->updateStock($unit_data, $param);

        if ($res > 0) {
            $this->session->set_flashdata('success', 'Successfully deleted unit record.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to delete unit record.');
        }

        redirect('Libraries/unit');
    }






    // STOCK LIST VIEW
    // Displays module for stock; Library Module
    // =========================================================================================================================================
    public function stock()
    {
        $l_model = new LibraryModel();

        $isms_items = $this->isms_db
            ->select('id, name')
            ->from('item_list')
            ->get()
            ->result();

        $pharmacy_items = $this->isms_db
            ->select('
                b.id as brand_id,
                b.brand_name,
                g.generic_name,
                d.dosage_name
            ')
            ->from('pcsr_brand_info b')
            ->join('pcsr_generic_info g', 'g.id = b.generic_id', 'left')
            ->join('pcsr_dosage_form d', 'd.id = g.dosage_id', 'left')
            ->where('b.delete_flag', 0)
            ->where('g.delete_flag', 0)
            ->where('d.delete_flag', 0)
            ->get()
            ->result();

        $data = array(
            'csrf'      => $this->csrf(),
            'csrf_ajax' => $this->csrf_ajax(),
            'fullname'  => $this->session->fullname,
            'items'     => $l_model->getActiveItems(),
            'isms_items'  => $isms_items,
            'pharmacy_items' => $pharmacy_items,
            'units'     => $l_model->getActiveUnits(),
        );

        $data['notif']  = 1;
        $data['config'] = 1;
        $data['ondue']  = 1;

        $this->load->view('header', $data);
        $this->load->view('libraries/stock/listview');
        $this->load->view('libraries/stock/modal_add');
        $this->load->view('libraries/stock/modal_update');
        $this->load->view('footer');
    }

    //VALIDATE ITEM
    //Validated input for unwanted entries; Set security checks
    // =========================================================================================================================================
    protected function validateStock($selector)
    {
        if ($selector == 'createStock') {
            $config = array(
                array('field' => 'item_id',      'label' => 'Item',         'rules' => 'required'),
                array('field' => 'unit_id',      'label' => 'Unit',         'rules' => 'required|integer'),
                array('field' => 'stock_onhand', 'label' => 'Stock Onhand', 'rules' => 'required|integer'),
            );
        } else if ($selector == 'updateStock') {
            $config = array(
                array('field' => 'stock_id',     'label' => 'Stock ID',     'rules' => 'required|integer'),
                array('field' => 'item_id',      'label' => 'Item',         'rules' => 'required'),
                array('field' => 'unit_id',      'label' => 'Unit',         'rules' => 'required|integer'),
                array('field' => 'stock_onhand', 'label' => 'Stock Onhand', 'rules' => 'required|integer'),
            );
        }

        return $this->form_validation->set_rules($config);
    }

    // GET STOCK
    // Gets a stock by id
    // =========================================================================================================================================
    public function getStockById()
    {
        $l_model = new LibraryModel();
        $stock_id = $this->input->post('stock_id');

        $stock = $l_model->getStockById($stock_id);

        $isms_items = $this->isms_db
            ->select('id, name')
            ->from('item_list')
            ->get()
            ->result();
        $isms_lookup = [];
        foreach ($isms_items as $item) {
            $isms_lookup[(int)$item->id] = $item->name;
        }

        $pharmacy_items = $this->isms_db
            ->select('
                b.id as brand_id,
                b.brand_name,
                g.generic_name,
                d.dosage_name
            ')
            ->from('pcsr_brand_info b')
            ->join('pcsr_generic_info g', 'g.id = b.generic_id', 'left')
            ->join('pcsr_dosage_form d', 'd.id = g.dosage_id', 'left')
            ->where('b.delete_flag', 0)
            ->where('g.delete_flag', 0)
            ->where('d.delete_flag', 0)
            ->get()
            ->result();

        $pharmacy_lookup = [];
        foreach ($pharmacy_items as $p) {
            $pharmacy_lookup[(int)$p->brand_id] =
                $p->brand_name . ' (' . $p->generic_name . ' - ' . $p->dosage_name . ')';
        }

        if ($stock) {
            // Build the display text
            if ($stock->item_source === 'diet') {
                $desc = $isms_lookup[(int)$stock->item_id] ?? '[Item not found]';
            } else if ($stock->item_source === 'pharmacy') {
                $desc = $pharmacy_lookup[(int)$stock->item_id] ?? '[Pharmacy item not found]';
            } else {
                $desc = $stock->item_description ?? '[No description]';
            }

            echo json_encode([
                'id' => $stock->stock_id,
                'text' => $desc . " (" . $stock->unit_code . ") - On hand: " . $stock->stock_onhand,
                'item_description' => $desc,
                'unit_code'        => $stock->unit_code,
                'stock_onhand'     => $stock->stock_onhand,
                'fullname'         => $stock->fullname,
                'item_id'          => $stock->item_id,
                'unit_id'          => $stock->unit_id,
            ]);
        } else {
            echo json_encode(null);
        }
        exit();
    }

    // ITEM LIST VIEW
    // Gets list of item for data table
    // =========================================================================================================================================
    public function getStockList()
    {
        $draw   = intval($this->input->get("draw"));
        $start  = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $search_value = $this->input->get('search')['value'] ?? '';

        $order_column_index = $this->input->get('order')[0]['column'] ?? 0;
        $order_dir          = $this->input->get('order')[0]['dir'] ?? 'asc';

        $columns = ['s.stock_id', 's.item_id', 'u.unit_code', 's.stock_onhand', 'u2.fullname'];
        $order_column = $columns[$order_column_index] ?? 's.stock_id';

        $l_model = new LibraryModel();
        list($results, $total, $filtered) = $l_model->getStockList($start, $length, $search_value, $order_column, $order_dir);

        $isms_items = $this->isms_db
            ->select('id, name')
            ->from('item_list')
            ->get()
            ->result();
        $isms_lookup = [];
        foreach ($isms_items as $item) {
            $isms_lookup[(int)$item->id] = $item->name;
        }

        $pharmacy_items = $this->isms_db
            ->select('
                b.id as brand_id,
                b.brand_name,
                g.generic_name,
                d.dosage_name
            ')
            ->from('pcsr_brand_info b')
            ->join('pcsr_generic_info g', 'g.id = b.generic_id', 'left')
            ->join('pcsr_dosage_form d', 'd.id = g.dosage_id', 'left')
            ->where('b.delete_flag', 0)
            ->where('g.delete_flag', 0)
            ->where('d.delete_flag', 0)
            ->get()
            ->result();

        $pharmacy_lookup = [];
        foreach ($pharmacy_items as $p) {
            $pharmacy_lookup[(int)$p->brand_id] =
                $p->brand_name . ' (' . $p->generic_name . ' - ' . $p->dosage_name . ')';
        }

        $data = array();

        $btn_update = '<button type="button" id="btnUpdate" class="btn btn-sm btn-primary btn-flat"><i class="fa fa-fw fa-pencil"></i></button> ';
        $btn_delete = '<button type="button" id="btnDel" class="btn btn-sm btn-danger btn-flat"><i class="fa fa-fw fa-trash"></i></button>';

        foreach ($results as $r) {
            if ($r->item_source === 'diet') {
                $desc = $isms_lookup[(int)$r->item_id] ?? '[Item not found]';
            } else if ($r->item_source === 'pharmacy') {
                $desc = $pharmacy_lookup[(int)$r->item_id] ?? '[Pharmacy item not found]';
            } else {
                $desc = $r->item_description ?? '[No description]';
            }

            $data[] = [
                'id'               => $r->stock_id,
                'item_description' => $desc,
                'unit_code'        => $r->unit_code,
                'stock_onhand'     => $r->stock_onhand,
                'fullname'         => $r->fullname,
                'item_id'          => $r->item_id,
                'unit_id'          => $r->unit_id,
                'actions'          => $btn_update . $btn_delete,
            ];
        }

        echo json_encode([
            "draw"            => $draw,
            "recordsTotal"    => $total,
            "recordsFiltered" => $filtered,
            "data"            => $data,
        ]);
        exit();
    }

    // SAVE ITEM
    // Saves newly created item
    // =========================================================================================================================================
    public function saveStock()
    {
        $l_model = new LibraryModel();

        $raw_item_id = $this->input->post('item_id', true);

        $item_source = 'unknown';
        $item_id = 0;

        if (strpos($raw_item_id, 'lib_') === 0) {
            $item_source = 'library';
            $item_id = intval(str_replace('lib_', '', $raw_item_id));
        } else if (strpos($raw_item_id, 'diet_') === 0) {
            $item_source = 'diet';
            $item_id = intval(str_replace('diet_', '', $raw_item_id));
        } else if (strpos($raw_item_id, 'pharm_') === 0) {
            $item_source = 'pharmacy';
            $item_id = intval(str_replace('pharm_', '', $raw_item_id));
        }

        $stock_data = [
            'item_id'      => $item_id,
            'item_source'  => $item_source,
            'unit_id'      => intval($this->input->post('unit_id', true)),
            'stock_onhand' => intval($this->input->post('stock_onhand', true)),
            'created_by'   => $this->session->userID,
            'date_created' => date("Y-m-d H:i:s")
        ];

        $this->validateStock('createStock');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($stock_data);
            $res = $l_model->saveStock($stock_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created new stock record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create new stock record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }

        redirect('Libraries/stock');
    }

    // UPDATE ITEM
    // Updates existing item
    // =========================================================================================================================================
    public function updateStock()
    {
        $l_model = new LibraryModel();

        $raw_item_id = $this->input->post('item_id', true);

        $item_source = 'unknown';
        $item_id = 0;

        if (strpos($raw_item_id, 'lib_') === 0) {
            $item_source = 'library';
            $item_id = intval(str_replace('lib_', '', $raw_item_id));
        } else if (strpos($raw_item_id, 'diet_') === 0) {
            $item_source = 'diet';
            $item_id = intval(str_replace('diet_', '', $raw_item_id));
        } else if (strpos($raw_item_id, 'pharm_') === 0) {
            $item_source = 'pharmacy';
            $item_id = intval(str_replace('pharm_', '', $raw_item_id));
        }

        $stock_data = array(
            'item_id'       => $item_id,
            'item_source'   => $item_source,
            'unit_id'       => intval($this->input->post('unit_id', true)),
            'stock_onhand'  => intval($this->input->post('stock_onhand', true)),
            'modified_by'   => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
        );

        $param = array('stock_id' => intval($this->input->post('stock_id')));

        $this->validateStock('updateStock');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($stock_data);
            $res = $l_model->updateStock($stock_data, $param);

            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully updated stock record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to update stock record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }

        redirect('Libraries/stock');
    }

    // public function updateStock()
    // {
    //     $l_model = new LibraryModel();

    //     $stock_data = array(
    //         'item_id'       => intval($_POST['item_id']),
    //         'unit_id'       => intval($_POST['unit_id']),
    //         'stock_onhand'  => intval($_POST['stock_onhand']),
    //         'modified_by'   => $this->session->userID,
    //         'date_modified' => date("Y-m-d H:i:s"),
    //     );

    //     $param = array('stock_id' => intval($_POST['id']));

    //     $this->validateStock('updateStock');

    //     if ($this->form_validation->run()) {
    //         $this->security->xss_clean($stock_data);
    //         $res = $l_model->updateStock($stock_data, $param);
    //         if ($res > 0) {
    //             $this->session->set_flashdata('success', 'Successfully updated stock record.');
    //         } else {
    //             $this->session->set_flashdata('fail', 'Failed to update stock record.');
    //         }
    //     } else {
    //         $this->session->set_flashdata('fail', validation_errors());
    //     }

    //     redirect('Libraries/stock');
    // }

    // DELETE ITEM
    // Deletes existing item
    // =========================================================================================================================================
    public function deleteStock()
    {
        $l_model = new LibraryModel();

        $item_data = [
            'modified_by'   => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived'      => 1
        ];

        $param = ['stock_id' => $this->input->post('id', true)];

        $this->security->xss_clean($item_data);

        $res = $l_model->updateStock($item_data, $param);

        if ($res > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Successfully deleted stock record.']);
        } else {
            echo json_encode(['status' => 'fail', 'message' => 'Failed to delete stock record.']);
        }
    }





    // FUND SOURCE LIST VIEW
    // Displays module for attachment; Library Module
    // =========================================================================================================================================
    public function fund()
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
        $this->load->view('libraries/fund/listview');
        $this->load->view('libraries/fund/modal_add');
        $this->load->view('libraries/fund/modal_update');
        $this->load->view('footer');
    }
    public function supplier()
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
        $this->load->view('libraries/supplier/listview');
        $this->load->view('libraries/supplier/modal_add');
        $this->load->view('libraries/supplier/modal_update');
        $this->load->view('footer');
    }

    public function getSupplierList()
    {
        $start  = $_GET['start'];
        $length = $_GET['length'];

        $l_model   = new LibraryModel();
        $fetch_data = $l_model->getSupplierList($start, $length);

        $data = array();
        foreach ($fetch_data[0] as $row) {

            $sub_array = array();
            $sub_array['id']                      = $row->supplier_id;
            $sub_array['supplier_name']           = $row->supplier_name;
            $sub_array['supplier_email']          = $row->supplier_email;
            $sub_array['supplier_address']        = $row->supplier_address;
            $sub_array['supplier_contact']              = $row->supplier_contact;
            $sub_array['supplier_contact_person'] = $row->supplier_contact_person;
            $sub_array['encoded_by']              = $row->encoded_by_name; // fixed
            $sub_array['modified_by']             = $row->modified_by;
            $sub_array['status']                  = ($row->archived == 0) ? 'Active' : 'Inactive';

            $sub_array['actions'] = '
    <button type="button" class="btn btn-sm btn-info btnUpdate" data-id="' . $row->supplier_id . '">
        <i class="fas fa-edit"></i>
    </button>
    <button type="button" class="btn btn-sm btn-danger btnDel" data-id="' . $row->supplier_id . '">
        <i class="fas fa-trash"></i>
    </button>
';

            $data[] = $sub_array;
        }

        $output = array(
            "draw"            => intval($_GET["draw"]),
            "recordsTotal"    => $fetch_data[1],
            "recordsFiltered" => $fetch_data[1],
            "data"            => $data
        );

        header('Content-Type: application/json');
        echo json_encode($output);
        exit;
    }
    // SAVE SUPPLIER
    // Creates new supplier record
    // =========================================================================================================================================
    public function saveSupplier()
    {
        $l_model = new LibraryModel();

        $supplier_data = array(
            'supplier_name' => htmlentities($this->input->post('supplier_name')),
            'supplier_address'       => htmlentities($this->input->post('supplier_address')),
            'supplier_email' => htmlentities($this->input->post('supplier_email')),
            'supplier_contact'    => htmlentities($this->input->post('supplier_contact')),
            'supplier_contact_person' => htmlentities($this->input->post('supplier_contact_person')),
            'created_by'    => $this->session->userID,
            'date_created'  => date("Y-m-d H:i:s")
        );

        // Make sure to create this validation method or update your existing one
        $this->validateSupplier('createSupplier');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($supplier_data);
            $res = $l_model->saveSupplier($supplier_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created new supplier record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create new supplier record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/supplier');
    }

    // UPDATE SUPPLIER
    // Updates existing supplier
    // =========================================================================================================================================
    public function updateSupplier()
    {
        $l_model = new LibraryModel();

        $supplier_data = array(
            'supplier_name'          => htmlentities($_POST['supplier_name']),
            'supplier_address'       => htmlentities($_POST['supplier_address']),
            'supplier_email'         => htmlentities($_POST['supplier_email']),
            'supplier_contact'       => htmlentities($_POST['supplier_contact']),
            'supplier_contact_person' => htmlentities($_POST['supplier_contact_person']),
            'modified_by'            => $this->session->userID,
            'date_modified'          => date("Y-m-d H:i:s")
        );

        $param = array('supplier_id' => htmlentities($_POST['supplier_id']));

        $this->validateSupplier('updateSupplier');

        if ($this->form_validation->run()) {
            $this->security->xss_clean($supplier_data);
            $res = $l_model->updateSupplier($supplier_data, $param);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully updated supplier record.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to update supplier record.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/supplier');
    }
    // DELETE SUPPLIER
    // Soft deletes existing supplier (archived = 1)
    // =========================================================================================================================================
    public function deleteSupplier()
    {
        $l_model = new LibraryModel();

        $supplier_data = array(
            'modified_by'   => $this->session->userID,
            'date_modified' => date("Y-m-d H:i:s"),
            'archived'      => 1
        );

        // Note: AJAX passes 'id', but database param is 'supplier_id'
        $param = array('supplier_id' => htmlentities($this->input->post('id')));

        $this->security->xss_clean($supplier_data);
        $res = $l_model->updateSupplier($supplier_data, $param);
        if ($res > 0) {
            $this->session->set_flashdata('success', 'Successfully deleted supplier record.');
        } else {
            $this->session->set_flashdata('fail', 'Failed to delete supplier record.');
        }

        // Usually for AJAX deletes, you don't redirect, but keeping your original logic
        redirect('Libraries/supplier');
    }
    protected function validateSupplier($selector)
    {
        if ($selector == 'createSupplier') {
            $config = array(
                array(
                    'field' => 'supplier_name',
                    'label' => 'Supplier Name',
                    'rules' => 'required|trim|regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\&\s]+$/]',
                ),
                array(
                    'field' => 'supplier_address',
                    'label' => 'Address',
                    'rules' => 'required|trim',
                ),
                array(
                    'field' => 'supplier_email',
                    'label' => 'Email Address',
                    'rules' => 'required|trim|valid_email',
                ),
                array(
                    'field' => 'supplier_contact',
                    'label' => 'Contact Number',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'supplier_contact_person',
                    'label' => 'Contact Person',
                    'rules' => 'required|trim',
                ),
            );
        } else if ($selector == 'updateSupplier') {
            $config = array(
                array(
                    'field' => 'supplier_name',
                    'label' => 'Supplier Name',
                    'rules' => 'required|trim|regex_match[/^[)\([\]\/\,:0-9a-zA-Z\-\.\&\s]+$/]',
                ),
                array(
                    'field' => 'supplier_address',
                    'label' => 'Address',
                    'rules' => 'required|trim',
                ),
                array(
                    'field' => 'supplier_email',
                    'label' => 'Email Address',
                    'rules' => 'required|trim|valid_email',
                ),
                array(
                    'field' => 'supplier_contact',
                    'label' => 'Contact Number',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'supplier_contact_person',
                    'label' => 'Contact Person',
                    'rules' => 'required|trim',
                ),
            );
        }

        return $this->form_validation->set_rules($config);
    }
    public function getGroupList($start, $length)
    {
        // Get total count
        $this->db->where('archived', 0);
        $num = $this->db->count_all_results('lib_groups');

        // Get Data
        $this->db->select('l1.group_id, l1.name, l1.definition, t1.fullname');
        $this->db->from('lib_groups l1');
        $this->db->join('aauth_users t1', 't1.id = l1.created_by', 'left');
        $this->db->where('l1.archived', 0);

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        // Return in the exact format your controller loop expects: [0] for data, [1] for count
        return array($res, $num);
    }
    // PERMISSIONS MANAGER VIEW
    // =========================================================================================================================================
    public function permission()
    {
        $data = array(
            'csrf' => $this->csrf(),
            'csrf_ajax' => $this->csrf_ajax(),
            'fullname' => $this->session->fullname
        );

        $data['notif'] = 1;
        $data['config'] = 1;
        $data['ondue'] = 1;

        $this->load->view('header', $data);
        // Sinunod ang singular folder name 'permission' base sa VS Code screenshot mo
        $this->load->view('libraries/permission/listview', $data);
        $this->load->view('libraries/permission/modal_add', $data);
        $this->load->view('libraries/permission/modal_update', $data);
        $this->load->view('footer');
    }

    // AJAX GET PERMISSIONS
    // =========================================================================================================================================
    public function getAllPermissions()
    {
        // 1. Get parameters from DataTables
        $draw   = intval($this->input->get("draw"));
        $start  = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $l_model = new LibraryModel();
        $results = $l_model->getPermissionList($start, $length);

        $data = array();

        // 2. Build the data array
        foreach ($results[0] as $r) {
            $btn_update = '<button type="button" class="btn btn-sm btn-primary btnUpdate"><i class="fas fa-edit"></i></button> ';
            $btn_delete = '<button type="button" class="btn btn-sm btn-danger btnDel"><i class="fas fa-trash"></i></button>';

            $data[] = array(
                'id'         => $r->id,
                'name'       => $r->permission_name, // Must match { data: 'name' } in JS
                'encoded_by' => $r->encoded_by,      // Must match { data: 'encoded_by' } in JS
                'actions'    => $btn_update . $btn_delete
            );
        }

        // 3. The Output (Crucial for DataTables)
        $output = array(
            "draw"            => $draw,
            "recordsTotal"    => $results[1],
            "recordsFiltered" => $results[1],
            "data"            => $data
        );

        echo json_encode($output);
        exit(); // Ensure no extra whitespace/HTML is appended
    }
    // SAVE PERMISSION
    public function savePermission()
    {
        $l_model = new LibraryModel();
        $perm_data = array(
            'permission_name' => htmlentities($this->input->post('name')),
            'encoded_by'      => $this->session->userID,
            'date_encoded'    => date("Y-m-d H:i:s"),
            'archived'        => 0
        );

        $this->validatePermission('createPerm');

        if ($this->form_validation->run()) {
            $res = $l_model->savePermission($perm_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created permission.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create permission.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }
        redirect('Libraries/permission'); // Fixed: Added 's' to match function name
    }

    // UPDATE PERMISSION
    // UPDATE PERMISSION
    // =========================================================================================================================================
    public function updatePermission()
    {
        $l_model = new LibraryModel();

        // 1. Alisin ang 'definition' dito dahil wala itong paglalagyan sa database
        $perm_data = array(
            'permission_name'    => htmlentities($this->input->post('name_u')),
            'modified_by'        => $this->session->userID,
            'date_last_modified' => date("Y-m-d H:i:s")
        );

        $param = array('id' => $this->input->post('id'));

        $this->validatePermission('updatePerm');

        if ($this->form_validation->run()) {
            // 2. Siguraduhin na ang updatePermission sa model ay tumatanggap ng $perm_data at $param
            $l_model->updatePermission($perm_data, $param);
            $this->session->set_flashdata('success', 'Permission updated successfully.');
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }

        // 3. Siguraduhin na 'permission' (singular) ang redirect kung iyon ang route mo
        redirect('Libraries/permission');
    }
    public function deletePermission()
    {
        // Dahil AJAX ito, hindi na natin kailangan ang flashdata/redirect logic
        $l_model = new LibraryModel();

        $perm_data = array(
            'modified_by'        => $this->session->userID,
            'date_last_modified' => date("Y-m-d H:i:s"),
            'archived'           => 1
        );

        $param = array('id' => $this->input->post('id'));

        // I-update ang record
        $res = $l_model->updatePermission($perm_data, $param);

        // Magsend ng JSON response pabalik sa AJAX
        header('Content-Type: application/json'); // Siguraduhin na JSON ang format
        if ($res) {
            echo json_encode(['status' => 'success', 'message' => 'Successfully deleted permission.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete permission.']);
        }
        exit(); // Napaka-importante nito para hindi sumama ang ibang HTML
    }

    // VALIDATE PERMISSION
    protected function validatePermission($selector)
    {
        if ($selector == 'createPerm') {
            $config = array(
                array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
               
            );
        } else if ($selector == 'updatePerm') {
            $config = array(
                array('field' => 'id', 'label' => 'ID', 'rules' => 'required'),
                array('field' => 'name_u', 'label' => 'Name', 'rules' => 'required'),
           
            );
        }
        return $this->form_validation->set_rules($config);
    }
    // GROUP LIST
    // =========================================================================================================================================
    // GROUPS VIEW
    // =========================================================================================================================================
    public function group()
    {
        $data = array(
            'csrf'      => $this->csrf(),
            'csrf_ajax' => $this->csrf_ajax(),
            'fullname'  => $this->session->fullname
        );

        $data['notif']  = 1;
        $data['config'] = 1;
        $data['ondue']  = 1;

        $this->load->view('header', $data);
        $this->load->view('libraries/group/listview');
        $this->load->view('footer');
    }

    // GROUP LIST
    // =========================================================================================================================================
    public function getAllGroups()
    {
        $draw   = intval($this->input->get("draw"));
        $start  = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $l_model = new LibraryModel();
        $results = $l_model->getGroupList($start, $length);

        $data = array();

        foreach ($results[0] as $r) {
            $btn_permissions = '<a href="' . base_url('GroupPermissions/manage_permissions/' . $r->id) . '" class="btn btn-sm btn-success" title="Manage Permissions"><i class="fas fa-key"></i></a> ';
            $btn_update      = '<button type="button" class="btn btn-sm btn-primary btnUpdate"><i class="fas fa-edit"></i></button> ';
            $btn_delete      = '<button type="button" class="btn btn-sm btn-danger btnDel"><i class="fas fa-trash"></i></button>';

            $data[] = array(
                'id'          => $r->id,
                'name'        => $r->name,
                'definition'  => $r->definition,
                'encoded_by'  => $r->encoded_by,
                'modified_by' => $r->modified_by,
                'status'      => $r->archived,   // 0 for Active, 1 for Archived
                'actions'     => $btn_permissions . $btn_update . $btn_delete
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

    // VALIDATE GROUP
    // =========================================================================================================================================
    protected function validateGroup($selector)
    {
        if ($selector == 'createGroup') {
            $config = array(
                array(
                    'field' => 'name',
                    'label' => 'Group Name',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'definition',
                    'label' => 'Definition',
                    'rules' => 'required'
                )
            );
        } elseif ($selector == 'updateGroup') {
            $config = array(
                array(
                    'field' => 'id',
                    'label' => 'Group ID',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'name_u',
                    'label' => 'Group Name',
                    'rules' => 'required'
                ),
                array(
                    'field' => 'definition_u',
                    'label' => 'Definition',
                    'rules' => 'required'
                )
            );
        }

        return $this->form_validation->set_rules($config);
    }

    // SAVE GROUP
    // =========================================================================================================================================
    public function saveGroup()
    {
        $l_model = new LibraryModel();

        $group_data = array(
            'name'         => htmlentities($this->input->post('name')),
            'definition'   => htmlentities($this->input->post('definition')),
            'encoded_by'   => $this->session->userID,
            'date_encoded' => date("Y-m-d H:i:s"),
            'archived'     => 0
        );

        $this->validateGroup('createGroup');

        if ($this->form_validation->run()) {
            $res = $l_model->saveGroup($group_data);
            if ($res > 0) {
                $this->session->set_flashdata('success', 'Successfully created group.');
            } else {
                $this->session->set_flashdata('fail', 'Failed to create group.');
            }
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }

        redirect('Libraries/group');
    }

    // UPDATE GROUP
    // =========================================================================================================================================
    public function updateGroup()
    {
        $l_model = new LibraryModel();

        $group_data = array(
            'name'               => htmlentities($this->input->post('name_u')),
            'definition'         => htmlentities($this->input->post('definition_u')),
            'modified_by'        => $this->session->userID,
            'date_last_modified' => date("Y-m-d H:i:s")
        );

        $param = array(
            'id' => htmlentities($this->input->post('id'))
        );

        $this->validateGroup('updateGroup');

        if ($this->form_validation->run()) {
            $l_model->updateGroup($group_data, $param);
            $this->session->set_flashdata('success', 'Group updated successfully.');
        } else {
            $this->session->set_flashdata('fail', validation_errors());
        }

        redirect('Libraries/group');
    }

    // DELETE GROUP
    // =========================================================================================================================================
    public function deleteGroup()
    {
        $l_model = new LibraryModel();

        $group_data = array(
            'modified_by'        => $this->session->userID,
            'date_last_modified' => date("Y-m-d H:i:s"),
            'archived'           => 1
        );

        $param = array(
            'id' => htmlentities($this->input->post('id'))
        );

        $res = $l_model->updateGroup($group_data, $param);

        echo json_encode(['success' => $res > 0]);
        exit();
    }
}
