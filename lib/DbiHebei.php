<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiHebei extends BaseDbi
{
    private static $instance;
    
    protected function __construct()
    {
        $this->init();
    }
    
    public static function getDbi()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getYizhongDoctor($hebeiDoctorId)
    {
        $sql = "select yizhong_user_id as doctor_id, doctor_name from hebei_doctor where doctor_id = '$hebeiDoctorId'";
        return $this->getDataRow($sql);
    }
    public function getHosDepTree($yizhongHospital)
    {
        $sql = "select h.hospital_id, h.hospital_name, d.department_id, d.department_name
                from hebei_department as d inner join hebei_hospital as h on d.hospital_id = h.hospital_id
                where d.yizhong_hos_id = '$yizhongHospital' limit 1";
        return $this->getDataRow($sql);
    }
    public function getHosDepDocTree($yizhongHospital)
    {
        $sql = "select h.hospital_id as invited_hospital_id, h.hospital_name as invited_hospital_name, 
                d.department_id as invited_section_id, d.department_name as invited_section_name, 
                u.doctor_id as invited_doctor_id, u.doctor_name as invited_doctor_name
                from hebei_department as d inner join hebei_hospital as h on d.hospital_id = h.hospital_id
                inner join hebei_doctor as u on d.department_id = u.department_id
                where d.yizhong_hos_id = '$yizhongHospital'";
        return $this->getDataAll($sql);
    }
    public function getHosDepDocTreeByDoctor($doctorId)
    {
        $sql = "select h.hospital_id as invited_hospital_id, h.hospital_name as invited_hospital_name,
                d.department_id as invited_section_id, d.department_name as invited_section_name,
                u.doctor_id as invited_doctor_id, u.doctor_name as invited_doctor_name
                from hebei_department as d inner join hebei_hospital as h on d.hospital_id = h.hospital_id
                inner join hebei_doctor as u on d.department_id = u.department_id
                where u.yizhong_user_id = '$doctorId'";
        return $this->getDataAll($sql);
    }
    public function getPatientInfo($guardianId)
    {
        $sql = "select g.guardian_id, g.regist_hospital_id, d.moved_hospital, 
                g.start_time, p.patient_name, p.sex, p.birth_year, p.tel, 
                a.doctor_id, a.doctor_name, a.id_card, a.mi_card, a.disease_id, a.disease_name, a.inspect_findings
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                left join hebei_add as a on g.guardian_id = a.guardian_id
                where g.guardian_id = '$guardianId' limit 1";
        return $this->getDataRow($sql);
    }
}
