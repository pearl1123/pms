<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ClinicalNotes_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge(); // Load DBForge for dynamic table creation
    }


    public function get_all_notes()
    {
        if ($this->db->table_exists('lib_patient_notes')) {
            return $this->db->get('lib_patient_notes')->result_array();
        }
        return [];
    }



    // ========= FORMS =========

    public function insert_form($data)
    {
        $this->db->insert('tbl_patient_note_forms', $data);
        return $this->db->insert_id();
    }

    public function update_form($form_id, $data)
    {
        return $this->db->where('id', $form_id)->update('tbl_patient_note_forms', $data);
    }

    public function get_form_by_id($form_id)
    {
        return $this->db->get_where('tbl_patient_note_forms', ['id' => $form_id])->row();
    }

    public function get_form($form_id)
    {
        return $this->db->get_where('tbl_patient_note_forms', ['id' => $form_id])->row();
    }

    public function get_all_forms($keyword = null)
    {
        $this->db->select('f.id, f.form_name, u.fullname as encoded_by, f.created_at as date_encoded, f.modified_at as date_modified, f.modified_by, f.archived');
        $this->db->from('tbl_patient_note_forms f');
        $this->db->join('aauth_users u', 'f.created_by = u.id', 'left');

        if ($keyword) {
            $this->db->group_start()
                ->like('f.form_name', $keyword)
                ->or_like('u.fullname', $keyword)
                ->or_like('f.created_at', $keyword)
                ->group_end();
        }

        $this->db->order_by('f.created_at', 'DESC');

        $query = $this->db->get();
        return $query ? $query->result() : [];
    }

    // ========= FIELDS =========

    public function insert_field($data)
    {
        return $this->db->insert('tbl_patient_note_fields', $data);
    }

    public function update_field($field_id, $data)
    {
        return $this->db->where('id', $field_id)->update('tbl_patient_note_fields', $data);
    }

    public function get_field_by_id($field_id)
    {
        return $this->db->get_where('tbl_patient_note_fields', ['id' => $field_id])->row();
    }

    public function get_fields_by_form_id($form_id)
    {
        return $this->db->get_where('tbl_patient_note_fields', ['form_id' => $form_id])->result();
    }

    public function get_all_fields_grouped_by_form()
    {
        $fields = $this->db->get('tbl_patient_note_fields')->result();
        $grouped = [];
        foreach ($fields as $field) {
            $grouped[$field->form_id][] = $field;
        }
        return $grouped;
    }

    // ========= lib_patient_notes TABLE MANAGEMENT =========

    /**
     * Check if lib_patient_notes table exists
     */
    public function table_exists()
    {
        return $this->db->table_exists('lib_patient_notes');
    }

    /**
     * Create lib_patient_notes table dynamically
     * @param array $fields - array of fields to define table schema
     */
    public function create_notes_table($fields)
    {
        // Add default fields
        $default_fields = [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'form_id' => ['type' => 'INT', 'constraint' => 11],
            'date_encoded' => ['type' => 'DATETIME'],
            'encoded_by' => ['type' => 'VARCHAR', 'constraint' => 100],
            'date_modified' => ['type' => 'DATETIME', 'null' => TRUE],
            'modified_by' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE]
        ];

        $schema = array_merge($default_fields, $fields);

        $this->dbforge->add_field($schema);
        $this->dbforge->add_key('id', TRUE);
        return $this->dbforge->create_table('lib_patient_notes', TRUE);
    }

    /**
     * Insert data into lib_patient_notes
     * @param array $note_data - includes form fields and meta fields
     */
    public function insert_note($note_data)
    {
        return $this->db->insert('lib_patient_notes', $note_data);
    }

    /**
     * Update note in lib_patient_notes
     */
    public function update_note($note_id, $data)
    {
        return $this->db->where('id', $note_id)->update('lib_patient_notes', $data);
    }

    /**
     * Get note by ID from lib_patient_notes
     */
    public function get_note_by_id($note_id)
    {
        return $this->db->get_where('lib_patient_notes', ['id' => $note_id])->row();
    }

    /**
     * Get all notes under a specific form
     */
    public function get_notes_by_form_id($form_id)
    {
        return $this->db->get_where('lib_patient_notes', ['form_id' => $form_id])->result();
    }

    /**
     * Delete a note
     */
    public function delete_note($note_id)
    {
        return $this->db->where('id', $note_id)->delete('lib_patient_notes');
    }

    // ========= PAGINATION SUPPORT FOR DATATABLES =========

    public function get_paginated_forms($start, $limit)
    {
        $this->db->limit($limit, $start);
        $query = $this->db->get('tbl_patient_note_forms');

        $data = [];
        foreach ($query->result() as $row) {
            $data[] = [
                'form_id' => $row->id,
                'form_name' => $row->form_name,
                'encoded_by' => $row->encoded_by ?? '',
                'date_encoded' => $row->created_at,
                'actions' => '
                    <a href="' . base_url('ClinicalNotes/edit_fields/' . $row->id) . '" class="btn btn-sm btn-info">Edit Fields</a>
                    <a href="' . base_url('ClinicalNotes/edit_form/' . $row->id) . '" class="btn btn-sm btn-warning">Edit Form Name</a>
                '
            ];
        }

        return $data;
    }

    public function get_total_forms()
    {
        return $this->db->count_all('tbl_patient_note_forms');
    }

    //To view in patient profile
    public function get_notes_by_patient_and_form($patient_id, $form_id)
    {
        $this->db->where('form_id', $form_id);
        $this->db->where('patient_id', $patient_id); // assumes you added patient_id column
        return $this->db->get('lib_patient_notes')->result_array();
    }

    public function get_notes_by_form($form_id)
    {
        $this->db->where('id', $form_id);
        return $this->db->get('tbl_patient_note_forms')->row();
    }

    public function get_form_id_by_name($form_name)
    {
        return $this->db->get_where('tbl_patient_note_forms', ['form_name' => $form_name])->row('id');
    }

    public function get_field_names_by_form_id($form_id)
    {
        $this->db->select('field_name');
        $this->db->from('tbl_patient_note_fields');
        $this->db->where('form_id', $form_id);
        $query = $this->db->get();

        $fields = [];
        foreach ($query->result() as $row) {
            $fields[] = $row->field_name;
        }
        return $fields;
    }

    public function get_field_options($field_id)
    {
        $options = $this->db->get_where('tbl_field_relationship', ['field_id' => $field_id])->result_array();

        // Build tree recursively
        return $this->build_options_tree($options);
    }

    private function build_options_tree($options, $parent_id = null)
    {
        $tree = [];

        foreach ($options as $option) {
            if ((int)$option['parent_option_id'] === (int)$parent_id) {
                $children = $this->build_options_tree($options, $option['id']);
                $tree[] = [
                    'label' => $option['option_label'],
                    'input_type' => $option['input_type'],
                    'sub_options' => $children
                ];
            }
        }

        return $tree;
    }
}
