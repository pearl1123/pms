<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PatientNotes_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // Fetch all note forms
    public function get_all_forms()
    {
        return $this->db->get('tbl_patient_note_forms')->result();
    }

    // Fetch all fields related to a form
    public function get_form_fields($form_id)
    {
        return $this->db->get_where('tbl_patient_note_fields', ['form_id' => $form_id])->result();
    }

    public function get_patient_records($table, $patient_id)
    {
        return $this->db->where('patient_id', $patient_id)->get($table)->result();
    }

    public function get_record_by_id($table, $id)
    {
        return $this->db->where('id', $id)->get($table)->row();
    }

    public function get_form_name($form_id)
    {
        $query = $this->db->get_where('tbl_patient_note_forms', ['id' => $form_id])->row();
        return $query ? $query->form_name : 'Unknown Form';
    }

    // Fetch notes by form with encoded and modified user names
    public function get_notes_by_form($form_id)
    {
        $this->db->select('n.*, 
            u1.fullname AS encoded_by_name, 
            u2.fullname AS modified_by_name
        ');
        $this->db->from('tbl_patient_notes n');
        $this->db->join('users u1', 'u1.id = n.encoded_by', 'left');
        $this->db->join('users u2', 'u2.id = n.modified_by', 'left');
        $this->db->where('n.form_id', $form_id);
        $this->db->order_by('n.id', 'DESC');
        return $this->db->get()->result();
    }

    // Get single note by ID with encoded and modified user names
    public function get_note_by_id($id)
    {
        $this->db->select('n.*, 
            u1.fullname AS encoded_by_name, 
            u2.fullname AS modified_by_name
        ');
        $this->db->from('tbl_patient_notes n');
        $this->db->join('users u1', 'u1.id = n.encoded_by', 'left');
        $this->db->join('users u2', 'u2.id = n.modified_by', 'left');
        $this->db->where('n.id', $id);
        return $this->db->get()->row();
    }

    public function get_note_by_id_dynamic($table, $note_id)
    {
        $this->db->where('id', $note_id);
        $query = $this->db->get($table);

        if (!$query || $query->num_rows() === 0) {
            return false;
        }

        $note = $query->row_array();

        // Add this block only if the form has multiple entries
        if ($table === 'tbl_nurses_progress_notes') {
            $entries_query = $this->db->get_where('tbl_nurses_progress_notes_entries', ['note_id' => $note_id]);

            if ($entries_query && $entries_query->num_rows() > 0) {
                $note['entries'] = $entries_query->result_array();
            } else {
                $note['entries'] = []; // Safe fallback
            }
        }

        return $note;
    }

    // public function get_note_by_id_dynamic($table, $id)
    // {
    //     return $this->db->get_where($table, ['id' => $id])->row_array();
    // }

    // Insert new patient note
    public function insert_note($form_id, $patient_id, $data_json, $user_id)
    {
        return $this->db->insert('tbl_patient_notes', [
            'form_id' => $form_id,
            'patient_id' => $patient_id,
            'data' => $data_json,
            'encoded_by' => $user_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Update patient note

    public function get_note_for_edit($table, $note_id)
    {
        $this->db->where('id', $note_id);
        $query = $this->db->get($table);
        $row = $query->row_array();

        if (!$row) return [];

        $excluded_fields = ['id', 'form_id', 'patient_id', 'encoded_by', 'date_encoded', 'modified_by', 'date_modified'];

        $form_id = $row['form_id'] ?? null;
        if (!$form_id) return [];

        $this->db->where('form_id', $form_id);
        $field_defs = $this->db->get('tbl_patient_note_fields')->result_array();

        $fields = [];
        foreach ($field_defs as $def) {
            $original_name = $def['field_name'];
            $normalized_name = strtolower(trim(str_replace([' ', '/'], '_', $original_name)));

            if (in_array($normalized_name, $excluded_fields)) continue;
            if (!array_key_exists($normalized_name, $row)) {
                log_message('debug', "Skipping field '{$normalized_name}': not found in table {$table}");
                continue;
            }

            $field_type = strtolower(trim($def['field_type']));
            $value = $row[$normalized_name];

            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }

            $fields[] = [
                'field_name'       => $normalized_name,
                'field_description' => $original_name,
                'field_type'       => $field_type,
                'value'            => $value,
                'field_id'         => $def['id'],
                'options'          => $this->get_field_options($def['id']),
                'null_constraint'  => $def['null_constraint'] ?? ''  // Fixed: changed from 'null' to 'null_constraint'
            ];
        }

        log_message('debug', 'Prepared fields for modal: ' . print_r($fields, true));
        return $fields;
    }



    // helper for the edit options
    public function get_field_options($field_id)
    {
        $this->db->where('field_id', $field_id);
        $this->db->where('parent_id', null); // top-level options only
        $query = $this->db->get('tbl_patient_note_field_options');

        $options = [];
        foreach ($query->result_array() as $row) {
            $options[] = $row['option_label'];
        }
        return $options;
    }





    // To view visit type in PDF
    public function get_visit_type($encounter_id)
    {
        if (empty($encounter_id)) {
            return 'Unknown';
        }

        $this->db->select('lib_visit_type.visit_type_desc AS visit_type_name');
        $this->db->from('tbl_patient_encounter');
        $this->db->join('lib_visit_type', 'lib_visit_type.visit_type_id = tbl_patient_encounter.visit_type_id');
        $this->db->where('tbl_patient_encounter.encounter_id', $encounter_id);

        $query = $this->db->get();

        if ($query === FALSE) {
            log_message('error', 'Database error: ' . $this->db->error()['message']);
            return 'Unknown';
        }

        if ($query->num_rows() > 0) {
            return $query->row()->visit_type_name;
        }

        return 'Unknown';
    }



    public function get_form_fields_by_table($table_name)
    {
        $this->db->select('*');
        $this->db->from('tbl_patient_note_fields');
        $this->db->where('table_name', $table_name);
        $this->db->order_by('id', 'ASC'); // optional
        return $this->db->get()->result_array();
    }

    // this function is used in PDF
    public function get_all_notes_for_patient($table_name, $patient_id, $encounter_id = null)
    {
        if (!$this->db->table_exists($table_name)) {
            log_message('error', "Table does not exist: $table_name");
            return [];
        }

        $this->db->select('*');
        $this->db->from($table_name);
        $this->db->where('patient_id', $patient_id);

        if ($encounter_id !== null) {
            $this->db->where('encounter_id', $encounter_id);
        }

        // ✅ Safe order_by based on available columns
        $fields = $this->db->list_fields($table_name);
        if (in_array('date_time', $fields)) {
            $this->db->order_by('date_time', 'ASC');
        } elseif (in_array('date_shift', $fields)) {
            $this->db->order_by('date_shift', 'ASC');
        } elseif (in_array('date_encoded', $fields)) {
            $this->db->order_by('date_encoded', 'ASC');
        } else {
            $this->db->order_by('id', 'ASC'); // fallback
        }

        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'DB Error in get_all_notes_for_patient(): ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    // public function update_note($table, $note_id, $fields)
    // {
    //     $this->load->library('session');
    //     $modified_by = $this->session->userdata('userID') ?: 'Unknown User';
    //     $fields['modified_by'] = $modified_by;
    //     $fields['date_modified'] = date('Y-m-d H:i:s');

    //     // Convert arrays to JSON
    //     foreach ($fields as $key => $value) {
    //         if (is_array($value)) {
    //             $fields[$key] = json_encode($value);
    //         }
    //     }

    //     // Perform update
    //     $this->db->where('id', $note_id);
    //     $success = $this->db->update($table, $fields);

    //     // Debugging
    //     if (!$success) {
    //         log_message('error', 'DB Error on update_note(): ' . print_r($this->db->error(), true));
    //         log_message('error', 'Last query: ' . $this->db->last_query());
    //     }

    //     return $success;
    // }

    public function update_note($table, $note_id, $fields)
    {
        $this->load->library('session');
        $modified_by = $this->session->userdata('userID') ?: 0; // int only
        $fields['modified_by'] = $modified_by;
        $fields['date_modified'] = date('Y-m-d H:i:s');

        // Remove non-column fields
        unset($fields['note_id'], $fields['table']);

        // Convert arrays to JSON and empty strings to NULL
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = json_encode($value);
            } elseif ($value === '') {
                $fields[$key] = null;
            }
        }

        $this->db->where('id', $note_id);
        log_message('debug', 'Updating table: ' . $table . ' with fields: ' . print_r($fields, true) . ' where id=' . $note_id);

        $success = $this->db->update($table, $fields);

        if (!$success) {
            $error = $this->db->error();
            log_message('error', 'DB Error on update_note(): ' . print_r($error, true));
            log_message('error', 'Last query: ' . $this->db->last_query());
        }

        return $success;
    }




    // Delete patient note
    public function delete_note($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_patient_notes');
    }

    // public function get_dynamic_patient_notes_datatable($patient_id, $start_date = null, $end_date = null, $encounter_id = null)
    // {
    //     $start = intval($this->input->get("start") ?? 0);
    //     $length = intval($this->input->get("length") ?? 10);
    //     $draw = intval($this->input->get("draw") ?? 1);
    //     $search = $this->input->get("search")['value'] ?? '';
    //     $columns = $this->input->get("columns") ?? [];

    //     $all_data = [];


    //     $forms = $this->db->select('id, form_name, table_name')->get('tbl_patient_note_forms')->result();

    //     $multi_row_forms = [
    //         'Nurses Progress Notes',
    //         'Capillary Blood Glucose Monitoring and Treatment Record',
    //         'Doctors Order Sheet'
    //     ];

    //     foreach ($forms as $form) {
    //         $table = $form->table_name;

    //         if (!$this->db->table_exists($table)) continue;

    //         $is_multi_row = in_array($form->form_name, $multi_row_forms);

    //         $this->db->reset_query();
    //         $this->db->from($table)
    //                  ->where('patient_id', $patient_id);


    //         if (!empty($encounter_id)) {
    //             $this->db->where('encounter_id', $encounter_id);
    //         }


    //         if (!empty($start_date) && !empty($end_date)) {
    //             $this->db->where('DATE(date_encoded) >=', $start_date)
    //                      ->where('DATE(date_encoded) <=', $end_date);
    //         }


    //         if ($is_multi_row) {
    //             $this->db->select('encounter_id, MAX(date_encoded) as date_encoded, MAX(id) as id');
    //             $this->db->group_by('encounter_id');
    //         } else {
    //             $this->db->select('*');
    //         }

    //         $results = $this->db->get()->result();

    //         foreach ($results as $note) {
    //             $note_enc_id = $note->encounter_id ?? null;


    //             if (!empty($encounter_id) && $note_enc_id != $encounter_id) continue;

    //             $date_encoded = $note->date_encoded ?? '';
    //             $all_data[] = [
    //                 'id' => $note->id ?? null,
    //                 'encounter_id' => $note_enc_id,
    //                 'form_name' => $form->form_name,
    //                 'table_name' => $table,
    //                 'date_encoded' => $date_encoded,
    //                 'actions' => ''
    //             ];
    //         }
    //     }


    //     if (!empty($search)) {
    //         $all_data = array_filter($all_data, function ($n) use ($search) {
    //             return stripos($n['form_name'], $search) !== false
    //                 || stripos((string)$n['encounter_id'], $search) !== false
    //                 || stripos($n['date_encoded'], $search) !== false;
    //         });
    //     }


    //     $order = $this->input->get("order")[0] ?? null;
    //     if ($order) {
    //         $col_idx = intval($order['column']);
    //         $col_dir = $order['dir'] === 'asc' ? SORT_ASC : SORT_DESC;
    //         $col_name = $columns[$col_idx]['data'] ?? 'date_encoded';
    //         usort($all_data, function ($a, $b) use ($col_name, $col_dir) {
    //             return $col_dir === SORT_ASC
    //                 ? strcmp(strtolower($a[$col_name]), strtolower($b[$col_name]))
    //                 : strcmp(strtolower($b[$col_name]), strtolower($a[$col_name]));
    //         });
    //     }

    //     $recordsTotal = count($all_data);
    //     $data = array_slice($all_data, $start, $length);

    //     return [
    //         "draw" => $draw,
    //         "recordsTotal" => $recordsTotal,
    //         "recordsFiltered" => $recordsTotal,
    //         "data" => $data
    //     ];
    // }
    public function get_dynamic_patient_notes_datatable($patient_id, $start_date = null, $end_date = null, $encounter_id = null)
    {
        $start = intval($this->input->get("start") ?? 0);
        $length = intval($this->input->get("length") ?? 10);
        $draw = intval($this->input->get("draw") ?? 1);
        $search = $this->input->get("search")['value'] ?? '';
        $columns = $this->input->get("columns") ?? [];

        $all_data = [];

        // Load all note forms
        $forms = $this->db->select('id, form_name, table_name')->get('tbl_patient_note_forms')->result();

        $multi_row_forms = [
            'Nurses Progress Notes',
            'Capillary Blood Glucose Monitoring and Treatment Record',
            'Doctors Order Sheet'
        ];

        foreach ($forms as $form) {
            $table = $form->table_name;

            if (!$this->db->table_exists($table)) continue;

            $is_multi_row = in_array($form->form_name, $multi_row_forms);

            $this->db->reset_query();
            $this->db->from($table)
                ->where('patient_id', $patient_id);

            // ✅ Encounter filter — skip if “all”
            if (!empty($encounter_id) && strtolower($encounter_id) !== 'all') {
                $this->db->where('encounter_id', $encounter_id);
            }

            // ✅ Date range filter
            if (!empty($start_date) && !empty($end_date)) {
                $this->db->where('DATE(date_encoded) >=', $start_date)
                    ->where('DATE(date_encoded) <=', $end_date);
            }

            // Multi-row forms: group by encounter
            if ($is_multi_row) {
                $this->db->select('encounter_id, MAX(date_encoded) as date_encoded, MAX(id) as id');
                $this->db->group_by('encounter_id');
            } else {
                $this->db->select('*');
            }

            $results = $this->db->get()->result();

            foreach ($results as $note) {
                $note_enc_id = $note->encounter_id ?? null;

                // ✅ Skip only if specific encounter is selected
                if (!empty($encounter_id) && strtolower($encounter_id) !== 'all' && $note_enc_id != $encounter_id) continue;

                $date_encoded = $note->date_encoded ?? '';
                $all_data[] = [
                    'id' => $note->id ?? null,
                    'encounter_id' => $note_enc_id,
                    'form_name' => $form->form_name,
                    'table_name' => $table,
                    'date_encoded' => $date_encoded,
                    'actions' => ''
                ];
            }
        }

        // ✅ Apply search
        if (!empty($search)) {
            $all_data = array_filter($all_data, function ($n) use ($search) {
                return stripos($n['form_name'], $search) !== false
                    || stripos((string)$n['encounter_id'], $search) !== false
                    || stripos($n['date_encoded'], $search) !== false;
            });
        }

        // ✅ Sorting
        $order = $this->input->get("order")[0] ?? null;
        if ($order) {
            $col_idx = intval($order['column']);
            $col_dir = $order['dir'] === 'asc' ? SORT_ASC : SORT_DESC;
            $col_name = $columns[$col_idx]['data'] ?? 'date_encoded';
            usort($all_data, function ($a, $b) use ($col_name, $col_dir) {
                $valA = strtolower($a[$col_name] ?? '');
                $valB = strtolower($b[$col_name] ?? '');
                return $col_dir === SORT_ASC
                    ? strcmp($valA, $valB)
                    : strcmp($valB, $valA);
            });
        }


        $recordsTotal = count($all_data);
        $data = array_slice($all_data, $start, $length);

        return [
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => $data
        ];
    }


    public function get_latest_encounter_id_from_all_tables($patient_id)
    {
        $forms = $this->db->select('table_name')->get('tbl_patient_note_forms')->result();
        $latest = null;

        foreach ($forms as $form) {
            $table = $form->table_name;

            if (!$this->db->table_exists($table)) continue;

            $this->db->select('encounter_id, date_encoded');
            $this->db->from($table);
            $this->db->where('patient_id', $patient_id);
            $this->db->where('encounter_id IS NOT NULL');
            $this->db->order_by('date_encoded', 'DESC');
            $this->db->limit(1);
            $row = $this->db->get()->row();

            if ($row && (!$latest || strtotime($row->date_encoded) > strtotime($latest->date_encoded))) {
                $latest = $row;
            }
        }

        return $latest->encounter_id ?? null;
    }

    public function get_visit_status_by_encounter_id($encounter_id)
    {
        $row = $this->db
            ->select('lvs.visit_status_abbr')
            ->from('db_emr.tbl_patient_encounter tpe')
            ->join('db_emr.lib_visit_status lvs', 'tpe.visit_status_id = lvs.visit_status_id', 'left')
            ->where('tpe.encounter_id', $encounter_id)
            ->get()
            ->row();

        log_message('error', '[MODEL] Encounter ID: ' . $encounter_id . ' → Result: ' . json_encode($row));

        return $row->visit_status_abbr ?? null;
    }





    // public function get_patient_by_id($id) {
    //     $query = $this->db->get_where('tbl_patient_list', ['id' => $id]);

    //     if ($query && $query->num_rows() > 0) {
    //         return $query->row_array();
    //     } else {
    //         log_message('error', 'Patient not found or DB error in get_patient_by_id. ID: ' . $id);
    //         return null;
    //     }
    // }

    public function get_patient_by_id($id)
    {
        $query = $this->db->get_where('tbl_patient_list', ['patient_id' => $id]);

        if ($query && $query->num_rows() > 0) {
            return $query->row_array();
        } else {
            log_message('error', 'Patient not found or DB error in get_patient_by_id. ID: ' . $id);
            return null;
        }
    }


    public function get_patient_form_data($patient_id, $form_name)
    {
        // Build dynamic table name with prefix tbl_
        $table_name = 'tbl_' . $form_name;

        // Check if table exists
        if (!$this->db->table_exists($table_name)) {
            log_message('error', 'Dynamic table not found: ' . $table_name);
            return null;
        }

        // Fetch patient data from dynamic table
        $this->db->where('patient_id', $patient_id);
        $query = $this->db->get($table_name);

        if ($query && $query->num_rows() > 0) {
            return $query->row_array(); // or result_array() if multiple records
        } else {
            log_message('error', "No data found in $table_name for patient_id: $patient_id");
            return null;
        }
    }



    public function get_latest_form_name_for_patient($patient_id)
    {
        $this->db->select('form_name');
        $this->db->from('tbl_patient_note_forms'); // or whatever table holds patient-form mapping
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('date_created', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->row()->form_name;
        }
        return null;
    }


    // Print PDF
    public function get_note_data($table_name, $note_id)
    {
        // Check if table exists
        if (!$this->db->table_exists($table_name)) {
            log_message('error', "Table not found: $table_name");
            return false;
        }

        // Query the note by its ID
        $this->db->where('id', $note_id);
        $query = $this->db->get($table_name);

        if ($query && $query->num_rows() > 0) {
            return $query->row_array();
        } else {
            log_message('error', "No note found with ID $note_id in $table_name");
            return false;
        }
    }

    public function get_all_review_of_systems()
    {
        $query = $this->db->get('lib_chf_review_of_systems');

        if (!$query) {
            log_message('error', 'Failed to fetch from lib_chf_review_of_systems: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    public function get_all_co_morbidities()
    {
        $query = $this->db->get('lib_chf_co_morbidities');

        if (!$query) {
            log_message('error', 'Failed to fetch from lib_chf_co_morbidities: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    public function get_all_current_medications()
    {
        $query = $this->db->get('lib_chf_current_medications');

        if (!$query) {
            log_message('error', 'Failed to fetch from lib_chf_current_medications: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    public function get_all_past_medical_history()
    {
        $query = $this->db->get('lib_chf_past_medical_history');

        if (!$query) {
            log_message('error', 'Failed to fetch from lib_chf_past_medical_history: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    public function get_all_family_history()
    {
        $query = $this->db->get('lib_chf_family_history');

        if (!$query) {
            log_message('error', 'Failed to fetch from lib_chf_family_history: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    public function get_all_smoking_status()
    {
        $query = $this->db->get('lib_chf_smoking_status');

        if (!$query) {
            log_message('error', 'Failed to fetch from lib_chf_smoking_status: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    public function get_all_alcohol_intake_history()
    {
        $query = $this->db->get('lib_chf_alcohol_intake_history');

        if (!$query) {
            log_message('error', 'Failed to fetch from lib_chf_alcohol_intake_history: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }


    public function get_all_general_survey()
    {
        $query = $this->db->get('lib_chf_general_survey');

        if (!$query) {
            log_message('error', 'Failed to fetch from lib_chf_general_survey: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    public function get_last_chief_complaint($patient_id)
    {
        $this->db->select('cc.chief_complaint_desc');
        $this->db->from('tbl_patient_encounter pe');
        $this->db->join('lib_chief_complaint cc', 'pe.chief_complaint = cc.chief_complaint_id', 'left');
        $this->db->where('pe.patient_id', $patient_id);
        $this->db->order_by('pe.encounter_date', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row()->chief_complaint_desc : '';
    }

    public function getpatientID($id)
    {
        $this->db->select('
        p.patient_id,
        p.firstname,
        p.middlename,
        p.lastname,
        p.title_id,
        p.suffix_id,
        s.suffix_desc
    ');
        $this->db->from('tbl_patient_list p');
        $this->db->join('lib_suffix s', 'p.suffix_id = s.suffix_id', 'left'); // ✅ join suffix
        $this->db->where('p.patient_id', $id);

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->row(); // return object
        } else {
            log_message('error', 'Patient not found. ID: ' . $id);
            return null;
        }
    }

    public function get_doctors_orders_by_patient($patient_id, $encounter_id = null)
    {
        $this->db->select('
        t1.*,
        p.mrn,
        CONCAT(p.lastname, ", ", p.firstname, " ", COALESCE(p.middlename, "")) AS patient_name,
        e.fullname AS encoded_by_name,
        m.fullname AS modified_by_name
    ');
        $this->db->from('tbl_doctors_order_sheet t1');
        $this->db->join('tbl_patient_list p', 'p.patient_id = t1.patient_id', 'left');
        $this->db->join('aauth_users e', 'e.id = t1.encoded_by', 'left');
        $this->db->join('aauth_users m', 'm.id = t1.modified_by', 'left');

        // Always filter by patient ID
        $this->db->where('t1.patient_id', $patient_id);

        // Filter by encounter only if defined
        if ($encounter_id !== null && $encounter_id !== '' && $encounter_id !== 0) {
            $this->db->where('t1.encounter_id', $encounter_id);
        }

        $this->db->order_by('t1.date_time', 'DESC');

        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'Error in get_doctors_orders_by_patient(): ' . $this->db->last_query());
            return [];
        }

        return $query->result_array();
    }
}
