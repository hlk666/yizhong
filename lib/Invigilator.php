<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'GeTui.php';

class Invigilator
{
    private $info = array();
    private $gtFlag = true;
    private $allowCommands = array(
            'action', 'card', 'all_time', 'check_info',
            'mode1_polycardia', 'mode1_bradycardia', 'mode1_lead',
            'mode2_record_time', 'mode2_polycardia', 'mode2_bradycardia', 'mode2_exminrate',
            'mode2_combeatrhy', 'mode2_stopbeat', 'mode2_sthigh', 'mode2_stlow', 'mode2_twave',
            'mode2_regular_time', 'mode2_premature_beat', 'mode2_lead',
            'mode3_polycardia', 'mode3_bradycardia', 'mode3_lead', 'mode3_record_time');
    private $commands = array();
    private $file = '';
    private $logFile = 'cmdLog.txt';
    private $guardianId;
    
    public function __construct($guardianId, $mode = '0', $hours = '24')
    {
        $this->guardianId = $guardianId;
        $this->file = PATH_CACHE_CMD . $guardianId . '.php';
        if (file_exists($this->file)) {
            include $this->file;
            if ($info['status'] == 2) {
                unlink($this->file);
                $this->setDefaultInfo($mode, $hours);
            } else {
                $this->info = $info;
                $this->commands = $command;
            }
        } else {
            $this->setDefaultInfo($mode, $hours);
        }
    }
    
    private function validate()
    {
        if (null == $this->guardianId || empty($this->guardianId)) {
            return false;
        }
        if (!is_numeric($this->guardianId)) {
            return false;
        }
        return true;
    }
    
    private function getClientId()
    {
        $deviceId = Dbi::getDbi()->getDeviceId($this->guardianId);
        if (empty($deviceId)) {
            return '';
        }
        $file = PATH_CACHE_CLIENT . $deviceId . '.php';
        if (file_exists($file)) {
            include $file;
            return $clientId;
        } else {
            return '';
        }
    }
    
    public function clearCommand()
    {
        $this->gtFlag = false;
        $this->commands = array();
        $this->create(array());
    }
    
    public function getCommand()
    {
        if (!empty($this->info['end_time'])) {
            if ($this->info['end_time'] != '' && time() >= $this->info['end_time']) {
                $this->commands['action'] = 'end';
                $this->setEnd();
            }
        }
        return $this->commands;
    }
    
    public function create(array $data = array())
    {
        Logger::writeCommands($this->logFile, $this->guardianId, $data);
        if (false === $this->validate()) {
            $this->gtFlag = true;
            return VALUE_PARAM_ERROR;
        }
        $commandKeys = array_intersect($this->allowCommands, array_keys($data));
        foreach ($commandKeys as $cmd) {
            $this->commands[$cmd] = $data[$cmd];
        }
        
        foreach ($data as $key => $value) {
            $this->info[$key] = $value;
        }
        
        if ($this->info['mode'] == 3 && isset($this->commands['action'])) {
            unset($this->commands['action']);
        }
        if (isset($data['action'])) {
            $ret = $this->handleAction($data['action']);
            if (VALUE_DB_ERROR === $ret) {
                $this->gtFlag = true;
                return VALUE_DB_ERROR;
            }
        }
        
        $this->clearInfoNotNeed();
        
        $template = $this->getTemplateInfo() . $this->getTemplateCmd();
        
        $handle = fopen($this->file, 'w');
        fwrite($handle, $template);
        fclose($handle);
        
        if ($this->gtFlag) {
            $clientId = $this->getClientId();
            if ('' == $clientId) {
                return VALUE_GT_ERROR;
            }
            $gt = GeTui::pushToSingle($clientId, 'READY');
            if (false === $gt) {
                return VALUE_GT_ERROR;
            }
        }
        $this->gtFlag = true;
        return true;
    }
    
    public function delete()
    {
        unlink($this->file);
        return Dbi::getDbi()->flowGuardianDelete($this->guardianId);
    }
    
    private function setDefaultInfo($mode, $hours)
    {
        $this->info['mode'] = $mode;
        $this->info['status'] = ($mode == '3') ? '3' : '0';
        $this->info['card'] = 'master';
        $this->info['all_time'] = $hours;
        $this->info['check_info'] = 'off';
        $this->info['mode1_polycardia'] = PARAM_POLYCARDIA;
        $this->info['mode1_bradycardia'] = PARAM_BRADYCARDIA;
        $this->info['mode1_lead'] = 'V1';
        
        $this->info['mode2_record_time'] = PARAM_MODE2_RECORD_TIME;
        $this->info['mode2_polycardia'] = PARAM_POLYCARDIA;
        $this->info['mode2_bradycardia'] = PARAM_BRADYCARDIA;
        $this->info['mode2_regular_time'] = PARAM_REGULAR_TIME;
        $this->info['mode2_premature_beat'] = PARAM_PREMATURE_BEAT;
        $this->info['mode2_lead'] = 'V1';
        $this->info['mode2_exminrate'] = PARAM_EXMINRATE;
        $this->info['mode2_combeatrhy'] = 'on';
        $this->info['mode2_stopbeat'] = PARAM_STOPBEAT;
        $this->info['mode2_sthigh'] = PARAM_STHIGH;
        $this->info['mode2_stlow'] = PARAM_STLOW;
        $this->info['mode2_twave'] = 'on';
        
        $this->info['mode3_polycardia'] = PARAM_POLYCARDIA;
        $this->info['mode3_bradycardia'] = PARAM_BRADYCARDIA;
        $this->info['mode3_lead'] = 'V1';
        $this->info['mode3_record_time'] = PARAM_MODE3_RECORD_TIME;
        
        $this->info['start_time'] = '';
        $this->info['end_time'] = '';
    }
    
    private function handleAction($action)
    {
        if ($this->info['mode'] != 1 && $this->info['mode'] != 2) {
            return true;
        }
        if ($action == 'start' && $this->info['end_time'] == '') {
            $this->info['status'] = 1;
            $this->info['start_time'] = time();
            $this->info['end_time'] = $this->info['start_time']
                + $this->info['all_time'] * 3600;
            $ret = Dbi::getDbi()->flowGuardianStartGuard($this->guardianId);
            if (VALUE_DB_ERROR === $ret) {
                return VALUE_DB_ERROR;
            }
        }
        if ($action == 'end') {
            $ret = $this->setEnd();
            if (VALUE_DB_ERROR === $ret) {
                return VALUE_DB_ERROR;
            }
        }
        return true;
    }
    
    private function getTemplateInfo()
    {
        $template = "<?php\n";
        $template .= '$info = array();' . "\n";
        
        foreach ($this->info as $key => $value) {
            $template .= "\$info['$key'] = '$value';\n";
        }
        $template .= "\n";
        
        return $template;
    }
    
    private function getTemplateCmd()
    {
        $template = "\$command = array();\n";
        foreach ($this->commands as $key => $value) {
            $template .= "\$command['$key'] = '$value';\n";
        }
        return $template;
    }
    
    private function clearInfoNotNeed()
    {
        if (isset($this->info['patient_id'])) {
            unset($this->info['patient_id']);
        }
        if (isset($this->info['action'])) {
            unset($this->info['action']);
        }
    }
    
    private function setEnd()
    {
        $ret = Dbi::getDbi()->flowGuardianEndGuard($this->guardianId);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        $this->info['status'] = 2;
        $this->info['start_time'] = '';
        $this->info['end_time'] = '';
        return true;
    }
}