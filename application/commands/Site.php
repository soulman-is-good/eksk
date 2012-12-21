<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Site
 *
 * @author Soul_man
 */
class SiteCommand extends X3_Command{
    
    public function init() {
        
    }
    
    public function runIndex() {
        //echo Company::getByPk(2)->title;
        //print_r(X3::app()->global);
        X3::import('@app:commands:Company.php');
        $c = new CompanyCommand();
        $this->runTest2();
    }
    
    public function runTest() {
        //$mailer = new X3_Mailer();
        //$mailer->send('soulman.is.good@gmail.com','Тест',"RAEA ываадлоаыдлваоывлдао");
        var_dump(mail('maxim@instinct.kz','TEST','TEST'));
        var_dump(error_get_last());
    }
    
    public function runDump() {
        //$d = date("d.m.Y");
        //exec('mysqldump -uroot -proot -d kansha_tmp > /var/www/kansha.'.$d.'.sql');
    }
    
    public function runImport() {
        if(!is_file('import.sql')) exit;
        $file = file("import.sql");
        set_time_limit(0);
        X3::db()->startTransaction();
        foreach($file as $i=>$line){
            X3::db()->addTransaction($line);
            if($i>0 && $i%500 == 0){
                if(!X3::db()->commit())
                    echo X3::db()->getErrors();
                X3::db()->startTransaction();
            }
        }
        if(!X3::db()->commit())
            echo X3::db()->getErrors();
    }
    
    public function runTest2() {
        echo date('d.m.Y H:i',strtotime('15.10.2012 03:10'));
    }
    
    public function runStat() {
        $file = X3::app()->basePath . DIRECTORY_SEPARATOR . 'dump' . DIRECTORY_SEPARATOR . 'stat' . DIRECTORY_SEPARATOR . 'main.stat';
        if(is_file($file))
            @unlink($file);
        X3::db()->query('
        SELECT (SELECT COUNT(0) FROM company_item) AS `companyitems`, (SELECT COUNT(0) FROM data_company) AS `companies` 
        INTO OUTFILE "'.$file.'"
        FIELDS TERMINATED BY \',\' OPTIONALLY ENCLOSED BY \'"\'
        LINES TERMINATED BY "\n";
        ');
    }
    
}

?>
