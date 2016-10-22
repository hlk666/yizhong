<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetCase extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $checkRequired = HpValidate::checkRequiredParam(['case_id'], $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['case_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $case = Dbi::getDbi()->getCaseCase($this->param['case_id']);
        if (VALUE_DB_ERROR === $case) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($case)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $consultation = Dbi::getDbi()->getCaseConsultation($this->param['case_id']);
        if (VALUE_DB_ERROR === $consultation) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $referral = Dbi::getDbi()->getCaseReferral($this->param['case_id']);
        if (VALUE_DB_ERROR === $referral) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $follow = Dbi::getDbi()->getCaseFollow($this->param['case_id']);
        if (VALUE_DB_ERROR === $follow) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $this->retSuccess = array_merge($this->retSuccess, $case);
        $this->retSuccess['consultation'] = $consultation;
        $this->retSuccess['referral'] = $referral;
        $this->retSuccess['follow'] = $follow;
        
        return $this->retSuccess;
    }
}
