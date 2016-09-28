<?php
require_once PATH_ROOT . 'logic/BaseLogic.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetHospitalList extends BaseLogic
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
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
        if (!isset($this->param['page']) && !isset($this->param['rows'])) {
            $hospitals = Dbi::getDbi()->getHospitalList();
        } else {
            $page = isset($this->param['page']) ? $this->param['page'] : 0;
            $rows = isset($this->param['rows']) ? $this->param['rows'] : VALUE_DEFAUTL_ROWS;
            $hospitals = Dbi::getDbi()->getHospitalList($page * $rows, $rows);
        }
        if (VALUE_DB_ERROR === $hospitals) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($hospitals)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $this->retSuccess['hospitals'] = $hospitals;
        return $this->retSuccess;
    }
}
