<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DocumentModel extends CI_Model
{

    protected $table = 'tbl_patient_documents';

    protected $fields = [
        'patient_id',
        'encounter_id',
        'document_type_id',
        'file_name',
        'file_path',
        'description',
        'uploaded_by',
        'date_uploaded',
        'date_last_modified',
        'last_modified_by'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    private function filterFields($data)
    {
        $clean = [];
        foreach ($this->fields as $field) {
            $clean[$field] = array_key_exists($field, $data) ? $data[$field] : null;
        }
        return $clean;
    }

    public function addDocument($data)
    {
        $data = $this->filterFields($data);
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updateDocument($data)
    {
        if (!isset($data['document_id'])) return false;
        $id = $data['document_id'];
        unset($data['document_id']);
        $data = $this->filterFields($data);
        $this->db->where('document_id', $id);
        return $this->db->update($this->table, $data);
    }

    public function getDocumentsByEncounter($encounter_id)
    {
        $this->db->select('d.*, 
                       dt.document_type_desc, 
                       u.fullname AS uploaded_by_name');
        $this->db->from('tbl_patient_documents d');
        $this->db->join('lib_document_type dt', 'dt.document_type_id = d.document_type_id', 'left'); // join for description
        $this->db->join('aauth_users u', 'u.id = d.uploaded_by', 'left'); // join users table
        $this->db->where('d.encounter_id', $encounter_id);
        $this->db->where('d.archived', 0); // optional: skip archived documents
        $this->db->order_by('d.date_uploaded', 'DESC');

        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'getDocumentsByEncounter query failed: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }


    public function getDocumentById($document_id)
    {
        $this->db->select('d.*, t.document_type_desc, u.fullname AS uploaded_by_name');
        $this->db->from($this->table . ' d');
        $this->db->join('lib_document_type t', 't.document_type_id = d.document_type_id', 'left');
        $this->db->join('aauth_users u', 'u.id = d.uploaded_by', 'left');
        $this->db->where('d.document_id', $document_id);
        $this->db->where('d.archived', 0);
        return $this->db->get()->row_array();
    }

    public function deleteDocument($document_id)
    {
        $this->db->where('document_id', $document_id);
        return $this->db->update($this->table, ['archived' => 1]);
    }

    public function getDocumentsByPatient($patient_id, $start, $length, $search)
    {
        $this->db->select("
        SQL_CALC_FOUND_ROWS 
        d.document_id, 
        t.document_type_desc, 
        u.fullname AS uploaded_by_name,
        d.description,
        d.date_uploaded,
        d.file_path,
        CASE
            WHEN d.file_name != '' THEN d.file_name
            WHEN d.file_path != '' THEN SUBSTRING_INDEX(d.file_path, '/', -1)
            ELSE CONCAT('Document-', d.document_id)
        END AS file_name
    ", false);
        $this->db->from($this->table . ' d');
        $this->db->join('lib_document_type t', 't.document_type_id = d.document_type_id', 'left');
        $this->db->join('aauth_users u', 'u.id = d.uploaded_by', 'left');
        $this->db->where('d.patient_id', $patient_id);
        $this->db->where('d.archived', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('t.document_type_desc', $search);
            $this->db->or_like('d.description', $search);
            $this->db->or_like('d.file_name', $search);
            $this->db->or_like('u.fullname', $search);
            $this->db->group_end();
        }

        $this->db->limit($length, $start);
        $query = $this->db->get();
        $data = $query->result();

        $totalQuery = $this->db->query("SELECT FOUND_ROWS() AS total");
        $total = $totalQuery->row()->total;

        return [$data, $total];
    }
}
