<?php 
//网站配置文件     

//是否开启数据库调试
define('DEBUG_SQL',false);

//db 配置
define('HOST','localhost');
define('USER','root');
define('PWD','');
define('DB','demo');

define('ERROR_LEVEL',E_ALL);
define('TIMEZONE','Asia/Shanghai');


define('WEBFILEDIR',$_SERVER['DOCUMENT_ROOT'].'/'); //绝对路径
define('WEBDIR','/');//相对路径
define('BASEURL','http://www.demo.com/');//网站访问基准url
define('COOKIE_DOMAN','demo.com');
define('UPLOAD','upload/'); //文件上传目录


//后台文件夹
define('AdminDir','admin');

//smarty 配置
define('Template_dir',WEBFILEDIR.'/templates/');
define('Compile_dir',WEBFILEDIR.'/templates_c/');
define('Admin_Template_dir',WEBFILEDIR.AdminDir.'/templates/');
define('Cache_dir',WEBFILEDIR.'/cache/');
define('Left_delimiter',"<{");
define('Right_delimiter',"}>");
define('Cache_lifetime','60');
define('Caching',false);
?>