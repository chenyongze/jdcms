<?php
class CateAction extends BaseAction{
	public function index(){
		$this->assign("sty",array('index','style1'));
		$id =intval($_GET['id']);
		$sortby=$_GET['sortby'];
		$price=isset($_GET['price'])?intval($_GET['price']):0;
		
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
		$items_cate_mod=M("ItemsCate");//创建分类模型
		$items_tags_mod=M("ItemsTags");//创建标签模型
		$errinfo = false;
		//只显示顶级分类的单品
		$cate_info = $items_cate_mod->where("id = $id and is_del = 0")->find();
		if(isset($cate_info['pid'])){
			$pid = $cate_info['pid'];
			if(!$pid){  //一级分类
				$scate_info=$items_cate_mod->where("pid = $id and is_del = 0")->order("ord desc")->limit(10)->select(); //二级分类
				foreach($scate_info as $ksi=>$vsi){ //获取二级分类id
					$snav[$vsi['id']] = $vsi;
					$scate_id_arr[]=$vsi['id'];
					$scate_id_str=implode(",",$scate_id_arr);
				}
				$tags_info = $items_tags_mod->field('id,name,item_nums,sid')->where("sid in ($scate_id_str) and is_del = 0")->order("ord desc")->limit('0,200')->select(); //标签
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
		
		//推广下一件
		if (C('spread_status')){
			$spread_content=$this->item_spread();
			preg_match('/<script(.*?)]/si',$spread_content,$matches);
			$spread_info=trim($matches[0],']');
			$spread_info=str_replace('广告品类',$cate_info['name'],$spread_info);
			$this->assign('spread_info',$spread_info);
		}
	
		//获取分类内容
		$items_mod=M("Items");//创建单品模型
		import("@.ORG.FallPage"); // 导入分页类
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
		$items_list = $items_mod->where($where)->field($field)->order($sql_order)->limit(($Page->firstRow*5).','.$Page->listRows)->select();

		//用户相关信息包括共享，评论
		$this->items_list($items_list);
		$this->assign('page',$show); // 赋值分页输出
		
		//替换模板seo的值
		$seo['title']=!empty($cate_info['seo_title']) ? $cate_info['seo_title'] : $cate_info['name'];
		$seo['title'].="-".C("site_name");
		$seo['keys']=!empty($cate_info['seo_keys']) ? $cate_info['seo_keys'] : C("site_keyword");
		$seo['desc']=!empty($cate_info['seo_desc']) ? $cate_info['seo_desc'] : C("site_description");
		$this->assign("seo",$seo);
		
		//店铺信息
		$this->shop_list();
 		$this->display();
	}
	public function tag(){
		$this->assign("sty",array('index','style1'));
		$items_cate_mod=M("ItemsCate");//创建分类模型
		$items_tags_mod=M("ItemsTags"); 
		$items_tags_item_mod=M("Items_tags_item");
		$id =intval($_GET['id']);
		$sortby=$_GET['sortby'];
		$price=isset($_GET['price'])?intval($_GET['price']):0;
		
		if(!$sortby){
			$sql_order="ord desc,i.add_time desc";
			$sortby='new';
		}elseif($sortby=="new"){
			$sql_order="ord desc,i.add_time desc";
		}elseif($sortby=="hot"){
			$sql_order="ord desc,i.likes desc";
		}else{
			get_404();
		}
		//tag名称
		$tag_info=$items_tags_mod->where("id=$id and is_del=0")->Field("name,pid,seo_title,seo_keys,seo_desc")->find();

		$cid = $tag_info['pid'];
		$tag_name = $tag_info['name'];
		
		//推广
		if (C('spread_status')){
			$spread_content=$this->item_spread();
			preg_match('/<script(.*?)]/si',$spread_content,$matches);
			$spread_info=trim($matches[0],']');
			$spread_info=str_replace('广告品类',$tag_info['name'],$spread_info);
			$this->assign('spread_info',$spread_info);
		}
		
		//找到对应顶级分类
		$cate_info = $items_cate_mod -> where("id = $cid and is_del=0 and pid=0")->find();
		if(!$cate_info){
			echo '没有对应顶级分类';
			exit;
		}
		$errinfo = false;
		if(isset($cate_info['pid'])){
			$pid = $cate_info['pid'];
			if(!$pid){  //一级分类
				$scate_info=$items_cate_mod->where("pid = $cid and is_del = 0")->order("ord desc")->limit(10)->select(); //二级分类
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
		$this->assign('id',$id);
		$this->assign('tag_id',$id);
		$this->assign('tag_name',$tag_name);

		$items_mod=M("Items");	
		//具体tag单品
		import("@.ORG.FallPage"); // 导入分页类		
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
		$count=$items_tags_item_mod->join($join)->where($where)->count(); // 查询满足要求的总记录数
		$Page= new Page($count,20); // 实例化分页类传入总记录数和每页显示的记录数
		$show =$Page->show(); // 分页显示输出
		$items_list=$items_tags_item_mod->join($join)->order($sql_order)->where($where)->limit(($Page->firstRow*5).','.$Page->listRows)->select();

		//用户相关信息包括共享，评论
		$this->items_list($items_list);
		//替换模板seo的值
		$seo['title']=!empty($tag_info['seo_title']) ? $tag_info['seo_title'] : $tag_info['name'];
		$seo['title'].="-".C("site_name");
		$seo['keys']=!empty($tag_info['seo_keys']) ? $tag_info['seo_keys'] : C("site_keyword");
		$seo['desc']=!empty($tag_info['seo_desc']) ? $tag_info['seo_desc'] : C("site_description");
		$this->assign("seo",$seo);
		$this->assign('page',$show); // 赋值分页输出

		//店铺信息
		$this->shop_list();
		$this->display('Cate:index');
	}
}