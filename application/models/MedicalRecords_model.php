<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MedicalRecords_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function get_all_patients()
    {
        $query = $this->db->select('patient_id, mrn, firstname, middlename, lastname, dob, sex')
            ->from('tbl_patient_list')
            ->order_by('lastname', 'ASC')
            ->get();
        return $query->result_array();
    }

    public function get_patient_by_id($patient_id)
    {
        $this->db->select('tbl_patient_list.*, lib_suffix.suffix_desc');
        $this->db->from('tbl_patient_list');
        $this->db->join('lib_suffix', 'lib_suffix.suffix_id = tbl_patient_list.suffix_id', 'left');
        $this->db->where('tbl_patient_list.patient_id', $patient_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_patient_list_for_table($name = null, $date_from = null, $date_to = null)
    {
        $this->db->select('patient_id, mrn, firstname, middlename, lastname, suffix_id, sex, dob, street_address, date_encoded')
            ->from('tbl_patient_list')
            ->where('isActive', 1);

        // Name filter if needed
        if (!empty($name)) {
            $this->db->group_start();
            $this->db->like('firstname', $name);
            $this->db->or_like('middlename', $name);
            $this->db->or_like('lastname', $name);
            $this->db->group_end();
        }

        // Date Encoded filter
        if (!empty($date_from)) {
            $this->db->where('date_encoded >=', $date_from . ' 00:00:00');
        }
        if (!empty($date_to)) {
            $this->db->where('date_encoded <=', $date_to . ' 23:59:59');
        }

        $this->db->order_by('date_encoded', 'DESC'); // newest first
        $query = $this->db->get();
        return $query->result_array();
    }

    // Get all Clinical Notes of the patient per patient encounter
    public function get_patient_notes($patient_id)
    {
        if (empty($patient_id)) {
            return [];
        }

        $notes = [];

        // Out Patient Clinical Abstract
        $query = $this->db->query("
        SELECT pe.encounter_id, 
               pe.encounter_date,
               opca.id AS record_id,
               'Out Patient Clinical Abstract' AS note_type,
               'tbl_out_patient_clinical_abstract' AS table_name
        FROM tbl_patient_encounter pe
        JOIN tbl_out_patient_clinical_abstract opca
          ON pe.encounter_id = opca.encounter_id
        WHERE pe.patient_id = ?
    ", [$patient_id]);

        if ($query) {
            $notes = array_merge($notes, $query->result_array());
        }

        // Medical Certificate (Out-Patient)
        $query = $this->db->query("
        SELECT pe.encounter_id, 
               pe.encounter_date,
               mc.id AS record_id,
               'Medical Certificate' AS note_type,
               'tbl_medical_certificate' AS table_name
        FROM tbl_patient_encounter pe
        JOIN tbl_medical_certificate mc
          ON pe.encounter_id = mc.encounter_id
        WHERE pe.patient_id = ?
    ", [$patient_id]);

        if ($query) {
            $notes = array_merge($notes, $query->result_array());
        }

        // 🆕 Medical Certificate (In-Patient)
        $query = $this->db->query("
        SELECT pe.encounter_id, 
               pe.encounter_date,
               mci.id AS record_id,
               'Medical Certificate (In-Patient)' AS note_type,
               'tbl_medical_certificate__in_patient_' AS table_name
        FROM tbl_patient_encounter pe
        JOIN tbl_medical_certificate__in_patient_ mci
          ON pe.encounter_id = mci.encounter_id
        WHERE pe.patient_id = ?
    ", [$patient_id]);

        if ($query) {
            $notes = array_merge($notes, $query->result_array());
        }

        // Sort by encounter date descending (newest first)
        usort($notes, function ($a, $b) {
            return strtotime($b['encounter_date']) - strtotime($a['encounter_date']);
        });

        return $notes;
    }


    public function get_patient_encounters($patient_id)
    {
        $this->db->select("
        pe.encounter_id,
        pe.encounter_date,
        vt.visit_type_desc,
        CONCAT(
            d.pDoctorFirstName, ' ',
            d.pDoctorMiddleName, ' ',
            d.pDoctorLastName,
            IF(d.pDoctorSuffix IS NOT NULL AND d.pDoctorSuffix <> '', CONCAT(' ', d.pDoctorSuffix), '')
        ) AS assigned_physician_name,
        cc.chief_complaint_desc AS chief_complaint
    ");
        $this->db->from('db_emr.tbl_patient_encounter pe');
        $this->db->join('db_eclaims_staging.lib_doctors d', 'pe.assigned_physician = d.doctor_id', 'left');
        $this->db->join('db_emr.lib_chief_complaint cc', 'pe.chief_complaint = cc.chief_complaint_id', 'left');
        $this->db->join('db_emr.lib_visit_type vt', 'pe.visit_type_id = vt.visit_type_id', 'left'); // join visit type
        $this->db->where('pe.patient_id', $patient_id);
        $this->db->order_by('pe.encounter_date', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_visit_types()
    {
        return $this->db->select('visit_type_id, visit_type_desc')
            ->from('db_emr.lib_visit_type')
            ->get()
            ->result_array();
    }
}
