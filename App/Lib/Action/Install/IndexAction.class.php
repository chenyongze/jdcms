<?php
/**
 * 安装模块
 */
class IndexAction extends Action{

	//安装界面
	public function _initialize(){

		//检测 right enter
		if($_SESSION['right_enter']!=1) {
			echo '系统不支持session!';
			exit();
		}
	}
	
	//许可协议
	public function index(){

		//判断install.lock是否存在，存在则跳转到前台首页
		$file = './Public/Install/install.lock';
		if (file_exists($file)){
			$this->redirect('Home/Index/index');
		}
		
		if ($_POST['startinstall']){
			if ($_POST['union']){
				$_SESSION['union'] = 1;
			}else {
				$_SESSION['union'] = 0;
			}
			$this->redirect('Install/Index/check');
		}else{
			$this->display();
		}
	}
		
	//检测服务器环境
	public function check(){

		//判断install.lock是否存在，存在则跳转到前台首页
		$file = './Public/Install/install.lock';
		if (file_exists($file)){
			$this->redirect('Home/Index/index');
		}
		
		$error=true;
		//需要检测目录、文件
		$check_file=array(
			'./Uploads',
			'./Uploads/LocalItems',
			'./Public',
			'./Public/Install',
			'./Public/statics',
			'./App/Conf',
			'./App/Conf/config.php',
			'./App/Conf/home.php',
			'./App/Conf/Home',
			'./App/Conf/Home/config.php',
			'./App/Runtime',
			'./App/Runtime/DataBackup',
		);		
		$error_msg=array();
		foreach ($check_file as $file){
			//检测文件是否存在
			if (!file_exists($file)){
				$error_msg[]=$file.' 不存在！';
				$error=false;
				continue;
			}
			if (is_dir($file)){
				//检测目录是否可写				
				$file_test=@fopen($file.'/test.txt','w');
				if(!$file_test){
					$error_msg[] = $file." 不可写!";
					$error = false;
				}
				@fclose($file_test);
				@unlink($file.'/test.txt');
			}else {
				//检测文件是否可写
				if (!is_writeable($file)) {
					$error_msg[] = $file." 不可写!";
					$error = false;
				}
			}
		}
		
			//检测是否支持gd
			if(!function_exists("gd_info")){
				$error_msg[] = "系统不支持gd!";
				$error = false;
			}
			
			//检测是否支持curl
			if(!function_exists("curl_getinfo")){
				$error_msg[] = "系统不支持curl!";
				$error = false;
			}
		
			if (!$error) {
				$this->assign('error_msg', $error_msg);
				$this->display();
			} else {
				$this->redirect('Install/Index/setconf');
			}
	}
	
	//填写配置信息
	public function setconf(){

		//判断install.lock是否存在，存在则跳转到前台首页
		$file = './Public/Install/install.lock';
		if (file_exists($file)){
			$this->redirect('Home/Index/index');
		}
		
		//默认配置信息
		$this->assign('db_host', 'localhost');
		$this->assign('db_port', '3306');
		$this->assign('db_user', 'root');
		$this->assign('db_pwd', '');
		$this->assign('db_name', 'jdcms');
		$this->assign('db_prefix', 'jd_');
		$this->assign('user_name', '');
		$this->assign('password', '');
		$this->assign('repassword', '');
		$this->assign('email', '');
		
		if (isset($_POST['edit'])) {
			
			foreach ($_POST as $key=>$val) {
				$this->assign($key, $val);
			}
			
			extract($_POST);
			$web_path=trim($web_path);
			$db_host=trim($db_host);
			$db_port=trim($db_port);
			$db_user=trim($db_user);
			$db_pwd=trim($db_pwd);
			$db_name=trim($db_name);
			$db_prefix=trim($db_prefix);
			$user_name=trim($user_name);
			$password=trim($password);
			$repassword=trim($repassword);
			$email=trim($email);
			
			//检测信息是否填写
			if (!$db_host || !$db_port || !$db_user || !$db_name || !$db_prefix || !$user_name || !$password || !$repassword || !$email) {
				$this->assign('error_msg','请完整填写配置信息!');
				$this->display();
				exit;
			}
			
			if($user_name =='admin'){
				$this->assign('error_msg','管理员帐号不能设为admin');
				$this->display();
				exit;
			}
			
			if(strlen($password)<6 || strlen($password)>20){
				$this->assign('error_msg','密码应在6-20位之间');
				$this->display();
				exit;
			}
			
			//检查两次密码是否一致
			if ($password != $repassword) {
				$this->assign('error_msg','两次密码不一致！');
				$this->display();
				exit;
			}
			
			//检查email格式
			if (!$this->is_email($email)) {
				$this->assign('error_msg','不是有效的邮箱！');
				$this->display();
				exit;
			}
			
			//检测数据库连接
			$conn = @mysql_connect($db_host,$db_user,$db_pwd);
			if (!$conn) {
				$this->assign('error_msg','数据库连接失败，请检查数据库配置信息!');
				$this->display();
				exit;
			}
			
			//检测数据库是否存在，不存在则创建数据库
			if (!@mysql_select_db($db_name)){
				$sql = "CREATE DATABASE `".$db_name."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
				mysql_query($sql);
			}
			
			//检测数据库是否创建成功
			if(!@mysql_select_db($db_name)){
				$this->assign('error_msg','数据库创建失败！');
				$this->display();
				exit;
			}

			$login_key=str_rand();

			//保存配置到config.php
			$config = array(
				'web_path'  => $web_path,
				'login_key' => $login_key,
				'db_host'	=> $db_host,
				'db_port'	=> $db_port,
				'db_user'	=> $db_user,
				'db_pwd'	=> $db_pwd,
				'db_name'	=> $db_name,
				'db_prefix' => $db_prefix,
			);
			$this->updateconfig($config);
	
			//导入SQL脚本
			$sqls = $this->get_sql('./Public/sql/install.sql');
			$conn = @mysql_connect($db_host,$db_user,$db_pwd);
			mysql_select_db($db_name);
			
			foreach ($sqls as $sql){
				//替换表前缀
				$sql = str_replace('jd_',$db_prefix,$sql);
				$result = mysql_query($sql,$conn);
				if (@mysql_affected_rows($conn)<0){
					$this->assign('error_msg','创建表失败');
					$this->display();
					exit;
				}
			}
						
			$domain = $config[cms_domain] = $config[site_domain] = 'http://'.$_SERVER['HTTP_HOST'].$web_path;
			$this->updateconfig($config);
			
			//添加管理员帐号
			$password=md5($password);
			$add_time=time();
			$sql = "INSERT INTO `".$db_prefix."admin` (`user_name`, `password`, `add_time`) VALUES " .
	        		"('".$user_name."', '".$password."', '".$add_time."');";
			$result = mysql_query($sql);	
			if (@mysql_affected_rows($conn)<0){
				$this->assign('error_msg','添加管理员失败！');
				$this->display();
				exit;
			}else{
							
// 				$url = C('official_website').'push/index';
// 				$data = array (
// 						"union" => $_SESSION['union'],
// 						"user" => $user_name,
// 						"site" => $domain,
// 						"ver" => C('cms_versions'),
// 				);
// 				$data=http_build_query($data);

// 				$ch = curl_init();
// 				$header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");		
// 				curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
// 				curl_setopt($ch, CURLOPT_URL, $url);
// 				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
// 				curl_setopt($ch, CURLOPT_POST, 1);
// 				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// 				$result = curl_exec($ch);
// 				curl_close($ch);

				fopen('./Public/Install/install.lock','w');
				$this->redirect('Install/Index/finish');
			}
		}
		$this->display();
	}
	

	//安装完成
	public function finish(){
		$this->display();
	}
	
	//保存配置信息
	private function updateconfig($config){
		$config_old = require './App/Conf/config.php';

		if(is_array($config) && is_array($config_old)){
			$config_new = array_merge($config_old,$config);
		}
		if(is_array($config_new)){
			arr2file('./App/Conf/config.php',$config_new);
		}
		@unlink('./App/Runtime/~runtime.php');
	}

	//获取SQL脚本
	private function get_sql($sql_file){
		
		$contents = file_get_contents($sql_file);
		$contents = str_replace("\r\n", "\n", $contents);
		$contents = trim(str_replace("\r", "\n", $contents));
		$return_items = $items = array();
		$items = explode(";\n", $contents);
		foreach ($items as $item) {
			$return_item = '';
			$item = trim($item);
			$lines = explode("\n", $item);
			foreach ($lines as $line) {
				if (isset($line[1]) && $line[0] . $line[1] == '--') {
					continue;
				}
				$return_item .= $line;
			}
			if ($return_item) {
				$return_items[] = $return_item;
			}
		}
		return $return_items;
	}
	
	//检测邮箱格式
	private function is_email($email){
		
		$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}\$/i";
		if (strpos($email, '@') !== false && strpos($email, '.') !== false) {
			if (preg_match($chars, $email)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
}
