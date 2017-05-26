<?php
class HpVerificationCode
{
    private static $letters = 'abcdefghijklmnopqrstuvwxyz';
    private static $numbers = '0123456789';
    
    public static function getNumericVC($length = 4)
    {
        $vc = '';
        for ($i = 1; $i <= $length; $i++) {
            $index = rand(0, 9);
            $vc .= substr(self::$numbers, $index, 1);
        }
        
        return $vc;
    }
    
    public static function getLetterVC($length = 4)
    {
        $vc = '';
        for ($i = 1; $i <= $length; $i++) {
            $index = rand(0, 25);
            $vc .= substr(self::$letters, $index, 1);
        }
        
        return $vc;
    }
    
    public static function createFileNumericVC($sid, $length = 4)
    {
        $vc = self::getNumericVC($length);
        self::writeVerificationCodeFile($sid, $vc);
        
        return $vc;
    }
    
    public static function getVC($sid)
    {
        $vcFile = PATH_ROOT . 'vc' . DIRECTORY_SEPARATOR . $sid . '.php';
        if (!file_exists($vcFile)) {
            return null;
        }
        include $vcFile;
        return $vcServer;
    }
    
    public static function createFileLetterVC($sid, $length = 4)
    {
        $vc = self::getLetterVC($length);
        self::writeVerificationCodeFile($sid, $vc);
    
        return $vc;
    }
    
    private static function writeVerificationCodeFile($sid, $vc)
    {
        $dir = PATH_ROOT . 'vc' . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $vcFile =  $dir . $sid . '.php';
        $template = "<?php\n";
        $template .= '$vcServer = \'' . $vc . "';\n";
        file_put_contents($vcFile, $template);
    }
}