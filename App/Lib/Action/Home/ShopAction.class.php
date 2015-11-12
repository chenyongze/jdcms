<?php
class ShopAction extends BaseAction{
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
		$shop_mod=M("Shop");//创建分类模型
		$shop_info=$shop_mod->where("id=$id")->find();
		
		//获取分类内容
		$items_mod=M("Items");//创建单品模型
		import("@.ORG.FallPage"); // 导入分页类
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
		$items_list = $items_mod->where($where)->field($field)->order($sql_order)->limit(($Page->firstRow*5).','.$Page->listRows)->select();

		//用户相关信息包括共享，评论
		$this->items_list($items_list);
		$this->assign('page',$show); // 赋值分页输出
		$this->assign('shop',$shop_info); // 店铺信息输出
		//替换模板seo的值
		$seo['title']=!empty($shop_info['seo_title']) ? $shop_info['seo_title'] : $shop_info['name'];
		$seo['title'].="-".C("site_name");
		$seo['keys']=!empty($shop_info['seo_keys']) ? $shop_info['seo_keys'] : C("site_keyword");
		$seo['desc']=!empty($shop_info['seo_desc']) ? $shop_info['seo_desc'] : C("site_description");
		$this->assign("seo",$seo);

 		$this->display();
	}

}