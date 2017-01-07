<?php
/**
 * two types.
 * 1:conig data-need update but not overwritten.
 * 2:cache data-clear every time.
 */
class HpDataFile
{
    private static $logFile = 'FileData.log';

    /**
     * set arrays to file. 
     * the array is at most two dimensional.
     * data file will be overwritten every time.
     * 
     * @param string $file
     * @param arrays formatter of 'key => value' is required. 
     * @return boolean
     */
    public static function setDataFile($file)
    {
        $args = func_get_args();
        unset($args[0]);
        
        $file = PATH_ROOT . 'data' . DIRECTORY_SEPARATOR . $file . '.php';
        $template = "<?php\n";
        foreach ($args as $arrayParam) {
            foreach ($arrayParam as $arrayName => $arrayKeyValue) {
                $template .= '$' . $arrayName . ' = array();' . "\n";
                foreach ($arrayKeyValue as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            $template .= '$' . $arrayName . "['$key']['$subKey'] = '$subValue';\n";
                        }
                    } else {
                        $template .= '$' . $arrayName . "['$key'] = '$value';\n";
                    }
                } //$value
                $template .= "\n";
            } //$arrayKeyValue
        } //$arrayParam
        $retIO = self::writeFile($file, $template);
        if (false === $retIO) {
            return false;
        }
        return true;
    }
    
    /**
     * get data(by include).
     * @param string $directory
     * @param string $file
     */
    public static function getDataFile($file)
    {
        $file = PATH_ROOT . 'data' . DIRECTORY_SEPARATOR . $file . '.php';
        if (file_exists($file)) {
            return $file;
        } else {
            return false;
        }
    }
    
    private static function writeFile($file, $text)
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
}