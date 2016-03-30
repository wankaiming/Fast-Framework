<?php 
//admin ctrl
class adminCtrl extends Ctrl {
	public function index(){
		$this->viewObj->display('index.html');
    }
}
?>