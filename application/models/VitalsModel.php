<?php 
/*by Pearlsss 02072025*/
class VitalsModel extends CI_Model{

    public function addVitals($data)
    {
        $this->db->insert('tbl_patient_vitals', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id(); // Return the new vitals_id
        } else {
            return false;
        }
    }
    
    public function updateVitals($data) {
        $vitals_id = $data['vitals_id'];
        unset($data['vitals_id']);
        
        $data['date_last_modified'] = date('Y-m-d H:i:s');
        $data['last_modified_by'] = $this->session->userdata('user_id');
        
        $this->db->where('vitals_id', $vitals_id);
        $this->db->update('tbl_patient_vitals', $data);
        
        return ['status' => 'success', 'message' => 'Vitals updated successfully'];
    }
    
    public function getVital($vitals_id) {
        $this->db->where('vitals_id', $vitals_id);
        $query = $this->db->get('tbl_patient_vitals');
        return $query->row_array();
    }
    
    public function deleteVital($vitals_id) {
        $this->db->where('vitals_id', $vitals_id);
        $this->db->delete('tbl_patient_vitals');
        
        return ['status' => 'success', 'message' => 'Vitals deleted successfully'];
    }

public function get_latest_vitals_by_patient($patient_id) {
    $this->db->select('*');
    $this->db->from('tbl_patient_vitals');
    $this->db->where('patient_id', $patient_id);
    $this->db->order_by('vitals_date', 'DESC'); // Latest first
    $this->db->limit(1);
    $query = $this->db->get();

    if ($query && $query->num_rows() > 0) {
        return $query->row_array(); // return only one latest record
    } else {
        return null;
    }
}
    // Get the most recent active encounter for a patient
public function getCurrentEncounter($patient_id) {
    $this->db->select('encounter_id')
             ->where('patient_id', $patient_id)
             ->where('encounter_status', 1) // 1 = ongoing
             ->order_by('encounter_date', 'DESC')
             ->limit(1);
    $query = $this->db->get('tbl_patient_encounter');
    return $query->row() ? $query->row()->encounter_id : null;
}

// Set current encounter (optional)
public function setCurrentEncounter($patient_id, $encounter_id) {
    $this->db->where('patient_id', $patient_id)
             ->update('tbl_patient_encounter', 
                      ['is_current' => 0]); // Reset all
    
    $this->db->where('encounter_id', $encounter_id)
             ->update('tbl_patient_encounter',
                      ['is_current' => 1]);
}

public function getVitalsByPatient($patient_id) {
    $this->db->where('patient_id', $patient_id);
    $query = $this->db->get('tbl_patient_vitals');
    return $query->result_array();
}

public function getVitalById($vitals_id) {
    $this->db->where('vitals_id', $vitals_id);
    $query = $this->db->get('tbl_patient_vitals');  // Replace 'vital_signs' with your actual table name
    
    // Log the query for debugging
    log_message('error', 'SQL Query: ' . $this->db->last_query());
    
    if ($query->num_rows() > 0) {
        return $query->row();  // Return the first row (as we're expecting a single record)
    } else {
        return null;  // Return null if no data found
    }
}

// In PatientModel.php
public function updateVital($vitals_id, $data) {
    if (empty($vitals_id)) {
        return false; // Invalid ID
    }

    $this->db->where('vitals_id', $vitals_id);
    $success = $this->db->update('tbl_patient_vitals', $data);

    return $success; // TRUE if no SQL error
}

public function getVitalsById($vitals_id) {
    $this->db->where('vitals_id', $vitals_id);
    $query = $this->db->get('tbl_patient_vitals'); // Use your table name here

    if ($query->num_rows() > 0) {
        return $query->row_array(); // Return the row as an associative array
    } else {
        return null; // No record found
    }
}


public function checkDuplicateVitals($patient_id, $encounter_id, $vitals_date)
{
    $this->db->select('vitals_id');
    $this->db->from('tbl_patient_vitals');
    $this->db->where('patient_id', $patient_id);
    $this->db->where('encounter_id', $encounter_id);
    $this->db->where('vitals_date', $vitals_date);  // Ensure you're checking the exact datetime
    $query = $this->db->get();
    
    if ($query->num_rows() > 0) {
        return true;  // Duplicate found
    } else {
        return false; // No duplicate
    }
}


}

?>