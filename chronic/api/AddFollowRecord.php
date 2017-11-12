<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddFollowRecord extends BaseApi
{
    private $examinationList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['department_id', 'patient_id', 'follow_plan_id', 'record_text', 'diagnosis', 'doctor_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['department_id', 'patient_id', 'follow_plan_id', 'doctor_id'], $this->param);
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
        if (false === Dbi::getDbi()->existedFollowPlan($this->param['follow_plan_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_EXISTED, 'follow_plan_id.');
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
        $followRecordId = Dbi::getDbi()->addFollowRecord($this->param['department_id'], $this->param['patient_id'], 
                $this->param['follow_plan_id'], $this->param['record_text'], $this->param['examination'], $this->examinationList, 
                $this->param['diagnosis'], $this->param['doctor_id']);
        if (VALUE_DB_ERROR === $followRecordId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['follow_record_id'] = $followRecordId;
        return $this->retSuccess;
    }
}
