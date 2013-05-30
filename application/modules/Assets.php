<?php
/*
 * Assets module is born to optimize css an js files
 */

class Assets extends X3_Module {
    
    public function beforeAction($action) {
        $url = X3::app()->basePath . str_replace('assets','',X3::app()->request->url);
        $assets = X3::app()->basePath . DIRECTORY_SEPARATOR .'assets';
        if(X3_DEBUG && !is_dir($assets)){
            @mkdir($assets, 0777);
        }        
        if(is_file($url) && is_dir($assets) && is_writable($assets)){
            $time = filemtime($url);
            $ext = strtolower(pathinfo($url,PATHINFO_EXTENSION));
            $filename = $assets . DIRECTORY_SEPARATOR . md5(basename($url));
            if(is_file($filename)){
                if(filemtime($filename) != $time)
                    @unlink($filename);
                else{
                    readfile($filename);
                    exit;
                }
            }
            //compress logic goes here
            if(strpos($ext,'css')!==false){
                $contents = file_get_contents($url);
                $realpath = realpath(pathinfo($url,PATHINFO_DIRNAME));
                if($realpath){
                    $m = array();
                    if(preg_match_all("#url\(([^\)]+)#", $contents,$m)>0){
                        foreach($m[1] as $src){
                            if(substr($src,0,1) == '/')
                                $res = realpath(X3::app()->basePath . $src);
                            else
                                $res = realpath($realpath . DIRECTORY_SEPARATOR . trim($src,'/ '));
                            if($res){
                                $res = X3::app()->baseUrl . str_replace(X3::app()->basePath, "", $res);
                                $contents = str_replace($src,$res,$contents);
                            }
                        }
                    }
                    //removing comments
//                    $contents = preg_replace("#/\*([^/]+)/#", "", $contents);
//                    $contents = preg_replace("#;(\s+)#", ";", $contents);
//                    $contents = preg_replace("#\}(\s+)#", "}", $contents);
//                    $contents = preg_replace("#(\s+)\{#", "{", $contents);
//                    $contents = preg_replace("#\{(\s+)#", "{", $contents);
//                    $contents = preg_replace("#,(\s+)#", ",", $contents);
//                    $contents = preg_replace("#:(\s+)#", ":", $contents);
                    echo preg_replace("#[\r\n\t]#", "", $contents);
                }else
                    echo $contents;
            }
        }else{
            throw new X3_Exception(!is_dir($assets)?"Assets dir does not exists.":(!is_writable($assets)?"assets directory is not writable":'file does not exists'));
        }
        exit;
    }
    
}
