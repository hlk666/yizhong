<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddOutpatientRecord extends BaseApi
{
    private $examinationList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['department_id', 'patient_id', 'diagnosis', 'doctor_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['department_id', 'patient_id', 'doctor_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        if (false === Dbi::getDbi()->isPatientInDepartment($this->param['patient_id'], $this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_NOT_IN_DEPARTMENT);
        }
        
        if (isset($this->param['examination'])) {
            $this->examinationList = $this->getStructalData($this->param['examination']);
            foreach ($this->examinationList as $exam) {
                if (false === Dbi::getDbi()->existedExamination($exam[0])) {
                    return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'examination_id.');
                }
            }
        }
        
        return true;
    }
    
    protected function execute()
    {
        $chiefComplaint = isset($this->param['chief_complaint']) ? $this->param['chief_complaint'] : '';
        $description = isset($this->param['descption']) ? $this->param['descption'] : '';
        $medicineHistory = isset($this->param['medicine_history']) ? $this->param['medicine_history'] : '';
        $medicineAdvice = isset($this->param['medicine_advice']) ? $this->param['medicine_advice'] : '';
        $examination = isset($this->param['examination']) ? $this->param['examination'] : '';
        
        $outpatientId = Dbi::getDbi()->addOutpatient($this->param['department_id'], $this->param['patient_id'], 
                $chiefComplaint, $descption, $medicineHistory, $medicineAdvice, $examination, $this->examinationList, 
                $this->param['diagnosis'], $this->param['doctor_id']);
        if (VALUE_DB_ERROR === $outpatientId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['outpatient_id'] = $outpatientId;
        return $this->retSuccess;
    }
}
