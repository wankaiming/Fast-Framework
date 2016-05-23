<?php
//网站入口类 
class app {

    //预加载文件
    public function init(){
        require_once 'config.php';
        error_reporting(ERROR_LEVEL);
        date_default_timezone_set(TIMEZONE); 
        require_once 'lib/router.php';
        require_once 'lib/super.php';
        require_once 'lib/smarty/smarty.class.php';
    }
    
    //加载crtl文件
    public function requireCtrl($module,$group=''){
        if(!empty($group)){
            $file = $group.'/ctrl/'.$module.'/'.$module.'Ctrl.class.php';
        }else{
            $file = 'ctrl/'.$module.'/'.$module.'Ctrl.class.php';
        }
        if(!file_exists($file)){
            self::error('404');
        }
        require_once $file;
        $ctrlClass  = $module.'Ctrl';
        $tpl = self::requireSmarty($module,$group);
        return new $ctrlClass($tpl,$module,$group);
    }
    
    //加载模板
    public function requireSmarty($dir,$group=''){
        $smarty = new Smarty();
        if(!empty($group)){
            $smarty->template_dir=Admin_Template_dir.$dir.'/';
        }else{
            $smarty->template_dir=Template_dir.$dir.'/';
        }
        $smarty->compile_dir=Compile_dir;
        $smarty->cache_dir=Cache_dir;
        $smarty->left_delimiter=Left_delimiter;
        $smarty->right_delimiter=Right_delimiter;
        $smarty->cache_lifetime=Cache_lifetime;
        $smarty->caching=Caching;
        return $smarty;
    }
    
    //显示错误
    public function error($type=''){
        header('Content-Type:text/html;charset=utf-8');
        echo '出错啦<br/>';    
        switch($type){
            case 'model':
                echo 'model 文件不存在';break;
            case 'ctrl':
                echo 'ctrl 文件不存在';break;
            case 'method':
                echo 'method 不存在';break;
            case '404':
                include '404.html';break;
            default:
                echo '<pre>';
                print_r($type);
                echo '</pre>';
                break;
        }
        
        //写入错误日志 
        writeLog('USER_AGENT:'.$_SERVER['HTTP_USER_AGENT'].' IP:'.getIP().' URL:'.'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]." \r\nGET:".var_export($_GET,true)." \r\nPOST:".var_export($_POST,true),'error');
        
        //出错后停止运行
        exit;
    }
    
    //程序运行入口
    public function run(){
        //加载公共文件
        self::init();
        
        //url路由解析
        $router = new Router();
        $group = $router->getGroup();
        $module = $router->getCtrl();
        $action = $router->getAction();
        $_GET = $router->getParams();
        if(!empty($_GET)){
            $_REQUEST = array_merge($_POST,$_GET);
        }
        
        //die($group.' **** '.$module.' **** '.$action);
        
        //实例化ctrl
        $ctrl = self::requireCtrl($module,$group);
        
        try { 
            if(method_exists($ctrl,$action)){
                header("Content-type: text/html; charset=utf-8"); 
                //响应url的操作 
                $ctrl->$action();
            }else{
                self::error('method');
            }
        } catch (Exception $e) { 
            self::error($e);
        }
    }
}
?>