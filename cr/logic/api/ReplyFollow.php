<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class ReplyFollow extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $checkRequired = HpValidate::checkRequiredParam(['follow_id', 'reply_user', 'reply_advice'], $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['follow_id', 'reply_user'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedFollow($this->param['follow_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'follow_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $replyQuestion = isset($this->param['reply_question']) ? $this->param['reply_question'] : '';
        $ret = Dbi::getDbi()->replyFollow($this->param['follow_id'], 
                $this->param['reply_user'], $this->param['reply_advice'], $replyQuestion);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
