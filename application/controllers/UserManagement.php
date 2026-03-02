<?php

defined('BASEPATH') or exit('No direct script access allowed');

class UserManagement extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_Model');
    }

    public function index()
    {
        $data = [
            'fullname' => $this->session->userdata('fullname'),
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
}
