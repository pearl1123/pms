<?php
class MY_Controller extends CI_Controller {
    public $data = [];

    public function __construct(){
        parent::__construct();

        if ($this->session->userdata('logged_in')) {
            $this->data['fullname'] = $this->session->userdata('fullname');
            $this->data['user_id'] = $this->session->userdata('userID');
        }
    }

    public function render_view($view, $data = []) {
        $data = array_merge($this->data, $data); // merge global data
        $this->load->view($view, $data);
    }
}
