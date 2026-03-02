<?php
/*by Pearlsss 08062025*/
class ReportModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        // Load the PatientModel so we can use its functions
        $this->load->model('PatientModel');
        $this->load->database(); // kailangan para gumana queries
    }

    public function getDailyOccupancyData($date_from, $date_to, $enc_type)
    {
        #query to get daily occupancy data here
    }

    public function getCensus($date_from = null, $date_to = null, $report_type = null)
    {
        if (!$date_from) $date_from = date('Y-m-d');
        if (!$date_to) $date_to = date('Y-m-d');

        $result = [
            'admissions'  => [],
            'discharges'  => [],
            'transferin'  => [],
            'transferout' => [],
            'death'       => []
        ];

        // ------------------- ADMISSIONS -------------------
        if ($report_type === null || $report_type === 'admissions') {
            $this->db->select("
        e.encounter_id,
        e.patient_id,
        p.mrn AS mrn,
        CONCAT(p.lastname, ', ', p.firstname, ' ', p.middlename, ' ', COALESCE(p.suffix_id, '')) AS fullname,
        DATE(e.encounter_date) AS admission_date,
        TIME(e.encounter_date) AS admission_time,
        COALESCE(b.c_bedName, 'Not assigned') AS bed_no,
        COALESCE(r.c_roomName, 'Not assigned') AS room_no,
        COALESCE(r.c_roomType, 0) AS room_type,
        CASE WHEN COALESCE(r.c_roomType,0) IN (1,2) THEN 'PAY' WHEN e.visit_type_id = 2 THEN 'PAY' ELSE 'CHARITY' END AS room_category,
        p.sex
    ");
            $this->db->from("tbl_patient_encounter e");
            $this->db->join("tbl_patient_list p", "e.patient_id = p.patient_id", "left");
            $this->db->join("ward_db3.t_bed b", "p.patient_id = b.c_patientID", "left");
            $this->db->join("ward_db3.t_room r", "b.c_roomID = r.c_roomID", "left");
            $this->db->where("DATE(e.encounter_date) >=", $date_from);
            $this->db->where("DATE(e.encounter_date) <=", $date_to);
            $this->db->where("e.visit_type_id", 2); // assuming 2 = inpatients
            $this->db->order_by("e.encounter_date", "ASC");
            $result['admissions'] = $this->db->get()->result();
        }


        // ------------------- DISCHARGES -------------------
        if ($report_type === null || $report_type === 'discharges') {
            $this->db->select("
            e.encounter_id,
            e.patient_id,
            p.mrn AS mrn,
            CONCAT(p.lastname, ', ', p.firstname, ' ', p.middlename, ' ', COALESCE(p.suffix_id, '')) AS fullname,
            DATE(e.date_last_modified) AS discharge_date,
            TIME(e.date_last_modified) AS discharge_time,
            e.discharged_status,
            COALESCE(b.c_bedName, 'Not assigned') AS bed_no,
            COALESCE(r.c_roomName, 'Not assigned') AS room_no,
            COALESCE(r.c_roomType, 0) AS room_type,
            CASE 
                WHEN r.c_roomType IN (1,2) THEN 'PAY'
                ELSE 'CHARITY'
            END AS room_category,
            p.sex
        ");
            $this->db->from("tbl_patient_encounter e");
            $this->db->join("tbl_patient_list p", "e.patient_id = p.patient_id", "left");
            $this->db->join("ward_db3.t_bed b", "p.patient_id = b.c_patientID", "left");
            $this->db->join("ward_db3.t_room r", "b.c_roomID = r.c_roomID", "left");
            $this->db->where("DATE(e.date_last_modified) >=", $date_from);
            $this->db->where("DATE(e.date_last_modified) <=", $date_to);
            $this->db->where("e.visit_status_id", 6); // discharged alive
            $this->db->order_by("e.date_last_modified", "ASC");
            $result['discharges'] = $this->db->get()->result();
        }

        // ------------------- DEATHS -------------------
        if ($report_type === null || $report_type === 'death') {
            $this->db->select("
            e.encounter_id,
            e.patient_id,
            p.mrn AS mrn,
            CONCAT(p.lastname, ', ', p.firstname, ' ', p.middlename, ' ', COALESCE(p.suffix_id, '')) AS fullname,
            DATE(e.date_last_modified) AS death_date,
            TIME(e.date_last_modified) AS death_time,
            e.discharged_status,
            COALESCE(b.c_bedName, 'Not assigned') AS bed_no,
            COALESCE(r.c_roomName, 'Not assigned') AS room_no,
            COALESCE(r.c_roomType, 0) AS room_type,
            CASE 
                WHEN r.c_roomType IN (1,2) THEN 'PAY'
                ELSE 'CHARITY'
            END AS room_category,
            p.sex
        ");
            $this->db->from("tbl_patient_encounter e");
            $this->db->join("tbl_patient_list p", "e.patient_id = p.patient_id", "left");
            $this->db->join("ward_db3.t_bed b", "p.patient_id = b.c_patientID", "left");
            $this->db->join("ward_db3.t_room r", "b.c_roomID = r.c_roomID", "left");
            $this->db->group_start()
                ->where("e.visit_status_id", 9)
                ->or_where("e.discharged_status", "death");
            $this->db->group_end();
            $this->db->where("DATE(e.date_last_modified) >=", $date_from);
            $this->db->where("DATE(e.date_last_modified) <=", $date_to);
            $this->db->order_by("e.date_last_modified", "ASC");
            $result['death'] = $this->db->get()->result();
        }

        return $result;
    }



























    // ==========================
    // Admissions
    // ==========================
    public function get_admissions($date_from = null, $date_to = null)
    {
        // Total admissions
        $this->db->from('tbl_patient_encounter');
        $this->db->where('visit_type_id', 2); // 2 = admission
        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(encounter_date) <=', $date_to);
        }
        $total = $this->db->count_all_results();

        // Daily breakdown
        $this->db->select("DATE(encounter_date) as day, COUNT(*) as count");
        $this->db->from('tbl_patient_encounter');
        $this->db->where('visit_type_id', 2);
        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(encounter_date) <=', $date_to);
        }
        $this->db->group_by("DATE(encounter_date)");
        $this->db->order_by("day", "ASC");
        $daily = $this->db->get()->result();

        $days = [];
        $counts = [];
        foreach ($daily as $row) {
            $days[]   = $row->day;
            $counts[] = (int) $row->count;
        }

        $average = count($days) > 0 ? round($total / count($days), 2) : $total;

        return [
            'total_admissions'   => $total,
            'average_admissions' => $average,
            'labels'             => $days,
            'data'               => $counts,
        ];
    }

    // ==========================
    // Discharges
    // ==========================
    public function get_discharges($date_from = null, $date_to = null)
    {
        $this->db->from('tbl_patient_encounter');
        $this->db->where('visit_status_id', 6); // discharged
        if (!empty($date_from)) {
            $this->db->where('DATE(date_encoded) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(date_encoded) <=', $date_to);
        }
        $total = $this->db->count_all_results();

        $this->db->select("DATE(date_encoded) as day, COUNT(*) as count");
        $this->db->from('tbl_patient_encounter');
        $this->db->where('visit_status_id', 6);
        if (!empty($date_from)) {
            $this->db->where('DATE(date_encoded) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(date_encoded) <=', $date_to);
        }
        $this->db->group_by("DATE(date_encoded)");
        $this->db->order_by("day", "ASC");
        $daily = $this->db->get()->result();

        $days = [];
        $counts = [];
        foreach ($daily as $row) {
            $days[]   = $row->day;
            $counts[] = (int) $row->count;
        }

        $average = count($days) > 0 ? round($total / count($days), 2) : $total;

        return [
            'total_discharges'   => $total,
            'average_discharges' => $average,
            'labels'             => $days,
            'data'               => $counts,
        ];
    }


    // ==========================
    // Mortality
    // ==========================
    // public function get_mortality($date_from = null, $date_to = null)
    // {
    //     $this->db->from('tbl_patient_encounter');
    //     $this->db->where('visit_status_id', 9); // expired
    //     if (!empty($date_from)) {
    //         $this->db->where('DATE(date_last_modified) >=', $date_from);
    //     }
    //     if (!empty($date_to)) {
    //         $this->db->where('DATE(date_last_modified) <=', $date_to);
    //     }
    //     $total_deaths = $this->db->count_all_results();

    //     // Daily breakdown
    //     $this->db->select("DATE(date_last_modified) as day, COUNT(*) as count");
    //     $this->db->from('tbl_patient_encounter');
    //     $this->db->where('visit_status_id', 9);
    //     if (!empty($date_from)) {
    //         $this->db->where('DATE(date_last_modified) >=', $date_from);
    //     }
    //     if (!empty($date_to)) {
    //         $this->db->where('DATE(date_last_modified) <=', $date_to);
    //     }
    //     $this->db->group_by("DATE(date_last_modified)");
    //     $this->db->order_by("day", "ASC");
    //     $daily = $this->db->get()->result();

    //     $days = [];
    //     $counts = [];
    //     foreach ($daily as $row) {
    //         $days[]   = $row->day;
    //         $counts[] = (int) $row->count;
    //     }

    //     // Total discharges
    //     $this->db->from('tbl_patient_encounter');
    //     $this->db->where('visit_status_id', 6);
    //     if (!empty($date_from)) {
    //         $this->db->where('DATE(date_last_modified) >=', $date_from);
    //     }
    //     if (!empty($date_to)) {
    //         $this->db->where('DATE(date_last_modified) <=', $date_to);
    //     }
    //     $total_discharges = $this->db->count_all_results();

    //     // Deaths < 48 hours
    //     $this->db->select("COUNT(*) as early_deaths");
    //     $this->db->from('tbl_patient_encounter');
    //     $this->db->where('visit_status_id', 9);
    //     if (!empty($date_from)) {
    //         $this->db->where('DATE(date_last_modified) >=', $date_from);
    //     }
    //     if (!empty($date_to)) {
    //         $this->db->where('DATE(date_last_modified) <=', $date_to);
    //     }
    //     $this->db->where("TIMESTAMPDIFF(HOUR, encounter_date, date_last_modified) <", 48);
    //     $early_deaths = $this->db->get()->row()->early_deaths ?? 0;

    //     $adjusted_discharges = max($total_discharges - $early_deaths, 0);

    //     $gross_rate = $total_discharges > 0 ? round(($total_deaths / $total_discharges) * 100, 2) : 0;
    //     $net_rate   = ($adjusted_discharges > 0)
    //         ? round((($total_deaths - $early_deaths) / $adjusted_discharges) * 100, 2)
    //         : 0;

    //     return [
    //         'total_deaths'     => $total_deaths,
    //         'early_deaths'     => $early_deaths,
    //         'gross_death_rate' => $gross_rate,
    //         'net_death_rate'   => $net_rate,
    //         'labels'           => $days,
    //         'data'             => $counts,
    //     ];
    // }

    public function get_mortality($date_from = null, $date_to = null)
    {
        // Build the where conditions for deaths
        $this->db->from('tbl_patient_encounter');
        $this->db->where('discharged_status', 'death');
        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(encounter_date) <=', $date_to);
        }
        $total_deaths = $this->db->count_all_results();

        // Daily breakdown
        $this->db->select("DATE(encounter_date) as day, COUNT(*) as count");
        $this->db->from('tbl_patient_encounter');
        $this->db->where('discharged_status', 'death');
        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(encounter_date) <=', $date_to);
        }
        $this->db->group_by("DATE(encounter_date)");
        $this->db->order_by("day", "ASC");
        $daily = $this->db->get()->result();

        $days = [];
        $counts = [];
        foreach ($daily as $row) {
            $days[]   = $row->day;
            $counts[] = (int) $row->count;
        }

        // Total discharges (visit_status_id = 6)
        $this->db->from('tbl_patient_encounter');
        $this->db->where('visit_status_id', 6);
        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(encounter_date) <=', $date_to);
        }
        $total_discharges = $this->db->count_all_results();

        // Deaths < 48 hours
        $this->db->select("COUNT(*) as early_deaths");
        $this->db->from('tbl_patient_encounter');
        $this->db->where('discharged_status', 'death');
        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(encounter_date) <=', $date_to);
        }
        $this->db->where("TIMESTAMPDIFF(HOUR, encounter_date, date_last_modified) <", 48);
        $early_deaths = $this->db->get()->row()->early_deaths ?? 0;

        $adjusted_discharges = max($total_discharges - $early_deaths, 0);

        $gross_rate = $total_discharges > 0 ? round(($total_deaths / $total_discharges) * 100, 2) : 0;
        $net_rate   = ($adjusted_discharges > 0)
            ? round((($total_deaths - $early_deaths) / $adjusted_discharges) * 100, 2)
            : 0;

        return [
            'total_deaths'     => $total_deaths,
            'early_deaths'     => $early_deaths,
            'gross_death_rate' => $gross_rate,
            'net_death_rate'   => $net_rate,
            'labels'           => $days,
            'data'             => $counts,
        ];
    }

    // ==========================
    // Patient Totals
    // ==========================
    public function get_patient_total($date_from = null, $date_to = null)
    {
        $cutoff_date = !empty($date_to) ? date("Y-m-d 23:59:59", strtotime($date_to)) : date("Y-m-d 23:59:59");

        $this->db->select("DATE(encounter_date) as day, COUNT(DISTINCT patient_id) as count");
        $this->db->from('tbl_patient_encounter');
        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        $this->db->where('DATE(encounter_date) <=', $cutoff_date);
        $this->db->where_in('visit_status_id', [1, 2, 3, 4, 5]);
        $this->db->group_by("DATE(encounter_date)");
        $this->db->order_by("day", "ASC");
        $daily = $this->db->get()->result();

        $days = [];
        $counts = [];
        $total_patients = 0;

        foreach ($daily as $row) {
            $days[] = $row->day;
            $counts[] = (int) $row->count;
            $total_patients += (int) $row->count;
        }

        $this->db->select("COUNT(DISTINCT patient_id) as total_last_day");
        $this->db->from('tbl_patient_encounter');
        $this->db->where('DATE(encounter_date)', date('Y-m-d', strtotime($cutoff_date)));
        $this->db->where_in('visit_status_id', [1, 2, 3, 4, 5]);
        $total_last_day = $this->db->get()->row()->total_last_day ?? 0;

        $day_count = count($days) > 0 ? count($days) : 1;
        $average_inpatients = round($total_patients / $day_count, 2);

        $this->db->select("AVG(DATEDIFF(date_last_modified, encounter_date)) as avg_length");
        $this->db->from('tbl_patient_encounter');
        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        $this->db->where('DATE(encounter_date) <=', $cutoff_date);
        $this->db->where_in('visit_status_id', [1, 2, 3, 4, 5]);
        $avg_length = $this->db->get()->row()->avg_length ?? 0;

        return [
            'remaining_patients'  => $total_patients,
            'total_last_day'      => $total_last_day,
            'average_inpatients'  => $average_inpatients,
            'avg_hospitalization' => round($avg_length, 2),
            'labels'              => $days,
            'data'                => $counts,
        ];
    }

    // ==========================
    // Beds
    // ==========================
    public function get_total_beds($date_from = null, $date_to = null)
    {
        if (empty($date_from)) $date_from = date('Y-m-01');
        if (empty($date_to)) $date_to = date('Y-m-d');

        // 1. Authorized beds (fixed number)
        $authorized_beds = 237;

        // 2. Actual beds (active beds)
        $this->db->from('ward_db3.t_bed');
        $this->db->where('c_isActive', 1);
        $this->db->where('c_isDeleted', 0);
        $actual_beds = $this->db->count_all_results();

        // 3. Under maintenance beds (snapshot as of date_to)
        $sql_maintenance = "
        SELECT COUNT(DISTINCT b.c_bedID) as count
        FROM ward_db3.t_trans_group tg
        INNER JOIN ward_db3.t_bed b ON tg.c_bedID = b.c_bedID
        WHERE tg.cur_statID = 13
          AND b.c_isActive = 1
          AND b.c_isDeleted = 0
          AND tg.c_createdDate <= '{$date_to} 23:59:59'
          AND tg.c_createdDate = (
              SELECT MAX(tg2.c_createdDate)
              FROM ward_db3.t_trans_group tg2
              WHERE tg2.c_bedID = tg.c_bedID
                AND tg2.c_createdDate <= '{$date_to} 23:59:59'
          )
    ";
        $under_maintenance = $this->db->query($sql_maintenance)->row()->count;

        $operating_beds = $actual_beds - $under_maintenance;

        // 4. Occupied beds (snapshot as of date_from -> date_to)
        $sql_occupied = "
        SELECT COUNT(*) AS total_patients_occupied
        FROM (
            SELECT
                t.trans_group_id,
                t.patient_id,
                t.c_bedID,
                t.c_createdDate,
                ROW_NUMBER() OVER (
                    PARTITION BY COALESCE(NULLIF(t.patient_id, ''), t.patient_name)
                    ORDER BY t.c_createdDate DESC
                ) AS rn
            FROM ward_db3.t_trans_group t
            INNER JOIN ward_db3.t_transaction trx 
                ON trx.trans_group_id = t.trans_group_id
                AND trx.c_transactionID = (
                    SELECT MAX(t3.c_transactionID)
                    FROM ward_db3.t_transaction t3
                    WHERE t3.trans_group_id = t.trans_group_id
                )
            WHERE trx.c_statusID = 5
            AND t.c_createdDate <= '{$date_to} 23:59:59'
            AND t.c_createdDate >= '{$date_from} 00:00:00'
        ) x
        WHERE x.rn = 1
    ";
        $occupied_beds = $this->db->query($sql_occupied)->row()->total_patients_occupied ?? 0;

        // 5. Percentages
        $authorized_percentage = ($authorized_beds > 0)
            ? round(($occupied_beds / $authorized_beds) * 100, 2)
            : 0;

        $actual_percentage = ($operating_beds > 0)
            ? round(($occupied_beds / $operating_beds) * 100, 2)
            : 0;

        return [
            'authorized_beds'       => $authorized_beds,
            'actual_beds'           => $actual_beds,
            'under_maintenance'     => $under_maintenance,
            'operating_beds'        => $operating_beds,
            'occupied_beds'         => $occupied_beds,
            'authorized_percentage' => $authorized_percentage,
            'actual_percentage'     => $actual_percentage,
            'date_from'             => $date_from,
            'date_to'               => $date_to
        ];
    }


    //     public function GetPatientMedicalPayType($date_from, $date_to)
    //     {
    //         // Build query
    //         $this->db->select("
    //     pe.encounter_id,
    //     pe.patient_id,
    //     pl.lastname,
    //     pl.firstname,
    //     pl.middlename,
    //     CONCAT(pl.lastname, ', ', pl.firstname, ' ', pl.middlename) AS full_name,
    //     tg.*
    // ");
    //         $this->db->from('db_emr.tbl_patient_encounter AS pe');
    //         $this->db->join('db_emr.tbl_patient_list AS pl', 'pl.patient_id = pe.patient_id', 'left');

    //         // join condition
    //         $joinCondition = "tg.patient_name = CONCAT(pl.lastname, ', ', pl.firstname, ' ', pl.middlename)
    //     AND (
    //         DATE(tg.c_createdDate) <= " . $this->db->escape($date_to) . "
    //         OR DATE(tg.c_deletedDate) >= " . $this->db->escape($date_from) . "
    //     )";

    //         $this->db->join('ward_db3.t_trans_group AS tg', $joinCondition, 'left');

    //         // ✅ Option 1: Debug only
    //         // echo $this->db->get_compiled_select(); exit;

    //         // ✅ Option 2: Run query
    //         $query = $this->db->get();

    //         if (!$query) {
    //             // See the actual database error
    //             $error = $this->db->error();
    //             print_r($error);
    //             exit;
    //         }

    //         $result = $query->result();
    //         return $result;
    //     }

    // public function GetPatientMedicalPayType($date_from, $date_to)
    // {
    //     $this->db->select("
    //     pl.sex,
    //     t_room.c_roomType,
    //     COUNT(*) AS total
    // ");
    //     $this->db->from('ward_db3.t_trans_group AS tg');
    //     $this->db->join('db_emr.tbl_patient_list AS pl', 'pl.patient_id = tg.patient_id', 'left');
    //     $this->db->join('ward_db3.t_bed AS t_bed', 't_bed.c_bedID = tg.c_bedID', 'left');
    //     $this->db->join('ward_db3.t_room AS t_room', 't_room.c_roomID = t_bed.c_roomID', 'left');
    //     $this->db->where('DATE(tg.c_createdDate) >=', $date_from);
    //     $this->db->where('DATE(tg.c_createdDate) <=', $date_to);
    //     $this->db->group_by(['pl.sex', 't_room.c_roomType']);
    //     $this->db->order_by('pl.sex', 'ASC');

    //     $query = $this->db->get();

    //     if (!$query) {
    //         $error = $this->db->error();
    //         print_r($error);
    //         exit;
    //     }

    //     return $query->result();
    // }

    public function GetPatientMedicalPayType($date_from, $date_to)
    {
        $sql = "
        SELECT
            CASE 
                WHEN p.sex = 1 THEN 'Male'
                WHEN p.sex = 2 THEN 'Female'
                ELSE 'Unknown'
            END AS sex_label,

            SUM(CASE WHEN r.c_roomType = 0 THEN 1 ELSE 0 END) AS Pay,
            SUM(CASE WHEN r.c_roomType IS NULL THEN 1 ELSE 0 END) AS Service
        FROM db_emr.tbl_patient_encounter e
        JOIN db_emr.tbl_patient_list p 
            ON p.patient_id = e.patient_id

        LEFT JOIN ward_db3.t_trans_group tg 
            ON tg.patient_id = p.patient_id

        LEFT JOIN ward_db3.t_room r 
            ON tg.c_roomID = r.c_roomID

        WHERE 
            e.medicine = 1
            AND e.archived = 0
    ";

        $params = [];

        // Apply date range if provided
        if (!empty($date_from) && !empty($date_to)) {
            $sql .= " AND DATE(e.encounter_date) BETWEEN ? AND ?";
            $params[] = $date_from;
            $params[] = $date_to;
        }

        // Group by gender
        $sql .= "
        GROUP BY p.sex
        ORDER BY p.sex
    ";

        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }

    public function GetPatientSurgicalPayType($date_from, $date_to)
    {
        $sql = "
        SELECT
            CASE 
                WHEN p.sex = 1 THEN 'Male'
                WHEN p.sex = 2 THEN 'Female'
                ELSE 'Unknown'
            END AS sex_label,

            SUM(CASE WHEN r.c_roomType = 0 THEN 1 ELSE 0 END) AS Pay,
            SUM(CASE WHEN r.c_roomType IS NULL THEN 1 ELSE 0 END) AS Service
        FROM db_emr.tbl_patient_encounter e
        JOIN db_emr.tbl_patient_list p 
            ON p.patient_id = e.patient_id

        LEFT JOIN ward_db3.t_trans_group tg 
            ON tg.patient_id = p.patient_id

        LEFT JOIN ward_db3.t_room r 
            ON tg.c_roomID = r.c_roomID

        WHERE 
            e.surgical = 1
            AND e.archived = 0
    ";

        $params = [];

        // Apply date range if provided
        if (!empty($date_from) && !empty($date_to)) {
            $sql .= " AND DATE(e.encounter_date) BETWEEN ? AND ?";
            $params[] = $date_from;
            $params[] = $date_to;
        }

        // Group by gender
        $sql .= "
        GROUP BY p.sex
        ORDER BY p.sex
    ";

        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }


    public function GetPatientDischargesMedical($date_from, $date_to)
    {
        $sql = "
        SELECT
            CASE 
                WHEN p.sex = 1 THEN 'Male'
                WHEN p.sex = 2 THEN 'Female'
                ELSE 'Unknown'
            END AS sex_label,
            COUNT(DISTINCT CASE WHEN r.c_roomType = 0 THEN e.patient_id END) AS Pay,
            COUNT(DISTINCT CASE WHEN r.c_roomType = 1 THEN e.patient_id END) AS Service,
            COUNT(DISTINCT e.patient_id) AS Total
        FROM ward_db3.t_transaction t
        JOIN ward_db3.t_bed b ON b.c_bedID = t.c_bedID
        JOIN ward_db3.t_room r ON r.c_roomID = b.c_roomID
        JOIN db_emr.tbl_patient_list p ON p.patient_id = b.c_patientID
        JOIN db_emr.tbl_patient_encounter e ON e.patient_id = p.patient_id
        WHERE t.c_statusID = 1
          AND e.medicine = 1
    ";

        $params = [];

        if (!empty($date_from) && !empty($date_to)) {
            $date_to_full = $date_to . ' 23:59:59';
            $sql .= " AND t.c_createdDate BETWEEN ? AND ?";
            $params = [$date_from, $date_to_full];
        }

        $sql .= "
        GROUP BY p.sex
        ORDER BY p.sex
    ";

        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }

    public function GetPatientDischargesSurgical($date_from, $date_to)
    {
        $sql = "
        SELECT
            CASE 
                WHEN p.sex = 1 THEN 'Male'
                WHEN p.sex = 2 THEN 'Female'
                ELSE 'Unknown'
            END AS sex_label,
            COUNT(DISTINCT CASE WHEN r.c_roomType = 0 THEN e.patient_id END) AS Pay,
            COUNT(DISTINCT CASE WHEN r.c_roomType = 1 THEN e.patient_id END) AS Service,
            COUNT(DISTINCT e.patient_id) AS Total
        FROM ward_db3.t_transaction t
        JOIN ward_db3.t_bed b ON b.c_bedID = t.c_bedID
        JOIN ward_db3.t_room r ON r.c_roomID = b.c_roomID
        JOIN db_emr.tbl_patient_list p ON p.patient_id = b.c_patientID
        JOIN db_emr.tbl_patient_encounter e ON e.patient_id = p.patient_id
        WHERE t.c_statusID = 1
          AND e.surgical = 1
    ";

        $params = [];

        if (!empty($date_from) && !empty($date_to)) {
            $date_to_full = $date_to . ' 23:59:59';
            $sql .= " AND t.c_createdDate BETWEEN ? AND ?";
            $params = [$date_from, $date_to_full];
        }

        $sql .= "
        GROUP BY p.sex
        ORDER BY p.sex
    ";

        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }

    public function get_medical_admissions($date_from = null, $date_to = null)
    {
        $sql = "
        SELECT
            CASE WHEN p.sex = 1 THEN 'Male'
                 WHEN p.sex = 2 THEN 'Female'
                 ELSE 'Unknown'
            END AS sex_label,
            SUM(CASE WHEN r.c_roomType = 0 THEN 1 ELSE 0 END) AS Pay,
            SUM(CASE WHEN r.c_roomType = 1 THEN 1 ELSE 0 END) AS Service
        FROM db_emr.tbl_patient_encounter e
        JOIN db_emr.tbl_patient_list p ON e.patient_id = p.patient_id
        LEFT JOIN ward_db3.t_trans_group tg ON tg.patient_id = e.patient_id
        LEFT JOIN ward_db3.t_room r ON tg.c_roomID = r.c_roomID
        WHERE e.medicine = 1
          AND e.archived = 0
    ";

        $params = [];

        if (!empty($date_from) && !empty($date_to)) {
            $sql .= " AND DATE(e.encounter_date) BETWEEN ? AND ?";
            $params[] = $date_from;
            $params[] = $date_to;
        }

        $sql .= " GROUP BY p.sex ORDER BY p.sex";

        $query = $this->db->query($sql, $params);
        $result = $query->result_array();

        // Format result as ['Male' => [...], 'Female' => [...]]
        $admissions = [
            'Male' => ['Pay' => 0, 'Service' => 0],
            'Female' => ['Pay' => 0, 'Service' => 0],
        ];

        foreach ($result as $row) {
            $sex = $row['sex_label'];
            if (isset($admissions[$sex])) {
                $admissions[$sex]['Pay'] = (int)($row['Pay'] ?? 0);
                $admissions[$sex]['Service'] = (int)($row['Service'] ?? 0);
            }
        }

        return $admissions;
    }

    public function get_surgical_admissions($date_from = null, $date_to = null)
    {
        $sql = "
        SELECT
            CASE WHEN p.sex = 1 THEN 'Male'
                 WHEN p.sex = 2 THEN 'Female'
                 ELSE 'Unknown'
            END AS sex_label,
            SUM(CASE WHEN r.c_roomType = 0 THEN 1 ELSE 0 END) AS Pay,
            SUM(CASE WHEN r.c_roomType = 1 THEN 1 ELSE 0 END) AS Service
        FROM db_emr.tbl_patient_encounter e
        JOIN db_emr.tbl_patient_list p ON e.patient_id = p.patient_id
        LEFT JOIN ward_db3.t_trans_group tg ON tg.patient_id = e.patient_id
        LEFT JOIN ward_db3.t_room r ON tg.c_roomID = r.c_roomID
        WHERE e.surgical = 1
          AND e.archived = 0
    ";

        $params = [];

        if (!empty($date_from) && !empty($date_to)) {
            $sql .= " AND DATE(e.encounter_date) BETWEEN ? AND ?";
            $params[] = $date_from;
            $params[] = $date_to;
        }

        $sql .= " GROUP BY p.sex ORDER BY p.sex";

        $query = $this->db->query($sql, $params);
        $result = $query->result_array();

        // Format result as ['Male' => [...], 'Female' => [...]]
        $admissions = [
            'Male' => ['Pay' => 0, 'Service' => 0],
            'Female' => ['Pay' => 0, 'Service' => 0],
        ];

        foreach ($result as $row) {
            $sex = $row['sex_label'];
            if (isset($admissions[$sex])) {
                $admissions[$sex]['Pay'] = (int)($row['Pay'] ?? 0);
                $admissions[$sex]['Service'] = (int)($row['Service'] ?? 0);
            }
        }

        return $admissions;
    }



    public function get_outcomes($date_from = null, $date_to = null)
    {
        $this->db->select("
        SUM(CASE WHEN discharged_status = 'Improved' THEN 1 ELSE 0 END) AS improved,
        SUM(CASE WHEN discharged_status = 'Recovered' THEN 1 ELSE 0 END) AS recovered,
        SUM(CASE WHEN discharged_status = 'Unimproved' THEN 1 ELSE 0 END) AS unimproved,
        SUM(CASE WHEN discharged_status = 'Abscond' THEN 1 ELSE 0 END) AS abscond,
        SUM(CASE WHEN discharged_status = 'Death' THEN 1 ELSE 0 END) AS death
    ");
        $this->db->from('tbl_patient_encounter');

        if (!empty($date_from)) {
            $this->db->where('DATE(encounter_date) >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('DATE(encounter_date) <=', $date_to);
        }

        return $this->db->get()->row_array();
    }

    public function get_all_statistical_data($date_from = null, $date_to = null)
    {
        $data = [];

        // Beds
        $beds = $this->get_total_beds($date_from, $date_to);
        $data[] = ['report_name' => 'Total Beds (Authorized)', 'value' => $beds['authorized_beds'] ?? 0];
        $data[] = ['report_name' => 'Operating Beds (Actual)', 'value' => $beds['operating_beds'] ?? 0];
        $data[] = ['report_name' => 'Occupied Beds', 'value' => $beds['occupied_beds'] ?? 0];
        $data[] = ['report_name' => 'Authorized Occupancy %', 'value' => $beds['authorized_percentage'] ?? 0];
        $data[] = ['report_name' => 'Actual Occupancy %', 'value' => $beds['actual_percentage'] ?? 0];

        // Admissions
        $admissions = $this->get_admissions($date_from, $date_to);
        $data[] = ['report_name' => 'Total Admissions', 'value' => $admissions['total_admissions'] ?? 0];
        $data[] = ['report_name' => 'Average Admissions per Day', 'value' => $admissions['average_admissions'] ?? 0];

        // Discharges
        $discharges = $this->get_discharges($date_from, $date_to);
        $data[] = ['report_name' => 'Total Discharges', 'value' => $discharges['total_discharges'] ?? 0];
        $data[] = ['report_name' => 'Average Discharges per Day', 'value' => $discharges['average_discharges'] ?? 0];

        // Mortality
        $mortality = $this->get_mortality($date_from, $date_to);
        $data[] = ['report_name' => 'Total Deaths', 'value' => $mortality['total_deaths'] ?? 0];
        $data[] = ['report_name' => 'Gross Death Rate (%)', 'value' => $mortality['gross_death_rate'] ?? 0];
        $data[] = ['report_name' => 'Net Death Rate (%)', 'value' => $mortality['net_death_rate'] ?? 0];

        // Patients
        $patients = $this->get_patient_total($date_from, $date_to);
        $data[] = ['report_name' => 'Remaining Patients', 'value' => $patients['remaining_patients'] ?? 0];
        $data[] = ['report_name' => 'Average In-Patients per Day', 'value' => $patients['average_inpatients'] ?? 0];
        $data[] = ['report_name' => 'Average Length of Stay (days)', 'value' => $patients['avg_hospitalization'] ?? 0];

        return $data;
    }

    public function get_top_discharges($date_from = null, $date_to = null)
    {
        $whereDate = "";
        $params = [];

        if (!empty($date_from) && !empty($date_to)) {
            // Adjust date_to by adding 1 day so it includes the entire last day
            $whereDate = " AND e.encounter_date >= ? AND e.encounter_date < ? ";
            $params[] = $date_from;
            // Add 1 day to date_to
            $params[] = date('Y-m-d', strtotime($date_to . ' +1 day'));
        }

        $sql = "
        SELECT 
            ROW_NUMBER() OVER (ORDER BY COUNT(*) DESC) AS rank_no,
            o.final_diagnosis AS icd10_code,
            i.icd10_title AS icd10_title,
            SUM(CASE WHEN p.sex = 1 THEN 1 ELSE 0 END) AS male_count,
            SUM(CASE WHEN p.sex = 2 THEN 1 ELSE 0 END) AS female_count,
            COUNT(*) AS total_count
        FROM tbl_out_patient_clinical_abstract AS o
        JOIN tbl_patient_encounter AS e 
            ON e.patient_id = o.patient_id
            AND e.encounter_id = o.encounter_id
        JOIN tbl_patient_list AS p 
            ON p.patient_id = o.patient_id
        LEFT JOIN lib_icd10 AS i 
            ON i.icd10_code = o.final_diagnosis
        WHERE e.visit_status_id = 6
          AND TRIM(o.final_diagnosis) <> ''
          $whereDate
        GROUP BY o.final_diagnosis, i.icd10_title
        ORDER BY total_count DESC

    ";

        if (!empty($params)) {
            return $this->db->query($sql, $params)->result_array();
        } else {
            return $this->db->query($sql)->result_array();
        }
    }

    public function get_top_death($date_from = null, $date_to = null)
    {
        $whereDate = "";
        $params = [];

        if (!empty($date_from) && !empty($date_to)) {
            // Adjust date_to by adding 1 day so it includes the entire last day
            $whereDate = " AND e.encounter_date >= ? AND e.encounter_date < ? ";
            $params[] = $date_from;
            // Add 1 day to date_to
            $params[] = date('Y-m-d', strtotime($date_to . ' +1 day'));
        }

        $sql = "
        SELECT 
            ROW_NUMBER() OVER (ORDER BY COUNT(*) DESC) AS rank_no,
            o.final_diagnosis AS icd10_code,
            i.icd10_title AS icd10_title,
            SUM(CASE WHEN p.sex = 1 THEN 1 ELSE 0 END) AS male_count,
            SUM(CASE WHEN p.sex = 2 THEN 1 ELSE 0 END) AS female_count,
            COUNT(*) AS total_count
        FROM tbl_out_patient_clinical_abstract AS o
        JOIN tbl_patient_encounter AS e 
            ON e.patient_id = o.patient_id
            AND e.encounter_id = o.encounter_id
        JOIN tbl_patient_list AS p 
            ON p.patient_id = o.patient_id
        LEFT JOIN lib_icd10 AS i 
            ON i.icd10_code = o.final_diagnosis
        WHERE e.visit_status_id = 6
            AND e.discharged_status = 'death'
          AND TRIM(o.final_diagnosis) <> ''
          $whereDate
        GROUP BY o.final_diagnosis, i.icd10_title
        ORDER BY total_count DESC
    ";

        if (!empty($params)) {
            return $this->db->query($sql, $params)->result_array();
        } else {
            return $this->db->query($sql)->result_array();
        }
    }

    public function get_days_care($date_from, $date_to)
    {
        $sql = "
        SELECT 
            CASE 
                WHEN r.c_roomType = 0 THEN 'Pay'
                WHEN r.c_roomType = 1 THEN 'Service'
                ELSE 'Unknown'
            END AS room_type,
            SUM(GREATEST(DATEDIFF(e.discharge_date, e.encounter_date), 0)) AS total_days
        FROM ward_db3.t_transaction t
        JOIN ward_db3.t_bed b ON b.c_bedID = t.c_bedID
        JOIN ward_db3.t_room r ON r.c_roomID = b.c_roomID
        JOIN db_emr.tbl_patient_list p ON p.patient_id = b.c_patientID
        JOIN db_emr.tbl_patient_encounter e ON e.patient_id = p.patient_id
        WHERE t.c_statusID = 1
          AND e.discharge_date IS NOT NULL
    ";

        $params = [];

        if (!empty($date_from) && !empty($date_to)) {
            $date_to_full = $date_to . ' 23:59:59';
            $sql .= " AND t.c_createdDate BETWEEN ? AND ?";
            $params = [$date_from, $date_to_full];
        }

        $sql .= "
        GROUP BY r.c_roomType
        ORDER BY r.c_roomType
    ";

        $query = $this->db->query($sql, $params);

        if (!$query) {
            log_message('error', 'Error in get_days_care query: ' . $this->db->last_query());
            return [];
        }

        $rows = $query->result_array();

        // Default structure
        $result = [
            'Pay' => 0,
            'Service' => 0,
        ];

        foreach ($rows as $r) {
            $type = $r['room_type'];
            $result[$type] = (int)$r['total_days'];
        }

        // Optional: add grand total
        $result['GrandTotal'] = ($result['Pay'] ?? 0) + ($result['Service'] ?? 0);

        return $result;
    }
}
