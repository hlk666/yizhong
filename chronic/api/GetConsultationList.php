<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetConsultationList extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        if (!isset($this->param['department_id']) && !isset($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'department_id | patient_id.');
        }
        
        if (isset($this->param['department_id'])) {
            if (false === HpValidate::checkRequired($this->param['department_id'])) {
                return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'department_id.');
            }
            $checkNumeric = HpValidate::checkNumeric(['department_id'], $this->param);
            if (true !== $checkNumeric) {
                return $checkNumeric;
            }
        }
        
        if (isset($this->param['patient_id'])) {
            if (false === HpValidate::checkRequired($this->param['patient_id'])) {
                return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'patient_id.');
            }
            $checkNumeric = HpValidate::checkNumeric(['patient_id'], $this->param);
            if (true !== $checkNumeric) {
                return $checkNumeric;
            }
        }
        
        return true;
    }
    
    protected function execute()
    {
        $departmentId = isset($this->param['department_id']) ? $this->param['department_id'] : null;
        $patientId = isset($this->param['patient_id']) ? $this->param['patient_id'] : null;
        //$startTime = isset($this->param['start_time']) ? $this->param['start_time'] : null;
        //$endTime = isset($this->param['end_time']) ? $this->param['end_time'] : null;
        
        $consultationApply = Dbi::getDbi()->getConsultationList($departmentId, $patientId, 'apply');
        if (VALUE_DB_ERROR === $consultationApply) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($departmentId)) {
            $consultationReply = array();
        } else {
            $consultationReply = Dbi::getDbi()->getConsultationList($departmentId, $patientId, 'reply');
            if (VALUE_DB_ERROR === $consultationReply) {
                return HpErrorMessage::getError(ERROR_DB);
            }
        }

        $this->retSuccess['consultation_apply'] = $consultationApply;
        $this->retSuccess['consultation_reply'] = $consultationReply;
        return $this->retSuccess;
    }
}
