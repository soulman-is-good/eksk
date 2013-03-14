<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Notify
 *
 * @author Soul_man
 */
class Notify extends X3_Module_Table{

    public $encoding = 'UTF-8';
    public $tableName = 'data_notify';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'to'=>array('string[255]','default'=>'NULL'),
        'from'=>array('string[255]','default'=>'NULL'),
        'title'=>array('string[255]','language'),
        'name'=>array('string[32]','unique'),
        'text'=>array('text','language'),
        'status'=>array('boolean','default'=>'1'),
        'created_at'=>array('datetime','default'=>'0'),
        'sent_at'=>array('datetime','default'=>'0'),
    );
    
    public function fieldNames() {
        return array(
            'to'=>'Кому',
            'from'=>'От Кого',
            'title'=>'Название',
            'name'=>'ID',
            'text'=>'Содержание',
            'status'=>'Слать',
        );
    }

    public function moduleTitle() {
        return 'Рассылка';
    }
    
    public function execSendclients() {
        $errors = array();
        if(isset($_POST['Company']) && !empty($_POST['Company'])){
            $ids = implode(',',$_POST['Company']);
            $cs = X3::db()->query("SELECT `email_private`,`title` FROM data_company WHERE id IN ($ids)");
            while($c = mysql_fetch_object($cs)){
                $mailer = new X3_Mailer();
                $mailer->encoding = "WINDOWS-1251";
                $from = $mailer->email = 'info@kansha.kz';
                $to = $c->email_private;
                $title = $_POST['title'];
                $letter = $_POST['letter'];
                $this->text = $letter;
                $letter = $this->formLetter(array('company'=>$c->title),$mailer);
                $title = $this->formLetter(array('company'=>$c->title),$mailer,$title);
                try{
                    if(!$mailer->send($to, $title, $letter, $from)){
                        $msg='Возникла неизвесная ошибка!';
                        $status = 'error';
                    }else{
                        $status = 'success';
                        $msg = 'Рассылка успешно проведена!';
                    }
                }catch(Exception $e){
                    $msg = $e->getMessage();
                    $status = 'error';
                }
            }
            X3_Session::writeOnce($status,$msg);
            X3::app()->module->redirect('/admin/notify');
        }
    }
    
    public function sendMessage($messsage,$from=0,$to=0) {
        return true;
    }
    
        
    public static function sendMail($mailName,$data=array(),$to=null,$from=null,$cc=array()) {
        if(NULL===($mail = self::get(array('name'=>$mailName),1)))
            return "Нет письма с именем '$mailName'";
        if($mail->status==0)
            return "Письмо '$mailName' не открыто к рассылке";
        $mailer = new X3_Mailer();
        $mailer->copy = $cc;
        $mailer->email = 'noreply@eksk.kz';
        $mailer->encoding = 'UTF-8';
        if(!isset($data['title']))
            $data['title'] = $mail->title;
        $data['title'] = $mail->formLetter($data,$mailer,$data['title']);
        $message = $mail->formLetter($data,$mailer);
        if(is_null($from) && !empty($mail->from)){
            $from = $mailer->email = $mail->from;
        }
        if(is_null($to) && !empty($mail->to)){
            $rcps = explode(',',$mail->to);
        }elseif(is_null($to))
            $rcps = array('info@eksk.kz');
        else 
            $rcps = explode(',',$to);
        $errs = '';
        foreach($rcps as $to)
            try{
                $to = trim($to);
                $msg = $mailer->send($to, $data['title'], $message,$from);
                $mail->sent_at = time();
                if(is_string($msg))
                    X3::log($msg,'mailer');
                else{
                    $mail->save();
                }
            }catch(Exception $e){
                $errs .= $e->getMessage();
                X3::log($e->getMessage(),'mailer');
            }
        return $errs==''?true:$errs;
    }
    
    public function formLetter($data=array(),$mailer,$text='') {
        if(empty($text))
            $text = $this->text;
        if(empty($data)) return $this->text;
        $ms = array();
        $data = array_extend($data, array(
            'host'=>X3::app()->baseUrl,
            'date'=>I18n::date()
        ));
        foreach($data as $k=>$val){
            if(is_array($val)){
                $m = array();
                $z = strtoupper($k);
                if(preg_match("/\[@$z\]([^@]+)\[$z@\]/", $text,$m)>0){
                    $html = '';
                    foreach($val as $vdata){
                        $tmp = $m[1];
                        foreach($vdata as $kk=>$vas){
                            $j = strtoupper($kk);
                            $tmp = str_replace("[$j]", $vas, $tmp);
                        }
                        $html .= $tmp;
                    }
                    $text = str_replace($m[0], $html, $text);
                }
            }elseif(is_string($val)){
                $j = strtoupper($k);
                $text = str_replace("[$j]", $val, $text);
            }
        }
        X3::import('@helpers:SimpleDom.php');
        $html = str_get_html($text);
        $imgs = $html->find('img');
        foreach($imgs as &$img){
            if(substr($img->src,0,1)=='/'){
                $src = X3::app()->basePath . $img->src;
                if(is_file($src) && 0){
                    $cid = $mailer->addFile($src);
                    $img->src = "cid:$cid";
                }else
                    $img->src = X3::app()->baseUrl . $img->src;
            }
        }
        $as = $html->find('a');
        foreach($as as &$a){
            if(substr($a->href,0,1)=='/'){
                $a->href = X3::app()->baseUrl . $a->href;
            }
        }
        return (string)$html;
    }
    
    public function beforeValidate() {
        if($this->created_at == 0)
            $this->created_at = time();
    }
}

?>
