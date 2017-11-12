<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditFollowTelMessage extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['follow_plan_id', 'doctor_id', 'message'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['follow_plan_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedFollowPlan($this->param['follow_plan_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'follow_plan_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $detail = Dbi::getDbi()->getFollowPlanDetail($this->param['follow_plan_id']);
        if (VALUE_DB_ERROR === $detail) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($detail)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $message = $detail['tel_message'] . $this->param['message'] . '$';
        $data = ['doctor_id' => $this->param['doctor_id'], 'tel_message' => $message];
        $ret = Dbi::getDbi()->editFollowPlan($this->param['follow_plan_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
