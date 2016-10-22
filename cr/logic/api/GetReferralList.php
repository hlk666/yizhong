<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetReferralList extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $checkRequired = HpValidate::checkRequiredParam(['hospital_id', 'status'], $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['hospital_id', 'page', 'rows'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        $checkRange = HpValidate::checkRange(['status'], $this->param, [NO_FILTER, 'start', 'ok', 'deny', 'confirm', 'discharge']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $status = ($this->param['status'] == NO_FILTER) ? null : constant('REFERRAL_' . strtoupper($this->param['status']));
        if (!isset($this->param['page']) && !isset($this->param['rows'])) {
            $cases = Dbi::getDbi()->getReferralList($this->param['hospital_id'], $status);
        } else {
            $page = isset($this->param['page']) ? $this->param['page'] : 0;
            $rows = isset($this->param['rows']) ? $this->param['rows'] : VALUE_DEFAUTL_ROWS;
            $cases = Dbi::getDbi()->getReferralList($this->param['hospital_id'], $status, $page * $rows, $rows);
        }
        if (VALUE_DB_ERROR === $cases) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($cases)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $this->retSuccess['cases'] = $cases;
        return $this->retSuccess;
    }
}
