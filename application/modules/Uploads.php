<?php
/**
 * Description of Uploads
 *
 * @author Soul_man
 */

/**
 * @property string $id primary key
 * @property integer $created_at unix timestamp
 */
class Uploads extends X3_Module_Table {

    public $encoding = 'UTF-8';

    public $tableName = 'data_uploads';

    public $_fields = array(
      'id'=>array('string[128]','primary'),
      'name'=>array('string[256]','default'=>'NULL'),
      'created_at'=>array('integer[10]','unsigned','default'=>'0')
    );
    public static function newInstance($class = __CLASS__) {
        return parent::newInstance($class);
    }

    public static function getInstance($class = __CLASS__) {
        return parent::getInstance($class);
    }
    
    public function beforeAction(&$action) {
        if ($this->controller->action == 'captcha' || $this->controller->action == 'get'  || $this->controller->action == 'excel')
            return true;
        if(!X3_DEBUG && !X3::user()->isAdmin())
            throw new X3_404();
        $act = $action;
        //$act = str_replace('action', '', $act);
        $resize = new Resize($act);
        exit;
        return false;
    }

    public function actionCaptcha() {
        header('Content-type: image/gif');
        if (!isset($_GET['F5']) && is_array(X3::app()->user->captcha) && X3::app()->user->captcha['times'] > 0 && is_file('uploads/' . X3::app()->user->captcha['file']) && !isset($_GET['f5'])) {
            $arr = X3::app()->user->captcha;
            if (time() - $arr['lasttime'] > 3) {
                $arr['times'] = $arr['times'] - 1;
                $arr['lasttime'] = time();
            }
            X3::app()->user->captcha = $arr;
            echo file_get_contents('uploads/' . X3::app()->user->captcha['file']);
            exit;
        } elseif (is_array(X3::app()->user->captcha) && is_file('uploads/' . X3::app()->user->captcha['file'])) {
            @unlink('uploads/' . X3::app()->user->captcha['file']);
        }
        if(!$this->cleanCaptcha(time()-86400)){
            X3::log('Error reading uploads/ dir. Just wanted to clean up captchas');
        }
        $name = 'Captcha_' . time() . rand(0, 10) . rand(100, 10000) . '.gif';
        $text = substr(str_shuffle(str_repeat('0123456789ABCDEFGHKLMPRSTXYZ', 5)), 0, rand(3, 6));
        X3::app()->user->captcha = array('text' => md5(strtolower($text)), 'file' => $name, 'times' => 3, 'lasttime' => time());
        $cap = new Gifcaptcha($text, 'css/ALoveofThunder.ttf', 'ffffff');
        $gif = $cap->AnimatedOut();
        file_put_contents('uploads/' . $name, $gif);
        echo $gif;
        exit;
    }
    
    public function actionGet() {
        $ins = $_GET['file'];
        $model = self::getByPk($ins);
        if($model == null) 
            throw new X3_404();
        $file = X3::app()->basePath . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $model->id;
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$model->name);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
        throw new X3_404();
    }
    
    public function cleanCaptcha($time) {
        $h = @opendir('uploads/');
        if(!$h) return false;
        while($file = @readdir($h)){
            if($file!='.' && $file!='..' && strpos($file,'Captcha_')===0){
                $ctime = filemtime("uploads/$file");
                if($ctime<=$time){
                    @unlink("uploads/$file");
                }
            }
        }
        return true;
    }
    
    public static function cleanUp($model,$file) {
        if(is_object($model))
            $class = get_class ($model);
        elseif(is_string($model))
            $class = $model;
        else
            return false;
        $dir = "uploads/$class";
        $h = opendir($dir);
        while(FALSE!==($fs = readdir($h))){
            if($fs!='.' && $fs!='..' && is_dir("$dir/$fs")){
                $dir2 = "$dir/$fs";
                $h2 = opendir($dir2);
                while(FALSE!==($fs2 = readdir($h2))){
                    if($fs2 == $file && is_file("$dir2/$fs2"))
                        @unlink("$dir2/$fs2");
                }
                closedir($h2);
            }
            if($fs == $file)
                @unlink("$dir/$fs");
        }
        closedir($h);
    }

    public function actionExcel() {
        
function testFormula($sheet,$cell) {
    $formulaValue = $sheet->getCell($cell)->getValue();
    echo 'Formula Value is' , $formulaValue , PHP_EOL;
    $expectedValue = $sheet->getCell($cell)->getOldCalculatedValue();
    echo 'Expected Value is '  , ((!is_null($expectedValue)) ? $expectedValue : 'UNKNOWN') , PHP_EOL;


     PHPExcel_Calculation::getInstance()->writeDebugLog = true;
    $calculate = false;
    try {
        $tokens = PHPExcel_Calculation::getInstance()->parseFormula($formulaValue,$sheet->getCell($cell));
        echo 'Parser Stack :-' , PHP_EOL;
        print_r($tokens);
        echo PHP_EOL;
        $calculate = true;
    } catch (Exception $e) {
        echo 'PARSER ERROR: ' , $e->getMessage() , PHP_EOL;

        echo 'Parser Stack :-' , PHP_EOL;
        print_r($tokens);
        echo PHP_EOL;
    }

    if ($calculate) {
        try {
            $cellValue = $sheet->getCell($cell)->getCalculatedValue();
            echo 'Calculated Value is ' , $cellValue , PHP_EOL;

            echo 'Evaluation Log:' , PHP_EOL;
            print_r(PHPExcel_Calculation::getInstance()->debugLog);
            echo PHP_EOL;
        } catch (Exception $e) {
            echo 'CALCULATION ENGINE ERROR: ' , $e->getMessage() , PHP_EOL;

            echo 'Evaluation Log:' , PHP_EOL;
            print_r(PHPExcel_Calculation::getInstance()->debugLog);
            echo PHP_EOL;
        }
    }
}
        if(!X3::user()->isAdmin() || !isset($_GET['generate']))
            throw new X3_404();
        $type = strtok($_GET['generate'],'.');
        require_once(X3::app()->basePath . "/application/extensions/PHPExcel.php");
        $phpExcel = new PHPExcel();
        $phpExcel->setActiveSheetIndex(0);
        $sheet = $phpExcel->getActiveSheet();
        $phpExcel->getProperties()->setCreator("eksk.kz")
                ->setLastModifiedBy("eksk.kz")
                ->setTitle("Аналитические данные")
                ->setSubject("Аналитические данные");
        $sheet->setTitle(($type=='user'?'Пользователи':($type=='ksk'?'КСК':'Опросы')));
        $abs = range('A', 'Z');
        $aba = array_map(create_function('$item','return "A$item";'),$abs);
        $abb = array_map(create_function('$item','return "B$item";'),$abs);
        $abc = array_map(create_function('$item','return "C$item";'),$abs);
        $abd = array_map(create_function('$item','return "D$item";'),$abs);
        $abs = array_merge($abs,$aba,$abb,$abc,$abd);
        if($type == 'user' || $type == 'ksk') {
            $models = X3::db()->query("SELECT u.id, u.name, u.surname, u.kskname, u.ksksurname, u.duty, u.gender, u.date_of_birth, u.created_at, us.about, us.home, us.work, us.mobile, us.email, us.skype, us.site
                FROM data_user u LEFT JOIN user_settings us ON us.user_id=u.id WHERE role='$type'");
            $j=0;
            $sheet->setCellValue("{$abs[$j++]}1", 'ID');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            if($type == 'ksk'){
                $sheet->setCellValue("{$abs[$j++]}1", 'Название');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
                $sheet->setCellValue("{$abs[$j++]}1", 'Должность');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
                $sheet->setCellValue("{$abs[$j++]}1", 'Рейтинг, %');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            }
            $sheet->setCellValue("{$abs[$j++]}1", 'Фамилия');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Имя');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Пол');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Дата рождения');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Дата регистрации');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'О себе');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Телефон');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Рабочий');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Мобильный');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'E-mail');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Skype');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Веб-сайт');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->getStyle("A1:{$abs[$j]}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
            $k=2;
            $maxaddr = 0;
            while($u = mysql_fetch_assoc($models)){
                $j=0;                
                $sheet->getRowDimension($k)->setRowHeight(-1);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['id']);
                if($type=='ksk'){
                    $rank = X3::db()->fetch("SELECT SUM(`rank`) `sum`,COUNT(`id`) AS `cnt` FROM `user_rank` WHERE user_ksk={$u['id']}");
                    $sheet->setCellValue("{$abs[$j++]}$k", $u['name']);
                    $sheet->setCellValue("{$abs[$j++]}$k", $u['duty']);
                    if($rank['cnt']>0)
                        $sheet->setCellValueExplicit("{$abs[$j++]}$k", "={$rank['sum']}/{$rank['cnt']}", PHPExcel_Cell_DataType::TYPE_FORMULA);
                    else
                        $sheet->setCellValue("{$abs[$j++]}$k", "0");
                    //testFormula($sheet, "{$abs[$j-1]}$k");die;
                    $sheet->setCellValue("{$abs[$j++]}$k", $u['ksksurname']);
                    $sheet->setCellValue("{$abs[$j++]}$k", $u['kskname']);
                }else {
                    $sheet->setCellValue("{$abs[$j++]}$k", $u['surname']);
                    $sheet->setCellValue("{$abs[$j++]}$k", $u['name']);
                }
                $sheet->setCellValue("{$abs[$j++]}$k", $u['gender']);
                $sheet->setCellValue("{$abs[$j++]}$k", date("d.m.Y",$u['date_of_birth']));
                $sheet->setCellValue("{$abs[$j++]}$k", date("d.m.Y H:i:s",$u['created_at']));
                $sheet->setCellValue("{$abs[$j++]}$k", $u['about']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['home']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['work']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['mobile']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['email']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['skype']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['site']);
                $aq = X3::db()->query("SELECT c.title AS city, cr.title AS region, ua.house, ua.flat, ua.status  FROM user_address ua INNER JOIN data_city c ON c.id=ua.city_id INNER JOIN city_region cr ON cr.id=ua.region_id WHERE user_id={$u['id']} ORDER BY ua.status, ua.id ASC");
                $x = 0;
                if(is_resource($aq)){
                    while($addr = mysql_fetch_assoc($aq)){
                      //
                        if($maxaddr<$x+1){
                            if($type=='ksk' && $x==0)
                                $sheet->setCellValue("{$abs[$j]}1", 'Адрес офиса');
                            elseif($type == 'ksk')
                                $sheet->setCellValue("{$abs[$j]}1", 'Адрес '.$x);
                            else
                                $sheet->setCellValue("{$abs[$j]}1", 'Адрес '.($x+1));
                            $sheet->getStyle("{$abs[$j]}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
                            $sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
                            $maxaddr = $x+1;
                        }
                      //
                        $sheet->getStyle("{$abs[$j]}$k")->getAlignment()->setWrapText(true);
                        if($type=='ksk'){
                            if($addr['status']==0)
                                $sheet->setCellValue("{$abs[$j++]}$k", $addr['city'] . ", " . $addr['region'] . ", ".$addr['house'].", офис ".$addr['flat']);
                            else
                                $sheet->setCellValue("{$abs[$j++]}$k", $addr['city'] . ", " . $addr['region'] . ", дом ".$addr['house']);
                        }else
                            $sheet->setCellValue("{$abs[$j++]}$k", $addr['city'] . ", " . $addr['region'] . ", ".$addr['house'].", квартира ".$addr['flat']);
                        $x++;
                    }
                }
                $k++;
            }
        }
        if($type == 'vote'){
            $models = X3::db()->query("SELECT v.id, v.user_id, v.title, v.status, v.created_at, v.end_at, v.answer, u.name, u.kskname, u.surname, u.ksksurname FROM data_vote v INNER JOIN data_user u ON u.id=v.user_id");
            $j=0;
            $sheet->setCellValue("{$abs[$j++]}1", 'ID');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'USER_ID');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Имя');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Заголовок');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Дата создания');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Дата окончания');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Ответы');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->getStyle("A1:{$abs[$i+$j]}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
            $sheet->getStyle("A1:{$abs[$i+$j]}".(mysql_num_rows($models)+1))->getAlignment()->setWrapText(true);
            $k=2;
            while($u = mysql_fetch_assoc($models)){
                $j=0;
                $user = new User();
                $user->acquire(array('name'=>$u['name'],'surname'=>$u['surname'],'kskname'=>$u['kskname'],'ksksurname'=>$u['ksksurname'],'id'=>$u['user_id']));
                $sheet->setCellValue("{$abs[$j++]}$k", $u['id']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['user_id']);
                $sheet->setCellValue("{$abs[$j++]}$k", $user->fullname);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['title']);
                $sheet->setCellValue("{$abs[$j++]}$k", date("d.m.Y H:i",$u['created_at']));
                $sheet->setCellValue("{$abs[$j++]}$k", date("d.m.Y H:i",$u['end_at']));
                $answers = explode('||',$u['answer']);
                foreach($answers as $x=>$answer){
                    $cnt=X3::db()->fetch("SELECT COUNT(0) cnt FROM vote_stat WHERE vote_id='{$u['id']}' AND answer='$x'");
                    $sheet->getStyle("{$abs[$j]}$k")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFACCCC');
                    $sheet->getStyle("{$abs[$j]}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
                    $sheet->setCellValue("{$abs[$j]}1", "Ответ ".($x+1));
                    $sheet->setCellValue("{$abs[$j++]}$k", $answer);
                    $sheet->getStyle("{$abs[$j]}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
                    $sheet->setCellValue("{$abs[$j]}1", "Кол-во ".($x+1));
                    $sheet->setCellValue("{$abs[$j++]}$k", (int)$cnt['cnt']);
                }
                $k++;
            }
        }elseif(preg_match("/^vote([0-9]+)$/",$type,$m)>0 && NULL!==($model=Vote::getByPk((int)$m[1]))){
            $answers = explode('||',$model->answer);
            $sheet->mergeCells("A1:{$abs[count($answers)-1]}1");
            $sheet->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFC9C9C');
            $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('A1',$model->title);
            $sheet->setCellValue('A5','Имя');
            $sheet->setCellValue('B5','Ответ');
            $sheet->getStyle("A5:B5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
            $j=0;
            foreach($answers as $x=>$answer){
                $cnt=X3::db()->fetch("SELECT COUNT(0) cnt FROM vote_stat WHERE vote_id='{$model->id}' AND answer='$x'");
                $sheet->getStyle("{$abs[$j]}2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
                $sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
                $sheet->setCellValue("{$abs[$j]}2", $answer);
                $sheet->setCellValue("{$abs[$j++]}3", (int)$cnt['cnt']);
            }
            $q = X3::db()->query("SELECT u.id, u.name, u.kskname, u.surname, u.ksksurname, u.image, v.answer FROM vote_stat v INNER JOIN user_address vv ON vv.id=v.address_id INNER JOIN data_user u ON u.id=vv.user_id WHERE vote_id='$model->id' GROUP BY u.id");
            $k = 6;
            while($u = mysql_fetch_assoc($q)){
                $user = new User();
                $user->acquire($u);
                $sheet->setCellValue("A$k", $user->fullname);
                $sheet->setCellValue("B$k", $answers[$u['answer']]);
                $sheet->getColumnDimension("A$k")->setAutoSize(true);
                $sheet->getColumnDimension("B$k")->setAutoSize(true);
                $k++;
            }
                                
        }elseif($type == 'sms'){
            $models = X3::db()->query("SELECT v.id, v.phone, v.text, v.status, v.created_at, v.sent_at FROM sms_stack v");
            $j=0;
            $sheet->setCellValue("{$abs[$j++]}1", 'ID');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Телефон');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Сообщение');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Добавлено');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Обработано');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->setCellValue("{$abs[$j++]}1", 'Статус');$sheet->getColumnDimension("{$abs[$j]}")->setAutoSize(true);
            $sheet->getStyle("A1:{$abs[$i+$j]}1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
            $sheet->getStyle("A1:{$abs[$i+$j]}".(mysql_num_rows($models)+1))->getAlignment()->setWrapText(true);
            $k=2;
            while($u = mysql_fetch_assoc($models)){
                $j=0;
                $status = (isset(Sms::$errorCodes[$u['status']])?Sms::$errorCodes[$u['status']]:'Неизвестный код #'.$u['status']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['id']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['phone']);
                $sheet->setCellValue("{$abs[$j++]}$k", $u['text']);
                $sheet->setCellValue("{$abs[$j++]}$k", date("d.m.Y H:i",$u['created_at']));
                $sheet->setCellValue("{$abs[$j++]}$k", date("d.m.Y H:i",$u['sent_at']));
                $sheet->setCellValue("{$abs[$j++]}$k", $status);
                $k++;
            }            
        }else
            throw new X3_404();
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename=',$type.'_'.date('d_m_Y').'.xls');
        header("Cache-Control: max-age=0");
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel,'Excel5');
        $objWriter->save('php://output');
//        $objWriter->save('uploads/ksk.xls');
//        header('Location: /uploads/ksk.xls');
        exit;
    }
}

?>
