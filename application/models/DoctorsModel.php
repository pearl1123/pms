<?php

class DoctorsModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPatientListActive($start, $length, $search)
    {
        // Base query for counting total rows
        $this->db->from('tbl_patient_list t1');

        // Subquery to get the most recent visit per patient
        $this->db->join(
            '(SELECT id, MAX(visit_date) AS last_visit_date
                FROM tbl_patient_visit
                GROUP BY patient_id) t2',
            't1.patient_id = t2.patient_id',
            'left'
        );

        // Join again to get full visit details
        $this->db->join(
            'tbl_patient_visit t3',
            't2.id = t3.id AND t2.last_visit_date = t3.visit_date',
            'left'
        );

        $this->db->where('t1.archived', 0);
        $this->db->where('t3.doctor_id', $this->session);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('t1.middlename', $search)
                ->or_like('t1.street_address', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        $num = $this->db->count_all_results();

        // Query for fetching paginated result
        $this->db->select("t1.*, t2.fullname, l1.region_desc, l2.province_desc, l3.city_desc, l4.barangay_desc");
        $this->db->from('tbl_patient_list t1');
        $this->db->where('t1.archived', 0);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('t1.middlename', $search)
                ->or_like('t1.street_address', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $this->db->join('lib_region l1', 't1.region = l1.region_id', 'left');
        $this->db->join('lib_province l2', 't1.province = l2.province_id', 'left');
        $this->db->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left');
        $this->db->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left');
        $this->db->join('aauth_users t2', 't1.encoded_by = t2.id', 'left');

        $res = $this->db->get()->result();

        return array($res, $num);
    }

    // GET CURRENT PATIENTS 
    // ========================================================= //
    public function getCurrentPatients($start, $length, $search, $activeOnly, $selectAll, $order_column = null, $order_dir = 'asc')
    {
        $doctor_id = $this->session->userdata('doctor_id'); // logged-in doctor

        // ----------------------
        // Step 1: Total records (for this doctor & active encounters)
        // ----------------------
        $this->db->from('tbl_patient_list t1')
            ->join('tbl_patient_encounter pe', 'pe.patient_id = t1.patient_id AND pe.is_current = 1 AND pe.archived = 0', 'left')
            ->where('pe.assigned_physician', $doctor_id)
            ->where('pe.visit_status_id', 1)
            ->where('t1.archived', 0);

        if ($activeOnly) {
            $this->db->where('t1.isActive', 1);
        }

        $totalRecords = $this->db->count_all_results();

        // ----------------------
        // Step 2: Filtered query (apply search and date filters)
        // ----------------------
        $this->db->select("t1.*, t2.fullname, l1.region_desc, l2.province_desc, 
            l3.city_desc, l4.barangay_desc, vt.visit_type_desc, 
            vs.visit_status_desc, pe.assigned_physician, pe.visit_status_id")
            ->from('tbl_patient_list t1')
            ->join('tbl_patient_encounter pe', 'pe.patient_id = t1.patient_id AND pe.is_current = 1 AND pe.archived = 0', 'left')
            ->join('lib_visit_type vt', 'pe.visit_type_id = vt.visit_type_id', 'left')
            ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id', 'left')
            ->join('lib_region l1', 't1.region = l1.region_id', 'left')
            ->join('lib_province l2', 't1.province = l2.province_id', 'left')
            ->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left')
            ->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left')
            ->join('aauth_users t2', 't1.encoded_by = t2.id', 'left')
            ->where('pe.assigned_physician', $doctor_id)
            ->where('pe.visit_status_id', 1)
            ->where('t1.archived', 0);

        if ($activeOnly) {
            $this->db->where('t1.isActive', 1);
        }

        if ($selectAll == false) {
            $this->db->like('pe.encounter_date', date('Y-m-d'));
        }

        if ($search) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('CONCAT(t1.firstname, " ", t1.lastname)', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        // ----------------------
        // Step 3: Count filtered
        // ----------------------
        $filteredQuery = clone $this->db;
        $recordsFiltered = $filteredQuery->count_all_results();

        // ----------------------
        // Step 4: Apply ordering
        // ----------------------
        if (!empty($order_column)) {
            switch ($order_column) {
                case 'patient_name':
                    $this->db->order_by('t1.lastname', $order_dir);
                    $this->db->order_by('t1.firstname', $order_dir);
                    break;
                case 'mrn':
                    $this->db->order_by('t1.mrn', $order_dir);
                    break;
                case 'sex':
                    $this->db->order_by('t1.sex', $order_dir);
                    break;
                case 'dob':
                    $this->db->order_by('t1.dob', $order_dir);
                    break;
                case 'encoded_by':
                    $this->db->order_by('t2.fullname', $order_dir);
                    break;
                case 'status':
                    $this->db->order_by('t1.isActive', $order_dir);
                    break;
                default:
                    $this->db->order_by('t1.patient_id', 'desc');
            }
        } else {
            $this->db->order_by('t1.patient_id', 'desc');
        }

        // ----------------------
        // Step 5: Apply pagination
        // ----------------------
        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();

        return [$query->result(), $totalRecords, $recordsFiltered];
    }

    public function getDoctorById($doctor_id)
    {
        return $this->db
            ->where('doctor_id', $doctor_id)
            ->get('db_eclaims_staging.lib_doctors')
            ->row();
    }
}
