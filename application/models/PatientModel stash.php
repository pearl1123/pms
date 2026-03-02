<?php 
/*by Pearlsss 02072025*/
class PatientModel extends CI_Model{
    public function __construct() {
        $this->eclaims_db = $this->load->database('eclaims', TRUE);
        parent::__construct();
        
    }
    public function savePatientRegistration($patientData){
        $this->db->insert('tbl_patient_list',$patientData);
        return $this->db->insert_id();
    }

    public function updatePatientRegistration($patient_id, $patientData)
    {
        $this->db->where('patient_id', $patient_id);
        return $this->db->update('tbl_patient_list', $patientData);
    }

    public function getPatientById($patient_id)
    {
        return $this->db->get_where('tbl_patient_list', ['patient_id' => $patient_id])->row();
    }


    public function getPatientList($start,$length,$search){
        $this->db->select('t1.patient_id');
        $this->db->where(array('archived'=>0));
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $num = $this->db->get('tbl_patient_list t1')->num_rows();
        

        $this->db->select("*,t2.fullname,l1.region_desc,l2.province_desc,l3.city_desc,l4.barangay_desc");
        $this->db->where(array("t1.archived"=>0));
        if($length > 0){
            $this->db->limit($length,$start);
        }

        $this->db->join('lib_region l1','t1.region = l1.region_id','left');
        $this->db->join('lib_province l2','t1.province = l2.province_id','left');
        $this->db->join('lib_cities_mun l3','t1.city = l3.city_id','left');
        $this->db->join('lib_barangay l4','t1.barangay = l4.barangay_id','left');
        $this->db->join('aauth_users t2','t1.encoded_by = t2.id','left');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $res = $this->db->get_where('tbl_patient_list t1',array('t1.archived'=>0))->result();

        return array($res,$num);
    }

    public function getPatientListAll($start,$length,$search){
        $this->db->select('t1.patient_id');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $num = $this->db->get('tbl_patient_list t1')->num_rows();
        

        $this->db->select("*,t2.fullname,l1.region_desc,l2.province_desc,l3.city_desc,l4.barangay_desc");
        $this->db->where(array("t1.archived"=>0));
        if($length > 0){
            $this->db->limit($length,$start);
        }

        $this->db->join('lib_region l1','t1.region = l1.region_id','left');
        $this->db->join('lib_province l2','t1.province = l2.province_id','left');
        $this->db->join('lib_cities_mun l3','t1.city = l3.city_id','left');
        $this->db->join('lib_barangay l4','t1.barangay = l4.barangay_id','left');
        $this->db->join('aauth_users t2','t1.encoded_by = t2.id','left');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $res = $this->db->get('tbl_patient_list t1')->result();

        return array($res,$num);
    }

    public function getOutPatientList($start,$length,$search){
        $this->db->select('t1.patient_id');
        $this->db->where(array('archived'=>0));
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $num = $this->db->get('tbl_patient_list t1')->num_rows();
        

        $this->db->select("*,t2.fullname,l1.region_desc,l2.province_desc,l3.city_desc,l4.barangay_desc");
        $this->db->where(array("t1.archived"=>0));
        if($length > 0){
            $this->db->limit($length,$start);
        }

        $this->db->join('lib_region l1','t1.region = l1.region_id','left');
        $this->db->join('lib_province l2','t1.province = l2.province_id','left');
        $this->db->join('lib_cities_mun l3','t1.city = l3.city_id','left');
        $this->db->join('lib_barangay l4','t1.barangay = l4.barangay_id','left');
        $this->db->join('aauth_users t2','t1.encoded_by = t2.id','left');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $res = $this->db->get_where('tbl_patient_list t1',array('t1.archived'=>0))->result();

        return array($res,$num);
    }

    public function getOutPatientListAll($start,$length,$search){
        $this->db->select('t1.patient_id');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $num = $this->db->get('tbl_patient_list t1')->num_rows();
        

        $this->db->select("*,t2.fullname,l1.region_desc,l2.province_desc,l3.city_desc,l4.barangay_desc");
        $this->db->where(array("t1.archived"=>0));
        if($length > 0){
            $this->db->limit($length,$start);
        }

        $this->db->join('lib_region l1','t1.region = l1.region_id','left');
        $this->db->join('lib_province l2','t1.province = l2.province_id','left');
        $this->db->join('lib_cities_mun l3','t1.city = l3.city_id','left');
        $this->db->join('lib_barangay l4','t1.barangay = l4.barangay_id','left');
        $this->db->join('aauth_users t2','t1.encoded_by = t2.id','left');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $res = $this->db->get('tbl_patient_list t1')->result();

        return array($res,$num);
    }

    public function getERPatientList($start,$length,$search){
        $this->db->select('t1.patient_id');
        $this->db->where(array('archived'=>0));
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $num = $this->db->get('tbl_patient_list t1')->num_rows();
        

        $this->db->select("*,t2.fullname,l1.region_desc,l2.province_desc,l3.city_desc,l4.barangay_desc");
        $this->db->where(array("t1.archived"=>0));
        if($length > 0){
            $this->db->limit($length,$start);
        }

        $this->db->join('lib_region l1','t1.region = l1.region_id','left');
        $this->db->join('lib_province l2','t1.province = l2.province_id','left');
        $this->db->join('lib_cities_mun l3','t1.city = l3.city_id','left');
        $this->db->join('lib_barangay l4','t1.barangay = l4.barangay_id','left');
        $this->db->join('aauth_users t2','t1.encoded_by = t2.id','left');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $res = $this->db->get_where('tbl_patient_list t1',array('t1.archived'=>0))->result();

        return array($res,$num);
    }

    public function getERPatientListAll($start,$length,$search){
        $this->db->select('t1.patient_id');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $num = $this->db->get('tbl_patient_list t1')->num_rows();
        

        $this->db->select("*,t2.fullname,l1.region_desc,l2.province_desc,l3.city_desc,l4.barangay_desc");
        $this->db->where(array("t1.archived"=>0));
        if($length > 0){
            $this->db->limit($length,$start);
        }

        $this->db->join('lib_region l1','t1.region = l1.region_id','left');
        $this->db->join('lib_province l2','t1.province = l2.province_id','left');
        $this->db->join('lib_cities_mun l3','t1.city = l3.city_id','left');
        $this->db->join('lib_barangay l4','t1.barangay = l4.barangay_id','left');
        $this->db->join('aauth_users t2','t1.encoded_by = t2.id','left');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $res = $this->db->get('tbl_patient_list t1')->result();

        return array($res,$num);
    }

    public function getPossibleDups($start,$length,$search,$dups){
        $this->db->select('t1.patient_id');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $this->db->where_in('patient_id',$dups);
        $num = $this->db->get('tbl_patient_list t1')->num_rows();
        

        $this->db->select("*,t2.fullname,l1.region_desc,l2.province_desc,l3.city_desc,l4.barangay_desc");
        $this->db->where(array("t1.archived"=>0));
        if($length > 0){
            $this->db->limit($length,$start);
        }

        $this->db->join('lib_region l1','t1.region = l1.region_id','left');
        $this->db->join('lib_province l2','t1.province = l2.province_id','left');
        $this->db->join('lib_cities_mun l3','t1.city = l3.city_id','left');
        $this->db->join('lib_barangay l4','t1.barangay = l4.barangay_id','left');
        $this->db->join('aauth_users t2','t1.encoded_by = t2.id','left');
        if(isset($search)){
            $this->db->like('t1.firstname',$search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $this->db->where_in('patient_id',$dups);
        $res = $this->db->get('tbl_patient_list t1')->result();

        return array($res,$num);
    }

    public function getLastPatientMRN(){
        $this->db->select('MAX(patient_id) as max_id');
        $lastRec = $this->db->get('tbl_patient_list')->row();

        return $this->db->get('tbl_patient_list',array('patient_id'=>$lastRec->max_id))->row();
    }

    public function getAllTitles(){
        return $this->db->get_where('lib_title',array('archived'=>0))->result();
    }

    public function getAllSuffix(){
        return $this->db->get_where('lib_suffix',array('archived'=>0))->result();
    }

    public function getAllNationality() {
        return $this->db->get_where('lib_nationality',array('archived'=>0))->result();
    }
    
    public function getAllMStatus(){
        return $this->db->get_where('lib_marital_status',array('archived'=>0))->result();
    }

    public function getAllReligion(){
        return $this->db->get_where('lib_religion', array('archived'=>0))->result();
    }

    public function getAllOccupation(){
        return $this->db->get_where('lib_occupation',array('archived'=>0))->result();
    }
    
    public function getAllRegion(){
        return $this->db->get_where('lib_region', array('archived'=>0))->result();
    }

    public function getAllRelToPatient(){
        return $this->db->get_where('lib_rel_to_patient',array('archived' => 0))->result();
    }

    public function getProvinces($region_id){
        return $this->db->get_where('lib_province',array('region_id' => $region_id, 'archived' => 0))->result();
    }

    public function getCities($prov_id){
        return $this->db->get_where('lib_cities_mun',array('province_id' => $prov_id, 'archived' => 0))->result();
    }

    public function getBarangay($city_id){
        return $this->db->get('lib_barangay',array('city_id' => $city_id, 'archived' => 0))->result();
    }

    public function getAllPatientData($patientID){
        $this->db->join('lib_title l1','t1.title_id = l1.title_id','left');
        $this->db->join('lib_suffix l2','t1.suffix_id = l2.suffix_id','left');
        $this->db->join('lib_region l3','t1.region = l3.region_id','left');
        $this->db->join('lib_province l4','t1.province = l4.province_id','left');
        $this->db->join('lib_cities_mun l5','t1.city = l5.city_id','left');
        $this->db->join('lib_barangay l6','t1.barangay = l6.barangay_id','left');
        $this->db->join('lib_nationality l7','t1.nationality = l7.nationality_id','left');
        $this->db->join('lib_marital_status l8','t1.marital_status = l8.marital_status_id','left');
        $this->db->join('lib_religion l9','t1.religion = l9.religion_id','left');
        $this->db->join('lib_occupation l10','t1.occupation = l10.occupation_id','left');
        $this->db->join('lib_rel_to_patient l11','t1.relationship_to_patient = l11.rel_patient_id','left');
        return $this->db->get_where('tbl_patient_list t1',array('patient_id' => $patientID))->row();
    }

    public function getAllPatients(){
        return $this->db->get('tbl_patient_list')->result();
    }

    public function getAllPhysicians(){
        $this->db->join('aauth_user_to_group t2','t1.id = t2.user_id','left');
        return $this->db->get_where('aauth_users t1',array('t2.group_id' => 3,'t1.email_verify'=>1,'t1.banned'=>0,'t1.DELETED'=>0))->result(); #group_id 3 == doctors
    }

    public function saveNewPatientVisitRecord($p_visit_data){
        $this->db->insert('tbl_patient_encounter',$p_visit_data);
        return $this->db->insert_id();
    }

    public function checkIfWithVisitRecord($patientID){
        return $this->db->get_where('tbl_patient_encounter',array('patient_id'=>$patientID))->num_rows();
    }

    public function getPatientRecordVisitList($start,$length,$search,$patient_id){
        $this->db->select('t1.patient_id');
        if(isset($search)){
            $this->db->like('t1.chief_complaint',$search);
            
        }
        $this->db->where('t1.patient_id',$patient_id);
        $num = $this->db->get('tbl_patient_encounter t1')->num_rows();
        

        $this->db->select("t1.*,t2.fullname,t3.fullname as `assigned_physician`");
        $this->db->where(array("t1.archived"=>0));
        if($length > 0){
            $this->db->limit($length,$start);
        }
        $this->db->join('aauth_users t2','t1.encoded_by = t2.id','left');
        $this->db->join('aauth_users t3','t1.assigned_physician = t2.id','left');
        if(isset($search)){
            $this->db->like('t1.chief_complaint',$search);
        }
        
        $res = $this->db->get_where('tbl_patient_encounter t1',array('t1.patient_id'=>$patient_id))->result();

        return array($res,$num);
    }

    public function getPatientVitals($start, $length, $search, $patient_id) {
        // Count matching records
        $this->db->select('v.vitals_id');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('v.vitals_date', $search);
            $this->db->or_like('v.temp', $search);
            $this->db->or_like('v.pulse_rate', $search);
            $this->db->or_like('v.respi_rate', $search);
            $this->db->or_like('v.systolic_bp', $search);
            $this->db->or_like('v.diastolic_bp', $search);
            $this->db->or_like('v.o2_sat', $search);
            $this->db->group_end();
        }
        $this->db->where('v.patient_id', $patient_id);
        $this->db->where('v.archived', 0);
        $num = $this->db->get('tbl_patient_vitals v')->num_rows();
    
        // Fetch paginated results
        $this->db->select('v.*, 
            CONCAT(v.systolic_bp, "/", v.diastolic_bp) as blood_pressure, 
            u.fullname as encoded_by_name');
        $this->db->from('tbl_patient_vitals v');
        $this->db->join('aauth_users u', 'v.encoded_by = u.id', 'left');
        $this->db->where('v.patient_id', $patient_id);
        $this->db->where('v.archived', 0);
    
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('v.vitals_date', $search);
            $this->db->or_like('v.temp', $search);
            $this->db->or_like('v.pulse_rate', $search);
            $this->db->or_like('v.respi_rate', $search);
            $this->db->or_like('v.systolic_bp', $search);
            $this->db->or_like('v.diastolic_bp', $search);
            $this->db->or_like('v.o2_sat', $search);
            $this->db->group_end();
        }
    
        $this->db->order_by('v.vitals_date', 'DESC');
    
        if ($length > 0) {
            $this->db->limit($length, $start);
        }
    
        $res = $this->db->get()->result();
    
        return array($res, $num);
    }
    

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

}

?>