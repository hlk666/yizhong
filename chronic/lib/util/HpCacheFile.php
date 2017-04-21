<?php
require_once PATH_ROOT . 'lib/util/HpBaseCache.php';
class HpCacheFile extends HpBaseCache
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
            return false;
        }
        
        if (file_exists($this->_file)) {
            include $this->_file;
        } else {
            $fileData = array();
        }
        
        foreach ($data as $field => $value) {
            $fileData[$field] = $value;
        }

        $template = $this->getTemplate($fileData);
        $retIO = $this->writeFile($this->_file, $template);
        if (false === $retIO) {
            return false;
        }
        return true;
    }
    
    public function get()
    {
        if (!file_exists($this->_file)) {
            return array();
        }
        include $this->_file;
        return $fileData;
    }
    
    public function delete()
    {
        if (file_exists($this->_file)) {
            $this->backupCache($this->get());
            unlink($this->_file);
        }
        return true;
    }
    
    private function getTemplate($fileData)
    {
        $template = "<?php\n";
        $template .= '$fileData = array();' . "\n";
        foreach ($fileData as $field => $value) {
            $template .= '$fileData' . "['$field'] = '$value';\n";
        }
        $template .= "\n";
        return $template;
    }
}