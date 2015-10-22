<?php
class Invigilator
{
    private $info = array();
    private $allowCommands = array(
            'action', 'card', 'all_time', 'check_info',
            'mode1_polycardia', 'mode1_bradycardia', 'mode1_lead',
            'mode2_record_time', 'mode2_polycardia', 'mode2_bradycardia',
            'mode2_regular_time', 'mode2_premature_beat', 'mode2_arrhythmia',
            'mode2_pacemaker', 'mode2_lead',
            'mode3_polycardia', 'mode3_bradycardia', 'mode3_lead', 'mode3_record_time');
    private $commands = array();
    private $file = '';
    
    public function __construct($patientId, $mode = '0')
    {
        $this->file = PATH_CACHE_CMD . $patientId . '.php';
        if (file_exists($this->file)) {
            include $this->file;
            if ($info['status'] == 2) {
                unlink($this->file);
                $this->setDefaultInfo($mode);
            } else {
                $this->info = $info;
                $this->commands = $command;
            }
        } else {
            $this->setDefaultInfo($mode);            
        }
    }
    
    public function clearCommand()
    {
        $this->commands = array();
        $this->create(array());
    }
    
    public function getCommand()
    {
        if (!empty($this->info['end_time'])) {
            if (time() >= $this->info['end_time']) {
                $this->commands['action'] = 'end';
            }
        }
        return $this->commands;
    }
    
    public function create(array $data)
    {
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
            $this->setStatuAndEndTime($data['action']);
        }
        
        $this->clearInfoNotNeed();
        
        $template = $this->getTemplateInfo() . $this->getTemplateCmd();
        
        $handle = fopen($this->file, 'w');
        fwrite($handle, $template);
        fclose($handle);
    }
    
    private function setDefaultInfo($mode)
    {
        $this->info['mode'] = '0';
        $this->info['status'] = ($mode == '3') ? '3' : '0';
        $this->info['card'] = 'master';
        $this->info['all_time'] = 24;
        $this->info['check_info'] = 'off';
        $this->info['mode1_polycardia'] = 120;
        $this->info['mode1_bradycardia'] = 50;
        $this->info['mode1_lead'] = 'V5';
        $this->info['mode2_record_time'] = 20;
        $this->info['mode2_polycardia'] = 120;
        $this->info['mode2_bradycardia'] = 50;
        $this->info['mode2_regular_time'] = 1;
        $this->info['mode2_premature_beat'] = 8;
        $this->info['mode2_arrhythmia'] = 'on';
        $this->info['mode2_pacemaker'] = 'on';
        $this->info['mode2_lead'] = 'V5';
        $this->info['mode3_polycardia'] = 120;
        $this->info['mode3_bradycardia'] = 50;
        $this->info['mode3_lead'] = 'V5';
        $this->info['mode3_record_time'] = 20;
        $this->info['start_time'] = '';
        $this->info['end_time'] = '';
    }
    
    private function setStatuAndEndTime($action)
    {
        if ($this->info['mode'] != 1 && $this->info['mode'] != 2) {
            return;
        }
        if ($action == 'start' && $this->info['end_time'] == '') {
            $this->info['status'] = 1;
            $this->info['start_time'] = time();
            $this->info['end_time'] = $this->info['start_time']
                + $this->info['all_time'] * 3600;
        }
        if ($action == 'end') {
            $this->info['status'] = 2;
            $this->info['start_time'] = '';
            $this->info['end_time'] = '';
        }
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
}