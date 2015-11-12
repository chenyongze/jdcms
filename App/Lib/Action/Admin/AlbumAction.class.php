<?php
class AlbumAction extends BaseAction{

	public function index(){
		$album_mod = M('Album');
		import("ORG.Util.Page");

		//搜索
		$where = 'is_del=0';
		$keyword = isset($_GET['keyword']) && trim($_GET['keyword']) ? trim($_GET['keyword']) : '';
		$status = isset($_GET['status']) ? intval($_GET['status']) : '-1';
		if ($keyword) {
			$where .= " AND title LIKE '%" . $keyword . "%'";
			$this->assign('keyword',$keyword);
		}
		$status >= 0 && $where .= " AND status=" . $status;
		$this->assign('status', $status);
		
		$count = $album_mod->where($where)->count();
		$page = new Page($count, 20);
		$list = $album_mod->where($where)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
		foreach ($list as $key => $val) {		
			$list[$key]['key'] = ++$page->firstRow;
		}

		$show = $page->show();
		$this->assign('page', $show);
		$this->assign('list', $list);
		$this->display();
	}
	
	//批量添加
	public function add(){
		$_album = M('Album');
		$_albumCate = M('AlbumCate');
		$_albumItems = M('AlbumItems');
		$_user = M('User');
		$_items = M('Items');
		if (isset($_POST['submit'])) {
			$data=$_album->create();
			//添加随机uid
			$user_info=$_user->field(id)->where('is_sys=1')->order('rand()')->find();
			$data['uid']=$user_info['id'];
			//添加随机cid
			$album_cate=$_albumCate->field(id)->where('is_del=0')->order('rand()')->find();
			$data['cid']=$album_cate['id'];
			$data['add_time']=time();
			$album_id=$_album->add($data);
			$items_info=$_items->field('id')->where('status=1 and is_del=0')->order('rand()')->limit('9')->select();
			$map['pid']=$album_id;
			foreach ($items_info as $item){
				$map['items_id']=$item['id'];
				$map['add_time']=time();
				$_albumItems->add($map);
			}
			$this->success('任务完成！',U('Album/index'));
		} else {
			$this->display();
		}
	}
	
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$album_mod = M('Album');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$album_mod->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$album_mod = M('Album');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$album_mod->where($data)->save($set);
		$val=$album_mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}

}