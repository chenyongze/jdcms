<?php
// 获取相对目录
function get_base_path($filename){
	$base_path = $_SERVER['PHP_SELF'];
	$base_path = substr($base_path,0,strpos($base_path,$filename));
	return $base_path;
}
//获取商品图片连接
function get_img($img,$size){
	$type = end(explode( '.', $img));
	if(strpos($img, 'http') === false){
		return $img.'_'.$size.'x1000'.'.'.$type;
	}elseif(strpos($img, 'taobao')!==false|| strpos($img, 'tmall') !==false){
		return $img.'_'.$size.'x1000'.'.'.$type;
	}elseif(strpos($img, 'paipaiimg')!==false){
		$img_arr=explode( '.', $img);
		unset($img_arr[count($img_arr)-1]);
		$newimg_pre=implode(".",$img_arr);
		if($size==500){
			$newimg=$newimg_pre.'.'.$type;
		}elseif($size==210){
			$newimg=$newimg_pre.".200x200.".$type;
		}elseif($size==350){
			$newimg=$newimg_pre.".300x300.".$type;
		}elseif($size==100){
			$newimg=$newimg_pre.".160x160".".".$type;
		}
		return $newimg;
	}
}

//获取广告
function get_ad($id){
	$ad=M('Ad');
	$where['id']=$id;
	$where['is_del']=0;
	$data=$ad->field('start_time,end_time,code')->where($where)->find();
	$now=time();
	if ($now < $data['start_time'] || $now > $data['end_time']){
		return false;
	}else{
		return $data['code'];
	}
}

//格式化商品链接
function match_url($url){
	$url=trim($url);
	$url=urldecode($url);
	preg_match('/^(http\:\/\/)/', $url, $result);
	if (!isset($result)){
		$rs = '';
	}else{
		$rs = @$result[1];
	}
	if (strlen($rs)==0) {
		$url = "http://".$url;
	}
	return $url;
}

//获取商品来源,例如taobao、tmall...
function gain_domain($url){
	$rs= parse_url($url);
	$host = isset($rs['host']) ? $rs['host'] : "none";
	$host = explode('.',$host);
	$host = array_slice($host,-2);
	return $domain = implode('.',$host);
}

// 数组保存到文件
function arr2file($filename, $arr=''){
	if(is_array($arr)){
		$con = var_export($arr,true);
	} else{
		$con = $arr;
	}
	$con = "<?php\nreturn $con;\n?>"; //生成配置文件内容

	$dir = dirname($filename);
	if(!is_dir($dir)){
		mkdir($dir);
	}
	return @file_put_contents($filename,$con); //写入./config.php中
}

/**
 * 获取路径
 *
 * @param $module 模块
 * @param $action 操作
 * @param $id     id
 * @param $aid    aid
 * @return url    url
 */
function get_url($action, $id='', $module='', $aid='',$price=''){

	//用户相关页
	if ($module=='user'){

		//静态页
		if(C('url_html')==1 && $action=='albumDetail' && $id){
			$url = C('web_path').C('url_dir_album').'/'.$id.C('home.html_file_suffix');
			return $url;
		}

		if (C('home.url_model')==0){ //普通模式下
			if ($id && $aid==''){
				$url = U('Home/Uc/'.$action,array('id'=>$id));
			}elseif ($aid){
				$url = U('Home/Uc/'.$action,array('id'=>$id,'aid'=>$aid));
			}else {
				$url = U('Home/Uc/'.$action);
			}
		}else { //REWRITE模式下,操作为index则省略index
			if ($action=='index'){
				if ($id && $aid==''){
					$url = C('web_path').C('home.url_rewrite_user').'/'.$id;
				}elseif ($aid){
					$url = C('web_path').C('home.url_rewrite_user').'/'.$id.'/'.$aid;
				}else {
					$url = C('web_path').C('home.url_rewrite_user');
				}
			}else{
				if ($id && $aid==''){
					$url = C('web_path').C('home.url_rewrite_user').'/'.$action.'/'.$id;
				}elseif ($aid){
					$url = C('web_path').C('home.url_rewrite_user').'/'.$action.'/'.$id.'/'.$aid;
				}else {
					$url = C('web_path').C('home.url_rewrite_user').'/'.$action;
				}
			}
		}
		return $url;
	}

	//店铺相关页
	if ($module=='shop'){

		//静态页
		if(C('url_html')==1 && $aid==''){
			$url = C('web_path').C('url_dir_shop').'/'.$id.C('home.html_file_suffix');
			return $url;
		}

		if (C('home.url_model')==0){ //普通模式下
			if ($id && $aid==''){
				$url = U('Home/Shop/'.$action,array('id'=>$id));
			}elseif($aid) {
				$url = U('Home/Shop/'.$action,array('id'=>$id,'sortby'=>$aid,'price'=>$price));
			}
		}else { //REWRITE模式下,操作为index则省略index
			if ($id && $aid==''){
				$url = C('web_path').C('home.url_rewrite_shop').'/'.$id.C('home.html_url_suffix');
			}elseif($aid) {
				$url = C('web_path').C('home.url_rewrite_shop').'/'.$id.'/'.$aid.'/'.$price.C('home.html_url_suffix');
			}
		}
		return $url;
	}

	//专辑相关页
	if ($module=='album'){

		//静态页
		if(C('url_html')==1 && $action=='index'){
			if ($id){
				$url = C('web_path').C('url_dir_albumCate').'/'.$id.C('home.html_file_suffix');
			}else{
				$url = C('web_path').C('url_dir_albumCate').'/0	'.C('home.html_file_suffix');
			}
			return $url;
		}

		if (C('home.url_model')==0){ //普通模式下
			if ($id){
				$url = U('Home/Album/'.$action,array('id'=>$id));
			}else {
				$url = U('Home/Album/'.$action);
			}
		}else { //REWRITE模式下,操作为index则省略index
			if ($action=='index'){
				if ($id){
					$url = C('web_path').C('home.url_rewrite_album').'/'.$id.C('home.html_url_suffix');
				}else {
					$url = C('web_path').C('home.url_rewrite_album').C('home.html_url_suffix');
				}
			}else {
				if ($id){
					$url = C('web_path').C('home.url_rewrite_album').'/'.$action.'/'.$id.C('home.html_url_suffix');
				}else {
					$url = C('web_path').C('home.url_rewrite_album').'/'.$action.C('home.html_url_suffix');
				}
			}
		}
		return $url;
	}

	//商品相关页
	if ($module=='item'){

		//静态页
		if(C('url_html')==1 && $action=='index'){
			$url = C('web_path').C('url_dir_item').'/'.$id.C('home.html_file_suffix');
			return $url;
		}

		if (C('home.url_model')==0){ //普通模式下
			if ($id && $aid==''){
				$url = U('Home/Item/'.$action,array('id'=>$id));
			}elseif($aid){
				$url = U('Home/Item/'.$action,array('id'=>$id,'p'=>$aid));
			}else {
				$url = U('Home/Item/'.$action);
			}
		}else { //REWRITE模式下,操作为index则省略index
			if ($action=='index'){
				if ($id){
					$url = C('web_path').C('home.url_rewrite_item').'/'.$id.C('home.html_url_suffix');
				}elseif($aid){
					$url = C('web_path').C('home.url_rewrite_item').'/'.$id.'/'.$aid.C('home.html_url_suffix');
				}else {
					$url = C('web_path').C('home.url_rewrite_item').C('home.html_url_suffix');
				}
			}else {
				if ($id){
					$url = C('web_path').C('home.url_rewrite_item').'/'.$action.'/'.$id.C('home.html_url_suffix');
				}elseif($aid){
					$url = C('web_path').C('home.url_rewrite_item').'/'.$action.'/'.$id.'/'.$aid.C('home.html_url_suffix');
				}else {
					$url = C('web_path').C('home.url_rewrite_item').'/'.$action.C('home.html_url_suffix');
				}
			}
		}
		return $url;
	}

	//分类相关页
	if ($module=='cate'){

		//静态页
		if(C('url_html')==1 && $action=='index' && $aid==''){
			$url = C('web_path').C('url_dir_cate').'/'.$id.C('home.html_file_suffix');
			return $url;
		}
		//静态页
		if(C('url_html')==1 && $action=='tag' && $aid==''){
			$url = C('web_path').C('url_dir_tag').'/'.$id.C('home.html_file_suffix');
			return $url;
		}

		if (C('home.url_model')==0){ //普通模式下
			if ($id && $aid==''){
				$url = U('Home/Cate/'.$action,array('id'=>$id));
			}elseif($aid) {
				$url = U('Home/Cate/'.$action,array('id'=>$id,'sortby'=>$aid,'price'=>$price));
			}
		}else { //REWRITE模式下,操作为index则省略index
			if ($action=='index'){
				if ($id && $aid==''){
					$url = C('web_path').C('home.url_rewrite_cate').'/'.$id.C('home.html_url_suffix');
				}elseif($aid) {
					$url = C('web_path').C('home.url_rewrite_cate').'/'.$id.'/'.$aid.'/'.$price.C('home.html_url_suffix');
				}
			}else {
				if ($id && $aid==''){
					$url = C('web_path').C('home.url_rewrite_tag').'/'.$id.C('home.html_url_suffix');
				}elseif($aid) {
					$url = C('web_path').C('home.url_rewrite_tag').'/'.$id.'/'.$aid.'/'.$price.C('home.html_url_suffix');
				}
			}
		}
		return $url;
	}

	//搜索相关页
	if ($module=='search'){
		if (C('home.url_model')==0){ //普通模式下
			if ($id && $aid==''){
				$url = U('Home/Search/'.$action,array('keywords'=>$id));
			}elseif($aid) {
				$url = U('Home/Search/'.$action,array('sortby'=>$aid,'keywords'=>$id));
			}else{
				$url = U('Home/Search/'.$action);
			}
		}else { //REWRITE模式下,操作为index则省略index
			if ($id && $aid==''){
				$url = C('web_path').C('home.url_rewrite_search').'/'.$id;
			}elseif($aid) {
				$url = C('web_path').C('home.url_rewrite_search').'/'.$aid.'/'.$id;
			}else{
				$url = C('web_path').'index.php?a=index&m=Search&g=Home';
			}
		}
		return $url;
	}

	//文章相关页
	if ($module=='article'){

		//静态页
		if(C('url_html')==1){
			if($action=='index'){
				if ($id){
					$url = C('web_path').C('url_dir_articleCate').'/'.$id.C('home.html_file_suffix');
				}else{
					$url = C('web_path').C('url_dir_articleCate').'/0'.C('home.html_file_suffix');
				}
			}else {
				$url = C('web_path').C('url_dir_article').'/'.$id.C('home.html_file_suffix');
			}
			return $url;
		}

		if (C('home.url_model')==0){ //普通模式下
			if ($id){
				$url = U('Home/Article/'.$action,array('id'=>$id));
			}else{
				$url = U('Home/Article/'.$action);
			}
		}else { //REWRITE模式下,操作为index则省略index
			if($action=='index'){
				if ($id){
					$url = C('web_path').C('home.url_rewrite_article').'/list/'.$id.C('home.html_url_suffix');
				}else{
					$url = C('web_path').C('home.url_rewrite_article').'/list'.C('home.html_url_suffix');
				}
			}else {
				$url = C('web_path').C('home.url_rewrite_article').'/'.$id.C('home.html_url_suffix');
			}

		}
		return $url;
	}

	//主站相关页
	if ($module=='index'){
		if (C('home.url_model')==0){ //普通模式下
			$url = U('Home/Index/'.$action);
		}else { //REWRITE模式下,操作为index则省略index
			if ($action=='index'){
				$url = C('web_path').C('home.url_rewrite_index').C('home.html_url_suffix');
			}else {
				$url = C('web_path').C('home.url_rewrite_index').'/'.$action.C('home.html_url_suffix');
			}
		}
		return $url;
	}

}

//POST请求函数
 function post($url, $postFields = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if (is_array($postFields) && 0 < count($postFields))
		{
			$postBodyString = "";
			foreach ($postFields as $k => $v)
			{
				$postBodyString .= "$k=" . urlencode($v) . "&"; 
			}
			unset($k, $v);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
 			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
		}
		$reponse = curl_exec($ch);
		if (curl_errno($ch)){
			throw new Exception(curl_error($ch),0);
		}
		else{
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		curl_close($ch);
		return $reponse;
	}

//返回404
function get_404(){
	header('HTTP/1.1 404 Not Found'); 
	header("status: 404 Not Found"); 
	$url=get_url('notfound','','user');
	header("Location:$url");
}

/*
 * curl获取内容，且最长时间为5秒
 * */
function curHtml($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	return curl_exec($ch);
	curl_close($ch);
}

/**
 * 获取客户端IP
 */
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

//反转义
function strclean($data){
	$str=$data;
	if (ini_get('magic_quotes_gpc')) {
		return clean($data);
	}else {
		return $data;
	}
}
function clean($data) {
	if (is_array($data)) {
		foreach ($data as $key => $value) {
			$data[clean($key)] = clean($value);
		}
	} else {
		$data =stripslashes($data);
	}
	return $data;
}
//获取随机字符串加密
function str_rand(){
	$arr=array_merge(range('a', 'z'),range('A', 'Z'),range(0, 9));
	shuffle($arr);
	$str='';
	for ($i=0;$i<10;$i++){
		$str .= $arr[$i];
	}
	return $str=md5($str);
}

/*插件部分begin*/
//获取激活的插件信息（从配置文件获取）
function get_active_plugins(){	
	$config = require './App/Conf/config.php';
	$active_plugins=unserialize($config['active_plugin']);//反序列化激活的插件信息
	$cmsHooks = array();
	if ($active_plugins && is_array($active_plugins)) {
		foreach($active_plugins as $plugin) {
			if(true === checkPlugin($plugin)) {//plugin/plugin.php
				include_once(CMS_ROOT . 'Public/Plugins/' . $plugin);
			}
		}
	}
}
//检查插件
function checkPlugin($plugin) {
	if (is_string($plugin) && preg_match('/^[\w\-\/]+\.php$/', $plugin) && file_exists(CMS_ROOT . 'Public/Plugins/' . $plugin)) {
		return true;
	} else {
		return false;
	}
}
//该函数在插件中调用,挂载插件函数到预留的钩子上
function addAction($hook, $actionFunc) {
	global $cmsHooks;
	if (!@in_array($actionFunc, $cmsHooks[$hook])) {
		$cmsHooks[$hook][] = $actionFunc;
	}
	return true;
}

// 执行挂在钩子上的函数,//支持多参数 eg:doAction('post_comment', $author, $email, $url, $comment);?
function doAction($hook) {
	global $cmsHooks;
	$args = array_slice(func_get_args(), 1);
	if (isset($cmsHooks[$hook])) {
		foreach ($cmsHooks[$hook] as $function) {
			$string = call_user_func_array($function, $args);
		}
	}
}
/*插件部分end*/

