<?php
require_once PATH_ROOT . 'lib/db/DbiCache.php';
class HpBaseCache
{
    protected $_category;
    protected $_type;
    protected $_id;
    
    protected function backupCache(array $data)
    {
        DbiCache::getDbi()->backupCache($this->_category, $this->_type, $this->_id, serialize($data));
    }
    
    protected function writeFile($file, $text)
    {
        $handle = fopen($file, 'w+');
    
        if (flock($handle, LOCK_EX)) {
            fwrite($handle, $text);
            flock($handle, LOCK_UN);
        } else {
            return false;
        }
    
        fclose($handle);
        return true;
    }
    
    protected function add(array $data)
    {
        return true;
    }
    
    protected function remove($id)
    {
        return true;
    }
    
    protected function delete()
    {
        return true;
    }
}