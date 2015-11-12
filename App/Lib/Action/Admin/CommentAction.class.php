<?php
class CommentAction extends BaseAction{

	public function index(){
		$comment_mod = M('ItemsComments');
		import("ORG.Util.Page");
		$prex = C('DB_PREFIX');

		//搜索
		$where = $prex.'items_comments.is_del=0';
		$keyword = isset($_GET['keyword']) && trim($_GET['keyword']) ? trim($_GET['keyword']) : '';
		$time_start = isset($_GET['time_start']) && trim($_GET['time_start']) ? trim($_GET['time_start']) : '';
		$time_end = isset($_GET['time_end']) && trim($_GET['time_end']) ? trim($_GET['time_end']) : '';
		$status = isset($_GET['status']) ? intval($_GET['status']) : '-1';
		if ($keyword) {
			$where .= " AND ".$prex."items.title LIKE '%" . $keyword . "%'";
			$this->assign('keyword',$keyword);
		}
		if ($time_start) {
			$time_start_int = strtotime($time_start);
			$where .= " AND ".$prex."items_comments.add_time>='" . $time_start_int . "'";
			$this->assign('time_start', $time_start);
		}
		if ($time_end) {
			$time_end_int = strtotime($time_end);
			$where .= " AND ".$prex."items_comments. add_time<='" . $time_end_int . "'";
			$this->assign('time_end', $time_end);
		}
		$status >= 0 && $where .= " AND ".$prex."items_comments. status=" . $status;
		$this->assign('status', $status);
		
		$count = $comment_mod->where($where)->count();
		$page = new Page($count, 20);
		$list = $comment_mod->where($where)->field($prex.'items_comments.* , '.$prex.'items.title as title , '.$prex.'items.img as items_img')->join('LEFT JOIN ' . $prex . 'items ON ' . $prex.'items_comments.items_id = '. $prex.'items.id ')->limit($page->firstRow.','.$page->listRows)->order($prex.'items_comments.add_time desc')->select();
		foreach ($list as $key => $val) {		
			$list[$key]['key'] = ++$page->firstRow;
		}

		$show = $page->show();
		$this->assign('page', $show);
		$this->assign('list', $list);
		$this->display();
	}
	
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$comment_mod = M('ItemsComments');
		$items=M('Items');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$result=$comment_mod->field('items_id')->where('id='.$id)->find();
			$items->where('id='.$result['items_id'])->setDec('comments');
			$comment_mod ->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');
	}
	
	
	//修改状态
	public function status() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$comment_mod = M('ItemsComments');
		$data['id']=$id;
		$set[$type]=array('exp',"($type+1)%2");
		$comment_mod->where($data)->save($set);
		$val=$comment_mod->field($type)->where($data)->find();
		$this->ajaxReturn($val[$type]);
	}
	
}