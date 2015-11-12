<?php
class LinkAction extends BaseAction{

	//链接列表显示
	public function index(){
		$link = M('Link');

		//搜索
		$where = 'is_del=0';
		if (isset($_GET['keyword']) && trim($_GET['keyword'])) {
			$where .= " AND (name LIKE '%".$_GET['keyword']."%' or url LIKE '%".$_GET['keyword']."%')";
			$this->assign('keyword', $_GET['keyword']);
		}
		
		import("ORG.Util.Page");
		$count=$link->where($where)->count();
		$page=new Page($count,10);
		$show=$page->show();
		$links=$link->where($where)->order('ord desc')->limit($page->firstRow.','.$page->listRows)->select();

		foreach ($links as $key=>$val){
			$links[$key]['key']=++$page->firstRow;
		}
		$this->assign('links',$links);
		$this->assign('page',$show);
		$this->display();
	}

	//添加
	public function add(){
			
		$this->display('edit');
		
	}
	
	//修改
	public function edit(){
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
		$link=M('Link');
		
		if ($_POST['submit']){
			$data=$link->create();
			
			if ($id){
				$link->where('id='.$id)->save($data);
				$this->success('修改成功',U('Link/index'));
			}else{
				$link->add($data);
				$this->success('添加成功',U('Link/index'));
			}

				
		}else {
			$link_info=$link->where('id='.$id)->find();
			$this->assign('link',$link_info);
			$this->display();
		}
	}
	
	//删除
	public function delete(){
		if (!isset($_POST['id'])){
			$this->error('请选择要删除的商品！');
		}
		$del_id = $_POST['id'];
		$link=M('Link');
		$data['is_del']=1;
		foreach ($del_id as $id){
			$link->where('id='.$id)->save($data);
		}
		$this->success('删除成功！');	
	}
	
	
	//排序
	public function order(){
		if ($_POST['order']){
			$link = M('Link');
			foreach ($_POST['orders'] as $id => $ord) {
				$data['ord'] = $ord;
				$link->where('id='.$id)->save($data);
			}
			$this->success('修改成功！');
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}