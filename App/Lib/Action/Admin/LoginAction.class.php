<?php
class LoginAction extends Action{
	
	//登陆界面
	public function _initialize(){

		if (C('login_key')==''){
			$config['login_key']=str_rand();
			$config_old = require './App/Conf/config.php';
			if(is_array($config) && is_array($config_old)){
				$config_new = array_merge($config_old,$config);
			}
			if(is_array($config_new)){
				arr2file('./App/Conf/config.php',$config_new);
			}
		}

		//检测 right enter
		if(empty($_SESSION['right_enter'])) {
			echo '<script>top.location.href="./"</script>';
			exit();
		}
	}
	
	//管理员登录
	public function index(){
		if($_POST['login']){
			if (!isset($_POST['user_name']) || !isset($_POST['password'])){
				$this->error('帐号或密码不能为空！');
			}
			
			$user_name=trim($_POST['user_name']);
			$password=trim($_POST['password']);
			$password=md5($password);
			$user=M('Admin');
			$where['user_name']=$user_name;
			$where['password']=$password;
			$where['is_del']=0;
			$result=$user->where($where)->find();
			if ($result){
				setcookie('user_name',$_POST['user_name'],time()+86400,'/');
				setcookie('check',md5($_POST['user_name'].C('login_key')),time()+86400,'/');
				$data['last_time']=time();
				$result=$user->where($where)->save($data);
				$this->success('登录成功',U('Index/index'));
			}else{
				$this->error('帐号或密码错误');
			}
		}else{
			if ($_COOKIE['user_name']){
				header("Location: ?a=index&m=Index&g=Admin");
			}
			$this->display();
		}
	}
	
	//管理员登出
	public function logout(){
		if (isset($_COOKIE['check'])){
			setcookie('user_name','',time()-1,'/');
			setcookie('check','',time()-1,'/');
			$this->success('登出成功',U('Login/index'));
		}else{
			$this->error('您并未登录',U('Login/index'));
		}
	}
	
}