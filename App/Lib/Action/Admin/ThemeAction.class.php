<?php
class ThemeAction extends BaseAction{

	//网站信息设置
	public function index(){
		if ($_POST['submit']){
			$config = $_POST["con"];
			$this->updateconf($config);
		}else {
			// 获得模板文件下的模板
			$tpl_dir =TMPL_PATH.'/Home';
			$dir = dir($tpl_dir);
			$theme_list = array();	
			$key=0;
			while (false !== ($file = $dir->read())){
				if (is_dir(TMPL_PATH.'/Home/'.$file) && $file != '.' && $file != '..'){
					$theme_list[$key]['dirname'] = $file;
					$key++;
				}
			}
			$dir->close();
			$this->assign('theme_list',$theme_list);
			$this->display();
		}
	}
}