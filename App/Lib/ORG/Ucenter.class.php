<?php
class Ucenter extends Action{
	//构造函数
	public function _initialize(){
		require('./api/config.inc.php');
		require('./api/uc_client/client.php');
	}
	
	//用户注册
	public function register($username, $password, $email){
		$uid = uc_user_register($username, $password, $email);
		if($uid > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	//用户登录
	public function login($username, $password){
		$ucresult = uc_user_login($username, $password);
		if($ucresult[0] > 0) {
// 			return true;
			return uc_user_synlogin($ucresult[0]);
		} elseif($ucresult[0] == -1) {
			return 'none';
		} elseif($ucresult[0] == -2) {
			return 'error';
		}
	}
	
	//用户修改密码
	public function pwd($username, $oldpassword, $newpassword){
		$ucresult = uc_user_edit($username, $oldpassword, $newpassword);
		if($ucresult == 1) {
			return $ucresult;
		} else {
			return false;
		}
	}
	
	//用户信息
	public function info($username){
		$ucresult = uc_get_user($username);
		if($ucresult) {
			return $ucresult;
		} else {
			return false;
		}
	}
	
	//用户退出
	public function logout(){
		$ucresult = uc_user_synlogout();
		return $ucresult;
	}
}