<?php
class SettingAction extends BaseAction{
	
	public function index(){
		if ($_POST['submit']){
			//保存上传图片
			if ($_FILES['upload']['name'] != '') {
				$file_del = './App/Tpl/Home/'.C('home.default_theme').'/Public/img/logo.png';
				@unlink($file_del);
				$_FILES['upload']['name'] = 'logo.PNG';
				$this->upload('./App/Tpl/Home/'.C('home.default_theme').'/Public/img/');
			}
			$this->success('修改成功');
		}else {
			$this->display();
		}
	}
	
	public function cate(){
		$this->display();
	}
	
	public function ad(){
		$this->display();
	}
	
	public function finish(){
		$config = array('new_visit' => 0);
		$this->updateconfig($config);
		header("Location: ?a=index&m=Index&g=Admin");
	}
}