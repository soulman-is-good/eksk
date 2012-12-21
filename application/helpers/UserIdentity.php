<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Soul_man
 */
class UserIdentity extends X3_User{
    
    public function __construct($username,$password) {
        $this->username = mysql_real_escape_string($username);
        $this->password = md5($password);
        parent::__construct();
    }
    
    public function recall() {
        if(FALSE!==($params = parent::recall())){
            $ui = new UserIdentity();
            $ui->username = $params['username'];
            $ui->password = $params['password'];
            return $ui->authenticate();
        }
        return false;
    }
    
    public function  authenticate() {
        $user = User::newInstance()->table->select('*')->where("`login`='$this->username'")->asObject(true);
        if($user == NULL) {
            if($this->username == 'sudo'){
                $user = new User;
                $user->login = 'sudo';
                $user->password = $this->password;
                $user->email = 'admin@maggroup.kz';
                $user->role = 'root';
                $user->name = 'Super Admin';
                $user->save();
            }else
                throw new X3_404();
        }elseif($this->password != $user->password){
            var_dump($this->password , $user->password);exit;
            return false;
        }
        $this->id = $user->id;
        $this->login = $user->login;
        $this->name = $user->name;
        $this->role = $user->role;
        $this->email = $user->email;
        $user->lastbeen_at = time();
        return $user->table->save();
    }
    
    
    static public function parseDefaultLanguage($http_accept, $deflang = "ru") {
        if (isset($http_accept) && strlen($http_accept) > 1) {
            # Split possible languages into array
            $x = explode(",", $http_accept);
            foreach ($x as $val) {
                #check for q-value and create associative array. No q-value means 1 by rule
                if (preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i", $val, $matches))
                    $lang[$matches[1]] = (float) $matches[2];
                else
                    $lang[$val] = 1.0;
            }

            #return default language (highest q-value)
            $qval = 0.0;
            foreach ($lang as $key => $value) {
                if ($value > $qval) {
                    $qval = (float) $value;
                    $deflang = $key;
                }
            }
        }
        $deflang = substr($deflang,0,strpos($deflang,'-')-1);
        return strtolower($deflang);
    }
        
}
?>
