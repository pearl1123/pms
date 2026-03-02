<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load any models needed
        $this->load->model('Notification_model');
    }

    public function notificationList()
    {
        // Get all notifications from the model
        $data['notifications'] = $this->Notification_model->getAllNotifications();

        // Load the view
        $this->load->view('templates/header'); // optional
        $this->load->view('notification/list', $data);
        $this->load->view('templates/footer'); // optional
    }

    public function notificationOnDueList()
    {
        $data['notifications'] = $this->Notification_model->getOnDueNotifications();
        $this->load->view('notification/on_due_list', $data);
    }
}
