<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BedsModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_assigned_beds($patient_id)
    {
        $this->db->select('b.c_bedID, b.c_bedName, b.c_doh, r.c_roomName, w.c_wardName');
        $this->db->from('ward_db3.t_bed b');
        $this->db->join('ward_db3.t_room r', 'r.c_roomID = b.c_roomID', 'left');
        $this->db->join('ward_db3.t_ward w', 'w.c_wardID = r.c_wardID', 'left');
        $this->db->where('b.c_patientID', $patient_id);
        $this->db->where('b.c_isActive', 1);
        $this->db->order_by('b.c_createdDate', 'DESC');

        $query = $this->db->get();

        if (!$query) {
            $error = $this->db->error();
            log_message('error', 'DB Error: ' . print_r($error, true));
            log_message('error', 'Last Query: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }

    // Get all active wards
    public function get_all_wards()
    {
        return $this->db
            ->where('c_isActive', 1)
            ->where('c_isDeleted', 0)
            ->order_by('c_wardName', 'ASC')
            ->get('ward_db3.t_ward')
            ->result();
    }

    // Get rooms by ward ID
    public function get_rooms_by_ward($ward_id)
    {
        return $this->db
            ->where('c_wardID', $ward_id)
            ->where('c_isActive', 1)
            ->where('c_isDeleted', 0)
            ->order_by('c_roomName', 'ASC')
            ->get('ward_db3.t_room')
            ->result();
    }

    // Get beds by room ID
    public function get_beds_by_room($room_id)
    {
        return $this->db
            ->where('c_roomID', $room_id)
            ->where('c_isActive', 1)
            ->where('c_isDeleted', 0)
            ->where('c_patientID', 0) // only unassigned beds
            ->order_by('c_bedName', 'ASC')
            ->get('ward_db3.t_bed')
            ->result();
    }

    // LIST VIEW
    // public function get_all_assigned_beds()
    // {
    //     $this->db->select('
    //     t.*,
    //     w.c_wardName as ward_name,
    //     r.c_roomName as room_name,
    //     b.c_bedName as bed_name,
    //     p.firstname, p.middlename, p.lastname,
    //     u.fullname as created_by_name
    // ');
    //     $this->db->from('ward_db3.t_trans_group t');
    //     $this->db->join('ward_db3.t_ward w', 'w.c_wardID = t.c_wardID', 'left');
    //     $this->db->join('ward_db3.t_room r', 'r.c_roomID = t.c_roomID', 'left');
    //     $this->db->join('ward_db3.t_bed b', 'b.c_bedID = t.c_bedID', 'left');
    //     $this->db->join('db_emr.tbl_patient_list p', 'p.patient_id = t.patient_id', 'left');
    //     $this->db->join('db_emr.aauth_users u', 'u.id = t.c_createdBy', 'left'); // join with users
    //     $this->db->order_by('t.c_createdDate', 'DESC');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    // public function get_all_assigned_beds()
    // {
    //     $this->db->select('
    //     t.trans_group_id,
    //     t.patient_id,
    //     CASE 
    //         WHEN t.patient_id IS NOT NULL AND t.patient_id != "" 
    //             THEN CONCAT_WS(" ", p.lastname, p.firstname, p.middlename)
    //         ELSE t.patient_name
    //     END as patient_name,
    //     t.c_wardID,
    //     t.c_roomID,
    //     t.c_bedID,
    //     w.c_wardName as ward_name,
    //     r.c_roomName as room_name,
    //     b.c_bedName as bed_name,
    //     u.fullname as created_by_name,
    //     t.c_createdDate
    // ');
    //     $this->db->from('ward_db3.t_trans_group t');
    //     $this->db->join('ward_db3.t_ward w', 'w.c_wardID = t.c_wardID', 'left');
    //     $this->db->join('ward_db3.t_room r', 'r.c_roomID = t.c_roomID', 'left');
    //     $this->db->join('ward_db3.t_bed b', 'b.c_bedID = t.c_bedID', 'left');
    //     $this->db->join('db_emr.tbl_patient_list p', 'p.patient_id = t.patient_id', 'left');
    //     $this->db->join('db_emr.aauth_users u', 'u.id = t.c_createdBy', 'left');

    //     // keep only latest record per patient OR per patient_name if no ID
    //     $this->db->where('t.c_createdDate = (
    //     SELECT MAX(t2.c_createdDate)
    //     FROM ward_db3.t_trans_group t2
    //     WHERE 
    //         (t.patient_id IS NOT NULL AND t2.patient_id = t.patient_id)
    //         OR (t.patient_id IS NULL AND t2.patient_id IS NULL AND t2.patient_name = t.patient_name)
    // )');

    //     $this->db->order_by('t.c_createdDate', 'DESC');
    //     $query = $this->db->get();

    //     return $query->result();
    // }

    public function get_all_available_beds()
    {
        $sql = "
         SELECT 
        b.c_bedID,
        b.c_bedName AS bed_name,
        r.c_roomName AS room_name,
        w.c_wardName AS ward_name,
        b.c_patientID,
        b.c_doh,
        b.c_isActive,
        b.c_isDeleted
    FROM ward_db3.t_bed AS b
    INNER JOIN ward_db3.t_room AS r ON b.c_roomID = r.c_roomID
    INNER JOIN ward_db3.t_ward AS w ON r.c_wardID = w.c_wardID
    WHERE b.c_isActive = 1
      AND b.c_isDeleted = 0
      AND b.c_bedID NOT IN (
          SELECT DISTINCT t.c_bedID
          FROM ward_db3.t_transaction AS t
          INNER JOIN ward_db3.t_trans_group AS tg
              ON t.trans_group_id = tg.trans_group_id
          WHERE t.c_statusID != 4 OR tg.cur_statID != 4
      )
    ORDER BY w.c_wardName, r.c_roomName, b.c_bedName
    ";

        $query = $this->db->query($sql);

        if (!$query) {
            log_message('error', 'Database error in get_all_available_beds: ' . $this->db->error()['message']);
            return [];
        }

        return $query->result();
    }




    // public function get_all_assigned_beds()
    // {
    //     $sql = "
    //     SELECT *
    //     FROM (
    //         SELECT 
    //             t.trans_group_id,
    //             t.patient_id,
    //             CASE 
    //                 WHEN t.patient_id IS NOT NULL AND t.patient_id != '' 
    //                     THEN CONCAT_WS(' ', p.lastname, p.firstname, p.middlename)
    //                 ELSE t.patient_name
    //             END AS patient_name,
    //             t.c_wardID,
    //             t.c_roomID,
    //             t.c_bedID,
    //             w.c_wardName AS ward_name,
    //             r.c_roomName AS room_name,
    //             b.c_bedName AS bed_name,
    //             u.fullname AS created_by_name,
    //             t.c_createdDate,
    //             ROW_NUMBER() OVER (
    //                 PARTITION BY COALESCE(NULLIF(t.patient_id,''), t.patient_name)
    //                 ORDER BY t.c_createdDate DESC
    //             ) AS rn
    //         FROM ward_db3.t_trans_group t
    //         LEFT JOIN ward_db3.t_ward w 
    //             ON w.c_wardID = t.c_wardID
    //         LEFT JOIN ward_db3.t_room r 
    //             ON r.c_roomID = t.c_roomID
    //         LEFT JOIN ward_db3.t_bed b 
    //             ON b.c_bedID = t.c_bedID
    //         LEFT JOIN db_emr.tbl_patient_list p 
    //             ON p.patient_id = t.patient_id
    //         LEFT JOIN db_emr.aauth_users u 
    //             ON u.id = t.c_createdBy
    //         INNER JOIN ward_db3.t_transaction trx 
    //             ON trx.trans_group_id = t.trans_group_id
    //            AND trx.c_transactionID = (
    //                 SELECT MAX(t3.c_transactionID)
    //                 FROM ward_db3.t_transaction t3
    //                 WHERE t3.trans_group_id = t.trans_group_id
    //            )
    //         WHERE trx.c_statusID = 5
    //           AND t.c_createdDate = (
    //                 SELECT MAX(t2.c_createdDate)
    //                 FROM ward_db3.t_trans_group t2
    //                 WHERE t2.c_bedID = t.c_bedID
    //             )
    //     ) x
    //     WHERE x.rn = 1
    //     ORDER BY x.c_createdDate DESC
    // ";

    //     $query = $this->db->query($sql);
    //     return $query->result();
    // }
}
