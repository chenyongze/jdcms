<?php
class ShopAction extends BaseAction{
	
	//列表页
	public function index(){

		$shop = M('Shop');
	
		//搜索功能
		$where = 'is_del=0';
		$keyword = isset($_GET['keyword']) && trim($_GET['keyword']) ? trim($_GET['keyword']) : '';
		if ($keyword) {
			$where .= " AND name LIKE '%" . $keyword . "%'";
			$this->assign('keyword', $keyword);
		}
		
		$order_str = 'ord desc'; //默认排序
		
		import("ORG.Util.Page");
		$count=$shop->where($where)->count();
		$page=new Page($count,20);
		$show=$page->show();
		$list=$shop->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();
		
		foreach ($list as $key=>$val){
			$list[$key]['key']= ++$page->firstRow;
		}
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	
	//获得店铺信息
	public function collect(){
	
		if (isset($_POST['url'])){
				
			header("Content-type: text/xml; charset=utf-8");

			//获取商品URL
			$url=trim($_POST['url']);
			$url=match_url($url);
			$fp = fopen($url, 'r');
			if (!$fp){
				$this->ajaxReturn(false);
			}
			fclose($fp);

			$file = fopen($url,'r');
			$content = fread($file,'5120');
			preg_match('/userid=(\d+)?/si',$content,$result);
			isset($result) ? $seller_id = $result[1] : $seller_id = '';
			unset($result);
			
			preg_match('/"user_nick": "(.*?)"/si',$content,$result);
			isset($result) ? $nick = urldecode($result[1]) : $nick = '';
			unset($result);
			
// 			preg_match('/shopId=(\d+)?/si',$content,$result);
// 			isset($result) ? $shop_id = $result[1] : $shop_id = '';
// 			unset($result);

// 			preg_match('/<title>(.*)<\/title>/si',$content,$result);
// 			if ($result){
// 				$result=explode('-',mb_convert_encoding($result[1], 'UTF-8', 'GBK'));
// 				$name=$result[1];
// 			}else {
// 				$name='';
// 			}
			fclose($file);

			import("@.ORG.Taobao");
			$taobao=new Taobao();
			$shop_info=$taobao->taobaoShopInfo($nick);
			$shop_id=$shop_info['shop']['sid'];
			$cid=$shop_info['shop']['cid'];
			$name=$shop_info['shop']['title'];
			$img=$shop_info['shop']['pic_path'];
		
			if ($img){
				$data['img']='http://logo.taobao.com/shop-logo'.$img.'_80x1000.jpg';
			}
			$data['shop_id']=$shop_id;
			$data['seller_id']=$seller_id;
			$data['cid']=$cid;
			$data['name']=$name;
			$data['url']=$url;

			$this->ajaxReturn($data);

		}else {		
			$items_cate_mod=M("Items_cate");
			$cate_list=$items_cate_mod->where('is_del = 0 and pid=0')->order('ord desc')->select();
			$this->assign('cate_list', $cate_list);
			$this->display();
		}
		
	}
	
	//添加
	public function add(){
		if (isset($_POST['submit'])){
		
			$shop = M('Shop');
		
			if ($_POST['name']==''){
				$this->error('标题不能为空！');
			}

			$data=$shop->create();
			
			if (!$data){
				$this->error($shop->getError());
			}
			
			//下载远程图片
			if (C('down_status')==1){
				$type = end(explode( '.', $data['img'] ));
				$data['img']=$this->down_shop($data['img'], md5($data['name'].time()).'.'.$type);
			}
			
			//保存上传图片
			if ($_FILES['upload_img']['name'] != '') {
				mkdir('./Uploads/Shop/');
				$thumb=array('width'=>50,'height'=>1000);
				$upload_info = $this->upload('./Uploads/Shop/',$thumb);
				$data['img'] = C('web_path').'Uploads/Shop/s_'. $upload_info['0']['savename'];
			}
			
			$where['shop_id'] = array('eq',$_POST['shop_id']);
			$where['is_del']  = array('eq',0);

			$add_shop = $shop->where($where)->find();
			
			if ($add_shop){
				$result=$shop->where($where)->save($data);
				if ($result){
					$this->success('更新店铺信息成功！',U('Shop/index'));
				}else {
					$this->error('店铺信息无变化！',U('Shop/index'));
				}
			}else {
				$result=$shop->add($data);
				if ($result){
					$this->success('添加店铺成功！',U('Shop/index'));
				}else {
					$this->error('添加店铺失败！');
				}
			}
		}
	}
	
	//编辑
	public function edit(){

		$shop = M('Shop');
		
		if (isset($_POST['submit']) && isset($_POST['id'])){

			if ($_POST['name']==''){
				$this->error('标题不能为空！');
			}
		
			$data=$shop->create();
				
			if (!$data){
				$this->error($shop->getError());
			}
			
			//保存上传图片
			if ($_FILES['upload_img']['name'] != '') {
				mkdir('./Uploads/Shop/');
				$thumb=array('width'=>50,'height'=>1000);
				$upload_info = $this->upload('./Uploads/Shop/',$thumb);
				$data['img'] = C('web_path').'Uploads/Shop/s_'. $upload_info['0']['savename'].'';
			}
				
			$where['id'] = array('eq',$_POST['id']);
			$where['is_del']  = array('eq',0);
		
			$add_shop = $shop->where($where)->find();
				
			if ($add_shop){
				$result=$shop->where($where)->save($data);
				if ($result){
					$this->success('更新店铺信息成功！',U('Shop/index'));
				}else {
					$this->error('店铺信息无变化！',U('Shop/index'));
				}
			}
		}else {
			if (!isset($_GET['id'])){
				$this->error('请选择店铺！');
			}
			
			$shop_info = $shop->where("id='".$_GET['id']."'")->find();
			$this->assign('shop',$shop_info);
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$shop = M('Shop');
		$del_id = $_POST['id'];
		foreach ($del_id as $id){
			$data['is_del'] = 1;
			$shop->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	
	//抓去店铺商品
	public function item(){
		set_time_limit(0);
		if (!isset($_GET['seller_id'])){
			$this->error('请选择店铺！');
		}
		$seller_id=$_GET['seller_id'];
		$url='http://shop.m.taobao.com/shop/a-3-oldstarts-5-15-34-1-42-'.$seller_id.'.htm';
		$content = file_get_contents($url);
		preg_match('/\(1\/(\d+)\)/si',$content,$result);
		$result ? $page = $result[1] : $page = 1;
		$page;
		unset($content);
		unset($result);
		$urls=array();
		for ($i=1;$i<=$page;$i++){
			if ($i==1){
				$url='http://shop.m.taobao.com/shop/a-3-oldstarts-5-15-34-1-42-'.$seller_id.'.htm';
			}else {
				$nums=($i-1)*15;
				$url='http://shop.m.taobao.com/shop/a-2-'.$nums.'-3-oldstarts-5-15-34-1-42-'.$seller_id.'.htm';
			}
			$content = file_get_contents($url);
			preg_match_all('/<div><a href=\"(.*?)\">/si',$content,$result);
			$urls = array_merge($urls,$result[1]);
			unset($content);
			unset($result);
		}
		foreach ($urls as $url){
			$result=explode('?', $url);
			$url=$result[0];
			unset($result);
			$item_urls.=$url."\r\n";
		}
		
		$items_cate = M('ItemsCate');
		//分类循环
		$result = $items_cate->where('is_del=0')->order('ord desc')->select();
		$cate_list = array();
		foreach ($result as $val) {
			if ($val['pid'] == 0) {
				$cate_list['parent'][$val['id']] = $val;
			} else {
				$cate_list['sub'][$val['pid']][] = $val;
			}
		}
		
		$this->assign('seller_id',$seller_id);
		$this->assign('urls',$item_urls);
		$this->assign('cate_list',$cate_list);
		$this->display('Items:betchadd');
	}
	
	//排序
	public function order(){
	
		if ($_POST['order']){
			$items = M('Shop');
			foreach ($_POST['orders'] as $id => $ord) {
				$data['ord'] = $ord;
				$items->where('id='.$id)->save($data);
			}
			$this->success('修改成功！',U('Shop/index'));
		}
	}
}