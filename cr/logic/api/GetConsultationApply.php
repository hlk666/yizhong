<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetConsultationApply extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['hospital_id']) ? HpValidate::checkRequired($this->param['hospital_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'hospital_id.');
        }
        
        if (false === is_numeric($this->param['hospital_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'hospital_id.');
        }
        
        if (isset($this->param['apply_hospital_id']) && false === is_numeric($this->param['apply_hospital_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'apply_hospital_id.');
        }
        
        if (isset($this->param['start_time']) && false === HpValidate::checkTime($this->param['start_time'])) {
            return HpErrorMessage::getError(ERROR_PARAM_TIME);
        }
        
        if (isset($this->param['end_time']) && false === HpValidate::checkTime($this->param['end_time'])) {
            return HpErrorMessage::getError(ERROR_PARAM_TIME);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $applyHospital = isset($this->param['apply_hospital_id']) ? $this->param['apply_hospital_id'] : null;
        $startTime = isset($this->param['start_time']) ? $this->param['start_time'] : null;
        $endTime = isset($this->param['end_time']) ? $this->param['end_time'] : null;
        
        $consultations = Dbi::getDbi()->getConsultationApply($this->param['hospital_id'], $applyHospital, $startTime, $endTime);
        if (VALUE_DB_ERROR === $consultations) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($consultations)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $this->retSuccess['consultations'] = $consultations;
        return $this->retSuccess;
    }
}
