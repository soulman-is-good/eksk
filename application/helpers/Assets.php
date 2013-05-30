<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Assets
 *
 * @author Soul_man
 */
class Assets extends X3_Component {

    private $_published = array();
    public $linkAssets = false;


    public function init() {
        //get published paths;
        $paths = file(X3::app()->basePath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'manager.txt',FILE_SKIP_EMPTY_LINES);
        foreach($paths as $path){
            $path = explode('=',$path);
            $this->_published[$path[1]] = $path[0];
        }
    }

    public function publish($path, $forceCopy = false, $level = -1, $hashByName = false) {
        if (isset($this->_published[$path]))
            return $this->_published[$path];
        else if (($src = realpath($path)) !== false) {
            if (is_file($src)) {
                $dir = crc32($hashByName ? basename($src) : dirname($src) . filemtime($src));
                $fileName = basename($src);
                $dstDir = X3::app()->basePath . DIRECTORY_SEPARATOR . $dir;
                $dstFile = $dstDir . DIRECTORY_SEPARATOR . $fileName;

                if ($this->linkAssets) {
                    if (!is_file($dstFile)) {
                        if (!is_dir($dstDir)) {
                            mkdir($dstDir);
                            @chmod($dstDir, $this->newDirMode);
                        }
                        symlink($src, $dstFile);
                    }
                } else if (@filemtime($dstFile) < @filemtime($src)) {
                    if (!is_dir($dstDir)) {
                        mkdir($dstDir);
                        @chmod($dstDir, $this->newDirMode);
                    }
                    copy($src, $dstFile);
                    @chmod($dstFile, $this->newFileMode);
                }

                return $this->_published[$path] = X3::app()->baseUrl . "/$dir/$fileName";
            } else if (is_dir($src)) {
                $dir = $this->hash($hashByName ? basename($src) : $src . filemtime($src));
                $dstDir = X3::app()->basePath . DIRECTORY_SEPARATOR . $dir;

                if ($this->linkAssets) {
                    if (!is_dir($dstDir))
                        symlink($src, $dstDir);
                }
                else if (!is_dir($dstDir) || $forceCopy) {
                    CFileHelper::copyDirectory($src, $dstDir, array(
                        'exclude' => $this->excludeFiles,
                        'level' => $level,
                        'newDirMode' => $this->newDirMode,
                        'newFileMode' => $this->newFileMode,
                    ));
                }

                return $this->_published[$path] = $this->getBaseUrl() . '/' . $dir;
            }
        }
        throw new CException(Yii::t('yii', 'The asset "{asset}" to be published does not exist.', array('{asset}' => $path)));
    }

}

?>
