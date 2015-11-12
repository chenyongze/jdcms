<?php

define('UC_CLIENT_VERSION', '1.6.0');
define('UC_CLIENT_RELEASE', '20110501');

define('API_DELETEUSER', 1);
define('API_RENAMEUSER', 1);
define('API_GETTAG', 1);
define('API_SYNLOGIN', 1);
define('API_SYNLOGOUT', 1);
define('API_UPDATEPW', 1);
define('API_UPDATEBADWORDS', 1);
define('API_UPDATEHOSTS', 1);
define('API_UPDATEAPPS', 1);
define('API_UPDATECLIENT', 1);
define('API_UPDATECREDIT', 1);
define('API_GETCREDIT', 1);
define('API_GETCREDITSETTINGS', 1);
define('API_UPDATECREDITSETTINGS', 1);
define('API_ADDFEED', 1);
define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '1');

define('IN_API', true);
define('CURSCRIPT', 'api');

//note 普通的 http 通知方式
if(!defined('IN_UC')){
	error_reporting(0);
	set_magic_quotes_runtime(0);
		
	defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	require_once './config.inc.php';
	
	$_DCACHE = $get = $post = array();
	
	$code = @$_GET['code'];
	parse_str(_authcode($code, 'DECODE', UC_KEY), $get);
	if(MAGIC_QUOTES_GPC) {
		$get = _stripslashes($get);
	}
	
	$timestamp = time();
	if($timestamp - $get['time'] > 3600) {
		exit('Authracation has expiried');
	}
	if(empty($get)) {
		exit('Invalid Request');
	}
	$action = $get['action'];
	
	require_once './uc_client/lib/xml.class.php';
	$post = xml_unserialize(file_get_contents('php://input'));
	
	if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcreditsettings', 'updatecreditsettings'))) {
		$uc_note = new uc_note();
		echo $uc_note->$get['action']($get, $post);
		exit();
	} else {
		exit(API_RETURN_FAILED);
	}
}else { //note include 通知方式	
	exit('Invalid Request');
}

class uc_note {

	var $dbconfig = '';
	var $db = '';
	var $tablepre = '';
	var $appdir = '';

	function _serialize($arr, $htmlon = 0) {
		if(!function_exists('xml_serialize')) {
			include_once './uc_client/lib/xml.class.php';
		}
		return xml_serialize($arr, $htmlon);
	}

	function uc_note() {
		$this->appdir = substr(dirname(__FILE__), 0, -3);
		$this->dbconfig = $this->appdir.'./config.inc.php';
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
	}

	function test($get, $post) {
		return API_RETURN_SUCCEED;
	}

	/**
	 * 同步登陆
	 */
	function synlogin($get, $post) {
		if(!API_SYNLOGIN) {
			return API_RETURN_FORBIDDEN;
		}
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$username = trim($get['username']);
		$login_time = $get['time'];
		
		//数据库配置文件
		$config = include_once '../App/Conf/config.php';
		//连接数据库
		$db=mysql_connect($config['db_host'],$config['db_user'],$config['db_pwd']);
		mysql_select_db($config['db_name']);
		mysql_query('set names utf8');
		
		$sql="select id,name from ".$config['db_prefix']."user where name = '".$username."' and is_del=0";
		$result=mysql_query($sql);
		$num=mysql_num_rows($result);
		if (!$num) {
			$table = $config['db_prefix'].'user';
			$passwd = md5(time().rand(100000, 999999));
			$sql="insert into ".$table."(name,passwd,add_time) values('".$username."','".$passwd."',".$login_time.")";
			$result=mysql_query($sql);
			$user_id = mysql_insert_id();
			$user_info = array('id' => $user_id, 'name' => $username);
		}else {
			$user_info = mysql_fetch_assoc($result);
		}

		//登陆
		setCookie('id',$user_info['id'],time()+3600*24*7,'/');
		setCookie('name',$user_info['name'],time()+3600*24*7,'/');

		// 更新用户信息
		$last_ip = getClientIp();
		$sql="update ".$table." set last_time = ".$login_time.",last_ip = '".$last_ip."' where id = ".$user_info['id'];
		mysql_query($sql);

		return API_RETURN_SUCCEED;
	}

	/**
	 * 同步退出
	 */
	function synlogout($get, $post) {
		
		if(!API_SYNLOGOUT) {
			return API_RETURN_FORBIDDEN;
		}
		
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		
		setCookie('id',null,time()-1,'/');
		setCookie('name',null,time()-1,'/');

		return API_RETURN_SUCCEED;
	}	
}

//note 使用该函数前需要 require_once $this->appdir.'./config.inc.php';
function _setcookie($var, $value, $life = 0, $prefix = 1) {
	global $cookiepre, $cookiedomain, $cookiepath, $timestamp, $_SERVER;
	setcookie(($prefix ? $cookiepre : '').$var, $value,
		$life ? $timestamp + $life : 0, $cookiepath,
		$cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
				return '';
			}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function _stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = _stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

function getClientIp(){
	$onlineip = '';
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$onlineip = getenv('HTTP_CLIENT_IP');
	}

	elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')){
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	}

	elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$onlineip = getenv('REMOTE_ADDR');
	}

	elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	return $onlineip;
}