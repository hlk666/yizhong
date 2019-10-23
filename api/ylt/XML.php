<?php
class XML
{
    private static $logFile = 'xml.log';
    public static function getXml($xml)
    {
        $xml = str_replace('GB2312', 'utf-8', $xml);
        $ret = array();
        try {
            $ret = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        } catch (Exception $e) {
            Logger::write(self::$logFile, $e->getMessage());
            return false;
        }
        return $ret;
    }
    public static function createXml($arrValue)
    {
        $xml = '<?xml version="1.0" encoding="GB2312"?><OriginalSummary>';
        foreach ($arrValue as $key => $value) {
            if (is_array($value)) {
                $xml .= "<$key>";
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        $xml .= "<$k>";
                        foreach ($v as $k1 => $v1) {
                            $xml .= "<$k1>$v1</$k1>";
                        }
                        $xml .= "</$k>";
                    } else {
                        $xml .= "<$k>$v</$k>";
                    }
                }
                $xml .= "</$key>";
            } else {
                $xml .= "<$key>$value</$key>";
            }
        }
        $xml .= '</OriginalSummary>';
        return $xml;
    }
    public static function createXmlForDownload($resultCode, $originalFilePath, 
            $ftpFilePath, $ftpUser, $ftpPassword, $ftpFiles, $resutInfo)
    {
        $xml = '<?xml version="1.0" encoding="GB2312"?><OriginalSummary><ResultCode>';
        $xml .= $resultCode . '</ResultCode><OutData><OriginalFilePath>' . $originalFilePath . '</OriginalFilePath>';
        $xml .= '<AnalysisFilePath FilePath="' . $ftpFilePath . '" FTPUser="' . $ftpUser . '" FTPPassword="' . $ftpPassword . '">';
        foreach ($ftpFiles as $file) {
            $xml .= "<FileName>$file</FileName>";
        }
        $xml .= "</AnalysisFilePath></OutData><ResultInfo>$resutInfo</ResultInfo></OriginalSummary>";
        return $xml;
    }
    public static function createXmlForDownloadPDF($resultCode, $pdfFilePath, $ftpUser, $ftpPassword, $fileName, $resutInfo)
    {
        $xml = '<?xml version="1.0" encoding="GB2312"?><OriginalSummary><ResultCode>' . $resultCode . '</ResultCode><OutData>';
        $xml .= '<PDFFilePath FilePath="' . $pdfFilePath . '" FTPUser="' . $ftpUser . '" FTPPassword="' . $ftpPassword . '">';
        $xml .= "<FileName>$fileName</FileName></PDFFilePath></OutData><ResultInfo>$resutInfo</ResultInfo></OriginalSummary>";
        return $xml;
    }
}