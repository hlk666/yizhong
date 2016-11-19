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
    
    public static function getFileNumericVC($sid, $length = 4)
    {
        $vc = self::getNumericVC($length);
        self::writeVerificationCodeFile($sid, $vc);
        
        return $vc;
    }
    
    public static function getFileLetterVC($sid, $length = 4)
    {
        $vc = self::getLetterVC($length);
        self::writeVerificationCodeFile($sid, $vc);
    
        return $vc;
    }
    
    private static function writeVerificationCodeFile($sid, $vc)
    {
        $vcFile = PATH_ROOT . 'vc' . DIRECTORY_SEPARATOR . $sid . '.php';
        $template = "<?php\n";
        $template .= '$vcServer = ' . $vc . ";\n";
        file_put_contents($vcFile, $template);
    }
}