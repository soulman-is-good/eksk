<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageAction
 *
 * @author maxim
 */
class Resize extends X3_Component {
    public $caching=true;
    public $extension='jpeg';
    public $operation = 2;
    private $gray = false;
    private $noresize = false;
    const FILL = 1;
    const STRETCH = 2;
    const HEIGHT = 3;
    const WIDTH = 4;

    public function  __construct($model) {
        $model = ucfirst($model);
        if(empty($_GET)) {echo 'NO GET parameters';return false;}
        if(!class_exists($model)) {
            $size = $dir = $model;
            $file = key($_GET);
            $folder = 'uploads/';
            $src = 'uploads/'.$file;
            $file="$size-$file";
            if(file_exists('uploads/'.$file)) {
                header('Content-Type: image/jpeg');
                echo file_get_contents('uploads/'.$file);
                exit;
            }
        }else{
            $dir = key($_GET);
            $folder = 'uploads/'.$model.'/'.$dir;
            $size = $dir;
            $file = $_GET[$dir];
            $src = 'uploads/'.$model.'/'.$file;
            $_exfile = 'uploads/' . $model . '/' . $dir . '/' . $file;
            if(file_exists($_exfile)) {
                header('Content-Type: image/png');
                echo file_get_contents($_exfile);
                exit;
            }
        }
        $prefix="";
        $size = explode('x',$size);
        if($size[0]=='gray'){
            array_shift($size);
            $this->gray = true;
        }
        if(sizeof($size)==3) {
            switch ($size[2]) {
                case 'f':
                    $this->operation = self::FILL;
                    break;
                case 's':
                    $this->operation = self::STRETCH;
                    break;
                case 'h':
                    $this->operation = self::HEIGHT;
                    break;
                case 'w':
                    $this->operation = self::WIDTH;
                    break;
                case 'o':
                    $this->operation = self::FILL;
                    $this->caching = false;
                    break;
                default:
                    $this->operation = self::STRETCH;
            }
            //array_pop($size);
        }
        if(!is_file($src)) { //if no src found
            $msg = 'NO source file '.$src . PHP_EOL;
            $file = 'default.jpg';
            $src = 'uploads/'.$model.'/'.$file;
            if(!is_file($src)) {
                echo $msg . 'NO source file '.$src;return false;
            }
        }
        if(!is_numeric($size[0])||!is_numeric($size[1])) {
            if($this->gray)
                $this->noresize = true;
            else{
                echo 'NaN sizes';
                return false;
            }
        }
        X3::import('@app:extensions:image:Image.php');
        $image = new Image($src);
        if($this->gray){
            $image->gray();
            if(!$this->noresize)
                $this->resize($image,$size);
        }else
            $this->resize($image,$size);
        if($this->caching) {
            //var_dump($dir,is_dir($dir));exit;
            if(!is_dir($folder)) {mkdir($folder);chmod($folder, 0777);}
            if(is_writable($folder)) {
                $image->save($folder.'/'.$prefix.$file,0777,TRUE);
                $image->render();
            }else
                $image->render();
        }
         else {
             $image->render(true);
         }
         exit;
    }

    public function resize(&$image,$size) {

        if(sizeof($size)==2){
            if($size[0]<=$image->width && $size[1]<=$image->height){
                if($image->width-$size[0] < $image->height-$size[1])
                    $image->resize($size[0],$size[1],4)->crop($size[0],$size[1],'top');
                else
                    $image->resize($size[0],$size[1],3)->crop($size[0],$size[1],'top');
            }elseif($size[0]>=$image->width && $size[1]>=$image->height){
                if($image->width-$size[0] < $image->height-$size[1])
                    $image->resize($size[0],$size[1],4)->crop($size[0],$size[1],'top');
                else
                    $image->resize($size[0],$size[1],3)->crop($size[0],$size[1],'top');
            }elseif($size[0]>=$image->width && $size[1]<=$image->height){
                $image->resize($size[0],$size[1],4)->crop($size[0],$size[1],'top');
            }elseif($size[0]<=$image->width && $size[1]>=$image->height){
                $image->resize($size[0],$size[1],3)->crop($size[0],$size[1],'top');
            }
        }


        //if($image->height>$size[1])
        //    $image;
        if(sizeof($size)==3){
            switch ($this->operation) {
                case self::STRETCH:
                case self::FILL:
                if($image->height > $image->width){
                    $image->resize($size[0],$size[1],Image::HEIGHT)->crop($size[0],$size[1]);
                }else
                    $image->resize($size[0],$size[1],Image::WIDTH)->crop($size[0],$size[1]);

                break;
                case self::HEIGHT:
                if($size[1]<$image->height)
                    $image->resize($size[0],$size[1],Image::HEIGHT);
                break;
                case self::WIDTH:
                if($size[0]<$image->width)
                    $image->resize($size[0],$size[1],Image::WIDTH);
                break;
                default:
                break;
            }
        }elseif(sizeof($size)==4)
            $image->crop($image->width,$image->height,$size[2],$size[3]);
        return $image;
    }
}
