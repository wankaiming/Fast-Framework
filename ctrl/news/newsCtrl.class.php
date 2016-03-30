<?php 
//news ctrl
class newsCtrl extends Ctrl {
	public function show(){
		$db = $this->conDb('news');
		$detail = $db->getNewsDetail($_GET['id']);
		$this->viewObj->assign('title',$detail['title']);
		$this->viewObj->assign('detail',$detail);
		$this->viewObj->display('show.html');
    }
}
?>