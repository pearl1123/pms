<?php

    class iHOMIS_Model extends CI_Model {

        private $ihomis_db;

        public function __construct()
        {
            parent::__construct();
            $this->ihomis_db = $this->load->database('dbo_test', TRUE);
        }

        // GET PATIENT LIST
        //============================================================================= //
        public function getPatientList($start, $length)
        {
            $this->ihomis_db->select("t1.hpatcode");
            $num = $this->ihomis_db->get('hperson t1')->num_rows();

            $this->ihomis_db->select("t1.hpatcode, t1.patlast, t1.patfirst, t1.patmiddle, t1.patsuffix, t1.patsex, t1.patbdate, t2.pattel");
            if ($length > 0) {
                $this->ihomis_db->limit($length, $start);
            }
            $this->ihomis_db->join('htelep t2', 't1.hpercode = t2.hpercode', 'left');
            $this->ihomis_db->order_by('hpatcode', 'DESC');
            $res = $this->ihomis_db->get('hperson t1')->result();

            return array($res, $num);
        }

        // GET PATIENT DETAILS
        //============================================================================= //
        public function getPatientDetails($id){
            $this->ihomis_db->select("t1.hpatcode, t1.patlast, t1.patfirst, t1.patmiddle, t1.patsuffix, t1.patsex, t1.patbdate, t2.pattel,
                                   t3.patstr, t4.provname, t5.ctyname, t6.bgyname, t7.regname");
            $this->ihomis_db->join('htelep t2', 't1.hpercode = t2.hpercode', 'left');
            $this->ihomis_db->join('haddr t3', 't1.hpercode = t3.hpercode', 'left');
            $this->ihomis_db->join('hprov t4', 't3.provcode = t4.provcode', 'left');
            $this->ihomis_db->join('hcity t5', 't3.ctycode = t5.ctycode', 'left');
            $this->ihomis_db->join('hregion t7', 't4.provreg = t7.regcode', 'left');
            $this->ihomis_db->join('hbrgy t6', 't3.brg = t6.bgycode', 'left');
            $this->ihomis_db->where('t1.hpatcode', $id);
            $this->ihomis_db->order_by('t1.hpatcode', 'DESC');
            $row = $this->ihomis_db->get('hperson t1')->row();

            return $row;
        }

        // GET PATIENT LATEST ENCOUNTER
        //============================================================================= //
        public function getLatestEncounter($id){

            $this->ihomis_db->select("henctr.enccode, hperson.hpercode,
                                    GROUP_CONCAT(DISTINCT CASE WHEN hencdiag.tdcode = 'FINDX' THEN hencdiag.diagtext END SEPARATOR '; ') AS final_diagnosis,
                                    vw_admlog.admdate, vw_admlog.disdate,
                                    henctr.toecode,
                                    hadmlog.dispcode, hadmlog.condcode, hadmlog.admnotes,
                                    hpersonal.lastname, hpersonal.firstname, hpersonal.middlename");

            $this->ihomis_db->join('hperson', 'henctr.hpercode = hperson.hpercode', 'left');
            $this->ihomis_db->join('vw_admlog', 'henctr.enccode = vw_admlog.enccode', 'left');
            $this->ihomis_db->join('hadmlog', 'vw_admlog.enccode = hadmlog.enccode', 'left');
            $this->ihomis_db->join('hencdiag', 'henctr.enccode = hencdiag.enccode', 'left');
            $this->ihomis_db->join('hadmcons', 'henctr.hpercode = hadmcons.hpercode', 'left');
            $this->ihomis_db->join('hprovider', 'hadmcons.licno = hprovider.licno', 'left');
            $this->ihomis_db->join('hpersonal', 'hprovider.employeeid = hpersonal.employeeid', 'left');
            $this->ihomis_db->where('hperson.hpercode', $id);
            $this->ihomis_db->where('hadmcons.doctype', 'ATTEN');
            $this->ihomis_db->group_by('henctr.enccode');
            $this->ihomis_db->order_by('vw_admlog.admdate', 'DESC');
            $row = $this->ihomis_db->get('henctr')->row();

            return $row;
        }

        // GET ALL PATIENT ENCOUNTERS
        //============================================================================= //
        public function getAllEncounters($start, $length, $id){
            $this->ihomis_db->select("enccode");
            $this->ihomis_db->join('hperson', 'henctr.hpercode = hperson.hpercode', 'left');
            $this->ihomis_db->where('hperson.hpercode', $id);
            $num = $this->ihomis_db->get('henctr')->num_rows();

            $this->ihomis_db->select("henctr.enccode, hperson.hpercode,
                                      GROUP_CONCAT(DISTINCT CASE WHEN hencdiag.tdcode = 'FINDX' THEN hencdiag.diagtext END SEPARATOR '; ') AS final_diagnosis,
                                      vw_admlog.admdate, vw_admlog.disdate,
                                      henctr.toecode,
                                      hadmlog.dispcode, hadmlog.condcode, hadmlog.admnotes,
                                      hpersonal.lastname, hpersonal.firstname, hpersonal.middlename");
            if ($length > 0) {
                $this->ihomis_db->limit($length, $start);
            }
            $this->ihomis_db->join('hperson', 'henctr.hpercode = hperson.hpercode', 'left');
            $this->ihomis_db->join('vw_admlog', 'henctr.enccode = vw_admlog.enccode', 'left');
            $this->ihomis_db->join('hadmlog', 'vw_admlog.enccode = hadmlog.enccode', 'left');
            $this->ihomis_db->join('hencdiag', 'henctr.enccode = hencdiag.enccode', 'left');
            $this->ihomis_db->join('hadmcons', 'henctr.hpercode = hadmcons.hpercode', 'left');
            $this->ihomis_db->join('hprovider', 'hadmcons.licno = hprovider.licno', 'left');
            $this->ihomis_db->join('hpersonal', 'hprovider.employeeid = hpersonal.employeeid', 'left');
            $this->ihomis_db->where('hperson.hpercode', $id);
            $this->ihomis_db->where('hadmcons.doctype', 'ATTEN');
            $this->ihomis_db->group_by('henctr.enccode');
            $this->ihomis_db->order_by('vw_admlog.admdate', 'DESC');
            $res = $this->ihomis_db->get('henctr')->result();

            return array($res, $num);
        }
    }


?>