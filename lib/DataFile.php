<?php
require_once PATH_LIB . 'Logger.php';

class DataFile
{
    private static $logFile = 'FileData.log';

    /**
     * set arrays to file. the array is at most two dimensional.
     * @param string $directory
     * @param string $file
     * @param arrays formatter of 'key => value' is required. 
     * @return boolean
     */
    public static function setDataFile($directory, $file)
    {
        //get array list and delete param of directory and file.
        $args = func_get_args();
        unset($args[0]);
        unset($args[1]);
        
        $directory = PATH_DATA . $directory . DIRECTORY_SEPARATOR;
        if (!file_exists($directory)) {
            mkdir($directory);
        }
        $file = $directory . $file . '.php';
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
        $retIO = file_put_contents($file, $template);
        if (false === $retIO) {
            Logger::write(self::$logFile, 'Failed to write file of ' . $file);
            return false;
        }
        return true;
    }
    
    /**
     * get data(by include).
     * @param string $directory
     * @param string $file
     */
    public static function getDataFile($directory, $file)
    {
        $file = PATH_DATA . $directory . DIRECTORY_SEPARATOR . $file . '.php';
        if (file_exists($file)) {
            return $file;
        } else {
            return false;
        }
    }
}