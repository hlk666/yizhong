<?php
require_once PATH_ROOT . 'logic/BaseLogic.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class ApplyConsultation extends BaseLogic
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = HpValidate::checkRequired($this->param['case_id']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'case_id.');
        }
        
        $ret = HpValidate::checkRequired($this->param['apply_hospital']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'apply_hospital.');
        }
        
        $ret = HpValidate::checkRequired($this->param['apply_user']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'apply_user.');
        }
        
        $ret = HpValidate::checkRequired($this->param['apply_message']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'apply_message');
        }
        
        $ret = HpValidate::checkRequired($this->param['reply_hospital']);
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
