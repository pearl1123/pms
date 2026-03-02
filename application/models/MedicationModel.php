<?php
/*by Pearlsss 02072025*/
defined('BASEPATH') or exit('No direct script access allowed');

class MedicationModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // ADD MEDICATION
    public function addMedication($data)
    {
        if ($this->db->insert('tbl_patient_medications', $data)) {
            return $this->db->insert_id(); // return last inserted ID
        }
        return false;
    }

    // UPDATE MEDICATION
    public function updateMedication($data)
    {
        if (!isset($data['medication_id'])) return false;

        $this->db->where('medication_id', $data['medication_id']);
        unset($data['medication_id']); // remove primary key from update set
        return $this->db->update('tbl_patient_medications', $data);
    }

    public function getMedicationsByEncounter($encounter_id)
    {
        $this->db->select('
        m.*,
        g.generic_name,
        d.dosage_desc,
        f.frequency_desc,
        r.route_desc,
        du.duration_desc
    ');
        $this->db->from('tbl_patient_medications m');

        // ✅ Correct join to pcsr_generic_info (actual generic source)
        $this->db->join('isms_db.pcsr_generic_info g', 'g.id = m.generic_id', 'left');

        $this->db->join('lib_dosage d', 'd.dosage_id = m.dosage_id', 'left');
        $this->db->join('lib_frequency f', 'f.frequency_id = m.frequency_id', 'left');
        $this->db->join('lib_route r', 'r.route_id = m.route_id', 'left');
        $this->db->join('lib_duration du', 'du.duration_id = m.duration_id', 'left');

        $this->db->where('m.encounter_id', $encounter_id);
        $this->db->where('m.archived', 0);
        $this->db->order_by('m.date_prescribed', 'DESC');

        $query = $this->db->get();

        // 🧩 Debugging for SQL errors
        if (!$query) {
            $error = $this->db->error();
            log_message('error', '❌ SQL FAILED in getMedicationsByEncounter: ' . $this->db->last_query());
            log_message('error', '❌ DB ERROR: ' . print_r($error, true));

            echo "<pre>";
            print_r($error);
            echo "</pre>";
            exit;
        }

        return $query->result();
    }


    // SAVE MULTIPLE MEDICATIONS (Batch Insert + Update in one call)
    public function saveMedications($patient_id, $encounter_id, $doctor_id, $encoded_by, $medications)
    {
        $this->db->trans_start();

        $insert_data = [];
        $update_data = [];

        foreach ($medications as $med) {
            $data = [
                'patient_id'      => $patient_id,
                'encounter_id'    => $encounter_id,
                'doctor_id'       => $doctor_id,
                'date_prescribed' => isset($med['date_prescribed']) ? $med['date_prescribed'] : date('Y-m-d'),
                'medicine_id'     => $med['medicine_id'],
                'dosage_id'       => $med['dosage_id'],
                'frequency_id'    => $med['frequency_id'],
                'route_id'        => $med['route_id'],
                'duration_id'     => $med['duration_id'],
                'remarks'         => isset($med['remarks']) ? $med['remarks'] : null
            ];

            if (!empty($med['medication_id'])) {
                // Existing medication → update
                $data['medication_id']    = $med['medication_id'];
                $data['date_last_modified'] = date("Y-m-d H:i:s");
                $data['last_modified_by']   = $encoded_by;
                $update_data[] = $data;
            } else {
                // New medication → insert
                $data['date_encoded'] = date("Y-m-d H:i:s");
                $data['encoded_by']   = $encoded_by;
                $insert_data[] = $data;
            }
        }

        // Batch insert new meds
        if (!empty($insert_data)) {
            $this->db->insert_batch('tbl_patient_medications', $insert_data);
        }

        // Batch update existing meds
        if (!empty($update_data)) {
            $this->db->update_batch('tbl_patient_medications', $update_data, 'medication_id');
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    // GET SINGLE MEDICATION
    public function getMedicationById($medication_id)
    {
        $this->db->select('m.*,
                           med.medicine_name,
                           d.dosage_desc,
                           f.frequency_desc,
                           r.route_desc,
                           du.duration_desc');
        $this->db->from('tbl_patient_medications m');
        $this->db->join('lib_medicine med', 'med.medicine_id = m.medicine_id', 'left');
        $this->db->join('lib_dosage d', 'd.dosage_id = m.dosage_id', 'left');
        $this->db->join('lib_frequency f', 'f.frequency_id = m.frequency_id', 'left');
        $this->db->join('lib_route r', 'r.route_id = m.route_id', 'left');
        $this->db->join('lib_duration du', 'du.duration_id = m.duration_id', 'left');
        $this->db->where('m.medication_id', $medication_id);
        $this->db->where('m.archived', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // DELETE (archive) MEDICATION
    public function deleteMedication($medication_id)
    {
        $this->db->where('medication_id', $medication_id);
        return $this->db->update('tbl_patient_medications', [
            'archived' => 1,
            'date_last_modified' => date("Y-m-d H:i:s")
        ]);
    }

    // GET ALL MEDICATIONS BY PATIENT
    public function getMedicationsByPatient($patient_id)
    {
        $this->db->select('m.*, 
                           med.medicine_name,
                           d.dosage_desc,
                           f.frequency_desc,
                           r.route_desc,
                           du.duration_desc');
        $this->db->from('tbl_patient_medications m');
        $this->db->join('lib_medicine med', 'med.medicine_id = m.medicine_id', 'left');
        $this->db->join('lib_dosage d', 'd.dosage_id = m.dosage_id', 'left');
        $this->db->join('lib_frequency f', 'f.frequency_id = m.frequency_id', 'left');
        $this->db->join('lib_route r', 'r.route_id = m.route_id', 'left');
        $this->db->join('lib_duration du', 'du.duration_id = m.duration_id', 'left');
        $this->db->where('m.patient_id', $patient_id);
        $this->db->where('m.archived', 0);
        $query = $this->db->get();
        return $query->result_array();
    }
}
