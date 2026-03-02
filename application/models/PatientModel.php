<?php
/*by Pearlsss 02072025*/
class PatientModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function savePatientRegistration($patientData)
    {
        $this->db->insert('tbl_patient_list', $patientData);
        return $this->db->insert_id();
    }

    public function updatePatientRegistration($patient_id, $data)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->update('tbl_patient_list', $data);

        // Optional debug logging
        log_message('debug', 'Update Query: ' . $this->db->last_query());
        log_message('debug', 'DB Error: ' . print_r($this->db->error(), true));

        // Return true if query executed without error
        return $this->db->error()['code'] === 0;
    }

    public function getPatientById($patient_id)
    {
        $this->db->select('
                p.*,
                t.title_desc,
                s.suffix_desc,
                ms.marital_status_desc,
                r.religion_desc,
                n.nationality_desc,
                o.occupation_desc,
                b.barangay_desc,
                c.city_desc AS city_desc,
                pr.province_desc,
                rg.region_desc
            ')
            ->from('tbl_patient_list p')
            ->join('lib_title t', 'p.title_id = t.title_id', 'left')  // ✅ fixed here
            ->join('lib_suffix s', 'p.suffix_id = s.suffix_id', 'left')
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

        // Debug if something goes wrong
        if (!$query) {
            echo $this->db->last_query();
            echo '<br>';
            echo $this->db->error()['message'];
            exit;
        }

        return $query->row();
    }

    public function getLastPatientMRNForYear($year)
    {
        return $this->db->select('mrn')
            ->like('mrn', $year . '-', 'after')
            ->order_by('mrn', 'DESC')
            ->limit(1)
            ->get('tbl_patient_list')
            ->row();
    }

    // GET ALL ACTIVE PATIENTS 
    // ========================================================= //
    public function getActivePatientList($start, $length, $search, $activeOnly, $visit_type_id = null, $orderColumn = 't1.mrn', $orderDir = 'asc')
    {
        $excluded_visit_types = [6, 7, 10, 11, 13, 15, 16];

        // ----------------------------
        // Base query
        // ----------------------------
        $baseQuery = $this->db
            ->from('tbl_patient_list t1')
            ->join('tbl_patient_encounter pe', 'pe.patient_id = t1.patient_id AND pe.is_current = 1 AND pe.archived = 0', 'left')
            ->join('lib_visit_type vt', 'pe.visit_type_id = vt.visit_type_id', 'left')
            ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id', 'left')
            ->join('lib_region l1', 't1.region = l1.region_id', 'left')
            ->join('lib_province l2', 't1.province = l2.province_id', 'left')
            ->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left')
            ->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left')
            ->join('aauth_users t2', 't1.encoded_by = t2.id', 'left')
            ->where('t1.archived', 0)
            ->where_not_in('pe.visit_type_id', $excluded_visit_types);

        if ($activeOnly) {
            $this->db->where('t1.isActive', 1);
            if ($visit_type_id !== null) {
                $this->db->where('pe.visit_type_id', $visit_type_id);
            }
        }

        if ($search) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        // ----------------------------
        // Count query (clone before select)
        // ----------------------------
        $count_query = clone $this->db;
        $totalRecords = $count_query->count_all_results('', false);

        // ----------------------------
        // Main data query (rebuild select)
        // ----------------------------
        $this->db->select("t1.*, t2.fullname, l1.region_desc, l2.province_desc,
                      l3.city_desc, l4.barangay_desc, vt.visit_type_desc, vs.visit_status_desc");

        $this->db->order_by($orderColumn, $orderDir);

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();

        return [$query->result(), $totalRecords];
    }



    // GET ALL PATIENTS 
    // ========================================================= //
    public function getActivePatientList_All($start, $length, $search, $orderColumn = 't1.mrn', $orderDir = 'asc')
    {
        // ----------------------------
        // Count total matching records
        // ----------------------------
        $this->db->from('tbl_patient_list t1');
        $this->db->where('t1.archived', 0);

        if ($search) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('t1.middlename', $search)
                ->or_like('t1.street_address', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        $num = $this->db->count_all_results();

        // ----------------------------
        // Main query with ordering and pagination
        // ----------------------------
        $this->db->select("t1.*, t1.isActive, t2.fullname, 
                      l1.region_desc, l2.province_desc, 
                      l3.city_desc, l4.barangay_desc");
        $this->db->from('tbl_patient_list t1');
        $this->db->where('t1.archived', 0);

        if ($search) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('t1.middlename', $search)
                ->or_like('t1.street_address', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        $this->db->join('lib_region l1', 't1.region = l1.region_id', 'left');
        $this->db->join('lib_province l2', 't1.province = l2.province_id', 'left');
        $this->db->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left');
        $this->db->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left');
        $this->db->join('aauth_users t2', 't1.encoded_by = t2.id', 'left');

        $this->db->order_by($orderColumn, $orderDir);

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        return [$res, $num];
    }

    public function getPatientList($start, $length, $search, $activeOnly = true, $visit_type_id = null, $order_column = 't1.date_encoded', $order_dir = 'DESC')
    {
        $applySearch = function () use ($search) {
            if ($search) {
                $this->db->group_start()
                    ->like('t1.firstname', $search)
                    ->or_like('t1.lastname', $search)
                    ->or_like('CONCAT(t1.firstname, " ", t1.lastname)', $search)
                    ->or_like('t1.mrn', $search)
                    ->group_end();
            }
        };

        // COUNT QUERY
        $this->db->from('tbl_patient_list t1')
            ->where('t1.archived', 0);
        if ($activeOnly) {
            $this->db->where('t1.isActive', 1);
            $db_sub = $this->load->database('default', TRUE);
            $subquery = $db_sub->select('1')
                ->from('tbl_patient_encounter pe')
                ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id')
                ->where('pe.patient_id = t1.patient_id')
                ->where('pe.is_current', 1)
                ->where('pe.archived', 0)
                ->where('vs.is_current_flag', 1);
            if ($visit_type_id !== null) {
                $subquery->where('pe.visit_type_id', $visit_type_id);
            }
            $subquery = $subquery->limit(1)->get_compiled_select();
            $this->db->where("EXISTS ($subquery)", NULL, FALSE);
        }
        $applySearch();
        $total_records = $this->db->count_all_results();

        // DATA QUERY
        $this->db->select("t1.*, t2.fullname, l1.region_desc, l2.province_desc, l3.city_desc, l4.barangay_desc, vt.visit_type_desc, vs.visit_status_desc")
            ->from('tbl_patient_list t1')
            ->join('tbl_patient_encounter pe', 'pe.patient_id = t1.patient_id AND pe.is_current = 1 AND pe.archived = 0', 'left')
            ->join('lib_visit_type vt', 'pe.visit_type_id = vt.visit_type_id', 'left')
            ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id', 'left')
            ->join('lib_region l1', 't1.region = l1.region_id', 'left')
            ->join('lib_province l2', 't1.province = l2.province_id', 'left')
            ->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left')
            ->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left')
            ->join('aauth_users t2', 't1.encoded_by = t2.id', 'left')
            ->where('t1.archived', 0);

        if ($activeOnly) {
            $this->db->where('t1.isActive', 1);
            if ($visit_type_id !== null) {
                $this->db->where('pe.visit_type_id', $visit_type_id);
            }
        }

        $applySearch();

        // 🔹 Dynamic sorting
        $sortable_columns = [
            'id' => 't1.patient_id',
            'mrn' => 't1.mrn',
            'patient_name' => 't1.lastname',
            'sex' => 't1.sex',
            'dob' => 't1.dob',
            'encoded_by' => 't2.fullname',
            'status' => 'vs.visit_status_desc',
            'date_encoded' => 't1.date_encoded'
        ];
        $order_by = isset($sortable_columns[$order_column]) ? $sortable_columns[$order_column] : 't1.date_encoded';
        $this->db->order_by($order_by, $order_dir);

        if ($length > 0) $this->db->limit($length, $start);

        $query = $this->db->get();
        return [$query->result(), $total_records];
    }

    public function getPatientListAll($start, $length, $search)
    {
        // Count total rows for pagination
        $this->db->select('t1.patient_id');
        $this->db->where(['t1.archived' => 0]);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('t1.middlename', $search)
                ->or_like('t1.street_address', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }
        $num = $this->db->get('tbl_patient_list t1')->num_rows();

        // Fetch actual data
        $this->db->select("
            t1.*,
            t1.isActive,
            t2.fullname,
            l1.region_desc,
            l2.province_desc,
            l3.city_desc,
            l4.barangay_desc
        ");
        $this->db->from('tbl_patient_list t1');
        $this->db->where(['t1.archived' => 0]);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('t1.middlename', $search)
                ->or_like('t1.street_address', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        $this->db->join('lib_region l1', 't1.region = l1.region_id', 'left');
        $this->db->join('lib_province l2', 't1.province = l2.province_id', 'left');
        $this->db->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left');
        $this->db->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left');
        $this->db->join('aauth_users t2', 't1.encoded_by = t2.id', 'left');

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        return [$res, $num];
    }

    public function getInPatientList($start, $length, $search, $order_column = 'mrn', $order_dir = 'DESC')
    {
        // COUNT total rows
        $this->db->select('p.patient_id');
        $this->db->from('tbl_patient_encounter t1');
        $this->db->join('tbl_patient_list p', 't1.patient_id = p.patient_id', 'left');
        $this->db->where(['p.archived' => 0]);
        $this->db->where(['t1.visit_type_id' => 2]);
        $this->db->group_by('p.patient_id');

        if (!empty($search)) {
            $this->db->group_start()
                ->like('p.firstname', $search)
                ->or_like('p.lastname', $search)
                ->or_like('p.middlename', $search)
                ->or_like('p.street_address', $search)
                ->or_like('p.mrn', $search)
                ->group_end();
        }

        $num = $this->db->get()->num_rows();

        // FETCH actual paginated data
        $this->db->select("
        t1.*,
        p.*,
        p.isActive,
        u.fullname,
        l1.region_desc,
        l2.province_desc,
        l3.city_desc,
        l4.barangay_desc
    ");
        $this->db->from('tbl_patient_encounter t1');
        $this->db->join('tbl_patient_list p', 't1.patient_id = p.patient_id', 'left');
        $this->db->where(['p.archived' => 0]);
        $this->db->where(['t1.visit_type_id' => 2]);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('p.firstname', $search)
                ->or_like('p.lastname', $search)
                ->or_like('p.middlename', $search)
                ->or_like('p.street_address', $search)
                ->or_like('p.mrn', $search)
                ->group_end();
        }

        $this->db->join('lib_region l1', 'p.region = l1.region_id', 'left');
        $this->db->join('lib_province l2', 'p.province = l2.province_id', 'left');
        $this->db->join('lib_cities_mun l3', 'p.city = l3.city_id', 'left');
        $this->db->join('lib_barangay l4', 'p.barangay = l4.barangay_id', 'left');
        $this->db->join('aauth_users u', 'p.encoded_by = u.id', 'left');

        $this->db->group_by('p.patient_id');

        $valid_columns = [
            'id' => 'p.patient_id',
            'mrn' => 'p.mrn',
            'patient_name' => 'p.lastname',
            'sex' => 'p.sex',
            'dob' => 'p.dob',
            'contact_no' => 'p.contact_no',
            'address' => 'p.street_address',
            'encoded_by' => 'u.fullname',
            'status' => 'p.isActive'
        ];

        if (isset($valid_columns[$order_column])) {
            $this->db->order_by($valid_columns[$order_column], $order_dir);
        } else {
            // fallback order
            $this->db->order_by('t1.date_encoded', 'DESC');
        }

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        return [$res, $num];
    }

    public function getOutPatientList($start, $length, $search, $activeOnly = true, $order_column = 'mrn', $order_dir = 'ASC')
    {
        $visit_type_id = 1; // Outpatient visit type ID

        // --- Base query (cached for count + data) ---
        $this->db->start_cache();
        $this->db->from('tbl_patient_list t1')
            ->join(
                'tbl_patient_encounter pe',
                't1.patient_id = pe.patient_id 
             AND pe.visit_type_id = ' . (int)$visit_type_id . ' 
             AND pe.is_current = 1 
             AND pe.archived = 0',
                'inner'
            )
            ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id', 'inner')
            ->where('t1.archived', 0);

        if ($activeOnly) {
            $this->db->where('t1.isActive', 1)
                ->where('vs.is_current_flag', 1);
        }

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('CONCAT(t1.firstname, " ", t1.lastname)', $search, false)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }
        $this->db->stop_cache();

        // --- Count query ---
        $total_records = $this->db->select('COUNT(DISTINCT t1.patient_id) as cnt')->get()->row()->cnt;

        // --- Data query ---
        $this->db->select("
            t1.*, 
            t2.fullname, 
            l1.region_desc, 
            l2.province_desc, 
            l3.city_desc, 
            l4.barangay_desc, 
            vs.visit_status_desc,
            pe.encounter_date
        ")
            ->join('lib_region l1', 't1.region = l1.region_id', 'left')
            ->join('lib_province l2', 't1.province = l2.province_id', 'left')
            ->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left')
            ->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left')
            ->join('aauth_users t2', 't1.encoded_by = t2.id', 'left');

        // Handle order column properly
        switch ($order_column) {
            case 'patient_name':
                $this->db->order_by('t1.lastname', $order_dir);
                break;
            case 'sex':
            case 'dob':
            case 'contact_no':
            case 'mrn':
                $this->db->order_by('t1.' . $order_column, $order_dir);
                break;
            case 'encoded_by':
                $this->db->order_by('t2.fullname', $order_dir);
                break;
            case 'status':
                $this->db->order_by('vs.visit_status_desc', $order_dir);
                break;
            default:
                $this->db->order_by('pe.encounter_date', 'DESC');
        }

        $this->db->limit($length, $start);
        $result = $this->db->get()->result();

        $this->db->flush_cache();

        return [$result, $total_records];
    }

    public function getOutPatientListAll($start, $length, $search, $order_column = 'mrn', $order_dir = 'ASC')
    {
        $visit_type_id = 1; // Outpatient visit type ID

        // --- COUNT QUERY ---
        $this->db->start_cache();
        $this->db->from('tbl_patient_list t1');
        $this->db->join('tbl_patient_encounter t2', 't1.patient_id = t2.patient_id', 'left');
        $this->db->where([
            't1.archived' => 0,
            't2.is_current' => 1,
            't2.visit_type_id' => $visit_type_id
        ]);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('t1.middlename', $search)
                ->or_like('t1.street_address', $search)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }
        $this->db->stop_cache();

        $num = $this->db->select('COUNT(DISTINCT t1.patient_id) AS cnt')->get()->row()->cnt;

        // --- DATA QUERY ---
        $this->db->select("
        t1.*, 
        t3.fullname, 
        l1.region_desc, 
        l2.province_desc, 
        l3.city_desc, 
        l4.barangay_desc
    ");
        $this->db->join('aauth_users t3', 't1.encoded_by = t3.id', 'left');
        $this->db->join('lib_region l1', 't1.region = l1.region_id', 'left');
        $this->db->join('lib_province l2', 't1.province = l2.province_id', 'left');
        $this->db->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left');
        $this->db->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left');
        $this->db->join('lib_suffix s', 't1.suffix_id = s.suffix_id', 'left');

        // --- ORDERING HANDLER ---
        switch ($order_column) {
            case 'patient_name':
                $this->db->order_by('t1.lastname', $order_dir);
                break;
            case 'sex':
            case 'dob':
            case 'contact_no':
            case 'mrn':
                $this->db->order_by('t1.' . $order_column, $order_dir);
                break;
            case 'encoded_by':
                $this->db->order_by('t3.fullname', $order_dir);
                break;
            case 'status':
                $this->db->order_by('t1.isActive', $order_dir);
                break;
            case 'address':
                $this->db->order_by('t1.street_address', $order_dir);
                break;
            default:
                $this->db->order_by('t1.lastname', 'ASC');
                break;
        }

        $this->db->group_by('t1.patient_id');
        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        $this->db->flush_cache();
        return [$res, $num];
    }

    public function getERPatientList($start, $length, $search = '', $activeOnly = true, $orderColumn = 'pe.encounter_date', $orderDir = 'DESC')
    {
        $visit_type_id = 3; // Emergency Visit

        // ---------- COUNT QUERY ----------
        $this->db->from('tbl_patient_list t1')
            ->join(
                'tbl_patient_encounter pe',
                't1.patient_id = pe.patient_id 
             AND pe.visit_type_id = ' . (int)$visit_type_id . ' 
             AND pe.is_current = 1 
             AND pe.archived = 0',
                'inner'
            )
            ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id', 'inner')
            ->where('t1.archived', 0);

        if ($activeOnly) {
            $this->db->where('t1.isActive', 1)
                ->where('vs.is_current_flag', 1);
        }

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('CONCAT(t1.firstname, " ", t1.lastname)', $search, false)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        $this->db->select('COUNT(DISTINCT t1.patient_id) as count');
        $total_records = $this->db->get()->row()->count ?? 0;

        // ---------- DATA QUERY ----------
        $this->db->select("
        t1.*,
        t2.fullname,
        l1.region_desc, 
        l2.province_desc, 
        l3.city_desc, 
        l4.barangay_desc, 
        vs.visit_status_desc,
        pe.encounter_date
    ")
            ->from('tbl_patient_list t1')
            ->join(
                'tbl_patient_encounter pe',
                't1.patient_id = pe.patient_id 
             AND pe.visit_type_id = ' . (int)$visit_type_id . ' 
             AND pe.is_current = 1 
             AND pe.archived = 0',
                'inner'
            )
            ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id', 'left')
            ->join('lib_region l1', 't1.region = l1.region_id', 'left')
            ->join('lib_province l2', 't1.province = l2.province_id', 'left')
            ->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left')
            ->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left')
            ->join('aauth_users t2', 't1.encoded_by = t2.id', 'left')
            ->where('t1.archived', 0);

        if ($activeOnly) {
            $this->db->where('t1.isActive', 1)
                ->where('vs.is_current_flag', 1);
        }

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('CONCAT(t1.firstname, " ", t1.lastname)', $search, false)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        // ✅ Fix sorting — only allow specific safe columns
        $allowedColumns = [
            'mrn' => 't1.mrn',
            'patient_name' => 't1.lastname',
            'sex' => 't1.sex',
            'dob' => 't1.dob',
            'contact_no' => 't1.contact_no',
            'address' => 't1.street_address',
            'encoded_by' => 't2.fullname',
            'status' => 'vs.visit_status_desc',
            'pe.encounter_date' => 'pe.encounter_date'
        ];
        $orderBy = $allowedColumns[$orderColumn] ?? 'pe.encounter_date';
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->order_by($orderBy, $orderDir)
            ->limit($length, $start);

        $data = $this->db->get()->result();

        return [$data, $total_records];
    }

    public function getERPatientListAll($start, $length, $search = '', $orderColumn = 'mrn', $orderDir = 'ASC')
    {
        // Base query for counting total rows
        // $this->db->from('tbl_patient_list t1');
        // $this->db->where('t1.archived', 0);

        // if (!empty($search)) {
        //     $this->db->group_start()
        //         ->like('t1.firstname', $search)
        //         ->or_like('t1.lastname', $search)
        //         ->or_like('t1.middlename', $search)
        //         ->or_like('t1.street_address', $search)
        //         ->or_like('t1.mrn', $search)
        //         ->group_end();
        // }

        // $num = $this->db->count_all_results();

        // // Query for fetching paginated result
        // $this->db->select("t1.*, t2.fullname, l1.region_desc, l2.province_desc, l3.city_desc, l4.barangay_desc");
        // $this->db->from('tbl_patient_list t1');
        // $this->db->where('t1.archived', 0);

        // if (!empty($search)) {
        //     $this->db->group_start()
        //         ->like('t1.firstname', $search)
        //         ->or_like('t1.lastname', $search)
        //         ->or_like('t1.middlename', $search)
        //         ->or_like('t1.street_address', $search)
        //         ->or_like('t1.mrn', $search)
        //         ->group_end();
        // }

        // if ($length > 0) {
        //     $this->db->limit($length, $start);
        // }

        // $this->db->join('lib_region l1', 't1.region = l1.region_id', 'left');
        // $this->db->join('lib_province l2', 't1.province = l2.province_id', 'left');
        // $this->db->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left');
        // $this->db->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left');
        // $this->db->join('aauth_users t2', 't1.encoded_by = t2.id', 'left');

        // $res = $this->db->get()->result();

        // return array($res, $num);

        // Count total rows
        $visit_type_id = 3; // Emergency Visit

        // ---------- COUNT QUERY ----------
        $this->db->from('tbl_patient_list t1')
            ->join(
                'tbl_patient_encounter pe',
                't1.patient_id = pe.patient_id 
             AND pe.visit_type_id = ' . (int)$visit_type_id . ' 
             AND pe.is_current = 1 
             AND pe.archived = 0',
                'inner'
            )
            ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id', 'inner')
            ->where('t1.archived', 0)
            ->where('t1.isActive', 1)
            ->where('vs.is_current_flag', 1);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('CONCAT(t1.firstname, " ", t1.lastname)', $search, false)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        $this->db->select('COUNT(DISTINCT t1.patient_id) as count');
        $total_records = $this->db->get()->row()->count ?? 0;

        // ---------- DATA QUERY ----------
        $this->db->select("
        t1.*,
        t2.fullname,
        l1.region_desc, 
        l2.province_desc, 
        l3.city_desc, 
        l4.barangay_desc, 
        vs.visit_status_desc,
        pe.encounter_date
    ")
            ->from('tbl_patient_list t1')
            ->join(
                'tbl_patient_encounter pe',
                't1.patient_id = pe.patient_id 
             AND pe.visit_type_id = ' . (int)$visit_type_id . ' 
             AND pe.is_current = 1 
             AND pe.archived = 0',
                'inner'
            )
            ->join('lib_visit_status vs', 'pe.visit_status_id = vs.visit_status_id', 'left')
            ->join('lib_region l1', 't1.region = l1.region_id', 'left')
            ->join('lib_province l2', 't1.province = l2.province_id', 'left')
            ->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left')
            ->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left')
            ->join('aauth_users t2', 't1.encoded_by = t2.id', 'left')
            ->where('t1.archived', 0)
            ->where('t1.isActive', 1)
            ->where('vs.is_current_flag', 1);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('t1.firstname', $search)
                ->or_like('t1.lastname', $search)
                ->or_like('CONCAT(t1.firstname, " ", t1.lastname)', $search, false)
                ->or_like('t1.mrn', $search)
                ->group_end();
        }

        // ✅ Safe allowed columns mapping for sorting
        $allowedColumns = [
            'mrn'         => 't1.mrn',
            'patient_name' => 't1.lastname',
            'sex'         => 't1.sex',
            'dob'         => 't1.dob',
            'contact_no'  => 't1.contact_no',
            'address'     => 't1.street_address',
            'encoded_by'  => 't2.fullname',
            'status'      => 'vs.visit_status_desc',
            'id'          => 't1.patient_id',
            'pe.encounter_date' => 'pe.encounter_date'
        ];

        $orderBy = $allowedColumns[$orderColumn] ?? 'pe.encounter_date';
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->order_by($orderBy, $orderDir)
            ->limit($length, $start);

        $data = $this->db->get()->result();

        return [$data, $total_records];
    }

    public function getPossibleDups($start, $length, $search, $dups)
    {
        if (empty($dups)) {
            return array([], 0);
        }

        $this->db->select('t1.patient_id');
        if (!empty($search)) {
            $this->db->like('t1.firstname', $search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }
        $this->db->where_in('patient_id', $dups);
        $num = $this->db->get('tbl_patient_list t1')->num_rows();

        $this->db->select("*, t2.fullname, l1.region_desc, l2.province_desc, l3.city_desc, l4.barangay_desc");
        $this->db->from('tbl_patient_list t1');
        $this->db->join('lib_region l1', 't1.region = l1.region_id', 'left');
        $this->db->join('lib_province l2', 't1.province = l2.province_id', 'left');
        $this->db->join('lib_cities_mun l3', 't1.city = l3.city_id', 'left');
        $this->db->join('lib_barangay l4', 't1.barangay = l4.barangay_id', 'left');
        $this->db->join('aauth_users t2', 't1.encoded_by = t2.id', 'left');
        $this->db->where("t1.archived", 0);

        if (!empty($search)) {
            $this->db->like('t1.firstname', $search);
            $this->db->or_like('t1.lastname', $search);
            $this->db->or_like('t1.middlename', $search);
            $this->db->or_like('t1.street_address', $search);
            $this->db->or_like('t1.mrn', $search);
        }

        $this->db->where_in('patient_id', $dups);

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();
        return array($res, $num);
    }

    public function getLastPatientMRN()
    {
        $this->db->select('MAX(patient_id) as max_id');
        $lastRec = $this->db->get('tbl_patient_list')->row();

        return $this->db->get('tbl_patient_list', array('patient_id' => $lastRec->max_id))->row();
    }

    public function getAllTitles()
    {
        return $this->db->get_where('lib_title', array('archived' => 0))->result();
    }

    public function getAllSuffix()
    {
        return $this->db->get_where('lib_suffix', array('archived' => 0))->result();
    }

    public function getAllNationality()
    {
        return $this->db->get_where('lib_nationality', array('archived' => 0))->result();
    }

    public function getAllMStatus()
    {
        return $this->db->get_where('lib_marital_status', array('archived' => 0))->result();
    }

    public function getAllReligion()
    {
        return $this->db->get_where('lib_religion', array('archived' => 0))->result();
    }

    public function getAllOccupation()
    {
        return $this->db->get_where('lib_occupation', array('archived' => 0))->result();
    }

    public function getAllRegion()
    {
        return $this->db->get_where('lib_region', array('archived' => 0))->result();
    }

    public function getAllRelToPatient()
    {
        return $this->db->get_where('lib_rel_to_patient', array('archived' => 0))->result();
    }

    public function getProvinces($region_id)
    {
        return $this->db->get_where('lib_province', array('region_id' => $region_id, 'archived' => 0))->result();
    }

    public function getCities($prov_id)
    {
        return $this->db->get_where('lib_cities_mun', array('province_id' => $prov_id, 'archived' => 0))->result();
    }

    public function getBarangay($city_id)
    {
        return $this->db->get_where('lib_barangay', [
            'city_id' => $city_id,
            'archived' => 0
        ])->result();
    }

    public function getAllPatientData($patientID)
    {
        $this->db->join('lib_title l1', 't1.title_id = l1.title_id', 'left');
        $this->db->join('lib_suffix l2', 't1.suffix_id = l2.suffix_id', 'left');
        $this->db->join('lib_region l3', 't1.region = l3.region_id', 'left');
        $this->db->join('lib_province l4', 't1.province = l4.province_id', 'left');
        $this->db->join('lib_cities_mun l5', 't1.city = l5.city_id', 'left');
        $this->db->join('lib_barangay l6', 't1.barangay = l6.barangay_id', 'left');
        $this->db->join('lib_nationality l7', 't1.nationality = l7.nationality_id', 'left');
        $this->db->join('lib_marital_status l8', 't1.marital_status = l8.marital_status_id', 'left');
        $this->db->join('lib_religion l9', 't1.religion = l9.religion_id', 'left');
        $this->db->join('lib_occupation l10', 't1.occupation = l10.occupation_id', 'left');
        $this->db->join('lib_rel_to_patient l11', 't1.relationship_to_patient = l11.rel_patient_id', 'left');
        return $this->db->get_where('tbl_patient_list t1', array('patient_id' => $patientID))->row();
    }

    public function getAllPatients()
    {
        return $this->db->get('tbl_patient_list')->result();
    }

    public function getAllPhysicians()
    {
        $this->db->join('aauth_user_to_group t2', 't1.id = t2.user_id', 'left');
        return $this->db->get_where('aauth_users t1', array('t2.group_id' => 3, 't1.email_verify' => 1, 't1.banned' => 0, 't1.DELETED' => 0))->result(); #group_id 3 == doctors
    }

    public function saveNewPatientVisitRecord($p_visit_data)
    {
        $this->db->insert('tbl_patient_encounter', $p_visit_data);
        return $this->db->insert_id();
    }

    public function hasActiveVisit($patient_id)
    {
        return $this->db->select('encounter_id')
            ->from('tbl_patient_encounter')
            ->where('patient_id', $patient_id)
            ->where('is_current', 1)
            ->where('archived', 0)
            ->get()
            ->num_rows() > 0;
    }

    public function checkIfWithVisitRecord($patientID)
    {
        return $this->db->get_where('tbl_patient_encounter', array('patient_id' => $patientID))->num_rows();
    }

    // public function getPatientRecordVisitList($start, $length, $search, $patient_id, $orderBy = 'pe.encounter_date', $orderDir = 'desc')
    // {
    //     $this->db->select("
    //     pe.encounter_id,
    //     pe.patient_id,
    //     pe.encounter_date,
    //     cc.chief_complaint_desc AS chief_complaint_desc,
    //     vt.visit_type_desc AS visit_type_desc,
    //     vs.visit_status_desc AS visit_status_desc,
    //     am.arrival_mode_desc AS arrival_mode_desc,
    //     pe.assigned_physician AS doctor_id
    // ");
    //     $this->db->from("tbl_patient_encounter pe");
    //     $this->db->join("lib_chief_complaint cc", "cc.chief_complaint_id = pe.chief_complaint", "left");
    //     $this->db->join("lib_visit_type vt", "vt.visit_type_id = pe.visit_type_id", "left");
    //     $this->db->join("lib_visit_status vs", "vs.visit_status_id = pe.visit_status_id", "left");
    //     $this->db->join("lib_arrival_mode am", "am.arrival_mode_id = pe.arrival_mode_id", "left");
    //     $this->db->where("pe.patient_id", $patient_id);
    //     $this->db->where("pe.archived", 0);

    //     if (!empty($search)) {
    //         $this->db->group_start();
    //         $this->db->like("pe.chief_complaint", $search);
    //         $this->db->or_like("vt.visit_type_desc", $search);
    //         $this->db->or_like("vs.visit_status_desc", $search);
    //         $this->db->or_like("am.arrival_mode_desc", $search);
    //         $this->db->group_end();
    //     }

    //     $this->db->order_by($orderBy, $orderDir);

    //     // Total rows
    //     $total = $this->db->count_all_results('', false);

    //     $this->db->limit($length, $start);
    //     $query = $this->db->get();

    //     $results = $query ? $query->result() : [];

    //     // Look up doctor names
    //     $eclaims_db = $this->load->database('eclaims', TRUE);
    //     foreach ($results as &$row) {
    //         $row->doctor_name = 'Unknown';
    //         if (!empty($row->doctor_id)) {
    //             $doc_query = $eclaims_db
    //                 ->select("pDoctorFirstName, pDoctorMiddleName, pDoctorLastName")
    //                 ->from("lib_doctors")
    //                 ->where("doctor_id", $row->doctor_id)
    //                 ->get();
    //             if ($doc_query && $doc_query->num_rows() > 0) {
    //                 $doc = $doc_query->row();
    //                 $row->doctor_name = trim("{$doc->pDoctorFirstName} {$doc->pDoctorMiddleName} {$doc->pDoctorLastName}");
    //             }
    //         }
    //     }

    //     return [$results, $total];
    // }

    public function getPatientRecordVisitList($start, $length, $search, $patient_id, $orderBy = 'pe.encounter_date', $orderDir = 'desc')
    {
        $this->db->select("
        pe.encounter_id,
        pe.patient_id,
        pe.encounter_date,
        pe.visit_status_id,
        pe.visit_type_id,
        cc.chief_complaint_desc AS chief_complaint_desc,
        vt.visit_type_desc AS visit_type_desc,
        vs.visit_status_desc AS visit_status_desc,
        am.arrival_mode_desc AS arrival_mode_desc,
        pe.assigned_physician AS doctor_id
    ");
        $this->db->from("tbl_patient_encounter pe");
        $this->db->join("lib_chief_complaint cc", "cc.chief_complaint_id = pe.chief_complaint", "left");
        $this->db->join("lib_visit_type vt", "vt.visit_type_id = pe.visit_type_id", "left");
        $this->db->join("lib_visit_status vs", "vs.visit_status_id = pe.visit_status_id", "left");
        $this->db->join("lib_arrival_mode am", "am.arrival_mode_id = pe.arrival_mode_id", "left");
        $this->db->where("pe.patient_id", $patient_id);
        $this->db->where("pe.archived", 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like("pe.chief_complaint", $search);
            $this->db->or_like("vt.visit_type_desc", $search);
            $this->db->or_like("vs.visit_status_desc", $search);
            $this->db->or_like("am.arrival_mode_desc", $search);
            $this->db->group_end();
        }

        $this->db->order_by($orderBy, $orderDir);

        // Total rows
        $total = $this->db->count_all_results('', false);

        $this->db->limit($length, $start);
        $query = $this->db->get();

        $results = $query ? $query->result() : [];

        // Look up doctor names and trans_group_id for the LATEST discharged encounter only
        $eclaims_db = $this->load->database('eclaims', TRUE);
        $ward_db = $this->load->database('ward_db3', TRUE);

        // Get the latest trans_group_id once for this patient
        $latest_trans_group_id = null;
        $trans_query = $ward_db
            ->select("trans_group_id")
            ->from("t_trans_group")
            ->where("patient_id", $patient_id)
            ->order_by("c_createdDate", "DESC")
            ->limit(1)
            ->get();
        if ($trans_query && $trans_query->num_rows() > 0) {
            $latest_trans_group_id = $trans_query->row()->trans_group_id;
        }

        // Find the most recent encounter
        $latest_encounter_id = null;
        if (!empty($results)) {
            $latest_encounter_id = $results[0]->encounter_id; // Assuming results are ordered by date DESC
        }

        foreach ($results as &$row) {
            // Get doctor name
            $row->doctor_name = 'Unknown';
            if (!empty($row->doctor_id)) {
                $doc_query = $eclaims_db
                    ->select("pDoctorFirstName, pDoctorMiddleName, pDoctorLastName")
                    ->from("lib_doctors")
                    ->where("doctor_id", $row->doctor_id)
                    ->get();
                if ($doc_query && $doc_query->num_rows() > 0) {
                    $doc = $doc_query->row();
                    $row->doctor_name = trim("{$doc->pDoctorFirstName} {$doc->pDoctorMiddleName} {$doc->pDoctorLastName}");
                }
            }

            // Only assign trans_group_id to the LATEST encounter
            $row->trans_group_id = null;
            if ($row->encounter_id == $latest_encounter_id && $latest_trans_group_id) {
                $row->trans_group_id = $latest_trans_group_id;
            }
        }

        return [$results, $total];
    }

    // ===== PATIENT VISIT DATA =====
    public function getPatientRecordVisitListWithDetails(
        $start,
        $length,
        $search,
        $patient_id,
        $orderBy = 'pe.encounter_date',
        $orderDir = 'desc',
        $filter_encounter_id = null
    ) {
        // Base query builder
        $this->db->start_cache();

        $this->db->from("tbl_patient_encounter pe")
            ->join("lib_chief_complaint cc", "cc.chief_complaint_id = pe.chief_complaint", "left")
            ->join("lib_visit_type vt", "vt.visit_type_id = pe.visit_type_id", "left")
            ->join("lib_visit_status vs", "vs.visit_status_id = pe.visit_status_id", "left")
            ->join("lib_arrival_mode am", "am.arrival_mode_id = pe.arrival_mode_id", "left")
            ->join("db_eclaims_staging.lib_doctors d", "d.doctor_id = pe.assigned_physician", "left")
            ->join("tbl_out_patient_clinical_abstract ca", "ca.encounter_id = pe.encounter_id AND ca.patient_id = pe.patient_id", "left")
            ->where("pe.patient_id", $patient_id)
            ->where("pe.archived", 0);

        if (!empty($filter_encounter_id)) {
            $this->db->where("pe.encounter_id", $filter_encounter_id); // strict filter
        }

        if (!empty($search)) {
            $this->db->group_start()
                ->like("cc.chief_complaint_desc", $search)
                ->or_like("vt.visit_type_desc", $search)
                ->or_like("vs.visit_status_desc", $search)
                ->or_like("am.arrival_mode_desc", $search)
                ->or_like("d.pDoctorLastName", $search)
                ->or_like("d.pDoctorFirstName", $search)
                ->group_end();
        }

        $this->db->stop_cache();

        // Count total rows
        $total = $this->db->count_all_results();

        // Fetch paginated rows
        $this->db->select("
            pe.encounter_id,
            pe.patient_id,
            pe.encounter_date,
            cc.chief_complaint_desc,
            vt.visit_type_desc,
            vs.visit_status_desc,
            am.arrival_mode_desc,
            pe.assigned_physician AS doctor_id,
            CONCAT(
                d.pDoctorLastName, ', ', d.pDoctorFirstName, ' ',
                IFNULL(d.pDoctorMiddleName,''), ' ',
                IFNULL(d.pDoctorSuffix,'')
            ) AS doctor_name,
            ca.admitting_diagnosis,
            ca.final_diagnosis
        ");
        $this->db->order_by($orderBy, $orderDir);

        // 🔹 If filtering by encounter → don’t paginate, show full encounter set
        if (empty($filter_encounter_id)) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $results = $query ? $query->result() : [];
        $this->db->flush_cache();

        // ✅ Convert ICD10 codes to titles
        foreach ($results as &$row) {
            // Convert admitting diagnosis
            if (!empty($row->admitting_diagnosis)) {
                $codes = json_decode($row->admitting_diagnosis, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($codes)) {
                    $titles = [];
                    foreach ($codes as $code) {
                        $icd = $this->db->select('icd10_title')
                            ->from('lib_icd10')
                            ->where('icd10_code', $code)
                            ->get()
                            ->row();
                        $titles[] = $icd ? $icd->icd10_title : $code;
                    }
                    $row->admitting_diagnosis = implode(', ', $titles);
                }
            }

            // Convert final diagnosis
            if (!empty($row->final_diagnosis)) {
                $code = $row->final_diagnosis;
                $icd = $this->db->select('icd10_title')
                    ->from('lib_icd10')
                    ->where('icd10_code', $code)
                    ->get()
                    ->row();
                $row->final_diagnosis = $icd ? $icd->icd10_title : $code;
            }
        }

        // Attach related data STRICTLY per encounter
        $CI = &get_instance();
        $CI->load->model('PatientNotes_model');

        foreach ($results as &$row) {
            $enc_id = $row->encounter_id;

            // ✅ Vitals
            $row->vitals = $this->db->from("tbl_patient_vitals")
                ->where("patient_id", $patient_id)
                ->where("encounter_id", $enc_id)
                ->where("archived", 0)
                ->order_by("vitals_date", "DESC")
                ->get()
                ->result();

            // ✅ Medications
            $row->medications = $this->db
                ->select("pm.*, g.generic_name, dos.dosage_desc, f.frequency_desc, r.route_desc, dur.duration_desc")
                ->from("db_emr.tbl_patient_medications pm")
                ->join("isms_db.pcsr_generic_info g", "pm.generic_id = g.id", "left")
                ->join("db_emr.lib_dosage dos", "pm.dosage_id = dos.dosage_id", "left")
                ->join("db_emr.lib_frequency f", "pm.frequency_id = f.frequency_id", "left")
                ->join("db_emr.lib_route r", "pm.route_id = r.route_id", "left")
                ->join("db_emr.lib_duration dur", "pm.duration_id = dur.duration_id", "left")
                ->where("pm.patient_id", $patient_id)
                ->where("pm.encounter_id", $enc_id)
                ->where("pm.archived", 0)
                ->get()
                ->result();

            // ✅ Immunizations
            $row->immunizations = $this->db
                ->select("pi.*, v.vaccine_name, d.dose_desc, r.route_desc, s.site_desc, u.fullname AS given_by_name")
                ->from("tbl_patient_immunizations pi")
                ->join("lib_vaccine v", "pi.vaccine_id = v.vaccine_id", "left")
                ->join("lib_dose d", "pi.dose_id = d.dose_id", "left")
                ->join("lib_route r", "pi.route_id = r.route_id", "left")
                ->join("lib_site s", "pi.site_id = s.site_id", "left")
                ->join("aauth_users u", "pi.given_by = u.id", "left")
                ->where("pi.patient_id", $patient_id)
                ->where("pi.encounter_id", $enc_id)
                ->where("pi.archived", 0)
                ->order_by("pi.date_given", "DESC")
                ->get()
                ->result();

            // ✅ Documents
            $row->documents = $this->db
                ->select("pd.*, dt.document_type_desc, u.fullname AS uploaded_by_name")
                ->from("tbl_patient_documents pd")
                ->join("lib_document_type dt", "pd.document_type_id = dt.document_type_id", "left")
                ->join("aauth_users u", "pd.uploaded_by = u.id", "left")
                ->where("pd.patient_id", $patient_id)
                ->where("pd.encounter_id", $enc_id)
                ->where("pd.archived", 0)
                ->order_by("pd.date_uploaded", "DESC")
                ->get()
                ->result();

            // ✅ Clinical Notes
            $notes_data = $CI->PatientNotes_model
                ->get_dynamic_patient_notes_datatable($patient_id, null, null, $enc_id);
            $row->notes = !empty($notes_data['data']) ? $notes_data['data'] : [];

            // ✅ Appointments
            $query = $this->db
                ->select("
        pa.*,
        CONCAT(d.pDoctorLastName, ', ', d.pDoctorFirstName, ' ', IFNULL(d.pDoctorMiddleName,'')) AS doctor_name,
        IFNULL(at.appoint_type_desc, '-') AS appoint_type,
        IFNULL(ast.appoint_status_desc, '-') AS appoint_status,
        IFNULL(cc.chief_complaint_desc, '-') AS chief_complaint
    ")
                ->from("tbl_patient_appointment pa") // singular table name
                ->join("db_eclaims_staging.lib_doctors d", "d.doctor_id = pa.doctor_id", "left")
                ->join("db_emr.lib_appoint_type at", "at.appoint_type_id = pa.appoint_type_id", "left")
                ->join("db_emr.lib_appoint_status ast", "ast.appoint_status_id = pa.appoint_status_id", "left")
                ->join("db_emr.lib_chief_complaint cc", "cc.chief_complaint_id = pa.chief_complaint_id", "left")
                ->where("pa.patient_id", $patient_id)
                ->where("pa.encounter_id", $enc_id)
                ->where("pa.archived", 0)
                ->order_by("pa.appointment_date", "DESC")
                ->get();

            if (!$query) {
                // debug if query fails
                log_message('error', 'Appointments query failed: ' . print_r($this->db->error(), true));
                $row->appointments = [];
            } else {
                $row->appointments = $query->result();
            }
        }

        return [$results, $total];
    }

    // ===== END OF PATIENT VISIT DATA =====

    public function get_latest_vitals_by_patient($patient_id)
    {
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

    public function getPatientVitals($start, $length, $search, $patient_id, $encounterFilter)
    {
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

        if ($encounterFilter != 'all') {
            $this->db->where('v.encounter_id', $encounterFilter);
        }

        $res = $this->db->get()->result();

        return array($res, $num);
    }

    public function getPatientAppointments($start, $length, $search, $patient_id)
    {
        $eclaims_db = $this->load->database('eclaims', TRUE);
        log_message('info', 'Executing getPatientAppointments function (latest created first).');

        // Step 1: Select appointment info
        $this->db->select('a.*, 
                    t.appoint_type_desc AS appoint_type, 
                    s.appoint_status_desc AS appoint_status, 
                    c.chief_complaint_desc AS chief_complaint');
        $this->db->from('tbl_patient_appointment a');
        $this->db->join('lib_appoint_type t', 't.appoint_type_id = a.appoint_type_id', 'left');
        $this->db->join('lib_appoint_status s', 's.appoint_status_id = a.appoint_status_id', 'left');
        $this->db->join('lib_chief_complaint c', 'c.chief_complaint_id = a.chief_complaint_id', 'left');
        $this->db->where('a.patient_id', $patient_id);
        $this->db->where('a.archived', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('t.appoint_type_desc', $search);
            $this->db->or_like('s.appoint_status_desc', $search);
            $this->db->or_like('c.chief_complaint_desc', $search);
            $this->db->group_end();
        }

        // ✅ ORDER BY created_at to get newest record first
        $this->db->order_by('a.created_at', 'DESC');
        $this->db->limit($length, $start);
        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'Main query failed: ' . $this->db->last_query());
            return array([], 0);
        }

        $appointments = $query->result();

        // Step 2: Fetch doctor names from eclaims_db
        foreach ($appointments as &$appointment) {
            if ($appointment->doctor_id) {
                $eclaims_db->select('pDoctorFirstName, pDoctorLastName');
                $eclaims_db->from('lib_doctors');
                $eclaims_db->where('doctor_id', $appointment->doctor_id);
                $doc_query = $eclaims_db->get();

                if ($doc_query && $doc_query->num_rows() > 0) {
                    $doc = $doc_query->row();
                    $appointment->doctor_name = $doc->pDoctorLastName . ', ' . $doc->pDoctorFirstName;
                } else {
                    $appointment->doctor_name = 'Unknown';
                }
            } else {
                $appointment->doctor_name = 'N/A';
            }
        }

        // Step 3: Count total
        $this->db->select('COUNT(*) as total');
        $this->db->from('tbl_patient_appointment a');
        $this->db->where('a.patient_id', $patient_id);
        $this->db->where('a.archived', 0);
        $total_query = $this->db->get();

        if (!$total_query) {
            log_message('error', 'Total count query failed: ' . $this->db->last_query());
            return array([], 0);
        }

        $total = $total_query->row()->total;

        return array($appointments, $total);
    }

    // Get the most recent active encounter for a patient
    public function getCurrentEncounter($patient_id)
    {
        if (empty($patient_id)) {
            log_message('error', 'getCurrentEncounter() called with empty patient_id');
            return null;
        }

        // These are the active status IDs: 1=ONGO, 2=FADM, 3=OBS, 4=PRAD
        $active_status_ids = [1, 2, 3, 4];

        // ✅ Step 1: Try to get an active encounter first
        $this->db->select('encounter_id')
            ->where('patient_id', $patient_id)
            ->where_in('visit_status_id', $active_status_ids)
            ->order_by('encounter_date', 'DESC')
            ->limit(1);

        $query = $this->db->get('tbl_patient_encounter');

        if (!$query) {
            log_message('error', 'Database query failed in getCurrentEncounter(). Last query: ' . $this->db->last_query());
            return null;
        }

        if ($query->num_rows() > 0) {
            return $query->row()->encounter_id;
        }

        // ✅ Step 2: If no active encounter found, get the latest (even if discharged/death)
        $this->db->select('encounter_id')
            ->where('patient_id', $patient_id)
            ->where('archived', 0)
            ->order_by('encounter_date', 'DESC')
            ->limit(1);

        $fallback_query = $this->db->get('tbl_patient_encounter');

        if (!$fallback_query) {
            log_message('error', 'Fallback query failed in getCurrentEncounter(). Last query: ' . $this->db->last_query());
            return null;
        }

        if ($fallback_query->num_rows() > 0) {
            return $fallback_query->row()->encounter_id;
        }

        return null;
    }


    public function getEncounterById($encounter_id)
    {
        if (empty($encounter_id)) {
            return null;
        }
        $query = $this->db->get_where('tbl_patient_encounter', ['encounter_id' => $encounter_id]);
        return $query->num_rows() > 0 ? $query->row_array() : null;
    }



    // Set current encounter for a patient
    public function setCurrentEncounter($patient_id, $encounter_id)
    {
        // Validate input to ensure patient_id and encounter_id are provided
        if (empty($patient_id) || empty($encounter_id)) {
            return false; // Optionally, throw an exception or log an error
        }

        // Start a transaction to ensure both updates are done together
        $this->db->trans_start();

        // Reset any previous "current" encounter for the patient
        $this->db->where('patient_id', $patient_id)
            ->update('tbl_patient_encounter', ['is_current' => 0]);

        // Set the new encounter as the "current" encounter
        $this->db->where('encounter_id', $encounter_id)
            ->update('tbl_patient_encounter', ['is_current' => 1]);

        // Complete the transaction and return true if successful, false otherwise
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false; // Transaction failed
        }

        return true; // Transaction successful
    }


    public function getVitalsByPatient($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $query = $this->db->get('tbl_patient_vitals');
        return $query->result_array();
    }

    public function getVitalById($vitals_id)
    {
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
    public function updateVital($vitals_id, $data)
    {
        if (empty($vitals_id)) {
            return false; // Invalid ID
        }

        $this->db->where('vitals_id', $vitals_id);
        $success = $this->db->update('tbl_patient_vitals', $data);

        return $success; // TRUE if no SQL error
    }

    public function get_latest_physician_and_complaint($patient_id)
    {
        $eclaims_db = $this->load->database('eclaims', TRUE);
        log_message('info', 'Fetching latest encounter details for patient ID: ' . $patient_id);

        $this->db->select('
        e.encounter_date,
        e.chief_complaint,
        c.chief_complaint_desc,
        e.assigned_physician
    ');
        $this->db->from('tbl_patient_encounter e');
        $this->db->join('lib_chief_complaint c', 'e.chief_complaint = c.chief_complaint_id', 'left');
        $this->db->where('e.patient_id', $patient_id);
        $this->db->where('e.archived', 0);
        $this->db->order_by('e.encounter_date', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            $encounter = $query->row_array();

            $assigned_ids = [];
            if (!empty($encounter['assigned_physician'])) {
                $assigned_ids = array_map('intval', explode(',', $encounter['assigned_physician']));
            }
            $encounter['assigned_physician_ids'] = $assigned_ids;

            // Use the description if exists; otherwise fallback to raw chief_complaint ID
            $encounter['chief_complaint'] = !empty($encounter['chief_complaint_desc']) ? $encounter['chief_complaint_desc'] : $encounter['chief_complaint'];

            // Fetch doctor details from eclaims
            $eclaims_db->select('pDoctorFirstName, pDoctorLastName, pDoctorSpecialization, pDoctorLicenseNumber');
            $eclaims_db->from('lib_doctors');
            $eclaims_db->where('doctor_id', $encounter['assigned_physician']);
            $doc_query = $eclaims_db->get();

            if ($doc_query->num_rows() > 0) {
                $doc = $doc_query->row();
                $encounter['doctor_name'] = $doc->pDoctorLastName . ', ' . $doc->pDoctorFirstName;
                $encounter['doctor_specialization'] = $doc->pDoctorSpecialization;
                $encounter['doctor_license_no'] = $doc->pDoctorLicenseNumber;
            } else {
                $encounter['doctor_name'] = 'Unknown';
                $encounter['doctor_specialization'] = 'N/A';
                $encounter['doctor_license_no'] = 'N/A';
            }

            // Format date and time
            $encounter['formatted_date'] = date('F j, Y', strtotime($encounter['encounter_date']));
            $encounter['formatted_time'] = date('g:i A', strtotime($encounter['encounter_date']));
            $encounter['appointment_datetime'] = $encounter['formatted_date'] . ' at ' . $encounter['formatted_time'];

            return $encounter;
        }

        return null;
    }

    public function get_latest_medication($patient_id)
    {
        $this->db->select('
        pm.medication_id,
        pm.date_prescribed,
        pm.remarks,
        g.generic_name
    ');
        $this->db->from('tbl_patient_medications pm');
        $this->db->join('isms_db.pcsr_generic_info g', 'pm.generic_id = g.id', 'left');
        $this->db->where('pm.patient_id', $patient_id);
        $this->db->where('pm.archived', 0);
        $this->db->order_by('pm.date_prescribed', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if (!$query) {
            $error = $this->db->error();
            log_message('error', 'DB Error in get_latest_medication: ' . print_r($error, true));
            return null;
        }

        return $query->row_array();
    }


    public function archiveVisit($encounter_id)
    {
        // Get the ID of the "Closed" status
        $closed_status_id = $this->db
            ->select('visit_status_id')
            ->from('lib_visit_status')
            ->where('visit_status_abbr', 'CLOS')
            ->get()
            ->row()
            ->visit_status_id ?? null;

        return $this->db
            ->where('encounter_id', $encounter_id)
            ->update('tbl_patient_encounter', [
                'archived' => 1,
                'is_current' => 0,
                'visit_status_id' => $closed_status_id, // ✅ set to closed
                'date_last_modified' => date('Y-m-d H:i:s'),
                'last_modified_by' => $this->session->userID
            ]);
    }

    // public function updateVisitStatus($encounter_id, $status_id,  $discharge_outcome = null)
    // {
    //     $this->db->where('encounter_id', $encounter_id);

    //     // Build data array for update
    //     $data = [
    //         'visit_status_id'    => $status_id,
    //         'date_last_modified' => date('Y-m-d H:i:s'),
    //         'last_modified_by'   => $this->session->userdata('user_id')
    //     ];

    //     // If status is among 6-16, set is_current to 0
    //     if (in_array((int)$status_id, [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16])) {
    //         $data['is_current'] = 0;
    //     }

    //     // Save discharge outcome only if Discharged was selected
    //     if ((int)$status_id === 8 && !empty($discharge_outcome)) {
    //         $data['discharged_status'] = $discharge_outcome;  // 👈 save to discharged_status column
    //     } else {
    //         // clear discharged_status if not discharged
    //         $data['discharged_status'] = null;
    //     }

    //     return $this->db->update('tbl_patient_encounter', $data);
    // }

    // public function updateVisitStatus($encounter_id, $status_id, $discharged_status = null)
    // {
    //     $this->db->where('encounter_id', $encounter_id);

    //     // Build data array for update
    //     $data = [
    //         'visit_status_id'    => $status_id,
    //         'date_last_modified' => date('Y-m-d H:i:s'),
    //         'last_modified_by'   => $this->session->userdata('user_id')
    //     ];

    //     // If status is among 6-16, set is_current to 0
    //     if (in_array((int)$status_id, [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16])) {
    //         $data['is_current'] = 0;
    //     }

    //     // Save discharged_status only if the visit status is "Discharged"
    //     $discharged_status_ids = [6]; // <-- put the actual ID for "Discharged" here
    //     if (in_array((int)$status_id, $discharged_status_ids)) {
    //         $data['discharged_status'] = !empty($discharged_status) ? $discharged_status : null;
    //     } else {
    //         $data['discharged_status'] = null; // clear it if another status is selected
    //     }

    //     // Log the data for debugging
    //     log_message('debug', 'updateVisitStatus called with encounter_id=' . $encounter_id .
    //         ', status_id=' . $status_id .
    //         ', discharged_status=' . $discharged_status);
    //     log_message('debug', 'Data array being updated: ' . print_r($data, true));

    //     $result = $this->db->update('tbl_patient_encounter', $data);

    //     // Log the result of the update
    //     log_message('debug', 'Update result: ' . ($result ? 'Success' : 'Failed'));

    //     return $result;
    // }

    public function updateVisitStatus($encounter_id, $status_id, $discharged_status = null, $type_option = null)
    {
        $selected_type = $type_option;
        $current_datetime = date('Y-m-d H:i:s');
        $current_date = date('Y-m-d');

        $is_medicine = ($selected_type === 'Medicine') ? 1 : 0;
        $is_surgical = ($selected_type === 'Surgical') ? 1 : 0;

        // Build data array for tbl_patient_encounter
        $data = [
            'medicine'           => $is_medicine,
            'surgical'           => $is_surgical,
            'visit_status_id'    => $status_id,
            'date_last_modified' => $current_datetime,
            'last_modified_by'   => $this->session->userdata('user_id')
        ];

        // Status IDs that mark the encounter as non-current
        if (in_array((int)$status_id, [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16])) {
            $data['is_current'] = 0;
        }

        $discharged_status_ids = [6]; // 6 = Discharged
        if (in_array((int)$status_id, $discharged_status_ids)) {
            $data['discharged_status'] = !empty($discharged_status) ? $discharged_status : null;
            $data['discharge_date'] = $current_date;
        } else {
            $data['discharged_status'] = null;
        }

        // Update tbl_patient_encounter (EMR DB)
        $this->db->where('encounter_id', $encounter_id);
        $result = $this->db->update('tbl_patient_encounter', $data);

        // If visit_status_id = 6 (Discharged), update ward_db3 tables
        if ((int)$status_id === 6) {

            // Get patient_id from tbl_patient_encounter
            $query = $this->db->select('patient_id')
                ->where('encounter_id', $encounter_id)
                ->get('tbl_patient_encounter');

            if ($query && $query->num_rows() > 0) {
                $patient = $query->row();

                if (!empty($patient->patient_id)) {
                    // Connect to ward_db3
                    $wardDB = $this->load->database('ward_db3', TRUE);

                    // 1. Find the active trans_group record to get the current c_bedID
                    // Assumes cur_statID = 5 is the active/assigned status
                    $current_group = $wardDB->select('trans_group_id, c_bedID')
                        ->where('patient_id', $patient->patient_id)
                        ->where('cur_statID', 5)
                        ->limit(1)
                        ->get('t_trans_group')
                        ->row();

                    if ($current_group) {
                        $bed_to_clear = $current_group->c_bedID;
                        $trans_group_id = $current_group->trans_group_id;

                        // 1. Update the active group status to Discharged (14)
                        $wardDB->where('trans_group_id', $trans_group_id);
                        $wardDB->update('t_trans_group', ['cur_statID' => 14]);

                        // Update all related transactions to status 14
                        $wardDB->where('trans_group_id', $trans_group_id);
                        $wardDB->update('t_transaction', ['c_statusID' => 14]);

                        // *** Removed the update to status 1 as requested by the user. ***

                        // *** CRITICAL STEP: Clear the bed assignment in t_bed ***
                        // *** ACTIONED: Changed NULL to 0 as requested ***
                        $wardDB->where('c_bedID', $bed_to_clear);
                        $wardDB->update('t_bed', ['c_patientID' => 0]);
                        // ******************************************************

                        log_message('debug', 'Ward Bed cleared (c_patientID=0): ' . $bed_to_clear . ' for patient: ' . $patient->patient_id);
                    } else {
                        // Fallback to update any pending transaction groups if the 'active' group (5) wasn't found
                        $trans_groups = $wardDB->select('trans_group_id')
                            ->where('patient_id', $patient->patient_id)
                            ->get('t_trans_group')
                            ->result();

                        if (!empty($trans_groups)) {
                            // Update to 14
                            $ids = array_column($trans_groups, 'trans_group_id');
                            $wardDB->where_in('trans_group_id', $ids);
                            $wardDB->update('t_trans_group', ['cur_statID' => 14]);
                            $wardDB->where_in('trans_group_id', $ids);
                            $wardDB->update('t_transaction', ['c_statusID' => 14]);

                            // *** Removed the secondary update to status 1 as requested by the user. ***
                        }
                    }
                } else {
                    log_message('error', 'Cannot update ward_db3: patient_id is NULL for encounter_id=' . $encounter_id);
                }
            }
        }

        log_message('debug', 'updateVisitStatus called with encounter_id=' . $encounter_id .
            ', status_id=' . $status_id .
            ', discharged_status=' . $discharged_status);
        log_message('debug', 'Data array updated: ' . print_r($data, true));
        log_message('debug', 'Update result: ' . ($result ? 'Success' : 'Failed'));

        return $result;
    }



    public function getPatientMedications($start, $length, $search, $patient_id)
    {
        // COUNT for DataTables
        $this->db->from('tbl_patient_medications m');
        $this->db->where('m.patient_id', $patient_id);
        $this->db->where('m.archived', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('m.remarks', $search);
            $this->db->group_end();
        }

        $num = $this->db->count_all_results();

        // FETCH data
        $this->db->select("
        m.*,
        g.generic_name AS medicine_name,
        ld.dosage_desc AS dosage,
        lf.frequency_desc AS frequency,
        lr.route_desc AS route,
        ldu.duration_desc AS duration
    ");
        $this->db->from('tbl_patient_medications m');

        // ✅ Updated: join to isms_db.pcsr_generic_info
        $this->db->join('isms_db.pcsr_generic_info g', 'g.id = m.generic_id', 'left');

        $this->db->join('lib_dosage ld', 'ld.dosage_id = m.dosage_id', 'left');
        $this->db->join('lib_frequency lf', 'lf.frequency_id = m.frequency_id', 'left');
        $this->db->join('lib_route lr', 'lr.route_id = m.route_id', 'left');
        $this->db->join('lib_duration ldu', 'ldu.duration_id = m.duration_id', 'left');
        $this->db->where('m.patient_id', $patient_id);
        $this->db->where('m.archived', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('g.generic_name', $search);
            $this->db->or_like('ld.dosage_desc', $search);
            $this->db->or_like('lf.frequency_desc', $search);
            $this->db->or_like('lr.route_desc', $search);
            $this->db->or_like('ldu.duration_desc', $search);
            $this->db->or_like('m.encounter_id', $search);
            $this->db->group_end();
        }

        $this->db->order_by('m.date_prescribed', 'DESC');
        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        // ⚡️ Fetch doctor names from `eclaims` db manually
        $eclaims_db = $this->load->database('eclaims', TRUE);
        foreach ($res as &$row) {
            $row->prescribed_by_name = 'Unknown';
            if (!empty($row->doctor_id)) {
                $eclaims_db->select("pDoctorFirstName, pDoctorMiddleName, pDoctorLastName");
                $eclaims_db->from("lib_doctors");
                $eclaims_db->where("doctor_id", $row->doctor_id);
                $doc_query = $eclaims_db->get();
                if ($doc_query && $doc_query->num_rows() > 0) {
                    $doc = $doc_query->row();
                    $row->prescribed_by_name = trim("{$doc->pDoctorFirstName} {$doc->pDoctorMiddleName} {$doc->pDoctorLastName}");
                }
            }
        }

        return array($res, $num);
    }


    public function getPatientImmunizations($start, $length, $search, $patient_id, $encounter_filter)
    {
        // Count total records for filtering
        $this->db->select('i.immunization_id');
        $this->db->from('tbl_patient_immunizations i');
        $this->db->join('lib_vaccine v', 'v.vaccine_id = i.vaccine_id', 'left');
        $this->db->join('lib_dose d', 'd.dose_id = i.dose_id', 'left');
        $this->db->join('lib_route r', 'r.route_id = i.route_id', 'left');
        $this->db->join('lib_site s', 's.site_id = i.site_id', 'left');
        $this->db->join('aauth_users u', 'u.id = i.given_by', 'left');
        $this->db->where('i.patient_id', $patient_id);
        $this->db->where('i.archived', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('i.date_given', $search);
            $this->db->or_like('i.lot_number', $search);
            $this->db->or_like('v.vaccine_name', $search);
            $this->db->or_like('d.dose_desc', $search);
            $this->db->or_like('r.route_desc', $search);
            $this->db->or_like('s.site_desc', $search);
            $this->db->or_like('u.fullname', $search);
            $this->db->group_end();
        }
        $num = $this->db->count_all_results();

        // Get paginated records
        $this->db->select('i.*, 
        COALESCE(v.vaccine_name, "-") as vaccine_name,
        v.vaccine_id,
        COALESCE(d.dose_desc, "-") as dose,
        d.dose_id,
        COALESCE(r.route_desc, "-") as route,
        r.route_id,
        COALESCE(s.site_desc, "-") as site,
        s.site_id,
        COALESCE(u.fullname, "-") as given_by_name');
        $this->db->from('tbl_patient_immunizations i');
        $this->db->join('lib_vaccine v', 'v.vaccine_id = i.vaccine_id', 'left');
        $this->db->join('lib_dose d', 'd.dose_id = i.dose_id', 'left');
        $this->db->join('lib_route r', 'r.route_id = i.route_id', 'left');
        $this->db->join('lib_site s', 's.site_id = i.site_id', 'left');
        $this->db->join('aauth_users u', 'u.id = i.given_by', 'left');
        $this->db->where('i.patient_id', $patient_id);
        $this->db->where('i.archived', 0);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('i.date_given', $search);
            $this->db->or_like('i.lot_number', $search);
            $this->db->or_like('v.vaccine_name', $search);
            $this->db->or_like('d.dose_desc', $search);
            $this->db->or_like('r.route_desc', $search);
            $this->db->or_like('s.site_desc', $search);
            $this->db->or_like('u.fullname', $search);
            $this->db->group_end();
        }
        if ($encounter_filter != 'all') {
            $this->db->where('i.encounter_id', $encounter_filter);
        }
        $this->db->order_by('i.date_given', 'DESC');

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        return array($res, $num);
    }

    public function getPatientDocuments($start, $length, $search, $patient_id)
    {
        // COUNT
        $this->db->select('d.document_id');
        $this->db->from('tbl_patient_documents d');
        $this->db->join('lib_document_type dt', 'dt.document_type_id = d.document_type_id', 'left');
        $this->db->join('aauth_users u', 'u.id = d.uploaded_by', 'left');
        $this->db->where('d.patient_id', $patient_id);
        $this->db->where('d.archived', 0);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('d.file_name', $search);
            $this->db->or_like('d.description', $search);
            $this->db->or_like('dt.document_type_desc', $search);
            $this->db->or_like('u.fullname', $search);
            $this->db->group_end();
        }
        $num = $this->db->count_all_results();

        // FETCH
        $this->db->select("
        d.document_id,
        CASE
            WHEN COALESCE(d.file_name, '') != '' THEN d.file_name
            WHEN COALESCE(d.file_path, '') != '' THEN SUBSTRING_INDEX(d.file_path, '/', -1)
            ELSE '-'
        END AS file_name,
        d.file_path,
        d.description,
        d.date_uploaded,
        COALESCE(dt.document_type_desc, '-') AS document_type_desc,
        COALESCE(u.fullname, '-') AS uploaded_by_name
    ", FALSE);
        $this->db->from('tbl_patient_documents d');
        $this->db->join('lib_document_type dt', 'dt.document_type_id = d.document_type_id', 'left');
        $this->db->join('aauth_users u', 'u.id = d.uploaded_by', 'left');
        $this->db->where('d.patient_id', $patient_id);
        $this->db->where('d.archived', 0);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('d.file_name', $search);
            $this->db->or_like('d.description', $search);
            $this->db->or_like('dt.document_type_desc', $search);
            $this->db->or_like('u.fullname', $search);
            $this->db->group_end();
        }
        $this->db->order_by('d.date_uploaded', 'DESC');

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        return [$res, $num];
    }

    public function getPatientsWithActiveFlag()
    {
        $this->db->select("pl.*,
        CASE 
            WHEN EXISTS (
                SELECT 1 
                FROM tbl_patient_encounter pe 
                WHERE pe.patient_id = pl.patient_id 
                  AND pe.is_current = 1 
                  AND pe.visit_status_id IN (1, 2, 3, 4, 5, 15, 16)
                  AND pe.archived = 0
            ) THEN 1
            ELSE 0
        END AS is_active");
        $this->db->from("tbl_patient_list pl");
        $this->db->where('pl.archived', 0); // Optional: exclude archived patients
        return $this->db->get()->result();
    }

    public function get_visit_type($encounter_id)
    {
        $this->db->select('lvt.visit_type_desc')
            ->from('tbl_patient_encounter tpe')
            ->join('lib_visit_type lvt', 'tpe.visit_type_id = lvt.visit_type_id', 'left')
            ->where('tpe.encounter_id', $encounter_id)
            ->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return strtolower(trim($query->row()->visit_type_desc)); // Ensure lowercase string
        }

        return null;
    }

    public function get_datatables_for_admission()
    {
        // Query 1: Patients with For Admission status
        $sql1 = "
        SELECT
            pe.encounter_id AS id,
            p.patient_id,
            p.mrn,
            CONCAT(p.lastname, ', ', p.firstname, ' ', p.middlename) AS patient_name,
            p.sex,
            p.dob,
            p.contact_no,
            CONCAT_WS(', ', p.street_address) AS address,
            u.fullname AS encoded_by,
            vs.visit_status_desc AS status
        FROM tbl_patient_encounter pe
        LEFT JOIN tbl_patient_list p ON p.patient_id = pe.patient_id
        LEFT JOIN lib_visit_status vs ON vs.visit_status_id = pe.visit_status_id
        LEFT JOIN aauth_users u ON u.id = pe.encoded_by
        WHERE vs.visit_status_desc = 'For Admission' AND pe.archived = 0
    ";

        // Query 2: Patients with NO encounter at all
        $sql2 = "
        SELECT
            NULL AS id,
            p.patient_id,
            p.mrn,
            CONCAT(p.lastname, ', ', p.firstname, ' ', p.middlename) AS patient_name,
            p.sex,
            p.dob,
            p.contact_no,
            CONCAT_WS(', ', p.street_address) AS address,
            NULL AS encoded_by,
            'No Encounter' AS status
        FROM tbl_patient_list p
        LEFT JOIN tbl_patient_encounter pe ON pe.patient_id = p.patient_id
        WHERE pe.patient_id IS NULL 
        ";

        // Combine both queries
        $full_sql = "$sql1 UNION $sql2";

        $query = $this->db->query($full_sql);
        return $query ? $query->result() : [];
    }


    public function get_datatables_for_er($start, $length, $search)
    {
        // Count total rows
        $this->db->select('t1.encounter_id'); // assuming encounter_id is the PK
        $this->db->from('tbl_patient_encounter t1');
        $this->db->join('tbl_patient_list p', 't1.patient_id = p.patient_id', 'left');
        $this->db->where(['p.archived' => 0]);
        $this->db->where(['t1.visit_type_id' => 3]);
        $this->db->group_by('p.patient_id');
        if (!empty($search)) {
            $this->db->group_start()
                ->like('p.firstname', $search)
                ->or_like('p.lastname', $search)
                ->or_like('p.middlename', $search)
                ->or_like('p.street_address', $search)
                ->or_like('p.mrn', $search)
                ->group_end();
        }

        $num = $this->db->get()->num_rows();


        // Fetch actual data
        $this->db->select("
            t1.*,
            p.*,
            p.isActive,
            u.fullname,
            l1.region_desc,
            l2.province_desc,
            l3.city_desc,
            l4.barangay_desc
        ");

        $this->db->from('tbl_patient_encounter t1');
        $this->db->join('tbl_patient_list p', 't1.patient_id = p.patient_id', 'left');
        $this->db->where(['p.archived' => 0]);
        $this->db->where(['t1.visit_type_id' => 3]);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('p.firstname', $search)
                ->or_like('p.lastname', $search)
                ->or_like('p.middlename', $search)
                ->or_like('p.street_address', $search)
                ->or_like('p.mrn', $search)
                ->group_end();
        }

        $this->db->join('lib_region l1', 'p.region = l1.region_id', 'left');
        $this->db->join('lib_province l2', 'p.province = l2.province_id', 'left');
        $this->db->join('lib_cities_mun l3', 'p.city = l3.city_id', 'left');
        $this->db->join('lib_barangay l4', 'p.barangay = l4.barangay_id', 'left');
        $this->db->join('aauth_users u', 'p.encoded_by = u.id', 'left');

        $this->db->group_by('p.patient_id');  // <-- ensures one row per patient

        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $res = $this->db->get()->result();

        return [$res, $num];
    }


    public function get_scheduled_appointments_today()
    {
        $today = date('Y-m-d');

        $this->db->select('
        a.appointment_id,
        a.encounter_id,
        a.patient_id,
        a.doctor_id,
        a.appointment_date,
        a.appointment_time_start,
        a.appointment_time_end,
        a.notes,
        a.created_at,
        a.updated_at,
        a.appoint_type_id,
        a.appoint_status_id,
        a.chief_complaint_id,
        a.archived,
        a.finalized_at,
        p.mrn,
        p.lastname,
        p.firstname,
        p.middlename,
        p.sex,
        p.dob,
        p.contact_no,
        p.street_address,
        p.encoded_by AS patient_encoded_by,
        u.fullname AS encoded_by_fullname
    ');
        $this->db->from('tbl_patient_appointment a');
        $this->db->join('tbl_patient_list p', 'a.patient_id = p.patient_id', 'left');
        $this->db->join('aauth_users u', 'p.encoded_by = u.id', 'left'); // ← Join using patient encoded_by

        $this->db->where('DATE(a.appointment_date)', $today);
        $this->db->where('a.archived', 0);

        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'DB Query failed: ' . $this->db->last_query());
            log_message('error', 'DB Error: ' . print_r($this->db->error(), true));
            return [];
        }

        return $query->result_array();
    }

    public function getPatientPhoto($patient_id)
    {
        return $this->db->select('photo_filename')
            ->from('tbl_patient_list')
            ->where('patient_id', $patient_id)
            ->get()->row('photo_filename');
    }

    public function get_latest_visit_date($patient_id)
    {
        $row = $this->db
            ->select('encounter_date')
            ->where('patient_id', $patient_id)
            ->order_by('encounter_date', 'DESC')
            ->limit(1)
            ->get('tbl_patient_encounter') // or your actual encounters table name
            ->row();

        return $row ? $row->encounter_date : null;
    }

    function updateExpiredPatient($patient_id)
    {
        if (empty($patient_id)) {
            log_message('error', 'updateExpiredPatient called with empty patient_id or data');
            return false;
        }

        // Update the patient record
        $this->db->where('patient_id', $patient_id);
        $success = $this->db->update('tbl_patient_list', ['is_expired' => 1]);

        // Update the patient existing appointments
        $this->db->where('patient_id', $patient_id);
        $this->db->where('appoint_status_id', 1);
        $this->db->update('tbl_patient_appointment', ['appoint_status_id' => 6]);

        if (!$success) {
            log_message('error', 'Failed to update patient: ' . $this->db->last_query());
        }

        return $success; // TRUE if no SQL error
    }

    public function get_latest_consultation($patient_id)
    {
        return $this->db->select('classification, hospital_admission, date_and_time_of_consult, date_and_time_discharged, referred_from_another_health_care_institution, encounter_id')
            ->where('patient_id', $patient_id)
            ->order_by('date_and_time_of_consult', 'DESC')
            ->limit(1)
            ->get('tbl_out_patient_clinical_abstract')
            ->row_array();
    }
}
