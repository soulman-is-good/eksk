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
        if(trim($this->username) == '' || md5('') == $this->password)
            return X3::translate(X3::translate('Логин и пароль не могут быть пусты'));
        $phone = preg_replace("/^\+7/","",trim($this->username));
        $phone = trim(preg_replace("/[\(\) ]/","",trim($phone)));
        $phone = array(substr($phone, 0,3),substr($phone, 3,3),substr($phone, 6,2),substr($phone, 8,2));
        if(preg_match("/^[0-9]{3} [0-9]{3}.{0,1}[0-9]{2}.{0,1}[0-9]{2}$/",implode(' ',$phone))!=false){
            $user = User::newInstance()->table->select('*')->where("`phone`<>'' AND `phone` REGEXP '{$phone[0]} {$phone[1]}.{0,1}{$phone[2]}.{0,1}{$phone[3]}'")->asObject(true);
        }else{
            $user = User::newInstance()->table->select('*')->where("`email`='$this->username'")->asObject(true);
        }
        if($user == null){
            return X3::translate(X3::translate('Введен неверный логин'));
        }
        if($this->password != $user->password){
            return X3::translate(X3::translate('Пароль не подходит к этому логину'));
        }
        if($user->status == 2)
            return X3::translate(X3::translate('Ваш аккаунт заблокирован'));
        if($user->status == 0)
            return X3::translate(X3::translate('Ваш аккаунт не активирован'));
        $this->id = $user->id;
        $this->fullname = $user->name." ".$user->surname;
        if($user->role == 'root'){
            $this->superAdmin = true;
        }
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
