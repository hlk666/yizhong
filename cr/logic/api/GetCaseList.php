<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetCaseList extends BaseLogicApi
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
        
        if (isset($this->param['start_time']) && false === HpValidate::checkTime($this->param['start_time'])) {
            return HpErrorMessage::getError(ERROR_PARAM_TIME);
        }
        
        if (isset($this->param['end_time']) && false === HpValidate::checkTime($this->param['end_time'])) {
            return HpErrorMessage::getError(ERROR_PARAM_TIME);
        }
        
        if (isset($this->param['page']) && !is_numeric($this->param['page'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'page');
        }
        if (isset($this->param['rows']) && !is_numeric($this->param['rows'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'rows');
        }
        
        return true;
    }
    
    protected function execute()
    {
        //$startTime = isset($this->param['start_time']) ? $this->param['start_time'] : null;
        //$endTime = isset($this->param['end_time']) ? $this->param['end_time'] : null;
        
        if (!isset($this->param['page']) && !isset($this->param['rows'])) {
            $cases = Dbi::getDbi()->getCaseList($this->param['hospital_id']);
        } else {
            $page = isset($this->param['page']) ? $this->param['page'] : 0;
            $rows = isset($this->param['rows']) ? $this->param['rows'] : VALUE_DEFAUTL_ROWS;
            $cases = Dbi::getDbi()->getCaseList($this->param['hospital_id'], $page * $rows, $rows);
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
