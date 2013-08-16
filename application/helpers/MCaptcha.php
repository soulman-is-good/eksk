<?php

class MCaptcha extends X3_Component {

    const TYPE_LESS = 0;
    const TYPE_MORE = 1;
    
    const SUCCESS = 0;
    const ERROR_WRONG_KEY = -1;
    const ERROR_WRONG_NUMBER = -2;
    const ERROR_OLD_SESSION = -3;

    private $key = 'X3.MCaptcha.';
    public $fontFile = "css/verdana.ttf";
    public $backgroundFile = "images/captcha.png";

    public function getCaptcha() {
        return X3_Session::read($this->key . 'captcha');
    }

    public function setCaptcha($captcha) {
        X3_Session::write($this->key . 'captcha', $captcha);
    }

    public function getKey1() {
        $captcha = $this->getCaptcha();
        list($key1, $key2) = $captcha['keys'];
        return $key1;
    }

    public function getKey2() {
        $captcha = $this->getCaptcha();
        list($key1, $key2) = $captcha['keys'];
        return $key2;
    }
    
    public function getNumber1() {
        $captcha = $this->getCaptcha();
        list($key1, $key2) = $captcha['keys'];
        return $captcha[$key1];
    }

    public function getNumber2() {
        $captcha = $this->getCaptcha();
        list($key1, $key2) = $captcha['keys'];
        return $captcha[$key2];
    }
    
    public function getType() {
        $captcha = $this->getCaptcha();
        return $captcha['type'];
    }
    
    public function getLabel() {
        $ctype = array(X3::translate('наименшее'), X3::translate('наибольшее'));
        return strtr(X3::translate('Выберите {some} число'),array('{some}'=>$ctype[$this->getType()]));
    }
    
    public function checkByKey($key) {
        $captcha = $this->getCaptcha();
        if ($captcha == null)
            return ERROR_OLD_SESSION;
        list($key1, $key2) = $captcha['keys'];
        if(!isset($captcha[$key]))
            return self::ERROR_WRONG_KEY;
        if($captcha['type'] == self::TYPE_LESS) {
            if(($captcha[$key1] < $captcha[$key2] && $key === $key1) || ($captcha[$key1] > $captcha[$key2] && $key === $key2))
                return self::SUCCESS;
        } else { //self::TYPE_MORE
            if(($captcha[$key1] > $captcha[$key2] && $key === $key1) || ($captcha[$key1] < $captcha[$key2] && $key === $key2))
                return self::SUCCESS;
        }
        return self::ERROR_WRONG_NUMBER;
    }

    public function regenerateCaptcha() {
        $captcha = array();
        $captcha['counter'] = 3;
        $captcha['type'] = rand(0, 1);
        $key1 = md5(rand(100, 999) . rand(100, 999) . rand(100, 999));
        while ($key1 === ($key2 = md5(rand(100, 999) . rand(100, 999) . rand(100, 999))));
        $captcha['keys'] = array($key1, $key2);
        $captcha[$key1] = rand(100, 999);
        while ($captcha[$key1] === ($captcha[$key2] = rand(100, 999)));
        $this->setCaptcha($captcha);
        return $captcha;
    }

    public function renderNumber($n = 0) {
        $texrstr = 0;
        if ($n == 0)
            $textstr = $this->getNumber1();
        else
            $textstr = $this->getNumber2();
        // output header
        header("Content-Type: image/gif");

        header("Expires: Mon, 21 Jul 2010 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // output error image
        $this->produceCaptchaImage($textstr);
    }

    private function produceCaptchaImage($text) {
        // constant values
        $backgroundSizeX = 178;
        $backgroundSizeY = 28;
        $sizeX = 100;
        $sizeY = 50;
        $fontFile = $this->fontFile;
        $textLength = strlen($text);

        // generate random security values
        $backgroundOffsetX = 0;//rand(0, $backgroundSizeX - $sizeX - 1);
        $backgroundOffsetY = 0;//rand(0, $backgroundSizeY - $sizeY - 1);
        $angle = rand(-5, 5);
        $fontColorR = rand(0, 127);
        $fontColorG = rand(0, 127);
        $fontColorB = rand(0, 127);

        $fontSize = rand(14, 24);
        $textX = rand(0, (int) ($sizeX - 0.9 * $textLength * $fontSize)); // these coefficients are empiric
        $textY = rand((int) (1.25 * $fontSize), (int) ($sizeY - 0.2 * $fontSize)); // don't try to learn how they were taken out

        // create image with background
        $src_im = imagecreatefrompng($this->backgroundFile);
        if (function_exists('imagecreatetruecolor')) {
            // this is more qualitative function, but it doesn't exist in old GD
            $dst_im = imagecreatetruecolor($sizeX, $sizeY);
            $resizeResult = imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $sizeX, $sizeY, $backgroundSizeX, $backgroundSizeY);
        } else {
            // this is for old GD versions
            $dst_im = imagecreate($sizeX, $sizeY);
            $resizeResult = imagecopyresized($dst_im, $src_im, 0, 0, $backgroundOffsetX, $backgroundOffsetY, $sizeX, $sizeY, $sizeX, $sizeY);
        }


        // write text on image
        if (!function_exists('imagettftext'))
            return IMAGE_ERROR_GD_TTF_NOT_SUPPORTED;
        $color = imagecolorallocate($dst_im, $fontColorR, $fontColorG, $fontColorB);
        imagettftext($dst_im, $fontSize, -$angle, $textX, $textY, $color, $fontFile, $text);

        // output header
        header("Content-Type: image/png");

        // output image
        imagepng($dst_im);

        // free memory
        imagedestroy($src_im);
        imagedestroy($dst_im);
    }
}
?>
