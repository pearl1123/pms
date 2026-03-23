<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function insert_user($data)
    {
        $this->db->where('email', $data['email']);
        $this->db->where('DELETED', 0);
        $existing = $this->db->get('aauth_users')->row();

        if ($existing) {
            return false;
        }

        $this->db->insert('aauth_users', [
            'fullname'     => $data['fullname'],
            'phone_number' => $data['phone_number'],
            'office'       => $data['office'],
            'email'        => $data['email'],
            'pass'         => password_hash($data['password'], PASSWORD_DEFAULT),
            'email_verify' => 1,
            'banned'       => 0,
            'DELETED'      => 0,
            'date_created' => date('Y-m-d H:i:s')
        ]);

        return $this->db->insert_id();
    }

    public function get_deleted_user_by_email($email)
    {
        $this->db->where('email', $email);
        $this->db->where('DELETED', 1);
        return $this->db->get('aauth_users')->row();
    }

    public function get_user_by_id($id)
    {
        if (empty($id)) return null;
        $this->db->where('id', $id);
        $query = $this->db->get('aauth_users');
        return ($query && $query->num_rows() > 0) ? $query->row() : null;
    }

    public function get_offices()
    {
        $this->db->where('archived', 0);
        $this->db->order_by('office_desc', 'ASC');
        return $this->db->get('lib_office')->result();
    }

    public function edit($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('aauth_users', $data);
    }

    public function get_all_users()
    {
        $this->db->select('au.id, au.fullname, au.email, au.banned, ag.name as group_name');
        $this->db->from('aauth_users au');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = au.id', 'left');
        $this->db->join('aauth_groups ag', 'ag.id = ug.group_id', 'left');
        $this->db->where('au.DELETED', 0);
        return $this->db->get()->result();
    }

    public function get_user($id)
    {
        $this->db->select('au.*, ug.group_id');
        $this->db->from('aauth_users au');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = au.id', 'left');
        $this->db->where('au.id', $id);
        return $this->db->get()->row();
    }

    public function get_groups()
    {
        return $this->db->get('aauth_groups')->result();
    }

    public function update_user($data)
    {
        $user_id = $data['id'];
        $this->db->update('aauth_users', [
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'banned' => isset($data['banned']) ? 1 : 0
        ], ['id' => $user_id]);

        $this->db->where('user_id', $user_id)->delete('aauth_user_to_group');
        $this->db->insert('aauth_user_to_group', [
            'user_id' => $user_id,
            'group_id' => $data['group_id']
        ]);
    }

    public function toggle_status($id)
    {
        $user = $this->get_user($id);
        $this->db->update('aauth_users', ['banned' => !$user->banned], ['id' => $id]);
    }

    public function delete_user($id)
    {
        $this->db->update('aauth_users', ['DELETED' => 1], ['id' => $id]);
    }

    public function get_users_datatable($start = 0, $length = 10, $search = '')
    {
        $this->db->select('au.id, au.fullname, au.email, au.banned, ag.name as group_name');
        $this->db->from('aauth_users au');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = au.id', 'left');
        $this->db->join('aauth_groups ag', 'ag.id = ug.group_id', 'left');
        $this->db->where('au.DELETED', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('au.fullname', $search);
            $this->db->or_like('au.email', $search);
            $this->db->or_like('ag.name', $search);
            $this->db->group_end();
        }

        $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    public function count_all_users()
    {
        $this->db->from('aauth_users');
        $this->db->where('DELETED', 0);
        return $this->db->count_all_results();
    }

    public function count_filtered_users($search = '')
    {
        $this->db->from('aauth_users au');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = au.id', 'left');
        $this->db->join('aauth_groups ag', 'ag.id = ug.group_id', 'left');
        $this->db->where('au.DELETED', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('au.fullname', $search);
            $this->db->or_like('au.email', $search);
            $this->db->or_like('ag.name', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_wards_by_office($office_id)
    {
        $this->db->select('ward_id, ward_desc, ward_abbr');
        $this->db->from('lib_ward');
        $this->db->where('archived', 0);
        $this->db->where('office_id', $office_id);
        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return []; // return empty array if no records found
        }
    }
}
