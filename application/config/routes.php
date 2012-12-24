<?php
return
array(
    //Common
    '/^\/moduler(.*)$/'=>array('/Admin_Moduler$1'),
    '/^\/sitemap\.xml$/' => array('/site/map/type/xml', true), // first element - module/action, second - if either way then redirect
    '/^\/sitemap.html$/' => array('/site/map/type/html'), 
    '/^\/sitemap\/(.*)$/' => array('/site/map/type/html$1'), 
    '/^\/login\/(.*)$/' => array('/admin/index/name/$1', true),
    '/^\/logout(.*)$/' => array('/user/logout$1', true),
    '/^\/feedback\.html$/' => array('/site/feedback.html', true),
    '/^\/contacts\.html$/' => array('/site/contacts.html', true),
    '/^\/unsubscribe\/(.*)$/'=>array('/subscribe/approve/unkey/$1',true),
    //Video/Photos
    '/^\/video\/([0-9]+)(.*)$/' => array('/video/show/id/$1$2', true),
    //Page
    '/^\/page\/(.+)?$/' => array('/page/show/name/$1', true),
    //Articles/News
    '/^\/news\/([0-9]+)?(.*)$/' => array('/news/show/id/$1$2', true),
    //Jobs
    '/^\/jobs\/([0-9]+)?(.*)$/' => array('/jobs/show/id/$1$2', true),
    
    
    '/^\/admins(.*)$/' => array('/user/admins/$1', true),
)
?>
