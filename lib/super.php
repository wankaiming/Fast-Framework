<?php 
//网站基类
//super web 
class Web {
	private static $con = null;
	
	//mysql数据库连接
	public function conDb($module,$group=''){
		if (self::$con==null){
			require_once 'lib/dataBase.php';
			self::$con = new dataBase(HOST,USER,PWD,DB);
		}
		return self::requireModel($module,$group);
	}

	//加载model文件
	public function requireModel($module,$group=''){
		if(!empty($group)){
			$file = $group.'/model/'.$module.'/'.$module.'Model.class.php'; 
		}else{
			$file = 'model/'.$module.'/'.$module.'Model.class.php'; 
		}
		
		if(!file_exists($file)){
			exit('没有model文件');
		}
		require_once $file;
		$modelClass = $module.'Model';
		return new $modelClass(self::$con); 
	}
}

//super Model
class Model extends Web {
    public $con;
	
    public function __construct(&$con) {
        $this->con = &$con;
    }
	public function setDebug($flag=false){
		$this->con->debug=$flag;		
	}
}

//super Ctrl
class Ctrl extends Web {
	public $viewObj;

    public function __construct(&$view,$module='',$group=''){
		$this->viewObj = &$view;
		
		if(!empty($group)){
			//验证权限 放过登录，上传模块
			if(!in_array($module,array('login','upload'))){
				//如果没有登录，则转向登录
				if(!isset($_SESSION['admin'])){
					header("location:/admin/login"); 
				}
				
				//获取登录角色允许访问的模块 
				$db = $this->conDb('role',AdminDir);
				$list = $db->getAllowModule($_SESSION['admin']['fk_role_id']);
				$this->viewObj->assign('user_name',$_SESSION['admin']['name']);
				$this->viewObj->assign('user_role',$list['role_name']);
				if($list['allow_module']=='all'){
					//最高管理员，放过验证
				}else{
					//判断当前的模块是否在允许访问的模块内 
					if(!strpos($list['allow_module'],$module)){
						exit("Sorry, you do not have access to this module!");
					}
				}
			}
		}
    }
}

//测试输出专用函数
function p($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

//按天写日志
function writeLog($msg,$type=''){
	if($type==''){
		$fileName = "log/".date("Y-m-d").".log";
	}else{
		$fileName = "log/".$type.'_'.date("Y-m-d").".log";
	}
	$handle = fopen($fileName, "a");
	$msg = date("Y-m-d H:i:s")." ".$msg."\n";
	fwrite($handle, $msg);
	fclose($handle);
}

//获取ip
function getIP(){
	if (getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("REMOTE_ADDR"))
		$ip = getenv("REMOTE_ADDR");
	else 
		$ip = "Unknow";
		
	return $ip;
}

//字符串截取
function strCut($str, $maxWidth, $encoding='utf-8'){
	$strlen = mb_strlen($str);
	$newStr = '';
	for($pos = 0, $currwidth = 0; $pos < $strlen; ++$pos ){
		$ch = mb_substr($str, $pos, 1, $encoding);
		if ($currwidth + mb_strwidth($ch, $encoding) > $maxWidth){
			$newStr .= '...';
			break;
		}
		$newStr .= $ch;
		$currwidth += mb_strwidth($ch, $encoding) > 1 ? 2 : 1;
	}
	return $newStr;
}

/*
$sourceImg 源图片路径
$aimDir 目标目录
$aimFileName 目标文件名
$aimWidth 缩放宽度
$aimHeight 缩放高度
*/
function ImgResize($sourceImg, $aimDir, $aimFileName, $aimWidth, $aimHeight)
{
	if (file_exists($aimDir.$aimFileName)) {
		return true;
	}
    if (file_exists($sourceImg)) {
        $arrType = array(
            "1" => "gif",
            "2" => "jpeg",
            "3" => "png"
        );
        list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourceImg);
        if (!isset($arrType[$sourceType])) {
            return false;
        }
        $arrTmpName     = explode(".", $sourceImg);
        $extName        = $arrTmpName[count($arrTmpName) - 1];
        $sourceType     = $arrType[$sourceType];
        $funImageCreate = "imagecreatefrom$sourceType";
        $sImg           = $funImageCreate($sourceImg);
        if ($sourceWidth > $aimWidth || $sourceHeight > $aimHeight) {
            if (($sourceWidth / $aimWidth) >= ($sourceHeight / $aimHeight)) {
                $scale = ($sourceWidth / $aimWidth);
            } else {
                $scale = ($sourceHeight / $aimHeight);
            }
            $aimWidth  = $sourceWidth / $scale;
            $aimHeight = $sourceHeight / $scale;
            $aimImg    = imagecreatetruecolor($aimWidth, $aimHeight);
            imagefilledrectangle($aimImg, 0, 0, $aimWidth, $aimHeight, imagecolorallocate($aimImg, 255, 255, 255));
            imagecopyresampled($aimImg, $sImg, 0, 0, 0, 0, $aimWidth, $aimHeight, $sourceWidth, $sourceHeight);
            $funImg = "image$sourceType";
            $funImg($aimImg, $aimDir . "/" . $aimFileName);
            imagedestroy($aimImg);
            imagedestroy($sImg);
        } else {
            copy($sourceImg, $aimDir . "/" . $aimFileName);
        }
        return $aimFileName;
    } else {
        return false;
    }
}

?>