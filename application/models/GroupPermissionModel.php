<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GroupPermissionModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getGroupList($start, $length)
    {
        $this->db->select('g.id, g.name, g.definition, g.archived, u1.fullname AS encoded_by, g.date_encoded, u2.fullname AS modified_by, g.date_last_modified');
        $this->db->from('aauth_groups g');
        $this->db->join('aauth_users u1', 'u1.id = g.encoded_by', 'left');
        $this->db->join('aauth_users u2', 'u2.id = g.modified_by', 'left');
        $this->db->where('g.archived', 0);
        $this->db->limit($length, $start);

        $query = $this->db->get();

        $this->db->where('archived', 0);
        $total_count = $this->db->count_all_results('aauth_groups');

        return array($query->result(), $total_count);
    }

    public function getGroupById($id)
    {
        return $this->db->get_where('aauth_groups', ['id' => $id, 'archived' => 0])->row();
    }

    public function saveGroup($group_data)
    {
        $this->db->insert('aauth_groups', $group_data);
        return $this->db->insert_id();
    }

    public function updateGroup($group_data, $param)
    {
        $this->db->update('aauth_groups', $group_data, $param);
        return $this->db->affected_rows();
    }


    public function getPermissionsByGroup($group_id)
    {
        $this->db->select('
            sm.id as sub_module_id,
            sm.sub_module_name,
            mm.main_module_name,
            IFNULL(gp.can_view, 0) as can_view,
            IFNULL(gp.can_add, 0) as can_add,
            IFNULL(gp.can_edit, 0) as can_edit,
            IFNULL(gp.can_delete, 0) as can_delete
        ');
        $this->db->from('lib_sub_modules sm');
        $this->db->join('lib_main_modules mm', 'mm.id = sm.main_module_id', 'left');
        
        $this->db->join('aauth_group_permissions gp', 'gp.sub_module_id = sm.id AND gp.group_id = ' . $this->db->escape($group_id), 'left');
        
        $this->db->order_by('mm.main_module_name', 'ASC');
        $this->db->order_by('sm.sub_module_name', 'ASC');

        return $this->db->get()->result();
    }

    public function updateGroupPermission($group_id, $sub_id, $column, $value)
    {
        
        $allowed_columns = ['view', 'add', 'edit', 'delete'];
        if (!in_array($column, $allowed_columns)) return false;

        $db_column = 'can_' . $column; 

        
        $exists = $this->db->get_where('aauth_group_permissions', [
            'group_id' => $group_id,
            'sub_module_id' => $sub_id
        ])->row();

        if ($exists) {
           
            $this->db->where(['group_id' => $group_id, 'sub_module_id' => $sub_id]);
            return $this->db->update('aauth_group_permissions', [$db_column => $value]);
        } else {
           
            $data = [
                'group_id' => $group_id,
                'sub_module_id' => $sub_id,
                $db_column => $value
            ];
            return $this->db->insert('aauth_group_permissions', $data);
        }
    }
}