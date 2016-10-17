<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetReferralInfo extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['referral_id']) ? HpValidate::checkRequired($this->param['referral_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'referral_id.');
        }
        
        if (false === is_numeric($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'referral_id.');
        }
        
        if (false === Dbi::getDbi()->existedReferral($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'referral_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $referral = Dbi::getDbi()->getReferralInfo($this->param['referral_id']);
        if (VALUE_DB_ERROR === $referral) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($referral)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        return array_merge($this->retSuccess, $referral);
    }
}
