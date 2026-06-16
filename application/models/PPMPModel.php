
<?php
    defined('BASEPATH') or exit('No direct script access allowed');

    class PPMPModel extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function getPPMPListView($start, $length)
        {
            $this->db->select('DISTINCT(t1.ppmp_year), t1.ppmp_id');
            $this->db->where(array('t1.archived' => 0));
            $num = $this->db->get('tbl_ppmp t1')->num_rows();

            $this->db->select('DISTINCT(t1.ppmp_year), t1.ppmp_id, t2.fullname');
            $this->db->where(array('t1.archived' => 0));
            if ($length > 0) {
                $this->db->limit($length, $start);
            }
            $this->db->join('aauth_users t2', 't2.id = t1.created_by', 'left');
            $res = $this->db->get('tbl_ppmp t1')->result();

            return array($res, $num);
        }

        public function getPPMPItemListView($start, $length,$year)
        {
            $this->db->select('t1.ppmp_id');
            $this->db->join('tbl_ppmp_items t6', 't6.ppmp_id = t1.ppmp_id AND t6.archived = 0', 'left');
            $this->db->where(array('t1.ppmp_year' => $year));
            $num = $this->db->get('tbl_ppmp t1')->num_rows();

            $this->db->select('t1.ppmp_id, t1.ppmp_year, t6.ppmp_general_description, t7.ppmp_quantity, t6.ppmp_project_type, t7.ppmp_cost, t6.ppmp_pre_proc, t6.ppmp_remarks,
                                t2.unit_code, t3.fund_name, t4.proc_code, t5.fullname');
            $this->db->where(array('t1.archived' => 0));
            if ($length > 0) {
                $this->db->limit($length, $start);
            }
            $this->db->join('tbl_ppmp_project t6', 't6.ppmp_id = t1.ppmp_id AND t6.archived = 0', 'left');
            $this->db->join('tbl_ppmp_items t7', 't7.ppmp_id = t1.ppmp_id AND t6.ppmp_project_id = t7.ppmp_project_id AND t7.archived = 0', 'left');
            $this->db->join('lib_unit t2', 't2.unit_id = t7.unit_id', 'left');
            $this->db->join('lib_funds t3', 't3.fund_id = t6.fund_id', 'left');
            $this->db->join('lib_procurement_mode t4', 't4.proc_id = t6.proc_id', 'left');
            $this->db->join('aauth_users t5', 't5.id = t6.created_by', 'left');
            
            $res = $this->db->get_where('tbl_ppmp t1', 't1.ppmp_year = ' . $this->db->escape($year))->result();

            return array($res, $num);
        }

        public function save_ppmp_projects($params) {
            $post = $params['post'];

            $ppmp_id = $params['ppmp_id'];
            $ppmp_year = $params['ppmp_year'];
            $office_id = $params['office_id'];
            $created_by = $params['created_by'];

            $this->db->trans_begin();

            foreach ($post['ppmp_general_description'] as $project_index => $description) {

                $pre_proc = isset($post['ppmp_pre_proc'][$project_index]) ? 1 : 0;

                $project_data = [
                    'ppmp_id' => $ppmp_id,
                    'ppmp_general_description' => $description,
                    'ppmp_project_type' => $post['ppmp_project_type'][$project_index],
                    'proc_id' => $post['proc_id'][$project_index],
                    'ppmp_pre_proc' => $pre_proc,
                    'ppmp_start_proc' => $post['ppmp_start_proc'][$project_index],
                    'ppmp_end_proc' => $post['ppmp_end_proc'][$project_index],
                    'ppmp_delivery' => $post['ppmp_delivery'][$project_index],
                    'fund_id' => $post['fund_id'][$project_index],
                    'ppmp_budget' => $post['ppmp_budget'][$project_index],
                    'ppmp_class_id' => 1,
                    'ppmp_supporting_docs' => $post['attachment_id'][$project_index],
                    'ppmp_remarks' => $post['ppmp_remarks'][$project_index],
                    'created_by' => $created_by,
                    'date_created' => date('Y-m-d H:i:s'),
                    'office_id' => $office_id,
                    'archived' => 0
                ];

                $this->db->insert('tbl_ppmp_project', $project_data);
                $ppmp_project_id = $this->db->insert_id();

                if (!empty($post['item_id'][$project_index])) {

                    foreach ($post['item_id'][$project_index] as $item_index => $item_id) {

                        $item_data = [
                            'ppmp_id' => $ppmp_id,
                            'ppmp_year' => $ppmp_year,
                            'item_id' => $item_id,
                            'ppmp_classification' => 1,
                            'ppmp_quantity' => $post['ppmp_quantity'][$project_index][$item_index],
                            'unit_id' => $post['unit_id'][$project_index][$item_index],
                            'proc_id' => $post['proc_id'][$project_index],
                            'fund_id' => $post['fund_id'][$project_index],
                            'ppmp_cost' => $post['ppmp_cost'][$project_index][$item_index],
                            'attachment_id' => $post['attachment_id'][$project_index],
                            'created_by' => $created_by,
                            'date_created' => date('Y-m-d H:i:s'),
                            'ppmp_project_id' => $ppmp_project_id,
                            'archived' => 0
                        ];

                        $this->db->insert('tbl_ppmp_items', $item_data);
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return false;
            }

            $this->db->trans_commit();
            return true;
        }

        public function countPPMPProjects($year)
        {
            $this->db->from('tbl_ppmp_project t6');
            $this->db->join('tbl_ppmp t1', 't1.ppmp_id = t6.ppmp_id', 'inner');
            $this->db->where('t1.ppmp_year', $year);
            $this->db->where('t1.archived', 0);
            $this->db->where('t6.archived', 0);

            return $this->db->count_all_results();
        }


        public function countFilteredPPMPProjects($year, $searchValue = '')
        {
            $this->db->from('tbl_ppmp_project t6');
            $this->db->join('tbl_ppmp t1', 't1.ppmp_id = t6.ppmp_id', 'inner');
            $this->db->join('lib_funds t3', 't3.fund_id = t6.fund_id', 'left');
            $this->db->join('lib_procurement_mode t4', 't4.proc_id = t6.proc_id', 'left');
            $this->db->join('aauth_users t5', 't5.id = t6.created_by', 'left');
            $this->db->join('lib_office t9', 't9.office_id = t6.office_id', 'left');

            $this->db->where('t1.ppmp_year', $year);
            $this->db->where('t1.archived', 0);
            $this->db->where('t6.archived', 0);

            if (!empty($searchValue)) {
                $this->db->group_start();
                $this->db->like('t6.ppmp_general_description', $searchValue);
                $this->db->or_like('t4.proc_code', $searchValue);
                $this->db->or_like('t3.fund_name', $searchValue);
                $this->db->or_like('t5.fullname', $searchValue);
                $this->db->or_like('t9.office_abbr', $searchValue);
                $this->db->group_end();
            }

            return $this->db->count_all_results();
        }


        public function getPPMPProjectListView($start, $length, $year, $searchValue = '', $orderColumn = 't6.ppmp_project_id', $orderDir = 'desc')
        {
            $this->db->select('
                t6.ppmp_project_id,
                t6.ppmp_id,
                t1.ppmp_year,
                t6.ppmp_general_description,
                t6.ppmp_project_type,
                t6.proc_id,
                t6.fund_id,
                t6.ppmp_budget,
                t6.ppmp_pre_proc,
                t6.ppmp_remarks,
                t3.fund_name,
                t4.proc_code,
                t5.fullname,
                t9.office_abbr,
            ');

            $this->db->from('tbl_ppmp_project t6');
            $this->db->join('tbl_ppmp t1', 't1.ppmp_id = t6.ppmp_id', 'inner');
            $this->db->join('lib_funds t3', 't3.fund_id = t6.fund_id', 'left');
            $this->db->join('lib_procurement_mode t4', 't4.proc_id = t6.proc_id', 'left');
            $this->db->join('aauth_users t5', 't5.id = t6.created_by', 'left');
            $this->db->join(
                'lib_office t9',
                't9.office_id = t6.office_id',
                'left'
            );
            $this->db->where('t1.ppmp_year', $year);
            $this->db->where('t1.archived', 0);
            $this->db->where('t6.archived', 0);

            if (!empty($searchValue)) {
                $this->db->group_start();
                $this->db->like('t6.ppmp_general_description', $searchValue);
                $this->db->or_like('t4.proc_code', $searchValue);
                $this->db->or_like('t3.fund_name', $searchValue);
                $this->db->or_like('t5.fullname', $searchValue);
                $this->db->or_like('t9.office_name', $searchValue);
                $this->db->group_end();
            }

            $orderDir = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';
            $this->db->order_by($orderColumn, $orderDir);

            if ($length > 0) {
                $this->db->limit($length, $start);
            }

            return $this->db->get()->result();
        }


        public function getItemsByProject($projectId)
        {
            $this->db->select('
                t7.ppmp_item_id,
                t7.item_id,
                t8.item_description,
                t7.ppmp_classification,
                t7.ppmp_quantity,
                t7.ppmp_cost,
                t2.unit_code
            ');

            $this->db->from('tbl_ppmp_items t7');

            $this->db->join(
                'lib_unit t2',
                't2.unit_id = t7.unit_id',
                'left'
            );

            $this->db->join(
                'lib_item t8',
                't8.item_id = t7.item_id',
                'left'
            );

            $this->db->where('t7.ppmp_project_id', $projectId);
            $this->db->where('t7.archived', 0);

            return $this->db->get()->result();
        }

        public function getProjectForEdit($projectId)
        {
            $this->db->select('
                p.ppmp_project_id,
                p.ppmp_id,
                t.ppmp_year,
                p.ppmp_general_description,
                p.ppmp_project_type,
                p.proc_id,
                p.ppmp_pre_proc,
                p.ppmp_start_proc,
                p.ppmp_end_proc,
                p.ppmp_delivery,
                p.fund_id,
                p.ppmp_budget,
                p.ppmp_class_id,
                p.ppmp_supporting_docs AS attachment_id,
                p.ppmp_remarks,
                p.office_id
            ');

            $this->db->from('tbl_ppmp_project p');
            $this->db->join('tbl_ppmp t', 't.ppmp_id = p.ppmp_id', 'left');
            $this->db->where('p.ppmp_project_id', $projectId);
            $this->db->where('p.archived', 0);

            return $this->db->get()->row();
        }


        public function getProjectItemsForEdit($projectId)
        {
            $this->db->select('
                i.ppmp_item_id,
                i.ppmp_id,
                i.ppmp_project_id,
                i.ppmp_year,
                i.item_id,
                i.ppmp_classification,
                i.ppmp_quantity,
                i.unit_id,
                i.proc_id,
                i.fund_id,
                i.ppmp_cost,
                i.ppmp_cost AS ppmp_unit_cost,
                i.attachment_id
            ');

            $this->db->from('tbl_ppmp_items i');
            $this->db->where('i.ppmp_project_id', $projectId);
            $this->db->where('i.archived', 0);
            $this->db->order_by('i.ppmp_item_id', 'asc');

            return $this->db->get()->result();
        }


    public function updateProjectWithItems($post, $userId)
    {
        $projectId = (int) $post['ppmp_project_id'];
        $ppmpId    = (int) $post['ppmp_id'];
        $ppmpYear  = (int) $post['ppmp_year'];

        $this->db->trans_begin();

        $projectData = [
            'ppmp_general_description' => $post['ppmp_general_description'],
            'ppmp_project_type'        => (int) $post['ppmp_project_type'],
            'proc_id'                  => (int) $post['proc_id'],
            'ppmp_pre_proc'            => isset($post['ppmp_pre_proc']) ? 1 : 0,
            'ppmp_start_proc'          => $post['ppmp_start_proc'],
            'ppmp_end_proc'            => $post['ppmp_end_proc'],
            'ppmp_delivery'            => $post['ppmp_delivery'],
            'fund_id'                  => (int) $post['fund_id'],
            'ppmp_budget'              => (float) $post['ppmp_budget'],
            'ppmp_supporting_docs'     => (int) $post['attachment_id'],
            'ppmp_remarks'             => $post['ppmp_remarks'],
            'modified_by'              => $userId,
            'date_modified'            => date('Y-m-d H:i:s')
        ];

        $this->db->where('ppmp_project_id', $projectId);
        $this->db->update('tbl_ppmp_project', $projectData);

        if (!empty($post['deleted_item_ids'])) {
            foreach ($post['deleted_item_ids'] as $deletedItemId) {
                $this->db->where('ppmp_item_id', (int) $deletedItemId);
                $this->db->where('ppmp_project_id', $projectId);
                $this->db->update('tbl_ppmp_items', [
                    'archived'      => 1,
                    'modified_by'   => $userId,
                    'date_modified' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $itemIds    = $post['ppmp_item_id'];
        $itemMaster = $post['item_id'];
        $qtys       = $post['ppmp_quantity'];
        $units      = $post['unit_id'];
        $costs      = $post['ppmp_cost'];

        foreach ($itemMaster as $index => $itemId) {
            $existingItemId = isset($itemIds[$index]) ? (int) $itemIds[$index] : 0;

            $itemData = [
                'ppmp_id'             => $ppmpId,
                'ppmp_year'           => $ppmpYear,
                'item_id'             => (int) $itemId,
                'ppmp_classification' => isset($post['ppmp_classification'][$index])
                    ? (int) $post['ppmp_classification'][$index]
                    : 1,
                'ppmp_quantity'       => (int) $qtys[$index],
                'unit_id'             => (int) $units[$index],
                'proc_id'             => (int) $post['proc_id'],
                'fund_id'             => (int) $post['fund_id'],
                'ppmp_cost'           => (float) $costs[$index],
                'attachment_id'       => (int) $post['attachment_id'],
                'ppmp_project_id'     => $projectId,
                'archived'            => 0
            ];

            if ($existingItemId > 0) {
                $itemData['modified_by'] = $userId;
                $itemData['date_modified'] = date('Y-m-d H:i:s');

                $this->db->where('ppmp_item_id', $existingItemId);
                $this->db->where('ppmp_project_id', $projectId);
                $this->db->update('tbl_ppmp_items', $itemData);
            } else {
                $itemData['created_by'] = $userId;
                $itemData['date_created'] = date('Y-m-d H:i:s');

                $this->db->insert('tbl_ppmp_items', $itemData);
            }
        }

        if ($this->db->trans_status() === false) {


            log_message('error', 'UPDATE PROJECT: ' . $this->db->last_query());

            $error = $this->db->error();

            if ($error['code'] != 0) {
                log_message('error', 'PROJECT UPDATE ERROR: ' . json_encode($error));
            }
                    $this->db->trans_rollback();
                    return false;
        }

        $this->db->trans_commit();
        return true;
    }

    public function saveInlinePPMPItem($post, $userId)
{
    $projectId = (int) $post['ppmp_project_id'];

    $project = $this->db
        ->where('ppmp_project_id', $projectId)
        ->where('archived', 0)
        ->get('tbl_ppmp_project')
        ->row();

    if (!$project) {
        return [
            'success' => false,
            'message' => 'Project not found.'
        ];
    }

    $ppmp = $this->db
        ->where('ppmp_id', $project->ppmp_id)
        ->get('tbl_ppmp_temp')
        ->row();

    $ppmpYear = $ppmp ? $ppmp->ppmp_year : null;

    $itemId = !empty($post['ppmp_item_id'])
        ? (int) $post['ppmp_item_id']
        : 0;

    $itemData = [
        'ppmp_id'             => $project->ppmp_id,
        'ppmp_year'           => $ppmpYear,
        'ppmp_classification' => (int) $project->ppmp_class_id,
        'ppmp_quantity'       => (int) $post['ppmp_quantity'],
        'unit_id'             => (int) $post['unit_id'],
        'proc_id'             => (int) $project->proc_id,
        'fund_id'             => (int) $project->fund_id,
        'ppmp_cost'           => (float) $post['ppmp_cost'],
        'attachment_id'       => (int) $project->ppmp_supporting_docs,
        'ppmp_project_id'     => $projectId,
        'item_id'             => (int) $post['item_id'],
        'archived'            => 0
    ];

    if ($itemId > 0) {
        $itemData['modified_by'] = $userId;
        $itemData['date_modified'] = date('Y-m-d H:i:s');

        $this->db->where('ppmp_item_id', $itemId);
        $this->db->where('ppmp_project_id', $projectId);
        $this->db->update('tbl_ppmp_items', $itemData);

        $savedItemId = $itemId;
    } else {
        $itemData['created_by'] = $userId;
        $itemData['date_created'] = date('Y-m-d H:i:s');

        $this->db->insert('tbl_ppmp_items', $itemData);

        $savedItemId = $this->db->insert_id();
    }

    $this->updateProjectBudgetFromItems($projectId, $userId);

    if ($this->db->affected_rows() < 0) {
        return [
            'success' => false,
            'message' => 'Database error.',
            'query' => $this->db->last_query(),
            'error' => $this->db->error()
        ];
    }

    return [
        'success' => true,
        'ppmp_item_id' => $savedItemId
    ];
}


public function deleteInlinePPMPItem($itemId, $userId)
{
    $item = $this->db
        ->where('ppmp_item_id', $itemId)
        ->where('archived', 0)
        ->get('tbl_ppmp_items')
        ->row();

    if (!$item) {
        return [
            'success' => false,
            'message' => 'Item not found.'
        ];
    }

    $this->db->where('ppmp_item_id', $itemId);
    $this->db->update('tbl_ppmp_items', [
        'archived' => 1,
        'modified_by' => $userId,
        'date_modified' => date('Y-m-d H:i:s')
    ]);

    $this->updateProjectBudgetFromItems($item->ppmp_project_id, $userId);

    return [
        'success' => true
    ];
}


private function updateProjectBudgetFromItems($projectId, $userId)
{
    $this->db->select_sum('ppmp_cost', 'total_budget');
    $this->db->where('ppmp_project_id', $projectId);
    $this->db->where('archived', 0);

    $row = $this->db->get('tbl_ppmp_items')->row();

    $budget = $row && $row->total_budget
        ? $row->total_budget
        : 0;

    $this->db->where('ppmp_project_id', $projectId);
    $this->db->update('tbl_ppmp_project', [
        'ppmp_budget' => $budget,
        'modified_by' => $userId,
        'date_modified' => date('Y-m-d H:i:s')
    ]);
}

public function getAllPPMPProjectsForYear($year)
{
    $this->db->select('p.ppmp_project_id, p.ppmp_id, p.ppmp_general_description, p.ppmp_project_type, p.fund_id, f.fund_name, o.office_desc, tp.ppmp_year');
    $this->db->from('tbl_ppmp_project p');
    $this->db->join('tbl_ppmp_temp t', 't.ppmp_id = p.ppmp_id', 'left');
    $this->db->join('lib_funds f', 'f.fund_id = p.fund_id', 'left');
    $this->db->join('lib_office o', 'o.office_id = p.office_id', 'left');
    $this->db->join('tbl_ppmp tp', 'p.ppmp_id = tp.ppmp_id', 'left');
    $this->db->where('tp.ppmp_year', $year);
    $this->db->where('p.archived', 0);
    $projects = $this->db->get()->result_array();

    foreach ($projects as &$p) {
        $this->db->select('i.ppmp_item_id, i.item_id, i.ppmp_quantity, i.ppmp_cost, u.unit_code, it.item_description');
        $this->db->from('tbl_ppmp_items i');
        $this->db->join('lib_unit u', 'u.unit_id = i.unit_id', 'left');
        $this->db->join('lib_item it', 'it.item_id = i.item_id', 'left');
        $this->db->where('i.ppmp_project_id', $p['ppmp_project_id']);
        $this->db->where('i.archived', 0);
        $p['items'] = $this->db->get()->result_array();
    }

    return $projects;
}

public function getPPMPPDFData($year, $ppmp_id)
{
    $this->db->select('
        p.ppmp_project_id,
        p.ppmp_id,
        t.ppmp_year,
        p.ppmp_general_description,
        p.ppmp_project_type,
        p.ppmp_pre_proc,
        p.ppmp_start_proc,
        p.ppmp_end_proc,
        p.ppmp_delivery,
        p.ppmp_budget,
        p.ppmp_supporting_docs,
        p.ppmp_remarks,
        p.office_id,
        f.fund_name,
        m.proc_code,
        m.proc_name,
        o.office_abbr,
        o.office_desc
    ');

    $this->db->from('tbl_ppmp_project p');
    $this->db->join('tbl_ppmp_temp t', 't.ppmp_id = p.ppmp_id', 'left');
    $this->db->join('lib_funds f', 'f.fund_id = p.fund_id', 'left');
    $this->db->join('lib_procurement_mode m', 'm.proc_id = p.proc_id', 'left');
    $this->db->join('lib_office o', 'o.office_id = p.office_id', 'left');

    $this->db->where('p.ppmp_id', (int) $ppmp_id);
    $this->db->where('t.ppmp_year', (int) $year);
    $this->db->where('p.archived', 0);

    $this->db->order_by('p.ppmp_project_id', 'ASC');

    $projects = $this->db->get()->result_array();

    foreach ($projects as &$project) {
        $this->db->select('
            i.ppmp_item_id,
            i.item_id,
            i.ppmp_quantity,
            i.unit_id,
            i.ppmp_cost,
            u.unit_code,
            itm.item_description
        ');

        $this->db->from('tbl_ppmp_items i');
        $this->db->join('lib_unit u', 'u.unit_id = i.unit_id', 'left');
        $this->db->join('lib_item itm', 'itm.item_id = i.item_id', 'left');

        $this->db->where('i.ppmp_project_id', $project['ppmp_project_id']);
        $this->db->where('i.archived', 0);

        $this->db->order_by('i.ppmp_item_id', 'ASC');

        $project['items'] = $this->db->get()->result_array();
    }

    return $projects;
}

public function createPPMPSubmission($data)
{
    $this->db->trans_begin();

    $submission = [
        'ppmp_id' => $data['ppmp_id'],
        'office_id' => $data['office_id'],
        'submitted_by' => $data['submitted_by'],
        'submitted_to' => $data['submitted_to'],
        'submission_type' => $data['submission_type'],
        'status' => 1,
        'remarks' => $data['remarks'],
        'date_submitted' => date('Y-m-d H:i:s'),
        'archived' => 0
    ];

    $this->db->insert('tbl_ppmp_submission', $submission);
    $submission_id = $this->db->insert_id();

    $this->db->insert('tbl_ppmp_submission_logs', [
        'submission_id' => $submission_id,
        'ppmp_id' => $data['ppmp_id'],
        'action_type' => $data['submission_type'] == 1 ? 'SUBMITTED_FOR_REVIEW' : 'SUBMITTED_FOR_APPROVAL',
        'action_by' => $data['submitted_by'],
        'action_to' => $data['submitted_to'],
        'remarks' => $data['remarks'],
        'date_created' => date('Y-m-d H:i:s')
    ]);

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return false;
    }

    $this->db->trans_commit();
    return true;
}


public function actOnPPMPSubmission($submission_id, $action, $remarks, $user_id)
{
    $submission = $this->db
        ->where('submission_id', $submission_id)
        ->where('archived', 0)
        ->get('tbl_ppmp_submission')
        ->row();

    if (!$submission) {
        return false;
    }

    $statusText = [
        2 => 'RETURNED',
        3 => 'REVIEWED',
        4 => 'APPROVED',
        5 => 'DISAPPROVED'
    ];

    if (!isset($statusText[$action])) {
        return false;
    }

    $this->db->trans_begin();

    $this->db->where('submission_id', $submission_id);
    $this->db->update('tbl_ppmp_submission', [
        'status' => $action,
        'date_actioned' => date('Y-m-d H:i:s'),
        'actioned_by' => $user_id,
        'action_remarks' => $remarks
    ]);

    $this->db->insert('tbl_ppmp_submission_logs', [
        'submission_id' => $submission_id,
        'ppmp_id' => $submission->ppmp_id,
        'action_type' => $statusText[$action],
        'action_by' => $user_id,
        'action_to' => $submission->submitted_by,
        'remarks' => $remarks,
        'date_created' => date('Y-m-d H:i:s')
    ]);

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return false;
    }

    $this->db->trans_commit();
    return true;
}


public function getPPMPSubmissionInbox($user_id)
{
    $this->db->select('
        s.*,
        t.ppmp_year,
        u1.fullname AS submitted_by_name,
        u2.fullname AS submitted_to_name,
        o.office_name,
        o.office_abbr
    ');

    $this->db->from('tbl_ppmp_submission s');
    $this->db->join('tbl_ppmp_temp t', 't.ppmp_id = s.ppmp_id', 'left');
    $this->db->join('aauth_users u1', 'u1.id = s.submitted_by', 'left');
    $this->db->join('aauth_users u2', 'u2.id = s.submitted_to', 'left');
    $this->db->join('lib_office o', 'o.office_id = s.office_id', 'left');

    $this->db->where('s.submitted_to', $user_id);
    $this->db->where('s.archived', 0);
    $this->db->order_by('s.date_submitted', 'DESC');

    return $this->db->get()->result();
}


    public function getUsersForPPMPRouting(){
        $this->db->select('id, fullname');
        $this->db->from('aauth_users');
        
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = aauth_users.id', 'left');
        $this->db->where('banned', 0);
        $this->db->group_start();
            $this->db->where('ug.group_id', 3);
            $this->db->or_where('ug.group_id', 4);
            $this->db->or_where('ug.group_id', 5);
        $this->db->group_end();
        $this->db->order_by('fullname', 'ASC');

        return $this->db->get()->result();
    }

    public function routePPMP($data)
    {
        $status = $this->mapWorkflowStatus($data['action_type']);

        if (!$status) {
            return false;
        }

        $this->db->trans_begin();

        $workflowData = [
            'ppmp_id'      => $data['ppmp_id'],
            'office_id'    => $data['office_id'],
            'from_user_id' => $data['from_user_id'],
            'to_user_id'   => $data['to_user_id'] > 0 ? $data['to_user_id'] : null,
            'action_type'  => $data['action_type'],
            'status'       => $status,
            'remarks'      => $data['remarks'],
            'created_by'   => $data['from_user_id'],
            'date_created' => date('Y-m-d H:i:s'),
            'archived'     => 0
        ];

        $this->db->insert('tbl_ppmp_workflow', $workflowData);

        $existing = $this->db
            ->where('ppmp_id', $data['ppmp_id'])
            ->get('tbl_ppmp_current_status')
            ->row();

        $currentData = [
            'office_id'       => $data['office_id'],
            'current_status' => $status,
            'current_holder' => $data['to_user_id'] > 0 ? $data['to_user_id'] : $data['from_user_id'],
            'last_action'    => $data['action_type'],
            'last_remarks'   => $data['remarks'],
            'modified_by'    => $data['from_user_id'],
            'date_modified'  => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $this->db->where('ppmp_id', $data['ppmp_id']);
            $this->db->update('tbl_ppmp_current_status', $currentData);
        } else {
            $currentData['ppmp_id'] = $data['ppmp_id'];
            $this->db->insert('tbl_ppmp_current_status', $currentData);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }


    private function mapWorkflowStatus($action_type)
    {
        switch ($action_type) {
            case 'SUBMIT_FOR_REVIEW':
                return 'FOR_REVIEW';

            case 'RETURN_FOR_REVISION':
                return 'RETURNED';

            case 'MARK_REVIEWED':
                return 'REVIEWED';

            case 'SUBMIT_FOR_APPROVAL':
                return 'FOR_APPROVAL';

            case 'APPROVE':
                return 'APPROVED';

            case 'DISAPPROVE':
                return 'DISAPPROVED';

            case 'RESUBMIT_FOR_REVIEW':
                return 'FOR_REVIEW';

            case 'RESUBMIT_FOR_APPROVAL':
                return 'FOR_APPROVAL';

            default:
                return false;
        }
    }


    public function getPPMPWorkflowHistory($ppmp_id)
    {
        $this->db->select('
            w.*,
            u1.fullname AS from_user,
            u2.fullname AS to_user,
            o.office_name,
            o.office_abbr
        ');

        $this->db->from('tbl_ppmp_workflow w');
        $this->db->join('aauth_users u1', 'u1.id = w.from_user_id', 'left');
        $this->db->join('aauth_users u2', 'u2.id = w.to_user_id', 'left');
        $this->db->join('lib_office o', 'o.office_id = w.office_id', 'left');

        $this->db->where('w.ppmp_id', $ppmp_id);
        $this->db->where('w.archived', 0);
        $this->db->order_by('w.date_created', 'ASC');

        return $this->db->get()->result();
    }


    public function getPPMPInbox($user_id)
    {
        $this->db->select('
            s.*,
            t.ppmp_year,
            u.fullname AS modified_by_name,
            o.office_name,
            o.office_abbr
        ');

        $this->db->from('tbl_ppmp_current_status s');
        $this->db->join('tbl_ppmp_temp t', 't.ppmp_id = s.ppmp_id', 'left');
        $this->db->join('aauth_users u', 'u.id = s.modified_by', 'left');
        $this->db->join('lib_office o', 'o.office_id = s.office_id', 'left');

        $this->db->where('s.current_holder', $user_id);
        $this->db->order_by('s.date_modified', 'DESC');

        return $this->db->get()->result();
    }


    public function getUsersForRouting()
    {
        $this->db->select('id, fullname');
        $this->db->from('aauth_users');
        $this->db->where('banned', 0);
        $this->db->order_by('fullname', 'ASC');

        return $this->db->get()->result();
    }

    public function getPPMPCountForReview(){
                
        return $this->db
            ->where('current_status', 'FOR_REVIEW')
            ->where('current_holder', $this->session->userdata('userID'))
            ->count_all_results('tbl_ppmp_current_status');
    }

    public function getPPMPCountForApproval(){
                
        return $this->db
            ->where('current_status', 'FOR_APPROVAL')
            ->where('current_holder', $this->session->userdata('userID'))
            ->count_all_results('tbl_ppmp_current_status');
    }
}

?>
