<?php
/*by Pearlsss 02072025*/
class LibraryModel extends CI_Model
{
    private $isms_db;

    public function __construct()
    {
        parent::__construct();
        $this->isms_db = $this->load->database('isms', TRUE);
    }

    // GET ATTACHMENT LIST
    // =========================================================================================================================================
    public function getAttachmentListView($start, $length)
    {
        $this->db->select('l1.attachment_id');
        $this->db->where(array('archived' => 0));
        $num = $this->db->get('lib_attachments l1')->num_rows();

        $this->db->select('l1.attachment_id, l1.attachment_name, t1.fullname');
        $this->db->where(array('archived' => 0));
        if ($length > 0) {
            $this->db->limit($length, $start);
        }
        $this->db->join('aauth_users t1', 't1.id = l1.created_by', 'left');
        $res = $this->db->get('lib_attachments l1')->result();

        return array($res, $num);
    }

    // GET PROCUREMENT MODE LIST
    // =========================================================================================================================================
    public function getAttachmentList()
    {
        
    }

    // SAVE ATTACHMENT
    // =========================================================================================================================================
    public function saveAttachment($attachment_data)
    {
        $this->db->insert('lib_attachments', $attachment_data);
        return $this->db->insert_id();
    }

    // UPDATE ATTACHMENT
    // =========================================================================================================================================
    public function updateAttachment($attachment_data, $param)
    {
        $this->db->update('lib_attachments', $attachment_data, $param);
        return $this->db->affected_rows();
    }

    // GET ITEM LIST
    // =========================================================================================================================================
    public function getItemList($start, $length)
    {
        $this->db->select('l1.item_id');
        $this->db->where(array('archived' => 0));
        $num = $this->db->get('lib_item l1')->num_rows();

        $this->db->select('l1.item_id, l1.item_description, t1.fullname');
        $this->db->where(array('archived' => 0));
        if ($length > 0) {
            $this->db->limit($length, $start);
        }
        $this->db->join('aauth_users t1', 't1.id = l1.created_by', 'left');
        $res = $this->db->get('lib_item l1')->result();

        return array($res, $num);
    }

    // SAVE ITEM
    // =========================================================================================================================================
    public function saveItem($item_data)
    {
        $this->db->insert('lib_item', $item_data);
        return $this->db->insert_id();
    }

    // UPDATE ITEM
    // =========================================================================================================================================
    public function updateItem($item_data, $param)
    {
        $this->db->update('lib_item', $item_data, $param);
        return $this->db->affected_rows();
    }

    // GET OFFICE
    // =========================================================================================================================================
    public function getOfficeById($office_id)
    {
        $this->db->where('office_id', $office_id);
        return $this->db->get('lib_office')->row();
    }

    // GET OFFICE LIST
    // =========================================================================================================================================
    public function getOfficeList($start, $length)
    {
        $this->db->select('l1.office_id');
        $this->db->where(array('archived' => 0));
        $num = $this->db->get('lib_office l1')->num_rows();

        $this->db->select('l1.office_id, l1.office_desc, l1.office_abbr, t1.fullname');
        $this->db->where(array('archived' => 0));
        if ($length > 0) {
            $this->db->limit($length, $start);
        }
        $this->db->join('aauth_users t1', 't1.id = l1.created_by', 'left');
        $res = $this->db->get('lib_office l1')->result();

        return array($res, $num);
    }

    // SAVE OFFICE
    // =========================================================================================================================================
    public function saveOffice($office_data)
    {
        $this->db->insert('lib_office', $office_data);
        return $this->db->insert_id();
    }

    // UPDATE OFFICE
    // =========================================================================================================================================
    public function updateOffice($office_data, $param)
    {
        $this->db->update('lib_office', $office_data, $param);
        return $this->db->affected_rows();
    }




    // GET ITEM STOCK
    // =========================================================================================================================================
    public function getActiveItems()
    {
        return $this->db->where('archived', 0)->get('lib_item')->result();
    }

    // GET UNIT STOCK
    // =========================================================================================================================================   
    public function getActiveUnits()
    {
        return $this->db->where('archived', 0)->get('lib_unit')->result();
    }

    // GET STOCK
    // =========================================================================================================================================
    public function getStockById($stock_id)
    {
        // return $this->db->where('stock_id', $stock_id)->get('lib_stocks')->row();
        $this->db->select('
            s.stock_id, 
            s.item_id, 
            s.unit_id, 
            s.stock_onhand, 
            s.item_source,
            u.unit_code, 
            u2.fullname,
            i.item_description
        ');
        $this->db->from('lib_stocks s');
        $this->db->join('lib_unit u', 'u.unit_id = s.unit_id', 'left');
        $this->db->join('aauth_users u2', 'u2.id = s.created_by', 'left');
        $this->db->join('lib_item i', 'i.item_id = s.item_id AND s.item_source = "library"', 'left');
        $this->db->where('s.archived', 0);
        $this->db->where('stock_id', $stock_id);
        $query = $this->db->get();

        return $query->row();
    }

    // GET STOCK LIST
    // =========================================================================================================================================
    public function getStockList($start, $length, $search = '', $order_column = 's.stock_id', $order_dir = 'ASC')
    {
        $this->db->from('lib_stocks s');
        $this->db->where('s.archived', 0);
        $total = $this->db->count_all_results();

        $this->db->select('
            s.stock_id, 
            s.item_id, 
            s.unit_id, 
            s.stock_onhand, 
            s.item_source,
            u.unit_code, 
            u2.fullname,
            i.item_description
        ');
        $this->db->from('lib_stocks s');
        $this->db->join('lib_unit u', 'u.unit_id = s.unit_id', 'left');
        $this->db->join('aauth_users u2', 'u2.id = s.created_by', 'left');
        $this->db->join('lib_item i', 'i.item_id = s.item_id AND s.item_source = "library"', 'left');
        $this->db->where('s.archived', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('i.item_description', $search);
            $this->db->or_like('u.unit_code', $search);
            $this->db->or_like('s.stock_onhand', $search);
            $this->db->group_end();
        }

        $allowed_columns = ['s.stock_id', 'i.item_description', 'u.unit_code', 's.stock_onhand', 'u2.fullname'];
        if (!in_array($order_column, $allowed_columns)) {
            $order_column = 's.stock_id';
        }
        $order_dir = strtoupper($order_dir) === 'DESC' ? 'DESC' : 'ASC';
        $this->db->order_by($order_column, $order_dir);
        $this->db->limit($length, $start);

        $results = $this->db->get()->result();

        $filtered_count = $total;
        if (!empty($search)) {
            $this->db->from('lib_stocks s');
            $this->db->join('lib_item i', 'i.item_id = s.item_id', 'left');
            $this->db->join('lib_unit u', 'u.unit_id = s.unit_id', 'left');
            $this->db->where('s.archived', 0);
            $this->db->group_start();
            $this->db->like('i.item_description', $search);
            $this->db->or_like('u.unit_code', $search);
            $this->db->or_like('s.stock_onhand', $search);
            $this->db->group_end();
            $filtered_count = $this->db->count_all_results();
        }

        return [$results, $total, $filtered_count];
    }

    // SAVE STOCK
    // =========================================================================================================================================
    public function saveStock($data)
    {
        $this->db->insert('lib_stocks', $data);
        return $this->db->insert_id();
    }

    // UPDATE STOCK
    // =========================================================================================================================================
    public function updateStock($data, $param)
    {
        $this->db->where($param);
        $this->db->update('lib_stocks', $data);
        return $this->db->affected_rows();
    }

    // GET UNIT LIST
    // =========================================================================================================================================
    public function getUnitList($start, $length, $search = '', $orderColumn = 'unit_code', $orderDir = 'asc')
    {
        $this->db->from('lib_unit u');
        $this->db->where('u.archived', 0);
        $total = $this->db->count_all_results();

        $this->db->select('u.unit_id, u.unit_code, u.unit_description, u2.fullname');
        $this->db->from('lib_unit u');
        $this->db->join('aauth_users u2', 'u2.id = u.created_by', 'left');
        $this->db->where('u.archived', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('u.unit_code', $search);
            $this->db->or_like('u.unit_description', $search);
            $this->db->or_like('u2.fullname', $search);
            $this->db->group_end();
        }

        $filteredCount = $this->db->count_all_results('', false);

        $this->db->order_by($orderColumn, $orderDir);
        $this->db->limit($length, $start);

        $results = $this->db->get()->result();

        return [$results, ['total' => $total, 'filtered' => $filteredCount]];
    }

    // SAVE UNIT
    // =========================================================================================================================================
    public function saveUnit($data)
    {
        $this->db->insert('lib_unit', $data);
        return $this->db->insert_id();
    }

    // UPDATE UNIT
    // =========================================================================================================================================
    public function updateUnit($data, $param)
    {
        $this->db->where($param);
        $this->db->update('lib_unit', $data);
        return $this->db->affected_rows();
    }


    // GET PROCUREMENT MODE LIST
    // =========================================================================================================================================
    public function getModeListView($start, $length)
    {
        $this->db->select('l1.proc_id');
        $this->db->where(array('archived' => 0));
        $num = $this->db->get('lib_procurement_mode l1')->num_rows();

        $this->db->select('l1.proc_id, l1.proc_code, l1.proc_name, t1.fullname');
        $this->db->where(array('archived' => 0));
        if ($length > 0) {
            $this->db->limit($length, $start);
        }
        $this->db->join('aauth_users t1', 't1.id = l1.created_by', 'left');
        $res = $this->db->get('lib_procurement_mode l1')->result();

        return array($res, $num);
    }

    // SAVE PROCURMENT MODE
    // =========================================================================================================================================
    public function saveMode($mode_data)
    {
        $this->db->insert('lib_procurement_mode', $mode_data);
        return $this->db->insert_id();
    }

    // UPDATE PROCURMENT MODE
    // =========================================================================================================================================
    public function updateMode($mode_data, $param)
    {
        $this->db->update('lib_procurement_mode', $mode_data, $param);
        return $this->db->affected_rows();
    }

    // GET PROCUREMENT SETTINGS LIST
    // =========================================================================================================================================
    public function getSettingsListView($start, $length)
    {
        $this->db->select('l1.proc_id');
        $this->db->where(array('archived' => 0));
        $num = $this->db->get('lib_procurement_mode l1')->num_rows();

        $this->db->select('l1.proc_id, l1.proc_code, l1.proc_name, t1.fullname');
        $this->db->where(array('archived' => 0));
        if($length > 0) {
            $this->db->limit($length, $start);
        }
        $this->db->join('aauth_users t1', 't1.id = l1.created_by', 'left');
        $res = $this->db->get('lib_procurement_mode l1')->result();

        return array($res, $num);
    }

    // GET PROCUREMENT SETTINGS UPDATE
    // =========================================================================================================================================
    public function getSettingsUpdateView($id)
    {
        $this->db->select('l1.attachment_id, l1.attachment_name');
        $this->db->where(array('archived' => 0));
        $res = $this->db->get('lib_attachments l1')->result();

        return $res;
    }




    public function getAllPermissionPerGroupModule($group_id)
    {
        $sql = "SELECT 
    g.id   AS group_id,
    g.name AS group_name,

    main.name AS main_module,
    sub.id   AS sub_id,
    sub.name AS sub_module,
    sub.extra_permissions,   -- ✅ bring in your extra column

    pview.id   AS view_perm_id,
    IF(gv.perm_id IS NULL, 0, 1) AS has_view,

    padd.id    AS add_perm_id,
    IF(ga.perm_id IS NULL, 0, 1) AS has_add,

    pedit.id   AS edit_perm_id,
    IF(ge.perm_id IS NULL, 0, 1) AS has_edit,

    pdelete.id AS delete_perm_id,
    IF(gd.perm_id IS NULL, 0, 1) AS has_delete

FROM aauth_groups g
JOIN aauth_perm_module_sub sub 
    ON 1=1
JOIN aauth_perm_module_main main 
    ON main.id = sub.module_main_id

LEFT JOIN aauth_perms pview   
    ON pview.name = sub.view
LEFT JOIN aauth_perms padd    
    ON padd.name = sub.add
LEFT JOIN aauth_perms pedit   
    ON pedit.name = sub.edit
LEFT JOIN aauth_perms pdelete 
    ON pdelete.name = sub.delete

LEFT JOIN aauth_perm_to_group gv 
    ON gv.perm_id = pview.id   
   AND gv.group_id = g.id
LEFT JOIN aauth_perm_to_group ga 
    ON ga.perm_id = padd.id    
   AND ga.group_id = g.id
LEFT JOIN aauth_perm_to_group ge 
    ON ge.perm_id = pedit.id   
   AND ge.group_id = g.id
LEFT JOIN aauth_perm_to_group gd 
    ON gd.perm_id = pdelete.id 
   AND gd.group_id = g.id

WHERE g.id = ?
  AND g.archived = 0
ORDER BY sub.id ASC

        ";

        $query = $this->db->query($sql, [$group_id]);
        return $query->result();
    }

    public function updatePermissions($group_id, $perm_id)
    {
        //check perm_id and group_id is exist
        $this->db->where('group_id', $group_id);
        $this->db->where('perm_id', $perm_id);
        $query = $this->db->get('aauth_perm_to_group');
        if ($query->num_rows() > 0) {
            // If exists, delete the record (revoke permission)
            $this->db->where('group_id', $group_id);
            $this->db->where('perm_id', $perm_id);
            $this->db->delete('aauth_perm_to_group');
            return 'revoked';
        } else {
            // If not exists, insert the record (grant permission)
            $data = array(
                'group_id' => $group_id,
                'perm_id' => $perm_id
            );
            $this->db->insert('aauth_perm_to_group', $data);
            return 'granted';
        }
    }
}
