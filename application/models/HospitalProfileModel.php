<?php
    class HospitalProfileModel extends CI_Model
    {
         public function __construct()
        {
            parent::__construct();
        }

        // GET HOSPITAL GENERAL INFORMATION
        // ========================================================
        public function getHospitalGeneralInformation()
        {
            $this->db->where(array('t1.isArchived'=>0));
            $this->db->select('t1.hospital_profile_name, t1.hospital_profile_address, t1.hospital_profile_contact, t1.hospital_profile_fax, t1.hospital_profile_email, 
                               t1.hospital_profile_region, t1.hospital_profile_province, t1.hospital_profile_municipality,
                               t2.region_desc, t3.province_desc, t4.city_desc');
            $this->db->join('lib_region t2', 't2.region_id = t1.hospital_profile_region', 'left');
            $this->db->join('lib_province t3', 't3.province_id = t1.hospital_profile_province', 'left');
            $this->db->join('lib_cities_mun t4', 't4.city_id = t1.hospital_profile_municipality', 'left');
            return $this->db->get_where('lib_hospital_profile t1')->result();
        }

        // GET HOSPITAL CLASSIFICATION
        // ========================================================
        public function getHospitalClassification()
        {return $this->db->get_where('lib_hospital_classification', array('isArchived'=>0))->result();}

        // GET HOSPITAL QMS
        // ========================================================
        public function getHospitalQMS()
        {return $this->db->get_where('lib_hospital_qms', array('isArchived'=>0))->result();}

        // SAVE HOSPITAL GENERAL INFORMATION
        // ========================================================
        public function saveGeneralInformation($genInfoData)
        {
            $rows = $this->db->get('lib_hospital_profile')->num_rows();
            
            if($rows > 0)
            {$this->db->update('lib_hospital_profile', array('isArchived' => 1));}

            $this->db->insert('lib_hospital_profile', $genInfoData);
            return $this->db->insert_id();
        }

        // SAVE HOSPITAL CLASSIFICATION
        // ========================================================
        public function saveClassification($classificationData)
        {
            $rows = $this->db->get('lib_hospital_classification')->num_rows();
            
            if($rows > 0)
            {$this->db->update('lib_hospital_classification', array('isArchived' => 1));}

            $this->db->insert('lib_hospital_classification', $classificationData);
            return $this->db->insert_id();
        }

        // SAVE HOSPITAL QMS
        // ========================================================
        public function saveQMS($qmsData)
        {
            $rows = $this->db->get('lib_hospital_qms')->num_rows();
            
            if($rows > 0)
            {$this->db->update('lib_hospital_qms', array('isArchived' => 1));}

            $this->db->insert('lib_hospital_qms', $qmsData);
            return $this->db->insert_id();
        }
    }
?>