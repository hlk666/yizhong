<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetReferralReply extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['hospital_id']) ? HpValidate::checkRequired($this->param['hospital_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'hospital_id.');
        }
        
        if (false === is_numeric($this->param['hospital_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'hospital_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $referrals = Dbi::getDbi()->getReferralReply($this->param['hospital_id']);
        if (VALUE_DB_ERROR === $referrals) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($referrals)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $this->retSuccess['referrals'] = $referrals;
        return $this->retSuccess;
    }
}
