<?php
/* Modified by Gemini - 2026 Refactor */

defined('BASEPATH') or exit('No direct script access allowed');

class GroupPermissions extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Load models and libraries once
        $this->load->library("aauth");
        $this->load->model('GroupPermissionModel');
        $this->load->library('session');

        // Session validation
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('fail', 'Session expired. Please Sign in');
            redirect('User/index');
        }
    }

    // --- CSRF HELPERS ---

    private function csrf()
    {
        return '<input type="hidden" name="' . $this->security->get_csrf_token_name() . '" value="' . $this->security->get_csrf_hash() . '">';
    }

    private function csrf_ajax()
    {
        return array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
    }

    // --- MAIN METHODS ---

    /**
     * View Permission Manager for a specific group
     */
    public function manage_permissions($group_id)
    {
        $group_details = $this->GroupPermissionModel->getGroupById($group_id);

        if (!$group_details) {
            $this->session->set_flashdata('fail', 'Group not found.');
            redirect('Libraries/group');
        }

        $data = array(
            'csrf'          => $this->csrf(),
            'csrf_ajax'     => $this->csrf_ajax(),
            'fullname'      => $this->session->userdata('fullname'),
            'group_details' => $group_details,
            'notif'         => 1,
            'config'        => 1,
            'ondue'         => 1
        );

        $this->load->view('header', $data);
        // BINAGO: Mula 'libraries/group/permissions_view' tungo sa 'user/permissions'
        $this->load->view('user/permissions', $data);
        $this->load->view('footer');
    }
    /**
     * AJAX: Get permissions list for DataTables
     */
    public function get_permissions($group_id)
    {
        // Validate group_id
        if (!is_numeric($group_id)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid group ID']));
        }

        $results = $this->GroupPermissionModel->getPermissionsByGroup($group_id);
        $data = array();

        foreach ($results as $r) {
            $sub_id = $r->sub_module_id;

            $data[] = array(
                'main_module' => $r->main_module_name,
                'sub_module'  => '<span class="sub-indent">' . $r->sub_module_name . '</span>',
                'check_all'   => '<input type="checkbox" class="check-submodule" data-sub="' . $sub_id . '">',
                'view'        => $this->_render_cb($group_id, $sub_id, 'view',   $r->can_view),
                'add'         => $this->_render_cb($group_id, $sub_id, 'add',    $r->can_add),
                'edit'        => $this->_render_cb($group_id, $sub_id, 'edit',   $r->can_edit),
                'delete'      => $this->_render_cb($group_id, $sub_id, 'delete', $r->can_delete),
                'extra'       => '<small class="text-muted">N/A</small>'
            );
        }

        // draw must come from input->get, default to 1
        $draw = $this->input->get('draw') ? intval($this->input->get('draw')) : 1;

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "draw"            => $draw,
                "recordsTotal"    => count($data),
                "recordsFiltered" => count($data),
                "data"            => $data
            ]));
    }

    public function updatePermToGroup()
    {
        $group_id   = $this->input->post('group_id');
        $perm_type  = $this->input->post('perm_id'); // column: view, add, edit, delete
        $is_checked = $this->input->post('checked');
        $sub_id     = $this->input->post('sub_id');

        // Update logic via model
        $res = $this->GroupPermissionModel->updateGroupPermission($group_id, $sub_id, $perm_type, $is_checked);

        if ($res) {
            $response = ['status' => 'success', 'message' => 'Updated successfully'];
        } else {
            $this->output->set_status_header(500);
            $response = ['status' => 'error', 'message' => 'Database update failed'];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }


    private function _render_cb($group_id, $sub_id, $perm_type, $is_checked)
    {
        $checked = ($is_checked == 1) ? 'checked' : '';
        return '<input type="checkbox" class="perm-toggle" 
                    data-group="' . $group_id . '" 
                    data-sub="' . $sub_id . '" 
                    data-perm="' . $perm_type . '" 
                    ' . $checked . '>';
    }
}
