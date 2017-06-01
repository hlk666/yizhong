<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetRecordInfo extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['record_id', 'type'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['record_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        $checkRange = HpValidate::checkRange(['type'], $this->param, ['outpatient', 'follow', 'consultation', 'referral']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        return true;
    }
    
    protected function execute()
    {
        if ($this->param['type'] == 'outpatient') {
            $recordInfo = Dbi::getDbi()->getRecordInfoOutpatient($this->param['record_id']);
        } elseif ($this->param['type'] == 'follow') {
            $recordInfo = Dbi::getDbi()->getRecordInfoFollow($this->param['record_id']);
        } elseif ($this->param['type'] == 'consultation') {
            $recordInfo = Dbi::getDbi()->getRecordInfoConsultation($this->param['record_id']);
        } elseif ($this->param['type'] == 'referral') {
            $recordInfo = Dbi::getDbi()->getRecordInfoReferral($this->param['record_id']);
        } else {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'type');
        }
        if (VALUE_DB_ERROR === $recordInfo) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($recordInfo)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        return array_merge($this->retSuccess, $recordInfo);
    }
}
