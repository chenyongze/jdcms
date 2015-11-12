<?php
class PublicAction extends BaseAction{
	public function index(){
		$this->assign("sty",array('index','style1'));
		$id =intval($_GET['id']);
		$sortby=$_GET['sortby'];
		$price=isset($_GET['price'])?intval($_GET['price']):0;
		
		if(!$sortby){
			$sql_order="add_time desc";
			$sortby='new';
		}elseif($sortby=="new"){
			$sql_order="add_time desc";
		}elseif($sortby=="hot"){
			$sql_order="likes desc";
		}else{
			get_404();
		}
		$items_cate_mod=M("ItemsCate");//创建分类模型
		$items_tags_mod=M("ItemsTags");//创建标签模型
		$errinfo = false;
		//只显示顶级分类的单品
		$cate_info = $items_cate_mod->where("id = $id and is_del = 0")->find();
		if(isset($cate_info['pid'])){
			$pid = $cate_info['pid'];
			if(!$pid){  //一级分类
				$scate_info=$items_cate_mod->where("pid = $id and is_del = 0")->order("ord desc")->limit(10)->select(); //二级分类
				foreach($scate_info as $ksi=>$vsi){ //获取耳机分类id
					$snav[$vsi['id']] = $vsi;
					$scate_id_arr[]=$vsi['id'];
					$scate_id_str=implode(",",$scate_id_arr);
				}
				$tags_info = $items_tags_mod->field('id,name,item_nums,sid')->where("sid in ($scate_id_str) and is_del = 0")->order("ord desc")->limit(200)->select(); //标签
				foreach($tags_info as $kti=>$vti){
					$snav[$vti['sid']]['tags'][] = $vti;
				}
				$this->assign("snav",$snav);
			}else{ //其他分类不被支持
				$errinfo = true;
			}
		}else{
			$errinfo = true;
		}
		if($errinfo){ //存在错误时
			get_404();
		}
		$this->assign("cate_name",$cate_info['name']);//获取相应分类名称
		$this->assign("id",$id);
		
		
		//获取分类内容
		$items_mod=M("Items");//创建单品模型
		import("ORG.Util.Page"); // 导入分页类
		if($price=="0"){
			$where = "1=1 and cid = $id and is_del = 0 and status = 1 ";
		}else{
			if($price=="100"){
				$sql_price="price <= 100";
			}elseif($price=="200"){
				$sql_price="price between 101 and 200";
			}elseif($price=="500"){
				$sql_price="price between 201 and 500";
			}elseif($price=="501"){
				$sql_price="price >500";
			}else{
				get_404();
			}
			$where = "1=1 and cid = $id and is_del = 0 and status = 1 and ".$sql_price;
		}
		$this->assign("sortby",$sortby);
		$this->assign("price",$price);
		$count=$items_mod->where($where)->count(); // 查询满足要求的总记录数
		$Page= new Page($count,20); // 实例化分页类传入总记录数和每页显示的记录数
		$show =$Page->show(); // 分页显示输出
		$field = "id,cid,title,price,img,url,uid,sid,likes,comments,add_time";
		$items_list = $items_mod->where($where)->field($field)->order($sql_order)->limit($Page->firstRow.','.$Page->listRows)->select();
		$uid=$_COOKIE['id'];
		if($uid){
			foreach($items_list as $kil=>$vil){
				$likeItemId[] = $vil['id'];
			}
			$itemIdstr = implode(',',$likeItemId);
//			获取用户喜欢
			$items_likes_mod=M("ItemsLikes");
			$likesarr = $items_likes_mod->where("items_id in (".$itemIdstr.") and uid = ".$uid." and is_del=0")->select();
			if(is_array($likesarr)){
				foreach ($likesarr as $kla => $vla){
					$likes[$vla['items_id']] = 1;
				}
			}
			$this->assign('likes',$likes);
		}
		//用户相关信息包括共享，评论
		$this->items_list($items_list);
		$this->assign('page',$show); // 赋值分页输出
		
		//替换模板seo的值
		$seo['title']=!empty($cate_info['seo_title']) ? $cate_info['seo_title'] : $cate_info['name'];
		$seo['title'].="-".C("site_name");
		$seo['keys']=!empty($cate_info['seo_keys']) ? $cate_info['seo_keys'] : C("site_keyword");
		$seo['desc']=!empty($cate_info['seo_desc']) ? $cate_info['seo_desc'] : C("site_description");
		$this->assign("seo",$seo);
		
		$this->display('itemsList');
	}
	
	public function tag(){
		$this->assign("sty",array('index','style1'));
		$items_cate_mod=M("ItemsCate");//创建分类模型
		$items_tags_mod=M("ItemsTags"); 
		$items_tags_item_mod=M("Items_tags_item");
		$id =intval($_GET['id']);
		$this->assign("tag_id",$id);
		$sortby=$_GET['sortby'];
		$price=isset($_GET['price'])?intval($_GET['price']):0;
		
		if(!$sortby){
			$sql_order="i.add_time desc";
			$sortby='new';
		}elseif($sortby=="new"){
			$sql_order="i.add_time desc";
		}elseif($sortby=="hot"){
			$sql_order="i.likes desc";
		}else{
			get_404();
		}
		//tag名称
		$tag_info=$items_tags_mod->where("id=$id and is_del=0")->Field("name,pid,seo_title,seo_keys,seo_desc")->find();
		if(!$tag_info){
			get_404();
		}
		$cid = $tag_info['pid'];
		$tag_name = $tag_info['name'];
		//找到对应顶级分类
		$cate_name = $items_cate_mod -> where("id = $cid and is_del=0 and pid=0")->getField("name");
		if(!$cate_name){
			echo '没有对应顶级分类';
			exit;
		}
		$this->assign("id",$cid);
		$this->assign("tag_name",$tag_name);
		$this->assign("cate_name",$cate_name);
		$items_mod=M("Items");	
		//具体tag单品
		import("ORG.Util.Page"); // 导入分页类		
		$join = " ".C("db_prefix")."items as i on i.id=".C("db_prefix")."items_tags_item.item_id";
		if($price=="0"){
			$where = "tag_id=$id and i.is_del=0 and i.status=1";
		}else{
			if($price=="100"){
				$sql_price="i.price <= 100";
			}elseif($price=="200"){
				$sql_price="i.price between 101 and 200";
			}elseif($price=="500"){
				$sql_price="i.price between 201 and 500";
			}elseif($price=="501"){
				$sql_price="i.price >500";
			}else{
				get_404();
			}
			$where = "tag_id=$id and i.is_del=0 and i.status=1 and ".$sql_price;
		}
		$this->assign("sortby",$sortby);
		$this->assign("price",$price);
		$count=$items_tags_item_mod->join($join)->order("i.add_time desc")->where($where)->count(); // 查询满足要求的总记录数
		$Page= new Page($count,20); // 实例化分页类传入总记录数和每页显示的记录数
		$show =$Page->show(); // 分页显示输出
		$items_list=$items_tags_item_mod->join($join)->order($sql_order)->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		//用户相关信息包括共享，评论
		$this->items_list($items_list);
		//替换模板seo的值
		$seo['title']=!empty($tag_info['seo_title']) ? $tag_info['seo_title'] : $tag_info['name'];
		$seo['title'].="-".C("site_name");
		$seo['keys']=!empty($tag_info['seo_keys']) ? $tag_info['seo_keys'] : C("site_keyword");
		$seo['desc']=!empty($tag_info['seo_desc']) ? $tag_info['seo_desc'] : C("site_description");
		$this->assign("seo",$seo);
		$this->assign('page',$show); // 赋值分页输出
		
		$this->display('itemsList');
	}
	
	public function search(){
		$this->assign("sty",array('index','style1'));//需要显示的样式
		$url_model=C("url_model");
		$keywords = isset($_REQUEST['keywords']) && trim($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) :'';
		$sortby = isset($_REQUEST['sortby']) && trim($_REQUEST['sortby']) ? trim($_REQUEST['sortby']) : '';

		if(!$sortby){
			$keywords = isset($_GET['keywords']) && trim($_GET['keywords']) ? trim($_GET['keywords']) :'';		
			if($keywords!=""){
				$sql_where = "status='1' and is_del='0' and title LIKE '%$keywords%'";
			}else{
				$sql_where = "status='1' and is_del='0'";
			}
		}else{
			if($sortby=="likes"){
				$sql_order="likes desc";
				if($keywords!=""){
					$sql_where = "status='1' and is_del='0' and title LIKE '%$keywords%'";
				}else{
					$sql_where = "status='1' and is_del='0'";
				}
			}elseif($sortby=="time"){
				$sql_order="add_time desc";
				if($keywords!=""){
					$sql_where = "status='1' and is_del='0' and title LIKE '%$keywords%'";
				}else{
					$sql_where = "status='1' and is_del='0'";
				}
			}else{
				get_404();
			}
		}
		$items_mod=M("Items");
		import("ORG.Util.Page"); // 导入分页类
		$count=$items_mod->where($sql_where)->count();
		$Page= new Page($count,20); // 实例化分页类传入总记录数和每页显示的记录数
		$show =$Page->show(); // 分页显示输出
		$field = "id,cid,title,price,img,url,uid,sid,likes,comments,add_time";
		$items_list=$items_mod->where("$sql_where")->field($field)->order($sql_order)->limit($Page->firstRow.','.$Page->listRows)->select();
		$uid=$_COOKIE['id'];
		if($uid){
			foreach($items_list as $kil=>$vil){
				$likeItemId[] = $vil['id'];
			}
			$itemIdstr = implode(',',$likeItemId);
//			获取用户喜欢
			$items_likes_mod=M("Items_likes");
			$likesarr = $items_likes_mod->where("items_id in (".$itemIdstr.") and uid = ".$uid." and is_del=0")->select();
			if(is_array($likesarr)){
				foreach ($likesarr as $kla => $vla){
					$likes[$vla['items_id']] = 1;
				}
			}
			$this->assign('likes',$likes);
		}
		//用户相关信息包括共享，评论
		$this->items_list($items_list);
		$this->assign('page',$show); // 赋值分页输出
		$this->assign("count",$count);
		$this->assign('sortby', $sortby);
		$this->assign("keywords",$keywords);
		
		$this->display('itemsList');
	}
	
	public function shop(){
		$this->assign("sty",array('index','style1'));
		$id =$_GET['id'];
		$sortby=$_GET['sortby'];
		$price=isset($_GET['price'])?$_GET['price']:0;
		
		if(!$sortby){
			$sql_order="ord desc,add_time desc";
			$sortby='new';
		}elseif($sortby=="new"){
			$sql_order="ord desc,add_time desc";
		}elseif($sortby=="hot"){
			$sql_order="ord desc,likes desc";
		}else{
			get_404();
		}
		$shop_mod=M("Shop");//创建分类模型
		$shop_info=$shop_mod->where("id=$id")->find();
		
		//获取分类内容
		$items_mod=M("Items");//创建单品模型
		import("ORG.Util.Page"); // 导入分页类
		if($price=="0"){
			$where = "seller_id = ".$shop_info['seller_id']." and is_del = 0 and status = 1 ";
		}else{
			if($price=="100"){
				$sql_price="price <= 100";
			}elseif($price=="200"){
				$sql_price="price between 101 and 200";
			}elseif($price=="500"){
				$sql_price="price between 201 and 500";
			}elseif($price=="501"){
				$sql_price="price >500";
			}else{
				get_404();
			}
			$where = "seller_id = ".$shop_info['seller_id']." and is_del = 0 and status = 1 and ".$sql_price;
		}
		$this->assign("sortby",$sortby);
		$this->assign("price",$price);
		$count=$items_mod->where($where)->count(); // 查询满足要求的总记录数
		$Page= new Page($count,20); // 实例化分页类传入总记录数和每页显示的记录数
		$show =$Page->show(); // 分页显示输出
		$field = "id,cid,title,price,img,url,uid,sid,likes,comments,add_time";
		$items_list = $items_mod->where($where)->field($field)->order($sql_order)->limit($Page->firstRow.','.$Page->listRows)->select();

		//用户相关信息包括共享，评论
		$this->items_list($items_list);
		
		//替换模板seo的值
		$seo['title']=!empty($shop_info['seo_title']) ? $shop_info['seo_title'] : $shop_info['name'];
		$seo['title'].="-".C("site_name");
		$seo['keys']=!empty($shop_info['seo_keys']) ? $shop_info['seo_keys'] : C("site_keyword");
		$seo['desc']=!empty($shop_info['seo_desc']) ? $shop_info['seo_desc'] : C("site_description");
		$this->assign("seo",$seo);
		$this->assign('page',$show); // 赋值分页输出
 		$this->display('itemsList');
	}
}