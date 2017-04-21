<?php
require_once PATH_ROOT . 'lib/util/HpBaseCache.php';
class HpCacheFile2D extends HpBaseCache
{
    private $_file = '';
    
    public function __construct($category, $type, $id)
    {
        if (empty($category) || empty($type || empty($id))) {
            $this->_file = '';
            return;
        }
        $this->_category = $category;
        $this->_type = $type;
        $this->_id = $id;
        
        $dir = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $dir .= $type . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $this->_file = $dir . $id . '.php';
    }
    public function add(array $data)
    {
        if ('' == $this->_file) {
            return VALUE_PARAM_ERROR;
        }
        
        if (file_exists($this->_file)) {
            include  $this->_file;
            $maxId = max(array_keys($fileData));
        } else {
            $maxId = 0;
        }
        foreach ($data as $item) {
            $maxId++;
            $fileData[$maxId] = $item;
        }
        $template = $this->getTemplate($fileData);
        $retIO = self::writeFile($this->_file, $template);
        if (false === $retIO) {
            return false;
        }
        return true;
    }
    
    public function getAll($sortKey, $order)
    {
        if (!file_exists($this->_file)) {
            return array();
        }
        include $this->_file;
        if (null !== $sortKey) {
            foreach ($fileData as $row) {
                $sortArray[] = $row[$sortKey];
            }
            array_multisort($sortArray, $order, SORT_STRING, $fileData);
        }
        return $fileData;
    }
    
    public function getOne($id)
    {
        if (!file_exists($this->_file)) {
            return array();
        }
        
        include $this->_file;
        if (array_key_exists($id, $fileData)) {
            return $fileData[$id];
        } else {
            return array();
        }
    }
    
    public function remove($id)
    {
        if (!file_exists($this->_file) || null === $id) {
            return VALUE_PARAM_ERROR;
        }
        
        include $this->_file;
        if (array_key_exists($id, $fileData)) {
            $this->backupCache($fileData[$id]);
            unset($fileData[$id]);
            $template = $this->getTemplate($fileData);
            unlink($this->_file);
            $retIO = $this->writeFile($this->_file, $template);
            if (false === $retIO) {
                return false;
            }
            return true;
        } else {
            return VALUE_PARAM_ERROR;
        }
    }
    
    private function getTemplate($fileData)
    {
        $template = "<?php\n";
        $template .= '$fileData = array();' . "\n";
        foreach ($fileData as $rowNo => $row) {
            foreach ($row as $field => $value) {
                $template .= '$fileData' . "['$rowNo']['$field'] = '$value';\n";
            }
        }
        $template .= "\n";
        return $template;
    }
    
}