<?php
set_time_limit(0); 
class PluginAction extends BaseAction{
	public function index(){
		$active_plugin=C('active_plugin');
		$plugin=$_GET['plugin'];
		$action=$_GET['action'];
		
		if(!$plugin && $action==''){
			//获取Public/Plugin下所有插件
			$plugins_list=$this->getPlugins() ;
			//获取配置文件里已激活插件信息
			$active_plugin=unserialize($active_plugin) ;//反序列
			//判断插件列表里的插件的激活状态
			foreach($plugins_list as $key=>$val){
				$plugin_name=explode("/",$key);//plugin/plugin.php
				$plugins_list[$key]['key']=$plugin_name[0];
				if(in_array($key,$active_plugin)){//
					$plugins_list[$key]['is_active']=1;
				}else{
					$plugins_list[$key]['is_active']=0;
				}
			}
			$this->assign('plugins_list',$plugins_list);
			$this->assign("plugins_index","plugins_index");
			$this->display();
		}
		//根据插件名加载相应的插件页面
		if($plugin && $action==''){
			$this->assign('plugin',$plugin);
			require_once "./Public/Plugins/".$plugin."/".$plugin."_index.php";
		}
		//根据插件名称加载加载相应操作
		if($plugin && $action=='setting'){
			require_once "./Public/Plugins/".$plugin."/".$plugin."_setting.php";
			
		}
	}
	//激活/禁止插件
	public function pluginStatus(){
		$is_active=$_GET['is_active'];
		$plugin=$_GET['plugin'];
		$active_plugin=C('active_plugin');
		$active_plugin=unserialize($active_plugin) ;//反序列
		$is_active_change=($is_active+1)%2;
		if($is_active_change==1){
			if(in_array($plugin,$active_plugin)){
				return true;
			}else{
				$active_plugin[]=$plugin;
			}
		}elseif($is_active_change==0){
			$key = array_search($plugin, $active_plugin);
			unset($active_plugin[$key]);
		}
		$config['active_plugin']=serialize($active_plugin);
		$check_file='./App/Conf/config.php';
		if(is_writeable($check_file)){
			$this->updateconf($config);
			$this->ajaxReturn($is_active_change,'y');
		}else{
			$this->ajaxReturn('配置文件'.$check_file.'不可写。如果您使用的是Unix/Linux主机，请修改该文件的权限为777。如果您使用的是Windows主机，请将此文件设为everyone可写！','n');
		}
		

	}
	//删除插件
	public function del(){
		$active_plugin=C('active_plugin');
		$active_plugin=unserialize($active_plugin);
		$plugin_name=$_POST['id'];
		foreach($plugin_name as $val){
			$dir='./Public/Plugins/'.$val;
			$result=$this->cmsDeleteFile($dir);
			if($result){
				if(in_array($val."/".$val.".php",$active_plugin)){
					$key = array_search($val."/".$val.".php", $active_plugin);
					unset($active_plugin[$key]);
				}
				$config['active_plugin']=serialize($active_plugin);
				$check_file='./App/Conf/config.php';
				if(is_writeable($check_file)){
					$this->updateconf($config);
				}
			}
		}
		$this->success('删除完成！');
	}
	public function updateconf($config){
		$config_old = require './App/Conf/config.php';
		if(is_array($config) && is_array($config_old)){
			$config_new = array_merge($config_old,$config);
		}
		if(is_array($config_new)){
			arr2file('./App/Conf/config.php',$config_new);
		}
		@unlink('./App/Runtime/~runtime.php');
	}
	function cmsDeleteFile($file) {
		if (empty($file))
			return false;
		if (@is_file($file))
			return @unlink($file);
		$ret = true;
		if ($handle = @opendir($file)) {
			while ($filename = @readdir($handle)) {
				if ($filename == '.' || $filename == '..')
					continue;
				if (!$this->cmsDeleteFile($file . '/' . $filename))
					$ret = false;
			}
		} else {
			$ret = false;
		}
		@closedir($handle);
		if (file_exists($file) && !rmdir($file)) {
			$ret = false;
		}
		return $ret;
	}
	public function getPlugins() {
		global $cmsPlugins;
		if (isset($cmsPlugins)) {
			return $cmsPlugins;
		}
		$cmsPlugins = array();
		$pluginFiles = array();
		$pluginPath = CMS_ROOT . 'Public/Plugins';
		$pluginDir = @ dir($pluginPath);//打开一个目录句柄，并返回一个对象。这个对象包含三个方法：read() , rewind() 以及 close()。
		if ($pluginDir) {
			while(($file = $pluginDir->read()) !== false) {
				if (preg_match('/^\.+$/', $file)) {
					continue;
				}
				if (is_dir($pluginPath . '/' . $file)) {//is_dir() 函数检查指定的文件是否是目录
					$pluginsSubDir = @ dir($pluginPath . '/' . $file);
					if ($pluginsSubDir) {
						while(($subFile = $pluginsSubDir->read()) !== false) {
							if (preg_match('/^\.+$/', $subFile)) {
								continue;
							}
							
							if ($subFile == $file.'.php') {//文件名里必须有一个文件名.php文件，插件的信息放在里面
								$pluginFiles[] = "$file/$subFile";
							}
							
						}
					}
				}
			}
		}
		if (!$pluginDir || !$pluginFiles) {
			return $cmsPlugins;//所有插件名/插件名.php
		}
		sort($pluginFiles);
		foreach ($pluginFiles as $pluginFile) {
			$pluginData = $this->getPluginData("$pluginPath/$pluginFile");
			if (empty($pluginData['name'])) {
				continue;
			}
			$cmsPlugins[$pluginFile] = $pluginData;
		}
		return $cmsPlugins;
	}
	public function getPluginData($pluginFile) {
		$pluginData = implode('', file($pluginFile));//file() 函数把整个文件读入一个数组中
		preg_match("/Plugin Name:(.*)/i", $pluginData, $plugin_name);
		preg_match("/Version:(.*)/i", $pluginData, $version);
		preg_match("/Set Page:(.*)/i", $pluginData, $plugin_url);
		preg_match("/Description:(.*)/i", $pluginData, $description);
		preg_match("/Jdcms Version:(.*)/i", $pluginData, $jdcms_version);
		preg_match("/Author:(.*)/i", $pluginData, $author_name);

		$plugin_name = isset($plugin_name[1]) ? trim($plugin_name[1]) : '';
		$version = isset($version[1]) ? $version[1] : '';
		$description = isset($description[1]) ? $description[1] : '';
		$plugin_url = isset($plugin_url[1]) ? trim($plugin_url[1]) : '';
		$author = isset($author_name[1]) ? trim($author_name[1]) : '';
		$jdcms_version = isset($jdcms_version[1]) ? trim($jdcms_version[1]) : '';

		return array(
		'name' => $plugin_name,
		'version' => $version,
		'description' => $description,
		'url' => $plugin_url,
		'author' => $author,
		'jdcms_version' => $jdcms_version,
		);
	}
	public function install(){
		Load('extend');
		$keyword=isset($_GET['keyword'])?$_GET['keyword']:'';//搜索关键字
		$currentPage=isset($_GET['p'])?$_GET['p']:'1';//搜索关键字
		$param='?&p='.$currentPage;
		if($keyword){
			$this->assign('keyword',$keyword);
			$param.="&keyword=".$keyword;
			$guanPluginsCount_param="?&keyword=$keyword";
		}
		$plugins_json=curHtml(C('official_website').'push/guanPlugins'.$param);
		$plugins_list=json_decode($plugins_json,true);//反json编码
		$count_json= curHtml(C('official_website').'push/guanPluginsCount'.$guanPluginsCount_param);//获取插件总个数
		$arr=json_decode($count_json,true);//插件总数
		$count=$arr['count'];
		$perNum=$arr['perNum'];//每页显示插件个数
		//计算总页数
		if($count){
			if($count<$perNum){
				$pageCount=1;
			}else{
				if($count % $perNum){
					$pageCount=(int)($count / $perNum) + 1; 
				}else{
					$pageCount=$count/$perNum;
				}
			}
			//翻页链接
			$page_string = '';
			$firstUrl=U('Plugin/install',array('keyword'=>$keyword));
			$finalUrl=U('Plugin/install',array('keyword'=>$keyword,'p'=>$pageCount));
			if( $currentPage == 1 ){
			   $page_string .= '';
			}else{
				$preUrl=U('Plugin/install',array('keyword'=>$keyword,'p'=>$currentPage-1));
			   $page_string .= '<a href="'.$firstUrl.'">首页</a><a href="'.$preUrl.'">上一页</a>';
			} 
			if($pageCount!=1){
				$page_string.="<a class='current'>".$currentPage."</a>";
			}
			if($currentPage==$pageCount){
			   $page_string .= '';
			}else{
				$nextUrl=U('Plugin/install',array('keyword'=>$keyword,'p'=>$currentPage+1));
			   $page_string .= '<a href="'.$nextUrl.'">下一页</a><a href="'.$finalUrl.'">尾页</a>';
			}
		}else{
			$pageCount=0;
			$currentPage=0;
		}
		$this->assign('count',$count);
		$this->assign('currentPage',$currentPage);
		$this->assign('pageCount',$pageCount);
		$this->assign('page_string',$page_string);
		$this->assign('plugins_list',$plugins_list);
		$this->display();
	}
	//上传zip格式插件
	public function uploadPluginZip(){
		$zipfile = isset($_FILES['pluzip']) ? $_FILES['pluzip'] : '';
		if ($zipfile['error'] == 4) {
			$this->error("请选择一个zip插件安装包",U('Plugin/install'));
		}
		if (!$zipfile || $zipfile['error'] >= 1 || empty($zipfile['tmp_name'])) {
			$this->error("插件上传失败",U('Plugin/install'));
		}
		$pluginExt=strtolower(pathinfo($zipfile['name'],  PATHINFO_EXTENSION));
		if($pluginExt!='zip'){
			$this->error("只支持zip压缩格式的插件包",U('Plugin/install'));
		}
		//判断插件目录是否存在
		$pluginPath='./Public/Plugins';
		if (!file_exists($pluginPath)){
			$this->error("插件目录(./Public/Plugins)不存在",U('Plugin/install'));
		}
		//检测插件目录是否可写
		if($this->checkDirWrite($pluginPath)==false){
			$this->error("插件目录(./Public/Plugins)不可写，请修改该目录权限",U('Plugin/install'));
		}
		$tempFile=$zipfile['tmp_name'];

		//解压并覆盖本地上传的插件
		if($this->UnPluginZip($tempFile,$pluginPath)==1){
			$this->success('插件安装成功，请激活使用',U('Plugin/index'));
		}elseif($this->UnPluginZip($tempFile,$pluginPath)==2){
			$this->error("空间不支持zip模块,建议加载zip模块或者按照提示手动安装插件",U('Plugin/install'));
		}elseif($this->UnPluginZip($tempFile,$pluginPath)==0){
			$this->error("安装失败,请按照提示手动安装插件",U('Plugin/install'));
		}elseif($this->UnPluginZip($tempFile,$pluginPath)==3){
			$this->error("安装失败，插件安装包不符合标准",U('Plugin/install'));
		}else{
			$this->error("安装失败,请按照提示手动安装插件",U('Plugin/install'));
		}
	}
	//从官网下载插件
	public function downloadPlugin(){
		header("Content-Type:text/html; charset=UTF-8");
		ob_end_flush();
		echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>正在下载......！</p>";
		flush();
		$down_url=$_GET['down_url'];
		$fileName=$down_url;
		$tempFile="temp.zip";
		$opts = array('http'=>array('method'=>"GET",'timeout'=>60));  
		$context = stream_context_create($opts);     
		$get_file=file_get_contents($fileName, false, $context);
		
		if($get_file){
			$put=file_put_contents($tempFile,$get_file);
		}
		$pluginPath="./Public/Plugins";
		if($put){
			if($this->checkDirWrite($pluginPath)==false){
				echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>插件目录(./Public/Plugins)不可写，请修改该目录权限！</p><br>";
				return false;
			}
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>正在安装......！</p>";
			flush();
		}else {
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>安装插件失败，请到<a href='http://www.jdcms.com/plugin.html' target='_blank'>简单CMS官方网站</a>下载插件解压后上传到./Public/Plugins目录即可</p><br>";
			return false;
		}

		//解压并覆盖官方下载的插件
		if($this->UnPluginZip($tempFile,$pluginPath)==1){
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>安装完成！</p><br>";
			@unlink("./".$tempFile);	
		}elseif($this->UnPluginZip($tempFile,$pluginPath)==2){
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>安装插件失败，请开启zip模块或者到<a href='http://www.jdcms.com/plugin.html' target='_blank'>简单CMS官方网站</a>下载插件,解压后上传到./Public/Plugins目录即可</p><br>";
		}elseif($this->UnPluginZip($tempFile,$pluginPath)==0){
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>安装插件失败，请到<a href='http://www.jdcms.com/plugin.html' target='_blank'>简单CMS官方网站</a>下载插件,解压后上传到./Public/Plugins目录即可</p><br>"; 
		}else{
			echo "<p style='color:#0099CC;font-size:14px;line-height:20px;'>安装插件失败，请到<a href='http://www.jdcms.com/plugin.html' target='_blank'>简单CMS官方网站</a>下载插件,解压后上传到./Public/Plugins目录即可</p><br>"; 
		}
		
	}
	//检查文件是否可写
	public function checkDirWrite($Path){
		$file_test=@fopen($Path.'/test.txt','w');
		if(!$file_test){
			return false;
		}else{
			@fclose($file_test);
			@unlink($Path.'/test.txt');
			return true;
		}
		
	}
	public function UnPluginZip($tempFile,$Path){
		@$zip = new ZipArchive();
		if (!$zip){
			return 2;
		}
		if ($zip->open($tempFile)) {
			$r = explode('/', $zip->getNameIndex(0), 2);
			$dir = isset($r[0]) ? $r[0] . '/' : '';
			$plugin_name = substr($dir, 0, -1);//获取插件的名字
			$re = $zip->getFromName($dir . $plugin_name . '.php');//获取文件内容
			preg_match("/Plugin Name:(.*)/i", $re, $pluginInfo_name);//获取插件信息里的插件名称
			$pluginInfo_name = isset($pluginInfo_name[1]) ? trim($pluginInfo_name[1]) : '';
			//如果插件该插件里没有pluginName.php文件,或者,有该文件但是信息里没有名称参数，测试为不符合标准插件
			if (false === $re || empty($pluginInfo_name)){
				return 3;
			}
			$res = $zip->extractTo($Path);
			if ($res == true){		
				return 1;
			}else {
				return 0;
			}
			return $zip->close();
		} else {
			return 0;
		}
	}
}
