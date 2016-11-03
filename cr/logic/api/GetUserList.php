<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetUserList extends BaseLogicApi
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
        $hospitalId = isset($this->param['hospital_id']) ? $this->param['hospital_id'] : null;
        if (!isset($this->param['page']) && !isset($this->param['rows'])) {
            $users = Dbi::getDbi()->getUserList($hospitalId);
        } else {
            $page = isset($this->param['page']) ? $this->param['page'] : 0;
            $rows = isset($this->param['rows']) ? $this->param['rows'] : VALUE_DEFAUTL_ROWS;
            $users = Dbi::getDbi()->getUserList($hospitalId, $page * $rows, $rows);
        }
        if (VALUE_DB_ERROR === $users) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($users)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $this->retSuccess['users'] = $users;
        return $this->retSuccess;
    }
}
