<?php
class ConfigAction extends BaseAction{

    
    //网站信息设置
    public function base(){
    	if ($_POST['submit']){
    		
    		//保存上传图片
    		if ($_FILES['upload']['name'] != '') {
    			$file_del = './App/Tpl/Home/'.C('home.default_theme').'/Public/img/logo.png';
    			@unlink($file_del);
    			$_FILES['upload']['name'] = 'logo.PNG';
    			$this->upload('./App/Tpl/Home/'.C('home.default_theme').'/Public/img/');
    		}
    		
    		$config = $_POST["con"];
    		$config['site_status']=intval($config['site_status']);
    		$config['comments_status']=intval($config['comments_status']);
    		$config['items_status']=intval($config['items_status']);
    		$config['album_status']=intval($config['album_status']);
    		$config['down_status']=intval($config['down_status']);
    		$config['article_show']=intval($config['article_show']);
    		$this->updateconfig($config);
    	}else {
    		$this->display();
    	}
    }
    
    //访问路径设置
    public function url(){
    	if ($_POST['submit']){
    		$config = $_POST["con"];

    		if ($config[url_html] == 1){
    			$config['url_rewrite']=0;
    			$file_arr = array('./.htaccess','./httpd.ini','./nginx.rewrite.conf');
    			foreach ($file_arr as $file){
    				@unlink($file);
    			}
    		}
    		
    		if ($config[url_rewrite] == 0){
    			$config['url_model']=0;
    		}else {
    			$config['url_model']=2;
    			if (@$ht=file_get_contents('./Public/statics/htaccess.conf')){
    				$ht=str_replace('RewriteBase /', 'RewriteBase '.C('web_path'), $ht);
    				$ht=str_replace('^search', '^'.$config['url_rewrite_search'], $ht);
    				$ht=str_replace('^shop', '^'.$config['url_rewrite_shop'], $ht);
    				$ht=str_replace('^album', '^'.$config['url_rewrite_album'], $ht);
    				$ht=str_replace('^cate', '^'.$config['url_rewrite_cate'], $ht);
    				$ht=str_replace('^tag', '^'.$config['url_rewrite_tag'], $ht);
    				$ht=str_replace('^item', '^'.$config['url_rewrite_item'], $ht);
    				$ht=str_replace('^article', '^'.$config['url_rewrite_article'], $ht);
    				$ht=str_replace('^u', '^'.$config['url_rewrite_user'], $ht);
    				@file_put_contents('.htaccess', $ht);
    			}
    			if (@$httpd=file_get_contents('./Public/statics/httpd.conf')){
    				$httpd=str_replace('^/search', '^/'.$config['url_rewrite_search'], $httpd);
    				$httpd=str_replace('^/shop', '^/'.$config['url_rewrite_shop'], $httpd);
    				$httpd=str_replace('^/album', '^/'.$config['url_rewrite_album'], $httpd);
    				$httpd=str_replace('^/cate', '^/'.$config['url_rewrite_cate'], $httpd);
    				$httpd=str_replace('^/tag', '^/'.$config['url_rewrite_tag'], $httpd);
    				$httpd=str_replace('^/item', '^/'.$config['url_rewrite_item'], $httpd);
    				$httpd=str_replace('^/article', '^/'.$config['url_rewrite_article'], $httpd);
    				$httpd=str_replace('^/u', '^/'.$config['url_rewrite_user'], $httpd);
    				@file_put_contents('httpd.ini', $httpd);
    			}
    			if (@$nginx=file_get_contents('./Public/statics/nginx.rewrite.conf')){
    				$nginx=str_replace('^/search', '^/'.$config['url_rewrite_search'], $nginx);
    				$nginx=str_replace('^/shop', '^/'.$config['url_rewrite_shop'], $nginx);
    				$nginx=str_replace('^/album', '^/'.$config['url_rewrite_album'], $nginx);
    				$nginx=str_replace('^/cate', '^/'.$config['url_rewrite_cate'], $nginx);
    				$nginx=str_replace('^/tag', '^/'.$config['url_rewrite_tag'], $nginx);
    				$nginx=str_replace('^/item', '^/'.$config['url_rewrite_item'], $nginx);
    				$nginx=str_replace('^/article', '^/'.$config['url_rewrite_article'], $nginx);
    				$nginx=str_replace('^/u', '^/'.$config['url_rewrite_user'], $nginx);
    				@file_put_contents('nginx.rewrite.conf', $nginx);
    			}
    		}
			
    		$config['url_model']=intval($config['url_model']);
    		$config['url_rewrite']=intval($config['url_rewrite']);
    		$config['url_html']=intval($config['url_html']);
    		$this->updateconf($config);
    	}else {
    		$this->display();
    	}
    }
    
    //网站缓存设置
    public function cache(){
    	if ($_POST['submit']){
    		$config = $_POST["con"];
    		$config['html_cache_on']=intval($config['html_cache_on']);
    		$config['html_cache_index']=intval($config['html_cache_index']);
    		$config['html_cache_cate']=intval($config['html_cache_cate']);
    		$config['html_cache_album']=intval($config['html_cache_album']);
    		
    		if ($config['html_cache_index'] > 0) {
    			$config['html_cache_rules']['index:'] = array('{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',$config['html_cache_index']*3600);
    		}else{
    			$config['html_cache_rules']['index:'] = null;
    		}
    		
    	   	if ($config['html_cache_cate'] > 0) {
    			$config['html_cache_rules']['cate:'] = array('{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',$config['html_cache_cate']*3600);
    		}else{
    			$config['html_cache_rules']['index:'] = null;
    		}   
    				
    		if ($config['html_cache_album'] > 0) {
    			$config['html_cache_rules']['album:'] = array('{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',$config['html_cache_album']*3600);
    		}else{
    			$config['html_cache_rules']['album:'] = null;
    		}

    		$this->updateconf($config);
    	}else {
    		$this->display();
    	}
    }
    
    //文章内链设置
    public function linktag(){
    	if ($_POST['submit']){
    		$config = $_POST["con"];
    		$config['article_tags_totle']=intval($config['article_tags_totle']);
    		$config['article_tags_limit']=intval($config['article_tags_limit']);
    		$this->updateconfig($config);
    	}else {
    		$this->display();
    	}
    }

    //Appkey设置
    public function appkey(){
    	if ($_POST['submit']){
    		$config = $_POST["con"];
    		$this->updateconfig($config);
    	}else {
    		$this->display();
    	}
    }
    
    //第三方登录设置
    public function oAuth(){
    	if ($_POST['submit']){
    		$config = $_POST["con"];
    		$this->updateconfig($config);
    	}else {
    		$this->display();
    	}
    }
    
    //关注我们设置
    public function attention(){
    	if ($_POST['submit']){
    		$config = $_POST["con"];
    		$this->updateconfig($config);
    	}else {
    		$this->display();
    	}
    }

    //恢复默认LOGO
    public function logo(){
    	$file_del = './App/Tpl/Home/'.C('home.default_theme').'/Public/img/logo.png';
    	$file_copy = './App/Tpl/Home/'.C('home.default_theme').'/Public/img/logo_backup.png';
    	@unlink($file_del);
    	copy($file_copy, $file_del);
    	$this->success('恢复默认LOGO成功');
    }
}