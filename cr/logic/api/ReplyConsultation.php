<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class ReplyConsultation extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['case_id']) ? HpValidate::checkRequired($this->param['consultation_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'consultation_id.');
        }
        
        
        $ret = isset($this->param['apply_user']) ? HpValidate::checkRequired($this->param['reply_user']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'reply_user.');
        }
        
        $ret = isset($this->param['apply_message']) ? HpValidate::checkRequired($this->param['reply_message']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'reply_message');
        }
        
        if (false === is_numeric($this->param['reply_user'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'reply_user.');
        }
        
        if ('' == $this->param['reply_message']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'reply_message.');
        }
        
        if (false === Dbi::getDbi()->existedConsultation($this->param['consultation_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'consultation_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->replyConsultation($this->param['consultation_id'], 
                $this->param['reply_user'], $this->param['reply_message']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
