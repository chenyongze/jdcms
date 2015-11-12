<?php
class UserAction extends BaseAction{

	//列表显示
	public function index(){
		$user_mod=M("User");
		import("ORG.Util.Page");
		$where='is_del=0';
		if(isset($_GET['keyword'])){
			$keyword = $_GET['keyword'];
			$where.=" and name like '%$keyword%'";
		}
	
		$count = $user_mod->where($where)->count();
		$page = new Page($count,20);
		$show = $page->show();
		$list=$user_mod->where($where)->order("last_time desc")->limit($page->firstRow.','.$page->listRows)->select();
		foreach ($list as $key=>$val){
			$list[$key]['key']=++$page->firstRow;
		}
		$this->assign('keyword',$keyword);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	
	}
	
	//批量添加
	public function add(){
		$user_mod = M('user');
		if (isset($_POST['submit'])) {
			$name=explode(',', $_POST['name']);
			
			$data['add_time']=time();
			$data['last_time']=time();
			if($_POST['passwd']){
				$data['passwd'] = md5($_POST['passwd']);
			}
			$data['is_sys']=$_POST['is_sys'];
			
			foreach ($name as $val){
				$val=trim($val);
				if (!$val){
					continue;
				}
				$where['name']=$val;
				$where['is_del']=0;
				$result = $user_mod->where($where)->find();
				if (!$result){
					if ($_POST['sex'] == '2'){
						$data['sex']=mt_rand(0,1);
					}else {
						$data['sex']=$_POST['sex'];
					}
					$data['name']=$val;
					$user_mod->add($data);
				}
				unset($where);
				unset($result);
			}
			$this->success('任务完成！',U('User/index'));
		} else {
			$this->display();
		}
	}
	
	//修改用户信息
	public function edit(){
		$user_mod = M('user');
		if (isset($_POST['submit'])) {
		
			$data = $user_mod->create();
			$data['passwd'] = md5($_POST['passwd']);
			$result = $user_mod->where("id=" . $data['id'])->save($data);
			if(false !== $result){
				$this->success('修改成功！',U('User/index'));
			}else{
				$this->error('修改失败！');
			}
		} else {

			if (!isset($_GET['id'])){
				$this->error('请选择要修改的用户！');
			}
			$id=$_GET['id'];
			$info = $user_mod->where('id='.$id)->find();
			$this->assign('info', $info);
			$this->display();
		}	
	}
	
	//修改uc配置
	public function uc(){
		if ($_POST){
			$file = './api/config.inc.php';
			if (get_magic_quotes_gpc()) {
				$uc_config = stripslashes($_POST['uc_config']);
			}else {
				$uc_config = $_POST['uc_config'];
			}
			$uc_config = "<?php\r\n".$uc_config;
			file_put_contents($file,$uc_config);

			$config = $_POST["con"];
			if ($_POST['uc_config'] == ''){
				$config['uc_status'] = 0;
			}
			$config['uc_status']=intval($config['uc_status']);
			$this->updateconf($config);
		}else {
			$file = './api/config.inc.php';
			$uc_config = str_replace('<?php','',file_get_contents($file));
			$this->assign('uc_config',$uc_config);
			$this->display();	
		}
	}
	
	//删除用户
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的用户！');
		}
		$user_follow_mod=M("User_follow");
		$del_id = $_POST['id'];
		$user_mod=M('User');
		$items_mod=M('Items');
		$items_comments_mod=M('Items_comments');
		$items_likes_mod=M("Items_likes");
		$album_mod=M("Album");
		$album_items_mod=M("Album_items");
		$data['is_del']=1;
		foreach ($del_id as $id){
			$user_mod->where('id='.$id)->save($data);
			//关注该用户的用户的follow_num减1
			$follow_list=$user_follow_mod->where("fans_id=$id")->field("uid")->select();
			foreach($follow_list as $valFo){
				$uid=$valFo['uid'];
				$user_mod->where("id=$uid and is_del=0")->setDec("follow_num");
				$user_follow_mod->where("fans_id=$id")->delete();
			}
			//该用户关注的用户的fans_num减1
			$fans_list=$user_follow_mod->where("uid=$id")->field("fans_id")->select();
			foreach($fans_list as $valFa){
				$fans_id=$valFa['fans_id'];
				$user_mod->where("id=$fans_id and is_del=0")->setDec("fans_num");
				$user_follow_mod->where("uid=$id")->delete();
			}
			
			$item_del_id=$items_mod->field('id')->where("uid=$id and is_del=0")->select();
			foreach ($item_del_id as $item_id){
				$this->delete_item($item_id['id']);
			}
			$items_mod->where("uid=$id and is_del=0")->delete();//删除用户分享的商品
			$items_comments_mod->where("uid=$id and is_del=0")->setField("is_del",1);//删除用户发表的评论
			$items_likes_list=$items_likes_mod->where("uid=$id")->field("items_id")->select();
			foreach($items_likes_list as $kyl=>$vll){
				$items_mod->where("is_del=0 and id=".$vll['items_id'])->setDec('likes');//用户喜欢的商品的喜欢数减1
			}
			$items_likes_mod->where("uid=$id")->delete();//删除用户喜欢的商品
			$album_list=$album_mod->where("uid=$id")->field("id")->select();
			foreach($album_list as $vla){
				$album_items_mod->where("pid=".$vla['id'])->delete();
			}
			$album_mod->where("uid=$id")->setField('is_del',1);//删除用户创建的专辑
		}
		
		$this->success('删除成功！');
	}
	
	//修改状态
	function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$user_mod = M('User');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$user_mod->where($data)->save($set);
		$val=$user_mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
	private function delete_item($id){
	
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
		$items_likes_list=$itemslikes_mod->field("uid")->where("items_id=$id and is_del=0")->select();
		$itemslikes_mod->where("items_id=$id")->setField("is_del",1);
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