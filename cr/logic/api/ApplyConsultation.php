<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class ApplyConsultation extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['case_id']) ? HpValidate::checkRequired($this->param['case_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'case_id.');
        }
        
        $ret = isset($this->param['apply_hospital']) ? HpValidate::checkRequired($this->param['apply_hospital']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'apply_hospital.');
        }
        
        $ret = isset($this->param['apply_user']) ? HpValidate::checkRequired($this->param['apply_user']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'apply_user.');
        }
        
        $ret = isset($this->param['apply_message']) ? HpValidate::checkRequired($this->param['apply_message']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'apply_message');
        }
        
        $ret = isset($this->param['reply_hospital']) ? HpValidate::checkRequired($this->param['reply_hospital']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'reply_hospital');
        }
        
        if (false === is_numeric($this->param['case_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'case_id.');
        }
        if (false === is_numeric($this->param['apply_hospital'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'apply_hospital.');
        }
        if (false === is_numeric($this->param['apply_user'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'apply_user.');
        }
        if (false === is_numeric($this->param['reply_hospital'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'reply_hospital.');
        }
        
        if ('' == $this->param['apply_message']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'apply_message.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->applyConsultation($this->param['case_id'], $this->param['apply_hospital'], 
                $this->param['apply_user'], $this->param['apply_message'], $this->param['reply_hospital']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
