<?php
/*by Pearlsss 02072025*/
class AppointmentModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        // Load the PatientModel so we can use its functions
        $this->load->model('PatientModel');
    }

    public function saveAppointment($data)
    {
        // Remove encounter_id if empty string or not set
        if (!isset($data['encounter_id']) || $data['encounter_id'] === '') {
            unset($data['encounter_id']);
        }

        // Insert into database
        if ($this->db->insert('tbl_patient_appointment', $data)) {
            return ['status' => 'success', 'message' => 'Appointment saved successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Database error: ' . $this->db->error()['message']];
        }
    }

    public function updateAppointment($appointment_id, $data)
    {
        $this->db->where('appointment_id', $appointment_id);
        return $this->db->update('tbl_patient_appointment', $data);
    }

    // // Nurse approval
    //     public function nurseApproveAppointment($appointment_id, $timestamp) {
    //         $this->db->where('appointment_id', $appointment_id);
    //         $this->db->update('tbl_patient_appointment', [
    //             'nurse_approved' => 1,
    //             'nurse_approved_at' => $timestamp
    //         ]);

    //         return $this->db->affected_rows() > 0;
    //     }


    //     // Patient approval
    //     public function patientApproveAppointment($appointment_id, $timestamp) {
    //         $this->db->where('appointment_id', $appointment_id);
    //         $this->db->update('tbl_patient_appointment', [
    //             'patient_approved' => 1,
    //             'patient_approved_at' => $timestamp
    //         ]);

    //         return $this->db->affected_rows() > 0;
    //     }

    // Finalize appointment
    public function finalizeAppointment($appointment_id)
    {
        $this->db->trans_start();

        // Fetch appointment details
        $appointment = $this->db
            ->get_where('tbl_patient_appointment', ['appointment_id' => $appointment_id])
            ->row();

        if (!$appointment) {
            return ['status' => 'error', 'message' => 'Appointment not found.'];
        }

        // Check if already finalized
        if ($appointment->appoint_status_id == 2) {
            return ['status' => 'already_finalized', 'message' => 'This appointment is already confirmed.'];
        }

        // Check active visit
        $active_visit_exists = $this->db->select('encounter_id')
            ->from('tbl_patient_encounter')
            ->where('patient_id', $appointment->patient_id)
            ->where('is_current', 1)
            ->where('archived', 0)
            ->get()
            ->num_rows() > 0;

        if ($active_visit_exists) {
            return ['status' => 'active_visit_exists', 'message' => 'Patient already has an active visit. Please close it before finalizing a new one.'];
        }

        // Update appointment
        $this->db->where('appointment_id', $appointment_id);
        $this->db->update('tbl_patient_appointment', [
            'appoint_status_id' => 2,
            'finalized_at' => date('Y-m-d H:i:s')
        ]);

        // Insert encounter
        $chief_complaint = $appointment->chief_complaint_id ?? 'N/A';
        $encounter_date = trim($appointment->appointment_date . ' ' . $appointment->appointment_time_start);
        if (empty($encounter_date) || $encounter_date == ' ') {
            $encounter_date = date('Y-m-d H:i:s');
        }

        $this->db->insert('tbl_patient_encounter', [
            'patient_id' => $appointment->patient_id,
            'encounter_date' => $encounter_date,
            'visit_type_id' => 1,
            'assigned_physician' => $appointment->doctor_id,
            'chief_complaint' => $chief_complaint,
            'arrival_mode_id' => 3,
            'visit_status_id' => 1,
            'date_encoded' => date('Y-m-d H:i:s'),
            'medicine' => 1,
            'surgical' => 0,
            'encoded_by' => $this->session->userdata('userID'),
            'archived' => 0,
            'is_current' => 1
        ]);

        $encounter_id = $this->db->insert_id();

        $this->db->where('appointment_id', $appointment_id)
            ->update('tbl_patient_appointment', ['encounter_id' => $encounter_id]);

        if ($this->db->affected_rows() == 0) {
            $this->db->trans_rollback();
            return ['status' => 'error', 'message' => 'Failed to create patient visit.'];
        }

        $this->db->trans_complete();

        // Return appointment as object for controller email use
        return [
            'status' => 'success',
            'message' => 'Appointment finalized and patient visit created.',
            'appointment' => $appointment // so controller can access doctor_id, patient_id, etc.
        ];
    }

    public function getAppointmentsByPatientId($patient_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_patient_appointment');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('archived', 0);
        $query = $this->db->get();
        return $query->result();
    }


    public function archiveAppointment($appointment_id)
    {
        $this->db->where('appointment_id', $appointment_id);
        $this->db->update('tbl_patient_appointment', ['archived' => 1]);

        if ($this->db->affected_rows() > 0) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Appointment not found or already archived'];
        }
    }



    // Get latest appointment by patient
    public function getLatestAppointmentByPatient($patient_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_patient_appointment');
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('appointment_date', 'DESC'); // Latest first
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->row_array(); // Return only one latest record
        } else {
            return null;
        }
    }

    // Get all appointments by patient ID
    public function getAppointmentsByPatient($patient_id)
    {
        $this->load->model('AppointmentModel');

        $appointments = $this->AppointmentModel->getAppointmentsByPatient($patient_id);

        echo json_encode([
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($appointments),
            "recordsFiltered" => count($appointments),
            "data" => $appointments
        ]);
    }

    public function getAppointmentById($appointment_id)
    {
        $this->db->select('a.*');
        $this->db->from('tbl_patient_appointment a');
        $this->db->where('a.appointment_id', $appointment_id);
        $this->db->where('a.archived', 0);

        $query = $this->db->get();

        if (!$query) {
            // Show last query and DB error in logs
            log_message('error', 'DB Query Failed: ' . $this->db->last_query());
            log_message('error', 'DB Error: ' . print_r($this->db->error(), true));
            return false;
        }

        return $query->row_array();
    }
}
