<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Profile
 *
 * @author Soul_man
 */
class Profile extends X3_Component {

    public $time = 0;
    public $enable = false;
    public $profiles = array();

    public function __construct($params=array()) {
        foreach($params as $param=>$value){
            $this->$param = $value;
        }
        if(NULL!==($traced = X3_Session::readOnce('X3-Profile'))){
            foreach($traced as $key=>$time){
                $this->profiles[$key] = array('start'=>0,'trace'=>false,'end'=>$time);
            }
        }
        $this->addTrigger('onEndApp');
        $this->addTrigger('onRender');
    }

    public function start($name,$leaveTrace = false) {
        $this->profiles[$name] = array('start'=>microtime(true),'trace'=>$leaveTrace);
    }
    /**
     * Alias to 'start' function
     * @param string $name profile name
     */
    public function begin($name,$leaveTrace = false) {
        $this->start($name,$leaveTrace);
    }

    public function end($name) {
        if(!isset($this->profiles[$name])) return false;
        $this->profiles[$name]['end'] = microtime(true);
        return $this->profiles[$name]['end'] - $this->profiles[$name]['start'];
    }

    public function onRender(&$output) {
        if(!$this->enable) return;
        $traced = array();
        $queries = X3::app()->db->query_num;
        $time = sprintf("%.02f",(microtime(true) - $this->time));
        $memory = sprintf("%.2f",memory_get_peak_usage()/1048576);
        $text = "
        <table style=\"width:100%;border:1px solid #a2a2a2;margin-top:20px;border-collapse:collapse;font:10px Arial;color:#565656\">
            <tr><td colspan=\"2\" style=\"background:#a2a2a2;color:#dcdcdc\">Application statistics</td></tr>
            <tr>
                <td>Browser</td>
                <td>{$_SERVER['HTTP_USER_AGENT']}</td>
            </tr>
            <tr>
                <td>DB queries</td>
                <td>$queries</td>
            </tr>
            <tr>
                <td>Application execution time</td>
                <td>$time sec</td>
            </tr>
            <tr>
                <td>Memory usage</td>
                <td>$memory MB</td>
            </tr>";
        if(!empty($this->profiles)){
            $text .= "<tr><td colspan=\"2\" style=\"background:#c2c2c2;color:#dcdcdc\">Custom statistics</td></tr>";
            foreach ($this->profiles as $key => $value) {
                //$time = sprintf("%.02f",($value['end'] - $value['start']));
                $time = ($value['end'] - $value['start']);
                if($value['trace']){
                    $traced[$key] = $time;
                }
                $text .= "<tr>
                <td>$key</td>
                <td>$time sec</td>
                </tr>";
            }
        }
        $text .= "</table>";
        $output = str_replace('</body', $text . '</body', $output);
        X3_Session::writeOnce('X3-Profile',$traced);
    }

}
?>
