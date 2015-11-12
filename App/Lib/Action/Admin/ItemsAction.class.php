<?php
class ItemsAction extends BaseAction{

	//分页显示所有商品
	public function index(){

		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		
		//搜索功能
		$where = 'is_del=0';
		$keyword = isset($_GET['keyword']) && trim($_GET['keyword']) ? trim($_GET['keyword']) : '';
		$cate_id = isset($_GET['cate_id']) && intval($_GET['cate_id']) ? intval($_GET['cate_id']) : '';
		$time_start = isset($_GET['time_start']) && trim($_GET['time_start']) ? trim($_GET['time_start']) : '';
		$time_end = isset($_GET['time_end']) && trim($_GET['time_end']) ? trim($_GET['time_end']) : '';
		$is_index = isset($_GET['is_index']) ? intval($_GET['is_index']) : '-1';
		$status = isset($_GET['status']) ? intval($_GET['status']) : '-1';
		$order = isset($_GET['order']) && trim($_GET['order']) ? trim($_GET['order']) : '';
		$sort = isset($_GET['sort']) && trim($_GET['sort']) ? trim($_GET['sort']) : 'asc';
		if ($keyword) {
			$where .= " AND title LIKE '%" . $keyword . "%'";
			$this->assign('keyword', $keyword);
		}
		if ($cate_id) {
			$where .= " AND cid=" . $cate_id;
			$this->assign('cate_id', $cate_id);
		}
		if ($time_start) {
			$time_start_int = strtotime($time_start);
			$where .= " AND add_time>='" . $time_start_int . "'";
			$this->assign('time_start', $time_start);
		}
		if ($time_end) {
			$time_end_int = strtotime($time_end);
			$where .= " AND add_time<='" . $time_end_int . "'";
			$this->assign('time_end', $time_end);
		}

		$is_index >= 0 && $where .= " AND is_index=" . $is_index;
		$status >= 0 && $where .= " AND status=" . $status;
		$status < 0 && $where .= " AND status !=2";
		$this->assign('is_index', $is_index);
		$this->assign('status', $status);
		
		//排序功能
		if ($sort=='desc'){
			$sort='asc';
		}elseif ($sort=='asc'){
			$sort='desc';
		}
		$this->assign('order',$order);
		$this->assign('sort', $sort);
		$order_str = 'ord desc,add_time desc'; //默认排序
		if ($order) {
			$order_str = 'ord desc,'.$order . ' ' . $sort;
		}
		
		//分类循环
		$result = $items_cate->where('is_del = 0')->order('ord desc')->select();
		$cate_list = array();
		foreach ($result as $val) {
			if ($val['pid'] == 0) {
				$cate_list['parent'][$val['id']] = $val;
// 			} else {
// 				$cate_list['sub'][$val['pid']][] = $val;
			}
		}
		
		import("ORG.Util.Page");
		$count=$items->where($where)->count();
		$page=new Page($count,20);
		$show=$page->show();
		$list=$items->where($where)->order($order_str)->limit($page->firstRow.','.$page->listRows)->select();

		foreach ($list as $key=>$val){
			$data['id']=$val['cid'];
			$data['is_del']=0;
			$cate=$items_cate->field('name')->where($data)->find();
			$list[$key]['cname']=$cate['name'];
			$site=$items_site->field('site_logo')->where("id='".$val['sid']."'")->find();
			$list[$key]['site_logo']=$site['site_logo'];
			$list[$key]['key']= ++$page->firstRow;
		}
		$this->assign('is_index',$is_index);
		$this->assign('status',$status);
		$this->assign('cate_list', $cate_list);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}

	//收集商品信息
	public function collect(){
		
		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');
		
		if (isset($_POST['url'])){
			
			header("Content-type: text/xml; charset=utf-8");
			
			//获取商品URL
			$url=trim($_POST['url']);
			$url=match_url($url);
			$fp = fopen($url, 'r');
			if (!$fp){
				$this->ajaxReturn(false);
			}
			
			//获得商品来源
			$domain=gain_domain($url);
			$site = $items_site->field('id,alias')->where("site_domain='".$domain."'")->find();

			if ($site['alias'] == 'taobao' || $site['alias'] == 'tmall'){
				import("@.ORG.Taobao");
				$taobao=new Taobao();
				$this->ajaxReturn($taobao->item($url));
			}elseif ($site['alias'] == 'paipai'){
				import("@.ORG.Paipai");
				$paipai=new Paipai();
				$this->ajaxReturn($paipai->item($url));
			}elseif ($site['alias'] == 'dangdang'){
				import("@.ORG.Dangdang");
				$dangdang=new Dangdang();
				$this->ajaxReturn($dangdang->item($url));
			}elseif ($site['alias'] == 'vancl'){
				import("@.ORG.Vancl");
				$vancl=new Vancl();
				$this->ajaxReturn($vancl->item($url));
			}elseif ($site['alias'] == '360buy'){
				import("@.ORG.Jingdong");
				$jingdong=new Jingdong();
				$this->ajaxReturn($jingdong->item($url));
			}elseif ($site['alias'] == 'caomeipai'){
				import("@.ORG.Caomeipai");
				$caomeipai=new Caomeipai();
				$this->ajaxReturn($caomeipai->item($url));
			}elseif ($site['alias'] == 'mbaobao'){
				import("@.ORG.Mbaobao");
				$mbaobao=new Mbaobao();
				$this->ajaxReturn($mbaobao->item($url));
			}elseif ($site['alias'] == 'nala'){
				import("@.ORG.Nala");
				$nala=new Nala();
				$this->ajaxReturn($nala->item($url));
			}
		}else {
			
			//分类循环
			$result = $items_cate->where('is_del = 0')->order('ord desc')->select();
			$cate_list = array();
			foreach ($result as $val) {
				if ($val['pid'] == 0) {
					$cate_list['parent'][$val['id']] = $val;
				} else {
					$cate_list['sub'][$val['pid']][] = $val;
				}
			}
			
			//获得来源
			$site_list = $items_site->field('id,name,alias')->select();
			$this->assign('site_list',$site_list);
			$this->assign('cate_list',$cate_list);
			$this->display();
		}

	}
	
	
	//添加商品
	public function add(){
		set_time_limit(0);
		if (isset($_POST['submit'])){

			$items = M('Items');
			$items_cate = M('ItemsCate');
			$items_site = M('ItemsSite');
			$items_tags = M('ItemsTags');
			$items_tags_item = M('ItemsTagsItem');
			$user = M('User');

			if ($_POST['title']==''){
				$this->error('标题不能为空！');
			}
			
			$data=$items->create();
			if (!$data){
				$this->error($items->getError());
			}
						
			//去除标题标签
			$data['title']=strip_tags($data['title']);		

			//下载远程图片
			if (C('down_status')==1){
				$type = end(explode( '.', $data['img'] ));
				$data['img']=$this->down_item($data['img'], $data['item_key'].'.'.$type);
			}
			
			//保存上传图片
			if ($_FILES['upload_img']['name'] != '') {
				$dir=date("Ymd");
				mkdir('./Uploads/LocalItems/'.$dir);
				$upload_info = $this->upload('./Uploads/LocalItems/'.$dir.'/');
				$this->upload_item($upload_info[0]['savepath'].$upload_info[0]['savename'], $upload_info[0]['savename']);
				$data['img'] = C('web_path').'Uploads/LocalItems/'.$dir.'/'.$upload_info['0']['savename'];
			}		
			
			//添加商品时间
			$data['add_time']=time();
			
			//添加随机uid
			$user_info=$user->field(id)->where('is_sys=1')->order('rand()')->find();
			$data['uid']=$user_info['id'];
			
			//审核状态
			$data['status']=(C('items_status')+1)%2;
			
			//分享介绍关键词过滤
			$data['info']=$this->filter($data['info']);
			
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
				$result1=$items->where($where)->save($data);
				$new_item_id=$add_item['id'];
			}else {
				$new_item_id=$result2=$items->add($data);
			}
			$items_cate->where("id='".$data['cid']."'")->setInc('item_nums');
			
			//处理标签
			if ($add_item){						
				//已存在商品，先将标签中item_nums减1,删除旧的标签和商品关系
				$old_tag=$items_tags_item->field('tag_id')->where("item_id='".$add_item['id']."'")->select();
				foreach ($old_tag as $tag){
					$items_tags->where("id='".$tag['tag_id']."'")->setDec('item_nums'); 
				}							
				$items_tags_item->where("item_id='".$add_item['id']."'")->delete();//删除标签和商品关系	
			}
			
			$tags = isset($_POST['tags']) && trim($_POST['tags']) ? trim($_POST['tags']) : '';	
			if ($tags) {				
				//标签不存在则添加，更新标签和商品关系
				$tags_arr = explode(' ', $tags);
				$tags_arr = array_unique($tags_arr);
				foreach ($tags_arr as $tag) {
					$isset_id = $items_tags->field('id')->where("name='".$tag."' and pid='".$data['cid']."'")->find();
					if ($isset_id) {
						$items_tags_item->add(array(
								'item_id' => $new_item_id,
								'tag_id' => $isset_id['id'],
						));
						$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
					} else {
						$tag_id = $items_tags->add(array('name' => $tag,'pid' => $data['cid'],));
						$items_tags_item->add(array(
								'item_id' => $new_item_id,
								'tag_id' => $tag_id,
						));
						$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
					}
				}
			}
			if ($result1){
				$this->success('修改成功',U('Items/index'));
			}elseif($result2){
				$this->success('添加成功',U('Items/index'));
			}else {
				$this->error('添加失败');
			}
		}
	}

	
	//编辑商品信息
	public function edit(){
	
		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
	
		//点击提交
		if (isset($_POST['submit']) && isset($_POST['id'])){
			if (!isset($_POST['title'])){
				$this->error('标题不能为空！');
			}
			$data=$items->create();
			if ($data===false){
				$this->error($items->getError());
			}

			//保存上传图片
			if ($_FILES['upload_img']['name'] != '') {
				unset($_FILES['focus_img']);
				$dir=date("Ymd");
				mkdir('./Uploads/LocalItems/'.$dir);
				$upload_info = $this->upload('./Uploads/LocalItems/'.$dir.'/');
				$this->upload_item($upload_info[0]['savepath'].$upload_info[0]['savename'], $upload_info[0]['savename']);
				$data['img'] = C('web_path').'Uploads/LocalItems/'.$dir.'/'.$upload_info['0']['savename'];
			}			

			//保存上传焦点图片
			if ($_FILES['focus_img']['name'] != '') {
				mkdir('./Uploads/LocalItems/focus');
				$upload_info = $this->upload('./Uploads/LocalItems/focus/');
				$data['remark1'] = C('web_path').'Uploads/LocalItems/focus/'.$upload_info['0']['savename'];
			}
				
			//获得商品的cid
			$edit_item = $items->field('cid')->where("id='".$id."'")->find();
			//将分类中item_nums减1，新的分类item_nums加1
			$items_cate->where("id='".$edit_item['cid']."'")->setDec('item_nums'); //旧的分类item_nums减1
			$items_cate->where("id='".$data['cid']."'")->setInc('item_nums'); //新的分类item_nums加1
	
			//处理标签
			$old_tag=$items_tags_item->field('tag_id')->where("item_id='".$id."'")->select();
			foreach ($old_tag as $tag){
				$items_tags->where("id='".$tag['tag_id']."'")->setDec('item_nums'); //旧的标签中item_nums减1
			}
			$items_tags_item->where("item_id='".$id."'")->delete(); //删除标签和商品关系
				
			$tags = isset($_POST['tags']) && trim($_POST['tags']) ? trim($_POST['tags']) : '';
			if ($tags) {
				//标签不存在则添加
				$tags_arr = explode(' ', $_POST['tags']);
				$tags_arr = array_unique($tags_arr);
				foreach ($tags_arr as $tag) {
					$isset_id = $items_tags->field('id')->where("name='".$tag."' and pid='".$data['cid']."' and is_del=0")->find();
					if ($isset_id) {
						$items_tags_item->add(array(
								'item_id' => $id,
								'tag_id' => $isset_id['id'],
						));
						$items_tags->where("id='".$isset_id['id']."'")->setInc('item_nums'); //标签item_nums加1
					} else {
						$tag_id = $items_tags->add(array('name' => $tag));
						$items_tags_item->add(array(
								'item_id' => $id,
								'tag_id' => $tag_id
						));
						$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
					}
				}
			}
			
			//更新商品信息
			$result=$items->where("id='".$id."'")->save($data);
			if ($result){
				$this->success('修改成功！',U('Items/index'));
			}else {
				$this->error('商品信息没有变化！',U('Items/index'));
			}
		}else {
				
			if (!isset($_GET['id'])){
				$this->error('请选择商品！');
			}
			//获得分类
			$result = $items_cate->where('is_del=0')->order('ord desc')->select();
			$cate_list = array();
			foreach ($result as $val) {
				if ($val['pid'] == 0) {
					$cate_list['parent'][$val['id']] = $val;
				} else {
					$cate_list['sub'][$val['pid']][] = $val;
				}
			}
			//获得来源
			$site_list = $items_site->field('id,name,alias')->select();
			//获得商品信息
			$items_info = $items->where("id='".$_GET['id']."'")->find();
			$tag_arr=$items_tags_item->field('tag_id')->where("item_id='".$_GET['id']."'")->select();
			foreach ($tag_arr as $tag) {
				$tag_id[] .= $tag['tag_id'];
			}
			$map['id'] = array('in',$tag_id);
			$tag_name=$items_tags->field('name')->where($map)->select();
			foreach ($tag_name as $tag){
				$tags[] .=$tag['name'];
			}
			$items_info['tags'] = implode(' ', $tags);
			//赋值变量，输出模板
			$this->assign('cate_list', $cate_list);
			$this->assign('site_list', $site_list);
			$this->assign('items', $items_info);
			$this->display();
		}
	}

	
	//删除商品
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		foreach ($del_id as $id){
			$this->delete_item($id);
		}
		$this->success('删除成功！');	
	}
	
	
	//批量添加商品
	public function betchadd(){
		set_time_limit(0);
		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');
		
		if (isset($_POST['urls'])){
			
			//记录操作信息
			$success_update_list=array();
			$success_insert_list=array();
					
			$cid=$_POST['cid'];
			$url=trim($_POST['urls']);
			$seller_id=$_POST['seller_id'];

			//获取商品URL
			$url=trim($url);
			$url=match_url($url);

			//获得商品来源
			$domain=gain_domain($url);
			$site = $items_site->field('id,alias')->where("site_domain='".$domain."'")->find();

			if ($site['alias'] == 'taobao' || $site['alias'] == 'tmall'){
				import("@.ORG.Taobao");
				$taobao=new taobao();
				$result=$taobao->betchitem($url,$cid,$seller_id);
			}elseif ($site['alias'] == 'paipai'){
				import("@.ORG.Paipai");
				$paipai=new Paipai();
				$result=$paipai->betchitem($url,$cid);
			}elseif ($site['alias'] == 'dangdang'){
				import("@.ORG.Dangdang");
				$dangdang=new Dangdang();
				$result=$dangdang->betchitem($url,$cid);
			}elseif ($site['alias'] == 'vancl'){
				import("@.ORG.Vancl");
				$vancl=new Vancl();
				$result=$vancl->betchitem($url,$cid);
			}elseif ($site['alias'] == '360buy'){
				import("@.ORG.Jingdong");
				$jingdong=new Jingdong();
				$result=$jingdong->betchitem($url,$cid);
			}elseif ($site['alias'] == 'caomeipai'){
				import("@.ORG.Caomeipai");
				$caomeipai=new Caomeipai();
				$result=$caomeipai->betchitem($url,$cid);
			}elseif ($site['alias'] == 'mbaobao'){
				import("@.ORG.Mbaobao");
				$mbaobao=new Mbaobao();
				$result=$mbaobao->betchitem($url,$cid);
			}elseif ($site['alias'] == 'nala'){
				import("@.ORG.Nala");
				$nala=new Nala();
				$result=$nala->betchitem($url,$cid);
			}
			$this->ajaxReturn($result);
		}else {
			
			//分类循环
			$result = $items_cate->where('is_del = 0')->order('ord desc')->select();
			$cate_list = array();
			foreach ($result as $val) {
				if ($val['pid'] == 0) {
					$cate_list['parent'][$val['id']] = $val;
				} else {
					$cate_list['sub'][$val['pid']][] = $val;
				}
			}

			$this->assign('cate_list',$cate_list);
			$this->display();
		}				
	}
	
	
	//按关键字添加
	public function addbykey(){
		set_time_limit(0);
		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');
		$user = M('User');
		
		if (isset($_POST['keyword'])){
			$sid=$_POST['sid'];
			$cid=$_POST['cid'];
			$condition['keyword']=$_POST['keyword'];
			$condition['cid']=$_POST['tid'];
			$condition['start_price']=$_POST['start_price'];
			$condition['end_price']=$_POST['end_price'];
			$condition['start_credit']=$_POST['start_credit'];
			$condition['end_credit']=$_POST['end_credit'];
			$condition['sort']=$_POST['sort'];
			$condition['guarantee']=$_POST['guarantee'];
			$condition['start_commissionRate']=$_POST['start_commissionRate']*100;
			$condition['end_commissionRate']=$_POST['end_commissionRate']*100;
			$condition['start_commissionNum']=$_POST['start_commissionNum'];
			$condition['end_commissionNum']=$_POST['end_commissionNum'];
			$condition['start_totalnum']=$_POST['start_totalnum'];
			$condition['end_totalnum']=$_POST['end_totalnum'];
			$condition['cash_coupon']=$_POST['cash_coupon'];
			$condition['sevendays_return']=$_POST['sevendays_return'];
			$condition['real_describe']=$_POST['real_describe'];
			$condition['cash_ondelivery']=$_POST['cash_ondelivery'];
			$condition['mall_item']=$_POST['mall_item'];
			$condition['page_no']=$_POST['page_no'];

			//获得商品来源
			$site = $items_site->field('alias')->where('id='.$sid)->find();
			
			if ($site['alias'] == 'taobao' || $site['alias'] == 'tmall'){
				import('@.ORG.Taobao');
				$taobao=new Taobao();
				$item_info=array();
				$result=$taobao->taobaokeKeyword($condition);
				if ($result == false || $result['total_results'] == 0){
					$this->ajaxReturn(0);
				}
				$item_info = $result['taobaoke_items']['taobaoke_item'];
			}
			
			if ($item_info){
				$nums=0;
				foreach ($item_info as $item){
					
//					卖家id
// 					$url = 'http://a.m.tmall.com/i'.$item['num_iid'].'.htm';
// 					$content = file_get_contents($url);
// 					preg_match('/<div class="left-margin-5">(.*?)进入店铺/si',$content,$result);
// 					preg_match('/href="(.*)">/si',$result[1],$re);
// 					isset($re) ? $shop_url = $re[1] : $shop_url = '';
// 					if ($shop_url){
// 						unset($content);
// 						unset($result);
// 						$content = file_get_contents($shop_url);
// 						preg_match('/userId (\d+)/si',$content,$result);
// 						isset($result) ? $seller_id = $result[1] : $seller_id = '';
// 					}else {
// 						$seller_id = '';
// 					}
					
//					网页端抓去卖家id，已受限制
// 					$url = 'http://detail.tmall.com/item.htm?id='.$item['num_iid'];
// 					$content = file_get_contents($url);
// 					preg_match('/userid=(\d+)?/si',$content,$result);
// 					isset($result) ? $seller_id = $result[1] : $seller_id = '';
					
					if (strlen($item['click_url']) < 10){
						continue;
					}
					//格式化标题
					$title=strip_tags($item['title']);
					
					//获得标签
					$tags=$this->get_tags($title);
					
					//获取图片
					$img=$item['pic_url'];
					
					//商品添加时间
					$add_time=time();
					
					//获得item_key
					$item_key=$site['alias'].'_'.$item['num_iid'];
					
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
					$data['seller_id']='';
					$data['title']=strip_tags($title);
					$data['url']=$item['click_url'];
					$data['tag']=$tags;
					$data['price']=$item['price'];
					$data['item_key']=$item_key;
					$data['sid']=$sid;
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
										'tag_id' => $tag_id,
								));
								$items_tags->where("id='".$tag_id."'")->setInc('item_nums'); //标签item_nums加1
							}
						}
					}
				}
				$this->ajaxReturn($nums);
			}			
		}else {
			
			//淘宝分类循环		
			import("@.ORG.Taobao");
			$taobao=new Taobao();
			$cid='0';
			$taobao_cate_list=$taobao->taobaoCateInfo($cid);
			$taobao_cate_info=$taobao_cate_list['item_cats']['item_cat'];
			
			//分类循环
			$result = $items_cate->where('is_del = 0')->order('ord desc')->select();
			$cate_list = array();
			foreach ($result as $val) {
				if ($val['pid'] == 0) {
					$cate_list['parent'][$val['id']] = $val;
				} else {
					$cate_list['sub'][$val['pid']][] = $val;
				}
			}

			$where['alias']='taobao';
			//获得来源
			$site_list = $items_site->field('id,name,alias')->where($where)->select();
			
			//获取标签
			$map['sid']=array('neq',0);
			$map['is_index']=1;
			$map['is_del']=0;
			$tags = $items_tags->field('id,pid,name')->where($map)->select();
			foreach ($tags as $val) {				
				$tag_list[$val['pid']][] = $val;
			}
			
			$this->assign('taobao_cate_info',$taobao_cate_info);
			$this->assign('tag_list',$tag_list);					
			$this->assign('site_list',$site_list);			
			$this->assign('cate_list',$cate_list);
			$this->display();
		}
		
	}
	
	//获取淘宝分类
	public function get_taobao_cate(){
		$cid=$_POST['tid'];	
		import("@.ORG.Taobao");
		$taobao=new Taobao();
		$taobao_cate_list=$taobao->taobaoCateInfo($cid);
		$data=$taobao_cate_list['item_cats']['item_cat'];
		if ($data){
			$this->ajaxReturn($data);
		}else {
			$this->ajaxReturn(false);
		}
	}
			
	//排序
	public function order(){
	
		if ($_POST['order']){
			$items = M('Items');
			foreach ($_POST['orders'] as $id => $ord) {
				$data['ord'] = $ord;
				$items->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$items = M('items');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$items->where($data)->save($set);
		$val=$items->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	//更新taobao、tmall商品卖家id
	public function update_seller_id(){
		set_time_limit(0);
		$items = M('items');
		$item_list=$items->field('id,item_key')->where('seller_id=0')->select();
		foreach ($item_list as $item){
			$result=explode('_', $item['item_key']);
			if ($result[0] == 'taobao' || $result[0] == 'tmall'){
				$url = 'http://a.m.tmall.com/i'.$result[1].'.htm';
				$content = file_get_contents($url);
				import("@.ORG.Taobao");
				$taobao=new taobao();
				unset($result);
				//获得商品图片
				$seller_id=$taobao->match_seller_id($content);
				unset($content);
				$where['id']=$item['id'];
				$data['seller_id']=$seller_id;
				$items->where($where)->save($data);
			}
		}
		$this->success('更新卖家id完成！',U('Items/index'));
	}
	
	//更新商品图片
	public function update_img(){
		set_time_limit(0);
		$items = M('items');
		$item_list=$items->field('id,img,item_key')->where('is_del=0')->select();
		dump($item_list);exit();
		foreach ($item_list as $item){
			preg_match('/^(http:\/\/)/si',$item['img'],$re);
			if ($re){
				continue;
			}
			unset($re);
			$result=explode('_', $item['item_key']);
			$url = 'http://a.m.tmall.com/i'.$result[1].'.htm';
			$content = file_get_contents($url);
			if ($result[0] == 'taobao' || $result[0] == 'tmall'){
				import("@.ORG.Taobao");
				$fp=new taobao();
			}elseif ($result[0] == 'paipai'){
				import("@.ORG.Paipai");
				$fp=new Paipai();
			}elseif ($result[0] == 'dangdang'){
				import("@.ORG.Dangdang");
				$fp=new Dangdang();
			}elseif ($result[0] == 'vancl'){
				import("@.ORG.Vancl");
				$fp=new Vancl();
			}elseif ($result[0] == '360buy'){
				import("@.ORG.Jingdong");
				$fp=new Jingdong();
			}elseif ($result[0] == 'caomeipai'){
				import("@.ORG.Caomeipai");
				$fp=new Caomeipai();
			}elseif ($result[0] == 'mbaobao'){
				import("@.ORG.Mbaobao");
				$fp=new Mbaobao();
			}elseif ($result[0] == 'nala'){
				import("@.ORG.Nala");
				$fp=new Nala();
			}
			unset($result);
			//获得商品图片
			if ($fp){
				$img=$fp->match_image($content);
				$type = end(explode( '.', $img ));
				$image = explode( '.'.$type, $img );
				$img=$image[0].'.'.$type;
				unset($content);
								
				$where['id']=$item['id'];
				$data['img']=$img;
				$items->where($where)->save($data);
			}

		}
		$this->success('更新商品图片完成！',U('Items/index'));
	}
	
	//更新taobao、tmall商品链接
	public function update_url(){
		set_time_limit(0);
		$items = M('items');
		$item_list=$items->field('id,item_key')->where('is_del=0')->select();
		foreach ($item_list as $item){
			$result=explode('_', $item['item_key']);
			if ($result[0] == 'taobao' || $result[0] == 'tmall'){
				import("@.ORG.Taobao");
				$taobao=new taobao();
				//获得淘宝客跳转链接
				$url = $taobao->gain_url($result[0], $result[1]);
				if (!$url){
					$url='http://detail.tmall.com/item.htm?id='.$result[1];
				}
				unset($result);
				$where['id']=$item['id'];
				$data['url']=$url;
				$items->where($where)->save($data);
			}
		}
		$this->success('更新商品链接完成！',U('Items/index'));
	}

	//删除商品方法
	public function delete_item($id){
	
		$items = M('Items');
		$items_cate = M('ItemsCate');
		$items_site = M('ItemsSite');
		$items_tags = M('ItemsTags');
		$items_tags_item = M('ItemsTagsItem');
	
		//分类中item_nums减1
		$cid=$items->field('cid')->where("id='".$id."'")->find();
		$items_cate->where("id='".$cid['cid']."'")->setDec('item_nums');
		//标签中item_nums减1
		$data['item_id']=$id;
		$old_tag=$items_tags_item->field('tag_id')->where($data)->select();
		foreach ($old_tag as $tag){
			$items_tags->where("id='".$tag['tag_id']."'")->setDec('item_nums');
		}
		//用户的likes_num减1，删除山品和用户喜欢关系
		$user_mod=M("User");
		$itemslikes_mod=M("ItemsLikes");
		$items_likes_list=$itemslikes_mod->field("uid")->where("items_id=$id")->select();
		$itemslikes_mod->where("items_id=$id")->delete();
		foreach($items_likes_list as $val){
			$uid=$val['uid'];
			$user_mod->where("id=$uid and is_del=0")->setDec("likes_num");
		}
		//删除商品信息及商品和标签关系
		$save['is_del']=1;
		$row = $items->where("id='".$id."'")->save($save);
		$items_tags_item->where($data)->delete();
		//删除用户评论信息
		$itemscommengs=M('ItemsComments');
		$itemscommengs->where("items_id=$id and is_del=0")->setField("is_del",1);	
		//删除商品和专辑关系
		$albumitems=M('AlbumItems');
		$albumitems->where("items_id=$id")->delete();
		if (!$row){
			$this->error('删除失败！');
			exit();
		}
	}
}

