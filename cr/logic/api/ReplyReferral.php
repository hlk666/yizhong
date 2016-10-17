<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class ReplyReferral extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $params = [
                        'referral_id' => $this->param['referral_id'],
                        'reply_user' => $this->param['reply_user'],
                        'judge' => $this->param['judge'],
                        'message' => $this->param['message'],
        ];
        $checkRequired = HpValidate::checkRequiredArray($params);
        if (true !== $required) {
            return $checkRequired;
        }
        
        if (false === is_numeric($this->param['reply_user'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'reply_user.');
        }
        
        if ($this->param['judge'] != REFERRAL_OK && $this->param['judge'] != REFERRAL_DENY) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'judge.');
        }
        
        if ('' == $this->param['message']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'message.');
        }
        
        if (false === Dbi::getDbi()->existedReferral($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'referral_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->replyReferral($this->param['referral_id'], 
                $this->param['reply_user'], $this->param['message'], $this->param['judge']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
