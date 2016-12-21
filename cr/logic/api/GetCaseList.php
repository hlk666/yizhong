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
        
        $checkRequired = HpValidate::checkRequiredParam(['hospital_id', 'type'], $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['hospital_id', 'page', 'rows'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        $checkRange = HpValidate::checkRange(['type'], $this->param, [NO_FILTER, 'consultation', 'referral']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $func = 'getCaseList' . ucwords($this->param['type']);
        if (!isset($this->param['page']) && !isset($this->param['rows'])) {
            $cases = Dbi::getDbi()->$func($this->param['hospital_id']);
        } else {
            $page = isset($this->param['page']) ? $this->param['page'] : 0;
            $rows = isset($this->param['rows']) ? $this->param['rows'] : VALUE_DEFAUTL_ROWS;
            $cases = Dbi::getDbi()->$func($this->param['hospital_id'], $page * $rows, $rows);
        }
        if (VALUE_DB_ERROR === $cases) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($cases)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        if (isset($this->param['filter_name'])) {
            $filterNames = explode(',', $this->param['filter_name']);
            $filterValues = explode(',', $this->param['filter_value']);
            $cases = parent::filter($cases, $filterNames, $filterValues);
            if (false === $cases) {
                return HpErrorMessage::getError(ERROR_SEARCH);
            }
        }
        
        $this->retSuccess['cases'] = $cases;
        return $this->retSuccess;
    }
}
