<?php
/**
 * City
 *
 * @author Soul_man
 */
class City extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_city';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        //'parent_id'=>array('integer[10]','unsigned','index','default'=>'NULL','ref'=>array('Region','id','default'=>'title')),
        'title'=>array('string[255]','language'),
        'weight'=>array('integer','default'=>'0'),
        'status'=>array('boolean','default'=>'1'),
    );

    public static function newInstance($class=__CLASS__) {
        return parent::newInstance($class);
    }
    public static function getInstance($class=__CLASS__) {
        return parent::getInstance($class);
    }
    public static function get($arr,$single=false,$class=__CLASS__,$asArray=false) {
        return parent::get($arr,$single,$class,$asArray);
    }
    public static function getByPk($pk,$class=__CLASS__) {
        return parent::getByPk($pk,$class);
    }
    public function fieldNames() {
        return array(
            //'parent_id'=>'Находится в:',
            'title'=>'Название',
            'weight'=>'Порядок',
            'status'=>'Видимость',
        );
    }
    
    public function moduleTitle() {
        return 'Города';
    }

    public function cache() {
        return array(
            //'cache'=>array('actions'=>'show','role'=>'*','expire'=>'+1 day'),
            'nocache'=>array('actions'=>'*','role'=>'admin')
        );
    }
    
    public function actionRegion() {
        if(IS_AJAX && isset($_GET['id']) && ($id = (int)$_GET['id'])>0){
            $query = array(
                    '@condition'=>array('city_region.city_id'=>$id),
                    '@order'=>'city_region.weight'
                );
            if(X3::user()->isKsk() && !X3::user()->superAdmin){
                $query['@join'] = "INNER JOIN user_address a ON a.region_id=city_region.id";
                $query['@condition']['a.user_id'] = X3::user()->id;
                $query['@group'] = "city_region.id";
            }
            $regions = City_Region::get($query,0,'City_Region',1);
            $result = array();
            foreach($regions as $reg){
                if(X3::user()->isKsk() && !X3::user()->superAdmin){
                    $f = "SELECT DISTINCT house FROM user_address WHERE region_id={$reg['id']} AND user_id=".X3::user()->id." AND status=1";
                }else
                    $f = "SELECT DISTINCT house FROM user_address WHERE region_id={$reg['id']}";
                $houses = array();
                $q = X3::db()->query($f);
                if(is_resource($q))
                while($a = mysql_fetch_assoc($q)){
                    $houses[] = $a['house'];
                }
                $result[] = array('id'=>$reg['id'],'title'=>$reg['title'],'houses'=>$houses);
            }
            echo json_encode($result);
            exit;
        }
        throw new X3_404();
    }
    
    public function actionImport(){
        if(!X3::user()->isAdmin() || !isset($_POST['city_id']) || null == ($city = City::getByPk((int)$_POST['city_id'])))
            throw new X3_404();
        $u = new Upload('excel',null,array('xls','xlsx'));
        if($u->message != false){
            die(json_encode(array('status'=>'ERROR','message'=>'Ошибка загрузки файла: '.$u->message)));
        }
        require_once(X3::app()->basePath . "/application/extensions/PHPExcel.php");
        $validLocale = PHPExcel_Settings::setLocale('ru');
        //$sheet = new PHPExcel_Worksheet;
        $objReader = PHPExcel_IOFactory::createReaderForFile($u->tmp_name)->load($u->tmp_name);
        $sheet = $objReader->getActiveSheet();
        $rezult = 0;
        $total = 0;
        $msg = '';
        
        foreach($sheet->getRowIterator() as $row){
            $y = $row->getRowIndex();
            $street = $sheet->getCellByColumnAndRow(0, $y)->getValue();
            if($street!=''){
                $total++;
                $res = X3::db()->fetch("SELECT city_id FROM city_region WHERE title LIKE '$street'");
                if(!$res || $res['city_id']!=$city->id){
                    $s = new City_Region();
                    $s->city_id = $city->id;
                    $s->title = $street;
                    $s->weight = $y;
                    if($s->save())
                        $rezult++;
                    else{
                        $msg .= "#row$y: ".X3_Html::errorSummary($s).' '.X3::db()->getErrors()."<br>";
                    }
                }elseif(!$res)
                    $msg .= "#row$y: ".X3::db()->getErrors()."<br>";
            }
        }
        @unlink($u->tmp_name);
        die(json_encode(array('status'=>'OK','message'=>"Всего улиц: $total<br>Новых улиц добавлено:$rezult".($msg!=''?"<br>Ошибки при выполенении:<p style='color:#822'>$msg<\/p>":''))));
    }
        
    public function getDefaultScope() {
        return array('@order'=>'weight');
    }

    public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
            City_Region::delete(array('city_id'=>$model->id));
        }
        parent::onDelete($tables, $condition);
    }
    
    public function beforeValidate() {
        //if($this->name == '')
            //$this->name = $this->title;
        //$name = new X3_String($this->name);
        //$this->name = strtolower($name->translit(0,"'"));
        //if(empty($this->parent_id) || $this->parent_id=="0") $this->parent_id = NULL;
    }

}
?>
