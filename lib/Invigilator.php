<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'GeTui.php';

class Invigilator
{
    private $info = array();
    private $gtFlag = true;
    private $allowCommands = array(
            'action', 'card', 'all_time', 'check_info', 'new_mode',
            'mode1_polycardia', 'mode1_bradycardia', 'mode1_lead',
            'mode2_record_time', 'mode2_polycardia', 'mode2_bradycardia', 'mode2_exminrate',
            'mode2_combeatrhy', 'mode2_stopbeat', 'mode2_sthigh', 'mode2_stlow', 'mode2_twave',
            'mode2_regular_time', 'mode2_premature_beat', 'mode2_lead',
            'mode3_polycardia', 'mode3_bradycardia', 'mode3_lead', 'mode3_record_time', 
            's_early_beat', 'v_early_beat', 'v_double', 'v_two', 'v_three', 's_double', 's_two', 's_three', 
            's_speed', 'v_speed', 'polycardia_hour', 'polycardia_times', 'bradycardia_hour', 'bradycardia_times', 
            'sthigh_hour', 'sthigh_times', 'stlow_hour', 'stlow_times', 's_early_beat_hour', 's_early_beat_times', 
            'v_early_beat_hour', 'v_early_beat_times', 'stopbeat_hour', 'stopbeat_times', 'v_double_hour', 'v_double_times', 
            'v_two_hour', 'v_two_times', 'v_three_hour', 'v_three_times', 's_double_hour', 's_double_times', 's_two_hour', 
            's_two_times', 's_three_hour', 's_three_times', 's_speed_hour', 's_speed_times', 'v_speed_hour', 'v_speed_times',
            'restart_bluetooth', 'upload_status');
    
    private $commands = array();
    private $file = '';
    private $logFile = 'cmdLog.txt';
    private $guardianId;
    
    public function __construct($guardianId, $hours = 24)
    {
        $this->guardianId = $guardianId;
        $this->file = PATH_CACHE_CMD . $guardianId . '.php';
        if (file_exists($this->file)) {
            include $this->file;
            $this->info = $info;
            $this->commands = $command;
        } else {
            $this->setDefaultInfo($hours);
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
                $deviceId = Dbi::getDbi()->getDeviceId($this->guardianId);
                Logger::write('getui_fail.log', 'GeTui failed with device_id : ' . $deviceId);
                return VALUE_GT_ERROR;
            }
            Logger::write($this->logFile, 'GeTui with id : ' . $clientId);
        }
        $this->gtFlag = true;
        return true;
    }
    
    public function delete()
    {
        unlink($this->file);
        return Dbi::getDbi()->flowGuardianDelete($this->guardianId);
    }
    
    private function setDefaultInfo($hours)
    {
        $this->info['card'] = 'master';
        $this->info['all_time'] = $hours;
        $this->info['check_info'] = 'off';
        $this->info['mode1_polycardia'] = PARAM_POLYCARDIA;
        $this->info['mode1_bradycardia'] = PARAM_BRADYCARDIA;
        $this->info['mode1_lead'] = PARAM_LEAD;
        
        $this->info['mode2_record_time'] = PARAM_MODE2_RECORD_TIME;
        $this->info['mode2_polycardia'] = PARAM_POLYCARDIA;
        $this->info['mode2_bradycardia'] = PARAM_BRADYCARDIA;
        $this->info['mode2_regular_time'] = PARAM_REGULAR_TIME;
        $this->info['mode2_premature_beat'] = PARAM_PREMATURE_BEAT;
        $this->info['mode2_lead'] = PARAM_LEAD;
        $this->info['mode2_exminrate'] = PARAM_EXMINRATE;
        $this->info['mode2_combeatrhy'] = PARAM_COMBEATRHY;
        $this->info['mode2_stopbeat'] = PARAM_STOPBEAT;
        $this->info['mode2_sthigh'] = PARAM_STHIGH;
        $this->info['mode2_stlow'] = PARAM_STLOW;
        $this->info['mode2_twave'] = PARAM_TWAVE;
        
        $this->info['mode3_polycardia'] = PARAM_POLYCARDIA;
        $this->info['mode3_bradycardia'] = PARAM_BRADYCARDIA;
        $this->info['mode3_lead'] = PARAM_LEAD;
        $this->info['mode3_record_time'] = PARAM_MODE3_RECORD_TIME;
        
        $this->info['s_early_beat'] = 'on';
        $this->info['v_early_beat'] = 'on';
        $this->info['v_double'] = 'on';
        $this->info['v_two'] = 'on';
        $this->info['v_three'] = 'on';
        $this->info['s_double'] = 'on';
        $this->info['s_two'] = 'on';
        $this->info['s_three'] = 'on';
        $this->info['s_speed'] = 'on';
        $this->info['v_speed'] = 'on';
        $this->info['polycardia_hour'] = '0';
        $this->info['polycardia_times'] = '0';
        $this->info['bradycardia_hour'] = '0';
        $this->info['bradycardia_times'] = '0';
        $this->info['sthigh_hour'] = '0';
        $this->info['sthigh_times'] = '0';
        $this->info['stlow_hour'] = '0';
        $this->info['stlow_times'] = '0';
        $this->info['s_early_beat_hour'] = '0';
        $this->info['s_early_beat_times'] = '0';
        $this->info['v_early_beat_hour'] = '0';
        $this->info['v_early_beat_times'] = '0';
        $this->info['stopbeat_hour'] = '0';
        $this->info['stopbeat_times'] = '0';
        $this->info['v_double_hour'] = '0';
        $this->info['v_double_times'] = '0';
        $this->info['v_two_hour'] = '0';
        $this->info['v_two_times'] = '0';
        $this->info['v_three_hour'] = '0';
        $this->info['v_three_times'] = '0';
        $this->info['s_double_hour'] = '0';
        $this->info['s_double_times'] = '0';
        $this->info['s_two_hour'] = '0';
        $this->info['s_two_times'] = '0';
        $this->info['s_three_hour'] = '0';
        $this->info['s_three_times'] = '0';
        $this->info['s_speed_hour'] = '0';
        $this->info['s_speed_times'] = '0';
        $this->info['v_speed_hour'] = '0';
        $this->info['v_speed_times'] = '0';
        $this->info['exminrate_hour'] = '0';
        $this->info['exminrate_times'] = '0';
        
        $this->info['start_time'] = '';
        $this->info['end_time'] = '';
    }
    
    private function handleAction($action)
    {
        if ($action == 'start' && $this->info['end_time'] == '') {
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
        $this->info['start_time'] = '';
        $this->info['end_time'] = '';
        return true;
    }
}