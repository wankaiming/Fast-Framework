<?php 
//index ctrl
class indexCtrl extends Ctrl {

    public function index(){
        $db = $this->conDb('index');
        $newsList = $db->getNewsList();
        $this->viewObj->assign('newsList',$newsList);
        $this->viewObj->display('index.html');
    }

}
?>