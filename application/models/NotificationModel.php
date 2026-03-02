<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NotificationModel extends CI_Model {

    public function getAllNotifications()
    {
        // Example: change table/columns to your DB structure
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('tbl_notifications')->result();
    }

    public function getOnDueNotifications()
    {
        // Example filter for "on due"
        $this->db->where('due_date <=', date('Y-m-d'));
        return $this->db->get('tbl_notifications')->result();
    }
}
