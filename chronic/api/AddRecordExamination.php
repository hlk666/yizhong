<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddRecordExamination extends BaseApi
{
    private $examinationList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['patient_id', 'examination_id', 'value', 'result'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['patient_id', 'examination_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if (false === Dbi::getDbi()->existedExamination($this->param['examination_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'examination_id.');
        }
        
        if (isset($this->param['follow_record_id']) 
                && false === Dbi::getDbi()->existedFollowRecord($this->param['follow_record_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'follow_record_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        if (isset($this->param['follow_record_id'])) {
            $type = 'follow';
            $recordId = $this->param['follow_record_id'];
        } elseif (isset($this->param['outpatient_id'])) {
            $type = 'outpatient';
            $recordId = $this->param['outpatient_id'];
        } else {
            $type = 'app';
            $recordId = null;
        }
        $ret = Dbi::getDbi()->addRecordExamination($this->param['patient_id'],
                $this->param['examination_id'], $this->param['value'], $this->param['result'], $type, $recordId);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        return $this->retSuccess;
    }
}
