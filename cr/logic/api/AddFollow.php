<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class AddFollow extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $params = [
                        'follow_hospital' => $this->param['follow_hospital'],
                        'discharge_hospital' => $this->param['discharge_hospital'],
                        'case_id' => $this->param['case_id'],
                        'user_id' => $this->param['user_id'],
                        'symptom' => $this->param['symptom'],
                        'advice' => $this->param['advice'],
                        'follow_date' => $this->param['follow_date'],
        ];
        $checkRequired = HpValidate::checkRequiredArray($params);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        if (false === is_numeric($this->param['follow_hospital'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'follow_hospital.');
        }
        if (false === is_numeric($this->param['discharge_hospital'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'discharge_hospital.');
        }
        if (false === is_numeric($this->param['case_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'case_id.');
        }
        if (false === is_numeric($this->param['user_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'user_id.');
        }
        
        if ('' == $this->param['symptom']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'symptom.');
        }
        if ('' == $this->param['advice']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'advice.');
        }
        if (isset($this->param['question']) && '' == $this->param['question']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'question.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $question = isset($this->param['question']) ? $this->param['question'] : '';
        $imgEcg = isset($this->param['img_ecg']) ? $this->param['img_ecg'] : '';
        $imgHolter = isset($this->param['img_holter']) ? $this->param['img_holter'] : '';
        $imgEchocardiography = isset($this->param['img_echocardiography']) ? $this->param['img_echocardiography'] : '';
        $imgInr = isset($this->param['img_inr']) ? $this->param['img_inr'] : '';
        $imgOther = isset($this->param['img_other']) ? $this->param['img_other'] : '';
        
        $telDoctor = Dbi::getDbi()->getTelList($this->param['discharge_hospital']);
        if (VALUE_DB_ERROR === $telDoctor) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $telCase = Dbi::getDbi()->getTelCase($this->param['case_id']);
        if (VALUE_DB_ERROR === $telCase) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $followId = Dbi::getDbi()->addFollow($this->param['follow_hospital'], $this->param['discharge_hospital'], 
                $this->param['case_id'], $this->param['user_id'], $this->param['symptom'], $this->param['advice'], 
                $question, $imgEcg, $imgHolter, $imgEchocardiography, $imgInr, $imgOther, $this->param['follow_date']);
        if (VALUE_DB_ERROR === $followId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $followInfo = Dbi::getDbi()->getFollowInfo($followId);
        if (VALUE_DB_ERROR === $followInfo || empty($followInfo)) {
            return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
        } else {
            $contentDoctor = HpErrorMessage::getTelMessageFollowDoctor($followInfo['follow_hospital_name'],
                    $followInfo['follow_doctor_name'], $followInfo['name']);
            $contentCase = HpErrorMessage::getTelMessageFollowCase($followInfo['follow_hospital_name'],
                    $followInfo['follow_doctor_name'], $followInfo['reply_hospital_name']);
        }
        
        foreach ($telDoctor as $tel) {
            if (true === HpValidate::checkPhoneNo($tel)) {
                $ret = HpShortMessageService::send($tel, $contentDoctor);
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
