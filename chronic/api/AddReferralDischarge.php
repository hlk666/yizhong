<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddReferralDischarge extends BaseApi
{
    private $planList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['referral_id', 'department_id', 'doctor_id', 'diagnosis', 'info', 
                        'patient_id', 'plan_text', 'plan_name'];
        
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['referral_id', 'department_id', 'doctor_id', 'patient_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedReferral($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'referral_id.');
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        $this->planList = $this->getStructalData($this->param['plan_text']);
        if (empty($this->planList)) {
            return HpErrorMessage::getError(ERROR_PARAM_FORMAT, 'plan_text.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        Dbi::getDbi()->beginTran();
        $followPlanId = Dbi::getDbi()->addFollowPlan($this->param['department_id'], $this->param['patient_id'],
                $this->param['plan_text'], $this->planList, $this->param['doctor_id'], $this->param['plan_name']);
        if (VALUE_DB_ERROR === $followPlanId) {
            Dbi::getDbi()->rollBack();
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $ret = Dbi::getDbi()->addReferralReply($this->param['referral_id'], $this->param['doctor_id'], 
                $this->param['diagnosis'], $this->param['info'], $followPlanId);
        if (VALUE_DB_ERROR === $ret) {
            Dbi::getDbi()->rollBack();
            return HpErrorMessage::getError(ERROR_DB);
        }
        Dbi::getDbi()->commit();
        return $this->retSuccess;
    }
}
