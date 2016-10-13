<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetConsultationInfo extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['consultation_id']) ? HpValidate::checkRequired($this->param['consultation_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'consultation_id.');
        }
        
        if (false === is_numeric($this->param['consultation_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'consultation_id.');
        }
        
        if (false === Dbi::getDbi()->existedConsultation($this->param['consultation_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'consultation_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $consultation = Dbi::getDbi()->getConsultationInfo($this->param['consultation_id']);
        if (VALUE_DB_ERROR === $consultation) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($consultation)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        return array_merge($this->retSuccess, $consultation);
    }
}
