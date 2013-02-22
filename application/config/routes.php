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
    
    
    '/^\/user\/login.html$/' => array('/user/login.html', true),
    //'/^\/user\/logout.html$/' => array('/user/login.html', true),
    '/^\/user\/([0-9]+)(.*)$/' => array('/user/index/id/$1$2', true),
    '/^\/message\/with\/([0-9]+)(.*)$/' => array('/message/with/id/$1$2', true),
    '/^\/message\/page\/(.*)$/' => array('/message/index/page/$1', true),
    '/^\/vote\/page\/(.*)$/' => array('/vote/index/page/$1', true),
    '/^\/warning\/page\/(.*)$/' => array('/warning/index/page/$1', true),
    '/^\/forum\/([0-9]+)(.*)$/' => array('/forum/show/id/$1$2', true),
    '/^\/forum\/edit\/id\/([0-9]+)$/' => array('/forum/create/id/$1', true),
    '/^\/forum\/page\/(.*)$/' => array('/forum/index/page/$1', true),
    '/^\/users(.*)$/' => array('/user/list$1', true),
    '/^\/ksk(.*)$/' => array('/user/list/type/ksk$1', true),
    '/^\/admins(.*)$/' => array('/user/admins/$1', true),
)
?>
