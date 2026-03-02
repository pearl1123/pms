<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeathModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function saveDeathCertificate($data)
    {
        $deathData = [
            'patient_id' => $data['patient_id'] ?? null,
            'certificate_no' => $this->generateCertificateNumber(),
            'firstname' => $data['firstname'] ?? null,
            'middlename' => $data['middlename'] ?? null,
            'lastname' => $data['lastname'] ?? null,
            'sex' => $data['sex'] ?? null,
            'date_of_death' => $data['date_of_death'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'civil_status' => $data['civil_status'] ?? null,
            'place_of_death' => $data['place_of_death'] ?? null,
            'religion' => $data['religion'] ?? null,
            'citizenship' => $data['citizenship'] ?? null,
            'residence_address' => $data['residence_address'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'father_name' => $data['father_name'] ?? null,
            'mother_maiden_name' => $data['mother_maiden_name'] ?? null,
            'immediate_cause' => $data['immediate_cause'] ?? null,
            'antecedent_cause' => $data['antecedent_cause'] ?? null,
            'underlying_cause' => $data['underlying_cause'] ?? null,
            'manner_of_death' => $data['manner_of_death'] ?? null,
            'autopsy_performed' => $data['autopsy_performed'] ?? 0,
            'physician_name' => $data['physician_name'] ?? null,
            'disposal_method' => $data['disposal_method'] ?? null,
            'burial_permit_no' => $data['burial_permit_no'] ?? null,
            'informant_name' => $data['informant_name'] ?? null,
            'informant_relationship' => $data['informant_relationship'] ?? null,
            'encoded_by' => $this->session->userID ?? null,
            'date_encoded' => date('Y-m-d H:i:s'),
            'last_modified_by' => $this->session->userID ?? null,
            'date_last_modified' => date('Y-m-d H:i:s'),
            'additional_data' => $data['additional_data'] ?? '{}'
        ];

        if (!empty($data['death_cert_id'])) {
            $this->db->where('death_cert_id', $data['death_cert_id']);
            $success = $this->db->update('tbl_death_certificate', $deathData);
        } else {
            $success = $this->db->insert('tbl_death_certificate', $deathData);
        }

        // Debug log
        log_message('error', 'Death cert save error: ' . json_encode($this->db->error()));
        return $success;
    }

    private function generateCertificateNumber()
    {
        $prefix = 'DC-' . date('Y') . '-';
        $this->db->select_max('certificate_no');
        $this->db->like('certificate_no', $prefix, 'after');
        $result = $this->db->get('tbl_death_certificate')->row();

        if ($result && $result->certificate_no) {
            $last_num = (int)substr($result->certificate_no, strlen($prefix));
            return $prefix . str_pad($last_num + 1, 5, '0', STR_PAD_LEFT);
        }
        return $prefix . '00001';
    }

    public function getDeathCertificate($death_cert_id)
    {
        $this->db->where('death_cert_id', $death_cert_id);
        $certificate = $this->db->get('tbl_death_certificate')->row();

        if ($certificate && isset($certificate->additional_data)) {
            $additional_data = json_decode($certificate->additional_data, true);
            foreach ($additional_data as $key => $value) {
                $certificate->$key = $value;
            }
        }
        return $certificate;
    }

    public function getByPatientId($patient_id)
    {
        // Check if there's already a death certificate
        $this->db->select('
        dc.death_cert_id,
        dc.*,
        dc.date_of_death,
        ms.marital_status_desc,
        r.religion_desc,
        n.nationality_desc,
        o.occupation_desc,
        b.barangay_desc,
        c.city_desc,
        pr.province_desc,
        rg.region_desc,
         p.occupation,
        p.street_address,
        p.barangay, p.city, p.province, p.region,
        dc.father_name,
        dc.mother_maiden_name,
        dc.other_significant_conditions,
        b.barangay_desc
    ')
            ->from('tbl_death_certificate dc')
            ->join('tbl_patient_list p', 'dc.patient_id = p.patient_id', 'left')
            ->join('lib_marital_status ms', 'dc.civil_status = ms.marital_status_id', 'left')
            ->join('lib_religion r', 'dc.religion = r.religion_id', 'left')
            ->join('lib_nationality n', 'dc.citizenship = n.nationality_id', 'left')
            ->join('lib_occupation o', 'CAST(dc.occupation AS SIGNED) = o.occupation_id', 'left')
            ->join('lib_barangay b', 'dc.barangay = b.barangay_id', 'left')
            ->join('lib_cities_mun c', 'dc.city = c.city_id', 'left')
            ->join('lib_province pr', 'dc.province = pr.province_id', 'left')
            ->join('lib_region rg', 'dc.region = rg.region_id', 'left')
            ->where('dc.patient_id', $patient_id)
            ->where('dc.archived', 0)
            ->order_by('dc.death_cert_id', 'DESC')
            ->limit(1);

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->row();
        }

        // Fallback from patient list
        $this->db->select('
        p.patient_id,
        p.firstname, p.middlename, p.lastname, p.sex, p.dob AS date_of_birth,
        p.marital_status AS civil_status,
        ms.marital_status_desc,
        r.religion_desc,
        n.nationality_desc,
        o.occupation_desc,
        p.occupation,
        p.street_address,
        p.barangay, p.city, p.province, p.region,
        p.name_of_father AS father_name,
        p.name_of_mother AS mother_maiden_name,
        b.barangay_desc,
        c.city_desc,
        pr.province_desc,
        rg.region_desc
    ')
            ->from('tbl_patient_list p')
            ->join('lib_marital_status ms', 'p.marital_status = ms.marital_status_id', 'left')
            ->join('lib_religion r', 'p.religion = r.religion_id', 'left')
            ->join('lib_nationality n', 'p.nationality = n.nationality_id', 'left')
            ->join('lib_occupation o', 'CAST(p.occupation AS SIGNED) = o.occupation_id', 'left')
            ->join('lib_barangay b', 'p.barangay = b.barangay_id', 'left')
            ->join('lib_cities_mun c', 'p.city = c.city_id', 'left')
            ->join('lib_province pr', 'p.province = pr.province_id', 'left')
            ->join('lib_region rg', 'p.region = rg.region_id', 'left')
            ->where('p.patient_id', $patient_id)
            ->where('p.archived', 0);

        $query = $this->db->get();
        return $query && $query->num_rows() > 0 ? $query->row() : null;
    }

    public function getAllNationality($id = null)
    {
        if ($id) {
            return $this->db->get_where('lib_nationality', array('nationality_id' => $id, 'archived' => 0))->row();
        }

        return "";
    }

     public function getAllReligion($id = null)
    {
        if ($id) {
            return $this->db->get_where('lib_religion', array('religion_id' => $id, 'archived' => 0))->row();
        }

        return "";
    }

     public function getAllOccupation($id = null)
    {
        if ($id) {
            return $this->db->get_where('lib_occupation', array('occupation_id' => $id, 'archived' => 0))->row();
        }

        return "";
    }

}
