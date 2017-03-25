<?php
require_once PATH_ROOT . 'lib/util/HpCacheFile.php';
require_once PATH_ROOT . 'lib/util/HpCacheFile2D.php';

class HpNoticeQuery
{
    private $_cacheFile;
    private $_dimension;
    private $_logFile = 'notice_query.log';
    public function __construct($category, $type, $id, $dimension)
    {
        $this->_dimension = $dimension;
        
        if (DIMENSION_ONE === $dimension) {
            $this->_cacheFile = new HpCacheFile($category, $type, $id);
        } elseif (DIMENSION_TWO === $dimension) {
            $this->_cacheFile = new HpCacheFile2D($category, $type, $id);
        } else {
            $this->_cacheFile = null;
        }
    }
    
    public function set($data)
    {
        if (null === $this->_cacheFile || empty($data)) {
            return false;
        }
        
        if (DIMENSION_ONE === $this->_dimension && count($data) != count($data, COUNT_RECURSIVE)) {
            $data = current($data);
        }
        if (DIMENSION_TWO === $this->_dimension && count($data) == count($data, COUNT_RECURSIVE)) {
            $data = [$data];
        }
        
        return $this->_cacheFile->add($data);
    }
    
    public function getAll($sortKey = null, $order = SORT_ASC)
    {
        if (null === $this->_cacheFile) {
            return array();
        }
        
        if (DIMENSION_ONE === $this->_dimension) {
            return $this->_cacheFile->get();
        } elseif (DIMENSION_TWO === $this->_dimension) {
            return $this->_cacheFile->getAll($sortKey, $order);
        } else {
            return array();
        }
    }
    
    public function getOne($id)
    {
        if (null === $this->_cacheFile) {
            return array();
        }
    
        if (DIMENSION_ONE === $this->_dimension) {
            return $this->_cacheFile->get();
        } elseif (DIMENSION_TWO === $this->_dimension) {
            return $this->_cacheFile->getOne($id);
        } else {
            return array();
        }
    }
    
    public function delete($id = null)
    {
        if (null === $this->_cacheFile) {
            return true;
        }
        if (DIMENSION_ONE === $this->_dimension) {
            return $this->_cacheFile->delete();
        } elseif (DIMENSION_TWO === $this->_dimension) {
            return $this->_cacheFile->remove($id);
        } else {
            return true;
        }
    }
}
