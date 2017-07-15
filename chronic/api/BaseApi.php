<?php
require_once PATH_ROOT . 'lib/tool/HpSession.php';
require_once PATH_ROOT . 'lib/util/HpValidate.php';

class BaseApi
{
    protected $retSuccess;
    protected $param = array();
    
    protected function validate($class)
    {
        $queryString = "access class[$class] with GET : ";
        foreach ($_GET as $key => $value) {
            $this->param[$key] = trim($value);
            $queryString .= "$key => $value, ";
        }
        $queryString = substr($queryString, 0, -2) . "\r\n";
        $queryString .= "access class[$class] with POST : ";
        
        foreach ($_POST as $key => $value) {
            $this->param[$key] = trim($value);
            $queryString .= "$key => $value, ";
        }
        $queryString = substr($queryString, 0, -2);
        HpLogger::write($queryString, 'debug.log');
        
        $data = file_get_contents('php://input');
        if (!empty($data)) {
            $this->param['data'] = $data;
        }
        
        return $this->authorize($class);
    }
    
    private function authorize($class)
    {
        /*
        $currentAuthorityLevel = HpAuthority::getClassAuthority($class);
        if (AUTHORITY_OTHER === $currentAuthorityLevel) {
            return true;
        }
        
        if (!isset($this->param['sid']) || empty($this->param['sid'])) {
            return HpErrorMessage::getError(ERROR_LOGIN_NO);
        }
        
        $session = new HpSession($this->param['sid']);
        return $session->checkSession($currentAuthorityLevel);
        */
        return true;
    }
    
    protected function execute()
    {
        return $this->retSuccess;
    }
    
    protected function filter(array $data, array $names, array $values)
    {
        $length = count($names);
        if ($length == 0 || $length != count($values)) {
            return false;
        }
        
        $retArray = array();
        foreach ($data as $row) {
            for ($i = 0; $i < $length; $i++) {
                $filterName = $names[$i];
                $filterValue = $values[$i];
                if (empty($filterName) || empty($filterValue)) {
                    return false;
                }
                if (false === stripos($row[$filterName], $filterValue)) {
                    continue 2;
                }
            }
            $retArray[] = $row;
        }
        return $retArray;
    }
    
    public function run()
    {
        //$startTime = microtime_float();
        
        $noError = $this->validate();
        if (true === $noError)
        {
            $this->retSuccess = HpErrorMessage::getError(ERROR_SUCCESS);
            $model = $this->execute();
        } else {
            $model = $noError;
        }
        
        echo json_encode($model, JSON_UNESCAPED_UNICODE);
    }
    
    protected function getStructalData($text)
    {
        $data = array();
        $rows = array_filter(explode(';', $text));
        foreach ($rows as $row) {
            $rowData = array();
            $columns = array_filter(explode(',', $row));
            if (count($columns) != 2) {
                return array();
            }
            $data[] = $columns;
        }
        return $data;
    }
    protected function getStructalDataWithHeader($text)
    {
        $data = array();
        $rows = array_filter(explode(';', $text));
        foreach ($rows as $row) {
            $rowData = array();
            $columns = array_filter(explode(',', $row));
            foreach ($columns as $column) {
                $colArray = explode(':', $column);
                if (2 != count($colArray)) {
                    return false;
                }
                $rowData[$colArray[0]] = $colArray[1];
            }
            $data[] = $rowData;
        }
        return $data;
    }
}