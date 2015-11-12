<?php
class Vancl extends BaseAction{

	//淘宝收集数据并返回到页面
	public function item($vancl_url){

		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');

		//获得URL
		$url=$vancl_url;
		$item = file_get_contents($url);

		//获得商品名称
		$title=$this->match_title($item);
		if ($title == ''){
			return false;
			exit();
		}
		
		//获得商品图片
		$img=$this->match_image($item);
		
		//获得商品来源
		$domain=gain_domain($url);
		$site = $items_site->field('id,alias')->where("site_domain='".$domain."'")->find();

		//获得商品价格
		$price=$this->match_price($item);

		//获得标签
		$tags=$this->get_tags($title);

		//获得item_key
		$item_id=$this->match_item_id($item);
		$item_key=$site['alias'].'_'.$item_id;
		
		//Ajax返回数据
		$data['title']=strip_tags($title);
		$data['url']=$url;
		$data['tags']=implode(' ',$tags);
		$data['price']=$price;
		$data['item_key']=$item_key;
		$data['sid']=$site['id'];
		$data['alias']=$site['alias'];
		$data['cid']='';
		$data['img']=$img;
		return $data;
	}
	
	//淘宝批量收集数据并添加到数据库
	public function betchitem($vancl_url,$cate_id){
	
		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');
		$user = M('User');
	
		$url=$vancl_url;
		$item = file_get_contents($url);

		$add_time=time();
		$cid=$cate_id;	

		//获得商品名称
		$title=$this->match_title($item);
		if ($title == ''){
			return false;
			exit();
		}
		
		//获得商品图片
		$img=$this->match_image($item);
		
		//获得商品来源
		$domain=gain_domain($url);
		$site = $items_site->field('id,alias')->where("site_domain='".$domain."'")->find();

		//获得商品价格
		$price=$this->match_price($item);

		//获得标签
		$tags=$this->get_tags($title);

		//获得item_key
		$item_id=$this->match_item_id($item);
		$item_key=$site['alias'].'_'.$item_id;
		
		//下载远程图片
		if (C('down_status')==1){
			$type = end(explode( '.', $img ));
			$img=$this->down_item($img, $item_key.'.'.$type);
		}
	
		//获取随机uid
		$user_info=$user->field(id)->where('is_sys=1')->order('rand()')->find();
		
		//审核状态
		$status=(C('items_status')+1)%2;

	
		//数据
		$data['title']=strip_tags($title);
		$data['url']=$url;
		$data['tag']=$tags;
		$data['price']=$price;
		$data['item_key']=$item_key;
		$data['sid']=$site['id'];
		$data['cid']=$cid;
		$data['img']=$img;
		$data['add_time']=$add_time;
		$data['uid']=$user_info['id'];
		$data['status']=$status;
		
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
		if (!$new_item_id){
			return false;
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
				$isset_id = $items_tags->field('id')->where("name='".$tag."' and pid='".$cid."'")->find();
				if ($isset_id) {
					$items_tags_item->add(array(
							'item_id' => $new_item_id,
							'tag_id' => $isset_id['id'],
					));
					$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
				} else {
					$tag_id = $items_tags->add(array('name' => $tag,'pid' => $cid));
					$items_tags_item->add(array(
							'item_id' => $new_item_id,
							'tag_id' => $tag_id
					));
					$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
				}
			}
		}
		return true;
	}
	
	//正则获取商品名称
	public function match_title($content){
		preg_match('/<div id=\"productTitle\" class=\"danpinTitleTab\">(.*?)<h2>(.*?)<\/h2>/si',$content,$result);
		isset($result) ? $title = trim(addslashes($result[2])) : $title = '';
		return $title;
	}
	
	//正则获取商品图片
	public function match_image($content){
		preg_match('/<img id=\"midimg\" src=\"(.*?)\"/si',$content,$result);
		isset($result) ? $image = $result[1] : $image = '';
		return $image;
	}
	
	//正则获取商品价格
	public function match_price($content){
		preg_match('/<div class=\"cuxiaoPrice\">(.*?)<strong>(.*?)<\/strong>/si',$content,$result);
		isset($result) ? $price = $result[2] : $price = '';
		return $price;
	}
	
	//正则获取商品ID
	public function match_item_id($content){
		preg_match('/<span id=\"productcode\">(.*?)(\d+)<\/span>/si',$content,$result);
		isset($result) ? $item_id = $result[2] : $item_id = '';
		return $item_id;
	}
}