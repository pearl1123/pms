<?php
/*by Pearllsss 31012025*/

defined('BASEPATH') or exit('No direct script access allowed');

class Libraries extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        #load all that you need
        $this->load->library("aauth");
        $this->load->model('LibraryModel');
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

        $results = $l_model->getAttachmentList($start, $length);

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

        $results = $l_model->getModeList($start, $length);

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

    // PROCUREMENT MODE LIST VIEW
    // Displays module for mode; Library Module
    // =========================================================================================================================================
    public function procurementSettings()
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
        $this->load->view('libraries/procurementSettings/listview');
        $this->load->view('libraries/procurementSettings/modal_add');
        $this->load->view('libraries/procurementSettings/modal_update');
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
}
