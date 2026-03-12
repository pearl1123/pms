<?php

defined('BASEPATH') or exit('No direct script access allowed');

class UserManagement extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_Model');
        $this->load->library('aauth');
    }

    public function index()
    {
        $data = [
            'fullname' => $this->session->userdata('fullname'),
            'offices' => $this->User_Model->get_offices(),
            'groups'   => $this->User_Model->get_groups(),
            'notif' => 1,
            'confi' => 1,
            'ondue' => 1
        ];

        $this->load->view('header', $data);
        $this->load->view('user/UserList', $data);
        $this->load->view('footer');
    }

    public function getUsersAjax()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $search = $this->input->get("search")['value'];

        // Get data from model
        $users = $this->User_Model->get_users_datatable($start, $length, $search);
        $recordsTotal = $this->User_Model->count_all_users();
        $recordsFiltered = $this->User_Model->count_filtered_users($search);

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'group_name' => $user->group_name ?? '',
                'banned' => $user->banned,
                'actions' => '<button class="btn btn-sm btn-primary btnEdit">Edit</button>
                          <button class="btn btn-sm btn-danger btnDelete">Delete</button>'
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function edit($id)
    {
        $data['user'] = $this->User_Model->get_user($id);
        $data['groups'] = $this->User_Model->get_groups();
        $data['offices'] = $this->User_Model->get_offices();

        $this->load->view('user/UpdateUser', $data);
    }

    public function update()
    {
        $this->User_Model->update_user($this->input->post());
        $this->session->set_flashdata('success', 'User updated successfully');
        redirect('UserManagement');
    }

    public function toggle_status($id)
    {
        $this->User_Model->toggle_status($id);
        $this->session->set_flashdata('success', 'User status updated');
        redirect('UserManagement');
    }

    public function delete($id)
    {
        $this->User_Model->delete_user($id);
        $this->session->set_flashdata('success', 'User deleted');
        redirect('UserManagement');
    }

    public function saveUser()
    {
        log_message('debug', '=== REGISTRATION START ===');
        log_message('debug', 'POST Data: ' . print_r($this->input->post(), true));

        // Check if form is actually being submitted
        if (!$this->input->post()) {
            log_message('debug', 'No POST data received');
            $this->session->set_flashdata('fail', 'No form data received.');
            redirect('UserManagement');
            return;
        }

        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules('fullname', 'Full Name', 'required|trim');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'required|trim');
        $this->form_validation->set_rules('office', 'Office', 'required|integer');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_pass', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            log_message('debug', 'Validation failed: ' . $errors);
            $this->session->set_flashdata('fail', $errors);
            $this->session->set_flashdata('old_input', $this->input->post());
            redirect('UserManagement');
            return;
        }

        log_message('debug', 'Form validation passed');

        // Check captcha
        $captcha = $this->input->post('captcha');
        log_message('debug', 'Captcha input: ' . $captcha);
        log_message('debug', 'Session captcha: ' . $this->session->captchaCode);

        if ($captcha !== $this->session->captchaCode) {
            log_message('debug', 'Captcha mismatch');
            $this->session->set_flashdata('fail', 'Captcha does not match.');
            $this->session->set_flashdata('old_input', $this->input->post());
            redirect('UserManagement/index');
            return;
        }

        log_message('debug', 'Captcha validation passed');

        // Prepare data
        $data = [
            'fullname'      => html_escape($this->input->post('fullname')),
            'phone_number'  => html_escape($this->input->post('phone_number')),
            'office'        => (int) $this->input->post('office'),
            // 'ward'          => $this->input->post('ward') ? (int) $this->input->post('ward') : null,
            'email'         => html_escape($this->input->post('email')),
            'password'      => $this->input->post('password')
        ];

        log_message('debug', 'Prepared data for user creation: ' . print_r($data, true));

        try {
            // Create user
            $user_data = $this->aauth->create_user(
                $data['email'],
                $data['password'],
                $data['fullname'],
                $data['phone_number'],
                $data['office'],
                // $data['ward'],
                null
            );

            if ($user_data) {
                log_message('debug', 'User created successfully: ' . print_r($user_data, true));

                $token = time();
                $aModel = new AuthModel();
                $aModel->insertTokenEmail($token, $data['email']);

                // Email notification code here...

                $this->session->set_flashdata('success', 'Registration successful! Please check your email for verification.');
                redirect('UserManagement');
            } else {
                $errors = $this->aauth->print_errors();
                log_message('debug', 'AAuth user creation failed: ' . $errors);
                $this->session->set_flashdata('fail', $errors);
                $this->session->set_flashdata('old_input', $data);
                redirect('UserManagement');
            }
        } catch (Exception $e) {
            log_message('error', 'Registration exception: ' . $e->getMessage());
            $this->session->set_flashdata('fail', 'System error: ' . $e->getMessage());
            $this->session->set_flashdata('old_input', $data);
            redirect('UserManagement');
        }
    }
}
