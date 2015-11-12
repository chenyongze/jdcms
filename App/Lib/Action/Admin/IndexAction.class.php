<?php
set_time_limit(0); 
class IndexAction extends BaseAction{
	
	//更新配置
	public function _initialize(){
		get_active_plugins();
		if (C('web_path')==''){
			$config['web_path']='/';
		}
		if (C('article_show')==''){
			$config['article_show']=0;
		}
		$file='./Public/statics/version.xml';
		$xml=simplexml_load_file($file);
		$ver=$xml->num;
		$config['cms_versions']=strval($ver);
		$config_old = require './App/Conf/config.php';
		if(is_array($config) && is_array($config_old)){
			$config_new = array_merge($config_old,$config);
		}
		if(is_array($config_new)){
			arr2file('./App/Conf/config.php',$config_new);
		}
	}

	//后台首页
    public function index(){
    	$this->assign('user_name',$_COOKIE['user_name']);
    	$this->display();
    }
    
    //左侧导航栏
    public function left(){
    	$this->display();
    }
    
    //系统环境信息
    public function main(){
    	$server_info = array(
    		'CMS版本'=>C('cms_versions'),
    		'操作系统'=>PHP_OS,
    		'运行环境'=>$_SERVER["SERVER_SOFTWARE"],
    		'PHP运行方式'=>php_sapi_name(),
    		'最大上传限制'=>ini_get('upload_max_filesize'),
    		'最大执行时间'=>ini_get('max_execution_time').'秒',
    		'服务器时间'=>date("Y年n月j日 H:i:s"),
    		//'北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
    		'服务器域名/IP'=>$_SERVER['SERVER_NAME'].' ['.gethostbyname($_SERVER['SERVER_NAME']).']',
    		'剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
    	);
    	$this->assign('server_info',$server_info);
    	$this->display();
    } 
    
    //更新缓存
    public function delcache(){
    	import("@.ORG.Dir");
    	$dir = new Dir;
    	@unlink(RUNTIME_PATH.'~runtime.php');
    	if(is_dir(RUNTIME_PATH.'Cache')){
    		$dir->delDir(RUNTIME_PATH.'Cache');
    	}
    	if(is_dir(RUNTIME_PATH.'Data')){
    		$dir->delDir(RUNTIME_PATH.'Data');
    	}
    	if(is_dir(RUNTIME_PATH.'Logs')){
    		$dir->delDir(RUNTIME_PATH.'Logs');
    	}
    	if(is_dir(RUNTIME_PATH.'Temp')){
    		$dir->delDir(RUNTIME_PATH.'Temp');
    	}
    	$this->ajaxReturn('清除成功');
    }
    
	//自动升级程序
	public function upgrade(){
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>正在下载......！</p>";
		flush();
		$now_version = C('cms_versions');
		$latest_version = curHtml(C('official_website').'push/cmsversion');
		$latest_version = explode('"', $latest_version);
		$latest_version = $latest_version['1'];
		$fileName="http://www.jdcms.com/down/jdcms".$now_version."_u.zip";//下载到的文件;
		$tempFile="temp.zip";
		$opts = array('http'=>array('method'=>"GET",'timeout'=>60));  
		$context = stream_context_create($opts);     
		$get_file=file_get_contents($fileName, false, $context);
		if($get_file){
			$put=file_put_contents($tempFile,$get_file);
		}
		if($put){
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>正在解压......！</p>";
			flush();
		}else {
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>自动升级失败，请到<a href='http://www.jdcms.com/' target='_blank'>简单CMS官方网站</a>下载更新包手动更新！</p><br>";
			return false;
		}
		$path="./";
		if($this->unArchive($tempFile,$path)!=false){
			$config_old = require './App/Conf/config.php';
			$config=array('cms_versions'=>$latest_version);
			if(is_array($config)){
				$config_new = array_merge($config_old,$config);
			}
			arr2file('./App/Conf/config.php',$config_new);
		}
		@unlink($tempFile);
		@unlink('./App/Runtime/~runtime.php');
	}
	
	//升级数据库
	public function update_database(){
		$err_nums=$this->execute_sql();
		echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>任务完成！</p><br>";
	}
	
	//解压并覆盖zip文件
	public function unArchive($tempFile,$path){
		@$zip = new ZipArchive();
		if (!$zip){
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>升级失败，请开启zip模块！</p><br>";
			return false;
		}
		if ($zip->open($tempFile)) {
			$res = $zip->extractTo($path);
			if ($res == true){
				$err_nums=$this->execute_sql();
				echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>任务完成！</p><br>";
			}else {
				echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>自动升级失败，请到<a href='http://www.jdcms.com/' target='_blank'>简单CMS官方网站</a>下载更新包手动更新！</p><br>"; 
			}
			return $zip->close();
		} else {
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>自动升级失败，请到<a href='http://www.jdcms.com/' target='_blank'>简单CMS官方网站</a>下载更新包手动更新！</p><br>";
			return false;
		}
	}
	
	//导入SQL脚本
	public function execute_sql(){
		$sqls = $this->get_sql('./Public/sql/jdcmsV1.3.sql');
		$err_nums=0;
		foreach ($sqls as $sql){
			//替换表前缀
			$sql = str_replace('jd_',C('db_prefix'),$sql);
			$Model = new Model();
			$result = $Model->query($sql);
			if (!$result){
				$err_nums++;
			}
		}
		return $err_nums;
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
}