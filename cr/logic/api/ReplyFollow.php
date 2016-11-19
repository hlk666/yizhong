<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

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
        $followInfo = Dbi::getDbi()->getFollowInfo($this->param['follow_id']);
        if (VALUE_DB_ERROR === $followInfo) {
            return HpErrorMessage::getError(ERROR_DB);
        } else {
            $contentDoctor = HpErrorMessage::getTelMessageFollowReplyDoctor($followInfo['reply_hospital_name'],
                    $followInfo['reply_doctor_name'], $followInfo['name']);
            $contentCase = HpErrorMessage::getTelMessageFollowReplyCase($followInfo['reply_hospital_name'],
                    $followInfo['reply_doctor_name'], $followInfo['follow_hospital_name']);
        }
        
        $telDoctor = Dbi::getDbi()->getTelList($followInfo['follow_hospital_id']);
        if (VALUE_DB_ERROR === $telDoctor) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $telCase = Dbi::getDbi()->getTelCase($followInfo['case_id']);
        if (VALUE_DB_ERROR === $telCase) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $replyQuestion = isset($this->param['reply_question']) ? $this->param['reply_question'] : '';
        $ret = Dbi::getDbi()->replyFollow($this->param['follow_id'], 
                $this->param['reply_user'], $this->param['reply_advice'], $replyQuestion);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        foreach ($telDoctor as $tel) {
            if (true === HpValidate::checkPhoneNo($tel['tel'])) {
                $ret = HpShortMessageService::send($tel['tel'], $contentDoctor);
                if (false === $ret) {
                    return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
                }
            }
        }
        if ('' != $telCase && true === HpValidate::checkPhoneNo($telCase)) {
            $ret = HpShortMessageService::send($telCase, $contentCase);
            if (false === $ret) {
                return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
            }
        }
        
        return $this->retSuccess;
    }
}
