<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Seo
 *
 * @author Soul_man
 */
class SeoHelper extends X3_Component {

    public static $deny = array('Admin', 'SysSettings', 'Admin_Admin');
    public static $allow = array('Page', 'News', 'Video');
    private static $_meta = array();
    private static $isset = false;
    public static $labels = array(
        'Catalog' => 'Каталог',
        'Page' => 'Текстовые',
        'Site' => 'Главная',
        'Place' => 'Филиалы',
        'Faq' => 'Вопросы-ответы',
        'Shop' => 'Магазины',
        'Contacts' => 'Контакты',
        'Clients' => 'Клиенты',
        'News' => 'Новости',
        'Scheme' => 'Cхемы',
        'Work' => 'Работы',
        'Section' => 'Услуги',
        'Map' => 'Карта сайта',
    );

    public function __construct() {
        $aMatch = array();
        X3::app()->browser = array(false,false);
        if (preg_match("/Opera.*?Version\/(\d+(:?\.\d+)?)/", @$_SERVER["HTTP_USER_AGENT"], $aMatch)) {
            X3::app()->browser[0] = 'opera';
            if (@$aMatch[1]) {
                X3::app()->browser[1] = $aMatch[1];
            }
        } elseif (preg_match("/MSIE\W*(\d+(:?\.\d+)?)/", @$_SERVER["HTTP_USER_AGENT"], $aMatch)) {
            X3::app()->browser = 'ie';
            if (@$aMatch[1]) {
                X3::app()->browser[1] = $aMatch[1];
            }
        } elseif (preg_match("/Chrome\W*(\d+(:?\.\d+)?)/", @$_SERVER["HTTP_USER_AGENT"], $aMatch)) {
            X3::app()->browser[0] = 'chrome';
            if (@$aMatch[1]) {
                X3::app()->browser[1] = $aMatch[1];
            }
        } elseif (preg_match("/Safari\W*(\d+(:?\.\d+)?)/", @$_SERVER["HTTP_USER_AGENT"], $aMatch)) {
            X3::app()->browser[0] = 'safari';
            if (@$aMatch[1]) {
                X3::app()->browser[1] = $aMatch[1];
            }
        } elseif (preg_match("/Firefox\W*(\d+(:?\.\d+)?)/", @$_SERVER["HTTP_USER_AGENT"], $aMatch)) {
            X3::app()->browser[0] = 'firefox';
            if (@$aMatch[1]) {
                X3::app()->browser[1] = $aMatch[1];
            }
        }        
        $this->addTrigger('onRender');
    }

    public static function setMeta($title = '', $keywords = '', $description = '') {
        if($title instanceOf X3_Module){
            $module = get_class($title);
            $keywords = $title->metakeywords;
            $description = $title->metadescription;
            $title = $title->metatitle;
        }else
            $module = ucfirst(X3::app()->module->controller->id);
        if (in_array($module, self::$deny))
            return false;
        self::$_meta['title'] = ($title != '') ? $title : SysSettings::getValue("SeoTitle$module");
        self::$_meta['keywords'] = ($keywords != '') ? $keywords : SysSettings::getValue("SeoKeywords$module");
        self::$_meta['description'] = ($description != '') ? $description : SysSettings::getValue("SeoDescription$module");
        self::$isset = true;
    }

    public function onRender($output) {
        $emails = array();
        if (preg_match_all('/"mailto:([^"]+?)"/', $output, $emails) > 0) {
            foreach ($emails[0] as $key => $value) {
                $output = str_replace($value, '"#' . base64_encode($emails[1][$key]) . '" email ', $output);
            }
        }
        $sBrowserClass = X3::app()->browser[0];
        if(X3::app()->browser[1]){
            $ver = explode('.',X3::app()->browser[1]);
            if(!empty($ver))
                $sBrowserClass .= " ".X3::app()->browser[0].array_shift($ver);
            $sBrowserClass .= " ".X3::app()->browser[0].X3::app()->browser[1];
        }
        if(!empty($sBrowserClass))
            $output = str_replace("<html","<html class=\"$sBrowserClass\"",$output);
        if (!self::$isset)
            return $output;
        self::$_meta['title'] = addslashes(self::$_meta['title']);
        self::$_meta['keywords'] = addslashes(self::$_meta['keywords']);
        self::$_meta['description'] = addslashes(self::$_meta['description']);
        $m = array();
        if (preg_match("/<title>.+?<\/title>/", $output, $m) > 0)
            $output = str_replace($m[0], "", $output);
        $m = array();
        if (preg_match("/<meta.+?name=\"description\".+?>/", $output, $m) > 0)
            $output = str_replace($m[0], "", $output);
        $m = array();
        if (preg_match("/<meta.+?name=\"keywords\".+?>/", $output, $m) > 0)
            $output = str_replace($m[0], "", $output);
        $output = str_replace("<head>", "<head><title>" . self::$_meta['title'] . "</title>", $output);
        $output = str_replace("</title>", "</title><meta name=\"description\" content=\"" . self::$_meta['description'] . "\" />", $output);
        $output = str_replace("</title>", "</title><meta name=\"keywords\" content=\"" . self::$_meta['keywords'] . "\" />", $output);
        return $output;
    }

}

?>
