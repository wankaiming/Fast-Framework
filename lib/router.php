<?php
//路由解析类
class Router{  
    private $route;  
    private $ctrl;  
    private $group;  
    private $action;  
    private $params;  
    
    public function __construct(){  
        $params = array();    
        $url = $_SERVER["PATH_INFO"];
        if(strpos($url,'/')==0){
            $url = substr($url,1);
        }
        
        //防止没有favicon.ico图片时请求php响应
        if($url=='favicon.ico'){
            exit();
        }
        
        //url使用“/”访问的情况
        if(strpos($url,'/') !== false){
        
            if($url=='/' || $url=='/index.php'){
                $url = 'index/index';
            }
            
            //?追加,截取后面的参数
            if(strpos($url,"?")){
                $uri = substr($url,strpos($url,"?")+1);
                $uriArr = explode("&",$uri);
                foreach($uriArr as $row){
                    $rowArr = explode("=",$row);
                    if(isset($rowArr[0]) && isset($rowArr[1])){
                        $params[$rowArr[0]]=$rowArr[1];
                    }else if(isset($rowArr[0]) && !isset($rowArr[1])){
                        $params[$rowArr[0]]='';
                    }
                }
                $url = substr($url,0,strpos($url,"?"));
            }
            
            //&追加 
            if(!strpos($url,"?") && strpos($url,"&")){
                $uri = substr($url,strpos($url,"&")+1);
                $uriArr = explode("&",$uri);
                foreach($uriArr as $row){
                    $rowArr = explode("=",$row);
                    $params[$rowArr[0]]=$rowArr[1];
                }
                $url = substr($url,0,strpos($url,"&"));
            }

            $afterPathInfo = substr($url,strlen(AdminDir)+1,strlen($url));
            if(strpos($_SERVER['PATH_INFO'],AdminDir)==1 && $afterPathInfo!=""){
                $this->group=AdminDir;
                $url = $afterPathInfo;
            }
            
            $routeParts = explode("/",$url);   
            
            $this->ctrl=$routeParts[0];    
            $this->action=isset($routeParts[1])? $routeParts[1]:"index";    
            array_shift($routeParts);    
            array_shift($routeParts);         
            
            if(!empty($routeParts)){
                $firstVal = "";
                $length = count($routeParts);
                if($length%2!=0){
                    $length++;
                }
                for($i=0;$i<$length;$i++){
                    if ($i%2==0){
                        $firstVal = $routeParts[$i];
                    }else{
                        $params[$firstVal] = isset($routeParts[$i])?$routeParts[$i]:'';
                    }
                }
            }
            $this->params=$params;  
        
        //url为空时，默认访问的模块和方法 
        }else{
            $this->ctrl="index";    
            $this->action="index";    
        }
    } 
    
    public function getAction(){    
        if(empty($this->action)) 
            $this->action="index";    
        return $this->action;  
    }  
    
    public function getGroup(){    
        return $this->group; 
    }  
    
    public function getCtrl(){    
        return $this->ctrl; 
    }  
    
    public function getParams(){    
        return $this->params;  
    }
}
