<?php
/*by Pearlsss 03072025*/
defined('BASEPATH') or exit('No direct script access allowed');

class ImmunizationModel extends CI_Model
{

    protected $table = 'tbl_patient_immunizations';

    protected $fields = [
        'patient_id',
        'encounter_id',
        'given_by',
        'date_given',
        'vaccine_id',
        'lot_number',
        'dose_id',
        'route_id',
        'site_id',
        'remarks',
        'date_encoded',
        'encoded_by',
        'date_last_modified',
        'last_modified_by'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    private function filterFields($data)
    {
        // Keep keys even if NULL, for route_id etc.
        $clean = [];
        foreach ($this->fields as $field) {
            $clean[$field] = array_key_exists($field, $data) ? $data[$field] : null;
        }
        return $clean;
    }

    public function addImmunization($data)
    {
        $data = $this->filterFields($data);
        if ($this->db->insert($this->table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function updateImmunization($data)
    {
        if (!isset($data['immunization_id'])) return false;
        $id = $data['immunization_id'];
        unset($data['immunization_id']);
        $data = $this->filterFields($data);
        $this->db->where('immunization_id', $id);
        return $this->db->update($this->table, $data);
    }

    public function getImmunizationsByEncounter($encounter_id)
    {
        try {
            $this->db->select('
            i.immunization_id,
            i.patient_id,
            i.encounter_id,
            i.lot_number,
            i.date_given,
            i.given_by,
            u.fullname AS given_by_name,
            i.remarks,
            i.date_encoded,
            i.encoded_by,
            i.date_last_modified,
            i.last_modified_by,
            i.archived,
            v.vaccine_name,
            ds.dose_desc,
            r.route_desc,
            s.site_desc
        ');
            $this->db->from('tbl_patient_immunizations i');

            // Join lookup tables for human-readable descriptions
            $this->db->join('lib_vaccine v', 'v.vaccine_id = i.vaccine_id', 'left');
            $this->db->join('lib_dose ds', 'ds.dose_id = i.dose_id', 'left');
            $this->db->join('lib_route r', 'r.route_id = i.route_id', 'left');
            $this->db->join('lib_site s', 's.site_id = i.site_id', 'left');

            // Join user table for fullname
            $this->db->join('aauth_users u', 'u.id = i.given_by', 'left');

            $this->db->where('i.encounter_id', $encounter_id);
            $this->db->order_by('i.date_given', 'DESC');

            $query = $this->db->get();

            if (!$query) {
                log_message('error', 'DB error in getImmunizationsByEncounter(): ' . print_r($this->db->error(), true));
                return [];
            }

            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'Exception in getImmunizationsByEncounter(): ' . $e->getMessage());
            return [];
        }
    }





    public function getImmunizationById($immunization_id)
    {
        $this->db->select('i.*,
                           v.vaccine_name,
                           d.dose_desc,
                           r.route_desc,
                           s.site_desc,
                           u.fullname AS given_by_name');
        $this->db->from($this->table . ' i');
        $this->db->join('lib_vaccine v', 'v.vaccine_id = i.vaccine_id', 'left');
        $this->db->join('lib_dose d', 'd.dose_id = i.dose_id', 'left');
        $this->db->join('lib_route r', 'r.route_id = i.route_id', 'left');
        $this->db->join('lib_site s', 's.site_id = i.site_id', 'left');
        $this->db->join('aauth_users u', 'u.id = i.given_by', 'left');
        $this->db->where('i.immunization_id', $immunization_id);
        $this->db->where('i.archived', 0);
        return $this->db->get()->row_array();
    }

    public function deleteImmunization($immunization_id)
    {
        $this->db->where('immunization_id', $immunization_id);
        return $this->db->update($this->table, ['archived' => 1]);
    }

    public function getImmunizationsByPatient($patient_id)
    {
        $this->db->select('i.*,
                           v.vaccine_name,
                           d.dose_desc,
                           r.route_desc,
                           s.site_desc,
                           u.fullname AS given_by_name');
        $this->db->from($this->table . ' i');
        $this->db->join('lib_vaccine v', 'v.vaccine_id = i.vaccine_id', 'left');
        $this->db->join('lib_dose d', 'd.dose_id = i.dose_id', 'left');
        $this->db->join('lib_route r', 'r.route_id = i.route_id', 'left');
        $this->db->join('lib_site s', 's.site_id = i.site_id', 'left');
        $this->db->join('aauth_users u', 'u.id = i.given_by', 'left');
        $this->db->where('i.patient_id', $patient_id);
        $this->db->where('i.archived', 0);
        $this->db->order_by('i.date_given', 'DESC');
        return $this->db->get()->result_array();
    }
}
