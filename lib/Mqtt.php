<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_GETUI . 'IGt.Push.php';
require_once PATH_ROOT . 'vendor\\emqx\\phpMQTT.php';

class Mqtt
{
    private $logFile = 'mqtt_logic.log';
    private $logFileError = 'mqtt_error.log';
    private $server = '39.106.71.114';
    private $port = 1883;
    private $user = 'yizhong';
    private $password = 'yizhong2020';
    private $mqtt = null;
    
    public function __construct()
    {
        try {
            if ($this->mqtt == null) {
                $this->mqtt = new phpMQTT($this->server, $this->port, uniqid());
            }
        } catch (Exception $e) {
            Logger::writeByHour($this->logFileError, $e->getMessage());
        }
    }
    
    /**
     * @param array $data [['type'=>'holter','id'=>'123','event'=>'upload_24h','message'=>'id=12345,url=1.zip...']
     * ['type'=>'online','id'=>'456','event'=>'ecg','message'=>'id=12345,time=20200601']]
     * @return boolean
     */
    public function publish(array $data)
    {
        $qos = 0;
        $retain = 0;
        if (!is_array($data) || empty($data)) {
            Logger::write($this->logFileError, 'topic or message param is wrong.');
            return false;
        }
        $topicList = array();
        $messageList = array();
        foreach ($data as $row) {
            if (!is_array($row) || !isset($row['type']) || !isset($row['id'])
                || !isset($row['event']) || !isset($row['message'])) {
                    Logger::write($this->logFileError, 'format error.');
                    return false;
                }
                if ($row['type'] == 'online') {
                    $topic = $row['type'] . '/';
                    $relationFile = PATH_DATA . 'relation' . DIRECTORY_SEPARATOR . $row['id'] . '.txt';
                    if (file_exists($relationFile)) {
                        $idLevel = file_get_contents($relationFile);
                    } else {
                        $idLevel = $row['id'] . '/' . $row['id'] . '/' . $row['id'] . '/' . $row['id'];
                    }
                    $topic .= $idLevel . '/' . $row['event'];
                } elseif ($row['type'] == 'holter') {
                    $topic = $row['type'] . '/' . $row['id'] . '/' . $row['event'];
                } else {
                    Logger::write($this->logFileError, 'type is wrong.');
                    return false;
                }
                
                $topicList[] = $topic;
                $messageList[] = $row['message'];
        }
        try {
            if ($this->mqtt->connect(true, null, $this->user, $this->password)) {
                for ($i = 0; $i < count($topicList); $i++) {
                    $this->mqtt->publish($topicList[$i], $messageList[$i], $qos, $retain);
                    Logger::write($this->logFile, 'publish topic : ' . $topicList[$i] . ' with message : ' . $messageList[$i]);
                }
                
                $this->mqtt->close();
                return true;
            } else {
                Logger::write($this->logFileError, 'connect failed');
                return false;
            }
        } catch (Exception $e) {
            Logger::write($this->logFileError, $e->getMessage());
            return false;
        }
    }
}
