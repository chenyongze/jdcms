<?php
class PushAction extends Action{
	
	/**
     * 推送数据
     * json格式参数：id、url、title、cid、img、price、tags
     */  
    public function index(){
    	set_time_limit(0);
    	$items = D('Items');
    	$items_cate = D('ItemsCate');
    	$items_site = D('ItemsSite');
    	$items_tags = D('ItemsTags');
    	$items_tags_item = M('ItemsTagsItem');
    	$admin = M('Admin');
    	$user = M('User');

    	if ($_POST){
    		//核对请求
  			$master = $admin->where('id=1')->find();
    		if ($_POST['domain'] != "http://".$_SERVER['SERVER_NAME'].C('web_path') || $_POST['name'] != $master['user_name'] ){
    			$this->ajaxReturn('无效的请求');
    			exit();
    		}
    		 		
    		//将JSON数据转化为数组
    		if(get_magic_quotes_gpc()){
    			$data=json_decode(stripslashes($_POST['data']),true);
    		}else{
				$data=json_decode($_POST['data'],true);    		
    		}
    		if (!is_array($data)){
    			$this->ajaxReturn('数据格式错误');
    			exit();
    		}
    		$nums=0;
    		foreach ($data as $item){

	    		//获取商品URL
	    		$url=trim($item['url']);
	    		if ($url){
	    			$url=match_url($url);
	    		}else {
	    			continue;
	    		}
    		 
	    		//获得商品来源
	    		$domain=gain_domain($url);
	    		$site = $items_site->field('id,alias')->where("site_domain='".$domain."'")->find();	

	    		//获得商品图片
	    		$img=$item['img'];
	    		
	    		//获得商品item_key和商品标签
	    		$item_key=$site['alias'].'_'.$item['id'];
	    		$tags=$this->get_tags($item['title']);
	    		
	    		//下载远程图片
	    		if (C('down_status')==1){
					$type = end(explode( '.', $img ));
					$img=$this->down_item($img, $item_key.'.'.$type);
	    		}
	    		
	    		//获得淘宝客跳转链接
	    		if ($site['alias']=='taobao' || $site['alias']=='tmall'){
	    			import("@.ORG.Taobao");
	    			$taobao=new Taobao();
	    			$url = $taobao->gain_url( $site['alias'], $item['id'] );
	    			if ( !$url ) {
	    				$url='http://detail.tmall.com/item.htm?id='.$item['id'];
	    			}
	    		}
	    		
	    		//获得随机uid
				$user_info=$user->field(id)->where('is_sys=1')->order('rand()')->find();
	    		
				$data['seller_id']=$item['seller_id'];
	    		$data['title']=$item['title'];
	    		$data['url']=$url;
	    		$data['tags']=array_merge($tags,$item['tags']);
	    		$data['price']=$item['price'];
	    		$data['item_key']=$item_key;
	    		$data['sid']=$site['id'];
	    		$data['cid']=$item['cid'];
	    		$data['img']=$img;
	    		$data['add_time']=time();
	    		$data['uid']=$user_info['id'];
	    		
	    		if($data['item_key'] != ''){
	    			$where['item_key']  = array('eq',$data['item_key']);
	    		}else {
	    			$where['url']  = array('eq',$data['url']);
	    		}
	    		$where['is_del']  = array('eq',0);		
				//如果添加的商品存在，获得商品的id、cid
				$add_item = $items->field('id,cid')->where($where)->find();

	    		//商品存在则将分类中item_nums减1，不存在则添加，新的分类item_nums加1
	    		if ($add_item){
	    			$items_cate->where("id='".$add_item['cid']."'")->setDec('item_nums');
	    			$items->where($where)->save($data);
	    			$new_item_id=$add_item['id'];
	    		}else{
	    			$new_item_id=$items->add($data);
	    		}
				if ($new_item_id){
					$nums++;
				}else {
					continue;
				}
	    		$items_cate->where("id='".$data['cid']."'")->setInc('item_nums'); //分类item_nums加1
	    		
	    		//处理标签
	    		if ($add_item){
	    			//已存在商品，先将标签中item_nums减1,删除旧的标签和商品关系，添加新的标签和商品关系
	    			$old_tag=$items_tags_item->field('tag_id')->where("item_id='".$add_item['id']."'")->select();
	    			foreach ($old_tag as $tag){
	    				$items_tags->where("id='".$tag['tag_id']."'")->setDec('item_nums');
	    			}
	    			//删除标签和商品关系
	    			$items_tags_item->where("item_id='".$add_item['id']."'")->delete();
	    		}
	    		
	    		if ($tags) {
	    			//标签不存在则添加
	    			foreach ($tags as $tag) {
	    				$isset_id = $items_tags->field('id')->where("name='".$tag."' and pid='".$item['cid']."'")->find();
	    				if ($isset_id) {
	    					$items_tags_item->add(array(
	    							'item_id' => $new_item_id,
	    							'tag_id' => $isset_id['id'],
	    					));
	    					$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
	    				} else {
	    					$tag_id = $items_tags->add(array('name' => $tag,'pid' => $item['cid']));
	    					$items_tags_item->add(array(
	    							'item_id' => $new_item_id,
	    							'tag_id' => $tag_id
	    					));
	    					$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
	    				}
	    			}
	    		}	    		
	    	}
	    	echo $nums;
//   		}else { 			
//   			$this->display();
  		}
    }
    
    /**
     * 推送数据
     * json格式参数：id、url、title、cid、img、price、tags
     */
    public function album(){
    	set_time_limit(0);
    	$items = D('Items');
    	$items_cate = D('ItemsCate');
    	$items_site = D('ItemsSite');
    	$items_tags = D('ItemsTags');
    	$items_tags_item = M('ItemsTagsItem');
    	$admin = M('Admin');
    	$user = M('User');
    	$_album = M('Album');
    	$_albumCate = M('AlbumCate');
    	$_albumItems = M('AlbumItems');
    
    	if ($_POST){
    		//核对请求
    		$master = $admin->where('id=1')->find();
    		if ($_POST['domain'] != "http://".$_SERVER['SERVER_NAME'].C('web_path') || $_POST['name'] != $master['user_name'] ){
    			$this->ajaxReturn('无效的请求');
    			exit();
    		}
    			
    		//将JSON数据转化为数组
    		if(get_magic_quotes_gpc()){
    			$albumData=json_decode(stripslashes($_POST['data']),true);
    		}else{
    			$albumData=json_decode($_POST['data'],true);
    		}
    		if (!is_array($albumData)){
    			$this->ajaxReturn('数据格式错误');
    			exit();
    		}
    		$nums=0;
    		$item_id=array();
    		foreach ($albumData['item'] as $item){
    			//获取商品URL
    			$url=trim($item['url']);
    			if ($url){
    				$url=match_url($url);
    			}else {
    				continue;
    			}
    		
    			//获得商品来源
    			$domain=gain_domain($url);
    			$site = $items_site->field('id,alias')->where("site_domain='".$domain."'")->find();
    		
    			//获得商品图片
    			$img=$item['img'];
    		
    			//获得商品item_key和商品标签
    			$item_key=$site['alias'].'_'.$item['id'];
    			$tags=$this->get_tags($item['title']);
    		
    			//下载远程图片
    			if (C('down_status')==1){
    				$type = end(explode( '.', $img ));
    				$img=$this->down_item($img, $item_key.'.'.$type);
    			}
    		
    			//获得淘宝客跳转链接
    			if ($site['alias']=='taobao' || $site['alias']=='tmall'){
    				import("@.ORG.Taobao");
    				$taobao=new Taobao();
    				$url = $taobao->gain_url( $site['alias'], $item['id'] );
    				if ( !$url ) {
    					$url='http://detail.tmall.com/item.htm?id='.$item['id'];
    				}
    			}
    		
    			//获得随机uid
    			$user_info=$user->field(id)->where('is_sys=1')->order('rand()')->find();
    		
    			$data['seller_id']=$item['seller_id'];
    			$data['title']=$item['title'];
    			$data['url']=$url;
    			$data['tags']=array_merge($tags,$item['tags']);
    			$data['price']=$item['price'];
    			$data['item_key']=$item_key;
    			$data['sid']=$site['id'];
    			$data['cid']=$item['cid'];
    			$data['img']=$img;
    			$data['add_time']=time();
    			$data['uid']=$user_info['id'];
    		
    			if($data['item_key'] != ''){
    				$where['item_key']  = array('eq',$data['item_key']);
    			}else {
    				$where['url']  = array('eq',$data['url']);
    			}
    			$where['is_del']  = array('eq',0);
    			//如果添加的商品存在，获得商品的id、cid
    			$add_item = $items->field('id,cid')->where($where)->find();
    		
    			//商品存在则将分类中item_nums减1，不存在则添加，新的分类item_nums加1
    			if ($add_item){
    				$items_cate->where("id='".$add_item['cid']."'")->setDec('item_nums');
    				$items->where($where)->save($data);
    				$new_item_id=$add_item['id'];
    			}else{
    				$new_item_id=$items->add($data);
    			}
    			if ($new_item_id){
    				$nums++;
    			}else {
    				continue;
    			}
    			$items_cate->where("id='".$data['cid']."'")->setInc('item_nums'); //分类item_nums加1
    		
    			//处理标签
    			if ($add_item){
    				//已存在商品，先将标签中item_nums减1,删除旧的标签和商品关系，添加新的标签和商品关系
    				$old_tag=$items_tags_item->field('tag_id')->where("item_id='".$add_item['id']."'")->select();
    				foreach ($old_tag as $tag){
    					$items_tags->where("id='".$tag['tag_id']."'")->setDec('item_nums');
    				}
    				//删除标签和商品关系
    				$items_tags_item->where("item_id='".$add_item['id']."'")->delete();
    			}
    		
    			if ($tags) {
    				//标签不存在则添加
    				foreach ($tags as $tag) {
    					$isset_id = $items_tags->field('id')->where("name='".$tag."' and pid='".$item['cid']."'")->find();
    					if ($isset_id) {
    						$items_tags_item->add(array(
    								'item_id' => $new_item_id,
    								'tag_id' => $isset_id['id'],
    						));
    						$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
    					} else {
    						$tag_id = $items_tags->add(array('name' => $tag,'pid' => $item['cid']));
    						$items_tags_item->add(array(
    								'item_id' => $new_item_id,
    								'tag_id' => $tag_id
    						));
    						$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
    					}
    				}
    			}
   				$item_id[]=$new_item_id;
    		}
    		$album['title']=$albumData['title'];
    		$album['info']=$albumData['info'];
 			//添加随机uid
 			$user_info=$user->field(id)->where('is_sys=1')->order('rand()')->find();
 			$album['uid']=$user_info['id'];
 			//添加随机cid
 			$album_cate=$_albumCate->field(id)->where('is_del=0')->order('rand()')->find();
 			$album['cid']=$album_cate['id'];
 			$album['add_time']=time();
 			$album_id=$_album->add($album);
 			$map['pid']=$album_id;
 			foreach ($item_id as $v){
 				$map['items_id']=$v;
 				$map['add_time']=time();
 				$_albumItems->add($map);
 			}   
    	}
    }
    
    /**
     * 推送选项
     * 参数：cate(字符串，以空格隔开)、cps、price、num、auto_push
     */
    public function op(){
	   $items = D('Items');
	   $items_cate = D('ItemsCate');
	   $items_site = D('ItemsSite');
	   $items_tags = D('ItemsTags');
	   $items_tags_item = M('ItemsTagsItem');
	   $admin = M('Admin');
	   $user = M('User');
		if ($_POST){
			//核对请求
			$master = $admin->where('id=1')->find();
			if ($_POST['domain'] != "http://".$_SERVER['SERVER_NAME'].C('web_path') || $_POST['name'] != $master['user_name'] ){
				$this->ajaxReturn('无效的请求');
				exit();
			}

			$push['cate']=implode(' ',$_POST['cate']);
			$push['cps']=$_POST['cps'];
			$push['price']=$_POST['price'];
			$push['nums']=$_POST['nums'];
			$push['auto_push']=$_POST['auto_push'];
			
			$file = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<push>
<cate></cate>
<cps></cps>
<price></price>
<nums></nums>
<auto_push></auto_push>
</push> 
XML;
			$xml = simplexml_load_string($file);	
			$xml->cate=$push['cate'];
			$xml->cps=$push['cps'];
			$xml->price=$push['price'];
			$xml->nums=$push['nums'];
			$xml->auto_push=$push['auto_push'];
			$xml->asXML('./Public/statics/push.xml');
			
			$config['push_request']=json_encode($push);
			$this->updateconfig($config);		
		}
	}
	
	/**
	 * 推广选项
	 * 参数：num
	 */
	public function spread(){
		$items = D('Items');
		$items_cate = D('ItemsCate');
		$items_site = D('ItemsSite');
		$items_tags = D('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');
		$admin = M('Admin');
		$user = M('User');
		
		if ($_POST){
			//核对请求
			$master = $admin->where('id=1')->find();
			if ($_POST['domain'] != "http://".$_SERVER['SERVER_NAME'].C('web_path') || $_POST['name'] != $master['user_name'] ){
				$this->ajaxReturn('无效的请求');
				exit();
			}
			if ($_POST["spread_status"] == 0 || $_POST["spread_status"] == 1){
				$push['status']=$_POST['spread_status'];
	
				$file = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<spread>
<status></status>
<position></position>
</spread> 
XML;
				$xml = simplexml_load_string($file);	
				$xml->status=$push['spread_status'];
				$xml->position=C('spread_position');
				$xml->asXML('./Public/statics/spread.xml');
				$spread_status=$_POST['spread_status'];
				$config = array('spread_status'=>$spread_status);
				$this->updateconfig($config);
			}
		}
	}
	

	public function request(){
		$items_cate=M('ItemsCate');
		$cate=$items_cate->field('id,name')->where('pid = 0 and is_del=0')->order('ord desc')->select();
		foreach ($cate as $key=>$val){
			$cate_list[$key]=$val['name'].'-'.$val['id'].'-1';
		}
		
		$result=json_decode(C('push_request'),true);
		$push_cate=explode(' ', $result['cate']);
		foreach ($cate_list as $val){
			if (in_array($val, $push_cate)){
				$record[]=$val;
			}else {
				$val=rtrim($val,'1').'0';
				$record[]=$val;
			}
		}
		
		$push['cate']=implode(' ',$record);
		$push['cps']=$result['cps'];
		$push['price']=$result['price'];
		$push['nums']=$result['nums'];
		$push['auto_push']=$result['auto_push'];
		$file = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<push>
<cate></cate>
<cps></cps>
<price></price>
<nums></nums>
<auto_push></auto_push>
</push>
XML;
		$xml = simplexml_load_string($file);
		$xml->cate=$push['cate'];
		$xml->cps=$push['cps'];
		$xml->price=$push['price'];
		$xml->nums=$push['nums'];
		$xml->auto_push=$push['auto_push'];
		$xml->asXML('./Public/statics/push.xml');
		
		$url=C('sita_domain').'Public/statics/push.xml';
		header("Location:$url");
	}
    
    //分割标题获得标签方法
    public function get_tags($title){
    
    	Vendor('pscws4.pscws4', '' ,'.class.php');
    	$pscws = new PSCWS4();
    	$pscws->set_dict('./Public/statics/js/scws/dict.utf8.xdb');
    	$pscws->set_rule('./Public/statics/js/scws/rules.utf8.ini');
    	$pscws->set_ignore(true);
    	$pscws->send_text($title);
    	$words = $pscws->get_tops(10);
    	$tags = array();
    	foreach ($words as $val) {
    		$tags[] = $val['word'];
    	}
    	$pscws->close();
    	return $tags;
    }
  
    //保存配置信息
    public function updateconfig($config){
    	$config_old = require './App/Conf/config.php';
    	if(is_array($config) && is_array($config_old)){
			$config_new = array_merge($config_old,$config);
		}
		if(is_array($config_new)){
			arr2file('./App/Conf/config.php',$config_new);
		}
    	@unlink('./App/Runtime/~runtime.php');
    }
    
	//下载远程图片
	public function down_item($img,$name){
		$dir=date("Ymd");
		mkdir('./Uploads/LocalItems/'.$dir);
		$type = end(explode( '.', $img ));
		import("ORG.Net.Http");
		$http=new Http();
		$http->curlDownload($img.'_100x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$name.'_100x1000.'.$type);
		$http->curlDownload($img.'_210x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$name.'_210x1000.'.$type);
		$http->curlDownload($img.'_350x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$name.'_350x1000.'.$type);
		$http->curlDownload($img.'_500x1000.jpg','Uploads/LocalItems/'.$dir.'/'.$name.'_500x1000.'.$type);
		return C('web_path').'Uploads/LocalItems/'.$dir.'/'.$name;
	}
   
}