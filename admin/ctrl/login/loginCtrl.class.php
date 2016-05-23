<?php 
//login ctrl
class loginCtrl extends Ctrl {
    public function index(){
        if(isset($_SESSION['admin'])){
            header("location:/admin/admin/index"); 
            exit;
        }
        $msg = '';
        if($_POST && $_POST['name']!='' && $_POST['pwd']!=''){
            $db = $this->conDb('login',AdminDir);
            $result = $db->checkPwd(trim($_POST['name']),md5(trim($_POST['pwd'])));
            if(!empty($result)){
                $_SESSION['admin'] = $result;
                header("location:/admin/admin/index"); 
                exit;
            }else{
                $msg = 'User name or password is incorrect!';
            }
        }
        $this->viewObj->assign('msg',$msg);
        $this->viewObj->display('index.html');
    }
    
    public function loginOut(){
        unset($_SESSION['admin']);
        header("location:/admin/login/index"); 
        exit;
    }
}
?>