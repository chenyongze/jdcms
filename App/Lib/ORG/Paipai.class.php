<?php
class Paipai extends BaseAction{

	//淘宝收集数据并返回到页面
	public function item($paipai_url){

		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');


		//根据url获取itemId
		$item_id=$this->match_item_id($paipai_url);
		$url="http://qgo.3g.qq.com/g/s?sid=AamMBKFFh5zrxzxW8e8Q64ls&pps1=0&pps2=0&pps3=0&aid=details_index&itemId=".$item_id;
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
		$domain=gain_domain($paipai_url);
		$site = $items_site->field('id,alias')->where("site_domain='".$domain."'")->find();
		
		//获得商品价格
		$price=$this->match_price($item);

		//获得标签
		$tags=$this->get_tags($title);
		
		//获得item_key
		$item_key=$site['alias'].'_'.$item_id;

		//Ajax返回数据
		$data['title']=strip_tags($title);
		$api_url=$this->get_url($item_id);
		if($api_url){
			$data['url']=$api_url;
		}else{
			$data['url']=$paipai_url;
		}
		$data['tags']=implode(' ',$tags);
		$data['price']=$price;
		$data['item_key']=$item_key;
		$data['sid']=$site['id'];
		$data['alias']=$site['alias'];
		$data['cid']='';
		$data['img']=$img;
		return $data;
	}
	
	//拍拍批量收集数据并添加到数据库
	public function betchitem($paipai_url,$cate_id){
	
		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');
		$user = M('User');
	
		//$url=$paipai_url;
		//$item = file_get_contents($url);

		//根据url获取itemId
		$item_id=$this->match_item_id($paipai_url);
		$url="http://qgo.3g.qq.com/g/s?sid=AamMBKFFh5zrxzxW8e8Q64ls&pps1=0&pps2=0&pps3=0&aid=details_index&itemId=".$item_id;
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
		$domain=gain_domain($paipai_url);
		$site = $items_site->field('id,alias')->where("site_domain='".$domain."'")->find();
		
		//获得商品价格
		$price=$this->match_price($item);
		
		//获得标签
		$tags=$this->get_tags($title);
		
		//获得item_key
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
		$api_url=$this->get_url($item_id);
		if($api_url){
			$data['url']=$api_url;
		}else{
			$data['url']=$paipai_url;
		}
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
		preg_match('/<card title=\"(.*?)\"/si',$content,$result);
		isset($result) ? $title = trim(addslashes($result[1])) : $title = '';
		//return $title=iconv('GBK', 'UTF-8', $title);
		return $title;
	}
	
	//正则获取商品价格
	public function match_price($content){
		preg_match('/拍拍价:<b>(.*?)<\/b>/si',$content,$result);
		isset($result) ? $price = $result[1] : $price = '';
		return $price;
	}
	
	//正则获取商品图片
	public function match_image($content){
		preg_match_all('/商品详情 -->(.*?)<img src=\"(.*?)\"/si',$content,$result);
		isset($result) ? $image = $result[2][0] : $image = '';
		return str_replace("160x160.","",$image);
	}
	
	//正则获取商品ID
	public function match_item_id($url){
		$id = '';
		$parse = parse_url($url);
		
		if(isset($parse['path']))
		{
			$parse = explode('/',$parse['path']);
			$parse = end($parse);
			$parse = explode('-',$parse);
			$item_id = current($parse);
        }
		return $item_id;
	}	
	//获取推广链接地址
	public function get_url($item_id){
		$pid=C('paipaike_pid');
		if(!$pid){
			return false;
			exit();
		}
		$api_url="http://api.paipai.com/cps/cpsCommQueryAction.xhtml?charset=utf-8&format=xml&commId=".$item_id."&userId=".$pid;
		$content=file_get_contents($api_url);
		preg_match_all('/<sClickUrl>(.*?)<\/sClickUrl>/si',$content,$re);
		isset($re) ? $url = $re[1][0] : $url = '';
		if($url){
			$url=str_replace("amp;","",$url);
		}else{
			$url="http://auction1.paipai.com/".$item_id;
		}
		return $url;
	}
}