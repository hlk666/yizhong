<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class DeleteReferralApply extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['referral_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['referral_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedReferral($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'referral_id.');
        }
        
        if (true === Dbi::getDbi()->existedReferralReplied($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_DELETE_DENY);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->deleteReferralApply($this->param['referral_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
