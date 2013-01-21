<?php

class Upload extends X3_Component {

    public $filename=null;
    public $message = null;
    public $model = null;
    public $field = null;
    public $tmp_name = null;
    public $source = false;

    public function __construct($file,$filename=null,$allowed=array(),$max_size = 20971520) {
        if($file instanceof  X3_Module){
            $class = get_class($file);
            $this->model = $model = $file;
            $this->field = $field = $filename;
            if(isset($file->_fields[$field]['allowed'])){
                $allowed = $file->_fields[$field]['allowed'];
            }
            if(isset($file->_fields[$field]['max_size']))
                $max_size = $file->_fields[$field]['max_size'] * 1024;            
            $file = $_FILES[$class];
            if(isset($file))
            foreach ($file as $k=>$f){
                $file[$k] = $f[$field];
            }
            $filename = $class . "-" . time() . rand(100,999);
        }else{
            if(is_string($file))
                $file = isset($_FILES[$file])?$_FILES[$file]:null;
            if($filename == null)
                $filename = pathinfo($file['name'],PATHINFO_FILENAME);
        }
        if (isset($file) && $file['error']==0) {
            $this->tmp_name = $tmp_name = $file['tmp_name'];
            if (is_uploaded_file($tmp_name)) {
                $ext = substr($file['name'],
                                1 + strrpos($file['name'], "."));
                $ext = strtolower($ext);
                if($ext == '') $ext = 'jpg';
                if (isset($max_size) && filesize($tmp_name) > $max_size) {
                    $this->message = 'Error: File size > '.round($max_size/1024).'K.';
                } elseif (!empty($allowed) && !in_array($ext, $allowed)) {
                    $this->message = 'Error: Invalid file type.';
                } else {
                    $this->filename = $filename . "." . $ext;
                }
            }
            else {
                if(isset($_POST[$class][$field . "_source"])){
                    $this->model->table[$this->field] = $_POST[$class][$field . "_source"];
                    $this->source = true;
                }elseif(!in_array('null',$this->model->_fields[$field]))
                    $this->message = "Error: empty file.";
            }
        }else{
            $this->source = true;
            if(!isset($file)){
                if(isset($model) && isset($_POST[$class][$field . "_source"]))
                        $model->table[$this->field] = $_POST[$class][$field . "_source"];
                elseif(isset($model) && ((isset($model->_fields[$field]['default']) && $model->_fields[$field]['default']=='NULL') || in_array('null',$model->_fields[$field]))){
                    $model->table[$this->field] = NULL;
                }else{
                    $this->message = "Нужно прикрепить файл ";
                    if(isset($model))
                        $model->addError($field,$this->message);
                }
            }else{
                switch($file['error']){
                    case UPLOAD_ERR_NO_FILE:
                        if(isset($model) && isset($_POST[$class][$field . "_source"]))
                                $model->table[$this->field] = $_POST[$class][$field . "_source"];
                        elseif(isset($model) && ((isset($model->_fields[$field]['default']) && $model->_fields[$field]['default']=='NULL') || in_array('null',$model->_fields[$field]))){
                            $model->table[$this->field] = NULL;
                        }else
                            $this->message = X3::translate("Нужно прикрепить файл");
                    break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $this->message = X3::translate("Ошибка при записи файла на диск");
                    break;
                    case UPLOAD_ERR_EXTENSION:
                        $this->message = X3::translate("Ошибка при загузке файла (UPLOAD_ERR_EXTENSION)");
                    break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $this->message = X3::translate("Отсутствует временная директория для загрузки фала");
                    break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->message = X3::translate("Превышен максимальный размер загружаемого файла (MAX_FILE_SIZE)");
                    break;
                    case UPLOAD_ERR_PARTIAL:
                        $this->message = X3::translate("Файл был загружен частично.");
                    break;
                    case UPLOAD_ERR_INI_SIZE:
                        $max = ini_get('upload_max_filesize');
                        $this->message = X3::translate("Превышен максимальный размер загружаемого файла ($max)");
                    break;
                    default:
                        $this->message = "Ошибка при загузке файла (UNKNOWN#{$file['error']})";
                    break;
                }
                if(isset($model) && $this->message!=null)
                    $model->addError($field,$this->message);
            }
        }
        return $this;
    }

    public function save() {
        $class="";
        if($this->source) return true;
        if(!is_file($this->tmp_name)) return false;
        if($this->model instanceof  X3_Module){
            $class = get_class($this->model);
            $path = "uploads/$class/";
        }else
            $path = "uploads/";
        if(!is_dir($path))
            @mkdir($path,0777);
        if ($this->message===null && @move_uploaded_file($this->tmp_name, X3::app()->basePath . "/$path" . $this->filename)) {
            $this->message = null;
            if(isset($_POST[$class][$this->field . "_source"]) && is_file($path . $_POST[$class][$this->field . "_source"])){
                unlink($path . $_POST[$class][$this->field . "_source"]);
            }
        } else {
            if($this->message===null)
            $this->message = 'Error: moving file failed.';
        }
        if(isset($this->model,$this->message)){
            $this->model->addError($this->field,$this->message);
            return false;
        }elseif(isset($this->model)){
            $this->model->table[$this->field] = $this->filename;
            return true;
        }elseif(isset($this->message)){
            echo $this->message;
            return false;
        }else {            
            return $path . $this->filename;
        }
    }

    public function saveAs($filename) {
        $this->filename = $filename;
        return $this->save();
    }

}