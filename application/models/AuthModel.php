<?php
#by Pearlsss 01312025

class AuthModel extends CI_Model
{

    public function getUserDetails($email)
    {
        return $this->db->get_where("aauth_users", array("email" => $email))->row();
    }

    public function validateEmail($email)
    {
        return $this->db->get_where("aauth_users", array("email" => $email))->num_rows();
    }

    public function getAllGroupsByUID($uid)
    {
        return $this->db->get_where("aauth_user_to_group", array("user_id" => $uid))->result();
    }

    public function insertTokenEmail($token, $email)
    {
        $data = array(
            'email_verify_token' => $token,
            'email_verify' => 0,
            'email_verify_req_date' => date("Y-m-d H:i:s"),
            'token_date_start' => date("Y-m-d H:i:s")
        );
        $this->db->update('aauth_users', $data, array('email' => $email));
        return $this->db->affected_rows();
    }

    public function check_exist_token_email($token)
    {
        $q = $this->db->get_where('aauth_users', array('email_verify_token' => $token));
        return $q->num_rows();
    }

    public function check_date_start_token($token)
    {
        $q = $this->db->get_where('aauth_users', array('email_verify_token' => $token));
        return $q->row();
    }

    public function update_email_verify($token)
    {
        $data = array(
            'email_verify_token' => '',
            'email_verify' => 1,
            'email_verify_date' => date("Y-m-d H:i:s")
        );
        $this->db->update('aauth_users', $data, array('email_verify_token' => $token));
        return $this->db->affected_rows();
    }

    public function check_email($email)
    {
        $this->db->select('email');
        return $this->db->get_where('aauth_users', array('email' => $email))->row();
    }

    public function reset_password_email($email, $password)
    {
        $data = array(
            'pass' => password_hash($password, PASSWORD_DEFAULT),
        );
        $this->db->update('aauth_users', $data, array('email' => $email));
        return $this->db->affected_rows();
    }

    public function deleteLoginAttempts($email)
    {
        $this->db->delete('aauth_login_attempts', array('ip_address' => $email));
    }

    public function get_active_user_by_email($email)
    {
        $this->db->where('email', $email);
        $this->db->where('DELETED', 0);
        $this->db->where('banned', 0);
        return $this->db->get('aauth_users')->row();
    }
}
